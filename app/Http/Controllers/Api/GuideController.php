<?php

namespace App\Http\Controllers\Api;

use App\GuideToken;
use App\Booking;
use App\Checkin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Hash;
use Str;
use Carbon\Carbon;
use App\Option;
use App\Pricing;
use App\Product;
use App\ProductGallery;
use App\Meeting;
use App\Admin;
use App\Billing;
use App\GuideImage;
use App\MeetingStartEndTime;
use App\Supplier;
use Illuminate\Support\Facades\Storage;
use DB;


class GuideController extends Controller
{

    protected function getUserMeetings($user)
    {
        $targetMeetings = collect();
        $allMeetings = Meeting::all();

        $allMeetings->each(function ($model) use ($user, $targetMeetings) {

            if (in_array($user->id, json_decode($model->guides, true))) {
                $model->guides = json_decode($model->guides);
                $targetMeetings->add($model);

            }

        });

        return $targetMeetings;

    }


    // mobile device's first attempt to receive tokens

    public function postLogin(Request $request)
    {

        if (Auth::guard('admin')->check()) {
            Auth::logout();
        }

        if (Auth::guard('supplier')->check()) {
            Auth::logout();
        }

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {


            if (in_array('Admin', json_decode(Auth::guard('admin')->user()->roles)) || in_array('Super Admin', json_decode(Auth::guard('admin')->user()->roles)) || in_array('Guide', json_decode(Auth::guard('admin')->user()->roles)) || in_array('Others', json_decode(Auth::guard('admin')->user()->roles))) {

                if (in_array('Super Admin', json_decode(Auth::guard('admin')->user()->roles)))
                    $role = 'Admin';
                elseif (in_array('Guide', json_decode(Auth::guard('admin')->user()->roles))) {
                    $role = 'Guide';
                } else {
                    $role = 'Others';

                }


                $user = Auth::guard('admin')->user();

                $user->tokens()->delete();

                $result = $user->tokens()->create([
                    "token_name" => 'guide_token',
                    "token" => $token = hash('sha256', Str::random(60)),
                    "until_validdate" => Carbon::now()->addYear()
                ]);

                $guideImages = $user->images()->pluck('src', 'guide_imageable_id')->toArray();
                $guideImages = empty($guideImages) ? json_decode("{}") : $guideImages;


                return response()->json(['statusCode' => 200, 'status' => 'success',


                    'data' => ['tokenableType' => $result->guide_tokenable_type, 'permissions' => json_decode($user->permissions), 'guideImages' => $guideImages, 'token' => $token, 'id' => $user->id, 'name' => $user->name, 'surname' => $user->surname, 'fullName' => $user->name . ' ' . $user->surname, 'email' => $user->email, 'role' => $role, 'realRoles' => json_decode($user->roles), 'companyName' => !empty($user->company) ? $user->company : null]]);


            } else {
                return response()->json(['statusCode' => 400, 'status' => 'error', 'error' => ['message' => 'invalid role']], 400);
            }

        }


        if (Auth::guard('supplier')->attempt(['email' => $request->email, 'password' => $request->password])) {


            $role = "Others";


            $user = Auth::guard('supplier')->user();


            if ($user->tokens()->count() && $user->tokens()->whereDate('until_validdate', '>=', date('Y-m-d'))->count()) {
                $token = $user->tokens()->first()->token;
                $tokenableType = $user->tokens()->first()->guide_tokenable_type;

            } else {


                $user->tokens()->delete();

                $result = $user->tokens()->create([
                    "token_name" => 'guide_token',
                    "token" => $token = hash('sha256', Str::random(60)),
                    "until_validdate" => Carbon::now()->addYear()
                ]);
                $tokenableType = $result->guide_tokenable_type;


            }


            $guideImages = $user->images()->pluck('src', 'guide_imageable_id')->toArray();
            $guideImages = empty($guideImages) ? json_decode("{}") : $guideImages;


            if (is_null($user->permissions)) {
                //$user->permissions = '["able_to_scroll","ability_to_view"]';
                $user->permissions = '["able_to_scroll","ability_to_view"]';
            }
            return response()->json(['statusCode' => 200, 'status' => 'success',


                'data' => ['isRestaurant' => ($user->isRestaurant == 1), 'tokenableType' => $tokenableType, 'permissions' => json_decode($user->permissions), 'guideImages' => $guideImages, 'token' => $token, 'id' => $user->id, 'name' => $user->contactName, 'surname' => $user->contactSurname, 'fullName' => $user->name . ' ' . $user->surname, 'email' => $user->email, 'role' => $role, 'realRoles' => json_decode($user->roles), 'companyName' => !empty($user->company) ? $user->company : null]]);


        }


        return response()->json(['statusCode' => 400, 'status' => 'error', 'error' => ['message' => 'invalid credintials']], 400);

    }


// We are registering the booking codes read on the device

    public function checkIn(Request $request)
    {


        if (!$request->booking_id) return response()->json(['status' => 'error', 'message' => 'invalid booking id']);

        $rcode = Booking::findOrFail($request->booking_id);


        if ($rcode->check()->where('role', 'Others')->where('checkinable_id', $request->reader_id)->exists() && strtolower($request->role) == 'others') {

            if (!$rcode->check()->where('role', 'Others')->where('checkinable_id', $request->reader_id)->orderBy('id', 'desc')->first()->status == 0)
                return response()->json(['statusCode' => 400, 'status' => 'error', 'error' => ['message' => 'This code has already been read with this role']], 400);
        }


        if (Checkin::where('checkinable_id', $request->reader_id)->where('booking_id', $request->booking_id)->exists()) {

            $rec = Checkin::where('checkinable_id', $request->reader_id)->where('booking_id', $request->booking_id)->first();
            $rec2 = new Checkin();
            $rec2->booking_id = $rec->booking_id;
            $rec2->checkinable_id = $request->reader_id;
            $rec2->checkinable_type = $request->tokenableType ?? 'App\Admin';

            $rec2->readerName = $request->readerName;
            $rec2->email = $request->email;

            $rec2->role = $rec->role;
            $rec2->person = is_array($request->person) ? json_encode($request->person) : $request->person;
            $rec2->ticket = $request->ticket;
            $rec2->save();

            return response()->json(['statusCode' => 200, 'status' => 'success', 'data' => ['message' => 'checkin has been Added successfully!', 'checkins' => $rcode->check()->orderBy("updated_at", "asc")->get()]]);
        }


        $checkin = new Checkin();
        $checkin->booking_id = $rcode->id;
        $checkin->checkinable_id = $request->reader_id;
        $checkin->checkinable_type = $request->tokenableType ?? 'App\Admin';
        $checkin->email = $request->email;
        $checkin->readerName = $request->readerName;
        $checkin->role = $request->role;
        $checkin->ticket = $request->ticket ?? 1;
        $checkin->person = is_array($request->person) ? json_encode($request->person) : $request->person;
        $checkin->status = 1;


        if ($rcode->check()->save($checkin)) {

            return response()->json(['statusCode' => 200, 'status' => 'success', 'data' => ['message' => 'checkin has been Added successfully!', 'checkins' => $rcode->check()->orderBy("updated_at", "asc")->get()]]);
        }


        return response()->json(['statusCode' => 400, 'status' => 'error', 'error' => ['message' => 'An Error Occured!']], 400);

    }


    public function deleteCheck(Request $request)
    {

        if (Checkin::where("id", $request->id)->exists()) {
            $c = Checkin::where("id", $request->id)->first();
            // $c->status = 0;


            $c2 = new Checkin();
            $c2->booking_id = $c->booking_id;
            $c2->checkinable_id = $request->reader_id;
            $c2->checkinable_type = $request->tokenableType ?? 'App\Admin';
            $c2->email = $c->email;
            $c2->readerName = $c->readerName;
            $c2->role = $c->role;
            $c2->person = is_array($c->person) ? json_encode($c->person) : $c->person;
            $c2->ticket = $c->ticket;
            $c2->status = 0;

            if ($c2->save()) {
                return response()->json(["status" => "success", "statusCode" => 200, "data" => ["message" => "record has been cancelled successfully!", "checkins" => $c->book->check()->orderBy("updated_at", "asc")->get()]]);
            }
        }

        return response()->json(["status" => "error", "statusCode" => 400, "error" => ["message" => "Checkin Not Found!"]], 400);

    }


    public function getDateById(Request $request)
    {

        $datesArray = [];
        $dates = Meeting::where("date", ">", Carbon::now()->subMonths($request->month ?? 1))->where("date", "<=", Carbon::now()->addMonths($request->month ?? 1))->orderBy("date", "asc")->get();

        foreach ($dates as $date) {
            if (in_array($request->guide_id, json_decode($date->guides, true)) || empty($request->guide_id)) {

                if (!in_array($date->date, $datesArray))
                    $datesArray[] = $date->date;

            }
        }


        return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => ['dates' => $datesArray]]);
    }


    public function getMeetingTimesByDate(Request $request)
    {

        $alls = Meeting::where('date', $request->date)->get();

        $times = [];

        foreach ($alls as $key => $rec) {
            if (in_array($request->guide_id, json_decode($rec->guides, true)) || empty($request->guide_id)) {

                if (!in_array($rec->time, $times))
                    $times[] = $rec->time;

            }

        }


        return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => ['date' => $request->date, 'times' => $times]]);


    }


    public function getOptionsByTime(Request $request)
    {
        $base_url_for_images = "https://cityzore.s3.eu-central-1.amazonaws.com/product-images/";
        $meetings = Meeting::where('date', $request->date)->where('time', $request->time)->get();
        $options = [];
        $optionsArray = [];


        foreach ($meetings as $key => $value) {

            if (in_array($request->guide_id, json_decode($value->guides, true)) || empty($request->guide_id)) {


                //$options[$value->operating_hours] = $value->option;
                if (!empty($value->operating_hours)) {
                    $options[$value->operating_hours][] = $value->option;
                } else {
                    $options[$key][] = $value->option;
                }

            }
        }

//return response()->json($options);


        foreach ($options as $k => $value2) {


            foreach ($value2 as $v2) {


                if (!Option::where('referenceCode', $v2)->exists()) continue;


                $meeting = Meeting::where("option", $v2)->where('date', $request->date)->where('time', $request->time)->first();
                $guides = Admin::select("name", "surname", "email")->whereIn("id", json_decode($meeting->guides, true))->whereJsonDoesntContain("roles", "Others")->get();

                $option = Option::where('referenceCode', $v2)->first();


                $product = Option::where('referenceCode', $v2)->first()->products()->first();


                $gallery = ProductGallery::where("id", $product->coverPhoto ?? null)->first();


                $optionsArray[$option->referenceCode] = [
                    'id' => $option->id,
                    'referenceCode' => $option->referenceCode,
                    'title' => $option->title,
                    'description' => $option->description,
                    'image' => $base_url_for_images . ($gallery->src ?? ''),
                    'operatingHours' => strlen($k) > 5 ? json_decode($k) : null,
                    'guides' => $guides

                ];

            }


        }


        $responseData = [
            'status' => 'success',
            'statusCode' => 200,
            'data' => $optionsArray
        ];

        return response()->json($responseData);


    }


    public function getTargetMeetings(Request $request)
    {
        $meetings = [];
        $meetings = Booking::with('check')->where(function ($q) use ($request) {


            if (isset($request->date)) {
                $q->where(\Illuminate\Support\Facades\DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $request->date);
            }
            if (isset($request->date)) {
                $q->where('dateTime', 'LIKE', '%' . $request->time . '%');
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

        })->where('status', 0)->orderBy('fullName', 'ASC')->get()->groupBy('optionRefCode');


        $responseArray = [];


        foreach ($meetings as $key => $value) {


            $pricing_id = Option::where('referenceCode', $key)->first()->pricings ?? null;
            $pricing = Pricing::findOrFail($pricing_id);
            $ignoredCategories = $pricing->ignoredCategories;

            foreach ($value as $v) {

                $selected = [];
                $selected["status"] = false;


                if (Meeting::where('date', $request->date)->where('time', $request->time)->where('option', $key)->exists()) {

                    $meeting = Meeting::where('date', $request->date)->where('time', $request->time)->where('option', $key)->first();
                    $bookings = $meeting->bookings ? json_decode($meeting->bookings, true) : [];


                    foreach ($bookings as $key2 => $bb) {

                        foreach ($bb as $b) {
                            if ($v['id'] == $b) {
                                $selected["status"] = true;
                                $selected["by"] = $key2;
                                break;
                            }
                        }


                    }

                }

                $ignoredCategories = empty($ignoredCategories) ? [] : $ignoredCategories;
                $ignoredCategories = is_array($ignoredCategories) ? $ignoredCategories : json_decode($ignoredCategories, true);

                $parsingData = explode('-', $v['bookingRefCode']);
                $responseArray[trim($key)][] = [
                    "meetingId" => $meeting->id,
                    "bookingId" => $v['id'],
                    "isBokun" => $v['isBokun'],
                    "isViator" => $v['isViator'],
                    "optionRefCode" => $v['optionRefCode'] ? trim($v['optionRefCode']) : null,
                    "reservationRefCode" => $v['reservationRefCode'] ? trim($v['reservationRefCode']) : null,
                    "bookingRefCode" => ($v['isBokun'] != 1 && $v['isViator'] != 1) ? $parsingData[count($parsingData) - 1] : $v['bookingRefCode'],
                    "gygBookingReference" => $v['gygBookingReference'] ? trim($v['gygBookingReference']) : null,
                    "bookingItems" => json_decode($v['bookingItems']),
                    "ignoredCategories" => array_map('strtoupper', $ignoredCategories),
                    "fullName" => $v['fullName'],
                    "travelers" => json_decode($v['travelers']),
                    "totalPrice" => $v['totalPrice'],
                    "currencyID" => $v['currencyID'],
                    "check" => $v->check()->orderBy('updated_at', 'asc')->get(),
                    "selected" => $selected,
                    "meetingStartEndTimes" => $meeting->startEndTimes()->where("guide_id", $request->guide_id)->get()
                ];


            }

        }


        return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $responseArray]);


    }


    public function getTargetMeetingsForKrep(Request $request)
    {

        $krepper = Admin::findOrFail($request->id);

        if (!in_array('Others', json_decode($krepper->roles))) {
            return response()->json(["status" => "error", "statusCode" => 400, "error" => ["message" => "invalid role"]], 400);
        }


        $targetMeetings = $this->getUserMeetings($krepper);

        if (!$targetMeetings->count()) return response()->json(["status" => "error", "statusCode" => 200, "data" => []]);


        $targets = Booking::where(function ($q) use ($targetMeetings) {

            $timer = 0;
            foreach ($targetMeetings as $model) {
                $queryString = $timer === 0 ? 'where' : 'orWhere';

                $q->$queryString(function ($q2) use ($model) {

                    $q2->where(\Illuminate\Support\Facades\DB::raw("(DATE_FORMAT(dateForSort,'%Y-%m-%d'))"), $model->date);
                    $q2->where('optionRefCode', $model->option);
                    $q2->where('dateTime', 'LIKE', '%' . $model->time . '%');

                });


                $timer++;
            }


        })->orderBy('dateForSort', 'desc')->where('status', 0)->whereDate("dateForSort", ">", Carbon::now()->subMonths($request->month ?? 1))->whereDate("dateForSort", "<=", Carbon::now()->addMonths($request->month ?? 1))->get()->groupBy('optionRefCode');


        $responseArray = [];

        foreach ($targets as $key => $value) {

            $pricing_id = Option::where('referenceCode', $key)->first()->pricings ?? null;
            $pricing = Pricing::findOrFail($pricing_id);
            $ignoredCategories = $pricing->ignoredCategories;

            $ignoredCategories = empty($ignoredCategories) ? [] : $ignoredCategories;
            $ignoredCategories = is_array($ignoredCategories) ? $ignoredCategories : json_decode($ignoredCategories, true);

            foreach ($value as $v) {
                $parsingData = explode('-', $v['bookingRefCode']);
                $responseArray[] = [
                    "bookingId" => $v['id'],
                    "isBokun" => $v['isBokun'],
                    "isViator" => $v['isViator'],
                    "optionRefCode" => $v['optionRefCode'],
                    "reservationRefCode" => $v['reservationRefCode'],
                    "bookingRefCode" => ($v['isBokun'] != 1 && $v['isViator'] != 1) ? $parsingData[count($parsingData) - 1] : $v['bookingRefCode'],
                    "gygBookingReference" => $v['gygBookingReference'],
                    "bookingItems" => json_decode($v['bookingItems']),
                    "ignoredCategories" => array_map('strtoupper', $ignoredCategories),
                    "fullName" => $v['fullName'],
                    "travelers" => json_decode($v['travelers']),
                    "totalPrice" => $v['totalPrice'],
                    "currencyID" => $v['currencyID'],
                    "dateForSort" => $v['dateForSort'],
                    "check" => $v->check()->orderBy('updated_at', 'asc')->get()
                    //"selected" => false
                ];


            }

        }


        return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $responseArray]);


    }


    public function getTargetMeetingsForSupplier(Request $request)
    {

        $krepper = Supplier::findOrFail($request->id);

        if (!in_array('Supplier', json_decode($krepper->roles))) {
            return response()->json(["status" => "error", "statusCode" => 400, "error" => ["message" => "invalid role"]], 400);
        }

        $targets = Booking::where(function ($q) use ($request) {
            return $q->where(function ($q) use ($request) {
                $q->where('companyID', $request->id);
            })->orWhere(function ($q) use ($request) {
                return $q->whereHas("bookingOption", function ($optQuery) use ($request) {
                    $optQuery->where('rCodeID', $request->id);
                });
            });
        })
            ->orderBy('dateForSort', 'desc')
            ->where('status', 0)
            ->whereDate("dateForSort", ">=",
                Carbon::createFromFormat("Y-m-d", $request->startDate))
            ->whereDate("dateForSort", "<=",
                Carbon::createFromFormat("Y-m-d", $request->endDate))
            ->get()->groupBy('optionRefCode');


        $responseArray = [];

        foreach ($targets as $key => $value) {

            $pricing_id = Option::where('referenceCode', $key)->first()->pricings ?? null;
            $pricing = Pricing::findOrFail($pricing_id);
            $ignoredCategories = $pricing->ignoredCategories;

            $ignoredCategories = empty($ignoredCategories) ? [] : $ignoredCategories;
            $ignoredCategories = is_array($ignoredCategories) ? $ignoredCategories : json_decode($ignoredCategories, true);

            foreach ($value as $v) {
                $parsingData = explode('-', $v['bookingRefCode']);
                $responseArray[] = [
                    "bookingId" => $v['id'],
                    "isBokun" => $v['isBokun'],
                    "isViator" => $v['isViator'],
                    "optionRefCode" => $v['optionRefCode'],
                    "reservationRefCode" => $v['reservationRefCode'],
                    "bookingRefCode" => ($v['isBokun'] != 1 && $v['isViator'] != 1) ? $parsingData[count($parsingData) - 1] : $v['bookingRefCode'],
                    "gygBookingReference" => $v['gygBookingReference'],
                    "bookingItems" => json_decode($v['bookingItems']),
                    "ignoredCategories" => array_map('strtoupper', $ignoredCategories),
                    "fullName" => $v['fullName'],
                    "travelers" => json_decode($v['travelers']),
                    "totalPrice" => $v['totalPrice'],
                    "currencyID" => $v['currencyID'],
                    "dateForSort" => $v['dateForSort'],
                    "check" => $v->check()->orderBy('updated_at', 'asc')->get()
                    //"selected" => false
                ];


            }

        }


        return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $responseArray]);


    }


    public function setCustomersByGuide(Request $request)
    {

        $guide_id = $request->guide_id;
        $datas = is_array($request->customers) ? $request->customers : json_decode($request->customers, true);


        foreach ($datas as $data) {


            $targetMeeting = Meeting::where('date', $request->date)->where('time', $request->time)->where('option', $data["referenceCode"])->first();
            $customerArray = $targetMeeting->bookings ? json_decode($targetMeeting->bookings, true) : [];

            $customerArray[$guide_id] = $data["customers"];
            $targetMeeting->bookings = json_encode($customerArray);
            $targetMeeting->save();

        }


        return response()->json(["status" => "success", "statusCode" => 200, "data" => ["message" => "change Has Been Done Successfully!"]]);


    }


    public function setGuideImage(Request $request)
    {
        $base_url_for_guide_image = "https://cityzore.s3.eu-central-1.amazonaws.com/guide-images/";

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');

            if (!($file->getMimeType() == "image/jpeg" || $file->getMimeType() == "image/png"))
                return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'Unsupported file type!']]);

            $fileName = $file->getClientOriginalName();
            $s3 = Storage::disk('s3');
            $filePath = '/guide-images/' . $fileName;
            $stream = fopen($file->getRealPath(), 'r+');
            $s3->put($filePath, $stream);

            $guideImage = new GuideImage();
            $guideImage->guide_imageable_id = $request->user_id;
            $guideImage->src = $base_url_for_guide_image . $fileName;

            if ($guideImage->save()) {
                return response()->json(["status" => "success", "statusCode" => 200, "data" => ["message" => "Data Send Successfully!", "url" => $guideImage->src]]);
            }
            return response()->json(["status" => "error", "statusCode" => 400, "error" => ["message" => "An Error Occured!"]], 400);


        }

        return response()->json(["status" => "error", "statusCode" => 400, "error" => ["message" => "No file Detected"]], 400);

    }


    public function setBill(Request $request)
    {


        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');

            if (!($file->getMimeType() == "image/jpeg" || $file->getMimeType() == "image/png"))
                return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'Unsupported file type!']]);

            $fileName = $file->getClientOriginalName();
            $s3 = Storage::disk('s3');
            $filePath = '/billing-files/' . $fileName;
            $stream = fopen($file->getRealPath(), 'r+');
            $s3->put($filePath, $stream);


            $bill = new Billing;
            $bill->name = $fileName;


            $admin = Admin::find($request->user_id);

            $result = $admin->billings()->save($bill);


            if ($result) {
                return response()->json(["status" => "success", "statusCode" => 200, "data" => ["message" => "Data Send Successfully!"]]);
            }
            return response()->json(["status" => "error", "statusCode" => 400, "error" => ["message" => "An Error Occured!"]], 400);


        }

        return response()->json(["status" => "error", "statusCode" => 400, "error" => ["message" => "No file Detected"]], 400);

    }


    function getBillsByUserId(Request $request)
    {

        $responseArray = [];
        $bills = Billing::where('billingable_id', $request->user_id)->orderBy("created_at", "desc")->take(20)->get();

        foreach ($bills as $bill) {
            $responseArray[] = [
                "fileUrl" => Storage::disk('s3')->url('billing-files/' . $bill->name),
                "created_at" => $bill->created_at->format('Y-m-d H:i:s')


            ];
        }


        return response()->json(["status" => "success", "statusCode" => 200, "data" => $responseArray]);

    }


    public function updateUser(Request $request)
    {

        $getToken = $this->getToken($request);
        $guide = GuideToken::where('token', $getToken)->first();
        $user = $guide->guide_tokenable;


        if (trim($request->name)) {
            if ($request->tokenableType == "App\\Supplier") {
                $user->contactName = $request->name;
            } else {
                $user->name = $request->name;
            }

        }
        if (trim($request->surname)) {
            if ($request->tokenableType == "App\\Supplier") {
                $user->contactSurname = $request->surname;
            } else {
                $user->surname = $request->surname;
            }
        }
        if (trim($request->password) || trim($request->password_confirmation)) {
            if ($request->password === $request->password_confirmation) {
                $user->password = Hash::make($request->password);
            } else {
                return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'your passwords area are not equal!']], 400);
            }

        }


        if ($user->save()) {
            return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => ['message' => 'your profile informations updated successfully!']]);
        }
        return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'An Error Occured!']], 400);

    }


    protected function getToken($request)
    {
        if (response()->json(!empty($request->header('Authorization')) && count(explode(' ', $request->header('Authorization'))) == 2)) {
            return explode(' ', $request->header('Authorization'))[1] ?? 0;
        }
        return false;
    }


    public function setMeetingStartEndTime(Request $request)
    {


        try {


            foreach ($request->meeting_ids as $mid) {


                if (!Meeting::where("id", $mid)->whereJsonContains("guides", $request->guide_id)->count()) {
                    return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'Undefined Meeting for this guide!']], 400);
                    continue;

                }

                if (MeetingStartEndTime::where("meeting_id", $mid)->where("guide_id", $request->guide_id)->count()) {
                    $meetingStartEndTime = MeetingStartEndTime::where("meeting_id", $mid)->where("guide_id", $request->guide_id)->first();

                } else {
                    $meetingStartEndTime = new MeetingStartEndTime();
                }


                $meetingStartEndTime->meeting_id = $mid;
                $meetingStartEndTime->guide_id = $request->guide_id;

                if ($request->meeting_start_time) {
                    $meetingStartEndTime->meeting_start_time = $request->meeting_start_time;
                }

                if ($request->meeting_end_time) {
                    $meetingStartEndTime->meeting_end_time = $request->meeting_end_time;
                }


                if ($meetingStartEndTime->save()) {

                }


            }

            $response = MeetingStartEndTime::whereIn("meeting_id", $request->meeting_ids)->where("guide_id", $request->guide_id)->get();

            return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $response]);


        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => $e->getMessage()]], 400);

        }


    }

    public function getGuideCalendar(Request $request)
    {
        try {
            if (isset($request->guideId)) {
                $guide_id = $request->guideId;

                // $guide_calendar = \App\Calendar::where('user_id', $guide_id)->whereDate('end', '>=', Carbon::now())->orderBy('start', 'DESC')->get()->groupBy(function ($val) {
                //     return Carbon::parse($val->start)->format('Y-m-d');
                // });

                $guide_calendar = \App\Calendar::where('user_id', $guide_id)->whereDate('end', '>=', Carbon::now())->orderBy('start', 'DESC')->select('*', DB::raw('DATE_FORMAT(start, "%Y-%m-%d") as startDate'))->get()->groupBy('startDate');
                return response()->json(['status' => 'success', 'statusCode' => 200, 'data' => $guide_calendar]);
            } else {
                return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => 'No guide id detected.']], 400);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'statusCode' => 400, 'error' => ['message' => $e->getMessage()]], 400);
        }
    }


}
