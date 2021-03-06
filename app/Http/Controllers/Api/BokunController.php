<?php

namespace App\Http\Controllers\Api;

use App\Av;
use App\Avdate;
use App\BokunLog;
use App\Booking;
use App\Cart;
use App\Country;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\BokunRelated;
use App\Http\Controllers\Helpers\Logger;
use App\Http\Controllers\Helpers\MailOperations;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Invoice;
use App\Mails;
use App\Option;
use App\Product;
use App\Supplier;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Nahid\JsonQ\Jsonq;

class BokunController extends Controller
{

    // We cannot use this API for barcodes, mixed avs and also products which is with multi rates on Bokun side.
    // We can use it for options which is connected to least one product is including country and city informations.
    // The API is running with one to one relationships. So we have to create an option on the Bokun extranet for each option in our system.
    // We are responding to some fields like "id => Standard" and "label => 5" in some request statically and It doesn't matter what is these values.
    // getDefinition function is only ran once for our plugin's configuration process. It will run again if we have alteration on our configuration datas.
    // Bokun always send us $parameter variable with "CZ_API_HOST", "CZ_API_USERNAME", "CZ_API_PASSWORD" keys on every requests.
    // We check these parameters in BokunRelated class with the checkParameters function. If we run into a problem, the function will be interrupted.

    public $bokunRelated, $refCodeGenerator, $timeRelatedFunctions, $apiRelated, $mailOperations;


    public function __construct()
    {
        $this->bokunRelated = new BokunRelated();
        $this->refCodeGenerator = new RefCodeGenerator();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->apiRelated = new ApiRelated();
        $this->mailOperations = new MailOperations();
    }

    public function getDefinition(Request $request)
    {
        $data = ["name" => 'CityZore Plugin',
            "capabilities" => ["SUPPORTS_RESERVATIONS", "SUPPORTS_AVAILABILITY"],
            "parameters" =>[
                [
                    'name' => 'CZ_API_HOST',
                    'type' => 'STRING',
                    'required' => true,
                ],
                [
                    'name' => 'CZ_API_USERNAME',
                    'type' => 'STRING',
                    'required' => true,
                ],
                [
                    'name' => 'CZ_API_PASSWORD',
                    'type' => 'STRING',
                    'required' => true
                ]
            ]];

        $bokunLog = new BokunLog();
        $bokunLog->request = json_encode($request->all());
        $bokunLog->server = json_encode($request->server());
        $bokunLog->query = json_encode($data);
        $bokunLog->headers = json_encode($request->header());
        $bokunLog->path = $request->path();
        $bokunLog->fullPath = $request->fullUrl();
        $bokunLog->save();

        return response()->json($data);
    }

    // Running on Settings -> Advanced Settings -> Inventory Service -> CityZore Plugin -> List Products Page
    public function searchProducts(Request $request)
    {
        $bokunLog = new BokunLog();
        if (!$this->bokunRelated->apiAuthorization($request)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('AUTHORIZATION_FAILURE');
            $bokunLog->save();
            return response()->json([]);
        }
        $return = $this->bokunRelated->checkParameters($request->parameters);
        if ($return) {
            $bokunLog->error = $return;
            $bokunLog->save();
            return response()->json([]);
        }
        $data['SearchProductRequest'] = [];

        $options = Option::where('isMixed', 0)->where('isPublished', 1)->get();
        foreach ($options as $o) {
            if (!is_null($o->avs()->get())) {
                $pricingCategories = $this->bokunRelated->getPricingCategories($o->referenceCode);
                $basicProductInfo = [
                    'id' => $o->referenceCode,
                    'name' => $o->title,
                    'description' => $o->description,
                    'cities' => ['Paris'],
                    'countries' => ['FR'],
                    'pricingCategories' => $pricingCategories,
                ];
                array_push($data['SearchProductRequest'], $basicProductInfo);
            }
        }

        if (count($data['SearchProductRequest']) == 0) {
            $bokunLog->error = $this->bokunRelated->throwErrors('PRODUCTS_NOT_FOUND');
            $bokunLog->request = json_encode($request->all());
            $bokunLog->server = json_encode($request->server());
            $bokunLog->query = json_encode($data);
            $bokunLog->headers = json_encode($request->header());
            $bokunLog->path = $request->path();
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->save();
            return response()->json([]);
        }

        return response()->json($data['SearchProductRequest']);
    }

    // Running on Settings -> Advanced Settings -> Inventory Service -> CityZore Plugin -> List Products Page -> Create Mapping / Update Mapping
    public function getProductById(Request $request)
    {
        $bokunLog = new BokunLog();
        if (!$this->bokunRelated->apiAuthorization($request)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('AUTHORIZATION_FAILURE');
            $bokunLog->save();
        }
        $return = $this->bokunRelated->checkParameters($request->parameters);
        if ($return) {
            $bokunLog->error = $return;
            $bokunLog->path = $request->path();
            $bokunLog->save();
            return response()->json([]);
        }

        $referenceCode = $request->externalId;
        $option = Option::where('referenceCode', '=', $referenceCode)->first();
        if (!$option) {
            $bokunLog->error = $this->bokunRelated->throwErrors('INVALID_PRODUCT');
            $bokunLog->path = $request->path();
            $bokunLog->productId = $referenceCode;
            $bokunLog->save();
            return response()->json([]);
        }
        $pricingCategories = $this->bokunRelated->getPricingCategories($referenceCode);
        $availability = $option->avs()->first();
        if (!$availability) {
            $bokunLog->error = $this->bokunRelated->throwErrors('NO_AVAILABILITY');
            $bokunLog->path = $request->path();
            $bokunLog->productId = $referenceCode;
            $bokunLog->save();
            return response()->json([]);
        }
        if ($availability->availabilityType == 'Starting Time') {
            $hourly = json_decode($availability->hourly, true);
            $bookingType = 'DATE_AND_TIME';
            $startTimesTemporary = [];
            $startTimesLast = [];
            foreach ($hourly as $h) {
                $startTime = $h['hour'];
                array_push($startTimesTemporary, ["hour" => $startTime]);
                $startTimesTemporary = $this->bokunRelated->unique_multidimensional_array($startTimesTemporary, 'hour');
            }
            foreach ($startTimesTemporary as $startTime) {
                $startTimeHour = explode(':', $startTime["hour"])[0];
                $startTimeMinute = explode(':', $startTime["hour"])[1];
                array_push($startTimesLast, ['hour' => (int)$startTimeHour, 'minute' => (int)$startTimeMinute]);
            }

            if (count($startTimesLast) == 0) {
                $bokunLog->error = $this->bokunRelated->throwErrors('NO_AVAILABILITY');
                $bokunLog->productId = $referenceCode;
                $bokunLog->path = $request->path();
                $bokunLog->save();
                return response()->json([]);
            }

            $data = [
                'id' => $referenceCode,
                'name' => $option->title,
                'pricingCategories' => $pricingCategories,
                'description' => $option->description,
                'rates' => [
                    [
                        'id'=> 'Standard',
                        'label' => '5'
                    ],
                ],
                'bookingType' => $bookingType,
                'dropoffAvailable' => false,
                'productCategory' => 'ACTIVITIES',
                'ticketSupport' => ['TICKETS_NOT_REQUIRED'],
                'countries' => ["FR"],
                'cities' => ['Paris'],
                'startTimes' => $startTimesLast,
                'meetingType' => 'MEET_ON_LOCATION',
            ];

            return response()->json($data);
        } elseif ($availability->availabilityType == 'Operating Hours') {
            $bookingType = 'DATE';
            $seasonalOpeningHours['seasonalOpeningHours'] = [];
            $avdatesFirst = $availability->avdates->first();
            $avdatesLast = $availability->avdates->last();
            $startMonth = date('m',strtotime($avdatesFirst->valid_from));
            $startDay = date('d', strtotime($avdatesFirst->valid_from));
            $endMonth = date('m',strtotime($avdatesLast->valid_to));
            $endDay = date('d', strtotime($avdatesLast->valid_to));
            $daily = json_decode($availability->daily,true);
            $dailyInformation = [
                "open24Hours" => false,
                "timeIntervals" => [
                    [
                        "openFrom" => "Open From",
                        "openForHours" => $this->bokunRelated->stringToIntHourOrMinute($daily[0]['hourFrom'])['hour'],
                        "openForMinutes" => $this->bokunRelated->stringToIntHourOrMinute($daily[0]['hourFrom'])['minute'],
                        "duration" => [
                            "minutes" => 0,
                            "hours" => 0,
                            "days" => 0,
                            "weeks" => 0
                        ]
                    ]
                ]
            ];
            array_push($seasonalOpeningHours['seasonalOpeningHours'], [
                "startMonth" => (int)$startMonth,
                "endMonth" => (int)$endMonth,
                "startDay" => (int)$startDay,
                "endDay" => (int)$endDay,
                "openingHours" => [
                    "monday" => $dailyInformation,
                    "tuesday" => $dailyInformation,
                    "wednesday" => $dailyInformation,
                    "thursday" => $dailyInformation,
                    "friday" => $dailyInformation,
                    "saturday" => $dailyInformation,
                    "sunday" => $dailyInformation
                ]
            ]);

            $data = [
                'id' => $referenceCode,
                'name' => $option->title,
                'pricingCategories' => $pricingCategories,
                'description' => $option->description,
                'rates' => [
                    [
                        'id'=> 'Standard', 'label' => '5'
                    ]
                ],
                'bookingType' => $bookingType,
                'dropoffAvailable' => false,
                'productCategory' => 'ACTIVITIES',
                'ticketSupport' => ['TICKETS_NOT_REQUIRED'],
                'countries' => ['FR'],
                'cities' => ['Paris'],
                'meetingType' => 'MEET_ON_LOCATION',
                "seasonalOpeningHours" => $seasonalOpeningHours,
            ];

            $bokunLog->request = json_encode($request->all());
            $bokunLog->server = json_encode($request->server());
            $bokunLog->query = json_encode($data);
            $bokunLog->headers = json_encode($request->header());
            $bokunLog->productId = $referenceCode;
            $bokunLog->path = $request->path();
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->save();

            return response()->json($data);
        } else {
            $bokunLog->error = $this->bokunRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
            $bokunLog->save();
            return response()->json([]);
        }
    }

    //It's running on "search available products" with date range
    public function getAvailable(Request $request)
    {
        $bokunLog = new BokunLog();
        if (!$this->bokunRelated->apiAuthorization($request)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('AUTHORIZATION_FAILURE');
            $bokunLog->save();
            return response()->json([]);
        }
        $return = $this->bokunRelated->checkParameters($request->parameters);
        if ($return) {
            $bokunLog = new BokunLog();
            $bokunLog->error = $return;
            $bokunLog->path = $request->path();
            $bokunLog->save();
            return response()->json([]);
        }
        $range = $request->range;
        $requiredCapacity = $request->requiredCapacity;
        $externalProductIds =  $request->externalProductIds;
        $date = $this->bokunRelated->bokunDateToCityZoreDMY($range);
        $data = [];
        $externalProductIdsArray = [];
        foreach ($externalProductIds as $externalProductId) {
            array_push($externalProductIdsArray, $externalProductId);
            $option = Option::where('referenceCode', '=', $externalProductId)->first();
            if (!$option) {
                $bokunLog = new BokunLog();
                $bokunLog->error = $this->bokunRelated->throwErrors('PRODUCTS_NOT_FOUND');
                $bokunLog->path = $request->path();
                $bokunLog->save();
                return response()->json([]);
            }
            $availability = $option->avs()->first();
            $hourly = json_decode($availability->hourly, true);
            $daily = json_decode($availability->daily, true);
            $dateRange = json_decode($availability->dateRange, true);
            if ($availability->isLimitless == 0) {
                if ($availability->availabilityType == 'Starting Time') {
                    if (!is_null($hourly)) {
                        foreach ($hourly as $t) {
                            if ($t['ticket'] >= $requiredCapacity) {
                                foreach ($availability->avdates()->get() as $av) {
                                    if (strtotime($av->valid_from) >= strtotime($date['from'])
                                        && strtotime($av->valid_to) <= strtotime($date['to'])) {
                                        array_push($data, ['actualCheckDone' => false, 'productId' => $externalProductId]);
                                    }
                                }
                            }
                        }
                    } else {
                        $bokunLog->error = $this->bokunRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                        $bokunLog->request = json_encode($request->all());
                        $bokunLog->productId = $option->referenceCode;
                        $bokunLog->path = $request->path();
                        $bokunLog->save();
                        return response()->json([]);
                    }
                } else {
                    if (!is_null($daily)) {
                        foreach ($daily as $t) {
                            if ($t['ticket'] >= $requiredCapacity) {
                                foreach ($availability->avdates()->get() as $av) {
                                    if (strtotime($av->valid_from) >= strtotime($date['from']) && strtotime($av->valid_to) <= strtotime($date['to'])) {
                                        array_push($data, ['actualCheckDone' => false, 'productId' => $externalProductId]);
                                    }
                                }
                            }
                        }
                    } elseif (!is_null($dateRange)) {
                        foreach ($dateRange as $t) {
                            if ($t['ticket'] >= $requiredCapacity) {
                                foreach ($availability->avdates()->get() as $av) {
                                    if (strtotime($av->valid_from) >= strtotime($date['from']) && strtotime($av->valid_to) <= strtotime($date['to'])) {
                                        array_push($data, ['actualCheckDone' => false, 'productId' => $externalProductId]);
                                    }
                                }
                            }
                        }
                    } else {
                        $bokunLog = new BokunLog();
                        $bokunLog->error = $this->bokunRelated->throwErrors('INTERNAL_SYSTEM_FAILURE');
                        $bokunLog->productId = $option->referenceCode;
                        $bokunLog->path = $request->path();
                        $bokunLog->save();
                        return response()->json([]);
                    }
                }
            } else {
                array_push($data, ['actualCheckDone' => false, 'productId' => $externalProductId]);
            }
        }

        $data = array_values($this->bokunRelated->unique_multidimensional_array($data, 'productId'));

        if (count($data) <= 0) {
            $bokunLog->request = json_encode($request->all());
            $bokunLog->server = json_encode($request->server());
            $bokunLog->query = json_encode($data);
            $bokunLog->headers = json_encode($request->header());
            $bokunLog->path = $request->path();
            $bokunLog->error = $this->bokunRelated->throwErrors('NO_AVAILABILITY');
            $bokunLog->productId = json_encode($externalProductIdsArray);
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->save();
            return response()->json([]);
        }

        $bokunLog->request = json_encode($request->all());
        $bokunLog->server = json_encode($request->server());
        $bokunLog->query = json_encode($data);
        $bokunLog->headers = json_encode($request->header());
        $bokunLog->path = $request->path();
        $bokunLog->productId = json_encode($externalProductIdsArray);
        $bokunLog->fullPath = $request->fullUrl();
        $bokunLog->save();

        return response()->json($data);
    }

    //It's running to get available dates, times, and max capacity that our product has.
    public function getAvailability(Request $request)
    {
        $bokunLog = new BokunLog();
        if (!$this->bokunRelated->apiAuthorization($request)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('AUTHORIZATION_FAILURE');
            $bokunLog->save();
            return response()->json([]);
        }

        $return = $this->bokunRelated->checkParameters($request->parameters);
        if ($return) {
            $bokunLog->error = $return;
            $bokunLog->path = $request->path();
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->save();
            return response()->json([]);
        }
        $productId = $request->productId;
        $range = $request->range;
        $date = $this->bokunRelated->bokunDateToCityZoreDMY($range);
        $option = Option::where('referenceCode', '=', $productId)->first();
        if (!$option) {
            $bokunLog->error = $this->bokunRelated->throwErrors('INVALID_PRODUCT');
            $bokunLog->path = $request->path();
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->productId = $productId;
            $bokunLog->save();
            return response()->json([]);
        }
        $availability = $option->avs()->first();
        $productAvailabilityWithRateResponse = [];

        $blockoutHours = [];
        $avBlockoutHours = json_decode($availability->blockoutHours, true);
        foreach($avBlockoutHours as $avBlockoutHour) {
            if(array_key_exists($option->referenceCode, $avBlockoutHour)) {
                $blockoutHours = $avBlockoutHour[$option->referenceCode];
            }
        }

        if ($availability->availabilityType == 'Starting Time') {
            $hourly = json_decode($availability->hourly,true);
            if (count($hourly) > 0) {
                foreach ($hourly as $h) {
                    if ($h['isActive'] == 1) {
                        $dayOfHourly = strtotime(date('Y-m-d', strtotime(explode('/', $h['day'])[2].'-'.explode('/', $h['day'])[1].'-'.explode('/', $h['day'])[0])));
                        $capacity = $availability->isLimitless == 1 ? 9999 : (int)$h['ticket'];
                        if ($dayOfHourly >= strtotime($date['from']) && $dayOfHourly <= strtotime($date['to']) && $capacity > 0 && !in_array(date('d\/m\/Y', $dayOfHourly), json_decode($availability->disabledDays, true))) {
                            if(!in_array(explode('/', $h['day'])[2], $availability->disabledYears ? json_decode($availability->disabledYears, true) : []) && !in_array(explode('/', $h['day'])[1], $availability->disabledMonths ? json_decode($availability->disabledMonths, true) : []) && !in_array(strtolower(Carbon::createFromTimestamp($dayOfHourly)->format('l')), $availability->disabledWeekDays ? json_decode($availability->disabledWeekDays, true) : [])) {
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

                                    //$a = (!isset($blockoutHour['months']) || (isset($blockoutHour['months']) && !in_array(explode('/', $h['day'])[1], $blockoutHour['months'])));
                                    //$b = (!isset($blockoutHour['days']) || (isset($blockoutHour['days']) && !in_array(Carbon::createFromFormat('d/m/Y', $h['day'])->format('l'), $blockoutHour['days'])));
                                    //$c = (!isset($blockoutHour['hours']) || (isset($blockoutHour['hours']) && !in_array($h['hour'], $blockoutHour['hours'])));

                                    if(!$a || !$b || !$c) {
                                        //
                                    } else {
                                        $isInBlockArr = true;
                                        break;
                                    }
                                }

                                if (!$isInBlockArr || count($blockoutHours) <= 0) {
                                    array_push($productAvailabilityWithRateResponse, [
                                        'date' => [
                                            "year" => (int)(explode('/', $h['day'])[2]),
                                            "month" => (int)explode('/', $h['day'])[1],
                                            "day" => (int)explode('/', $h['day'])[0],
                                        ],
                                        'capacity' => $capacity,
                                        'time' => ['hour' => (int)explode(':', $h['hour'])[0], 'minute' => (int)explode(':', $h['hour'])[1]],
                                        'rates' => [
                                            [
                                                'rateId' => 'Standard',
                                                "pricePerPerson" => [
                                                    "pricingCategoryWithPrice" => $this->bokunRelated->getPricingCategoriesWithPrice($option->referenceCode)
                                                ]
                                            ]
                                        ],
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
                            if ($dayOfHourly >= strtotime($date['from']) && $dayOfHourly <= strtotime($date['to']) && $capacity > 0 && !in_array(date('d\/m\/Y', $dayOfHourly), json_decode($availability->disabledDays, true))) {
                                if(!in_array(explode('/', $d['day'])[2], $availability->disabledYears ? json_decode($availability->disabledYears, true) : []) && !in_array(explode('/', $d['day'])[1], $availability->disabledMonths ? json_decode($availability->disabledMonths, true) : []) && !in_array(strtolower(Carbon::createFromTimestamp($dayOfHourly)->format('l')), $availability->disabledWeekDays ? json_decode($availability->disabledWeekDays, true) : [])) {
                                    array_push($productAvailabilityWithRateResponse, [
                                        'date' => [
                                            "year" => (int)(explode('/', $d['day'])[2]),
                                            "month" => (int)explode('/', $d['day'])[1],
                                            "day" => (int)explode('/', $d['day'])[0],
                                        ],
                                        'capacity' => (int)$d['ticket'],
                                        'time' => ['hour' => (int)explode(':', $d['hourFrom'])[0], 'minute' => (int)explode(':', $d['hourFrom'])[1]],
                                        'rates' => [
                                            [
                                                "rateId" => 'Standard',
                                                "pricePerPerson" => [
                                                    "pricingCategoryWithPrice" => $this->bokunRelated->getPricingCategoriesWithPrice($option->referenceCode)
                                                ]
                                            ]
                                        ]
                                    ]);
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
                        $dailyDay = strtotime(str_replace('/', '-',$res['day']));
                        $hourFrom = $res['hourFrom'];
                        $range = $request->range;
                        $date = $this->bokunRelated->bokunDateToCityZoreDMY($range);
                        $capacity = $availability->isLimitless == 1 ? 9999 : (int)$dateRange[0]['ticket'];
                        if ($capacity > 0 && $res['isActive'] == 1 && $dailyDay >= $firstDayOfDateRange && $dailyDay <= $lastDayOfDateRange && $dailyDay >= strtotime($date['from']) && $dailyDay <= strtotime($date['to']) && !in_array(date('d\/m\/Y', $dayOfHourly), json_decode($availability->disabledDays, true))) {
                            if(!in_array(explode('/', $res['day'])[2], $availability->disabledYears ? json_decode($availability->disabledYears, true) : []) && !in_array(explode('/', $res['day'])[1], $availability->disabledMonths ? json_decode($availability->disabledMonths, true) : []) && !in_array(strtolower(Carbon::createFromTimestamp($dayOfHourly)->format('l')), $availability->disabledWeekDays ? json_decode($availability->disabledWeekDays, true) : [])) {
                                array_push($productAvailabilityWithRateResponse, [
                                    'date' => [
                                        "year" => (int)(explode('/', $res['day'])[2]),
                                        "month" => (int)explode('/', $res['day'])[1],
                                        "day" => (int)explode('/', $res['day'])[0],
                                    ],
                                    'capacity' => $capacity,
                                    'time' => ['hour' => (int)$this->bokunRelated->stringToIntHourOrMinute($hourFrom)['hour'], 'minute' => (int)$this->bokunRelated->stringToIntHourOrMinute($hourFrom)['minute']],
                                    'rates' => [
                                        [
                                            'rateId' => 'Standard',
                                            'pricePerPerson' => [
                                                'pricingCategoryWithPrice' => $this->bokunRelated->getPricingCategoriesWithPrice($option->referenceCode)
                                            ],
                                        ]
                                    ]
                                ]);
                            }
                        }
                    }
                    return response()->json($productAvailabilityWithRateResponse);
                } else {
                    $bokunLog->error = $this->bokunRelated->throwErrors('NO_AVAILABILITY');
                    $bokunLog->path = $request->path();
                    $bokunLog->request = json_encode($request->all());
                    $bokunLog->fullPath = $request->fullUrl();
                    $bokunLog->query = json_encode($productAvailabilityWithRateResponse);
                    $bokunLog->save();
                    return response()->json([]);
                }
            }
        }

        return response()->json($productAvailabilityWithRateResponse);
    }

    public function reservation(Request $request)
    {
        $bokunLog = new BokunLog();
        if (!$this->bokunRelated->apiAuthorization($request)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('AUTHORIZATION_FAILURE');
            $bokunLog->save();
            return response()->json([]);
        }
        $bokunLog->request = json_encode($request->all());
        $bokunLog->server = json_encode($request->server());
        $bokunLog->headers = json_encode($request->header());
        $bokunLog->path = $request->path();
        $bokunLog->fullPath = $request->fullUrl();
        $bokunLog->save();
        $reservationData = $request->reservationData;
        $return = $this->bokunRelated->checkParameters($request->parameters);
        if ($return) {
            $bokunLog->error = $return;
            $bokunLog->path = $request->path();
            $bokunLog->request = json_encode($request->all());
            $bokunLog->save();
            return response()->json([]);
        }

        $adultCount = 0;
        $youthCount = 0;
        $childCount = 0;
        $infantCount = 0;
        $euCitizenCount = 0;
        $adultPrice = 0;
        $youthPrice = 0;
        $childPrice = 0;
        $infantPrice = 0;
        $euCitizenPrice = 0;
        $option = Option::where('referenceCode', '=', $reservationData['productId'])->first();
        if (!$option) {
            $bokunLog->error = $this->bokunRelated->throwErrors('INVALID_PRODUCT');
            $bokunLog->request = json_encode($request->all());
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->path = $request->path();
            $bokunLog->productId = $reservationData['productId'];
            $bokunLog->save();
            return response()->json([]);
        }
        $availability = $option->avs()->first();

        $date = $this->bokunRelated->arrayIntToStringForDateOrHour($reservationData, $bokunLog, 'DATE');
        if(!empty($reservationData['time'])){
            $hour = $this->bokunRelated->arrayIntToStringForDateOrHour($reservationData, $bokunLog, 'HOUR');
        }else{
          $hour = '00:00';
        }


        $currencyID = $this->bokunRelated->getCurrencyID($reservationData['reservations'][0]['passengers'][0]['pricePerPassenger']['currency']);

        if (is_null($currencyID)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('INVALID_CURRENCY');
            $bokunLog->path = $request->path();
            $bokunLog->request = json_encode($request->all());
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->save();
            return response()->json([]);
        }

        foreach ($reservationData['reservations'] as $reservation) {
            foreach ($reservation['passengers'] as $passenger) {
                if ($passenger['pricingCategoryId'] == 'ADULT') {
                    $adultPrice = (float)$passenger['pricePerPassenger']['amount'];
                    $adultCount += 1;
                }
                if ($passenger['pricingCategoryId'] == 'YOUTH') {
                    $youthPrice = (float)$passenger['pricePerPassenger']['amount'];
                    $youthCount += 1;
                }
                if ($passenger['pricingCategoryId'] == 'CHILD') {
                    $childPrice = (float)$passenger['pricePerPassenger']['amount'];
                    $childCount += 1;
                }
                if ($passenger['pricingCategoryId'] == 'INFANT') {
                    $infantPrice = (float)$passenger['pricePerPassenger']['amount'];
                    $infantCount += 1;
                }
                if ($passenger['pricingCategoryId'] == 'EUCITIZEN') {
                    $euCitizenPrice = (float)$passenger['pricePerPassenger']['amount'];
                    $euCitizenCount += 1;
                }
            }
        }


        $totalPrice = ($adultPrice * $adultCount) + ($youthPrice * $youthCount) + ($childPrice * $childCount) + ($infantPrice * $infantCount) + ($euCitizenPrice * $euCitizenCount);
        $ticketCount = $adultCount + $youthCount + $childCount + $infantCount + $euCitizenCount;
        $hours = [];
        array_push($hours, ['hour' => $hour]);
        $bookingItems = [];
        if ($adultCount > 0) {
            array_push($bookingItems, ['category' => 'ADULT', 'count' => (string)$adultCount]);
        }
        if ($youthCount > 0) {
            array_push($bookingItems, ['category' => 'YOUTH', 'count' => (string)$youthCount]);
        }
        if ($childCount > 0) {
            array_push($bookingItems, ['category' => 'CHILD', 'count' => (string)$childCount]);
        }
        if ($infantCount > 0) {
            array_push($bookingItems, ['category' => 'INFANT', 'count' => (string)$infantCount]);
        }
        if ($euCitizenCount > 0) {
            array_push($bookingItems, ['category' => 'EU_CITIZEN', 'count' => (string)$euCitizenCount]);
        }
        $referenceCode = $this->refCodeGenerator->refCodeGeneratorForCart();

        $cart = new Cart();
        $cart->referenceCode = $referenceCode;
        $cart->status = 0;
        $cart->optionID = $option->id;
        $cart->dateTime = json_encode(["dateTime" => date('Y-m-d', strtotime($date)) . 'T' . $hour . ':00+01:00']);
        $cart->date = date('d/m/Y', strtotime($date));
        $cart->hour = json_encode($hours);
        $cart->ticketCount = $ticketCount;
        $cart->maxCommission = 0;
        $cart->isBokun = 1;
        $cart->bookingItems = json_encode($bookingItems);
        $cart->totalPrice = $totalPrice;
        $cart->totalPriceWOSO = $totalPrice;
        $cart->ticketID = $availability->id;
        $cart->currencyID = $currencyID;

        if ($availability->isLimitless == 0) {
            $ticketCountDecrementReturn = $this->bokunRelated->ticketCountDecrement($option->id, $ticketCount, $cart);
            $result = $ticketCountDecrementReturn['result'];
            $ticketState = array_key_exists('ticketState', $ticketCountDecrementReturn) ? $ticketCountDecrementReturn['ticketState'] : 0;
            if ($result == 'SUCCESSFUL_RESERVATION') {
                $time = $availability->availabilityType == 'Starting Time' ? json_decode($cart->hour, true)[0]['hour'] : "00:00";
                $date = str_replace('-', '/', $date);
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
            $cart->save();
        }

        if ($result == 'SUCCESSFUL_RESERVATION') {
            $bokunLog->error = $this->bokunRelated->throwErrors('NO_AVAILABILITY');
            $bokunLog->path = $request->path();
            $bokunLog->request = json_encode($request->all());
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->productId = $reservationData['productId'];
            $bokunLog->query = json_encode(['successfulReservation' => ["reservationConfirmationCode" => $cart->referenceCode]]);
            $bokunLog->save();
            return response()->json(
                [
                    'successfulReservation' => [
                        "reservationConfirmationCode" => $cart->referenceCode
                    ]
                ]
            );
        } elseif ($result == 'NO_AVAILABILITY') {
            $bokunLog->error = $this->bokunRelated->throwErrors('NO_AVAILABILITY');
            $bokunLog->path = $request->path();
            $bokunLog->request = json_encode($request->all());
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->productId = $reservationData['productId'];
            $bokunLog->query = json_encode(['failedReservation' => ['reservationError' => $this->bokunRelated->throwErrors('NO_AVAILABILITY')]]);
            $bokunLog->save();

            return response()->json(
                [
                    'failedReservation' => [
                        'reservationError' => $this->bokunRelated->throwErrors('NO_AVAILABILITY')
                    ]
                ]
            );
        }
    }

    public function confirmation(Request $request)
    {
        $bokunLog = new BokunLog();
        if (!$this->bokunRelated->apiAuthorization($request)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('AUTHORIZATION_FAILURE');
            $bokunLog->save();
            return response()->json([]);
        }

        $return = $this->bokunRelated->checkParameters($request->parameters);
        if ($return) {
            $bokunLog->error = $return;
            $bokunLog->path = $request->path();
            $bokunLog->save();
            return response()->json([]);
        }

        $reservationData = $request->reservationData;
        $reservationConfirmationCode = $request->reservationConfirmationCode;
        $cart = Cart::where('referenceCode', '=', $reservationConfirmationCode)->first();
        if (!$cart) {
            $bokunLog->error = $this->bokunRelated->throwErrors('VALIDATION_FAILURE');
            $bokunLog->path = $request->path();
            $bokunLog->productId = $reservationConfirmationCode;
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->request = json_encode($request->all());
            $bokunLog->save();
            return response()->json([]);
        }
        explode('T', $this->timeRelatedFunctions->convertDmyToYmd($cart->date)['fromDateTime'])[0];
        $contact = $reservationData['customerContact'];
        if (!$contact || !array_key_exists('firstName', $contact) || !array_key_exists('lastName', $contact)) {
            $bokunLog->error = $this->bokunRelated->throwErrors('VALIDATION_FAILURE');
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->request = json_encode($request->all());
            $bokunLog->productId = $reservationConfirmationCode;
            $bokunLog->path = $request->path();
            $bokunLog->save();
            return response()->json([]);
        } else {
            $travelers = [];
            array_push($travelers, [
                'firstName' => $contact['firstName'],
                'lastName' => $contact['lastName'],
                'email' => $contact['email'],
                'phoneNumber' => $contact['phone']
            ]);
        }

        $dateTimeArray = [];
        $bookingRefCode = $this->refCodeGenerator->refCodeGeneratorForBokunBooking();
        $currencyID = $cart->currencyID;
        $booking = new Booking();
        $booking->status = 0;
        $booking->optionRefCode = $reservationData['productId'];
        $booking->reservationRefCode = $reservationConfirmationCode;
        if($reservationData['agentCode'] == 'Viator.com') {
            $booking->bookingRefCode = 'BR-'.$reservationData['externalSaleId'];
            $booking->platformID =  5;
        }
        elseif($reservationData['agentCode'] == 'Musement') {
            $booking->bookingRefCode = $reservationData['externalSaleId'];
            $booking->platformID =  6;
        }
        else {
            $booking->bookingRefCode = $bookingRefCode;
            $booking->platformID =  3;
        }
        $booking->bookingItems = $cart->bookingItems;
        //$booking->dateTime = json_encode(array_push($dateTimeArray, $cart->dateTime));
        $booking->dateTime = json_decode($cart->dateTime,true)['dateTime'];
        $booking->date = $cart->date;
        $booking->dateForSort = explode('/', $cart->date)[2].'-'.explode('/', $cart->date)[1].'-'.explode('/', $cart->date)[0];
        $booking->hour = $cart->hour;
        $booking->language = 'EN';
        $booking->travelers = json_encode($travelers);
        $booking->fullName = $contact['firstName'].' '.$contact['lastName'];
        $booking->totalPrice = $cart->totalPrice;
        $booking->companyID = -1;
        $booking->currencyID =  $currencyID;
        $booking->fromWebsite =  'Bokun';
        $booking->isBokun = 1;
        $avID = [];
        $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
        foreach($bAvs as $bAv) {
            array_push($avID, $bAv->id);
        }
        $booking->avID = json_encode($avID);
        if ($cart->status == 0) {
            if ($booking->save()) {
                $option = Option::where('referenceCode', '=', $reservationData['productId'])->first();
                $this->bokunRelated->soldCountIncrement($option->id, $cart->ticketCount, $cart);
                $availability = $option->avs()->first();
                $hour = $availability->type == "Operating Hours" ? "Operating Hours" : json_decode($booking->hour, true)[0]['hour'];
                $mail = new Mails();
                $mail->to = 'contact@parisviptrips.com';
                $mail->data = json_encode([
                    [
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => $hour,
                        'BKNCode' => $booking->bookingRefCode,
                        'subject' => $booking->bookingRefCode . ' - Successful Booking !',
                        'name' => $contact['firstName'],
                        'surname' => $contact['lastName'],
                        'sendToCC' => true,
                    ]
                ]);
                $mail->blade = 'mail.booking-successful-for-creator';
                $mail->status = 0;
                $mail->save();

                $restaurant = null;
                if (!is_null(Option::where('referenceCode', $option->referenceCode)->first()->rCodeID)) {
                    $restaurant = Supplier::where('isRestaurant', 1)->where('id', Option::where('referenceCode', $option->referenceCode)->first()->rCodeID)->first();
                    // Mail for restaurant
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'subject' => 'New Booking ! ' .$option->title,
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'BKNCode' => $booking->bookingRefCode,
                        'name' => $contact['firstName'],
                        'surname' => $contact['lastName'],
                        'sendToCC' => false
                    ]);
                    $mail->to = $restaurant->email;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.booking-successful-for-restaurant';
                    $mail->save();
                }

                $cart->status = 2;
                $cart->save();
                $invoice = new Invoice();
                $invoice->paymentMethod = 'API';
                $invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
                $invoice->bookingID = $booking->id;
                $invoice->companyID = -1;
                if ($invoice->save()) {
                    $booking->invoiceID = $invoice->id;
                }
                if ($booking->save()) {
                    $bokunLog->request = json_encode($request->all());
                    $bokunLog->server = json_encode($request->server());
                    $bokunLog->headers = json_encode($request->header());
                    $bokunLog->path = $request->path();
                    $bokunLog->fromDateTime = $cart->date;
                    $bokunLog->toDateTime = $cart->date;
                    $bokunLog->productId = $bookingRefCode;
                    $bokunLog->fullPath = $request->fullUrl();
                    $bokunLog->query = json_encode(["successfulBooking" =>['bookingConfirmationCode' => $bookingRefCode]]);
                    $bokunLog->save();
                    $this->apiRelated->placeBarcodeForBooking($booking);

                    return response()->json(
                        [
                            "successfulBooking" =>[
                                'bookingConfirmationCode' => $bookingRefCode
                            ]

                        ]
                    );
                } else {
                    $bokunLog->error = $this->bokunRelated->throwErrors('INVOICE_NOT_CREATED');
                    $bokunLog->path = $request->path();
                    $bokunLog->productId = $bookingRefCode;
                    $bokunLog->fullPath = $request->fullUrl();
                    $bokunLog->request = json_encode($request->all());
                    $bokunLog->query = json_encode(['bookingError' => $this->bokunRelated->throwErrors('INVOICE_NOT_CREATED')]);
                    $bokunLog->save();
                    return response()->json(
                        [
                            'bookingError' => $this->bokunRelated->throwErrors('INVOICE_NOT_CREATED')
                        ]
                    );
                }
            } else {
                $bokunLog->error = $this->bokunRelated->throwErrors('VALIDATION_FAILURE');
                $bokunLog->path = $request->path();
                $bokunLog->request = json_encode($request->all());
                $bokunLog->productId = $bookingRefCode;
                $bokunLog->fullPath = $request->fullUrl();
                $bokunLog->save();
                return response()->json([]);
            }
        } else {
            $bokunLog->error = $this->bokunRelated->throwErrors('CART_FAILURE');
            $bokunLog->path = $request->path();
            $bokunLog->fullPath = $request->fullUrl();
            $bokunLog->productId = $reservationConfirmationCode;
            $bokunLog->request = json_encode($request->all());
            $bokunLog->save();
            return response()->json([]);
        }
    }

    public function cancellation(Request $request)
    {
        $bokunLog = new BokunLog();
        if (!$this->bokunRelated->apiAuthorization($request)) {
            $bokunLog->error =  $this->bokunRelated->throwErrors('AUTHORIZATION_FAILURE');
            $bokunLog->save();
            return response()->json([]);
        }

        $return = $this->bokunRelated->checkParameters($request->parameters);
        if ($return) {
            $bokunLog->error = $return;
            $bokunLog->path = $request->path();
            $bokunLog->save();
            return response()->json([]);
        }
        $bookingConfirmationCode = $request->bookingConfirmationCode;
        $booking = Booking::where('bookingRefCode', '=', $bookingConfirmationCode)->first();
        $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
        $availability = $option->avs()->first();
        $ticketCountIncrementReturn = $this->bokunRelated->ticketCountIncrement($booking);
        $result = $availability->isLimitless == 0 ? $ticketCountIncrementReturn['result'] : 'BOOKING_CANCELLED';
        $ticketState = $ticketCountIncrementReturn['ticketState'];
        if ($result == 'BOOKING_CANCELLED') {
            $booking->status = 2;
            if ($booking->save()) {
                $bokunLog->request = json_encode($request->all());
                $bokunLog->server = json_encode($request->server());
                $bokunLog->headers = json_encode($request->header());
                $bokunLog->path = $request->path();
                $bokunLog->fromDateTime = $booking->date;
                $bokunLog->toDateTime = $booking->date;
                $bokunLog->productId = $bookingConfirmationCode;
                $bokunLog->fullPath = $request->fullUrl();
                $bokunLog->save();
                $hour = $availability->type == 'Operating Hours' ? 'Operating Hours ' : json_decode($booking->hour, true)[0]['hour'];
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

                return response()->json([
                   'successfulCancellation' => (object) null
                ]);
            } else {
                $bokunLog->error = $this->bokunRelated->throwErrors('INVALID_CANCELLATION');
                $bokunLog->request = json_encode($request->all());
                $bokunLog->query = json_encode(['failedCancellation' => $this->bokunRelated->throwErrors('INVALID_CANCELLATION')]);
                $bokunLog->productId = $bookingConfirmationCode;
                $bokunLog->path = $request->path();
                $bokunLog->save();
                return response()->json([
                    'failedCancellation' => $this->bokunRelated->throwErrors('INVALID_CANCELLATION')
                ]);
            }
        }
    }

}
