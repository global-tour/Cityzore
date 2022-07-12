<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Option;
use App\Product;
use App\SpecialOffers;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nahid\JsonQ\Jsonq;
use Carbon\Carbon;


class SpecialOffersController extends Controller
{

    public $apiRelated;
    public $timeRelatedFunctions;

    public function __construct()
    {
        $this->apiRelated = new ApiRelated();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        } else if (auth()->guard('subUser')->check()) {
            $supplier = Supplier::findOrFail(auth()->guard('subUser')->user()->supervisor);
            $ownerID = $supplier->id;
        }
        $specialOffers = SpecialOffers::where('ownerID', $ownerID)->get();
        $offersAsRows = [];
        foreach ($specialOffers as $offer) {
            if (!is_null($offer->dateTimes)) {
                $dateTimes = json_decode($offer->dateTimes, true);
                foreach ($dateTimes as $dt) {
                    array_push($offersAsRows,
                        [
                            'id' => $offer->id,
                            'productID' => $offer->productID,
                            'optionID' => $offer->optionID,
                            'dateType' => $dt['dateType'],
                            'from' => '',
                            'to' => '',
                            'dayName' => '',
                            'day' => $dt['day'],
                            'hour' => $dt['hour'],
                            'productTitle' => $offer->product->title,
                            'optionTitle' => $offer->option->title,
                            'type' => 'Date & Time',
                            'date' => $dt['day'] . ' ' . $dt['hour'],
                            'discount' => $dt['discountType'] == 'money' ? '€ ' . $dt['discount'] : '% ' . $dt['discount'],
                            'minPersonCartTotal' => $dt['minType'] == 'minPerson' ? 'Min. Person: ' . $dt['minimum'] : 'Min. Cart Total: ' . $dt['minimum'],
                            'isActive' => $dt['isActive'],
                            'maximumUsability' => $dt["maximumUsability"] ?? '-',
                            'used' => $dt["used"] ?? '-'
                        ]
                    );
                }
            }
            if (!is_null($offer->randomDay)) {
                $randomDay = json_decode($offer->randomDay, true);
                foreach ($randomDay as $rd) {
                    array_push($offersAsRows,
                        [
                            'id' => $offer->id,
                            'productID' => $offer->productID,
                            'optionID' => $offer->optionID,
                            'dateType' => $rd['dateType'],
                            'from' => '',
                            'to' => '',
                            'dayName' => '',
                            'day' => $rd['day'],
                            'hour' => '',
                            'productTitle' => $offer->product->title,
                            'optionTitle' => $offer->option->title,
                            'type' => 'Random Day',
                            'date' => $rd['day'],
                            'discount' => $rd['discountType'] == 'money' ? '€ ' . $rd['discount'] : '% ' . $rd['discount'],
                            'minPersonCartTotal' => $rd['minType'] == 'minPerson' ? 'Min. Person: ' . $rd['minimum'] : 'Min. Cart Total: ' . $rd['minimum'],
                            'isActive' => $rd['isActive'],
                            'maximumUsability' => $rd["maximumUsability"] ?? '-',
                            'used' => $rd["used"] ?? '-'

                        ]
                    );
                }
            }
            if (!is_null($offer->weekDay)) {
                $weekDay = json_decode($offer->weekDay, true);
                foreach ($weekDay as $wd) {
                    array_push($offersAsRows,
                        [
                            'id' => $offer->id,
                            'productID' => $offer->productID,
                            'optionID' => $offer->optionID,
                            'dateType' => $wd['dateType'],
                            'from' => '',
                            'to' => '',
                            'dayName' => $wd['dayName'],
                            'day' => '',
                            'hour' => '',
                            'productTitle' => $offer->product->title,
                            'optionTitle' => $offer->option->title,
                            'type' => 'Week Day',
                            'date' => $wd['dayName'],
                            'discount' => $wd['discountType'] == 'money' ? '€ ' . $wd['discount'] : '% ' . $wd['discount'],
                            'minPersonCartTotal' => $wd['minType'] == 'minPerson' ? 'Min. Person: ' . $wd['minimum'] : 'Min. Cart Total: ' . $wd['minimum'],
                            'isActive' => $wd['isActive'],
                            'maximumUsability' => $wd["maximumUsability"] ?? '-',
                            'used' => $wd["used"] ?? '-'
                        ]
                    );
                }
            }
            if (!is_null($offer->dateRange)) {
                $dateRange = json_decode($offer->dateRange, true);
                foreach ($dateRange as $dr) {
                    array_push($offersAsRows,
                        [
                            'id' => $offer->id,
                            'productID' => $offer->productID,
                            'optionID' => $offer->optionID,
                            'dateType' => $dr['dateType'],
                            'from' => $dr['from'],
                            'to' => $dr['to'],
                            'dayName' => '',
                            'day' => '',
                            'hour' => '',
                            'productTitle' => $offer->product->title,
                            'optionTitle' => $offer->option->title,
                            'type' => 'Date Range',
                            'date' => $dr['from'] . ' - ' . $dr['to'],
                            'discount' => $dr['discountType'] == 'money' ? '€ ' . $dr['discount'] : '% ' . $dr['discount'],
                            'minPersonCartTotal' => $dr['minType'] == 'minPerson' ? 'Min. Person: ' . $dr['minimum'] : 'Min. Cart Total: ' . $dr['minimum'],
                            'isActive' => $dr['isActive'],
                            'maximumUsability' => $dr["maximumUsability"] ?? '-',
                            'used' => $dr["used"] ?? '-'
                        ]
                    );
                }
            }
        }
        return view('panel.special-offers.index',
            [
                'offersAsRows' => $offersAsRows
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            $products = Product::where('isDraft', 0)->where('isPublished', 1)->get();
            $owner = 'admin';
        }

        if (Auth::guard('supplier')->check()) {
            $products = Product::where('isDraft', 0)->where('isPublished', 1)->where('supplierID', '=', Auth::guard('supplier')->user()->id)->get();
            $owner = 'supplier';
        }

        return view('panel.special-offers.create',
            [
                'products' => $products,
                'owner' => $owner
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function getDateTimes(Request $request)
    {
        $option = Option::findOrFail($request->optionId);
        $option_availabilities = $option->avs()->get();
        $mixedAv = null;
        $mixedAv['availability_names'] = [];
        $mixedAv['availability_types'] = [];
        $mixedAv['valid_weekly_datetimes'] = [];
        $mixedAv['only_selected_times'] = [];
        $ticketCount = 1;
        $selectedDate = $request->formattedDate;
        // Getting tickets and loop through tickets for valid ticket count for selected date
        foreach ($option_availabilities as $i => $availability) {
            if ($availability->isLimitless == 1) {
                $ticketCount = 0;
            }
            $validTimes = [];
            array_push($mixedAv['availability_names'], $availability['name']);
            array_push($mixedAv['availability_types'], $availability['availabilityType']);
            if ($availability->isLimitless == 0) {
                // if ticket count is limited, filter only valid ticket counts
                if ($availability->avTicketType == '1') {
                    $hourly = json_decode($availability->hourly, true);
                    $jsonq = new Jsonq();
                    $res = $jsonq->json($availability->hourly);
                    $res->where('day', '=', $selectedDate)->where('ticket', '>=', $ticketCount)->get();
                    $keys = array_keys(json_decode($res->toJson(), true));
                    if (count($keys) > 0) {
                        foreach ($keys as $k) {
                            array_push($validTimes, ['hourFrom' => $hourly[$k]['hour']]);
                        }
                    } else {
                        return response()->json(['mixedAv' => null]);
                    }
                    array_push($mixedAv['only_selected_times'], $validTimes);
                    $validTimes = [];
                    $res->reset();
                } else if ($availability->avTicketType == '2') {
                    $daily = json_decode($availability->daily, true);
                    $jsonq = new Jsonq();
                    $res = $jsonq->json($availability->daily);
                    $res->where('day', '=', $selectedDate)->where('ticket', '>=', $ticketCount)->get();
                    $keys = array_keys(json_decode($res->toJson(), true));
                    if (count($keys) > 0) {
                        foreach ($keys as $k) {
                            array_push($validTimes, ['hourFrom' => $daily[$k]['hourFrom'], 'hourTo' => $daily[$k]['hourTo']]);
                        }
                    } else {
                        return response()->json(['mixedAv' => null]);
                    }
                    array_push($mixedAv['only_selected_times'], $validTimes);
                    $validTimes = [];
                    $res->reset();
                } else if ($availability->avTicketType == '3') {
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $res = $jsonq->json($availability->dateRange);
                    $result = $res->where('dayFrom', 'dateLte', $selectedDate)
                        ->where('dayTo', 'dateGte', $selectedDate)
                        ->where('ticket', '>=', $ticketCount)
                        ->get();
                    if (count($result) == 1) {
                        $daily = json_decode($availability->daily, true);
                        $jsonq2 = $this->apiRelated->prepareJsonQ();
                        $res2 = $jsonq2->json($availability->daily);
                        $result2 = $res2->where('day', '=', $selectedDate)
                            ->where('isActive', '=', 1)
                            ->get();
                        $keys = array_keys($result2);
                        if (count($keys) > 0) {
                            foreach ($keys as $k) {
                                array_push($validTimes, ['hourFrom' => $daily[$k]['hourFrom'], 'hourTo' => $daily[$k]['hourTo']]);
                            }
                        }
                    } else {
                        return response()->json(['mixedAv' => null]);
                    }
                    array_push($mixedAv['only_selected_times'], $validTimes);
                    $validTimes = [];
                    $res->reset();
                }
            }
        }
        return response()->json(['mixedAv' => $mixedAv]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function saveChanges(Request $request)
    {
        $isExisting = SpecialOffers::where('productID', $request->productID)->where('optionID', $request->optionID);
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }
        $specialOffers = null;
        $dateType = $request->dateType;
        $userType = $request->userType;
        $obj = [];
        $jsonq = $this->apiRelated->prepareJsonQ();
        if ($isExisting->count() == 1) {
            $specialOffers = $isExisting->first();
            if ($dateType == 'dateRange') {
                $obj = json_decode($specialOffers->dateRange, true);
            }
            if ($dateType == 'weekDay') {
                $obj = json_decode($specialOffers->weekDay, true);
            }
            if ($dateType == 'randomDay') {
                $obj = json_decode($specialOffers->randomDay, true);
            }
            if ($dateType == 'dateTimes') {
                $obj = json_decode($specialOffers->dateTimes, true);
            }
            if (is_null($obj)) {
                $obj = [];
            }
        } else {
            $specialOffers = new SpecialOffers();
        }
        $specialOffers->ownerID = $ownerID;
        $specialOffers->productID = $request->productID;
        $specialOffers->optionID = $request->optionID;
        if ($dateType == 'dateRange') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $from = $selectedDate[0];
            $to = $selectedDate[1];
            if (!is_null($specialOffers->dateRange)) {
                $res = $jsonq->json($specialOffers->dateRange);

                $result = $res->where('from', 'dateLte', $to)
                    ->where('to', 'dateGte', $from)
                    ->get();

                if (count($result) == 1) {
                    return response()->json(['error' => 'There is a special offer for this date range. Please delete the old one first!', 'type' => 'toast-alert']);
                }
            }

            array_push($obj,
                [
                    'dateType' => 'dateRange',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'from' => $from,
                    'to' => $to,
                    'discount' => $request->discount,
                    'isActive' => 1,
                    'maximumUsability' => intval($request->maximumUsability),
                    'used' => 0
                ]
            );
            $specialOffers->dateRange = json_encode($obj);
        }
        if ($dateType == 'weekDay') {
            $week = $request->week;
            $res = null;
            foreach ($week as $name => $w) {
                if ($w != '0') {
                    if (!is_null($specialOffers->weekDay)) {
                        $res = $jsonq->json($specialOffers->weekDay);
                        $result = $res->where('dayName', '=', $name)->get();

                        if (count($result) == 1) {
                            $key = key($result);
                            unset($obj[$key]);
                            $obj = array_values($obj);
                        }
                        $res->reset();
                    }
                    array_push($obj,
                        [
                            'dateType' => 'weekDay',
                            'userType' => $userType,
                            'minType' => $request->minType,
                            'minimum' => $request->minimum,
                            'discountType' => $request->discountType,
                            'dayName' => $name,
                            'discount' => $w,
                            'isActive' => 1,
                            'maximumUsability' => intval($request->maximumUsability),
                            'used' => 0

                        ]
                    );
                    $specialOffers->weekDay = json_encode($obj);
                }
            }
        }
        if ($dateType == 'randomDay') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $res = null;
            foreach ($selectedDate as $x => $sDate) {
                if (!is_null($specialOffers->randomDay)) {
                    $res = $jsonq->json($specialOffers->randomDay);
                    $result = $res->where('day', '=', $sDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        unset($obj[$key]);
                        $obj = array_values($obj);
                    }
                    $res->reset();
                }
                array_push($obj,
                    [
                        'dateType' => 'randomDay',
                        'userType' => $userType,
                        'minType' => $request->minType,
                        'minimum' => $request->minimum,
                        'discountType' => $request->discountType,
                        'day' => $sDate,
                        'discount' => $request->discount,
                        'isActive' => 1,
                        'maximumUsability' => intval($request->maximumUsability),
                        'used' => 0

                    ]
                );
                $specialOffers->randomDay = json_encode($obj);
            }
        }
        if ($dateType == 'dateTimes') {
            $selectedDate = $request->day;
            $res = null;
            if (!is_null($specialOffers->dateTimes)) {
                $res = $jsonq->json($specialOffers->dateTimes);
                $result = $res->where('day', '=', $selectedDate)->where('hour', '=', $request->hour)->get();
                if (count($result) == 1) {
                    $key = key($result);
                    unset($obj[$key]);
                    $obj = array_values($obj);
                }
                $res->reset();
            }
            array_push($obj,
                [
                    'dateType' => 'dateTimes',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'day' => $selectedDate,
                    'hour' => $request->hour,
                    'discount' => $request->discount,
                    'isActive' => 1,
                    'maximumUsability' => intval($request->maximumUsability),
                    'used' => 0
                ]
            );
            $specialOffers->dateTimes = json_encode($obj);
        }

        if ($specialOffers->save()) {
            $this->saveChangesPCT($request, $specialOffers->id);
            $this->saveChangesPCTcom($request, $specialOffers->id);
            $this->saveChangesCTP($request, $specialOffers->id);
            return response()->json(['success' => 'Special Offer is saved successfully!', 'type' => 'toast-success']);
        }

        return response()->json(['error' => 'An error occured while saving your special offer. Please consult with the development team.', 'type' => 'toast-alert']);
    }


        protected function saveChangesPCT($request, $id)
    {
        $isExisting = SpecialOffers::on('mysql2')->where('productID', $request->productID)->where('optionID', $request->optionID);
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }
        $specialOffers = null;
        $dateType = $request->dateType;
        $userType = $request->userType;
        $obj = [];
        $jsonq = $this->apiRelated->prepareJsonQ();
        if ($isExisting->count() == 1) {
            $specialOffers = $isExisting->first();
            if ($dateType == 'dateRange') {
                $obj = json_decode($specialOffers->dateRange, true);
            }
            if ($dateType == 'weekDay') {
                $obj = json_decode($specialOffers->weekDay, true);
            }
            if ($dateType == 'randomDay') {
                $obj = json_decode($specialOffers->randomDay, true);
            }
            if ($dateType == 'dateTimes') {
                $obj = json_decode($specialOffers->dateTimes, true);
            }
            if (is_null($obj)) {
                $obj = [];
            }
        } else {
            $specialOffers = new SpecialOffers();
            $specialOffers->setConnection('mysql2');
        }
        $specialOffers->ownerID = $ownerID;
        $specialOffers->productID = $request->productID;
        $specialOffers->optionID = $request->optionID;
         $specialOffers->id = $id;
        if ($dateType == 'dateRange') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $from = $selectedDate[0];
            $to = $selectedDate[1];
            if (!is_null($specialOffers->dateRange)) {
                $res = $jsonq->json($specialOffers->dateRange);

                $result = $res->where('from', 'dateLte', $to)
                    ->where('to', 'dateGte', $from)
                    ->get();

                if (count($result) == 1) {
                    return response()->json(['error' => 'There is a special offer for this date range. Please delete the old one first!', 'type' => 'toast-alert']);
                }
            }

            array_push($obj,
                [
                    'dateType' => 'dateRange',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'from' => $from,
                    'to' => $to,
                    'discount' => $request->discount,
                    'isActive' => 1
                ]
            );
            $specialOffers->dateRange = json_encode($obj);
        }
        if ($dateType == 'weekDay') {
            $week = $request->week;
            $res = null;
            foreach ($week as $name => $w) {
                if ($w != '0') {
                    if (!is_null($specialOffers->weekDay)) {
                        $res = $jsonq->json($specialOffers->weekDay);
                        $result = $res->where('dayName', '=', $name)->get();

                        if (count($result) == 1) {
                            $key = key($result);
                            unset($obj[$key]);
                            $obj = array_values($obj);
                        }
                        $res->reset();
                    }
                    array_push($obj,
                        [
                            'dateType' => 'weekDay',
                            'userType' => $userType,
                            'minType' => $request->minType,
                            'minimum' => $request->minimum,
                            'discountType' => $request->discountType,
                            'dayName' => $name,
                            'discount' => $w,
                            'isActive' => 1

                        ]
                    );
                    $specialOffers->weekDay = json_encode($obj);
                }
            }
        }
        if ($dateType == 'randomDay') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $res = null;
            foreach ($selectedDate as $x => $sDate) {
                if (!is_null($specialOffers->randomDay)) {
                    $res = $jsonq->json($specialOffers->randomDay);
                    $result = $res->where('day', '=', $sDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        unset($obj[$key]);
                        $obj = array_values($obj);
                    }
                    $res->reset();
                }
                array_push($obj,
                    [
                        'dateType' => 'randomDay',
                        'userType' => $userType,
                        'minType' => $request->minType,
                        'minimum' => $request->minimum,
                        'discountType' => $request->discountType,
                        'day' => $sDate,
                        'discount' => $request->discount,
                        'isActive' => 1

                    ]
                );
                $specialOffers->randomDay = json_encode($obj);
            }
        }
        if ($dateType == 'dateTimes') {
            $selectedDate = $request->day;
            $res = null;
            if (!is_null($specialOffers->dateTimes)) {
                $res = $jsonq->json($specialOffers->dateTimes);
                $result = $res->where('day', '=', $selectedDate)->where('hour', '=', $request->hour)->get();
                if (count($result) == 1) {
                    $key = key($result);
                    unset($obj[$key]);
                    $obj = array_values($obj);
                }
                $res->reset();
            }
            array_push($obj,
                [
                    'dateType' => 'dateTimes',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'day' => $selectedDate,
                    'hour' => $request->hour,
                    'discount' => $request->discount,
                    'isActive' => 1
                ]
            );
            $specialOffers->dateTimes = json_encode($obj);
        }

      $specialOffers->save();
    }


            protected function saveChangesPCTcom($request, $id)
    {
        $isExisting = SpecialOffers::on('mysql3')->where('productID', $request->productID)->where('optionID', $request->optionID);
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }
        $specialOffers = null;
        $dateType = $request->dateType;
        $userType = $request->userType;
        $obj = [];
        $jsonq = $this->apiRelated->prepareJsonQ();
        if ($isExisting->count() == 1) {
            $specialOffers = $isExisting->first();
            if ($dateType == 'dateRange') {
                $obj = json_decode($specialOffers->dateRange, true);
            }
            if ($dateType == 'weekDay') {
                $obj = json_decode($specialOffers->weekDay, true);
            }
            if ($dateType == 'randomDay') {
                $obj = json_decode($specialOffers->randomDay, true);
            }
            if ($dateType == 'dateTimes') {
                $obj = json_decode($specialOffers->dateTimes, true);
            }
            if (is_null($obj)) {
                $obj = [];
            }
        } else {
            $specialOffers = new SpecialOffers();
            $specialOffers->setConnection('mysql3');
        }
        $specialOffers->ownerID = $ownerID;
        $specialOffers->productID = $request->productID;
        $specialOffers->optionID = $request->optionID;
         $specialOffers->id = $id;
        if ($dateType == 'dateRange') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $from = $selectedDate[0];
            $to = $selectedDate[1];
            if (!is_null($specialOffers->dateRange)) {
                $res = $jsonq->json($specialOffers->dateRange);

                $result = $res->where('from', 'dateLte', $to)
                    ->where('to', 'dateGte', $from)
                    ->get();

                if (count($result) == 1) {
                    return response()->json(['error' => 'There is a special offer for this date range. Please delete the old one first!', 'type' => 'toast-alert']);
                }
            }

            array_push($obj,
                [
                    'dateType' => 'dateRange',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'from' => $from,
                    'to' => $to,
                    'discount' => $request->discount,
                    'isActive' => 1
                ]
            );
            $specialOffers->dateRange = json_encode($obj);
        }
        if ($dateType == 'weekDay') {
            $week = $request->week;
            $res = null;
            foreach ($week as $name => $w) {
                if ($w != '0') {
                    if (!is_null($specialOffers->weekDay)) {
                        $res = $jsonq->json($specialOffers->weekDay);
                        $result = $res->where('dayName', '=', $name)->get();

                        if (count($result) == 1) {
                            $key = key($result);
                            unset($obj[$key]);
                            $obj = array_values($obj);
                        }
                        $res->reset();
                    }
                    array_push($obj,
                        [
                            'dateType' => 'weekDay',
                            'userType' => $userType,
                            'minType' => $request->minType,
                            'minimum' => $request->minimum,
                            'discountType' => $request->discountType,
                            'dayName' => $name,
                            'discount' => $w,
                            'isActive' => 1

                        ]
                    );
                    $specialOffers->weekDay = json_encode($obj);
                }
            }
        }
        if ($dateType == 'randomDay') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $res = null;
            foreach ($selectedDate as $x => $sDate) {
                if (!is_null($specialOffers->randomDay)) {
                    $res = $jsonq->json($specialOffers->randomDay);
                    $result = $res->where('day', '=', $sDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        unset($obj[$key]);
                        $obj = array_values($obj);
                    }
                    $res->reset();
                }
                array_push($obj,
                    [
                        'dateType' => 'randomDay',
                        'userType' => $userType,
                        'minType' => $request->minType,
                        'minimum' => $request->minimum,
                        'discountType' => $request->discountType,
                        'day' => $sDate,
                        'discount' => $request->discount,
                        'isActive' => 1

                    ]
                );
                $specialOffers->randomDay = json_encode($obj);
            }
        }
        if ($dateType == 'dateTimes') {
            $selectedDate = $request->day;
            $res = null;
            if (!is_null($specialOffers->dateTimes)) {
                $res = $jsonq->json($specialOffers->dateTimes);
                $result = $res->where('day', '=', $selectedDate)->where('hour', '=', $request->hour)->get();
                if (count($result) == 1) {
                    $key = key($result);
                    unset($obj[$key]);
                    $obj = array_values($obj);
                }
                $res->reset();
            }
            array_push($obj,
                [
                    'dateType' => 'dateTimes',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'day' => $selectedDate,
                    'hour' => $request->hour,
                    'discount' => $request->discount,
                    'isActive' => 1
                ]
            );
            $specialOffers->dateTimes = json_encode($obj);
        }

        $specialOffers->save();
           
        

       
    }



            protected function saveChangesCTP($request, $id)
    {
        $isExisting = SpecialOffers::on('mysql4')->where('productID', $request->productID)->where('optionID', $request->optionID);
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }
        $specialOffers = null;
        $dateType = $request->dateType;
        $userType = $request->userType;
        $obj = [];
        $jsonq = $this->apiRelated->prepareJsonQ();
        if ($isExisting->count() == 1) {
            $specialOffers = $isExisting->first();
            if ($dateType == 'dateRange') {
                $obj = json_decode($specialOffers->dateRange, true);
            }
            if ($dateType == 'weekDay') {
                $obj = json_decode($specialOffers->weekDay, true);
            }
            if ($dateType == 'randomDay') {
                $obj = json_decode($specialOffers->randomDay, true);
            }
            if ($dateType == 'dateTimes') {
                $obj = json_decode($specialOffers->dateTimes, true);
            }
            if (is_null($obj)) {
                $obj = [];
            }
        } else {
            $specialOffers = new SpecialOffers();
            $specialOffers->setConnection('mysql4');
        }
        $specialOffers->ownerID = $ownerID;
        $specialOffers->productID = $request->productID;
        $specialOffers->optionID = $request->optionID;
        $specialOffers->id = $id;
        if ($dateType == 'dateRange') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $from = $selectedDate[0];
            $to = $selectedDate[1];
            if (!is_null($specialOffers->dateRange)) {
                $res = $jsonq->json($specialOffers->dateRange);

                $result = $res->where('from', 'dateLte', $to)
                    ->where('to', 'dateGte', $from)
                    ->get();

                if (count($result) == 1) {
                    return response()->json(['error' => 'There is a special offer for this date range. Please delete the old one first!', 'type' => 'toast-alert']);
                }
            }

            array_push($obj,
                [
                    'dateType' => 'dateRange',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'from' => $from,
                    'to' => $to,
                    'discount' => $request->discount,
                    'isActive' => 1
                ]
            );
            $specialOffers->dateRange = json_encode($obj);
        }
        if ($dateType == 'weekDay') {
            $week = $request->week;
            $res = null;
            foreach ($week as $name => $w) {
                if ($w != '0') {
                    if (!is_null($specialOffers->weekDay)) {
                        $res = $jsonq->json($specialOffers->weekDay);
                        $result = $res->where('dayName', '=', $name)->get();

                        if (count($result) == 1) {
                            $key = key($result);
                            unset($obj[$key]);
                            $obj = array_values($obj);
                        }
                        $res->reset();
                    }
                    array_push($obj,
                        [
                            'dateType' => 'weekDay',
                            'userType' => $userType,
                            'minType' => $request->minType,
                            'minimum' => $request->minimum,
                            'discountType' => $request->discountType,
                            'dayName' => $name,
                            'discount' => $w,
                            'isActive' => 1

                        ]
                    );
                    $specialOffers->weekDay = json_encode($obj);
                }
            }
        }
        if ($dateType == 'randomDay') {
            $selectedDate = $request->selectedDate;
            $selectedDate = explode(',', $selectedDate);
            $res = null;
            foreach ($selectedDate as $x => $sDate) {
                if (!is_null($specialOffers->randomDay)) {
                    $res = $jsonq->json($specialOffers->randomDay);
                    $result = $res->where('day', '=', $sDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        unset($obj[$key]);
                        $obj = array_values($obj);
                    }
                    $res->reset();
                }
                array_push($obj,
                    [
                        'dateType' => 'randomDay',
                        'userType' => $userType,
                        'minType' => $request->minType,
                        'minimum' => $request->minimum,
                        'discountType' => $request->discountType,
                        'day' => $sDate,
                        'discount' => $request->discount,
                        'isActive' => 1

                    ]
                );
                $specialOffers->randomDay = json_encode($obj);
            }
        }
        if ($dateType == 'dateTimes') {
            $selectedDate = $request->day;
            $res = null;
            if (!is_null($specialOffers->dateTimes)) {
                $res = $jsonq->json($specialOffers->dateTimes);
                $result = $res->where('day', '=', $selectedDate)->where('hour', '=', $request->hour)->get();
                if (count($result) == 1) {
                    $key = key($result);
                    unset($obj[$key]);
                    $obj = array_values($obj);
                }
                $res->reset();
            }
            array_push($obj,
                [
                    'dateType' => 'dateTimes',
                    'userType' => $userType,
                    'minType' => $request->minType,
                    'minimum' => $request->minimum,
                    'discountType' => $request->discountType,
                    'day' => $selectedDate,
                    'hour' => $request->hour,
                    'discount' => $request->discount,
                    'isActive' => 1
                ]
            );
            $specialOffers->dateTimes = json_encode($obj);
        }

        $specialOffers->save();
    }




    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailabilityType(Request $request)
    {
        $option = Option::findOrFail($request->optionID);
        $specialOffers = SpecialOffers::where('productID', $request->productID)->where('optionId', $request->optionID);
        if ($specialOffers->count() == 0) {
            $specialOffers = null;
        } else {
            $specialOffers = $specialOffers->first();
        }

        $datesAndTimesVisible = false;
        if ($option->isMixed == 0 && $option->avs()->first()->availabilityType == 'Starting Time') {
            $datesAndTimesVisible = true;
        }

        $pricing = $option->pricings()->first();
        $min_price = min(json_decode($pricing->adultPrice, true));


        return response()->json(['datesAndTimesVisible' => $datesAndTimesVisible, 'specialOffers' => $specialOffers, 'max_value' => $min_price]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function deleteOldSpecialOffer(Request $request)
    {
        $productID = $request->productID;
        $optionID = $request->optionID;
        $dateType = $request->dateType;
        $specialOffer = SpecialOffers::where('productID', $productID)->where('optionID', $optionID)->first();
        if ($dateType == 'dateRange') {
            $from = $request->from;
            $to = $request->to;
            $column = $specialOffer->dateRange;
        } else if ($dateType == 'weekDay') {
            $dayName = $request->dayName;
            $column = $specialOffer->weekDay;
        } else if ($dateType == 'randomDay') {
            $day = $request->day;
            $column = $specialOffer->randomDay;
        } else if ($dateType == 'dateTimes') {
            $day = $request->day;
            $hour = $request->hour;
            $column = $specialOffer->dateTimes;
        }

        $jsonq = $this->apiRelated->prepareJsonQ();
        $columnDecoded = json_decode($column, true);
        $res = $jsonq->json($column);
        if ($dateType == 'dateRange') {
            $result = $res->where('from', $from)->where('to', $to)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateRange = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'weekDay') {
            $result = $res->where('dayName', $dayName)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->weekDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'randomDay') {
            $result = $res->where('day', $day)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->randomDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'dateTimes') {
            $result = $res->where('day', $day)->where('hour', $hour)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateTimes = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        }
        $specialOffer->save();
        $this->deleteOldSpecialOfferPCT($request);
        $this->deleteOldSpecialOfferPCTcom($request);
        $this->deleteOldSpecialOfferCTP($request);

        if ($request->requestType == 'ajax') {
            return response()->json(['success' => 'Special Offer is deleted successfully!', 'type' => 'toast-success']);
        } else {
            return redirect('/special-offers');
        }
    }


        protected function deleteOldSpecialOfferPCT($request)
    {
        $productID = $request->productID;
        $optionID = $request->optionID;
        $dateType = $request->dateType;
        $specialOffer = SpecialOffers::on("mysql2")->where('productID', $productID)->where('optionID', $optionID)->first();
        if ($dateType == 'dateRange') {
            $from = $request->from;
            $to = $request->to;
            $column = $specialOffer->dateRange;
        } else if ($dateType == 'weekDay') {
            $dayName = $request->dayName;
            $column = $specialOffer->weekDay;
        } else if ($dateType == 'randomDay') {
            $day = $request->day;
            $column = $specialOffer->randomDay;
        } else if ($dateType == 'dateTimes') {
            $day = $request->day;
            $hour = $request->hour;
            $column = $specialOffer->dateTimes;
        }

        $jsonq = $this->apiRelated->prepareJsonQ();
        $columnDecoded = json_decode($column, true);
        $res = $jsonq->json($column);
        if ($dateType == 'dateRange') {
            $result = $res->where('from', $from)->where('to', $to)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateRange = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'weekDay') {
            $result = $res->where('dayName', $dayName)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->weekDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'randomDay') {
            $result = $res->where('day', $day)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->randomDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'dateTimes') {
            $result = $res->where('day', $day)->where('hour', $hour)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateTimes = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        }
        $specialOffer->save();

      
    }



            protected function deleteOldSpecialOfferPCTcom($request)
    {
        $productID = $request->productID;
        $optionID = $request->optionID;
        $dateType = $request->dateType;
        $specialOffer = SpecialOffers::on("mysql3")->where('productID', $productID)->where('optionID', $optionID)->first();
        if ($dateType == 'dateRange') {
            $from = $request->from;
            $to = $request->to;
            $column = $specialOffer->dateRange;
        } else if ($dateType == 'weekDay') {
            $dayName = $request->dayName;
            $column = $specialOffer->weekDay;
        } else if ($dateType == 'randomDay') {
            $day = $request->day;
            $column = $specialOffer->randomDay;
        } else if ($dateType == 'dateTimes') {
            $day = $request->day;
            $hour = $request->hour;
            $column = $specialOffer->dateTimes;
        }

        $jsonq = $this->apiRelated->prepareJsonQ();
        $columnDecoded = json_decode($column, true);
        $res = $jsonq->json($column);
        if ($dateType == 'dateRange') {
            $result = $res->where('from', $from)->where('to', $to)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateRange = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'weekDay') {
            $result = $res->where('dayName', $dayName)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->weekDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'randomDay') {
            $result = $res->where('day', $day)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->randomDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'dateTimes') {
            $result = $res->where('day', $day)->where('hour', $hour)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateTimes = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        }
        $specialOffer->save();

        
    }



            protected function deleteOldSpecialOfferCTP($request)
    {
        $productID = $request->productID;
        $optionID = $request->optionID;
        $dateType = $request->dateType;
        $specialOffer = SpecialOffers::on("mysql4")->where('productID', $productID)->where('optionID', $optionID)->first();
        if ($dateType == 'dateRange') {
            $from = $request->from;
            $to = $request->to;
            $column = $specialOffer->dateRange;
        } else if ($dateType == 'weekDay') {
            $dayName = $request->dayName;
            $column = $specialOffer->weekDay;
        } else if ($dateType == 'randomDay') {
            $day = $request->day;
            $column = $specialOffer->randomDay;
        } else if ($dateType == 'dateTimes') {
            $day = $request->day;
            $hour = $request->hour;
            $column = $specialOffer->dateTimes;
        }

        $jsonq = $this->apiRelated->prepareJsonQ();
        $columnDecoded = json_decode($column, true);
        $res = $jsonq->json($column);
        if ($dateType == 'dateRange') {
            $result = $res->where('from', $from)->where('to', $to)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateRange = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'weekDay') {
            $result = $res->where('dayName', $dayName)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->weekDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'randomDay') {
            $result = $res->where('day', $day)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->randomDay = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        } else if ($dateType == 'dateTimes') {
            $result = $res->where('day', $day)->where('hour', $hour)->get();
            if (count($result) > 0) {
                $key = key($result);
                unset($columnDecoded[$key]);
                $columnDecoded = array_values($columnDecoded);
                $columnDecoded = count($columnDecoded) > 0 ? $columnDecoded : null;
                $specialOffer->dateTimes = is_null($columnDecoded) ? null : json_encode($columnDecoded);
            }
        }
        $specialOffer->save();

      
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function changeSpecialOfferStatus(Request $request)
    {
        foreach ($request->obj as $obj) {
            $dateType = $obj['dateType'];
            $specialOffer = SpecialOffers::findOrFail($obj['id']);
            if ($dateType == 'dateRange') {
                $from = $obj['from'];
                $to = $obj['to'];
                $column = $specialOffer->dateRange;
            } else if ($dateType == 'weekDay') {
                $dayName = $obj['dayName'];
                $column = $specialOffer->weekDay;
            } else if ($dateType == 'randomDay') {
                $day = $obj['day'];
                $column = $specialOffer->randomDay;
            } else if ($dateType == 'dateTimes') {
                $day = $obj['day'];
                $hour = $obj['hour'];
                $column = $specialOffer->dateTimes;
            }

            $jsonq = $this->apiRelated->prepareJsonQ();
            $columnDecoded = json_decode($column, true);
            $res = $jsonq->json($column);
            if ($dateType == 'dateRange') {
                $result = $res->where('from', $from)->where('to', $to)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateRange = json_encode($columnDecoded);
                }
            } else if ($dateType == 'weekDay') {
                $result = $res->where('dayName', $dayName)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->weekDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'randomDay') {
                $result = $res->where('day', $day)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->randomDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'dateTimes') {
                $result = $res->where('day', $day)->where('hour', $hour)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateTimes = json_encode($columnDecoded);
                }
            }
            if($specialOffer->save()){
            $this->changeSpecialOfferStatusPCT($request);
            $this->changeSpecialOfferStatusPCTcom($request);
            $this->changeSpecialOfferStatusCTP($request);
            }
            
           
        }

        return response()->json(['success' => 'Special Offer is activate/deactivate successfully!', 'type' => 'toast-success', 'dateType' => $dateType, 'isActive' => $isActive]);
    }






        public function changeSpecialOfferStatusPCT($request)
    {
        foreach ($request->obj as $obj) {
            $dateType = $obj['dateType'];
            $specialOffer = SpecialOffers::on("mysql2")->findOrFail($obj['id']);
            if ($dateType == 'dateRange') {
                $from = $obj['from'];
                $to = $obj['to'];
                $column = $specialOffer->dateRange;
            } else if ($dateType == 'weekDay') {
                $dayName = $obj['dayName'];
                $column = $specialOffer->weekDay;
            } else if ($dateType == 'randomDay') {
                $day = $obj['day'];
                $column = $specialOffer->randomDay;
            } else if ($dateType == 'dateTimes') {
                $day = $obj['day'];
                $hour = $obj['hour'];
                $column = $specialOffer->dateTimes;
            }

            $jsonq = $this->apiRelated->prepareJsonQ();
            $columnDecoded = json_decode($column, true);
            $res = $jsonq->json($column);
            if ($dateType == 'dateRange') {
                $result = $res->where('from', $from)->where('to', $to)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateRange = json_encode($columnDecoded);
                }
            } else if ($dateType == 'weekDay') {
                $result = $res->where('dayName', $dayName)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->weekDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'randomDay') {
                $result = $res->where('day', $day)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->randomDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'dateTimes') {
                $result = $res->where('day', $day)->where('hour', $hour)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateTimes = json_encode($columnDecoded);
                }
            }
            $specialOffer->save();
        }
        return true;

        
    }




        public function changeSpecialOfferStatusPCTcom($request)
    {
        foreach ($request->obj as $obj) {
            $dateType = $obj['dateType'];
            $specialOffer = SpecialOffers::on("mysql3")->findOrFail($obj['id']);
            if ($dateType == 'dateRange') {
                $from = $obj['from'];
                $to = $obj['to'];
                $column = $specialOffer->dateRange;
            } else if ($dateType == 'weekDay') {
                $dayName = $obj['dayName'];
                $column = $specialOffer->weekDay;
            } else if ($dateType == 'randomDay') {
                $day = $obj['day'];
                $column = $specialOffer->randomDay;
            } else if ($dateType == 'dateTimes') {
                $day = $obj['day'];
                $hour = $obj['hour'];
                $column = $specialOffer->dateTimes;
            }

            $jsonq = $this->apiRelated->prepareJsonQ();
            $columnDecoded = json_decode($column, true);
            $res = $jsonq->json($column);
            if ($dateType == 'dateRange') {
                $result = $res->where('from', $from)->where('to', $to)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateRange = json_encode($columnDecoded);
                }
            } else if ($dateType == 'weekDay') {
                $result = $res->where('dayName', $dayName)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->weekDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'randomDay') {
                $result = $res->where('day', $day)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->randomDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'dateTimes') {
                $result = $res->where('day', $day)->where('hour', $hour)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateTimes = json_encode($columnDecoded);
                }
            }
            $specialOffer->save();
        }
        return true;

        
    }



        public function changeSpecialOfferStatusCTP($request)
    {
        foreach ($request->obj as $obj) {
            $dateType = $obj['dateType'];
            $specialOffer = SpecialOffers::on("mysql4")->findOrFail($obj['id']);
            if ($dateType == 'dateRange') {
                $from = $obj['from'];
                $to = $obj['to'];
                $column = $specialOffer->dateRange;
            } else if ($dateType == 'weekDay') {
                $dayName = $obj['dayName'];
                $column = $specialOffer->weekDay;
            } else if ($dateType == 'randomDay') {
                $day = $obj['day'];
                $column = $specialOffer->randomDay;
            } else if ($dateType == 'dateTimes') {
                $day = $obj['day'];
                $hour = $obj['hour'];
                $column = $specialOffer->dateTimes;
            }

            $jsonq = $this->apiRelated->prepareJsonQ();
            $columnDecoded = json_decode($column, true);
            $res = $jsonq->json($column);
            if ($dateType == 'dateRange') {
                $result = $res->where('from', $from)->where('to', $to)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateRange = json_encode($columnDecoded);
                }
            } else if ($dateType == 'weekDay') {
                $result = $res->where('dayName', $dayName)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->weekDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'randomDay') {
                $result = $res->where('day', $day)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->randomDay = json_encode($columnDecoded);
                }
            } else if ($dateType == 'dateTimes') {
                $result = $res->where('day', $day)->where('hour', $hour)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $columnDecoded[$key]['isActive'] = $columnDecoded[$key]['isActive'] == 1 ? 0 : 1;
                    $isActive = $columnDecoded[$key]['isActive'];
                    $specialOffer->dateTimes = json_encode($columnDecoded);
                }
            }
            $specialOffer->save();
        }
        return true;

        
    }

    public function edit(Request $request) {
        try {
            $specialOfferID = $request->get('specialOfferID');
            $specialOffer = SpecialOffers::where('id', $specialOfferID)->first();
            $dateRangeColumn = json_decode($specialOffer->dateRange, true);

            $fromDateInResponse = "";
            $toDateInResponse = "";
            $discountTypeInResponse = "";
            $minTypeInResponse = "";
            foreach($dateRangeColumn as $ind => $dt) {
                if($dt["dateType"] == $request->get('dateType') && $dt["from"] == $request->get('from') && $dt["to"] == $request->get('to')) {
                    $dateRangeColumn[$ind]["from"] = Carbon::createFromFormat('Y-m-d', $request->get('fromDateNewVal'))->format('d/m/Y');
                    $dateRangeColumn[$ind]["to"] = Carbon::createFromFormat('Y-m-d', $request->get('toDateNewVal'))->format('d/m/Y');
                    $dateRangeColumn[$ind]["discount"] = $request->get('discountNewVal');
                    $dateRangeColumn[$ind]["minimum"] = $request->get('minPersonCartTotalNewVal');

                    $fromDateInResponse = $dateRangeColumn[$ind]["from"];
                    $toDateInResponse = $dateRangeColumn[$ind]["to"];
                    if($dt["discountType"] == "percentage") $discountTypeInResponse = "%";
                    elseif($dt["discountType"] == "money") $discountTypeInResponse = "€";
                    if($dt["minType"] == "minPerson") $minTypeInResponse = "Min. Person:";

                    break;
                }
            }
            $specialOffer->dateRange = json_encode($dateRangeColumn);
            if($specialOffer->save()) {
                return ["success" => "success", "fromDateInResponse" => $fromDateInResponse, "toDateInResponse" => $toDateInResponse, "discountTypeInResponse" => $discountTypeInResponse, "minTypeInResponse" => $minTypeInResponse];   
            }
        } catch (Exception $e) {
            return ["error" => "An Error Occurred!"];
        }
    }

}
