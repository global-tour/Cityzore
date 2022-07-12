<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking;
use App\Supplier;
use App\Option;
use App\ProductGallery;
use App\Product;
use App\Admin;
use App\Meeting;
use App\Checkin;
use App\Meetinglog;
use Auth;
use Carbon\Carbon;

use App\Exports\MeetingsExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;

class MeetingController extends Controller
{


    public function index()
    {

        $minDate = Booking::min('dateForSort');
        $maxDate = Booking::max('dateForSort');

        $suppliers = Supplier::pluck("companyName", "id");

        return view('panel.bookings.meetings.index', compact('minDate', 'maxDate', 'suppliers'));

    }


    protected function parsingTime(array $times): array
    {
        $arr = [];


        foreach ($times as $value) {
            if (strpos($value, "T") === false) {
                $value = Carbon::parse($value, "Europe/Paris")->toIso8601String();
            }


            if (strpos($value, 'dateTime') !== false && strlen($value) > 1) {

                foreach (json_decode($value, true) as $dates) {
                    $arr[] = explode('+', explode('T', $dates["dateTime"])[1])[0];


                }


            } else {

                if (strlen($value) > 1)
                    $arr[] = explode('+', explode('T', $value)[1])[0];


            }

        }
        $arr = array_unique($arr);
        return $arr;


    }


    protected function get_meeting_hours(Request $request)
    {


        if (!Booking::where(DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $request->date)->exists()) return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'There is No valid Date']], 400);

        $meetingTimes = Booking::orderBy('created_at', 'asc')->where(function ($q) use ($request) {

            if (isset($request->date)) {
                $q->where(DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $request->date);
            }

            if (isset($request->supplierID) && $request->supplierID != "all") {
                if ($request->supplierID == 33)
                    $request->supplierID = -1;

                $q->where('companyID', $request->supplierID);
            }

        })->where('status', 0)->get()->groupBy('dateTime');

        $timesArray = [];
        foreach ($meetingTimes as $key => $value) {
            $timesArray[] = $key;
        }

        $timesArray = $this->parsingTime($timesArray);
        $currentDate = isset($request->date) ? $request->date : '';

        $view = view('panel.bookings.meetings.parts.hours', compact('timesArray', 'currentDate'))->render();
        return response()->json(['view' => $view]);

        //return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => [ 'date' => $request->date,  'times' => $timesArray] ]);


    }


    protected function get_meeting_options(Request $request)
    {


        $base_url_for_images = "https://cityzore.s3.eu-central-1.amazonaws.com/product-images/";

        $options = Booking::orderBy('created_at')->where(function ($q) use ($request) {

            if (isset($request->date)) {
                $q->where(DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $request->date);
            }

            if (isset($request->supplierID) && $request->supplierID != "all") {
                if ($request->supplierID == 33)
                    $request->supplierID = -1;

                $q->where('companyID', $request->supplierID);
            }

        })->where('status', 0)->get();
        $optionsArray = [];


        foreach ($options as $value) {


            if (!Option::where('referenceCode', $value->optionRefCode)->exists()) continue;

            if (strpos($value->dateTime, $request->time) === false) continue;


            $option = Option::where('referenceCode', $value->optionRefCode)->first();


            if (is_null($value->productRefCode)) {


                $product = Option::where('referenceCode', $value->optionRefCode)->first()->products()->first();

            } else {


                $product = Product::where('referenceCode', $value->productRefCode)->first();
            }


            $gallery = ProductGallery::where("id", $product->coverPhoto ?? null)->first();


            $optionsArray[$option->referenceCode] = [
                'id' => $option->id,
                'referenceCode' => $option->referenceCode,
                'title' => $option->title,
                'description' => $option->description,
                'image' => $base_url_for_images . ($gallery->src ?? ''),
                'operating_hour' => $value->hour ? $value->hour : json_encode([["hour" => Carbon::parse($value->dateTime)->format("H:i")]]),
                "time" => $request->time,
                "day" => Carbon::parse($value->dateForSort)->format("d/m/Y")

            ];


        }

        $responseData = [
            'status' => 'success',
            'statusCode' => 200,
            'data' => $optionsArray
        ];

        $currentDate = isset($request->date) ? $request->date : '';
        $currentTime = isset($request->time) ? $request->time : '';
        $supplierID = isset($request->supplierID) ? $request->supplierID : '';


        $view = view('panel.bookings.meetings.parts.options', compact('supplierID', 'responseData', 'currentDate', 'currentTime'))->render();
        return response()->json(['view' => $view]);
        //return response()->json($responseData);


    }


    protected function get_meetings(Request $request)
    {


        $meetings = [];
        $meetings = Booking::with('check')->where(function ($q) use ($request) {


            if (isset($request->date)) {
                $q->where(DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $request->date);
            }
            if (isset($request->time)) {
                $q->where('dateTime', 'LIKE', '%' . $request->time . '%');
            }

            if (isset($request->supplierID) && $request->supplierID != "all") {
                if ($request->supplierID == 33)
                    $request->supplierID = -1;

                $q->where('companyID', $request->supplierID);
            }

        })->where(function ($q) use ($request) {

            if (isset($request->options)) {


                $timer = 0;

                $request->options = is_array($request->options) ? $request->options : json_decode($request->options, true);

                foreach ($request->options as $value) {
                    $queryStr = ($timer === 0) ? 'where' : 'orWhere';

                    $q->$queryStr('optionRefCode', $value);

                    $timer++;
                }

            }

        })->where('status', 0)->get()->groupBy('optionRefCode');


        $responseArray = [];


        foreach ($meetings as $key => $value) {


            foreach ($value as $v) {
                $responseArray[$key][] = [
                    "bookingId" => $v['id'],
                    "optionRefCode" => $v['optionRefCode'],
                    "reservationRefCode" => $v['reservationRefCode'],
                    "bookingRefCode" => $v['bookingRefCode'],
                    "gygBookingReference" => $v['gygBookingReference'],
                    "bookingItems" => json_decode($v['bookingItems']),
                    "fullName" => $v['fullName'],
                    "travelers" => json_decode($v['travelers']),
                    "totalPrice" => $v['totalPrice'],
                    "currencyID" => $v['currencyID'],
                    "check" => $v['check'],
                    //"time" => $request->time,
                    "operating_hour" => $v['hour'] ? $v['hour'] : json_encode([["hour" => Carbon::parse($v["dateTime"])->format("H:i")]]),
                    "day" => Carbon::parse($v['dateForSort'])->format('d/m/Y')

                ];


            }

        }

        $guides = Admin::where(function ($q) {
            $q->where('roles', "LIKE", "%Guide%")->orWhere('roles', "LIKE", "%Others%");

        })->pluck("name", "id");

        $currentDate = isset($request->date) ? $request->date : '';
        $currentTime = isset($request->time) ? $request->time : '';
        $supplierID = isset($request->supplierID) ? $request->supplierID : '';
        $view = view('panel.bookings.meetings.parts.meetings', compact('supplierID', 'responseArray', 'currentDate', 'currentTime', 'guides'))->render();
        return response()->json(['view' => $view]);
        //return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $responseArray ]);


    }


    public function get_meetings_excel(Request $request)
    {


        $meetings = [];
        $meetings = Booking::with('check')->where(function ($q) use ($request) {


            if (isset($request->date)) {
                $q->where(DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $request->date);
            }
            if (isset($request->time)) {
                $q->where('dateTime', 'LIKE', '%' . $request->time . '%');
            }

            if (isset($request->supplierID) && $request->supplierID != "all") {
                if ($request->supplierID == 33)
                    $request->supplierID = -1;

                $q->where('companyID', $request->supplierID);
            }

        })->where(function ($q) use ($request) {

            if (isset($request->options)) {


                $timer = 0;

                $request->options = is_array($request->options) ? $request->options : json_decode($request->options, true);

                foreach ($request->options as $value) {
                    $queryStr = ($timer === 0) ? 'where' : 'orWhere';

                    $q->$queryStr('optionRefCode', $value);

                    $timer++;
                }

            }

        })->where('status', 0)->get()->groupBy('optionRefCode');


        $responseArray = [];


        foreach ($meetings as $key => $value) {


            foreach ($value as $v) {
                $responseArray[$key][] = [

                    "optionRefCode" => $v['optionRefCode'],
                    "reservationRefCode" => $v['reservationRefCode'],
                    "bookingRefCode" => $v['bookingRefCode'],
                    "gygBookingReference" => $v['gygBookingReference'],
                    "bookingItems" => json_decode($v['bookingItems']),
                    "fullName" => $v['fullName'],
                    "travelers" => json_decode($v['travelers']),
                    "totalPrice" => $v['totalPrice'],
                    "currencyID" => $v['currencyID'],
                    "check" => $v['check'],
                    "operating_hour" => $v['hour'] ? $v['hour'] : json_encode([["hour" => Carbon::parse($v["dateTime"])->format("H:i")]]),
                    "day" => Carbon::parse($v['dateForSort'])->format('d/m/Y')

                ];


            }

        }

        $guides = Admin::where(function ($q) {
            $q->where('roles', "LIKE", "%Guide%");

        })->pluck("email", "id");

        $currentDate = isset($request->date) ? $request->date : '';
        $currentTime = isset($request->time) ? $request->time : '';
        $supplierID = isset($request->supplierID) ? $request->supplierID : '';


        return Excel::download(new MeetingsExport($responseArray, $currentDate, $currentTime, $supplierID), 'meetings.xlsx');


    }


    public function get_meetings_pdf(Request $request)
    {


        $meetings = [];
        $meetings = Booking::with('check')->where(function ($q) use ($request) {


            if (isset($request->date)) {
                $q->where(DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $request->date);
            }
            if (isset($request->time)) {
                $q->where('dateTime', 'LIKE', '%' . $request->time . '%');
            }

            if (isset($request->supplierID) && $request->supplierID != "all") {
                if ($request->supplierID == 33)
                    $request->supplierID = -1;

                $q->where('companyID', $request->supplierID);
            }

        })->where(function ($q) use ($request) {

            if (isset($request->options)) {


                $timer = 0;

                $request->options = is_array($request->options) ? $request->options : json_decode($request->options, true);

                foreach ($request->options as $value) {
                    $queryStr = ($timer === 0) ? 'where' : 'orWhere';

                    $q->$queryStr('optionRefCode', $value);

                    $timer++;
                }

            }

        })->where('status', 0)->orderBy('fullName')->get()->groupBy('optionRefCode');


        $responseArray = [];


        foreach ($meetings as $key => $value) {


            $familyArray = array();
            foreach ($value as $v) {
                array_push($familyArray, json_decode($v['bookingItems']));
                $responseArray[$key][] = [

                    "optionRefCode" => $v['optionRefCode'],
                    "reservationRefCode" => $v['reservationRefCode'],
                    "bookingRefCode" => $v['bookingRefCode'],
                    "gygBookingReference" => $v['gygBookingReference'],
                    "bookingItems" => json_decode($v['bookingItems']),
                    "fullName" => $v['fullName'],
                    "travelers" => json_decode($v['travelers']),
                    "totalPrice" => $v['totalPrice'],
                    "currencyID" => $v['currencyID'],
                    "check" => $v['check'],
                    "operating_hour" => $v['hour'] ? $v['hour'] : json_encode([["hour" => Carbon::parse($v["dateTime"])->format("H:i")]]),
                    "day" => Carbon::parse($v['dateForSort'])->format('d/m/Y')

                ];


            }

            $totalPeople = 0;
            foreach ($familyArray as $family) {
                foreach ($family as $f) {
                    if ($f->category != "INFANT") {
                        $totalPeople += $f->count;
                    }
                }
            }

            $responseArray[$key][0]["totalPeople"] = $totalPeople;

        }

        $guides = Admin::where(function ($q) {
            $q->where('roles', "LIKE", "%Guide%");

        })->pluck("email", "id");

        $currentDate = isset($request->date) ? $request->date : '';
        $currentTime = isset($request->time) ? $request->time : '';
        $supplierID = isset($request->supplierID) ? $request->supplierID : '';

        $pdf = PDF::loadView('panel.bookings.meetings.pdf.meetings', ['supplierID' => $supplierID, 'responseArray' => $responseArray, 'currentDate' => $currentDate, 'currentTime' => $currentTime]);
        return $pdf->stream($currentDate . $currentTime . '.pdf');


    }


    public function ajax(Request $request)
    {


        switch ($request->action) {
            case 'get_meeting_hours':
                return $this->get_meeting_hours($request);
                break;

            case 'get_meeting_options':
                return $this->get_meeting_options($request);
                break;

            case 'get_meetings':
                return $this->get_meetings($request);
                break;

            case 'turn_to_status':

                $allRecords = Checkin::where('booking_id', $request->booking_id)->orderBy('updated_at', 'asc')->get();

                $view = view('panel.bookings.meetings.checkins', compact('allRecords'))->render();
                return response()->json(['view' => $view]);

                break;

            case 'set_guides':

                if ($request->time != "00:00:00") {


//                    if (Carbon::parse(date("Y-m-d"))->gt(Carbon::parse($request->date))) {
//                        return response()->json(["status" => "error", "statusCode" => 400, "message" => "you cannot change a past dated transaction!"]);
//                    }

                }


                if (Meeting::where('date', $request->date)->where('time', $request->time)->where('option', $request->option)->exists()) {
                    $meeting = Meeting::where('date', $request->date)->where('time', $request->time)->where('option', $request->option)->first();
                } else {
                    $meeting = new Meeting();
                }


                $metguides = $meeting->guides;
                $reqguides = $request->guides;
                $meeting->option = $request->option;
                $meeting->date = $request->date;
                $meeting->time = $request->time;
                $meeting->guides = $request->guides;
                $meeting->operating_hours = $request->operating_hour;


                $optRecord = Option::where("referenceCode", $request->option)->first();
                $optTime = $optRecord->guideTime;
                $optTimeType = $optRecord->guideTimeType;

                if (empty($optTime)) {
                    $optTime = 1;
                }

                if (empty($optTimeType)) {
                    $optTimeType = 'h';
                }


                // return response()->json($optTime. ' - '.$optTimeType);

                if ($meeting->time == '00:00:00') {

                    if ($request->operating_hour != '[{"hour":"00:00"}]') {
                        $operating_array = json_decode($request->operating_hour, true);

                        $first_one = $operating_array[0]["hour"];
                        $last_one = $operating_array[count($operating_array) - 1]["hour"];
                        $starting_hour = trim(explode("-", $first_one)[0]) . ":00";
                        $ending_hour = trim(explode("-", $last_one)[1]) . ":00";
                        $meeting->clock_in = carbon::parse($request->date . " " . $starting_hour)->format("Y-m-d H:i:s");
                        $meeting->clock_out = carbon::parse($request->date . " " . $ending_hour)->format("Y-m-d H:i:s");


                        $starting_last = trim(explode("-", $first_one)[1]) . ":00";
                        $ending_first = trim(explode("-", $last_one)[0]) . ":00";

                        $total_diff = Carbon::parse($request->date . " " . $ending_first)->timestamp - Carbon::parse($request->date . " " . $starting_last)->timestamp;

                        if ($total_diff < 0)
                            $total_diff = 0;

                        $diff_minute = (int)($total_diff / 60);
                        $meeting->diff = $diff_minute;


                    }


                } else {


                    if ($optTime && $optTimeType) {


                        $getAlReadyTimeStamp = $this->dateTimeToTimestamp($meeting->date, $meeting->time);


                        if ($optTimeType == "m") {
                            $schedule_in_timestamp = $getAlReadyTimeStamp->copy()->subMinutes($optTime);
                            $schedule_out_timestamp = $getAlReadyTimeStamp->copy()->addMinutes($optTime);
                        } else {
                            $schedule_in_timestamp = $getAlReadyTimeStamp->copy()->subHours($optTime);
                            $schedule_out_timestamp = $getAlReadyTimeStamp->copy()->addHours($optTime);
                        }


                        $meeting->clock_in = $schedule_in_timestamp->format('Y-m-d H:i:s');
                        $meeting->clock_out = $schedule_out_timestamp->format('Y-m-d H:i:s');

                    }

                }


                if ($meeting->save()) {

                    $this->addLog($meeting->id, $metguides, $reqguides);
                    return response()->json(["status" => "success", "statusCode" => 200, "message" => "Changes has been done successfully"]);
                }
                return response()->json(["status" => "error", "statusCode" => 400, "message" => "There is An Error!"]);


                break;

            case 'check_or_not':
                $checkin = Checkin::find($request->data_check_id);
                $checkin->status = abs($checkin->status - 1);
                if ($checkin->save()) {
                    return response()->json(["result" => 1, "message" => "Status Changed", "status" => $checkin->status]);
                }
                break;


            default:
                # code...
                break;
        }
    }


    protected function dateTimeToTimestamp($date, $time)
    {

        $carbon = Carbon::parse($date . ' ' . $time);
        return $carbon;

    }


    protected function addLog($meeting_id, $metguides, $reqguides)
    {
        $metguides = is_null($metguides) ? [] : json_decode($metguides, true);
        $reqguides = is_null($reqguides) ? [] : json_decode($reqguides, true);


        if (count($metguides) > count($reqguides)) {
            $action = "Remove";
            $diff = array_diff($metguides, $reqguides);
        } else {
            $action = "Add";
            $diff = array_diff($reqguides, $metguides);
        }


        $newDiff = [];
        foreach ($diff as $value) {
            $newDiff[] = $value;
        }


        $log = new Meetinglog();
        $log->meeting_id = $meeting_id;
        $log->logger_id = Auth::guard('admin')->user()->id;
        $log->logger_email = Auth::guard('admin')->user()->email;
        $log->affected_id = json_encode($newDiff);
        $log->action = $action;

        $log->save();

    }


    public function getMeetingLogs()
    {
        return view('panel.meetinglogs.index');
    }

}
