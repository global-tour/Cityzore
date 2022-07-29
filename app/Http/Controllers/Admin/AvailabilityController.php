<?php

namespace App\Http\Controllers\Admin;

use App\Cart;
use App\Events\StatusLiked;
use App\Http\Controllers\Controller;
use App\Option;
use App\Avdate;
use App\Barcode;
use App\TicketType;
use App\Adminlog;
use App\Av;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use DatePeriod;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Config;

class AvailabilityController extends Controller
{

    public $timeRelatedFunctions;
    public $commonFunctions;
    public $apiRelated;
    public $intervals;

    public function __construct()
    {
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->commonFunctions = new CommonFunctions();
        $this->apiRelated = new ApiRelated();
        $this->intervals = array(
            (object) ["name" => "15 minutes", "value" => 15],
            (object) ["name" => "30 minutes", "value" => 30],
            (object) ["name" => "1 hour", "value" => 60],
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('panel.av.index');
    }

    /**
     * Json response for getting availabilities
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailabilities(Request $request)
    {
        $id = -1;
        if (auth()->guard('supplier')->check()) {
            $id = auth()->id();
        }
        elseif ($request->supplier_id > -1)
        {
            $id = $request->supplier_id;
        }

        $avs = Av::select(['id', 'availabilityType', 'name', 'supplierID', 'ticketReferenceCode', 'avTicketType', 'isLimitless'])->where('supplierID', $id)->get();
        return response()->json(['success' => 'Successful', 'availabilities' => $avs]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Availability';
        $adminLog->url = env('APP_ADMIN', 'https://admin.cityzore.com'). '/availability/'.$id.'/delete';
        $adminLog->action = 'Deleted Availability';
        $adminLog->tableName = 'availabilitys';
        $availability = Av::findOrFail($id);

        $options = $availability->options()->get();
        if ($availability->delete()) {
            $availability->options()->detach();
            $adminLog->details = auth()->user()->name. ' clicked to Delete Button and deleted availability with id ' . $id;
            $adminLog->result = 'successful';
            foreach ($options as $opt) {
                if (count($opt->avs()->get()) <= 0) {
                    $opt->isPublished = 0;
                    $opt->save();
                }
            }
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        return redirect()->back();
    }

    /**
     *  Checks for expired availabilities and notifies admins
     */
    public function expiredAvailabilities()
    {
        $availabilities = Av::all();
        foreach ($availabilities as $availability) {
            $avDates = $availability->avdates()->get();
            foreach ($avDates as $av) {
                $validTo = DateTime::createFromFormat('Y-m-d',$av->valid_to);
                $todaySub30Days = DateTime::createFromFormat('Y-m-d',DateTime::createFromFormat('Y-m-d',date('Y-m-d'))->modify("+30 days")->format('Y-m-d'));
                if ($validTo <= $todaySub30Days) {
                    $strToTimeValidTo = strtotime(json_decode(json_encode($validTo), true)['date']);
                    $strToTimeTodaySub30Days = strtotime(json_decode(json_encode($todaySub30Days), true)['date']);
                    $howManyDaysLeft = ($strToTimeTodaySub30Days-$strToTimeValidTo)/86400;
                    event(new StatusLiked($availability->name.' will expire in '.$howManyDaysLeft.' days. Please check this availability !', $availability, 'AVAILABILITY_EXPIRED'));
                }
            }
        }
    }

    /**
     * Function for availability edit page with required variables.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function avEdit($id)
    {
        $availability = Av::findOrFail($id);


   /*   if(auth()->guard('supplier')->check()){
        if($availability->supplierID != auth()->guard('supplier')->user()->id)
            return redirect()->back()->with(['error' => 'You cannot view this availability information']);
      }
*/



        $ticketTypes = TicketType::all();
        $minDate = $availability->avdates()->min('valid_from');
        $maxDate = $availability->avdates()->max('valid_to');
        $weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $disabledWeekDays = json_decode($availability->disabledWeekDays, true);
        $months = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
        $disabledMonths = json_decode($availability->disabledMonths, true);
        $years = ['2020', '2021'];
        $disabledYears = json_decode($availability->disabledYears, true);
        $avdates = $availability->avdates()->get();
        $connectedOptions = $availability->options()->get();
        $connectedProducts = [];
        foreach ($connectedOptions as $opt) {
            $products = $opt->products()->get();
            foreach ($products as $prod) {
                array_push($connectedProducts, $prod);
            }
        }

        $disabledDates = json_decode($availability->disabledDays, true);
        $regularDates = [];
        foreach ($disabledDates as $dt) {
            $dateExplode = explode('/', $dt);
            $regularDates[] = $dateExplode[2].'-'.$dateExplode[1].'-'.$dateExplode[0];
        }

        $connectedProducts = $this->commonFunctions->unique_multidimensional_array($connectedProducts, 'id');
        $notValidForBlockout = count(json_decode($availability->hourly, true)) == 0 && count(json_decode($availability->daily, true)) == 0 && count(json_decode($availability->dateRange, true)) == 0 ? 0 : 1;

        return view('panel.av.edit',
            [
                'regularDates' => $regularDates,
                'availability' => $availability,
                'ticketTypes' => $ticketTypes,
                'minDate' => $minDate,
                'maxDate' => $maxDate,
                'weekDays' => $weekDays,
                'disabledWeekDays' => $disabledWeekDays,
                'months' => $months,
                'disabledMonths' => $disabledMonths,
                'years' => $years,
                'disabledYears' => $disabledYears,
                'avdates' => $avdates,
                'connectedOptions' => $connectedOptions,
                'connectedProducts' => $connectedProducts,
                'notValidForBlockout' => $notValidForBlockout,
                'intervals' => $this->intervals
            ]
        );
    }

    /**
     * Function for avdates. There is 1 to n relation between availabilities and avdates.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function getAvdates(Request $request)
    {
        $availability = Av::findOrFail($request->availabilityId);
        $date = $request->formattedDate;
        $disabledDays = json_decode($availability->disabledDays, true);
        $availabilityType = $availability->availabilityType;
        $isDisabled = false;
        if (in_array($date, $disabledDays)) {
            $isDisabled = true;
        }
        $allDateTimes = [];

        // To get ongoing tickets
        $optionsUsingThisAv = $availability->options()->pluck('id')->toArray();
        $carts = Cart::whereIn('optionID', $optionsUsingThisAv)
            ->whereIn('status', [0, 6])
            ->where(function ($q) use ($request){
                $q->where('date', 'like', '%' . $request->formattedDate . '%')
                    ->orWhere('dateTime', 'like', '%' . Carbon::createFromFormat('d/m/Y', $request->formattedDate)->format('Y-m-d') . '%');
            })
            ->get();

        // For Starting Time
        if ($availabilityType == 'Starting Time') {
            $hourlyDecoded = json_decode($availability->hourly, true);
            if (count($hourlyDecoded) > 0) {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $res = $jsonq->json($availability->hourly);
                $result = $res->where('day', '=', $date)->get();
                if ($res->count() > 0) {
                    $keys = array_keys($result);

                    foreach ($keys as $key) {

                        $ticketCount = $result[$key]['ticket'];

                        if ($availability->isLimitless == 1) {
                            $ticketCount = 0;
                        }
                        $hourFrom = $result[$key]['hour'];

                        $meetingArr = [];
                        $meetingGuides = '';
                        $options = $availability->options;
                        foreach($options as $option) {
                            $meetings = $option->meetings;
                            foreach($meetings as $meeting) {
                                $meetingDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                                $operatingHours = json_decode($meeting->operating_hours, true);
                                if($meeting->date == $meetingDate && $hourFrom == $operatingHours[0]["hour"]) {
                                    $guides = json_decode($meeting->guides, true);
                                    foreach($guides as $keyGuide => $guide) {
                                        $meetingGuide = \App\Admin::findOrFail($guide);
                                        if(!in_array($guide, $meetingArr)) {
                                            if($keyGuide == count($guides)-1)
                                                $meetingGuides = $meetingGuides . $meetingGuide->name . " " . $meetingGuide->surname;
                                            else
                                                $meetingGuides = $meetingGuides . $meetingGuide->name . " " . $meetingGuide->surname . ", ";
                                            array_push($meetingArr, $guide);
                                        }
                                    }
                                }
                            }
                        }

                        $isActive = $result[$key]['isActive'];
                        $sold = $result[$key]['sold'];
                        $onGoingGYG = 0;
                        $onGoingCZ = 0;
                        $onGoingBKN = 0;
                        foreach ($carts as $c) {
                            if ($c->isGYG == 1 && !is_null($c->dateTime)) {
                                $dateCartGYG = explode('-', explode('T', $c->dateTime)[0])[2].'/'.explode('-',explode('T', $c->dateTime)[0])[1].'/'.explode('-',explode('T', $c->dateTime)[0])[0];
                                $hourCartGYG = explode(':', explode('T', $c->dateTime)[1])[0].':'.explode(':',explode('T', $c->dateTime)[1])[1];
                                if ($dateCartGYG == $date && $hourCartGYG == $hourFrom) {
                                    $onGoingGYG += $c->ticketCount;
                                }
                            } elseif ($c->status == 0 && $c->isBokun == 1 && !is_null($c->dateTime)) {
                                $decodedDT = json_decode($c->dateTime);
                                $dateCartBKN = explode('-', explode('T', $decodedDT->dateTime)[0])[2].'/'.explode('-',explode('T', $decodedDT->dateTime)[0])[1].'/'.explode('-',explode('T', $decodedDT->dateTime)[0])[0];
                                $hourCartBKN = explode(':', explode('T', $decodedDT->dateTime)[1])[0].':'.explode(':',explode('T', $decodedDT->dateTime)[1])[1];
                                if ($dateCartBKN == $date && $hourCartBKN == $hourFrom) {
                                    $onGoingBKN += $c->ticketCount ;
                                }
                            } else {
                                $hourCart = json_decode($c->hour,true);
                                foreach($options as $option) {
                                    $avs = $option->avs;
                                    foreach($avs as $ind => $av) {
                                        if($av->id == $request->availabilityId) {
                                            if ($hourCart[$ind]['hour'] == $hourFrom && $c->date == $date) {
                                                $onGoingCZ += $c->ticketCount ;
                                            }

                                            break 2;
                                        }
                                    }
                                }
                            }
                        }
                        array_push($allDateTimes, ['hourFrom' => $hourFrom, 'diffCatFromAdult' => 0, 'ticket' => $ticketCount, 'isActive' => $isActive, 'onGoingCZ' => $onGoingCZ, 'onGoingGYG' => $onGoingGYG, 'onGoingBKN' => $onGoingBKN, 'sold' => $sold, 'availabilityId' => $availability->id, 'availabilityTimeIndex' => "Hourly." . $key, 'meetingGuides' => $meetingGuides]);
                    }
                }
                $res->reset();
            }
        }

        if ($availabilityType == 'Operating Hours') {
            $dailyDecoded = json_decode($availability->daily, true);
            if (count($dailyDecoded) > 0) {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $res = $jsonq->json($availability->daily);
                $result = $res->where('day', '=', $date)->get();
                if ($res->count() >= 1) {
                    $key = key($result);


                    $ticketCount = $result[$key]['ticket'];
                    if ($availability->isLimitless == 1) {
                        $ticketCount = 0;
                    }
                    $hourFrom = $result[$key]['hourFrom'];
                    $hourTo = $result[$key]['hourTo'];

                    $meetingArr = [];
                    $meetingGuides = '';
                    $options = $availability->options;
                    foreach($options as $option) {
                        $meetings = $option->meetings;
                        foreach($meetings as $meeting) {
                            $meetingDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                            $operatingHours = json_decode($meeting->operating_hours, true);
                            $operatingHours[0]["hour"] = preg_replace('/\s+/', '', $operatingHours[0]["hour"]);

                            $avHour = $hourFrom . "-" . $hourTo;

                            if($meeting->date == $meetingDate && $avHour == $operatingHours[0]["hour"]) {
                                $guides = json_decode($meeting->guides, true);
                                foreach($guides as $keyGuide => $guide) {
                                    $meetingGuide = \App\Admin::findOrFail($guide);
                                    if(!in_array($guide, $meetingArr)) {
                                        if($keyGuide == count($guides)-1)
                                            $meetingGuides = $meetingGuides . $meetingGuide->name . " " . $meetingGuide->surname;
                                        else
                                            $meetingGuides = $meetingGuides . $meetingGuide->name . " " . $meetingGuide->surname . ", ";
                                        array_push($meetingArr, $guide);
                                    }
                                }
                            }
                        }
                    }

                    $isActive = $result[$key]['isActive'];
                    $sold = $result[$key]['sold'];
                    $onGoingGYG = 0;
                    $onGoingCZ = 0;
                    $onGoingBKN = 0;
                    foreach ($carts as $c) {
                        if ($c->isGYG == 1 && !is_null($c->dateTime)) {
                            $dateCartGYG = explode('-',explode('T', $c->dateTime)[0])[2].'/'.explode('-',explode('T', $c->dateTime)[0])[1].'/'.explode('-',explode('T', $c->dateTime)[0])[0];
                            $hourCartGYG = explode(':',explode('T', $c->dateTime)[1])[0].':'.explode(':',explode('T', $c->dateTime)[1])[1];
                            // That if will be checked
                            if ($dateCartGYG == $date && $hourCartGYG == '00:00') {
                                $onGoingGYG += $c->ticketCount;
                            }
                        } elseif ($c->status == 0 && $c->isBokun == 1 && !is_null($c->dateTime)) {
                            $decodedDT = json_decode($c->dateTime);
                            $dateCartBKN = explode('-',explode('T', $decodedDT->dateTime)[0])[2].'/'.explode('-',explode('T', $decodedDT->dateTime)[0])[1].'/'.explode('-',explode('T', $decodedDT->dateTime)[0])[0];
                            $hourCartBKN = explode(':',explode('T', $decodedDT->dateTime)[1])[0].':'.explode(':',explode('T', $decodedDT->dateTime)[1])[1];
                            // That if will be checked
                            if ($dateCartBKN == $date && $hourCartBKN == '00:00') {
                                $onGoingBKN += $c->ticketCount;
                            }
                        } else {
                            $hourCart = json_decode($c->hour,true);
                            foreach($options as $option) {
                                $avs = $option->avs;
                                foreach($avs as $ind => $av) {
                                    if($av->id == $request->availabilityId) {
                                        $cHourExploded = explode(' - ', $hourCart[$ind]['hour']);
                                        $cHourFrom = $cHourExploded[0];
                                        $cHourTo = $cHourExploded[1];
                                        if ($c->date == $date && $hourFrom == $cHourFrom && $hourTo == $cHourTo) {
                                            $onGoingCZ += $c->ticketCount;
                                        }

                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                    array_push($allDateTimes, ['hourFrom' => $hourFrom, 'diffCatFromAdult' => 0 , 'hourTo' => $hourTo, 'ticket' => $ticketCount, 'isActive' => $isActive, 'onGoingCZ' => $onGoingCZ, 'onGoingGYG' => $onGoingGYG, 'onGoingBKN' => $onGoingBKN, 'sold' => $sold, 'availabilityId' => $availability->id, 'availabilityTimeIndex' => "Daily." . $key, 'meetingGuides' => $meetingGuides]);
                }
                $res->reset();
            }
        }

        $ms = Cart::whereIn('optionID', $optionsUsingThisAv)
            ->where('status', 2)
            ->where(function ($q) use ($request){
                $q->where('date', 'like', '%' . $request->formattedDate . '%')
                    ->orWhere('dateTime', 'like', '%' . Carbon::createFromFormat('d/m/Y', $request->formattedDate)->format('Y-m-d') . '%');
            })
            ->get();

        foreach ($allDateTimes as $key => $dateTime) {

            foreach ($ms as $item) {

                if (!is_null($item->hour)) {

                    if (count(json_decode($item->hour, 1)) > 1) {
                        $arr = array_column(json_decode($item->hour, 1), 'hour');

                        $minHour = min($arr);

                        $minHour = explode('-', $minHour)[0];

                    }else{

                        $arr = json_decode($item->hour, 1)[0]['hour'];

                        $minHour = explode('-' , $arr)[0];

                    }

                }else{

                    $minHour = Carbon::make($item->dateTime)->format('H:i')[0];

                }


                if ($dateTime['hourFrom'] == $minHour) {
                    foreach (json_decode($item['bookingItems'], 1) as $c) {
                        if ($c['category'] != 'ADULT') {
                            ++$allDateTimes[$key]['diffCatFromAdult'];
                        }
                    }
                }
            }
        }
        // Sorting hours ascending
        usort($allDateTimes, function($a, $b) {return strtotime($a['hourFrom']) > strtotime($b['hourFrom']);});

        return response()->json(['allDateTimes' => $allDateTimes, 'isDisabled' => $isDisabled]);
    }

    public function getAvBookings(Request $request) {
        $avId = $request->avId;
        $avTimeType = $request->avTimeType;
        $avTimeIndex = $request->avTimeIndex;
        $time = $request->avTime;

        $availability = Av::findOrFail($avId);
        $avTime = [];

        if($avTimeType == "Hourly")
            $avTime = $availability->hourly;
        elseif($avTimeType == "Daily")
            $avTime = $availability->daily;

        $avTime = json_decode($avTime, true);
        $avTime = $avTime[$avTimeIndex];

        $avBookings = [];
        $options = $availability->options;
        $bookingsItemsArr = array(
            "ADULT" => 0,
            "EU_CITIZEN" => 0,
            "YOUTH" => 0,
            "CHILD" => 0,
            "INFANT" => 0
        );

        foreach($options as $option) {
            $bookings = \App\Booking::where('optionRefCode', $option->referenceCode)
                ->where('dateTime', 'LIKE', '%'. $time .'%')
                ->where('status', '=', 0)
                ->get();
            foreach($bookings as $booking) {
                $dateTime = $booking->dateTime;
                if($avTimeType == "Hourly") {
                    if($this->isJson($dateTime)) {
                        $dateTime = json_decode($dateTime);
                        foreach($dateTime as $dt) {
                            $day = $dt->dateTime;
                            $day = explode("+", $day)[0];
                            $day = explode("T", $day);
                            $day = $day[0] . " " . $day[1];
                            if(Carbon::createFromFormat('Y-m-d H:i:s', $day)->timestamp == Carbon::createFromFormat('d/m/Y H:i:s', $avTime["day"] . " " . $avTime["hour"] . ":00")->timestamp) {
                                $booking["modalInformations"] = $this->getBookingModalInformations($booking);
                                $bookingsItemsArr = $this->increaseItemValues($booking, $bookingsItemsArr);
                                $booking["itemsSum"] = $bookingsItemsArr;
                                array_push($avBookings, $booking);
                            }
                        }
                    } else {
                        $day = $dateTime;
                        $day = explode("+", $day)[0];
                        $day = explode("T", $day);
                        $day = $day[0] . " " . $day[1];
                        if(Carbon::createFromFormat('Y-m-d H:i:s', $day)->timestamp == Carbon::createFromFormat('d/m/Y H:i:s', $avTime["day"] . " " . $avTime["hour"] . ":00")->timestamp) {
                            $booking["modalInformations"] = $this->getBookingModalInformations($booking);
                            $bookingsItemsArr = $this->increaseItemValues($booking, $bookingsItemsArr);
                            $booking["itemsSum"] = $bookingsItemsArr;
                            array_push($avBookings, $booking);
                        }
                    }
                } elseif($avTimeType == "Daily") {
                    if($this->isJson($dateTime)) {
                        $dateTime = json_decode($dateTime);
                        foreach($dateTime as $dt) {
                            $day = explode("T", $dt->dateTime)[0];
                            if(Carbon::createFromFormat('Y-m-d', $day)->timestamp == Carbon::createFromFormat('d/m/Y', $avTime["day"])->timestamp){
                                $booking["modalInformations"] = $this->getBookingModalInformations($booking);
                                $bookingsItemsArr = $this->increaseItemValues($booking, $bookingsItemsArr);
                                $booking["itemsSum"] = $bookingsItemsArr;
                                array_push($avBookings, $booking);
                            }
                        }
                    } else {
                        $day = explode("T", $dateTime)[0];
                        if(Carbon::createFromFormat('Y-m-d', $day)->timestamp == Carbon::createFromFormat('d/m/Y', $avTime["day"])->timestamp){
                            $booking["modalInformations"] = $this->getBookingModalInformations($booking);
                            $bookingsItemsArr = $this->increaseItemValues($booking, $bookingsItemsArr);
                            $booking["itemsSum"] = $bookingsItemsArr;
                            array_push($avBookings, $booking);
                        }
                    }
                }
            }
        }

        return $avBookings;
    }

    public function getAvOnGoing(Request $request) {
        $hourTo = $request->get('hourTo');
        $date = $request->get('date');
        if($hourTo)
            $hour = $request->get('hour') . " - " . $hourTo;
        else
            $hour = $request->get('hour');
        $avDateTime = $date . " " . $hour;

        $avOnGoing = [];

        $carts = Cart::where('status', 0)->get();
        foreach($carts as $cart) {
            $dateTime = "";
            if($cart->dateTime) {
                if($this->isJson($cart->dateTime)) {
                    $dateTime = Carbon::parse(json_decode($cart->dateTime, true)["dateTime"])->format('d/m/Y H:i'); // Bokun
                } else {
                    $dateTime = Carbon::parse($cart->dateTime)->format('d/m/Y H:i'); // GYG
                }
            } else {
                $option = Option::where('id', $cart->optionID)->first();
                $avs = $option->avs;
                foreach($avs as $ind => $av) {
                    if($av->id == $request->get('avId')) {
                        $cHours = json_decode($cart->hour, true);
                        $dateTime = $cart->date . " " . $cHours[$ind]["hour"]; // Cityzore

                        break;
                    }
                }
            }
            if($dateTime == $avDateTime) {
                $avOnGoingItem = [];
                $avOnGoingItem["items"] = $this->apiRelated->getCategoryAndCountInfo($cart->bookingItems);
                if($cart->isGYG == 1)
                    $avOnGoingItem["from"] = "GYG";
                elseif($cart->isBokun == 1)
                    $avOnGoingItem["from"] = "Bokun";
                else
                    $avOnGoingItem["from"] = "Cityzore";
                $avOnGoingItem["created_at"] = Carbon::parse($cart->created_at)->format('d/m/Y H:i');
                array_push($avOnGoing, $avOnGoingItem);
            }
        }

        return $avOnGoing;
    }

    public function isJson($str) {
        $json = json_decode($str);
        return $json && $str != $json;
    }

    public function getBookingModalInformations($booking) {
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }
        $config = Config::where('userID', $ownerID)->first();

        $product = $booking->bookingProduct;
        $option = $booking->bookingOption;

        $travelers = json_decode($booking->travelers, true)[0];
        $firstName = '';
        $lastName = '';
        $phoneNumber = '';
        $email = '';
        $participantsModal = '';
        if (array_key_exists('firstName', $travelers))
            $firstName = $travelers['firstName'];
        if (array_key_exists('lastName', $travelers))
            $lastName = $travelers['lastName'];
        if (array_key_exists('phoneNumber', $travelers))
            $phoneNumber = $travelers['phoneNumber'];
        if (array_key_exists('email', $travelers))
            $email = $travelers['email'];
        foreach (json_decode($booking->bookingItems, true) as $participants) {
            $participantsModal .= $participants['category']. ': '. $participants['count'] .' ';
        }

        $modalInformations = array(
            'productTitle' => ($product && $product->title) ? $product->title : '-',
            'optionTitle' => ($option && $option->title) ? $option->title : '-',
            'leadTraveler' => $firstName.' '.$lastName,
            'phoneNumber' => $phoneNumber,
            'email' => $email,
            'bookingRefCode' => $booking->bookingRefCode ? $booking->bookingRefCode : '-',
            'bookedOn' => date('d-m-Y H:i', strtotime($booking->created_at)),
            'participants' => $participantsModal,
            'price' => '<i class="'.$config->currencyName->iconClass.'"></i> '.$config->calculateCurrency($booking->totalPrice, $config->currencyName->value, $booking->currencyID)
        );

        return $modalInformations;
    }

    public function createBookingItemsArr($booking) {
        $itemsArr = [];
        foreach (json_decode($booking->bookingItems, true) as $participants) {
            $itemsArr[$participants['category']] = $participants['count'];
        }

        return $itemsArr;
    }

    public function increaseItemValues($booking, $bookingsItemsArr) {
        $itemArr = $this->createBookingItemsArr($booking);
        foreach($itemArr as $key => $item) {
            $bookingsItemsArr[$key] += $item;
        }

        return $bookingsItemsArr;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function avCreate()
    {
        $ticketTypes = TicketType::all();

        return view('panel.av.create', ['ticketTypes' => $ticketTypes, 'intervals' => $this->intervals]);
    }

    /**
     * Function that stores availability, and attaches related tables with it.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function avStore(Request $request)
    {

        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Availability';
        $adminLog->url = $request->url();
        $adminLog->action = 'Saved Availability';

        $availability = new Av();

        $sameNameCount = Av::where('name', $request->name)->count();
        if ($sameNameCount > 0) {
            return response()->json(['errors' => 'There is an availability using this name. Please change name of availability', 'availability' => []]);
        }

        if ($request->isLimitless == 1 && (!is_null($request->ticketType) && $request->ticketType != '0')) {
            $ticketTypeCheck = TicketType::findOrFail($request->ticketType);
            if ($ticketTypeCheck->usableAsTicket == 1) {
                return response()->json(['errors' => 'Barcoded ticket types can\'t be used as limitless tickets. If you want to use limitless ticket option, you should choose "No Ticket" as ticket type']);
            }
        }

        //If Admin => -1
        $supplierID = -1;

        //Auth Supplier ID Check
        if (auth()->guard('supplier')->check()) {
            $supplierID = auth()->guard('supplier')->user()->id;
        }

        $dateRanges = $request->dateRanges;
        $daysAsTicket = [];
        $disabledDays = [];
        $avdates = [];
        foreach ($dateRanges as $i=>$d) {
            $dateFrom = DateTime::createFromFormat('d/m/Y', explode(" - ", $d)[0]);
            $dateTo = DateTime::createFromFormat('d/m/Y', explode(" - ", $d)[1]);
            $startDate = strtotime($dateFrom->format('Y-m-d'));
            $endDate = strtotime($dateTo->format('Y-m-d'));
            $validAndInvalidDays = $this->getValidAndInvalidDays($startDate, $endDate, $i, $request);
            array_push($daysAsTicket, $validAndInvalidDays['daysAsTicket']);
            array_push($disabledDays, $validAndInvalidDays['disabledDays']);

            $avdate = new Avdate();
            $avdate->valid_from_to = $d;
            $avdate->valid_from = $dateFrom->format('Y-m-d');
            $avdate->valid_to = $dateTo->format('Y-m-d');
            $avdate->save();
            $avdates[$i] = $avdate;
        }

        $disabledDays = call_user_func_array('array_merge', $disabledDays); // Merging array to first level
        $daysAsTicket = call_user_func_array('array_merge', $daysAsTicket); // Merging array to first level

        $availability->availabilityType = $request->type;
        $availability->name = $request->name;
        $availability->supplierID = $supplierID;
        if (!is_null($request->ticketType) && $request->ticketType != '0') {
            $availability->hourly = "[]";
            $availability->daily = "[]";
            $availability->barcode = "[]";
            $ticketType = TicketType::findOrFail($request->ticketType);
            if ($ticketType->usableAsTicket == 1) {
                $availability->avTicketType = 4;
                $barcodeCount = Barcode::where('ownerID', $supplierID)->where('ticketType', $request->ticketType)
                    ->where('isUsed', 0)->where('isReserved', 0)->where('isExpired', 0)->count();
                $firstDayFrom = explode(' - ', $dateRanges[0])[0];
                $barcodeArr = [];
                array_push($barcodeArr, ['dayFrom' => $firstDayFrom, 'dayTo' => date('d/m/Y', strtotime('+1 years')), 'ticket' => $barcodeCount, 'sold' => 0]);
                $availability->barcode = json_encode($barcodeArr);
                if ($request->type == 'Starting Time') {
                    $availability->hourly = json_encode($daysAsTicket);
                } else {
                    $availability->daily = json_encode($daysAsTicket);
                }
            } else {
                $availability->avTicketType = $request->type == 'Starting Time' ? 1 : 2;
                if ($request->type == 'Starting Time') {
                    $availability->hourly = json_encode($daysAsTicket);
                    $availability->daily = "[]";
                } else {
                    $availability->hourly = "[]";
                    $availability->daily = json_encode($daysAsTicket);
                }
            }
        } else {
            $availability->avTicketType = $request->type == 'Starting Time' ? 1 : 2;
            $availability->barcode = "[]";
            if ($request->type == 'Starting Time') {
                $availability->hourly = json_encode($daysAsTicket);
                $availability->daily = "[]";
            } else {
                $availability->hourly = "[]";
                $availability->daily = json_encode($daysAsTicket);
            }
        }
        $availability->dateRange = "[]";
        $availability->isLimitless = $request->isLimitless;
        $availability->disabledDays = json_encode($disabledDays);

        if ($availability->save()) {
            $adminLog->details = auth()->user()->name . ' clicked to Save Button and created a new availability with id ' . $availability->id;
            $adminLog->tableName = 'availabilitys';
            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        foreach ($avdates as $a) {
            $availability->avdates()->attach($a);
        }

        if (auth()->guard('supplier')->check()) {
            $availability->supplier()->attach($supplierID);
        }
        $availability->ticketType()->attach($request->ticketType);

        return response()->json(['success' => 'Availability is successfully added.', 'availability' => $availability]);
    }

    /**
     * Gets valid and invalid days for availability view.
     *
     * @param $startDate
     * @param $endDate
     * @param $i
     * @param $request
     * @return array
     */
    public function getValidAndInvalidDays($startDate, $endDate, $i, $request)
    {
        $daysAsTicket = [];
        $disabledDays = [];
        $weekDays = [
            'Monday' => $request->monday, 'Tuesday' => $request->tuesday, 'Wednesday' => $request->wednesday, 'Thursday' => $request->thursday,
            'Friday' => $request->friday, 'Saturday' => $request->saturday, 'Sunday' => $request->sunday
        ];
        foreach ($weekDays as $dayName => $dayRequest) {
            for ($x = strtotime($dayName, $startDate); $x <= $endDate; $x = strtotime('+1 week', $x)) {
                if ($dayRequest && array_key_exists($i, $dayRequest)) {
                    foreach ($dayRequest[$i] as $hours) {
                        if ($request->type == 'Starting Time') {
                            array_push($daysAsTicket, ['day' => date('d/m/Y', $x), 'hour' => $hours['hourFrom'], 'ticket' => 0, 'sold' => 0, 'isActive' => 1]);
                        } else {
                            array_push($daysAsTicket, ['day' => date('d/m/Y', $x), 'hourFrom' => $hours['hourFrom'], 'hourTo' => $hours['hourTo'], 'ticket' => 0, 'sold' => 0, 'isActive' => 1]);
                        }
                    }
                } else {
                    array_push($disabledDays, date('d/m/Y', $x));
                }
            }
        }

        return ['daysAsTicket' => $daysAsTicket, 'disabledDays' => $disabledDays];
    }

    /**
     * Applies all changes to availability table in availability edit view. It's a long function so it can be divided to
     * multiple functions.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function applyChanges(Request $request)
    {
        $availability = Av::findOrFail($request->availabilityId);
        $adminLog = new Adminlog();
        if (auth()->guard('supplier')->check()) {


            $user = auth()->guard('supplier')->user();
            $userID = $user->id;

            if($userID != $availability->supplierID)
                return redirect()->back()->with(['error' => 'You cannot change any option on this availability']);
        }
        else if (auth()->guard('admin')->check()) {
            $user = auth()->guard('admin')->user();
            $userID = $user->id;
        }
        $adminLog->userID = $userID;
        $adminLog->page = 'Availability';
        $adminLog->url = $request->url();




        $changeType = $request->type;
        $data = $request->data;
        $availabilityType = $availability->availabilityType;

        if ($availabilityType == 'Starting Time') {

            $dbColumnNotDecoded = $availability->hourly;

            if ($changeType == 'addNewHoursToAvdate') {
                $avdate = Avdate::findOrFail($data['avdateID']);
                $monday = array_key_exists('monday', $data) ? $data['monday'] : [];
                $tuesday = array_key_exists('tuesday', $data) ? $data['tuesday'] : [];
                $wednesday = array_key_exists('wednesday', $data) ? $data['wednesday'] : [];
                $thursday = array_key_exists('thursday', $data) ? $data['thursday'] : [];
                $friday = array_key_exists('friday', $data) ? $data['friday'] : [];
                $saturday = array_key_exists('saturday', $data) ? $data['saturday'] : [];
                $sunday = array_key_exists('sunday', $data) ? $data['sunday'] : [];
                $weekDays = [
                    'Monday' => $monday, 'Tuesday' => $tuesday, 'Wednesday' => $wednesday, 'Thursday' => $thursday,
                    'Friday' => $friday, 'Saturday' => $saturday, 'Sunday' => $sunday
                ];
                $startDate = strtotime($avdate->valid_from);
                $endDate = strtotime($avdate->valid_to);
                $hourlyDecoded = json_decode($availability->hourly, true);
                if (count($hourlyDecoded) > 0) {
                    foreach ($weekDays as $dayName => $dayRequest) {
                        for ($x = strtotime($dayName, $startDate); $x <= $endDate; $x = strtotime('+1 week', $x)) {
                            if (count($dayRequest) > 0) {
                                foreach ($dayRequest as $hour) {
                                    $dateDmy = date('d/m/Y', $x);
                                    $jsonq = $this->apiRelated->prepareJsonQ();
                                    $res = $jsonq->json($availability->hourly);
                                    $result = $res->where('day', '=', $dateDmy)->where('hour', '=', $hour)->get();
                                    if (count($result) == 0) { // If it's 1, it already has ticket so there's no need to check.
                                        array_push($hourlyDecoded,
                                            ['day' => $dateDmy, 'hour' => $hour, 'ticket' => 0, 'sold' => 0, 'isActive' => 1]
                                        );
                                        // notify push for just isLimitless = 1
                                        $this->apiRelated->makeNotificationOps($availability, $dateDmy, $hour, 999999, 3);
                                    }
                                    $res->reset();
                                }
                            }
                        }
                    }
                } else if (count($hourlyDecoded) == 0) {
                    foreach ($weekDays as $dayName => $dayRequest) {
                        for ($x = strtotime($dayName, $startDate); $x <= $endDate; $x = strtotime('+1 week', $x)) {
                            if (count($dayRequest) > 0) {
                                foreach ($dayRequest as $hour) {
                                    $dateDmy = date('d/m/Y', $x);
                                    array_push($hourlyDecoded,
                                        ['day' => $dateDmy, 'hour' => $hour, 'ticket' => 0, 'sold' => 0, 'isActive' => 1]
                                    );
                                    // notify push for just isLimitless = 1
                                    $this->apiRelated->makeNotificationOps($availability, $dateDmy, $hour, 999999, 3);
                                }
                            }
                        }
                    }
                }

                $availability->hourly = json_encode($hourlyDecoded);
                if ($availability->save()) {
                    $adminLog->action = 'Added New Hours';
                    $adminLog->details = $user->name . ' clicked Save Hours button and added some new hours to the availability with id ' . $availability->id;
                    $adminLog->tableName = 'avs';
                    $adminLog->columnName = 'hourly';
                    $adminLog->result = 'successful';
                    $adminLog->save();
                }
            }

            if ($changeType == 'addedUpdatedDateTime') {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $hourly = $availability->hourly;
                $hourlyDecoded = json_decode($hourly, true);
                if (count($hourlyDecoded) > 0) {
                    $res = $jsonq->json($hourly);
                    $result = $res->where('day', '=', $data['day'])
                        ->where('hour', '=', $data['hourCopy'])->get();
                    if (count($result) == 1) {
                        $addedOrDeleted = 'added';
                        $fromTo = 'to';
                        $key = key($result);
                        $oldTicketCount = $result[$key]['ticket'];
                        $newTicketCount = $data['ticket'];
                        $difference = abs($newTicketCount - $oldTicketCount);
                        if ($newTicketCount - $oldTicketCount <= 0) {
                            $addedOrDeleted = 'deleted';
                            $fromTo = 'from';
                        }
                        $hourlyDecoded[$key]['hour'] = $data['hour'];
                        $hourlyDecoded[$key]['ticket'] = $data['ticket'];
                        $ticketState = $hourlyDecoded[$key]['ticket'];
                        $availability->hourly = json_encode($hourlyDecoded);
                        if ($availability->save()) {
                            $adminLog->action = 'Updated Date Time';
                            $adminLog->details = $user->name . ' clicked Save button of existing datetime and ' . $addedOrDeleted . ' ' . $difference . ' tickets '
                                . $fromTo . ' ' . $data['day'] . ' ' . $data['hour'] . ' to the availability with id ' . $availability->id;
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'hourly';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            $this->apiRelated->makeNotificationOps($availability, $hourlyDecoded[$key]['day'], $hourlyDecoded[$key]['hour'], $ticketState, 1);
                        }
                    } elseif (count($result) > 1) {
                        return response()->json(['error' => 'An unexpected error occured']);
                    } elseif (count($result) == 0) {
                        array_push($hourlyDecoded,
                            ['day' => $data['day'], 'hour' => $data['hour'], 'ticket' => $data['ticket'], 'sold' => 0, 'isActive' => 1]
                        );
                        $ticketState = $data['ticket'];
                        $availability->hourly = json_encode($hourlyDecoded);
                        if ($availability->save()) {
                            $adminLog->action = 'Added Date Time';
                            $adminLog->details = $user->name . ' clicked Save button of new datetime and added ' . $data['ticket'] . ' tickets to '
                                . $data['day'] . ' ' . $data['hour'].'with id '.$availability->id;
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'hourly';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            if ($availability->isLimitless == 1) {
                                $ticketState = 999999;
                            }
                            $this->apiRelated->makeNotificationOps($availability, $data['day'], $data['hour'], $ticketState, 3);
                        }
                    }
                } else {
                    return response()->json(['error' => 'An unexpected error occured']);
                }
            }

            if ($changeType == 'toggleDateTime') {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $hourly = $availability->hourly;
                $hourlyDecoded = json_decode($hourly, true);
                if (count($hourlyDecoded) > 0) {
                    $res = $jsonq->json($hourly);
                    $result = $res->where('day', '=', $data['day'])->where('hour', '=', $data['hour'])->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $hourlyDecoded[$key]['isActive'] = $data['isActive'];
                        $availability->hourly = json_encode($hourlyDecoded);
                        if ($availability->save()) {
                            $ticketState = $data['isActive'] == 1 ? $hourlyDecoded[$key]['ticket'] : 0;
                            if ($availability->isLimitless == 1 && $data['isActive'] == 1) {
                                $ticketState = 999999;
                            }
                            $disabledEnabled = 'Disabled';
                            if ($data['isActive'] == 1) {
                                $disabledEnabled = 'Enabled';
                            }
                            $adminLog->action = $disabledEnabled . ' Date Time';
                            $adminLog->details = $user->name . ' clicked Toggle button of existing datetime and ' . $disabledEnabled . ' '
                                . $data['day'] . ' ' . $data['hour'] . ' to the availability with id ' . $availability->id;
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'hourly';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            $this->apiRelated->makeNotificationOps($availability, $hourlyDecoded[$key]['day'], $hourlyDecoded[$key]['hour'], $ticketState, 3);
                        }
                    } else {
                        return response()->json(['error' => 'An unexpected error occured']);
                    }
                } else {
                    return response()->json(['error' => 'An unexpected error occured']);
                }
            }

            if ($changeType == 'removeDateTime') {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $hourly = $availability->hourly;
                $hourlyDecoded = json_decode($hourly, true);
                if (count($hourlyDecoded) > 0) {
                    $res = $jsonq->json($hourly);
                    $result = $res->where('day', '=', $data['day'])->where('hour', '=', $data['hour'])->get();
                    if (count($result) >= 1) {
                        $key = key($result);
                        $day = $hourlyDecoded[$key]['day'];
                        $hour = $hourlyDecoded[$key]['hour'];
                        unset($hourlyDecoded[$key]);
                        $hourlyDecoded = array_values($hourlyDecoded);
                        $availability->hourly = json_encode($hourlyDecoded);
                        if ($availability->save()) {
                            $adminLog->action = 'Deleted Date Time';
                            $adminLog->details = $user->name . ' clicked X button of existing datetime and deleted '
                                . $data['day'] . ' ' . $data['hour'] . ' to the availability with id ' . $availability->id;
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'hourly';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            $this->apiRelated->makeNotificationOps($availability, $day, $hour, 0, 3);
                            // Disable Day if there is no hour on that day
                            $res->reset();
                            $res = $jsonq->json($availability->hourly);
                            $result = $res->where('day', '=', $data['day'])->get();
                            if (count($result) == 0) {
                                $disabledDaysDecoded = json_decode($availability->disabledDays, true);
                                if (!in_array($data['day'], $disabledDaysDecoded)) {
                                    array_push($disabledDaysDecoded, $data['day']);
                                    $availability->disabledDays = json_encode($disabledDaysDecoded);
                                    $availability->save();
                                }
                            }
                        }
                    } else {
                        return response()->json(['error' => 'An unexpected error occured']);
                    }
                } else {
                    return response()->json(['error' => 'An unexpected error occured']);
                }
            }
        }

        if ($availabilityType == 'Operating Hours') {

            $dbColumnNotDecoded = $availability->daily;

            if ($changeType == 'addNewHoursToAvdate') {
                $avdate = Avdate::findOrFail($data['avdateID']);
                $monday = array_key_exists('monday', $data) ? $data['monday'] : [];
                $tuesday = array_key_exists('tuesday', $data) ? $data['tuesday'] : [];
                $wednesday = array_key_exists('wednesday', $data) ? $data['wednesday'] : [];
                $thursday = array_key_exists('thursday', $data) ? $data['thursday'] : [];
                $friday = array_key_exists('friday', $data) ? $data['friday'] : [];
                $saturday = array_key_exists('saturday', $data) ? $data['saturday'] : [];
                $sunday = array_key_exists('sunday', $data) ? $data['sunday'] : [];
                $weekDays = [
                    'Monday' => $monday, 'Tuesday' => $tuesday, 'Wednesday' => $wednesday, 'Thursday' => $thursday,
                    'Friday' => $friday, 'Saturday' => $saturday, 'Sunday' => $sunday
                ];
                $startDate = strtotime($avdate->valid_from);
                $endDate = strtotime($avdate->valid_to);
                $dailyDecoded = json_decode($availability->daily, true);
                if (count($dailyDecoded) > 0) {
                    foreach ($weekDays as $dayName => $dayRequest) {
                        for ($x = strtotime($dayName, $startDate); $x <= $endDate; $x = strtotime('+1 week', $x)) {
                            if (count($dayRequest) > 0) {
                                $dateDmy = date('d/m/Y', $x);
                                $jsonq = $this->apiRelated->prepareJsonQ();
                                $res = $jsonq->json($availability->daily);
                                $result = $res->where('day', '=', $dateDmy)->get();
                                if (count($result) == 0) { // If it's 1 and/or it's another hourFrom hourTo, we will not touch it
                                    array_push($dailyDecoded,
                                        [
                                            'day' => $dateDmy, 'hourFrom' => $dayRequest[0], 'hourTo' => $dayRequest[1],
                                            'ticket' => 0, 'sold' => 0, 'isActive' => 1
                                        ]
                                    );
                                    // notify push for just isLimitless = 1
                                    $this->apiRelated->makeNotificationOps($availability, $dateDmy, '00:00', 999999, 4);
                                }
                            }
                        }
                    }
                } else if (count($dailyDecoded) == 0) {
                    foreach ($weekDays as $dayName => $dayRequest) {
                        for ($x = strtotime($dayName, $startDate); $x <= $endDate; $x = strtotime('+1 week', $x)) {
                            if (count($dayRequest) > 0) {
                                $dateDmy = date('d/m/Y', $x);
                                array_push($dailyDecoded,
                                    [
                                        'day' => $dateDmy, 'hourFrom' => $dayRequest[0], 'hourTo' => $dayRequest[1],
                                        'ticket' => 0, 'sold' => 0, 'isActive' => 1
                                    ]
                                );
                                // notify push for just isLimitless = 1
                                $this->apiRelated->makeNotificationOps($availability, $dateDmy, '00:00', 999999, 4);
                            }
                        }
                    }
                }
                $availability->daily = json_encode($dailyDecoded);
                if ($availability->save()) {
                    $adminLog->action = 'Added New Hours';
                    $adminLog->details = $user->name . ' clicked Save Hours button and added some new hours to the availability with id ' . $availability->id;
                    $adminLog->tableName = 'avs';
                    $adminLog->columnName = 'daily';
                    $adminLog->result = 'successful';
                    $adminLog->save();
                }
            }

            if ($changeType == 'addedUpdatedDateTime') {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $daily = $availability->daily;
                $dailyDecoded = json_decode($daily, true);
                if (count($dailyDecoded) > 0) {
                    $res = $jsonq->json($daily);
                    $result = $res->where('day', $data['day'])->where('hourFrom', '=', $data['hourCopy'])
                        ->where('hourTo', '=', $data['hourToCopy'])->get();
                    if (count($result) == 1) {
                        $addedOrDeleted = 'added';
                        $fromTo = 'to';
                        $key = key($result);
                        $oldTicketCount = $result[$key]['ticket'];
                        $newTicketCount = $data['ticket'];
                        $difference = abs($newTicketCount - $oldTicketCount);
                        if ($newTicketCount - $oldTicketCount <= 0) {
                            $addedOrDeleted = 'deleted';
                            $fromTo = 'from';
                        }
                        $dailyDecoded[$key]['hourFrom'] = $data['hour'];
                        $dailyDecoded[$key]['hourTo'] = $data['hourTo'];
                        $dailyDecoded[$key]['ticket'] = $data['ticket'];

                        $ticketState = $dailyDecoded[$key]['ticket'];
                        $availability->daily = json_encode($dailyDecoded);
                        if ($availability->save()) {
                            $adminLog->action = 'Updated Date Time';
                            $adminLog->details = $user->name . ' clicked Save button of existing datetime and ' . $addedOrDeleted . ' ' . $difference . ' tickets '
                                . $fromTo . ' ' . $data['day'] . ' ' . $data['hour'] . ' - ' . $data['hourTo'];
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'daily';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            $this->apiRelated->makeNotificationOps($availability, $dailyDecoded[$key]['day'], '00:00', $ticketState, 1);
                        }
                    } elseif (count($result) > 1) {
                        return response()->json(['error' => 'An unexpected error occured!']);
                    } elseif (count($result) == 0) {

                        array_push($dailyDecoded,
                            [
                                'day' => $data['day'], 'hourFrom' => $data['hour'], 'hourTo' => $data['hourTo'],
                                'ticket' => $data['ticket'], 'sold' => 0, 'isActive' => 1
                            ]
                        );

                        $ticketState = $data['ticket'];
                        $availability->daily = json_encode($dailyDecoded);
                        if ($availability->save()) {
                            // return response()->json(['error' => 'An unexpected error occured!', "message" => $availability]);
                            $adminLog->action = 'Added Date Time';
                            $adminLog->details = $user->name . ' clicked Save button of new datetime and added ' . $data['ticket'] . ' tickets to '
                                . $data['day'] . ' ' . $data['hour'] . ' - ' . $data['hourTo'].'with id '.$availability->id;
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'daily';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            if ($availability->isLimitless == 1) {
                                $ticketState = 999999;
                            }
                            $this->apiRelated->makeNotificationOps($availability, $data['day'], '00:00', $ticketState, 3);
                        }
                    }
                } else {
                    return response()->json(['error' => 'An unexpected error occured']);
                }
            }

            if ($changeType == 'toggleDateTime') {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $daily = $availability->daily;
                $dailyDecoded = json_decode($daily, true);
                if (count($dailyDecoded) > 0) {
                    $res = $jsonq->json($daily);
                    $result = $res->where('day', '=', $data['day'])->where('hourFrom', '=', $data['hour'])
                        ->where('hourTo', '=', $data['hourTo'])->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $dailyDecoded[$key]['isActive'] = $data['isActive'];
                        $availability->daily = json_encode($dailyDecoded);
                        if ($availability->save()) {
                            $ticketState = $data['isActive'] == 1 ? $dailyDecoded[$key]['ticket'] : 0;
                            if ($availability->isLimitless == 1 && $data['isActive'] == 1) {
                                $ticketState = 999999;
                            }
                            $disabledEnabled = 'Disabled';
                            if ($data['isActive'] == 1) {
                                $disabledEnabled = 'Enabled';
                            }
                            $adminLog->action = $disabledEnabled . ' Date Time';
                            $adminLog->details = $user->name . ' clicked Toggle button of existing datetime and ' . $disabledEnabled . ' '
                                . $data['day'] . ' ' . $data['hour'] . ' - ' . $data['hourTo'] . ' to the availability with id ' . $availability->id;
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'daily';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            $this->apiRelated->makeNotificationOps($availability, $dailyDecoded[$key]['day'], '00:00', $ticketState, 3);
                        }
                    } else {
                        return response()->json(['error' => 'An unexpected error occured']);
                    }
                } else {
                    return response()->json(['error' => 'An unexpected error occured']);
                }
            }

            if ($changeType == 'removeDateTime') {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $daily = $availability->daily;
                $dailyDecoded = json_decode($daily, true);
                if (count($dailyDecoded) > 0) {
                    $res = $jsonq->json($daily);
                    $result = $res->where('day', '=', $data['day'])->where('hourFrom', '=', $data['hour'])
                        ->where('hourTo', '=', $data['hourTo'])->get();
                    if (count($result) >= 1) {
                        $key = key($result);
                        $day = $dailyDecoded[$key]['day'];
                        unset($dailyDecoded[$key]);
                        $dailyDecoded = array_values($dailyDecoded);
                        $availability->daily = json_encode($dailyDecoded);
                        if ($availability->save()) {
                            $adminLog->action = 'Deleted Date Time';
                            $adminLog->details = $user->name . ' clicked X button of existing datetime and deleted '
                                . $data['day'] . ' ' . $data['hour'] . ' - ' . $data['hourTo'] . ' to the availability with id ' . $availability->id;
                            $adminLog->tableName = 'avs';
                            $adminLog->columnName = 'daily';
                            $adminLog->result = 'successful';
                            $adminLog->save();
                            $this->apiRelated->makeNotificationOps($availability, $day, '00:00', 0, 3);
                            // Disable Day since OH only has one hour
                            $res->reset();
                            $res = $jsonq->json($availability->daily);
                            $result = $res->where('day', '=', $data['day'])->get();
                            if (count($result) == 0) {
                                $disabledDaysDecoded = json_decode($availability->disabledDays, true);
                                if (!in_array($data['day'], $disabledDaysDecoded)) {
                                    array_push($disabledDaysDecoded, $data['day']);
                                    $availability->disabledDays = json_encode($disabledDaysDecoded);
                                    $availability->save();
                                }
                            }
                        }
                    } else {
                        return response()->json(['error' => 'An unexpected error occured']);
                    }
                } else {
                    return response()->json(['error' => 'An unexpected error occured']);
                }
            }

        }

        // Both works for ST and OH

        if ($changeType == 'extendAvdate') {
            $avdate = Avdate::findOrFail($data['avdateID']);
            $oldAvdateTo = $avdate->valid_to;
            $newAvdateTo = $data['validTo'];
            $a = strtotime($oldAvdateTo);
            $b = strtotime($newAvdateTo);
            if ($b < $a) {
                $dbColumnDecoded = json_decode($dbColumnNotDecoded, true);
                $disabledDaysDecoded = json_decode($availability->disabledDays, true);
                $period = new DatePeriod(new DateTime($newAvdateTo), new DateInterval('P1D'), new DateTime($oldAvdateTo . ' 23:59:59')); // added 23:59:59 because of the date period object is not including end date
                foreach ($period as $key => $day) {
                    $dayFormatted = $day->format('d/m/Y');
                    $disabledDaysDecoded = array_values(array_diff($disabledDaysDecoded, [$dayFormatted]));
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    if (count($dbColumnDecoded) > 0) {
                        $res = $jsonq->json($dbColumnNotDecoded);
                        $result = $res->where('day', '=', $dayFormatted)->get();
                        if (count($result) > 0) {
                            $keys = array_keys($result);
                            foreach ($keys as $key) {
                                unset($dbColumnDecoded[$key]);
                            }
                        }
                    }
                    $res->reset();
                }
                $dbColumnDecoded = array_values($dbColumnDecoded);
                if ($availabilityType == 'Starting Time') {
                    $availability->hourly = json_encode($dbColumnDecoded);
                } else {
                    $availability->daily = json_encode($dbColumnDecoded);
                }
                $availability->disabledDays = json_encode($disabledDaysDecoded);
                $availability->save();
            }
            $oldValidFromTo = $avdate->valid_from_to;
            $avdate->valid_from_to = $data['validFromTo'];
            $avdate->valid_from = $data['validFrom'];
            $avdate->valid_to = $data['validTo'];
            if ($avdate->save()) {
                $adminLog->action = 'Extended Date Range';
                $adminLog->details = $user->name . ' clicked Apply button of existing date range and extended '
                    . $oldValidFromTo . ' to ' . $data['validFromTo'] . ' to the availability with id ' . $availability->id;
                $adminLog->tableName = 'avs';
                $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                $adminLog->columnName = $hourlyDaily;
                $adminLog->result = 'successful';
                $adminLog->save();
            }
        }

        if ($changeType == 'addAvdate') {
            $avdate = new Avdate();
            $avdate->valid_from_to = $data['validFromTo'];
            $explodedValidFromTo = explode(' - ', $data['validFromTo']);
            $validFrom = $explodedValidFromTo[0];
            $validTo = $explodedValidFromTo[1];
            $validFromObj = date_create_from_format('d/m/Y', $validFrom);
            $validToObj = date_create_from_format('d/m/Y', $validTo);
            $avdate->valid_from = $validFromObj->format('Y-m-d');
            $avdate->valid_to = $validToObj->format('Y-m-d');
            if ($avdate->save()) {
                $adminLog->action = 'Added Date Range';
                $adminLog->details = $user->name . ' clicked + button for date range and added '
                    . $data['validFromTo'] . ' to the availability with id ' . $availability->id;
                $adminLog->tableName = 'avs';
                $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                $adminLog->columnName = $hourlyDaily;
                $adminLog->result = 'successful';
                $adminLog->save();
                $availability->avdates()->attach($avdate->id);
            }

            return response()->json(['success' => 'Successful', 'orderID' => $data['orderID'], 'avdateID' => $avdate->id]);
        }

        if ($changeType == 'removeAvdate') {
            $avdate = Avdate::findOrFail($data);
            $validFrom = $avdate->valid_from;
            $validTo = $avdate->valid_to;
            $dbColumnDecoded= json_decode($dbColumnNotDecoded, true);
            $disabledDaysDecoded = json_decode($availability->disabledDays, true);
            $period = new DatePeriod(new DateTime($validFrom), new DateInterval('P1D'), new DateTime($validTo . ' 23:59:59')); // added 23:59:59 because of the date period object is not including end date
            foreach ($period as $key => $day) {
                $dayFormatted = $day->format('d/m/Y');
                $disabledDaysDecoded = array_values(array_diff($disabledDaysDecoded, [$dayFormatted]));
                $jsonq = $this->apiRelated->prepareJsonQ();
                if (count($dbColumnDecoded) > 0) {
                    $res = $jsonq->json($dbColumnNotDecoded);
                    $result = $res->where('day', '=', $dayFormatted)->get();
                    if (count($result) > 0) {
                        $keys = array_keys($result);
                        foreach ($keys as $key) {
                            unset($dbColumnDecoded[$key]);
                        }
                    }
                }
                $res->reset();
            }
            $dbColumnDecoded = array_values($dbColumnDecoded);
            if ($availabilityType == 'Starting Time') {
                $availability->hourly = json_encode($dbColumnDecoded);
            } else {
                $availability->daily = json_encode($dbColumnDecoded);
            }
            $availability->disabledDays = json_encode($disabledDaysDecoded);
            if ($availability->save()) {
                $adminLog->action = 'Deleted Date Range';
                $adminLog->details = $user->name . ' clicked x button for date range and deleted '
                    . $avdate->valid_from_to . ' to the availability with id ' . $availability->id;
                $adminLog->tableName = 'avs';
                $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                $adminLog->columnName = $hourlyDaily;
                $adminLog->result = 'successful';
                $adminLog->save();
                $availability->avdates()->detach($avdate->id);
                $avdate->delete();
            }
        }

        if (in_array($changeType, ['enabledDates', 'disabledDates'])) {
            $disabledDays = json_decode($availability->disabledDays, true);
            $isDateValid = $this->timeRelatedFunctions->isDateValid($data, 'Europe/Paris');
            if ($availabilityType == 'Starting Time') {
                $columnStr = 'hourly';
            } else if ($availabilityType == 'Operating Hours') {
                $columnStr = 'daily';
            }
            $jsonq = $this->apiRelated->prepareJsonQ();
            $res = $jsonq->json($availability->$columnStr);
            if ($changeType == 'disabledDates') {
                if ($isDateValid) {
                    $result = $res->where('day', '=', $data)
                        ->where('isActive', '=', 1)
                        ->get();
                    if (count($result) > 0) {
                        $keys = array_keys($result);
                        foreach ($keys as $key) {
                            if (($availability->isLimitless == 0 && $result[$key]['ticket'] > 0) || $availability->isLimitless == 1) {
                                $hour = $availabilityType == 'Starting Time' ? $result[$key]['hour'] : '00:00';
                                $this->apiRelated->makeNotificationOps($availability, $result[$key]['day'], $hour, 0, 3);
                            }
                        }
                    }
                }
                array_push($disabledDays, $data);
                $availability->disabledDays = json_encode($disabledDays);
                if ($availability->save()) {
                    $adminLog->action = 'Disabled Date';
                    $adminLog->details = $user->name . ' clicked DISABLE DAY button and disabled '
                        . $data . ' to the availability with id ' . $availability->id;
                    $adminLog->tableName = 'avs';
                    $adminLog->columnName = $columnStr;
                    $adminLog->result = 'successful';
                    $adminLog->save();
                }
            }

            if ($changeType == 'enabledDates') {
                if (count($disabledDays) > 0 && in_array($data, $disabledDays)) {
                    $disabledDaysDiff = array_values(array_diff($disabledDays, [$data]));
                    $availability->disabledDays = json_encode($disabledDaysDiff);
                    if ($availability->save()) {  // it needs to be inside of the if condition for disabledDays check
                        $adminLog->action = 'Enabled Date';
                        $adminLog->details = $user->name . ' clicked ENABLE DAY button and enabled '
                            . $data . ' to the availability with id ' . $availability->id;
                        $adminLog->tableName = 'avs';
                        $adminLog->columnName = $columnStr;
                        $adminLog->result = 'successful';
                        $adminLog->save();
                    }
                    $result = $res->where('day', '=', $data)
                        ->where('isActive', '=', 1)
                        ->get();
                    if (count($result) > 0) {
                        $keys = array_keys($result);
                        foreach ($keys as $key) {
                            if (($availability->isLimitless == 0 && $result[$key]['ticket'] > 0) || $availability->isLimitless == 1) {
                                $hour = $availabilityType == 'Starting Time' ? $result[$key]['hour'] : '00:00';
                                $ticketState = $result[$key]['ticket'];
                                if ($availability->isLimitless == 1) {
                                    $ticketState = 999999;
                                }
                                $this->apiRelated->makeNotificationOps($availability, $data, $hour, $ticketState, 3);
                            }
                        }
                    }
                }
            }
        }

        if ($changeType == 'disableDateRange') {
            $dateFrom = explode(',', $data)[0];
            $dateTo = explode(',', $data)[1];
            $begin = DateTime::createFromFormat('d/m/Y', $dateFrom);
            $end = DateTime::createFromFormat('d/m/Y', $dateTo);


            $interval = new DateInterval('P1D');
            $end->add($interval);
            $period = new DatePeriod($begin, $interval, $end);
            $disabledDaysDecoded = json_decode($availability->disabledDays, true);

            $daysForNotification = [];
            foreach ($period as $day) {
                $formattedDay = $day->format('d/m/Y');
                if (!in_array($formattedDay, $disabledDaysDecoded)) {
                    array_push($disabledDaysDecoded, $formattedDay);
                    array_push($daysForNotification, $formattedDay);
                }
            }
            // notification check should be done here before saving disabledDays field
            foreach ($daysForNotification as $day) {
                $isDateValid = $this->timeRelatedFunctions->isDateValid($day, 'Europe/Paris');
                if ($isDateValid) {
                    if ($availabilityType == 'Starting Time') {
                        $columnStr = 'hourly';
                    } else if ($availabilityType == 'Operating Hours') {
                        $columnStr = 'daily';
                    }
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $res = $jsonq->json($availability->$columnStr);
                    $result = $res->where('day', '=', $day)
                        ->where('isActive', '=', 1)
                        ->get();
                    if (count($result) > 0) {
                        $keys = array_keys($result);
                        foreach ($keys as $key) {
                            $hour = $availabilityType == 'Starting Time' ? $result[$key]['hour'] : '00:00';
                            $this->apiRelated->makeNotificationOps($availability, $day, $hour, 0, 3);
                        }
                    }
                }
            }
            $availability->disabledDays = json_encode($disabledDaysDecoded);
            if ($availability->save()) {
                $adminLog->action = 'Disabled Date Range';
                $adminLog->details = $user->name . ' clicked DISABLE DATE RANGE button and disabled between '
                    . $dateFrom . ' and ' . $dateTo . ' of the availability with id ' . $availability->id;
                $adminLog->tableName = 'avs';
                $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                $adminLog->columnName = $hourlyDaily;
                $adminLog->result = 'successful';
                $adminLog->save();
            }
        }


        if ($changeType == 'enableDateRange') {
            $dateFrom = explode(',', $data)[0];
            $dateTo = explode(',', $data)[1];
            $begin = DateTime::createFromFormat('d/m/Y', $dateFrom);
            $end = DateTime::createFromFormat('d/m/Y', $dateTo);


            $interval = new DateInterval('P1D');
            $end->add($interval);
            $period = new DatePeriod($begin, $interval, $end);
            $disabledDaysDecoded = json_decode($availability->disabledDays, true);
            $enableDates = [];
            $daysForNotification = [];
            foreach ($period as $day) {
                $formattedDay = $day->format('d/m/Y');
                $enableDates[] = $formattedDay;
            }
            $disabledDaysDiff = array_diff($disabledDaysDecoded, $enableDates);
            // notification check should be done here before saving disabledDays field

            $availability->disabledDays = json_encode($disabledDaysDiff);
            if ($availability->save()) {
                $adminLog->action = 'Disabled Date Range';
                $adminLog->details = $user->name . ' clicked DISABLE DATE RANGE button and disabled between '
                    . $dateFrom . ' and ' . $dateTo . ' of the availability with id ' . $availability->id;
                $adminLog->tableName = 'avs';
                $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                $adminLog->columnName = $hourlyDaily;
                $adminLog->result = 'successful';
                $adminLog->save();
            }
        }


        if ($changeType == 'saveBulkTicket') {
            if ($data['dailyOrDateRange'] != 'Date Range') {
                $dbColumnDecoded = json_decode($dbColumnNotDecoded, true);
                $ticketCount = $data['bulkTicketCount'];
                $selectedDate = $data['selectedDate'];
                $dateFrom = explode(',', $selectedDate)[0];
                $dateTo = explode(',', $selectedDate)[1];
                $jsonq = $this->apiRelated->prepareJsonQ();
                if (count($dbColumnDecoded) > 0) {
                    $res = $jsonq->json($dbColumnNotDecoded);
                    $result = $res->where('day', 'dateGte', $dateFrom)->where('day', 'dateLte', $dateTo)->get();
                    if (count($result) > 0) {
                        $keys = array_keys($result);
                        foreach ($keys as $key) {
                            $oldTicketState = $dbColumnDecoded[$key]['ticket'];
                            $dbColumnDecoded[$key]['ticket'] = $ticketCount;
                            $ticketState = $ticketCount;
                            $isActive = $dbColumnDecoded[$key]['isActive'];
                            $day = $dbColumnDecoded[$key]['day'];
                            $hour = $availabilityType == 'Starting Time' ? $dbColumnDecoded[$key]['hour'] : '00:00';
                            if ($isActive == 1) {
                                $isDateValid = $this->timeRelatedFunctions->isDateValid($day, 'Europe/Paris');
                                if ($isDateValid) {
                                    if ($oldTicketState == 0 || $ticketState < 5) {
                                        $this->apiRelated->makeNotificationOps($availability, $day, $hour, $ticketState, 2);
                                    }
                                }
                            }
                        }
                        if ($availabilityType == 'Starting Time') {
                            $availability->hourly = json_encode($dbColumnDecoded);
                        } else {
                            $availability->daily = json_encode($dbColumnDecoded);
                        }
                        if ($availability->save()) {
                            $adminLog->action = 'Added Bulk Tickets';
                            $adminLog->details = $user->name . ' clicked SAVE TICKETS button and added ' . $ticketCount . ' tickets between '
                                . $dateFrom . ' and ' . $dateTo . ' of the availability with id ' . $availability->id;
                            $adminLog->tableName = 'avs';
                            $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                            $adminLog->columnName = $hourlyDaily;
                            $adminLog->result = 'successful';
                            $adminLog->save();
                        }
                    } else {
                        return response()->json(['bulkTicketToNoMatchException' => 'There is no hour between selected dates. First you should add hours to add bulk tickets!']);
                    }
                }
            } else {
                $dateRangeDecoded = json_decode($availability->dateRange, true);
                $ticketCount = $data['bulkTicketCount'];
                $selectedDate = $data['selectedDate'];
                $dateFrom = explode(',', $selectedDate)[0];
                $dateTo = explode(',', $selectedDate)[1];
                $jsonq = $this->apiRelated->prepareJsonQ();
                if (count($dateRangeDecoded) > 0) {
                    $res = $jsonq->json($availability->dateRange);
                    $result = $res->where('dayFrom', 'dateBetween', [$dateFrom, $dateTo])
                        ->orWhere('dayTo', 'dateBetween', [$dateFrom, $dateTo])
                        ->orWhere('dayFrom', 'dateLte', $dateFrom)->where('dayTo', 'dateGte', $dateTo)
                        ->get();
                    if (count($result) == 0) {
                        array_push($dateRangeDecoded,
                            [
                                'dayFrom' => $dateFrom, 'dayTo'=> $dateTo, 'ticket' => $ticketCount, 'sold' => 0
                            ]
                        );
                    } else if (count($result) >= 1) {
                        return response()->json(['dateRangeError' => 'Error']);
                    }
                    $availability->avTicketType = 3;
                    $availability->dateRange = json_encode($dateRangeDecoded);
                    if ($availability->save()) {
                        $adminLog->action = 'Added Bulk Tickets';
                        $adminLog->details = $user->name . ' clicked SAVE TICKETS button and added ' . $ticketCount . ' tickets between '
                            . $dateFrom . ' and ' . $dateTo . ' of the availability with id ' . $availability->id;
                        $adminLog->tableName = 'avs';
                        $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                        $adminLog->columnName = $hourlyDaily;
                        $adminLog->result = 'successful';
                        $adminLog->save();
                        $dateFromFormatted = DateTime::createFromFormat('d/m/Y', $dateFrom);
                        $dateToFormatted = DateTime::createFromFormat('d/m/Y', $dateTo);
                        $interval = new DateInterval('P1D');
                        $dateToFormatted->add($interval);
                        $period = new DatePeriod($dateFromFormatted, $interval, $dateToFormatted);
                        foreach ($period as $dt) {
                            $dtFormatted = $dt->format('d/m/Y');
                            $isDateValid = $this->timeRelatedFunctions->isDateValid($dtFormatted, 'Europe/Paris');
                            if ($isDateValid) {
                                $this->apiRelated->makeNotificationOps($availability, $dtFormatted, '00:00', $ticketCount, 2);
                            }
                        }
                    }
                } else if (count($dateRangeDecoded) == 0) {
                    array_push($dateRangeDecoded,
                        [
                            'dayFrom' => $dateFrom, 'dayTo'=> $dateTo, 'ticket' => $ticketCount,
                            'sold' => 0
                        ]
                    );
                    $availability->avTicketType = 3;
                    $availability->dateRange = json_encode($dateRangeDecoded);
                    if ($availability->save()) {
                        $adminLog->action = 'Added Bulk Tickets';
                        $adminLog->details = $user->name . ' clicked SAVE TICKETS button and added ' . $ticketCount . ' tickets between '
                            . $dateFrom . ' and ' . $dateTo . ' of the availability with id ' . $availability->id;
                        $adminLog->tableName = 'avs';
                        $hourlyDaily = $availability->avTicketType == 1 ? 'hourly' : 'daily';
                        $adminLog->columnName = $hourlyDaily;
                        $adminLog->result = 'successful';
                        $adminLog->save();
                        $dateFromFormatted = DateTime::createFromFormat('d/m/Y', $dateFrom);
                        $dateToFormatted = DateTime::createFromFormat('d/m/Y', $dateTo);
                        $interval = new DateInterval('P1D');
                        $dateToFormatted->add($interval);
                        $period = new DatePeriod($dateFromFormatted, $interval, $dateToFormatted);
                        foreach ($period as $dt) {
                            $dtFormatted = $dt->format('d/m/Y');
                            $isDateValid = $this->timeRelatedFunctions->isDateValid($dtFormatted, 'Europe/Paris');
                            if ($isDateValid) {
                                $this->apiRelated->makeNotificationOps($availability, $dtFormatted, '00:00', $ticketCount, 2);
                            }
                        }
                    }
                }
            }
        }

        if ($changeType == 'saveInformation') {
            if ($data['isLimitless'] == 1 && (is_null($data['ticketType']) || $data['ticketType'] != '0')) {
                $usableAsTicket = TicketType::find($data['ticketType']) ? TicketType::find($data['ticketType'])->usableAsTicket : 0;
                if ($usableAsTicket == 1) {
                    return response()->json(['limitlessWithTicketTypeError' => 'Barcoded ticket types can\'t be used as limitless tickets. If you want to use limitless ticket option, you should choose "No Ticket" as ticket type']);
                }
            }


            if (!($availability->name == $data['avName'])) {
                $availability->name = $data['avName'];
                $adminLog->action = 'Title Changed';
                $adminLog->details = 'Title Changed';
                $adminLog->tableName = 'avs';
                $adminLog->result = 'successful';
            }


            if($availability->isLimitless == 0 && $data['isLimitless'] ==1) {
                $availability->isLimitless = $data['isLimitless'];
                $adminLog->action = 'Is Limitless';
                $adminLog->details = $user->name . ' clicked SAVE CHANGES button and changed isLimitless to 1 of the availability with id ' . $availability->id;
                $adminLog->tableName = 'avs';
                $adminLog->result = 'successful';

            }

            elseif($availability->isLimitless == 1 && $data['isLimitless']==0) {
                $availability->isLimitless = $data['isLimitless'];
                $adminLog->action = 'Is Limitless';
                $adminLog->details = $user->name . ' clicked SAVE CHANGES button and changed isLimitless to 0 of the availability with id ' . $availability->id;
                $adminLog->tableName = 'avs';
                $adminLog->result = 'successful';

            }


            if (!is_null($data['ticketType']) && $data['ticketType'] != '0') {
                $supplierID = -1;
                if (auth()->guard('supplier')->check()) {
                    $supplierID = auth()->user()->id;
                }
                $ticketType = TicketType::findOrFail($data['ticketType']);
                if ($ticketType->usableAsTicket == 1) {
                    $availability->avTicketType = 4;
                    $barcodeCount = Barcode::where('ownerID', $supplierID)->where('ticketType', $data['ticketType'])
                        ->where('isUsed', 0)->where('isReserved', 0)->where('isExpired', 0)->count();
                    $firstAvdate = $availability->avdates()->first();
                    $firstDayFrom = explode(' - ', $firstAvdate->valid_from_to)[0];
                    $barcodeArr = [];
                    array_push($barcodeArr, ['dayFrom' => $firstDayFrom, 'dayTo' => date('d/m/Y', strtotime('+1 years')), 'ticket' => $barcodeCount, 'sold' => 0]);
                    $availability->barcode = json_encode($barcodeArr);
                }
                $existingTicketTypes = json_decode($availability->ticketType()->pluck('id'), true);
                if (!in_array($data['ticketType'], $existingTicketTypes)) {
                    $availability->ticketType()->detach($existingTicketTypes);
                    $availability->ticketType()->attach($data['ticketType']);
                    $adminLog->action = 'Ticket Type Attach';
                    $adminLog->details = $user->name . ' clicked SAVE CHANGES button and changed ticket type to '.$availability->ticketType()->first()->name.' of the availability with id ' . $availability->id;
                    $adminLog->tableName = 'avs';
                    $adminLog->result = 'successful';

                }
            } else {
                $hourlyDecoded = json_decode($availability->hourly, true);
                if (count($hourlyDecoded) > 0) {
                    $availability->avTicketType = 1;
                } else {
                    $availability->avTicketType = 2;
                }
                if($availability->ticketType()->detach()) {
                    $adminLog->action = 'Ticket Type Detach';
                    $adminLog->details = $user->name . ' clicked SAVE CHANGES button and changed ticket type no "No Ticket" of the availability with id ' . $availability->id;
                    $adminLog->tableName = 'avs';
                    $adminLog->result = 'successful';

                }

            }
            if($availability->save()) {
                $adminLog->save();
            }
        }

        if (in_array($changeType, ['daysOfWeek', 'monthsOfYear', 'years'])) {
            $newDataState = $data;
            $data = is_null($data) ? null : json_encode($data);
            // Disable Days Of Week
            if ($request->type == 'daysOfWeek') {
                $this->logOps($adminLog, 'daysOfWeek', $availability, $data, 'disabledWeekDays');
                $oldDataState = json_decode($availability->disabledWeekDays, true);
                $availability->disabledWeekDays = $data;
                $this->apiRelated->weekDayMonthYearNotifications($oldDataState, $newDataState, $availability, 'weekDay');
            }
            /////////

            // Disable Months Of Year
            if ($request->type == 'monthsOfYear') {
                $this->logOps($adminLog, 'monthsOfYear', $availability, $data, 'disabledMonths');
                $oldDataState = json_decode($availability->disabledMonths, true);
                $availability->disabledMonths = $data;
                $this->apiRelated->weekDayMonthYearNotifications($oldDataState, $newDataState, $availability, 'month');
            }
            /////////

            // Disable Years
            if ($request->type == 'years') {
                $this->logOps($adminLog, 'years', $availability, $data, 'disabledYears');
                $oldDataState = json_decode($availability->disabledYears, true);
                $availability->disabledYears = $data;
                $this->apiRelated->weekDayMonthYearNotifications($oldDataState, $newDataState, $availability, 'year');
            }
            /////////
            if ($availability->save()) {
                $adminLog->save();
            }
        }

        $notValidForBlockout = count(json_decode($availability->hourly, true)) == 0 && count(json_decode($availability->daily, true)) == 0 && count(json_decode($availability->dateRange, true)) == 0 ? 0 : 1;

        return response()->json(['success' => 'Successful', 'notValidForBlockout' => $notValidForBlockout]);
    }

    /**
     * Function for logging operations on availability edit page.
     *
     * @param $adminLog
     * @param $type
     * @param $availability
     * @param $data
     * @param $columnName
     */
    public function logOps($adminLog, $type, $availability, $data, $columnName)
    {
        $adminLog->columnName = $columnName;
        if ($type == 'daysOfWeek') {
            $strUpper = 'Days of Week';
        }
        if ($type == 'monthsOfYear') {
            $strUpper = 'Months of Year';
        }

        if ($type == 'years') {
            $strUpper = 'Years';
        }
        $adminLog->action = 'Disabled/Enabled '. $strUpper;
        $adminLog->oldState = $availability->$columnName;
        $adminLog->newState = $data;
        $dColumn = json_decode($availability->$columnName, true);
        $dData = json_decode($data, true);
        $dColumn = is_null($dColumn) ? [] : $dColumn;
        $dData = is_null($dData) ? [] : $dData;
        $diffAB = array_values(array_diff($dColumn, $dData));
        $diffBA = array_values(array_diff($dData, $dColumn));
        $changeStr = '';
        if ($availability->$columnName == $data) {
            $changeStr .= 'Nothing changed.';
        }
        if (in_array($type, ['daysOfWeek', 'years'])) {
            if (count($diffAB) > 0) {
                $changeStr .= implode(', ', $diffAB) . ' enabled with id '.$availability->id;
            }
            if (count($diffBA) > 0) {
                $changeStr .= implode(', ', $diffBA) . ' disabled with id '.$availability->id;
            }
        }
        if ($type == 'monthsOfYear') {
            $monthsArr = [
                '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
            ];
            if (count($diffAB) > 0) {
                foreach ($diffAB as $ind => $ab) {
                    $changeStr .= $monthsArr[$ab];
                    if (count($diffAB) != 1 && $ind != (count($diffAB) - 1)) {
                        $changeStr .= ', ';
                    }
                }
                $changeStr .= ' enabled with id'.$availability->id;
            }
            if (count($diffBA) > 0) {
                foreach ($diffBA as $ind => $ba) {
                    $changeStr .= $monthsArr[$ba];
                    if (count($diffBA) != 1 && $ind != (count($diffBA) - 1)) {
                        $changeStr .= ', ';
                    }
                }
                $changeStr .= ' disabled with id '.$availability->id;
            }
        }
        $adminLog->details = auth()->user()->name . ' clicked this button: Apply Changes('.$strUpper.'). '. $changeStr;
    }

    /**
     * Function that returns avdates to availability edit page.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvdatesToEditWithoutHour(Request $request)
    {
        $availability = Av::findOrFail($request->availabilityId);
        $avdates = $availability->avdates()->get();
        return response()->json($avdates);
    }

}
