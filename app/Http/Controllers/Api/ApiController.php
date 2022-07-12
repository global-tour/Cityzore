<?php

namespace App\Http\Controllers\Api;

use App\Events\StatusLiked;
use App\Invoice;
use App\Mails;
use App\Ticket;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Http\Controllers\Helpers\MailOperations;
use Nahid\JsonQ\Jsonq;
use App\Option;
use App\Av;
use App\Avdate;
use App\Cart;
use App\Booking;
use App\Physicalticket;
use App\Apilog;
use App\Supplier;
use App\Barcode;


class ApiController extends Controller
{

    // Please note that at the time I'm coding this API GetYourGuide was not supporting Mixed Tour Options.
    // Hence this API is not supporting Mixed Tour Options, too.

    public $apiRelated;
    public $refCodeGenerator;
    public $timeRelatedFunctions;
    public $mailOperations;

    public function __construct()
    {
        $this->apiRelated = new ApiRelated();
        $this->refCodeGenerator = new RefCodeGenerator();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->mailOperations = new MailOperations();
    }

    // Example url: http://localhost:8000/api/v1/test/?productId=OPT88426&fromDateTime=2019-10-22T00:00:00+02:00&toDateTime=2019-10-29T23:59:59+02:00
    // With Basic Authentication. Postman is required for this
    public function getAvailabilities(Request $request) {
        $apilog = new Apilog();
        $apilog->request = json_encode($request->all());
        $apilog->query = json_encode($request->query());
        $apilog->server = json_encode($request->server());
        $apilog->headers = json_encode($request->header());
        $apilog->path = $request->path();
        $apilog->fullPath = $request->fullUrl();
        if (!$this->apiRelated->apiAuthorization($request)) {
          return $this->apiRelated->throwErrors('AUTHORIZATION_FAILURE');
        }

        if (!$this->apiRelated->isDataValid($request, 'get-availabilities')) {
            return $this->apiRelated->throwErrors('VALIDATION_FAILURE');
        }

        // variables from request
        $fromDateTime = $this->apiRelated->getTimeFormatCorrectly($request->fromDateTime);
        $toDateTime = $this->apiRelated->getTimeFormatCorrectly($request->toDateTime);
        $productId = $request->productId;
        $apilog->fromDateTime = $fromDateTime;
        $apilog->toDateTime = $toDateTime;
        $apilog->productId = $productId;
        $apilog->save();

        // date and time variables splitted
        $fromDateTime = $this->apiRelated->setTimeFormatForUs($fromDateTime);
        $fromDate = $fromDateTime['date'];
        $fromTime = $fromDateTime['time'];
        $toDateTime = $this->apiRelated->setTimeFormatForUs($toDateTime);
        $toDate = $toDateTime['date'];
        $toTime = $toDateTime['time'];

        // option check
        $options = Option::where('referenceCode', $productId)->get();
        if (count($options) != 1) {
            return $this->apiRelated->throwErrors('INVALID_PRODUCT');
        }

        $data = array(
            'data' =>
                [
                    'availabilities' => []
                ]
        );

        $option = $options->first();
        // Mixed availabilities are not supported by GYG,
        // so there is 1 to 1 relation between option and availability
        $availability = $option->avs()->first();
        $disabledDates = $this->apiRelated->getDisabledDatesForGYG($availability);
        $jsonq = $this->apiRelated->prepareJsonQ();

        $validAvailabilities = array();
        $validAvailabilities['productId'] = $productId;

        $blockoutHours = [];
        $avBlockoutHours = json_decode($availability->blockoutHours, true);
        foreach($avBlockoutHours as $avBlockoutHour) {
            if(array_key_exists($option->referenceCode, $avBlockoutHour)) {
                $blockoutHours = $avBlockoutHour[$option->referenceCode];
            }
        }

        if (in_array($availability->avTicketType, [1, 2])) {
            $column = $availability->avTicketType == 1 ? $availability->hourly : $availability->daily;
            $res = $jsonq->json($column);
            $result = $res->where('day', 'dateGte', $fromDate)
                ->where('day', 'dateLte', $toDate)
                ->where('isActive', '=', 1)
                ->get();

            if (count($result) == 0) {
                return response()->json($data);
            }
            foreach ($result as $r) {
                if (!in_array($r['day'], $disabledDates)) {
                    if ($availability->avTicketType == 1) {
                        if (!($r['day'] == $fromDate && $r['hour'] < $fromTime) && !($r['day'] == $toDate && $r['hour'] > $toTime)) {
                            $isInBlockArr = false;
                            foreach($blockoutHours as $blockoutHour) {
                                if(isset($blockoutHour['hours'])) {
                                    $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r['day'])[1], $blockoutHour['months'])) && in_array($r['hour'], $blockoutHour['hours']));
                                    $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days'])) && in_array($r['hour'], $blockoutHour['hours']));
                                    $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r['hour'], $blockoutHour['hours'])));
                                } elseif(isset($blockoutHour['days'])) {
                                    $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r['day'])[1], $blockoutHour['months'])) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days']));
                                    $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days'])));
                                    $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r['hour'], $blockoutHour['hours'])) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days']));
                                } elseif(isset($blockoutHour['months'])) {
                                    $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $r['day'])[1], $blockoutHour['months'])));
                                    $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days'])) && in_array(explode('/', $r['day'])[1], $blockoutHour['months']));
                                    $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($r['hour'], $blockoutHour['hours'])) && in_array(explode('/', $r['day'])[1], $blockoutHour['months']));
                                }

                                //$a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && !in_array(explode('/', $r['day'])[1], $blockoutHour['months'])));
                                //$b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && !in_array(Carbon::createFromFormat('d/m/Y', $r['day'])->format('l'), $blockoutHour['days'])));
                                //$c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && !in_array($r['hour'], $blockoutHour['hours'])));

                                if(!$a || !$b || !$c) {
                                    $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r['day'], $r['hour']);
                                    $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']);
                                    $validAvailabilities['vacancies'] = $ticket;
                                } else {
                                    $isInBlockArr = true;
                                    break;
                                }
                            }
                            if(count($blockoutHours) > 0) {
                                if (!$isInBlockArr)
                                    array_push($data['data']['availabilities'], $validAvailabilities);
                            } else {
                                $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r['day'], $r['hour']);
                                $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']);
                                $validAvailabilities['vacancies'] = $ticket;
                                array_push($data['data']['availabilities'], $validAvailabilities);
                            }
                        }
                    } else if ($availability->avTicketType == 2) {
                        if (!($r['day'] == $fromDate && $r['hourFrom'] < $fromTime) && !($r['day'] == $toDate && $r['hourFrom'] > $toTime)) {
                            $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r['day']);
                            $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']);
                            $validAvailabilities['vacancies'] = $ticket;
                            $validAvailabilities['openingTimes'] = array();
                            array_push($validAvailabilities['openingTimes'], ['fromTime' => $r['hourFrom'], 'toTime' => $r['hourTo']]);
                            array_push($data['data']['availabilities'], $validAvailabilities);
                        }
                    }
                }
            }
            $res->reset();

            return response()->json($data);
        } else if ($availability->avTicketType == 3) {
            $dateRange = $availability->dateRange;
            $res = $jsonq->json($dateRange);
            $result = $res->where('dayFrom', 'dateBetween', [$fromDate, $toDate])
                ->orWhere('dayTo', 'dateBetween', [$fromDate, $toDate])
                ->orWhere('dayFrom', 'dateLte', $fromDate)
                ->orWhere('dayTo', 'dateGte', $toDate)
                ->get();
            $res->reset();
            if (count($result) == 0) {
                return response()->json($data);
            } else {
                foreach ($result as $i => $r) {
                    $fromDateFormatted = DateTime::createFromFormat('d/m/Y', $fromDate);
                    $dayFrom = DateTime::createFromFormat('d/m/Y', $r['dayFrom']);
                    if ($dayFrom < $fromDateFormatted) {
                        $dayFrom = $fromDateFormatted;
                    }
                    $toDateFormatted = DateTime::createFromFormat('d/m/Y', $toDate);
                    $dayTo = DateTime::createFromFormat('d/m/Y', $r['dayTo']);
                    if ($dayTo > $toDateFormatted) {
                        $dayTo = $toDateFormatted;
                    }
                    $interval = new DateInterval('P1D');
                    $dayTo->add($interval);
                    $period = new DatePeriod($dayFrom, $interval, $dayTo);

                    foreach ($period as $dt) {
                        $dtFormatted = $dt->format('d/m/Y');
                        if (!in_array($dtFormatted, $disabledDates)) {
                            $daily = $availability->daily;
                            $res2 = $jsonq->json($daily);
                            $result2 = $res2->where('day', '=', $dtFormatted)
                                ->where('isActive', '=', 1)
                                ->get();
                            if (count($result2) != 0) {
                                foreach ($result2 as $r2) {
                                    if (!($r2['day'] == $fromDate && $r2['hourFrom'] < $fromTime) && !($r2['day'] == $toDate && $r2['hourFrom'] > $toTime)) {
                                        $validAvailabilities['dateTime'] = $this->timeRelatedFunctions->arrangeDateTimeForGetAvailabilities($r2['day']);
                                        $ticket = $availability->isLimitless == 1 ? 999999 : intval($r['ticket']); // getting ticket count from dateRange column
                                        $validAvailabilities['vacancies'] = $ticket;
                                        $validAvailabilities['openingTimes'] = array();
                                        array_push($validAvailabilities['openingTimes'], ['fromTime' => $r2['hourFrom'], 'toTime' => $r2['hourTo']]);
                                        array_push($data['data']['availabilities'], $validAvailabilities);
                                    }
                                }
                            }
                            $res2->reset();
                        }
                    }
                }
            }
            return response()->json($data);
        } else {
            return $this->apiRelated->throwErrors('INVALID_PRODUCT');
        }
    }

    // /1/reserve request
    // We may need to check if there is a valid date time on given dateTime in request.
    // We actually do this in get-availabilities request but I'm not sure if we need this in reserve and book requests
    public function reserve(Request $request) {
        $apilog = new Apilog();
        $apilog->request = json_encode($request->all());
        $apilog->query = json_encode($request->query());
        $apilog->server = json_encode($request->server());
        $apilog->headers = json_encode($request->header());
        $apilog->path = $request->path();
        $apilog->fullPath = $request->fullUrl();
        $apilog->save();
        if (!$this->apiRelated->apiAuthorization($request)) {
          return $this->apiRelated->throwErrors('AUTHORIZATION_FAILURE');
        }

        // Only checks for 1st and 2nd level keys of request.
        // Elements of bookingItems will be checked when looping through.
        if (!$this->apiRelated->isDataValid($request, 'reserve')) {
            return $this->apiRelated->throwErrors('VALIDATION_FAILURE');
        }
        // Data from request
        $data = $request->data;

        // Variables from data
        $dateTime = $data['dateTime'];
        $productId = $data['productId'];
        $bookingItems = $data['bookingItems'];
        $gygBookingReference = $data['gygBookingReference'];

        // date and time variables that we need
        $dateTimeForUs = $this->apiRelated->setTimeFormatForUs($dateTime);
        $date = $dateTimeForUs['date'];
        $time = $dateTimeForUs['time'];
        $ticketCategories = array();
        $ticketCount = 0;
        $ticketState = 0; // this variable is used for notify push
        foreach ($bookingItems as $item) {
            array_push($ticketCategories, $item['category']);
            $ticketCount += $item['count'];
        }

        $options = Option::where('referenceCode', $productId)->get();
        if (count($options) != 1) {
            return $this->apiRelated->throwErrors('INVALID_PRODUCT');
        }
        $option = $options->first();
        $availability = $option->avs()->first();
        $pricing = $option->pricings()->first();
        $validCategories = $this->apiRelated->getValidCategories($pricing);
        $differentCategory = array_diff($ticketCategories, $validCategories);
        if (count($differentCategory) > 0) {
            $differentCategory = array_values($differentCategory)[0];
            return $this->apiRelated->throwErrors('INVALID_TICKET_CATEGORY', $differentCategory);
        }

        $disabledDates = $this->apiRelated->getDisabledDatesForGYG($availability);
        $jsonq = $this->apiRelated->prepareJsonQ();

        if (in_array($availability->avTicketType, [1, 2, 3])) {
            if (!in_array($date, $disabledDates)) {
                $ticketCountForQuery = $availability->isLimitless == 1 ? 0 : $ticketCount;
                $result = null;
                $columnStr = 'hourly';
                if ($availability->avTicketType == 2) {
                    $columnStr = 'daily';
                } else if ($availability->avTicketType == 3) {
                    $columnStr = 'dateRange';
                }
                $column = $availability->$columnStr;
                $res = $jsonq->json($column);

                if ($availability->avTicketType == 1) {
                    $result = $res->where('day', '=', $date)
                        ->where('hour', '=', $time)
                        ->where('ticket', '>=', $ticketCountForQuery)
                        ->where('isActive', '=', 1)
                        ->get();
                } else if ($availability->avTicketType == 2) {
                    $result = $res->where('day', '=', $date)
                        ->where('ticket', '>=', $ticketCountForQuery)
                        ->where('isActive', '=', 1)
                        ->get();
                } else if ($availability->avTicketType == 3) {
                    $result = $res->where('dayFrom', 'dateLte', $date)
                        ->where('dayTo', 'dateGte', $date)
                        ->where('ticket', '>=', $ticketCountForQuery)
                        ->get();
                }

                if (count($result) == 0) {
                    return $this->apiRelated->throwErrors('NO_AVAILABILITY');
                }

                if (count($result) > 1) {
                    return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                }

                if ($availability->isLimitless == 0) {
                    $key = key($result); // there should always be 1 occurence
                    $decoded = json_decode($column, true);


                    $ourTicketCount = intval($decoded[$key]['ticket']);
                    $theyTicketCount = intval($ticketCount);

                    if($ticketCount < intval($option->minPerson) || $ticketCount > intval($option->maxPerson)){
                        return $this->apiRelated->throwErrors('INVALID_PARTICIPANTS_CONFIGURATION', ["min" => intval($option->minPerson), "max" => intval($option->maxPerson)]);
                    }



                    $decoded[$key]['ticket'] -= $ticketCount;
                    $ticketState = $decoded[$key]['ticket'];
                    $decoded[$key]['ticket'] = strval($decoded[$key]['ticket']);
                    $availability->$columnStr = json_encode($decoded);
                }
                if ($availability->save()) {
                    $cart = new Cart();
                    $cart->referenceCode = $this->refCodeGenerator->refCodeGeneratorForCart();
                    $cart->bookingItems = json_encode($bookingItems);
                    $cart->status = 0;
                    $cart->isGYG = 1;
                    $cart->gygBookingReference = $gygBookingReference; // that field is new
                    $cart->optionID = $option->id;
                    $cart->dateTime = $dateTime;
                    $cart->ticketID = $availability->id; // ticket table is removed, replaced with availability id
                    $cart->ticketCount = $ticketCount;
                    if ($cart->save()) {
                        $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($date, $time, 'Europe/Paris');
                        // checks if the availability is limitless, ticket count dropped under 5 and not older than 1 week
                        if ($availability->isLimitless == 0 && $ticketState < 5 && $isDateTimeValid) {
                            $optionRefCodes = $availability->options()->where('connectedToApi', 1)->pluck('referenceCode');
                            foreach($optionRefCodes as $orc) {
                                $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                            }
                        }
                        $reservationExpiration = $this->timeRelatedFunctions->addXMinutesToToday(70);
                        return response()->json(['data' =>
                            ['reservationReference' => $cart->referenceCode, 'reservationExpiration' => $reservationExpiration]
                        ]);
                    } else {
                        return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                    }
                } else {
                    return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                }
            } else {
                return $this->apiRelated->throwErrors('NO_AVAILABILITY');
            }
        } else {
            return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
        }
    }

    // /1/cancel-reservation request
    // We may need to check if there is a valid date time on given dateTime in request.
    // We actually do this in get-availabilities request but I'm not sure if we need this in reserve and book requests
    public function cancelReservation(Request $request) {
        $apilog = new Apilog();
        $apilog->request = json_encode($request->all());
        $apilog->query = json_encode($request->query());
        $apilog->server = json_encode($request->server());
        $apilog->headers = json_encode($request->header());
        $apilog->path = $request->path();
        $apilog->fullPath = $request->fullUrl();
        $apilog->save();
        if (!$this->apiRelated->apiAuthorization($request)) {
           return $this->apiRelated->throwErrors('AUTHORIZATION_FAILURE');
        }

        if (!$this->apiRelated->isDataValid($request, 'cancel-reservation')) {
            return $this->apiRelated->throwErrors('VALIDATION_FAILURE');
        }

        // Data from request
        $data = $request->data;
        $reservationReference = $data['reservationReference'];

        // Is cart instance changed by us because of the ammendment request from GYG?
        // (We are augmenting the ticket count and setting the status to 3)
        $isCartChanged = Cart::where('referenceCode', $reservationReference)->where('isGYG', 1)->where('status', 3)->get();
        if (count($isCartChanged) == 1) {
            return response()->json(['data' => (object) null]);
        }

        $cart = Cart::where('referenceCode', $reservationReference)->where('isGYG', 1)->get();

        if (count($cart) == 0) {
            return $this->apiRelated->throwErrors('INVALID_RESERVATION');
        }

        if (count($cart) > 1) {
            return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
        }

        if (count($cart) == 1) {
            $cartItem = $cart->first();
            // Only if reservation is not bought. 0 -> 1
            // 0 -> 2 state will be done on /1/book request
            if ($cartItem->status == 0) {
                $cartItem->status = 1; // removed from cart (not bought)
                if ($cartItem->save()) {
                    $dateTime = $cartItem->dateTime;
                    // date and time variables that we need
                    $dateTimeForUs = $this->apiRelated->setTimeFormatForUs($dateTime);
                    $date = $dateTimeForUs['date'];
                    $time = $dateTimeForUs['time'];
                    $ticketCount = $cartItem->ticketCount;
                    $availability = Av::find($cartItem->ticketID); // column name is ticketID but it's availability id now
                    if (in_array($availability->avTicketType, [1, 2, 3])) {
                        $columnStr = 'hourly';
                        if ($availability->avTicketType == 2) {
                            $columnStr = 'daily';
                        } else if ($availability->avTicketType == 3) {
                            $columnStr = 'dateRange';
                        }
                        $column = $availability->$columnStr;
                        $jsonq = $this->apiRelated->prepareJsonQ();
                        $res = $jsonq->json($column);
                        $result = array();
                        $columnDecoded = json_decode($availability->$columnStr, true);
                        if (count($columnDecoded) > 0) {
                            if ($availability->avTicketType == 1) {
                                // There is no isActive = 1 check for avTicketType == 1 and 2
                                // because if reservation team
                                // set isActive = 0 after the reservation is made,
                                // result would be 0 and it may cause error
                                $result = $res->where('day', '=', $date)
                                    ->where('hour', '=', $time)
                                    ->get();
                            } else if ($availability->avTicketType == 2) {
                                $result = $res->where('day', '=', $date)
                                    ->get();
                            } else if ($availability->avTicketType == 3) {
                                $result = $res->where('dayFrom', 'dateLte', $date)
                                    ->where('dayTo', 'dateGte', $date)
                                    ->get();
                            }
                        } else {
                            return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                        }

                        if ($availability->isLimitless == 0) {
                            if (count($result) == 1) {
                                $key = key($result);
                                $columnDecoded[$key]['ticket'] += $ticketCount;
                                $ticketState = $columnDecoded[$key]['ticket'];
                                $columnDecoded[$key]['ticket'] = strval($columnDecoded[$key]['ticket']);
                                $availability->$columnStr = json_encode($columnDecoded);
                                if ($availability->save()) {
                                    $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($date, $time, 'Europe/Paris');
                                    // ticket count dropped under 5 and not older than 1 week
                                    if ($ticketState < 5 && $isDateTimeValid) {
                                        $optionRefCodes = $availability->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                        foreach($optionRefCodes as $orc) {
                                            $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                        }
                                    }
                                    return response()->json(['data' => (object) null]);
                                }
                            } else {
                                // we are setting status 1 -> 0 if there is an error
                                $cartItem->status = 0;
                                $cartItem->save();
                                return $this->apiRelated->throwErrors('INVALID_RESERVATION');
                            }
                        } else {
                            return response()->json(['data' => (object) null]);
                        }
                    } else {
                        $cartItem->status = 0;
                        $cartItem->save();
                        return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                    }
                } else {
                    return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                }
            } else {
                return $this->apiRelated->throwErrors('INVALID_RESERVATION');
            }
        }

        return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
    }

    // /1/book
    // We may need to check if there is a valid date time on given dateTime in request.
    // We actually do this in get-availabilities request but I'm not sure if we need this in reserve and book requests
    public function book(Request $request) {
        $apilog = new Apilog();
        $apilog->request = json_encode($request->all());
        $apilog->query = json_encode($request->query());
        $apilog->server = json_encode($request->server());
        $apilog->headers = json_encode($request->header());
        $apilog->path = $request->path();
        $apilog->fullPath = $request->fullUrl();
        $apilog->save();

        if (!$this->apiRelated->apiAuthorization($request)) {
           return $this->apiRelated->throwErrors('AUTHORIZATION_FAILURE');
        }

        // Only checks for 1st and 2nd level keys of request.
        if (!$this->apiRelated->isDataValid($request, 'book')) {
            return $this->apiRelated->throwErrors('VALIDATION_FAILURE');
        }

        $data = $request->data;
        $productId = $data['productId'];
        $reservationReference = $data['reservationReference'];
        $gygBookingReference = $data['gygBookingReference'];
        $dateTime = $data['dateTime'];
        $bookingItems = $data['bookingItems'];
        $travelers = $data['travelers'];
        $language = array_key_exists('language', $data) ? $data['language'] : null;
        $travelerHotel = array_key_exists('travelerHotel', $data) ? $data['travelerHotel'] : null;
        $comment = array_key_exists('comment', $data) ? $data['comment'] : null;

        $isRepetitiveBookingRequests = Booking::where('optionRefCode', $productId)
            ->where('gygBookingReference', $gygBookingReference)
            ->where('reservationRefCode', $reservationReference)
            ->where('status', 0)
            ->get();

        // from documentation: Whenever a call to this method previously failed, it will be retried up to
        // 10 additional times. In case the booking was confirmed internally on the supplier’s side,
        // this endpoint must return the expected response (repeat booking confirmation)
        // without generating another booking nor an error.
        if (count($isRepetitiveBookingRequests) == 1) {
            $repetitiveBooking = $isRepetitiveBookingRequests->first();
            $bookingReference = $repetitiveBooking->bookingRefCode;
            $physicalTickets = $repetitiveBooking->physicalTickets()->get();
            $tickets = array();
            foreach($physicalTickets as $physicalTicket) {
                array_push($tickets,
                    [
                        'category' => $physicalTicket->ticketCategory,
                        'ticketCode' => $physicalTicket->ticketCode,
                        'ticketCodeType' => $physicalTicket->ticketCodeType
                    ]
                );
            }

            return response()->json(['data' => ['bookingReference' => $bookingReference, 'tickets' => $tickets]]);
        }

        $ticketCount = 0;
        foreach($bookingItems as $bookingItem) {
            $ticketCount += $bookingItem['count'];
        }

        $options = Option::where('referenceCode', $productId)->get();
        if (count($options) == 0) {
            return $this->apiRelated->throwErrors('INVALID_RESERVATION');
        }
        if (count($options) > 1) {
            return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
        }
        if (count($options) == 1) {
            $option = $options->first();
            $carts = Cart::where('referenceCode', $reservationReference)
                ->whereIn('status', [0, 2])->get();
            if (count($carts) == 0) {
                return $this->apiRelated->throwErrors('INVALID_RESERVATION');
            }
            if (count($carts) > 1) {
                return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
            }

            if (count($carts) == 1) {
                $cart = $carts->first();
                // sold field operations
                $availability = Av::findOrFail($cart->ticketID); // ticketID is availability id now
                $jsonq = $this->apiRelated->prepareJsonQ();
                $result = null;
                $columnStr = 'hourly';
                if ($availability->avTicketType == 2) {
                    $columnStr = 'daily';
                } else if ($availability->avTicketType == 3) {
                    $columnStr = 'dateRange';
                }
                $column = $availability->$columnStr;
                // date and time variables that we need
                $dateTimeForUs = $this->apiRelated->setTimeFormatForUs($dateTime);
                $date = $dateTimeForUs['date'];
                $time = $dateTimeForUs['time'];
                $res = $jsonq->json($column);
                if ($availability->avTicketType == 1) {
                    $result = $res->where('day', '=', $date)
                        ->where('hour', '=', $time)
                        ->get();
                } else if ($availability->avTicketType == 2) {
                    $result = $res->where('day', '=', $date)
                        ->get();
                } else if ($availability->avTicketType == 3) {
                    $result = $res->where('dayFrom', 'dateLte', $date)
                        ->where('dayTo', 'dateGte', $date)
                        ->get();
                }

                if (count($result) == 0) {
                    return $this->apiRelated->throwErrors('NO_AVAILABILITY');
                }

                if (count($result) > 1) {
                    return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                }

                $key = key($result);
                $decoded = json_decode($column, true);
                $decoded[$key]['sold'] += $ticketCount;
                $decoded[$key]['sold'] = strval($decoded[$key]['sold']);
                $availability->$columnStr = json_encode($decoded);
                $availability->save();

                $oldBookings = Booking::where('gygBookingReference', $gygBookingReference)
                    ->where('optionRefCode', $productId)->where('status', 0)
                    ->get();
                $pricing = $option->pricings()->first();
                $ticketCategories = array();
                foreach($bookingItems as $bookingItem) {
                    array_push($ticketCategories, $bookingItem['category']);
                }
                $validCategories = $this->apiRelated->getValidCategories($pricing);
                $differentCategory = array_diff($ticketCategories, $validCategories);
                if (count($differentCategory) > 0) {
                    $differentCategory = array_values($differentCategory)[0];
                    return $this->apiRelated->throwErrors('INVALID_TICKET_CATEGORY', $differentCategory);
                }
                $bookingReference = $this->refCodeGenerator->refCodeGeneratorForBooking($reservationReference);
                $cartOption = Option::findOrFail($cart->optionID);
                $cartOptionRefCode = $cartOption->referenceCode;
                $cartBookingItemsDecoded = json_decode($cart->bookingItems, true);

                // Checks if the core elements of cart and booking are equal
                if ($productId == $cartOptionRefCode && $dateTime == $cart->dateTime
                    && $this->apiRelated->validateBookingItems($bookingItems, $cartBookingItemsDecoded)) {
                    $extraMailSentence = '';
                    if (count($oldBookings) == 1) {
                        $oldBooking = $oldBookings->first();
                        $oldBooking->status = 1;
                        $oldBooking->save();
                        $extraMailSentence = 'Booking detail changed';
                    }
                    $booking = new Booking();
                    $booking->status = 0; // default is 0, not necessary to put it here
                    $booking->optionRefCode = $productId;
                    $booking->reservationRefCode = $reservationReference;
                    $booking->bookingRefCode = $bookingReference;
                    $booking->gygBookingReference = $gygBookingReference;
                    $booking->bookingItems = json_encode($bookingItems);
                    $booking->comment = $comment;
                    $booking->dateTime = $dateTime;
                    $booking->dateForSort = $this->apiRelated->dateForSortOperation($dateTime);
                    $booking->language = $language;
                    $booking->travelerHotel = $travelerHotel;
                    $booking->platformID = 1;
                    $booking->travelers = json_encode($travelers);
                    $avID = [];
                    $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
                    foreach($bAvs as $bAv) {
                        array_push($avID, $bAv->id);
                    }
                    $booking->avID = json_encode($avID);
                    $firstName = '';
                    $lastName = '';
                    if (array_key_exists('firstName', $travelers[0])) {
                        $firstName = $travelers[0]['firstName'];
                    }
                    if (array_key_exists('lastName', $travelers[0])) {
                        $lastName = $travelers[0]['lastName'];
                    }
                    $booking->fullName = $firstName . ' ' . $lastName;
                    if ($booking->save()) {
                        $invoice = new Invoice();
                        $invoice->paymentMethod = 'API';
                        $invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
                        $invoice->bookingID = $booking->id;
                        $invoice->companyID = -1;
                        if ($invoice->save()) {
                            $booking->invoiceID = $invoice->id;
                            $booking->save();
                        }

                        if(count($cartOption->ticket_types()->get()->toArray()) > 0) {
                            $tickets = $this->apiRelated->makeTicketResponseForBookingForTicketType($booking, $bookingItems, $bookingReference, $cartOption->ticket_types); // For booking response if option has ticket type
                        }
                        else {
                            $tickets = $this->apiRelated->makeTicketResponseForBooking($booking, $bookingItems, $bookingReference); // For booking response
                        }

                        $cart->status = 2; // removed from cart (bought)
                        if ($cart->save()) {
                            $mail = new Mails();
                            $data = [];
                            array_push($data, [
                                'booking_id' => $booking->id,
                                'gygBookingReference' => $booking->gygBookingReference,
                                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                'date' => $this->timeRelatedFunctions->convertYmdHisPlusTimezoneToDmyHi($booking->dateTime, $cartOption, $booking),
                                'options' => $cartOption->title,
                                'BKNCode' => $this->apiRelated->getOnlyBKNNumber($booking->bookingRefCode),
                                'subject' => 'New Booking via GetYourGuide! '. $extraMailSentence .' - ' . $cartOption->title . ' | '. $booking->gygBookingReference. ' | '. $this->apiRelated->getOnlyBKNNumber($booking->bookingRefCode),
                                'name' => $firstName,
                                'surname' => $lastName,
                                'sendToCC' => true
                            ]);
                            $mail->data = json_encode($data);
                            $mail->to = 'contact@parisviptrips.com';
                            $mail->blade = 'mail.api-booking-successful';
                            $mail->save();

                            $restaurant = null;
                            if (!is_null(Option::where('referenceCode', $productId)->first()->rCodeID)) {
                                $restaurant = Supplier::where('isRestaurant', 1)->where('id', Option::where('referenceCode', $productId)->first()->rCodeID)->first();
                                    // Mail for restaurant
                                    $mail = new Mails();
                                    $data = [];
                                    array_push($data, [
                                        'subject' => 'New Booking ! ' .$cartOption->title,
                                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                        'options' => $cartOption->title,
                                        'date' => $this->timeRelatedFunctions->convertYmdHisPlusTimezoneToDmyHi($booking->dateTime, $cartOption, $booking),
                                        'BKNCode' => $this->apiRelated->getOnlyBKNNumber($booking->bookingRefCode),
                                        'name' => $firstName,
                                        'surname' => $lastName,
                                        'sendToCC' => false
                                    ]);
                                    $mail->to = $restaurant->email;
                                    $mail->data = json_encode($data);
                                    $mail->blade = 'mail.booking-successful-for-restaurant';
                                    $mail->save();
                            }
                            $this->apiRelated->placeBarcodeForBooking($booking);

                            return response()->json(['data' => ['bookingReference' => $bookingReference, 'tickets' => $tickets]]);
                        } else {
                            return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                        }
                    } else {
                        return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                    }
                } else {
                    return $this->apiRelated->throwErrors('VALIDATION_FAILURE');
                }
            }
        }

        return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
    }

    // /1/cancel-booking
    public function cancelBooking(Request $request) {
        $apilog = new Apilog();
        $apilog->request = json_encode($request->all());
        $apilog->query = json_encode($request->query());
        $apilog->server = json_encode($request->server());
        $apilog->headers = json_encode($request->header());
        $apilog->path = $request->path();
        $apilog->fullPath = $request->fullUrl();
        $apilog->save();

        if (!$this->apiRelated->apiAuthorization($request)) {
           return $this->apiRelated->throwErrors('AUTHORIZATION_FAILURE');
        }

        // Only checks for 1st and 2nd level keys of request.
        if (!$this->apiRelated->isDataValid($request, 'cancel-booking')) {
            return $this->apiRelated->throwErrors('VALIDATION_FAILURE');
        }

        $data = $request->data;
        $bookingReference = $data['bookingReference'];
        $gygBookingReference = $data['gygBookingReference'];
        $productId = $data['productId'];
        $bookings = Booking::where('bookingRefCode', $bookingReference)
            ->where('gygBookingReference', $gygBookingReference)
            ->where('optionRefCode', $productId)
            ->get();

        if (count($bookings) == 0) {
            return $this->apiRelated->throwErrors('INVALID_PRODUCT');
        }

        if (count($bookings) > 1) {
            return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
        }

        if (count($bookings) == 1) {
            $booking = $bookings->first();
            $booking->status = 2;

            $barcodes = Barcode::where('bookingID', $booking->id)->get();
            foreach($barcodes as $barcode) {
                $logs = json_decode($barcode->log, true) ?? [];
                array_push($logs, [
                    "oldBookingID" => $booking->id,
                    "cancelReason" => str_replace(' ', '&nbsp', 'GYG Cancel Operation'),
                    "cancelBy" => str_replace(' ', '&nbsp', 'description: ' . $barcode->description . ' | bookingID: ' . $barcode->bookingID . ' | bookingDate: ' . $barcode->booking_date . ' | usedDate: ' . Carbon::parse($barcode->updated_at)->format('d/m/Y H:i')),
                    "cancelDate" => Carbon::now()->format('d/m/Y-H:i')
                ]);
                $barcode->log = json_encode($logs);

                $barcode->isUsed = 0;
                $barcode->description = null;
                $barcode->bookingID = null;
                $barcode->booking_date = null;
                $barcode->cartID = null;
                $barcode->save();
            }

            if ($booking->save()) {
                $reservationRefCode = $booking->reservationRefCode;
                $carts = Cart::where('referenceCode', $reservationRefCode)->get();
                if (count($carts) == 1) {
                    $cart = $carts->first();
                    $cart->status = 5;
                    if ($cart->save()) {
                        $availability = Av::findOrFail($cart->ticketID); // ticketID is availabilityID now
                        $dateTime = $cart->dateTime;
                        $dateTimeForUs = $this->apiRelated->setTimeFormatForUs($dateTime);
                        $date = $dateTimeForUs['date'];
                        $time = $dateTimeForUs['time'];
                        $ticketCount = $cart->ticketCount;
                        if (in_array($availability->avTicketType, [1, 2, 3])) {
                            $result = null;
                            $columnStr = 'hourly';
                            if ($availability->avTicketType == 2) {
                                $columnStr = 'daily';
                            } else if ($availability->avTicketType == 3) {
                                $columnStr = 'dateRange';
                            }
                            $jsonq = $this->apiRelated->prepareJsonQ();
                            $res = $jsonq->json($availability->$columnStr);
                            if ($availability->avTicketType == 1) {
                                $result = $res->where('day', '=', $date)
                                    ->where('hour', '=', $time)
                                    ->get();
                            } else if ($availability->avTicketType == 2) {
                                $result = $res->where('day','=', $date)->get();
                            } else if ($availability->avTicketType == 3) {
                                $result = $res->where('dayFrom', 'dateLte', $date)
                                    ->where('dayTo', 'dateGte', $date)
                                    ->get();
                            }

                            if (count($result) == 1) {
                                $ticketState = 0; // this variable is used for notify push
                                $key = key($result);
                                $decoded = json_decode($availability->$columnStr, true);
                                if ($availability->isLimitless == 0) { // if availability is limitless, only make sold operation
                                    $decoded[$key]['ticket'] += $ticketCount;
                                    $ticketState = $decoded[$key]['ticket'];
                                    $decoded[$key]['ticket'] = strval($decoded[$key]['ticket']);
                                }
                                $decoded[$key]['sold'] -= $ticketCount;
                                $decoded[$key]['sold'] = strval($decoded[$key]['sold']);
                                $availability->$columnStr = json_encode($decoded);
                                if ($availability->save()) {
                                    // booking cancelled mail
                                    $mail = new Mails();
                                    $travelers = json_decode($booking->travelers, true)[0];
                                    $firstName = array_key_exists('firstName', $travelers) ? $travelers['firstName'] : '';
                                    $lastName = array_key_exists('lastName', $travelers) ? $travelers['lastName'] : '';
                                    $cartOption = Option::find($cart->optionID);
                                    $data = [];
                                    array_push($data, [
                                        'gygBookingReference' => $booking->gygBookingReference,
                                        'date' => $this->timeRelatedFunctions->convertYmdHisPlusTimezoneToDmyHi($booking->dateTime, $cartOption, $booking),
                                        'options' => $cartOption->title,
                                        'subject' => 'Booking(GYG) is cancelled! | ' . $cartOption->title . ' | '. $booking->gygBookingReference. ' | '. $this->apiRelated->getOnlyBKNNumber($booking->bookingRefCode),
                                        'name' => $firstName,
                                        'surname' => $lastName,
                                        'sendToCC' => true
                                    ]);
                                    $mail->data = json_encode($data);
                                    $mail->to = 'contact@parisviptrips.com';
                                    $mail->blade = 'mail.api-booking-cancel';
                                    $mail->save();

                                    $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($date, $time, 'Europe/Paris');
                                    // checks if the availability is limitless, ticket count dropped under 5 and not older than 1 week
                                    if ($availability->isLimitless == 0 && $ticketState < 5 && $isDateTimeValid) {
                                        $optionRefCodes = $availability->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                        foreach($optionRefCodes as $orc) {
                                            $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                        }
                                    }
                                    return response()->json(['data' => (object) null]);
                                }
                            } else {
                                return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                            }
                        } else {
                            return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                        }
                    }
                } else {
                    return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                }
            }
        }

        return $this->apiRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
    }

}

