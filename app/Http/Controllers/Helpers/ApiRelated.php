<?php

namespace App\Http\Controllers\Helpers;

use App\Physicalticket;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;
use GuzzleHttp\Client;
use App\Option;
use App\Apilog;
use Nahid\JsonQ\Jsonq;
use App\Mails;
use App\GygNotification;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Barcode;
use function GuzzleHttp\Psr7\str;


class ApiRelated
{
    public $commonFunctions;
    public $refCodeGenerator;
    public $timeRelatedFunctions;
    public $errorMessages;

    public function __construct()
    {
        $this->commonFunctions = new CommonFunctions();
        $this->refCodeGenerator = new RefCodeGenerator();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->errorMessages = [
            'AUTHORIZATION_FAILURE' => 'The provided credentials are not valid.',
            'VALIDATION_FAILURE' => 'The request object contains inconsistent or invalid data or is missing data.',
            'INVALID_PRODUCT' => 'The specified product does not exist or is broken for another reason (excluding availability issues).',
            'INTERNAL_SYSTEM_FAILURE' => 'An error occurred that is unexpected.',
            'NO_AVAILABILITY' => 'The reservation or booking call cannot be fullfilled because there is insufficient availability.',
            'INVALID_RESERVATION' => 'The specified reservation does not exist or is not in a valid state for the requested operation.',
            'INVALID_BOOKING' => 'The specified booking does not exist or is not in a valid state.',
            'INVALID_TICKET_CATEGORY' => 'The reservation or booking call specified a ticket category that is not configured for the requested product.',
            'INVALID_PARTICIPANTS_CONFIGURATION' => 'Number of participants is outside the required range'
        ];
    }

    /**
     * Basic Access Authorization
     *
     * @param $request
     * @return bool
     */
    public function apiAuthorization($request)
    {
        $authorization = $request->header('authorization');
        $authorization = explode("Basic ", $authorization);
        $usernamePassword = array_key_exists(1, $authorization) ? base64_decode($authorization[1]) : '';
        return $usernamePassword == env('GETYOURGUIDE_CREDENTIALS', 'gygtopctapi:msEYa2ZR=9ma+^Jw');
    }

    /**
     * DateTime Format can be 2016-12-01T00:00:00+02:00 or 2016-12-01T00:00:00%2B02:00
     * (+ character in request receiving us as ' ', so we need to do this to handle errors)
     *
     * @param $dateString
     * @return Carbon|string
     */
    public function getTimeFormatCorrectly($dateString)
    {
        if (strpos($dateString, ' ') !== false) {
            $dateString = explode(' ', $dateString);
            $dateString = implode('+', $dateString);
        }
        $date = Carbon::parse($dateString);
        $date = $date->toIso8601String();
        return $date;
    }

    /**
     * returns dateForSort string as Y-m-d
     *
     * @param $dateTime
     * @return string
     */
    public function dateForSortOperation($dateTime)
    {
        $withoutTimeZone = explode('+', $dateTime)[0];
        $dateForSort = Carbon::createFromFormat('Y-m-d\TH:i:s', $withoutTimeZone, 'Europe/Paris');
        $dateForSortFormatted = $dateForSort->format('Y-m-d');

        return $dateForSortFormatted;
    }

    /**
     * @param $booking
     * @param $bookingItems
     * @param $bookingReference
     * @return array
     */
    public function makeTicketResponseForBooking($booking, $bookingItems, $bookingReference)
    {
        $tickets = array();
        foreach ($bookingItems as $bookingItem) {
            for ($i=0; $i<$bookingItem['count']; $i++) {
                //$ticketCode = $this->refCodeGenerator->refCodeGeneratorForPhysicalTicket($bookingReference);
                $ticketCode = $booking->gygBookingReference;
                $physicalTicket = new Physicalticket();
                $physicalTicket->bookingReference = $bookingReference;
                $physicalTicket->ticketCategory = $bookingItem['category'];
                $physicalTicket->ticketCode = $ticketCode;
                $physicalTicket->ticketCodeType = 'TEXT'; // Possible values are TEXT, BARCODE_CODE39, BARCODE_CODE128, QR_CODE, DATA_MATRIX, EAN_13, ITF
                if ($physicalTicket->save()) {
                    $booking->physicalTickets()->attach($physicalTicket->id);
                }
                array_push($tickets,
                    [
                        'category' => $bookingItem['category'],
                        'ticketCode' => $ticketCode,
                        'ticketCodeType' => 'TEXT'
                    ]
                );
            }
        }

        return $tickets;
    }

    /**
     * fromDateTime => fromDate('d/m/Y'), fromTime('H:i')
     * toDateTime => toDate('d/m/Y'), toTime('H:i')
     *
     * @param $dateString
     * @return array
     */
    public function setTimeFormatForUs($dateString)
    {
        $dateString = explode('+', $dateString)[0];
        $dateString = Carbon::createFromFormat('Y-m-d\TH:i:s', $dateString);
        $date = $dateString->format('d/m/Y');
        $time = $dateString->format('H:i');
        return array('date' => $date, 'time' => $time);
    }

    /**
     * throws VALIDATION_FAILURE error on API Documentation. Checks if fromDateTime, toDateTime and productId values are existing
     *
     * @param $request
     * @param $type
     * @return bool
     */
    public function isDataValid($request, $type)
    {
        if ($type == 'get-availabilities') {
            return isset($request->fromDateTime) && isset($request->toDateTime) && isset($request->productId);
        } else if ($type == 'reserve') {
            return isset($request->data) && array_key_exists('dateTime', $request->data)
                && array_key_exists('productId', $request->data)
                && array_key_exists('bookingItems', $request->data)
                && array_key_exists('gygBookingReference', $request->data);
        } else if ($type == 'cancel-reservation') {
            return isset($request->data) && array_key_exists('reservationReference', $request->data)
                && array_key_exists('gygBookingReference', $request->data);
        } else if ($type == 'book') {
            return isset($request->data) && array_key_exists('bookingItems', $request->data)
                && array_key_exists('dateTime', $request->data) && array_key_exists('gygBookingReference', $request->data)
                && array_key_exists('productId', $request->data) && array_key_exists('reservationReference', $request->data)
                && array_key_exists('travelers', $request->data);
        } else if ($type == 'cancel-booking') {
            return isset($request->data) && array_key_exists('bookingReference', $request->data)
                && array_key_exists('gygBookingReference', $request->data)
                && array_key_exists('productId',$request->data);
        }
        return false;
    }

    /**
     * Gets Disabled Days for GYG API
     *
     * @param $availability
     * @return array
     * @throws \Exception
     */
    public function getDisabledDatesForGYG($availability)
    {
        $disabledDates = [];
        $avdates = $availability->avdates()->get();

        // disabled = disabled + disabledDays
        array_push($disabledDates, json_decode($availability->disabledDays, true));
        /////////////

        $disabledDates = $this->commonFunctions->flatten($disabledDates);

        // disabled = disabled + disabledYears
        $disabledYears = json_decode($availability->disabledYears, true);
        if (!is_null($disabledYears)) {
            foreach ($disabledYears as $dy) {
                $begin = DateTime::createFromFormat('Y-m-d', $dy . '-01-01');
                $begin = $begin->format('d/m/Y');
                $begin = DateTime::createFromFormat('d/m/Y', $begin);
                $end = DateTime::createFromFormat('Y-m-d', $dy . '-12-31');
                $end = $end->format('d/m/Y');
                $end = DateTime::createFromFormat('d/m/Y', $end);
                $interval = new DateInterval('P1D');
                $end->add($interval);
                $period = new DatePeriod($begin, $interval, $end);
                foreach ($period as $dt) {
                    $formattedDt = $dt->format('d/m/Y');
                    array_push($disabledDates, $formattedDt);
                }
            }
        }
        /////////////

        // disabled = disabled + disabledMonths
        $disabledMonths = json_decode($availability->disabledMonths, true);
        if (!is_null($disabledMonths)) {
            $minYear = strtok($avdates->min('valid_from'), '-');
            $maxYear = strtok($avdates->max('valid_to'), '-');
            $allYears = range($minYear, $maxYear);

            foreach ($allYears as $aY) {
                foreach ($disabledMonths as $dM) {
                    $begin = DateTime::createFromFormat('Y-m-d', $aY . '-' . $dM . '-01');
                    $begin = $begin->format('d/m/Y');
                    $begin = DateTime::createFromFormat('d/m/Y', $begin);
                    $end = DateTime::createFromFormat('Y-m-d', $aY . '-' . $dM . '-01')->modify('last day of this month');
                    $end = $end->format('d/m/Y');
                    $end = DateTime::createFromFormat('d/m/Y', $end);
                    $interval = new DateInterval('P1D');
                    $end->add($interval);
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $dt) {
                        $formattedDt = $dt->format('d/m/Y');
                        array_push($disabledDates, $formattedDt);
                    }
                }
            }
        }
        /////////////

        // disabled = disabled + weekDays
        $disabledWeekDays = json_decode($availability->disabledWeekDays, true);
        if (!is_null($disabledWeekDays)) {
            $startDate = new DateTime($avdates->min('valid_from'));
            $endDate = new DateTime($avdates->max('valid_to'));
            foreach ($disabledWeekDays as $dwd) {
                while ($startDate <= $endDate) {
                    if (strtolower($startDate->format('l')) == $dwd) {
                        array_push($disabledDates, $startDate->format('d/m/Y'));
                    }
                    $startDate->modify('+1 day');
                }
                $startDate = new DateTime($avdates->min('valid_from'));
            }
        }
        /////////////

        $disabledDates = $this->commonFunctions->flatten($disabledDates);
        $disabledDates = array_values(array_unique($disabledDates));

        return $disabledDates;
    }

    /**
     * Date Comparison for jsonq: $type can be dateGte or dateLte
     *
     * @param $val
     * @param $comp
     * @param $type
     * @return bool
     */
    public function dateComparison($val, $comp, $type)
    {
        if (strpos($val, '/')) {
            $date_split = explode('/', $val);
            $date_format = $date_split[2]. '-' . $date_split[1] . '-' . $date_split[0];

        } else {
            $date_split = explode('.', $val);
            $date_format = $date_split[2]. '-' . $date_split[1] . '-' . $date_split[0];

        }

        if (strpos($comp, '/')) {
            $comp_split = explode('/', $comp);
            $comp_format = $comp_split[2]. '-' . $comp_split[1] . '-' . $comp_split[0];
        }
        else {
            $comp_split = explode('.', $comp);
            $comp_format = $comp_split[2]. '-' . $comp_split[1] . '-' . $comp_split[0];
        }
        $comp = strtotime($comp_format);
        $date = strtotime($date_format);
        return $type == 'dateGte' ? $comp <= $date : $comp >= $date;
    }

    /**
     * Time Comparison for jsonq: $type can be timeGte or timeLte
     *
     * @param $val
     * @param $comp
     * @param $type
     * @return bool
     */
    public function timeComparison($val, $comp, $type)
    {
        $comp = strtotime($comp);
        $val = strtotime($val);
        return $type == 'timeGte' ? $comp <= $val : $comp >= $val;
    }

    /**
     * Date Between Comparison for jsonq: Please note that $comp is an array with two values
     *
     * @param $val
     * @param $comp
     * @return bool
     */
    public function dateBetweenComparison($val, $comp)
    {
        $date_split = explode('/', $val);
        $date_format = $date_split[2]. '-' . $date_split[1] . '-' . $date_split[0];
        $date = strtotime($date_format);
        $from = $comp[0];
        $from_split = explode('/', $from);
        $from_format = $from_split[2]. '-' . $from_split[1] . '-' . $from_split[0];
        $from = strtotime($from_format);
        $to = $comp[1];
        $to_split = explode('/', $to);
        $to_format = $to_split[2]. '-' . $to_split[1] . '-' . $to_split[0];
        $to = strtotime($to_format);
        return ($from <= $date) && ($date <= $to);
    }

    /**
     * Throws Errors that GetYourGuide wants us to
     *
     * @param $type
     * @param null $ticketCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function throwErrors($type, $ticketCategory=null)
    {
        $errorArray = [
            'errorCode'=> $type,
            'errorMessage' => $this->errorMessages[$type]
        ];
        if ($type == 'INVALID_TICKET_CATEGORY') {
            $errorArray['ticketCategory'] = $ticketCategory;
        }
        if ($type == 'INVALID_PARTICIPANTS_CONFIGURATION') {
            $errorArray['participantsConfiguration'] = $ticketCategory;
        }
        return response()->json($errorArray);
    }

    /**
     * Prepares JsonQ Object and adds some macros that we have to use
     *
     * @return Jsonq
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function prepareJsonQ()
    {
        $jsonq = new Jsonq();
        $jsonq->macro('dateGte', function($val, $comp) {
            return $this->dateComparison($val, $comp, 'dateGte');
        });

        $jsonq->macro('dateLte', function($val, $comp) {
            return $this->dateComparison($val, $comp, 'dateLte');
        });

        $jsonq->macro('timeGte', function($val, $comp) {
            return $this->timeComparison($val, $comp, 'timeGte');
        });

        $jsonq->macro('timeLte', function($val, $comp) {
            return $this->timeComparison($val, $comp, 'timeLte');
        });

        $jsonq->macro('dateBetween', function($val, $comp) {
            return $this->dateBetweenComparison($val, $comp);
        });

        return $jsonq;
    }

    /**
     * Getting valid ticket Categories from pricings table.
     *
     * @param $pricing
     * @return array
     */
    public function getValidCategories($pricing)
    {
        $validCategories = array();
        $adultMin = $pricing->adultMin;
        $adultMax = $pricing->adultMax;
        $youthMin = $pricing->youthMin;
        $youthMax = $pricing->youthMax;
        $childMin = $pricing->childMin;
        $childMax = $pricing->childMax;
        $infantMin = $pricing->infantMin;
        $infantMax = $pricing->infantMax;
        if (!is_null($adultMin) && !is_null($adultMax)) {
            array_push($validCategories, 'ADULT');
        }
        if (!is_null($youthMin) && !is_null($youthMax)) {
            array_push($validCategories, 'YOUTH');
        }
        if (!is_null($childMin) && !is_null($childMax)) {
            array_push($validCategories, 'CHILD');
        }
        if (!is_null($infantMin) && !is_null($infantMax)) {
            array_push($validCategories, 'INFANT');
        }
        array_push($validCategories, 'SENIOR');
        array_push($validCategories, 'STUDENT');
        array_push($validCategories, 'EU_CITIZEN');
        array_push($validCategories, 'MILITARY');
        array_push($validCategories, 'EU_CITIZEN_STUDENT');
        return $validCategories;
    }

    /**
     * /1/notify-availability-update
     *
     * @param $productId
     * @param $dateTime
     * @param $vacancies
     * @return object|\Psr\Http\Message\StreamInterface
     */
    public function notifyAvailabilityUpdate($productId, $dateTime, $vacancies)
    {
        if (env('APP_ENV', 'prod') == 'local') {
            return (object) null;
        }
        $url = env('GETYOURGUIDE_PRODUCTION_ENDPOINT', 'https://supplier-api.getyourguide.com'); // Endpoint for production environment
        //$url = env('GETYOURGUIDE_TESTING_ENDPOINT');      // Endpoint for testing environment
        $credentials = base64_encode(env('PBT_CREDENTIALS', "gygpariscitytoursapi:=B'C{@P9fHL?j_m@"));
        $config = [
            'headers' => [
                'authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json'
            ]
        ];

        $client = new Client($config);

        $body['data'] = array();
        $body['data']['productId'] = $productId;
        $body['data']['availabilities'] = array();
        array_push($body['data']['availabilities'], ['dateTime' => $dateTime, 'vacancies' => $vacancies]);

        $response = $client->request(
            'POST',
            $url . '/1/notify-availability-update',
            [
                'json' => $body
            ]
        );

        $apiLog = new Apilog();
        $apiLog->request = $response->getBody();
        $apiLog->query = json_encode($body);
        $apiLog->fullPath = $url . '/1/notify-availability-update';
        $apiLog->save();

        return $response->getBody();
    }

    /**
     * @param $productId
     * @param $dateTime
     * @param $vacancies
     * @return object
     */
    public function saveNotificationToTable($productId, $dateTime, $vacancies)
    {
        $notification = new GygNotification();
        $notification->optionRefCode = $productId;
        $notification->dateTime = $dateTime;
        $notification->vacancies = $vacancies;
        if ($notification->save()) {
            return (object) null;
        }
    }

    /**
     * @param $responseBI
     * @param $cartBI
     * @return bool
     */
    public function validateBookingItems($responseBI, $cartBI)
    {
        $ticketCatsForResponse = array();
        $ticketCatsForCart = array();
        $ticketCountsForResponse = array();
        $ticketCountsForCart = array();

        foreach ($responseBI as $resBI) {
            array_push($ticketCatsForResponse, $resBI['category']);
            array_push($ticketCountsForResponse, $resBI['count']);
        }

        foreach ($cartBI as $cBI) {
            array_push($ticketCatsForCart, $cBI['category']);
            array_push($ticketCountsForCart, $cBI['count']);
        }

        return count(array_diff($ticketCountsForResponse, $ticketCountsForCart)) == 0 && count(array_diff($ticketCatsForResponse, $ticketCatsForCart)) == 0;
    }

    /**
     * If user deletes the datetime from blockout, when cancel reservation made, this datetime must be enabled again.
     * This code block removes the datetime from blockouts->disabledDateTimes column
     *
     * @param $option
     * @param $jsonq
     * @param $date
     * @param $time
     */
    public function deleteFromDisabledDateTimes($option, $jsonq, $date, $time)
    {
        $blockout = $option->availabilities()->first()->blockouts()->first();
        $blockoutDisabledDateTimes = $blockout->disabledDateTimes;
        $res2 = $jsonq->json($blockoutDisabledDateTimes);
        $result2 = $res2->where('day', '=', $date)
            ->where('hour', '=', $time)
            ->get();
        if (count($result2) == 1) {
            $keyForDisabledDateTimes = key($result2);
            $decodedBlockoutDisabledDatetimes = json_decode($blockoutDisabledDateTimes);
            array_splice($decodedBlockoutDisabledDatetimes, $keyForDisabledDateTimes, 1);
            $blockout->disabledDateTimes = json_encode($decodedBlockoutDisabledDatetimes);
            if ($blockout->save()) {
                // We're sending Action Required mail to inform reservation team.
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'optionTitle' => $option->title,
                    'optionReferenceCode' => $option->referenceCode,
                    'date' => $date,
                    'time' => $time,
                    'subject' => 'Action Required!',
                    'sendToCC' => true
                ]);
                $mail->data = json_encode($data);
                $mail->to = 'contact@parisviptrips.com';
                $mail->blade = 'mail.api-action-required';
                $mail->save();
                //
            }
        }
    }

    /**
     * @param $bookingItems
     * @return string
     */
    public function getCategoryAndCountInfo($bookingItems)
    {
        $categoryAndCountInfo = '';
        $bookingItems = json_decode($bookingItems, true);
        foreach ($bookingItems as $index => $bI) {
            $categoryAndCountInfo .= $bI['count'] . ' ' . $bI['category'];
            if (count($bookingItems) > 1 && count($bookingItems) != ($index + 1)) {
                $categoryAndCountInfo .= ', ';
            }
        }
        return $categoryAndCountInfo;
    }

    public function getCategoryAndCountInfoWithLang($bookingItems)
    {
        $categoryAndCountInfo = '';
        $bookingItems = json_decode($bookingItems, true);
        foreach ($bookingItems as $index => $bI) {
            if(strtolower($bI['category']) == "adult") $bI['category'] = __('adult');
            elseif(strtolower($bI['category']) == "youth") $bI['category'] = __('youth');
            elseif(strtolower($bI['category']) == "child") $bI['category'] = __('child');
            elseif(strtolower($bI['category']) == "infant") $bI['category'] = __('infant');
            elseif(strtolower($bI['category']) == "eucitizen") $bI['category'] = __('euCitizen');

            $categoryAndCountInfo .= $bI['count'] . ' ' . $bI['category'];
            if (count($bookingItems) > 1 && count($bookingItems) != ($index + 1)) {
                $categoryAndCountInfo .= ', ';
            }
        }
        return $categoryAndCountInfo;
    }

    /**
     * @param $bkn
     * @return mixed
     */
    public function getOnlyBKNNumber($bkn)
    {
        $bknexploded = explode('-', $bkn)[2];
        return $bknexploded;
    }

    /**
     * @param $optionRefCode
     * @return array
     */
    public function getOptionsSharingSameAvailability($optionRefCode)
    {
        $option = Option::where('referenceCode', $optionRefCode)->first();
        // first can be used since we don't send mixed availabilities to the API
        $availability = $option->avs()->first();
        $optionsSharingSameAvailability = $availability->options()->get();
        $optionRefCodes = array();
        foreach ($optionsSharingSameAvailability as $ossa) {
            if ($ossa->connectedToApi == 1) {
                array_push($optionRefCodes, $ossa->referenceCode);
            }
        }
        return $optionRefCodes;
    }

    /**
     * @param $availability
     * @param $day
     * @param $hour
     * @param $ticketState
     * @param $type
     * @param string $change
     * @throws \Exception
     */
    public function makeNotificationOps($availability, $day, $hour, $ticketState, $type, $change = 'default')
    {
        $todayAsDmy = date('d/m/Y');
        $todayAsHi = date('H:i');
        // Checking if day is today and hour is passed
        if (($day == $todayAsDmy && $hour > $todayAsHi) || $day > $todayAsDmy) {
            $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($day, $hour, 'Europe/Paris');
            if ($change == 'weekDayMonthYear') {
                $disabledDays = json_decode($availability->disabledDays, true);
            } else {
                $disabledDays = $this->getDisabledDatesForGYG($availability);
            }
            if (!in_array($day, $disabledDays)) {
                // check if availability is not limitless, ticket count dropped under 5 and not older than 1 week
                $condition = $availability->isLimitless == 0 && $ticketState < 5 && $isDateTimeValid;
                if ($type == 2) {
                    $condition = $availability->isLimitless == 0 && $isDateTimeValid;
                } else if ($type == 3) {
                    $condition = $isDateTimeValid;
                    if ($availability->isLimitless == 1 && $ticketState != 0) {
                        $ticketState = 999999;
                    }
                } else if ($type == 4) {
                    $condition = $availability->isLimitless == 1 && $isDateTimeValid;
                    if ($ticketState != 0) {
                        $ticketState = 999999;
                    }
                }
                if ($condition) {
                    $optionRefCodesConnectedToApi = $availability->options()->where('connectedToApi', 1)->pluck('referenceCode');
                    $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($day, $hour, 'Europe/Paris');
                    foreach ($optionRefCodesConnectedToApi as $orc) {
                        $this->saveNotificationToTable($orc, $dateTime, $ticketState);
                    }
                }
            }
        }
    }

    /**
     * @param $oldDataState
     * @param $newDataState
     * @param $availability
     * @param $type
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function weekDayMonthYearNotifications($oldDataState, $newDataState, $availability, $type)
    {
        $willBeEnabled = null;
        $willBeDisabled = null;
        if (!is_null($oldDataState) && !is_null($newDataState)) {
            $willBeDisabled = array_diff($newDataState, $oldDataState);
            $willBeEnabled = array_diff($oldDataState, $newDataState);
        }
        if (!is_null($oldDataState) && is_null($newDataState)) {
            $willBeEnabled = $oldDataState;
        }
        if (is_null($oldDataState) && !is_null($newDataState)) {
            $willBeDisabled = $newDataState;
        }
        if (!is_null($willBeDisabled)) {
            if ($type == 'weekDay') {
                $this->enabledDisabledWeekDayOps($availability, $willBeDisabled, 'disabled');
            } else {
                $this->enabledDisabledMonthYearOps($availability, $willBeDisabled, 'disabled', $type);
            }
        }
        if (!is_null($willBeEnabled)) {
            if ($type == 'weekDay') {
                $this->enabledDisabledWeekDayOps($availability, $willBeEnabled, 'enabled');
            } else {
                $this->enabledDisabledMonthYearOps($availability, $willBeEnabled, 'enabled', $type);
            }
        }
    }

    /**
     * @param $availability
     * @param $array
     * @param $type
     * @param $monthOrYear
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function enabledDisabledMonthYearOps($availability, $array, $type, $monthOrYear)
    {
        $availabilityType = $availability->availabilityType;
        $current = date('m');
        if ($monthOrYear == 'year') {
            $current = date('Y');
        }
        if (in_array($current, $array)) {
            $columnStr = 'hourly';
            if ($availability->avTicketType == 2) {
                $columnStr = 'daily';
            } else if ($availability->avTicketType == 3) {
                $columnStr = 'dateRange';
            }
            $today = date('d/m/Y');
            $oneWeekAfter = date('d/m/Y', strtotime('+1 week'));
            $result = null;
            if (in_array($availability->avTicketType, [1, 2])) {
                $jsonq = $this->prepareJsonQ();
                $res = $jsonq->json($availability->$columnStr);
                $result = $res->where('day', 'dateGte', $today)
                    ->where('day', 'dateLte', $oneWeekAfter)
                    ->where('isActive', '=', 1)
                    ->get();
                if (count($result) > 0) {
                    $keys = array_keys($result);
                    foreach ($keys as $key) {
                        $day = $result[$key]['day'];
                        $hour = $availabilityType == 'Starting Time' ? $result[$key]['hour'] : '00:00';
                        $ticket = $type == 'disabled' ? 0 : $result[$key]['ticket'];
                        if ($availability->isLimitless == 1 && $type == 'enabled') {
                            $ticket = 999999;
                        }
                        $this->makeNotificationOps($availability, $day, $hour, $ticket, 3, 'weekDayMonthYear');
                    }
                }
            }
            if ($availability->avTicketType == 3) {
                $todayObj = DateTime::createFromFormat('d/m/Y', $today);
                $oneWeekAfterObj = DateTime::createFromFormat('d/m/Y', $oneWeekAfter);
                $interval = new DateInterval('P1D');
                $oneWeekAfterObj->add($interval);
                $period = new DatePeriod($todayObj, $interval, $oneWeekAfterObj);
                foreach ($period as $dt) {
                    $dtFormatted = $dt->format('d/m/Y');
                    $jsonq = $this->prepareJsonQ();
                    $res = $jsonq->json($availability->$columnStr);
                    $result = $res->where('dayFrom', 'dateLte', $dtFormatted)
                        ->where('dayTo', 'dateGte', $dtFormatted)
                        ->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $jsonq2 = $this->prepareJsonQ();
                        $res2 = $jsonq2->json($availability->daily);
                        $result2 = $res2->where('day', '=', $dtFormatted)
                            ->where('isActive', '=', 1)
                            ->get();
                        if (count($result2) == 1) {
                            $ticket = $type == 'disabled' ? 0 : $result[$key]['ticket'];
                            if ($availability->isLimitless == 1 && $type == 'enabled') {
                                $ticket = 999999;
                            }
                            $this->makeNotificationOps($availability, $dtFormatted, '00:00', $ticket, 3, 'weekDayMonthYear');
                        }
                        $res2->reset();
                    }
                    $res->reset();
                }
            }
        }
    }

    /**
     * @param $availability
     * @param $array
     * @param $type
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function enabledDisabledWeekDayOps($availability, $array, $type)
    {
        $availabilityType = $availability->availabilityType;
        $startDate = strtotime("now");
        $endDate = strtotime("+1 week");
        $columnStr = 'hourly';
        if ($availability->avTicketType == 2) {
            $columnStr = 'daily';
        } else if ($availability->avTicketType == 3) {
            $columnStr = 'dateRange';
        }
        foreach ($array as $dayName) {
            for ($x = strtotime($dayName, $startDate); $x <= $endDate; $x = strtotime('+1 week', $x)) {
                $dateDmy = date('d/m/Y', $x);
                $jsonq = $this->prepareJsonQ();
                $res = $jsonq->json($availability->$columnStr);
                $result = null;
                if (in_array($availability->avTicketType, [1, 2])) {
                    $result = $res->where('day', '=', $dateDmy)
                        ->where('isActive', '=', 1)->get();
                    if (count($result) >= 1) {
                        $keys = array_keys($result);
                        foreach ($keys as $key) {
                            $ticket = $type == 'disabled' ? 0 : $result[$key]['ticket'];
                            $hour = $availabilityType == 'Starting Time' ? $result[$key]['hour'] : '00:00';
                            $this->makeNotificationOps($availability, $dateDmy, $hour, $ticket, 3, 'weekDayMonthYear');
                        }
                    }
                }
                if ($availability->avTicketType == 3) {
                    $result = $res->where('dayFrom', 'dateLte', $dateDmy)
                        ->where('dayTo', 'dateGte', $dateDmy)->get();
                    if (count($result) >= 1) {
                        $keys = array_keys($result);
                        foreach ($keys as $key) {
                            $dayFrom = $result[$key]['dayFrom'];
                            $dayTo = $result[$key]['dayTo'];
                            $ticket = $type == 'disabled' ? 0 : $result[$key]['ticket'];
                            $begin = DateTime::createFromFormat('d/m/Y', $dayFrom);
                            $end = DateTime::createFromFormat('d/m/Y', $dayTo);
                            $interval = new DateInterval('P1D');
                            $end->add($interval);
                            $period = new DatePeriod($begin, $interval, $end);
                            foreach ($period as $dt) {
                                $dtFormatted = $dt->format('d/m/Y');
                                $jsonq2 = $this->prepareJsonQ();
                                $res2 = $jsonq2->json($availability->hourly);
                                $result2 = $res2->where('day', '=', $dtFormatted)
                                    ->where('isActive', '=', 1)->get();
                                if (count($result2) == 1) {
                                    $this->makeNotificationOps($availability, $dateDmy, '00:00', $ticket, 3, 'weekDayMonthYear');
                                }
                            }
                        }
                    }
                }
                $res->reset();
            }
        }
    }

    public function placeBarcodeForBooking($booking) {
        if(count(Barcode::where('bookingID', $booking->id)->get()) <= 0) {
            $dateTime = Carbon::parse($booking->dateTime)->format('d/m/Y H:i');
            $option = $booking->bookingOption;

            if($option && $option->ticket_types) {
                $cancelPolicyTime = $option->cancelPolicyTime;
                $cancelPolicyTimeType = $option->cancelPolicyTimeType;

                $status = false;

                switch ($cancelPolicyTimeType) {
                    case 'd':
                        $status = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->diffInDays(Carbon::now()) < $cancelPolicyTime;
                        break;
                    case 'h':
                        $status = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->diffInHours(Carbon::now()) < $cancelPolicyTime;
                        break;
                    case 'm':
                        $status = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->diffInMinutes(Carbon::now()) < $cancelPolicyTime;
                        break;

                    default:
                        // code...
                        break;
                }

                //dd([$dateTime, $cancelPolicyTime, $cancelPolicyTimeType]);

                if($status) {
                    $totalBarcodeCount = 0;
                    $bookingItems = json_decode($booking->bookingItems, true);
                    $pricing = $option->pricings()->get();

                    if(!$pricing[0]->ignoredCategories) {
                        foreach($bookingItems as $bookingItem) {
                            $totalBarcodeCount += $bookingItem["count"];
                        }
                    } else {
                        foreach($bookingItems as $bookingItem) {
                            $inIgnoredCategory = false;
                            foreach(json_decode($pricing[0]->ignoredCategories) as $ignoredCategory) {
                                if(strtolower($bookingItem["category"]) == strtolower($ignoredCategory)) {
                                    $inIgnoredCategory = true;
                                    break;
                                }
                            }
                            if(!$inIgnoredCategory)
                                $totalBarcodeCount += $bookingItem["count"];
                        }
                    }

                    if($totalBarcodeCount > 0) {
                        $ticketTypes = $option->ticket_types()->get();
                        foreach($ticketTypes as $ticketType) {
                            $barcodes = Barcode::where('isUsed', 0)->where('ticketType', $ticketType->id)->where('isReserved', 0)->where('isExpired', 0)->get();
                            for($i=0; $i<$totalBarcodeCount; $i++) {
                                if(count($barcodes) >= $totalBarcodeCount) {
                                    $barcode = $barcodes[$i];
                                    $barcode->isUsed = 1;
                                    $barcode->bookingID = $booking->id;
                                    $barcode->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function makeTicketResponseForBookingForTicketType($booking, $bookingItems, $bookingReference, $ticketTypes)
    {
        $tickets = array();
        $codeTypes = [
            "QR_CODE" => [24, 20, 19, 17, 3, 6],
            "BARCODE_CODE128" => [5, 7, 4]
        ];
        $ticketType = $ticketTypes[0];
        $ticketCodeType = "";

        $option = $booking->bookingOption;
        $pricing = $option->pricings()->first();
        $ignoredCategories = $pricing->ignoredCategories ? json_decode($pricing->ignoredCategories, true) : [];

        foreach($ignoredCategories as $key => $ignoredCategory) {
            $ignoredCategories[$key] = strtolower($ignoredCategory);
        }

        foreach($codeTypes as $key => $codeType) {
            if(in_array($ticketType->id, $codeType))
                $ticketCodeType = $key;
        }

        foreach ($bookingItems as $bookingItem) {
            if(!in_array(strtolower(str_replace('_', '', $bookingItem['category'])), $ignoredCategories)) {
                for ($i = 0; $i < $bookingItem['count']; $i++) {
                    $barcode = Barcode::where([['isUsed', '=', 0], ['isReserved', '=', 0], ['isExpired', '=', 0], ['ticketType', '=', $ticketType->id]])->first();
                    $physicalTicket = new Physicalticket();
                    $physicalTicket->bookingReference = $bookingReference;
                    $physicalTicket->ticketCategory = $bookingItem['category'];
                    $physicalTicket->ticketCode = $barcode->code;
                    $physicalTicket->ticketCodeType = $ticketCodeType; // Possible values are TEXT, BARCODE_CODE39, BARCODE_CODE128, QR_CODE, DATA_MATRIX, EAN_13, ITF
                    if ($physicalTicket->save()) {
                        $booking->physicalTickets()->attach($physicalTicket->id);
                    }
                    array_push($tickets,
                        [
                            'category' => $bookingItem['category'],
                            'ticketCode' => $barcode->code,
                            'ticketCodeType' => $ticketCodeType
                        ]
                    );

                    $barcode->isUsed = 1;
                    $barcode->bookingID = $booking->id;
                    $barcode->save();
                }
            }
        }

        return $tickets;
    }

    public function explodeBookingRefCode($bknRefCode)
    {
        $bknRefCode = array_filter(explode('-', $bknRefCode));
        $ref = [];

        foreach ($bknRefCode as $item){
            switch ($item){
                case strpos('PBT', $item):
                    $ref['product'] = $item;
                    break;

                case strpos('OPT', $item):
                    $ref['option'] = $item;
                    break;

                case strpos('CRT', $item):
                    $ref['cart'] = $item;
                    break;

                default:
                    $ref['bkn'] = $item;
                    break;

            }
        }

        return $ref;
    }

}
