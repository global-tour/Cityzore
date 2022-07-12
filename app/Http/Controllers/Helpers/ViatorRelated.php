<?php

namespace App\Http\Controllers\Helpers;

use App\Cart;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\BokunRelated;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Invoice;
use App\Mails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Option;
use App\Product;
use App\Booking;
use App\Currency;
use App\Supplier;
use Nahid\JsonQ\Jsonq;
use App\Http\Controllers\Helpers\RefCodeGenerator;

class ViatorRelated
{
    public $errorMessages, $apiRelated, $bokunRelated, $refCodeGenerator, $timeRelatedFunctions;
    public function __construct() {
        $this->errorMessages = array(
            'TGDS0002' => 'Authentication error',
            'TGDS0006' => 'Unknown internal error',
            'TGDS0012' => 'Invalid product',
            'TGDS0013' => 'Invalid product option code',
            'TGDS0022' => 'Missing API parameter key/value',
        );
        $this->apiRelated = new ApiRelated();
        $this->bokunRelated = new BokunRelated();
        $this->refCodeGenerator = new RefCodeGenerator();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
    }

    /* Helpers */

    public function apiAuthorization($incomingCredential, $supplierId, $resellerId)
    {

        return $incomingCredential == env('VIATOR_CREDENTIALS', '5xXGczDYtzIPS1hPl7Ge3cu8IfOdSn1YGOa5ofy-7cM')
            && $supplierId == env('VIATOR_CREDENTIALS_SUPPLIER', '5000152') && $resellerId == env('VIATOR_CREDENTIALS_RESELLER', '1000');
    }

    public function throwErrors($errorKey, $responseType) {
        $errorArray = [
            'Status'=> 'ERROR',
            'Error' => ['ErrorCode' => $errorKey, 'ErrorMessage' => $this->errorMessages[$errorKey]]
        ];

        /*
        if($responseType == 'BookingResponse') {
            return response()->json(['responseType' => $responseType, 'data' => ['RequestStatus' => $errorArray, 'TransactionStatus' => ['Status' => 'REJECTED']]]);
        }
        */

        return response()->json(['responseType' => $responseType, 'data' => ['RequestStatus' => $errorArray]]);
    }

    public function putConstantParameters($constantParameters, $data, $request) {
        foreach($constantParameters as $key => $constantParameter) {
            foreach($constantParameter as $parameter) {
                if(isset($request->$key[$parameter]))
                    $data[$key][$parameter] = $request->$key[$parameter];
            }
        }

        return $data;
    }

    public function putChangeableParameters($changeableParameters, $data) {
        foreach($changeableParameters as $key1 => $changeableParameter) {
            if(!is_array($changeableParameter))
                $data[$key1] = $changeableParameter;
            else {
                foreach ($changeableParameter as $key2 => $parameter) {
                    $data[$key1][$key2] = $parameter;
                }
            }
        }

        return $data;
    }

    public function checkMandatoryParameters($mandatoryParameters, $request) {
        foreach($mandatoryParameters as $key => $mandatoryParameter) {
            if(!is_array($mandatoryParameter)) {
                if (!isset($request->$mandatoryParameter))
                    return false;
            }
            else {
                foreach($mandatoryParameter as $key1 => $parameter) {
                    if(!is_array($parameter)) {
                        if (!isset($request->$key[$parameter]))
                            return false;
                    }
                    else { // Here will work for [book/traveller params]
                        $arr = $request->$key[$key1];
                        foreach($arr as $key2 => $ar) {
                            if (!isset($request->$key[$key1][$key2])) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function checkSemiMandatoryParameters($semiMandatoryParameters, $request) {
        foreach($semiMandatoryParameters as $key => $semiMandatoryParameter) {
            if(!is_array($semiMandatoryParameter)) {
                if (!isset($request->$semiMandatoryParameter))
                    return false;
            } else {
                foreach ($semiMandatoryParameter as $key1 => $parameter) {
                    if(!is_array($parameter)) {
                        if (!isset($request->$key[$parameter]))
                            return false;
                    } else {
                        if (isset($request->$key[$key1])) {
                            foreach ($parameter as $key2 => $param) {
                                if (!is_array($param)) {
                                    if (!isset($request->$key[$key1][$param]))
                                        return false;
                                } else {
                                    if (isset($request->$key[$key1][$key2])) {
                                        foreach ($param as $key3 => $p) {
                                            if (!is_array($p)) {
                                                if($param[count($param)-1] && $p !== $param[count($param)-1]) { // Here will work for book/question params
                                                    $arr = $request->$key[$key1][$key2];
                                                    foreach($arr as $key4 => $ar) {
                                                        if (!isset($request->$key[$key1][$key2][$key4][$p]))
                                                            return false;
                                                    }
                                                } else {
                                                    if (!isset($request->$key[$key1][$key2][$p]))
                                                        return false;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function extractBookingItemsFromTraveller($travellers) {
        $bookingItems = [];
        foreach($travellers as $traveller) {
            $doesItExist = false;
            foreach ($bookingItems as $key => $bookingItem) {
                if ($bookingItem["category"] == $traveller["AgeBand"]) {
                    $bookingItems[$key]["count"] += 1;
                    $doesItExist = true;
                }
            }
            if(!$doesItExist)
                array_push($bookingItems, ["category" => $traveller["AgeBand"], "count" => 1]);
        }

        return $bookingItems;
    }

    public function ticketCountDecrement($optionID, $ticketCount, $countVariables)
    {
        $jsonq = new Jsonq();
        $option = Option::findOrFail($optionID);
        $availability = $option->avs()->first();

        if (!$availability) {
            return ['result' => 'NO_AVAILABILITY'];
        }

        $ticketHourlyDatabase = json_decode($availability->hourly, true);
        $ticketDailyDatabase = json_decode($availability->daily, true);
        $ticketDateRangeDatabase = json_decode($availability->dateRange, true);

        if (count($ticketHourlyDatabase) > 0) {
            $res = $jsonq->json($availability->hourly);
            $result = $res->where('day', '=', $countVariables['date'])->where('hour', '=', json_decode($countVariables['hour'], true)['hour'])->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketHourlyDatabase[$key]['ticket'] = $ticketHourlyDatabase[$key]['ticket'] - $ticketCount;
                $availability->hourly = json_encode($ticketHourlyDatabase);
                if ($ticketHourlyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $res->reset();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketHourlyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDailyDatabase) > 0) {
            $res = $jsonq->json($availability->daily);
            $res->where('day', '=', $countVariables['date'])->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDailyDatabase[$key]['ticket'] -= $ticketCount;
                $availability->daily = json_encode($ticketDailyDatabase);
                if ($ticketDailyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $res->reset();
                    return ['result'=>'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDailyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDateRangeDatabase) > 0) {
            $selectedDate = $countVariables['date'];
            $res = $jsonq->json($availability->dateRange);
            $res->where('dayFrom', '<=', $selectedDate)->where('dayTo', '>=', $selectedDate)->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDateRangeDatabase[$key]['ticket'] -= $ticketCount;
                $availability->dateRange = json_encode($ticketDateRangeDatabase);
                if ($ticketDateRangeDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDateRangeDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }
    }

    public function soldCountIncrement($optionID, $ticketCount, $countVariables) {
        $jsonq = new Jsonq();
        $option = Option::findOrFail($optionID);
        $availability = $option->avs()->first();

        if (!$availability) {
            return ['result' => 'NO_AVAILABILITY'];
        }

        $ticketHourlyDatabase = json_decode($availability->hourly, true);
        $ticketDailyDatabase = json_decode($availability->daily, true);
        $ticketDateRangeDatabase = json_decode($availability->dateRange, true);

        if (count($ticketHourlyDatabase) > 0) {
            $res = $jsonq->json($availability->hourly);
            $result = $res->where('day', '=', $countVariables['date'])->where('hour', '=', json_decode($countVariables['hour'], true)['hour'])->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketHourlyDatabase[$key]['sold'] = $ticketHourlyDatabase[$key]['sold'] + $ticketCount;
                $availability->hourly = json_encode($ticketHourlyDatabase);
                if ($ticketHourlyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $res->reset();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketHourlyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDailyDatabase) > 0) {
            $res = $jsonq->json($availability->daily);
            $res->where('day', '=', $countVariables['date'])->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDailyDatabase[$key]['sold'] += $ticketCount;
                $availability->daily = json_encode($ticketDailyDatabase);
                if ($ticketDailyDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    $res->reset();
                    return ['result'=>'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDailyDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }

        if (count($ticketDateRangeDatabase) > 0) {
            $selectedDate = $countVariables['date'];
            $res = $jsonq->json($availability->dateRange);
            $res->where('dayFrom', '<=', $selectedDate)->where('dayTo', '>=', $selectedDate)->get();
            $resDecoded = json_decode($res->toJson(), true);
            $key = key($resDecoded);
            if (array_key_exists($key, $resDecoded)) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDateRangeDatabase[$key]['sold'] += $ticketCount;
                $availability->dateRange = json_encode($ticketDateRangeDatabase);
                if ($ticketDateRangeDatabase[$key]['ticket'] >= 0) {
                    $availability->save();
                    return ['result' => 'SUCCESSFUL_RESERVATION', 'ticketState' => $ticketDateRangeDatabase[$key]['ticket']];
                } else {
                    $res->reset();
                    return ['result' => 'NO_AVAILABILITY'];
                }
            } else {
                $res->reset();
                return ['result' => 'NO_AVAILABILITY'];
            }
        }
    }

    public function ticketCountIncrement($booking)
    {
        $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
        $cart = Cart::where('referenceCode', '=', $booking->reservationRefCode)->first();
        $availability = $option->avs()->first();
        $ticketHourlyDatabase = json_decode($availability->hourly, true);
        $ticketDailyDatabase = json_decode($availability->daily, true);
        $ticketDateRangeDatabase = json_decode($availability->dateRange, true);
        $selectedDate = $booking->date;
        $jsonq = new Jsonq();
        $selectedHour = json_decode($booking->hour, true)[0]['hour'];
        if (count($ticketHourlyDatabase) > 0) {
            $ticketCount = count(json_decode($booking->travelers));
            $res = $jsonq->json($availability->hourly);
            $res->where('day', '=', $selectedDate)->where('hour', '=', $selectedHour)->get();
            if ($res->count() > 0) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketHourlyDatabase[$key]['ticket'] = json_encode($ticketHourlyDatabase[$key]['ticket'] + $ticketCount);
                $ticketHourlyDatabase[$key]['sold'] = json_encode($ticketHourlyDatabase[$key]['sold'] - $ticketCount);
                $availability->hourly = json_encode($ticketHourlyDatabase);
            }
            $availability->save();
            $res->reset();
            return ['result' => 'BOOKING_CANCELLED', 'ticketState' => $ticketHourlyDatabase[$key]['ticket']];
        } else if (count($ticketDailyDatabase) > 0) {
            $ticketCount = $cart->ticketCount;
            $res = $jsonq->json($availability->daily);
            $res->where('day', '=', $selectedDate)->get();
            if ($res->count() > 0) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDailyDatabase[$key]['ticket'] = json_encode($ticketDailyDatabase[$key]['ticket'] + $ticketCount);
                $ticketDailyDatabase[$key]['sold'] = json_encode($ticketDailyDatabase[$key]['sold'] - $ticketCount);
                $availability->daily = json_encode($ticketDailyDatabase);
            }
            $availability->save();
            $res->reset();
            return ['result' => 'BOOKING_CANCELLED', 'ticketState' => $ticketDailyDatabase[$key]['ticket']];
        } else if (count($ticketDateRangeDatabase) > 0) {
            $ticketCount = $cart->ticketCount;
            $res = $jsonq->json($availability->dateRange);
            $res->where('dayFrom', '<=', $selectedDate)->where('dayTo', '>=', $selectedDate)->get();
            if ($res->count() > 0) {
                $key = array_keys(json_decode($res->toJson(), true))[0];
                $ticketDateRangeDatabase[$key]['ticket'] = json_encode($ticketDateRangeDatabase[$key]['ticket'] + $ticketCount);
                $ticketDateRangeDatabase[$key]['sold'] = json_encode($ticketDateRangeDatabase[$key]['sold'] - $ticketCount);
                $availability->dateRange = json_encode($ticketDateRangeDatabase);
            }
            $availability->save();
            $res->reset();
            return ['result' => 'BOOKING_CANCELLED', 'ticketState' => $ticketDateRangeDatabase[$key]['ticket']];
        }

        return ['result' => $this->throwErrors('INTERNAL_SYSTEM_FAILURE')];
    }

    /* Main */

    public function getAvailability($request) {
        $startDate = $request->data['StartDate'];
        $endDate = isset($request->data['EndDate']) ? $request->data['EndDate'] : null;
        // $supplierProductCode = $request->data['SupplierProductCode'];
        // $supplierOptionCode = isset($request->data['TourOptions']) ? (isset($request->data['TourOptions']['SupplierOptionCode']) ? $request->data['TourOptions']['SupplierOptionCode']: null) : null;
        $supplierOptionCode = $request->data['SupplierProductCode'];
        $tourDepartureTime = isset($request->data['TourOptions']) ? (isset($request->data['TourOptions']['TourDepartureTime']) ? $request->data['TourOptions']['TourDepartureTime']: null) : null;

        $ticketCount = null;
        if(isset($request->data['TravellerMix'])) {
            if(isset($request->data['TravellerMix']['Total']))
                $ticketCount = intval($request->data['TravellerMix']['Total']);
            else {
                if(count($request->data['TravellerMix']) > 0) {
                    $ticketCount = 0;
                    foreach ($request->data['TravellerMix'] as $travellerCount) {
                        $ticketCount += intVal($travellerCount);
                    }
                }
            }
        }

        $option = Option::where('referenceCode', $supplierOptionCode)->first();
        if(!$option) return ['status' => 'error', 'errorkey' => 'TGDS0013'];

        /*
        $product = Product::where('referenceCode', $supplierProductCode)->first();
        if(!$product) return ['status' => 'error', 'errorkey' => 'TGDS0012'];

        if($supplierOptionCode) {
            $optionExists = Option::where('referenceCode', $supplierOptionCode)->first();
            if(!$optionExists || ($optionExists && !$product->options()->where('referenceCode', $optionExists->referenceCode)->first()))
                return ['status' => 'error', 'errorkey' => 'TGDS0013'];
        }
        */

        $data = [];
        // $options = $product->options()->get();

        // foreach($options as $option) {
            if(!$supplierOptionCode || ($supplierOptionCode && $supplierOptionCode == $option->referenceCode)) {
                $availability = $option->avs->first();

                $blockoutHours = [];
                $avBlockoutHours = json_decode($availability->blockoutHours, true);
                foreach ($avBlockoutHours as $avBlockoutHour) {
                    if (array_key_exists($option->referenceCode, $avBlockoutHour)) {
                        $blockoutHours = $avBlockoutHour[$option->referenceCode];
                    }
                }

                if ($availability->availabilityType == 'Starting Time') {
                    $hourly = json_decode($availability->hourly, true);
                    if (count($hourly) > 0) {
                        foreach ($hourly as $h) {
                            if ($h['isActive'] == 1) {
                                $dayOfHourly = strtotime(date('Y-m-d', strtotime(explode('/', $h['day'])[2] . '-' . explode('/', $h['day'])[1] . '-' . explode('/', $h['day'])[0])));
                                $capacity = $availability->isLimitless == 1 ? 9999 : (int)$h['ticket'];
                                if (($endDate ? ($dayOfHourly >= strtotime($startDate) && $dayOfHourly <= strtotime($endDate)) : $dayOfHourly == strtotime($startDate)) && !in_array(date('d\/m\/Y', $dayOfHourly), json_decode($availability->disabledDays, true))) {
                                    if (!in_array(explode('/', $h['day'])[2], $availability->disabledYears ? json_decode($availability->disabledYears, true) : []) && !in_array(explode('/', $h['day'])[1], $availability->disabledMonths ? json_decode($availability->disabledMonths, true) : []) && !in_array(strtolower(Carbon::createFromTimestamp($dayOfHourly)->format('l')), $availability->disabledWeekDays ? json_decode($availability->disabledWeekDays, true) : [])) {
                                        if(!$tourDepartureTime || ($tourDepartureTime && $tourDepartureTime == $h['hour'] . ':00')) {
                                            $availabilityStatus = ["Status" => "AVAILABLE"];

                                            $isInBlockArr = false;
                                            foreach($blockoutHours as $blockoutHour) {
                                                if(isset($blockoutHour['hours'])) {
                                                    $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $h['day'])[1], $blockoutHour['months'])) && in_array($h['hour'], $blockoutHour['hours']));
                                                    $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $h['day'])->format('l'), $blockoutHour['days'])) && in_array($h['hour'], $blockoutHour['hours']));
                                                    $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($h['hour'], $blockoutHour['hours'])));
                                                } elseif(isset($blockoutHour['days'])) {
                                                    $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $h['day'])[1], $blockoutHour['months'])) && in_array(Carbon::createFromFormat('d/m/Y', $h['day'])->format('l'), $blockoutHour['days']));
                                                    $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $h['day'])->format('l'), $blockoutHour['days'])));
                                                    $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($h['hour'], $blockoutHour['hours'])) && in_array(Carbon::createFromFormat('d/m/Y', $h['day'])->format('l'), $blockoutHour['days']));
                                                } elseif(isset($blockoutHour['months'])) {
                                                    $a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && in_array(explode('/', $h['day'])[1], $blockoutHour['months'])));
                                                    $b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && in_array(Carbon::createFromFormat('d/m/Y', $h['day'])->format('l'), $blockoutHour['days'])) && in_array(explode('/', $h['day'])[1], $blockoutHour['months']));
                                                    $c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && in_array($h['hour'], $blockoutHour['hours'])) && in_array(explode('/', $h['day'])[1], $blockoutHour['months']));
                                                }

                                                if(!$a || !$b || !$c) {
                                                    //
                                                } else {
                                                    $isInBlockArr = true;
                                                    break;
                                                }
                                            }

                                            if ($capacity <= 0)
                                                $availabilityStatus = [
                                                    "Status" => "UNAVAILABLE",
                                                    "UnavailabilityReason" => "SOLD_OUT"
                                                ];
                                            if ($isInBlockArr)
                                                $availabilityStatus = [
                                                    "Status" => "UNAVAILABLE",
                                                    "UnavailabilityReason" => "BLOCKED_OUT"
                                                ];
                                            if ($ticketCount && $capacity < $ticketCount)
                                                $availabilityStatus = [
                                                    "Status" => "UNAVAILABLE",
                                                    "UnavailabilityReason" => "TRAVELLER_MISMATCH"
                                                ];

                                            array_push($data, [
                                                "Date" => Carbon::createFromFormat('d/m/Y', $h['day'])->format('Y-m-d'),
                                                "TourOptions" => [
                                                    // "SupplierOptionCode" => $option->referenceCode,
                                                    // "SupplierOptionName" => $option->title,
                                                    "TourDepartureTime" => $h['hour'] . ':00'
                                                ],
                                                "AvailabilityStatus" => $availabilityStatus
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($availability->availabilityType == 'Operating Hours') {
                    if ($availability->avTicketType == 2) { // Daily Ticket Type
                        $daily = json_decode($availability->daily, true);
                        if (count($daily) > 0) {
                            foreach ($daily as $d) {
                                if ($d['isActive'] == 1) {
                                    $dayOfHourly = strtotime(date('Y-m-d', strtotime(explode('/', $d['day'])[2] . '-' . explode('/', $d['day'])[1] . '-' . explode('/', $d['day'])[0])));
                                    $capacity = $availability->isLimitless == 1 ? 9999 : (int)$d['ticket'];
                                    if (($endDate ? ($dayOfHourly >= strtotime($startDate) && $dayOfHourly <= strtotime($endDate)) : $dayOfHourly == strtotime($startDate)) && !in_array(date('d\/m\/Y', $dayOfHourly), json_decode($availability->disabledDays, true))) {
                                        if (!in_array(explode('/', $d['day'])[2], $availability->disabledYears ? json_decode($availability->disabledYears, true) : []) && !in_array(explode('/', $d['day'])[1], $availability->disabledMonths ? json_decode($availability->disabledMonths, true) : []) && !in_array(strtolower(Carbon::createFromTimestamp($dayOfHourly)->format('l')), $availability->disabledWeekDays ? json_decode($availability->disabledWeekDays, true) : [])) {
                                            if(!$tourDepartureTime || ($tourDepartureTime && $tourDepartureTime == $d['hourFrom'] . ':00')) {
                                                $availabilityStatus = ["Status" => "AVAILABLE"];
                                                if ($capacity <= 0)
                                                    $availabilityStatus = [
                                                        "Status" => "UNAVAILABLE",
                                                        "UnavailabilityReason" => "SOLD_OUT"
                                                    ];
                                                if ($ticketCount && $capacity < $ticketCount)
                                                    $availabilityStatus = [
                                                        "Status" => "UNAVAILABLE",
                                                        "UnavailabilityReason" => "TRAVELLER_MISMATCH"
                                                    ];

                                                array_push($data, [
                                                    "Date" => Carbon::createFromFormat('d/m/Y', $d['day'])->format('Y-m-d'),
                                                    "TourOptions" => [
                                                        // "SupplierOptionCode" => $option->referenceCode,
                                                        // "SupplierOptionName" => $option->title,
                                                        "TourDepartureTime" => $d['hourFrom'] . ':00'
                                                    ],
                                                    "AvailabilityStatus" => $availabilityStatus
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($availability->avTicketType == 3) { // Date Range Ticket Type
                        $dateRange = json_decode($availability->dateRange, true);
                        $daily = $availability->daily;
                        $jsonq = $this->apiRelated->prepareJsonQ();
                        $res = $jsonq->json($daily);
                        $result = $res->get();
                        if (count($dateRange) > 0) {
                            $firstDayOfDateRange = array_first($dateRange)['dayFrom'];
                            $firstDayOfDateRange = strtotime(str_replace('/', '-', $firstDayOfDateRange));
                            $lastDayOfDateRange = last($dateRange)["dayTo"];
                            $lastDayOfDateRange = strtotime(str_replace('/', '-', $lastDayOfDateRange));
                            foreach ($result as $res) {
                                $dailyDay = strtotime(str_replace('/', '-', $res['day']));
                                $hourFrom = $res['hourFrom'];
                                // $range = $request->range;
                                // $date = $this->bokunRelated->bokunDateToCityZoreDMY($range);
                                $capacity = $availability->isLimitless == 1 ? 9999 : (int)$dateRange[0]['ticket'];
                                if ($capacity > 0 && $res['isActive'] == 1 && $dailyDay >= $firstDayOfDateRange && $dailyDay <= $lastDayOfDateRange && ($endDate ? ($dailyDay >= strtotime($startDate) && $dailyDay <= strtotime($endDate)) : $dailyDay == strtotime($startDate)) && !in_array(date('d\/m\/Y', $dayOfHourly), json_decode($availability->disabledDays, true))) {
                                    if (!in_array(explode('/', $res['day'])[2], $availability->disabledYears ? json_decode($availability->disabledYears, true) : []) && !in_array(explode('/', $res['day'])[1], $availability->disabledMonths ? json_decode($availability->disabledMonths, true) : []) && !in_array(strtolower(Carbon::createFromTimestamp($dayOfHourly)->format('l')), $availability->disabledWeekDays ? json_decode($availability->disabledWeekDays, true) : [])) {
                                        if(!$tourDepartureTime || ($tourDepartureTime && $tourDepartureTime == $res['hourFrom'] . ':00')) {
                                            $availabilityStatus = ["Status" => "AVAILABLE"];
                                            if ($capacity <= 0)
                                                $availabilityStatus = [
                                                    "Status" => "UNAVAILABLE",
                                                    "UnavailabilityReason" => "SOLD_OUT"
                                                ];
                                            if ($ticketCount && $capacity < $ticketCount)
                                                $availabilityStatus = [
                                                    "Status" => "UNAVAILABLE",
                                                    "UnavailabilityReason" => "TRAVELLER_MISMATCH"
                                                ];

                                            array_push($data, [
                                                "Date" => Carbon::createFromFormat('d/m/Y', $res['day'])->format('Y-m-d'),
                                                "TourOptions" => [
                                                    // "SupplierOptionCode" => $option->referenceCode,
                                                    // "SupplierOptionName" => $option->title,
                                                    "TourDepartureTime" => $res['hourFrom'] . ':00'
                                                ],
                                                "AvailabilityStatus" => $availabilityStatus
                                            ]);
                                        }
                                    }
                                }
                            }
                            return $data;
                        }
                    }
                }
            }
        // }

        return $data;
    }

    public function getTourList($request) {
        $data = [];
        // $products = Product::all();

        // foreach($products as $product) {
            // $options = $product->options()->where('isMixed', 0)->where('isPublished', 1)->get();
            $options = Option::where('isMixed', 0)->where('isPublished', 1)->get();
            foreach($options as $option) {
                array_push($data, [
                    /*
                    'TourOption' => [
                        'SupplierOptionCode' => $option->referenceCode,
                        'SupplierOptionName' => $option->title,
                    ],
                    */
                    'SupplierProductCode' => $option->referenceCode,
                    'SupplierProductName' => $option->title,
                    'CountryCode' => isset($option->products[0]) ? $option->products[0]->countryName->countries_iso_code : 'FR',
                    'DestinationCode' => 'FR PAR',
                    'DestinationName' => isset($option->products[0]) ? $option->products[0]->city : 'Paris',
                    'TourDescription' => isset($option->products[0]) ? $option->products[0]->shortDesc : $option->description
                ]);
            }
        // }

        return $data;
    }

    public function book($request) {

            $travellers = [
                [
                    'firstName' => isset($request->data['Traveller']['LeadTraveller']) && $request->data['Traveller']['LeadTraveller'] ? $request->data["Traveller"]['GivenName'] : $request->data["Traveller"][0]['GivenName'],
                    'lastName' => isset($request->data['Traveller']['LeadTraveller']) && $request->data['Traveller']['LeadTraveller'] ? $request->data["Traveller"]['Surname'] :  $request->data["Traveller"][0]['Surname'],
                    'email' => isset($request->data['ContactEmail']) ? $request->data['ContactEmail'] : '',
                    'phoneNumber' => '',
                    'TravellerIdentifier' => isset($request->data['Traveller']['LeadTraveller']) && $request->data['Traveller']['LeadTraveller'] ? $request->data["Traveller"]['TravellerIdentifier'] : $request->data["Traveller"][0]['TravellerIdentifier'],
                    'AgeBand' => isset($request->data['Traveller']['LeadTraveller']) && $request->data['Traveller']['LeadTraveller'] ? $request->data["Traveller"]['AgeBand'] : $request->data["Traveller"][0]['AgeBand'],
                ]
            ];
            $travellersForResponse = [];

            foreach($travellers as $traveller) {
                if(isset($traveller['LeadTraveller']) && $traveller['LeadTraveller'] == true)
                    $travellers = [
                        [
                            'firstName' => $traveller['GivenName'],
                            'lastName' => $traveller['Surname'],
                            'email' => isset($request->data['ContactEmail']) ? $request->data['ContactEmail'] : '',
                            'phoneNumber' => ''
                        ]
                    ];

                array_push($travellersForResponse, [
                    'TravellerIdentifier' => $traveller['TravellerIdentifier']
                ]);
            }

            $countVariables = [
                "date" => Carbon::createFromFormat('Y-m-d', $request->data["TravelDate"])->format('d/m/Y'),
                "hour" => json_encode(["hour" => explode(':', $request->data["TourOptions"]["TourDepartureTime"])[0] . ':' . explode(':', $request->data["TourOptions"]["TourDepartureTime"])[1]])
            ];

            /* Copy of Bokun Reservation Function */
            $option = Option::where('referenceCode', $request->data['SupplierProductCode'])->first();

            if (is_null($option)) {
                return ['status' => 'error', 'errorkey' => 'TGDS0012'];
            }

            $availability = $option->avs()->first();
            if ($availability->isLimitless == 0) {
                $ticketCountDecrementReturn = $this->ticketCountDecrement($option->id, count($travellers), $countVariables);
                $result = $ticketCountDecrementReturn['result'];
                $ticketState = array_key_exists('ticketState', $ticketCountDecrementReturn) ? $ticketCountDecrementReturn['ticketState'] : 0;
                if ($result == 'SUCCESSFUL_RESERVATION') {
                    $time = $availability->availabilityType == 'Starting Time' ? json_decode($countVariables['hour'], true)['hour'] : "00:00";
                    $date = str_replace('-', '/', $countVariables['date']);
                    $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($date, $time, 'Europe/Paris');
                    // checks if the availability is limitless, ticket count dropped under 5 and not older than 1 week
                    if ($availability->isLimitless == 0 && $ticketState < 5 && $isDateTimeValid) {
                        $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($date, $time, 'Europe/Paris');
                        $optionRefCodes = $availability->options()->where('connectedToApi', 1)->pluck('referenceCode');
                        foreach ($optionRefCodes as $orc) {
                            $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                        }
                    }
                }
            } else {
                $result = 'SUCCESSFUL_RESERVATION';
            }

            if ($result == 'NO_AVAILABILITY')
                return ['status' => 'error', 'errorkey' => 'TGDS0006'];
            elseif ($result == 'SUCCESSFUL_RESERVATION') {
                $referenceCode = $this->refCodeGenerator->refCodeGeneratorForCart();

                $cart = new Cart();
                $cart->referenceCode = $referenceCode;
                $cart->status = 2;
                $cart->optionID = $option->id;
                $cart->date = Carbon::createFromFormat('Y-m-d', $request->data["TravelDate"])->format('d/m/Y');
                $cart->hour = json_encode([["hour" => explode(':', $request->data["TourOptions"]["TourDepartureTime"])[0] . ':' . explode(':', $request->data["TourOptions"]["TourDepartureTime"])[1]]]);
                $cart->ticketCount = count($travellers);
                $cart->dateTime = date('Y-m-d', strtotime($request->data["TravelDate"])) . 'T' . json_decode($cart->hour, true)[0]['hour'] . ':00+01:00';
                $cart->isViator = 1;
                $cart->bookingItems = json_encode($this->extractBookingItemsFromTraveller($travellers));
                $cart->totalPrice = isset($request->data['Amount']) ? $request->data['Amount'] : 0;
                $cart->totalPriceWOSO = isset($request->data['Amount']) ? $request->data['Amount'] : 0;
                $cart->ticketID = $availability->id;
                $cart->currencyID = isset($request->data['CurrencyCode']) ? (Currency::where('currency', $request->data['CurrencyCode'])->first() ? Currency::where('currency', $request->data['CurrencyCode'])->first()->id : 2) : 2;
                $cart->save();

                $booking = new Booking();
                $booking->status = 0;
                // $booking->optionRefCode = $request->data['TourOptions']['SupplierOptionCode'];
                $booking->optionRefCode = $request->data['SupplierProductCode'];
                $booking->reservationRefCode = $cart->referenceCode;
                $booking->bookingRefCode = 'BR-' . $request->data['BookingReference'];
                $booking->bookingItems = $cart->bookingItems;
                $booking->dateTime = $cart->dateTime;
                $booking->date = $cart->date;
                $booking->dateForSort = $request->data["TravelDate"];
                $booking->hour = $cart->hour;
                $booking->language = 'EN';
                $booking->travelers = json_encode($travellers);
                $booking->fullName = $travellers[0]['firstName'] . ' ' . $travellers[0]['lastName'];
                $booking->totalPrice = $cart->totalPrice;
                $booking->currencyID = $cart->currencyID;
                $booking->companyID = -1;
                $booking->fromWebsite = 'Viator';
                $booking->platformID = 5;
                $booking->isViator = 1;

                $avID = [];
                $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
                foreach ($bAvs as $bAv) {
                    array_push($avID, $bAv->id);
                }
                $booking->avID = json_encode($avID);

                if ($booking->save()) {
                    $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
                    $this->soldCountIncrement($option->id, count($travellers), $countVariables);
                    $availability = $option->avs()->first();
                    $hour = $availability->type == "Operating Hours" ? "Operating Hours" : json_decode($booking->hour, true)[0]['hour'];

                    $invoice = new Invoice();
                    $invoice->paymentMethod = 'API';
                    $invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
                    $invoice->bookingID = $booking->id;
                    $invoice->companyID = -1;
                    if ($invoice->save()) {
                        $booking->invoiceID = $invoice->id;
                    }
                    if ($booking->save()) {
                        $this->apiRelated->placeBarcodeForBooking($booking);

                        return [
                            'Traveller' => $travellersForResponse,
                            'SupplierConfirmationNumber' => $booking->bookingRefCode
                        ];
                    } else {
                        return ['status' => 'error', 'errorkey' => 'TGDS0006']; // Invoice Not Created
                    }
                } else {
                    return ['status' => 'error', 'errorkey' => 'TGDS0006']; // Booking Not Created
                }
            }

    }

    public function cancelBooking($request) {
        $bookingConfirmationCode = $request->data['BookingReference'];
        $booking = Booking::where('bookingRefCode', '=', 'BR-' . $bookingConfirmationCode)->where('status', 0)->first();
        $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
        $availability = $option->avs()->first();
        $ticketCountIncrementReturn = $this->ticketCountIncrement($booking);
        $result = $availability->isLimitless == 0 ? $ticketCountIncrementReturn['result'] : 'BOOKING_CANCELLED';
        $ticketState = $ticketCountIncrementReturn['ticketState'];
        if ($result == 'BOOKING_CANCELLED') {
            $booking->status = 2;
            if ($booking->save()) {
                $hour = $availability->type == 'Operating Hours' ? 'Operating Hours ' : json_decode($booking->hour, true)[0]['hour'];
                /*
                $mail = new Mails();
                $mail->to = 'contact@parisviptrips.com';
                $mail->data = json_encode([
                    [
                        'BKNCode' => $booking->bookingRefCode,
                        'name' => $booking->fullName,
                        'date' => $booking->dateForSort,
                        'hour' => $hour,
                        'options' => $option->title,
                        'subject' => $booking->bookingRefCode. ' - Booking Cancelled !',
                        'sendToCC' => true
                    ]
                ]);
                $mail->blade = 'mail.booking-cancel-for-creator';
                $mail->status = 0;
                $mail->save();
                */
                $date = explode('-',$booking->dateForSort)[2].'/'.explode('-',$booking->dateForSort)[1].'/'.explode('-',$booking->dateForSort)[0];
                $time = $availability->availabilityType == 'Starting Time' ? json_decode($booking->hour, true)[0]['hour'] : '00:00';
                $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($date, $time, 'Europe/Paris');
                // ticket count dropped under 5 and not older than 1 week
                if ($ticketState < 5 && $isDateTimeValid) {
                    $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($date, $time, 'Europe/Paris');
                    $optionRefCodes = $availability->options()->where('connectedToApi', 1)->pluck('referenceCode');
                    foreach ($optionRefCodes as $orc) {
                        $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                    }
                }

                return ([]);
            } else {
                return ['status' => 'error', 'errorkey' => 'TGDS0006'];
            }
        }
    }

    public function amendBooking($request) {
        $bookings = Booking::where('bookingRefCode', 'BR-' . $request->data['BookingReference'])->where('status', 0)->get();
        if(count($bookings) <= 0) return ['status' => 'error', 'errorkey' => 'TGDS0006']; // There's no booking to amend
        foreach($bookings as $booking) {
            $booking->status = 1;
            $booking->save();
        }

        return $this->book($request);
    }
}
