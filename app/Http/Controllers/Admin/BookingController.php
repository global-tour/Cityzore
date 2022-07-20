<?php

namespace App\Http\Controllers\Admin;

use App\Adminlog;
use App\BookingContactMailLog;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Http\Controllers\Helpers\MailOperations;
use App\Http\Controllers\Helpers\BigBusRelated;
use App\Http\Controllers\Helpers\TootbusRelated;
use App\Http\Controllers\Helpers\AccessRelated;
use App\Booking;
use App\BookingImage;
use App\BookingInvoice;
use App\Imports\BookingsImport;
use App\Invoice;
use App\Mails;
use App\Option;
use App\Platform;
use App\Product;
use App\Supplier;
use App\User;
use App\Rcode;
use App\Barcode;
use App\Checkin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Nahid\JsonQ\Jsonq;
use App\Exports\BookingsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Helpers\CryptRelated;
use App\Av;
use PHPUnit\Exception;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;


class BookingController extends Controller
{
    public $refCodeGenerator;
    public $timeRelatedFunctions;
    public $apiRelated;
    public $mailOperations;
    public $bigBusRelated;
    public $tootbusRelated;
    public $cryptRelated;

    public function __construct()
    {
        $this->refCodeGenerator = new RefCodeGenerator();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->apiRelated = new ApiRelated();
        $this->mailOperations = new MailOperations();
        $this->bigBusRelated = new BigBusRelated();
        $this->tootbusRelated = new TootbusRelated();
        $this->cryptRelated = new CryptRelated();
        $this->accessRelated = new AccessRelated();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $suppliers = Supplier::pluck('companyName', 'id');
        $suppliers->prepend("choose", "");
        $affiliated = User::where('commission', '<>', null)->where('affiliate_unique', '<>', null)->pluck('email', 'id');
        $affiliated->prepend("choose", "");
        //$options = Option::pluck('title', 'referenceCode');
        if (auth()->guard('supplier')->check()) {
            $products = Product::where('supplierID', auth()->guard('supplier')->user()->id)->pluck('title', 'id');
            $restaurants = Option::where('rCodeID', auth()->guard('supplier')->user()->id)->pluck('title', 'id');
        } else {
            $products = Product::pluck('title', 'id');
            $restaurants = [];
        }
        $platforms=Platform::all();
        $comissioners=User::whereNotNull('commission')->where('commission','!=',0)->select('id','name','surname')->get();

        return view('panel.bookings.index', compact('suppliers', 'affiliated', 'products','platforms','comissioners', 'restaurants'));
    }

    public function indexV2()
    {
        return view('panel.bookings.booking-v2');
    }

    /**
     * Exports bookings to excel file
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(Request $request)
    {
        return Excel::download(new BookingsExport($request), 'bookings.xlsx');
    }

    public function importExcel(Request $request)
    {
        Excel::import(new BookingsImport, $request->file);
        return response()->json(['status' => true, 'message' => 'Excel imported successfully']);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $bookings = Booking::findOrFail($id);
        $invoice=Invoice::where('bookingID',intval($id))->value('companyAddress');

        $bookingsOption = $bookings->bookingOption;
        if (is_null($bookings->avID))
            $avs = $bookingsOption->avs()->orderBy('id', 'asc')->get();
        else {
            $avID = json_decode($bookings->avID);
            $avs = Av::whereIn('id', $avID)->get();
        }
        $bookingAvData = [];
        $dateTime = $bookings->dateTime;
        if ($this->isJSON($dateTime)) {
            $date = json_decode($dateTime, true);
            $date = Carbon::parse($date[0]["dateTime"])->format('d/m/Y');
        } else {
            $date = Carbon::parse($dateTime)->format('d/m/Y');
        }
        foreach ($avs as $ind => $av) {
            $availabilityType = $av->availabilityType;
            $availabilityName = $av->name;

            $bookingAvData[$ind]["availabilityType"] = $availabilityType;
            $bookingAvData[$ind]["availabilityName"] = $availabilityName;
            $bookingAvData[$ind]["date"] = $date;
                if (!is_null($bookings->hour)) {
                    $hour = json_decode($bookings->hour, true);

                    $h = $hour[$ind];

                    $h = preg_replace('/\s+/', '', $h["hour"]);
                    if (count(explode('-', $h)) == 2) {
                        $bookingAvData[$ind]["hourFrom"] = explode('-', $h)[0];
                        $bookingAvData[$ind]["hourTo"] = explode('-', $h)[1];
                    } else {
                        $bookingAvData[$ind]["hourFrom"] = $h;
                        $bookingAvData[$ind]["hourTo"] = null;
                    }
                } else {

                    if ($this->isJSON($dateTime)) {
                        if (isset(json_decode($dateTime, true)[$ind])) {
                            $hour = json_decode($dateTime, true);
                            $hour = Carbon::parse($hour[$ind]["dateTime"])->format('H:i');
                        }
                    } else {
                        $hour = Carbon::parse($dateTime)->format('H:i');
                    }

                    $bookingAvData[$ind]["hourFrom"] = $hour;
                    $bookingAvData[$ind]["hourTo"] = null;
                }
        }
        $platforms=Platform::all();
        return view('panel.bookings.edit',
            [
                'bookings' => $bookings,
                'invoice' => $invoice,
                'bookingAvData' => $bookingAvData,
                'platforms' => $platforms,
            ]
        );
    }

    /**
     * Update booking after bookIt function
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $bookings = Booking::findOrFail($id);
        $invoice = Invoice::where('bookingID', $bookings->id)->first();


        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $email = $request->email;
        $phoneNumber = $request->phoneNumber;

        $user = Auth::guard('admin')->user();
        $travelPeople=json_decode($bookings->travelers)[0];
        $rDate=Carbon::createFromFormat('d/m/Y', $request->dateTime)->format('Y-m-d');
        $bDateRaw=$this->isJSON($bookings->dateTime) ? json_decode($bookings->dateTime)[0]->dateTime : $bookings->dateTime;
        $bDate=explode('T',$bDateRaw)[0];
        $referenceCode = $bookings->gygBookingReference ?? (($bookings->isBokun == 1 || $bookings->isViator == 1) ? $bookings->bookingRefCode : (explode('-', $bookings->bookingRefCode)[count(explode('-', $bookings->bookingRefCode))-1]));
        $logDetail=$user->name.' '.$referenceCode.' reference code of Booking Updated';
        $isChanged=false;
        $action='Update';
        if ($request->firstName!=$travelPeople->firstName){
            $logDetail.= ', changed Name '.$travelPeople->firstName.' to '.$request->firstName;
            $isChanged=true;
        }
        if ($request->lastName!=$travelPeople->lastName){
            $logDetail.= ', changed LastName '.$travelPeople->lastName.' to '.$request->lastName;
            $isChanged=true;
        }
        if ($request->email!=$travelPeople->email){
            $logDetail.= ', changed Email '.$travelPeople->email.' to '.$request->email;
            $isChanged=true;
        }
        if ($request->phoneNumber!=$travelPeople->phoneNumber){
            $logDetail.= ', changed PhoneNumber '.$travelPeople->phoneNumber.' to '.$request->phoneNumber;
            $isChanged=true;
        }
        if ($request->travelerHotel!=$bookings->travelerHotel){
            $logDetail.= ', changed Hotel '.($bookings->travelerHotel ==null ? 'Null':$bookings->travelerHotel).' to '.$request->travelerHotel;
            $isChanged=true;
        }
        if ($request->totalPrice!=$bookings->totalPrice){
            $logDetail.= ', changed TotalPrice '.($bookings->totalPrice ==null ? 0:$bookings->totalPrice).' to '.$request->totalPrice;
            $isChanged=true;
        }
        if ($rDate!=$bDate){
            $logDetail.= ', changed Date '.$bDate.' to '.$rDate;
            $isChanged=true;
        }
        if (intval($request->platformID)!=$bookings->platformID){
            $logDetail.= ', changed Platform '.Platform::where('id',$bookings->platformID)->value('name').' to '.Platform::where('id',$request->platformID)->value('name');
            $isChanged=true;
        }
        if($invoice!=null){
            if ($request->companyAddress!=$invoice->companyAddress){
                $logDetail.= ', changed CompanyAddress '.($invoice->companyAddress ==null ? 'Null':$invoice->companyAddress).' to '.$request->companyAddress;
                $isChanged=true;
            }
        }
        if ($request->companyAddress!=null &&$invoice!=null) $invoice->companyAddress = $request->companyAddress;
        $bookings->travelerHotel = $request->get('travelerHotel');
        $bookings->totalPrice = $request->get('totalPrice');

        $travelers2 = [];
        $arr = ['email' => $email, 'firstName' => $firstName, 'lastName' => $lastName, 'phoneNumber' => $phoneNumber];
        array_push($travelers2, $arr);
        $bookings->travelers = json_encode($travelers2);

        $date = $request->get('dateTime');
        if (!is_null($bookings->date)) $bookings->date = $date;
        if (!is_null($bookings->dateForSort)) $bookings->dateForSort = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        if (!is_null($bookings->hour)) {
            $hours = json_decode($bookings->hour, true);

            foreach ($hours as $ind => $hour) {
                if ($request->has('hourTo_' . $ind)){
                    $old=$hours[$ind]["hour"];
                    $hours[$ind]["hour"] = $request->get('hourFrom_' . $ind) . " - " . $request->get('hourTo_' . $ind);
                    if ($old!=$hours[$ind]["hour"]){
                        $logDetail.= ', changed Hour'.$ind.' '.$old.' to '.$hours[$ind]["hour"];
                        $isChanged=true;
                    }
                }

                else{
                    $old=$hours[$ind]["hour"];
                    $hours[$ind]["hour"] = $request->get('hourFrom_' . $ind);
                    if ($old!=$hours[$ind]["hour"]){
                        $logDetail.= ', changed Hour'.$ind.' '.$old.' to '.$hours[$ind]["hour"];
                        $isChanged=true;
                    }
                }

            }

            $bookings->hour = json_encode($hours);
        }
        if ($this->isJSON($bookings->dateTime)) {
            $dateTime = json_decode($bookings->dateTime, true);
            foreach ($dateTime as $ind => $dt) {
                $dateTime[$ind]["dateTime"] = explode("T", $dateTime[$ind]["dateTime"]);
                $dateTime[$ind]["dateTime"][0] = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                $dateTime[$ind]["dateTime"][1] = explode("+", $dateTime[$ind]["dateTime"][1]);
                if ($dateTime[$ind]["dateTime"][1][0] != "00:00:00")
                    $dateTime[$ind]["dateTime"][1][0] = $request->get('hourFrom_' . $ind) . ":00";

                $dateTime[$ind]["dateTime"][1] = implode("+", $dateTime[$ind]["dateTime"][1]);
                $dateTime[$ind]["dateTime"] = implode("T", $dateTime[$ind]["dateTime"]);
            }
            $dateTime = json_encode($dateTime);
        } else {
            $dateTime = $bookings->dateTime;
            $dateTime = explode("T", $dateTime);
            $dateTime[0] = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            $dateTime[1] = explode("+", $dateTime[1]);
            if ($dateTime[1][0] != "00:00:00")
                $dateTime[1][0] = $request->get('hourFrom_0') . ":00";

            $dateTime[1] = implode("+", $dateTime[1]);
            $dateTime = implode("T", $dateTime);
        }
        $bookings->dateTime = $dateTime;
        $bookings->platformID = intval($request->platformID);

        if(!$isChanged) {
            $logDetail.= ' NOTHING';
            $action= 'NOTHING';
        }
        $adminLog = new AdminLog();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Booking Edit';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = $action;
        $adminLog->details = $logDetail;
        $adminLog->tableName = 'bookings invoce';
        $adminLog->columnName = 'bookings.*, companyAddress';

        if($bookings->save()){
            Invoice::where('bookingID',$id)->update([
                'companyAddress'=>$request->companyAddress
            ]);
            $adminLog->result = 'successful';
        }else{
            $adminLog->result = 'failed';
        }
        $adminLog->save();
        //return redirect('/bookings');
        return redirect('/close-window');
    }

    public function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (Auth::guard('supplier')->check()) {
            $supplier = Supplier::where('id', Auth::guard('supplier')->user()->id)->first();
            $products = Product::where('supplierID', auth()->guard('supplier')->user()->id)
                ->where('isPublished', 1)->where('isDraft', 0)->where('supplierPublished', 1)->get();
        }

        if (Auth::guard('admin')->check()) {
            $supplier = Supplier::where('isRestaurant', null)->orWhere('isRestaurant', 0)->get();
            $products = [];
        }

        return view('panel.bookings.create',
            [
                'supplier' => $supplier,
                'products' => $products
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function store(Request $request)
    {
        $product = Product::findOrFail($request->productSelect);
        $option = Option::findOrFail($request->optionSelect);
        $pricing = $option->pricings()->first();
        $supplierID = $option->supplierID;
        $supplier = Supplier::find($supplierID);
        $availabilities = $option->avs;
        $dateTime = [];
        $type = [];
        $hour = [];
        $adultPrice = null;
        $youthPrice = null;
        $childPrice = null;
        $infantPrice = null;
        $euCitizenPrice = null;
        $ticketTypes = [];

        foreach ($availabilities as $av) {
            array_push($type, $av->availabilityType);
            $ticketType = $av->ticketType()->first();
            array_push($ticketTypes, $ticketType);
            for ($i = 0; $i < count($type); $i++) {
                $bookingTime = 'bookingTime' . $i;
            }
            if ($av->availabilityType == 'Starting Time') {
                $ymd = $this->timeRelatedFunctions->convertDmyToYmdWithHour($request->bookingDate, $request->$bookingTime, $product->countryName->timezone);
                $date = ['dateTime' => $ymd];
                array_push($dateTime, $date);

            }
            if ($av->type == 'Operating Hours') {
                $ymd = $this->timeRelatedFunctions->convertDmyToYmdWithHour($request->bookingDate, '00:00', $product->countryName->timezone);
                $date = ['dateTime' => $ymd];
                array_push($dateTime, $date);
            }

            $h = ['hour' => $request->$bookingTime];
            array_push($hour, $h);
        }

        $bookingItems = [];
        $minPersonArr = json_decode($pricing->minPerson, true);
        $maxPersonArr = json_decode($pricing->maxPerson, true);
        $adultPriceCom = 0;
        $youthPriceCom = 0;
        $childPriceCom = 0;
        $infantPriceCom = 0;
        $euCitizenPriceCom = 0;
        if ($request->adultCount > 0) {
            $adultPriceComArr = json_decode($pricing->adultPriceCom, true);
            foreach ($maxPersonArr as $index => $item) {
                if ($request->adultCount >= $minPersonArr[$index] && $request->adultCount <= $item) {
                    $adultPriceCom = $adultPriceComArr[$index];
                }
            }
            $adult = ['category' => 'ADULT', 'count' => $request->adultCount];
            array_push($bookingItems, $adult);
            $adultPrice = $adultPriceCom * $request->adultCount;
        }
        if ($request->euCitizenCount > 0) {
            $euCitizenPriceComArr = json_decode($pricing->euCitizenPriceCom, true);
            foreach ($maxPersonArr as $index => $item) {
                if ($request->euCitizenCount >= $minPersonArr[$index] && $request->euCitizenCount <= $item) {
                    $euCitizenPriceCom = $euCitizenPriceComArr[$index];
                }
            }
            $euCitizen = ['category' => 'EU_CITIZEN', 'count' => $request->euCitizenCount];
            array_push($bookingItems, $euCitizen);
            $euCitizenPrice = $euCitizenPriceCom * $request->euCitizenCount;
        }
        if ($request->youthCount > 0) {
            $youthPriceComArr = json_decode($pricing->youthPriceCom, true);
            foreach ($maxPersonArr as $index => $item) {
                if ($request->youthCount >= $minPersonArr[$index] && $request->youthCount <= $item) {
                    $youthPriceCom = $youthPriceComArr[$index];
                }
            }
            $youth = ['category' => 'YOUTH', 'count' => $request->youthCount];
            array_push($bookingItems, $youth);
            $youthPrice = $youthPriceCom * $request->youthCount;
        }
        if ($request->childCount > 0) {
            $childPriceComArr = json_decode($pricing->childPriceComArr, true);
            foreach ($maxPersonArr as $index => $item) {
                if ($request->childCount >= $minPersonArr[$index] && $request->childCount <= $item) {
                    $childPriceCom = $childPriceComArr[$index];
                }
            }
            $child = ['category' => 'CHILD', 'count' => $request->childCount];
            array_push($bookingItems, $child);
            $childPrice = $childPriceCom * $request->childCount;
        }
        if ($request->infantCount > 0) {
            $infantPriceComArr = json_decode($pricing->infantPriceCom, true);
            foreach ($maxPersonArr as $index => $item) {
                if ($request->infantCount >= $minPersonArr[$index] && $request->infantCount <= $item) {
                    $infantPriceCom = $infantPriceComArr[$index];
                }
            }
            $infant = ['category' => 'INFANT', 'count' => $request->infantCount];
            array_push($bookingItems, $infant);
            $infantPrice = $infantPriceCom * $request->infantCount;
        }


        if ($request->bookingPrice) {
            $totalPrice = $request->bookingPrice;
        } else {
            $totalPrice = $adultPrice + $youthPrice + $childPrice + $infantPrice + $euCitizenPrice;
        }

        $totalTicketCount = $request->adultCount + $request->youthCount + $request->childCount + $request->infantCount + $request->euCitizenCount;
        $refCodeGenerator = new RefCodeGenerator();
        $reservationRefCode = $refCodeGenerator->refCodeGeneratorForCart();
        $t = [];
        $travelers = [
            'email' => $request->email,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'phoneNumber' => $request->phoneNumber
        ];

        array_push($t, $travelers);
        $booking = new Booking();
        $restaurant = null;
        if (is_null($option->rCodeID)) {
            $booking->status = 0;
        } else {
            $restaurant = Supplier::where('isRestaurant', 1)->where('id', $option->rCodeID)->first();
            $booking->status = $restaurant->isActive == 1 ? 4 : 0;
        }

        $BKNCode = $refCodeGenerator->refCodeGeneratorForBooking(null);
        $booking->productRefCode = $product->referenceCode;
        $booking->optionRefCode = $option->referenceCode;
        $booking->reservationRefCode = $product->referenceCode . '-' . $option->referenceCode . $reservationRefCode;
        $booking->bookingRefCode = $booking->reservationRefCode . $BKNCode;
        $booking->bookingItems = json_encode($bookingItems);
        $booking->dateTime = json_encode($dateTime);
        $booking->date = $request->bookingDate;
        $date = Carbon::createFromFormat('d/m/Y', $request->bookingDate);
        $asExpected = $date->format('Y-m-d');
        $booking->dateForSort = $asExpected;
        $booking->hour = json_encode($hour);
        $booking->language = 'tr';
        $booking->travelers = json_encode($t);
        $booking->fullName = $request->firstName . ' ' . $request->lastName;
        $booking->totalPrice = $totalPrice;
        $booking->companyID = is_null($request->companySelect) && auth()->guard('supplier')->check() ? auth()->guard('supplier')->user()->id : $request->companySelect;
        $booking->userID = -1;
        $avID = [];
        $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
        foreach ($bAvs as $bAv) {
            array_push($avID, $bAv->id);
        }
        $booking->avID = json_encode($avID);
        if ($booking->save()) {
            $invoice = new Invoice();
            $invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
            $invoice->paymentMethod = "MANUEL";
            $invoice->bookingID = $booking->id;
            $invoice->companyID = is_null($request->companySelect) && auth()->guard('supplier')->check() ? auth()->guard('supplier')->check() : $request->companySelect;
            $invoice->save();
            $booking->invoiceID = $invoice->id;
            $booking->save();

            //TODO: Make everything for ticket->dateRange.

            foreach ($availabilities as $ind => $av) {
                $hourArrSt[$ind] = [];
                $ticketsSt[$ind] = [];
                array_push($type, $av->availabilityType);
                $jsonq = new Jsonq();
                $hour = json_decode($booking->hour, true);
                $ticketHourlyDatabase = json_decode($av->hourly, true);
                $ticketDailyDatabase = json_decode($av->daily, true);
                $ticketDateRangeDatabase = json_decode($av->dateRange, true);
                $bookingDate = $request->bookingDate;
                if (count($ticketHourlyDatabase) > 0) {
                    foreach ($hour as $h) {
                        $res = $jsonq->json($av->hourly);
                        $result = $res->where('day', '=', $bookingDate)->where('hour', '=', $h['hour'])->get();
                        if (count($result) == 1) {
                            $key = key($result);
                            $ticketHourlyDatabase[$key]['ticket'] = $ticketHourlyDatabase[$key]['ticket'] - $totalTicketCount;
                            $av->hourly = json_encode($ticketHourlyDatabase);
                            $av->save();
                        }
                        $res->reset();
                    }
                }
                if (count($ticketDailyDatabase) > 0) {
                    $res = $jsonq->json($av->daily);
                    $result = $res->where('day', '=', $bookingDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $ticketDailyDatabase[$key]['ticket'] = $ticketDailyDatabase[$key]['ticket'] - $totalTicketCount;
                        $av->daily = json_encode($ticketDailyDatabase);
                        $av->save();
                    }
                    $res->reset();
                }
                if (count($ticketDateRangeDatabase) > 0) {
                    $res = $jsonq->json($av->daily);
                    $result = $res->where('dayFrom', '<=', $bookingDate)->where('dayTo', '>=', $bookingDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $ticketDateRangeDatabase[$key]['ticket'] = $ticketDateRangeDatabase[$key]['ticket'] - $totalTicketCount;
                        $av->dateRange = json_encode($ticketDateRangeDatabase);
                        $av->save();
                    }
                    $res->reset();
                }
            }

            if (is_null($option->rCodeID)) {
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'booking_id' => $booking->id,
                    'subject' => 'Booking is Successful!',
                    'options' => $option->title,
                    'date' => $booking->date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $BKNCode,
                    'name' => $request->firstName,
                    'surname' => $request->lastname,
                    'sendToCC' => true
                ]);
                $mail->to = $request->email;
                $mail->data = json_encode($data);
                $mail->blade = 'mail.booking-successful';
                $mail->save();

                if ($supplierID == -1) {
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'subject' => 'New Booking ! ' . $option->title,
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'name' => $request->firstName,
                        'surname' => $request->lastName,
                        'sendToCC' => true
                    ]);
                    $mail->to = Auth::guard('admin')->user()->email;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.booking-successful-for-creator';
                    $mail->save();

                } else {
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'subject' => 'New Booking ! ' . $option->title,
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'name' => $request->firstName,
                        'surname' => $request->lastName,
                        'sendToCC' => false
                    ]);
                    $mail->to = $supplier->email;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.booking-successful-for-creator';
                    $mail->save();

                }
            } else {
                if ($restaurant->isActive == 1) {
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'subject' => 'Booking is Pending',
                        'options' => $option->title,
                        'date' => $booking->date,
                        'BKNCode' => $BKNCode,
                        'name' => $request->firstName,
                        'surname' => $request->lastName,
                        'sendToCC' => false

                    ]);
                    $mail->to = $request->email;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.booking-pending';
                    $mail->save();

                    // Mail for company

                    if ($supplierID == -1) {
                        $mail = new Mails();
                        $data = [];
                        array_push($data, [
                            'subject' => 'New Pending Booking !' . $option->title,
                            'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                            'date' => $booking->date,
                            'hour' => json_decode($booking->hour, true)[0]['hour'],
                            'BKNCode' => $BKNCode,
                            'name' => $request->firstName,
                            'surname' => $request->lastName,
                            'sendToCC' => true
                        ]);
                        $mail->to = Auth::guard('admin')->user()->email;
                        $mail->data = json_encode($data);
                        $mail->blade = 'mail.booking-successful-for-creator';
                        $mail->save();

                    } else {
                        $mail = new Mails();
                        $data = [];
                        array_push($data, [
                            'subject' => 'New Booking ! ' . $option->title,
                            'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                            'options' => $option->title,
                            'date' => $booking->date,
                            'hour' => json_decode($booking->hour, true)[0]['hour'],
                            'BKNCode' => $BKNCode,
                            'name' => $request->firstName,
                            'surname' => $request->lastName,
                            'sendToCC' => false

                        ]);
                        $mail->to = $supplier->email;
                        $mail->data = json_encode($data);
                        $mail->blade = 'mail.booking-successful-for-creator';
                        $mail->save();
                    }

                    // Mail for restaurant

                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'subject' => 'R-Code is required!',
                        'BKNCode' => $BKNCode,
                        'hash' => $supplier->mailHash,
                        'refCode' => $option->referenceCode,
                        'sendToCC' => false
                    ]);
                    $mail->to = $restaurant->email;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.restaurant';
                    $mail->save();
                } else {
                    // Mail for customer
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'booking_id' => $booking->id,
                        'subject' => 'Booking is successful',
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'name' => $request->firstName,
                        'surname' => $request->lastName,
                        'sendToCC' => false
                    ]);
                    $mail->to = $request->email;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.booking-successful';
                    $mail->save();
                    // Mail for company
                    if ($supplierID == -1) {
                        $to = Auth::guard('admin')->user()->email;
                        $sendToCC = true;
                    } else {
                        $to = $supplier->email;
                        $sendToCC = false;
                    }

                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'subject' => 'New Booking ! ' . $option->title,
                        'options' => $option->title,
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'name' => $request->firstName,
                        'surname' => $request->lastName,
                        'sendToCC' => $sendToCC
                    ]);
                    $mail->to = $to;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.booking-successful-for-creator';
                    $mail->save();
                }
            }
            foreach ($ticketTypes as $ticketType) {
                if (!is_null($ticketType)) {
                    Barcode::where('isUsed', 0)->where('isReserved', 0)
                        ->where('ownerID', $supplierID)->where('ticketType', $ticketType->id)
                        ->take($totalTicketCount)->update(['bookingID' => $booking->id, 'isUsed' => 1, 'isReserved' => 1]);
                }
            }
            return redirect('/bookings');
        }
    }

    /**
     * Product select box content for manual booking.
     *
     * @param Request $request
     * @return array
     */
    public function productSelect(Request $request)
    {
        $product = Product::where('supplierID', '=', $request->companySelect)->where('isDraft', 0)->get();
        return ['product' => $product];
    }

    /**
     * Option select box content for manual booking.
     * @param Request $request
     * @return array
     */
    public function optionSelect(Request $request)
    {
        $product = Product::findOrFail($request->productSelect);
        $option = $product->options()->get();
        return ['option' => $option];
    }

    public function optionSelectMultiple(Request $request)
    {
        $products = $request->products;
        $options = [];
        foreach ($products as $product) {
            if ($product != -1) {
                $product = Product::findOrFail($product);
                $optionsProduct = $product->options()->get();
                foreach ($optionsProduct as $optionProduct)
                    array_push($options, $optionProduct);
            }
        }
        return ['options' => $options];
    }

    /**
     * Returns hours and ticket counts for manual booking
     *
     * @param Request $request
     * @return array
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function bookingTime(Request $request)
    {
        $option = Option::findOrFail($request->optionSelect);
        $availability = $option->avs()->get();
        $type = [];
        $hourArrSt = [];
        $ticketsSt = [];
        $hourArrOp = [];
        $ticketsOp = [];
        $isLimitless = [];
        $avNames = [];
        $bookingDate = $request->bookingDate;
        foreach ($availability as $ind => $av) {
            $hourArrSt[$ind] = [];
            $ticketsSt[$ind] = [];
            $hourArrOp[$ind] = [];
            $ticketsOp[$ind] = [];
            array_push($avNames, $av->name);
            array_push($type, $av->availabilityType);
            array_push($isLimitless, $av->isLimitless);
            $jsonq = new Jsonq();
            $ticketHourlyDatabase = json_decode($av->hourly);
            $ticketDailyDatabase = json_decode($av->daily);
            $ticketDateRangeDatabase = json_decode($av->dateRange);
            if (count($ticketHourlyDatabase) > 0) {
                $res = $jsonq->json($av->hourly);
                $result = $res->where('day', '=', $bookingDate)->get();
                foreach ($result as $r) {
                    $result = [];
                    array_push($result, $r);
                    $resHour = $r['hour'];
                    array_push($hourArrSt[$ind], $resHour);
                    $ticketCount = $r['ticket'];
                    array_push($ticketsSt[$ind], $ticketCount);
                }

                $res->reset();
            }
            if (count($ticketDailyDatabase) > 0) {
                $hourArrOp[$ind] = [];
                $ticketsOp[$ind] = [];
                $res = $jsonq->json($av->daily);
                $result = $res->where('day', '=', $bookingDate)->get();
                foreach ($result as $r) {
                    $resHour = $r['hourFrom'] . '-' . $r['hourTo'];
                    array_push($hourArrOp[$ind], $resHour);
                    $ticketCount = $r['ticket'];
                    array_push($ticketsOp[$ind], $ticketCount);
                }

                $res->reset();
            }
            if (count($ticketDateRangeDatabase) > 0) {
                $hourArrOp[$ind] = [];
                $ticketsOp[$ind] = [];
                $jsonq->macro('dateGte', function ($val, $comp) {
                    return $this->dateComparison($val, $comp, 'dateGte');
                });

                $jsonq->macro('dateLte', function ($val, $comp) {
                    return $this->dateComparison($val, $comp, 'dateLte');
                });
                $res = $jsonq->json($av->dateRange);
                $result = $res->where('dayFrom', 'dateLte', $bookingDate)->where('dayTo', 'dateGte', $bookingDate)->get();
                foreach ($result as $r) {
                    $resHour = '00:00 - 23:59';
                    array_push($hourArrOp[$ind], $resHour);
                    $ticketCount = $r['ticket'];
                    array_push($ticketsOp[$ind], $ticketCount);
                }

                $res->reset();
            }
        }

        return [
            'type' => $type,
            'ticketsSt' => $ticketsSt,
            'hourArrSt' => $hourArrSt,
            'availabilities' => $availability,
            'ticketsOp' => $ticketsOp,
            'hourArrOp' => $hourArrOp,
            'result' => null,
            'isLimitless' => $isLimitless,
            'avNames' => $avNames
        ];
    }

    /**
     * Date comparison macro for Jsonq package. We have this on another controller. So it can be deleted.
     *
     * @param $val
     * @param $comp
     * @param $type
     * @return bool
     */
    public function dateComparison($val, $comp, $type)
    {
        $date_split = explode('/', $val);
        $date_format = $date_split[2] . '-' . $date_split[1] . '-' . $date_split[0];
        $comp_split = explode('/', $comp);
        $comp_format = $comp_split[2] . '-' . $comp_split[1] . '-' . $comp_split[0];
        $comp = strtotime($comp_format);
        $date = strtotime($date_format);
        return $type == 'dateGte' ? $comp <= $date : $comp >= $date;
    }

    /**
     * Changes booking status
     *
     * @param Request $request
     * @return array
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function changeStatus(Request $request)
    {
        $status = $request->status;
        $booking = Booking::findOrFail($request->id);
        $options = Option::where('referenceCode', '=', $booking->optionRefCode)->get();
        $BKNCode = ($booking->isBokun == 1 || $booking->isViator == 1) ? $booking->bookingRefCode : explode('-', $booking->bookingRefCode)[2];
        $date = date('D, d-F-Y', strtotime(json_decode($booking->dateTime, true)[0]['dateTime']));
        $oldStatus = $booking->status;
        $booking->status = $status;
        $bookingItems = json_decode($booking->bookingItems, true);
        $totalTicketCount = 0;
        $travelers = json_decode($booking->travelers, true);
        $referenceCode = $booking->gygBookingReference ?? (($booking->isBokun == 1 || $booking->isViator == 1) ? $booking->bookingRefCode : (explode('-', $booking->bookingRefCode)[count(explode('-', $booking->bookingRefCode))-1]));
        $user = Auth::guard('admin')->user();
        $logDetail = $user->name . ' '.$referenceCode.' reference code of Booking Status Changed';
        $action = 'Changed Status';
        if ($oldStatus != $status) {
            if ($booking->save()) {
                if ($status == 4) {
                    $subjectForCustomer = 'Booking is pending!';
                    $subjectForCompany = $travelers[0]['firstName'] . ' ' . $travelers[0]['lastName'] . "'s booking is pending! $BKNCode";
                    $bladeForCustomer = 'mail.booking-pending';
                    $bladeForCompany = 'mail.booking-pending-for-creator';
                } elseif ($status == 3) {
                    // If booking has bigBusRefCode send cancelBooking request to Big Bus API, else resume normal operations
                    if (!is_null($booking->bigBusRefCode)) {

                        $bigBus = $this->bigBusRelated
                            ->setClient();

                        $bigBusResponse = $bigBus->delete($booking->bigBusRefCode, ['reason' => $request->cancelReason]);

                        if (!$bigBusResponse['status']) {
                            return ['error' => 'Error'];
                        }

                        $bigBus->setLog($booking->id, 'cancel', $bigBusResponse['data']);
                    }


                    // if booking has tootbus send cancel booking request to tootbus else continue normal operations (start)
                    if ($booking->is_tootbus == 1) {


                        $data = [];
                        $data["reason"] = "Cancel Booking";


                        $reserveResponse = $this->tootbusRelated->delete(json_decode($booking->tootbus_booking_response, true)["data"]["uuid"], $data);

                        if ($reserveResponse["status"] === false) {
                            $booking->tootbus_booking_response = json_encode(["type" => "error cancel Booking", "data" => $reserveResponse["message"]]);
                            return ['error' => 'Error'];
                        }

                        $booking->tootbus_booking_response = json_encode(["type" => "cancel booking", "data" => json_decode($reserveResponse["message"], true)]);


                    }
                    // if booking has tootbus send cancel booking request to tootbus else continue normal operations (end)

                    $subjectForCustomer = 'Booking is canceled!';
                    $subjectForCompany = $travelers[0]['firstName'] . ' ' . $travelers[0]['lastName'] . "'s booking is canceled! $BKNCode";
                    $bladeForCustomer = 'mail.booking-cancel';
                    $bladeForCompany = 'mail.booking-cancel-for-creator';

                } elseif ($status == 0) {
                    $subjectForCustomer = 'Booking is successful!';
                    $subjectForCompany = $travelers[0]['firstName'] . ' ' . $travelers[0]['lastName'] . "'s booking is successful! $BKNCode";
                    $bladeForCustomer = 'mail.booking-successful';
                    $bladeForCompany = 'mail.booking-successful-for-creator';
                }

                $dataForCustomerArray = [];
                array_push($dataForCustomerArray, ['sendToCC' => false, 'BKNCode' => $BKNCode, 'options' => $options[0]->title, 'date' => $date, 'hour' => json_decode($booking->hour, true)[0]['hour'], 'subject' => $subjectForCustomer, 'name' => $travelers[0]['firstName'], 'surname' => $travelers[0]['lastName']]);
                $mailForCustomer = $travelers[0]['email'];
                if ($booking->companyID == -1) {
                    $sendToCC = true;
                    //$mailForCompany = Auth::guard('admin')->user()->email;
                    $mailForCompany = "contact@parisviptrips.com";
                    $dataForCompanyArray = [];
                    array_push($dataForCompanyArray, ['sendToCC' => $sendToCC, 'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems), 'hour' => json_decode($booking->hour, true)[0]['hour'], 'BKNCode' => $BKNCode, 'options' => $options[0]->title, 'date' => $date, 'subject' => $subjectForCompany, 'name' => $travelers[0]['firstName'], 'surname' => $travelers[0]['lastName']]);
                } else {
                    $sendToCC = false;
                    $mailForCompany = Supplier::findOrFail($booking->companyID)->first()->email;
                    $dataForCompanyArray = [];
                    array_push($dataForCompanyArray, ['sendToCC' => $sendToCC, 'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems), 'hour' => json_decode($booking->hour, true)[0]['hour'], 'BKNCode' => $BKNCode, 'options' => $options[0]->title, 'date' => $date, 'subject' => $subjectForCompany, 'name' => $travelers[0]['firstName'], 'surname' => $travelers[0]['lastName']]);

                    $mail = new Mails();
                    $mail->to = "contact@parisviptrips.com";
                    $mail->data = json_encode($dataForCustomerArray);
                    $mail->blade = $bladeForCompany;
                    $mail->save();

                }

                $logDetail .= ', status changed '. $oldStatus . ' to '. $status . " ({$subjectForCustomer})";

                $adminLog = new AdminLog();
                $adminLog->userID = $user->id;
                $adminLog->page = 'All Bookings';
                $adminLog->url = $request->fullUrl();
                $adminLog->action = $action;
                $adminLog->details = $logDetail;
                $adminLog->productRefCode = $booking->productRefCode;
                $adminLog->optionRefCode = $booking->optionRefCode;
                $adminLog->tableName = 'bookings';
                $adminLog->columnName = 'bookings.*, status';
                $adminLog->save();

                $mail = new Mails();
                $mail->to = $mailForCustomer;
                $mail->data = json_encode($dataForCustomerArray);
                $mail->blade = $bladeForCustomer;
                $mail->save();
                $mail = new Mails();
                $mail->to = $mailForCompany;
                $mail->data = json_encode($dataForCompanyArray);
                $mail->blade = $bladeForCompany;
                $mail->save();
            }

            $count = [];
            foreach ($bookingItems as $b) {
                $totalTicketCount += $b['count'];
            }
            $ticket = null;
            if ($status == 3) {
                $option = Option::with('bigBus')->where('referenceCode', '=', $booking->optionRefCode)->first();
                $supplierID = $option->supplierID;
                $availability = $option->avs;
                $ticketTypes = [];
                foreach ($availability as $ind => $av) {
                    array_push($ticketTypes, $av->ticketType()->first());
                    $hourArrSt[$ind] = [];
                    $ticketsSt[$ind] = [];
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $hour = json_decode($booking->hour, true);
                    $ticketHourlyDatabase = json_decode($av->hourly, true);
                    $ticketDailyDatabase = json_decode($av->daily, true);
                    $ticketDateRangeDatabase = json_decode($av->dateRange, true);
                    $ticketBarcodeDatabase = json_decode($av->barcode, true);
                    $bookingDate = $booking->date;
                    if (count($ticketHourlyDatabase) > 0) {
                        foreach ($hour as $h) {
                            $res = $jsonq->json($av->hourly);
                            $resultHourly = $res->where('day', '=', $bookingDate)->where('hour', '=', $h['hour'])->get();
                            if (array_key_exists(key($resultHourly), json_decode($res->toJson(), true))) {
                                $key = array_keys(json_decode($res->toJson(), true))[0];
                                $ticketHourlyDatabase[$key]['ticket'] += $totalTicketCount;
                                $ticketHourlyDatabase[$key]['sold'] -= $totalTicketCount;
                                $av->hourly = json_encode($ticketHourlyDatabase);
                                $av->save();
                                $count = $ticketHourlyDatabase[$key]['ticket'];
                            }
                            $res->reset();
                        }
                    }
                    if (count($ticketDailyDatabase) > 0) {
                        $res = $jsonq->json($av->daily);
                        $resultDaily = $res->where('day', '=', $bookingDate)->get();
                        if (array_key_exists(key($resultDaily), json_decode($res->toJson(), true))) {
                            $key = array_keys(json_decode($res->toJson(), true))[0];
                            $ticketDailyDatabase[$key]['ticket'] += $totalTicketCount;
                            $ticketDailyDatabase[$key]['sold'] -= $totalTicketCount;
                            $av->daily = json_encode($ticketDailyDatabase);
                            $av->save();
                            $count = $ticketDailyDatabase[$key]['ticket'];
                        }
                        $res->reset();
                    }
                    if (count($ticketDateRangeDatabase) > 0) {
                        $res = $jsonq->json($av->dateRange);
                        $resultRange = $res->where('dayFrom', 'dateLte', $bookingDate)
                            ->where('dayTo', 'dateGte', $bookingDate)
                            ->get();
                        if (array_key_exists(key($resultRange), $resultRange)) {
                            $key = key($resultRange);
                            $ticketDateRangeDatabase[$key]['ticket'] += $totalTicketCount;
                            $ticketDateRangeDatabase[$key]['sold'] -= $totalTicketCount;
                            $av->dateRange = json_encode($ticketDateRangeDatabase);
                            $av->save();
                            $count = $ticketDateRangeDatabase[$key]['ticket'];
                        }
                        $res->reset();
                    }
                    if (count($ticketBarcodeDatabase) > 0) {
                        $res = $jsonq->json($av->barcode);
                        $resultBarcode = $res->where('dayFrom', 'dateLte', $bookingDate)
                            ->where('dayTo', 'dateGte', $bookingDate)
                            ->get();
                        if (count($resultBarcode) == 0) {
                            $key = key($resultBarcode);
                            $ticketBarcodeDatabase[$key]['ticket'] += $totalTicketCount;
                            $ticketBarcodeDatabase[$key]['sold'] -= $totalTicketCount;
                            $av->barcode = json_encode($ticketBarcodeDatabase);
                            $av->save();
                            $count = $ticketBarcodeDatabase[$key]['ticket'];
                        }
                        $res->reset();
                    }
                }

                if (!is_null($option->bigBus)) {
                    Barcode::where('bookingID', $booking->id)->delete();
                }

                foreach ($ticketTypes as $ticketType) {
                    if (!is_null($ticketType)) {

                        /*        $barcodes =  Barcode::where('isUsed', 1)->where('isReserved', 1)
                                     ->where('ownerID', $supplierID)->where('ticketType', $ticketType->id)->where('bookingID', $booking->id)
                                     ->take($totalTicketCount)->get();


                                     foreach($barcodes as $barcode){

                                         $oldBookingID = $barcode->bookingID;
                                         $cancelReason = $request->cancelReason ?? 'Cancellation Request';
                                         $cancelBy = auth()->guard('admin')->user()->email;
                                         $cancelDate = Carbon::now()->format('d/m/Y H:i');
                                         $pastLogs = is_null($barcode->pastLog) ? [] : json_decode($barcode->pastLog, true);

                                         $pastLogs[] = [

                                             "oldBookingID" => $oldBookingID,
                                             "cancelReason" => $cancelReason,
                                             "cancelBy" => $cancelBy,
                                             "cancelDate" => $cancelDate


                                     ];






                                         $barcode->cartID = null;
                                         $barcode->bookingID = null;
                                         $barcode->isUsed = 0;
                                         $barcode->isReserved = 0;
                                         $barcode->log = json_encode($pastLogs);
                                         $barcode->save();



                                     }*/

                        $targetOne = Barcode::where('isUsed', 1)->where('isReserved', 1)
                            ->where('ownerID', $supplierID)->where('ticketType', $ticketType->id)->where('bookingID', $booking->id)->get();

                        $cancelReason = $request->cancelReason;
                        $cancelBy = auth()->guard('admin')->user()->email;
                        $cancelDate = Carbon::now()->format('d/m/Y-H:i');


                        if (!empty($targetOne)) {


//                            $pastLogs = is_null($targetOne->log) ? [] : json_decode($targetOne->log, true);

                            $pastLogs[] = [

                                "oldBookingID" => $booking->id,
                                "cancelReason" => $cancelReason,
                                "cancelBy" => $cancelBy,
                                "cancelDate" => $cancelDate


                            ];

                            $insertLog = json_encode($pastLogs);

                        } else {
                            $insertLog = json_encode([

                                "oldBookingID" => $booking->id,
                                "cancelReason" => $cancelReason,
                                "cancelBy" => $cancelBy,
                                "cancelDate" => $cancelDate


                            ]);
                        }

                        if($ticketType->id == 30) {
                            Barcode::where('isUsed', 1)->where('isReserved', 1)
                                ->where('ownerID', $supplierID)->where(function($q) {
                                    $q->where('ticketType', 30)->orWhere('ticketType', 31);
                                })->where('bookingID', $booking->id)
                                ->take($totalTicketCount)->update(['cartID' => null, 'bookingID' => null, 'isUsed' => 0, 'isReserved' => 0, "log" => $insertLog]);
                        } else {
                            Barcode::where('isUsed', 1)->where('isReserved', 1)
                                ->where('ownerID', $supplierID)->where('ticketType', $ticketType->id)->where('bookingID', $booking->id)
                                ->take($totalTicketCount)->update(['cartID' => null, 'bookingID' => null, 'isUsed' => 0, 'isReserved' => 0, "log" => $insertLog]);
                        }
                    }
                }
            }

            return ['success' => 'Successful', 'status' => $status, 'ticket' => $ticket, 'count' => $count, 'message' => 'Status changed successfully'];
        }
    }

    /**
     * Stores r code (restaurant code)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveRCode(Request $request)
    {
        $booking = Booking::findOrFail($request->get('bookingId'));
        $rCode = null;

        if (is_null($booking->rCodeID)) {
            $rCode = new Rcode();
        } else {
            $rCode = Rcode::findOrFail($booking->rCodeID);
        }
        $rCode->rCode = $request->get('rCode');
        if ($rCode->save()) {
            $booking = Booking::findOrFail($request->get('bookingId'));
            $booking->rCodeID = $rCode->id;

            if ($booking->save()) {
                return response()->json(['success' => 'R-Code is saved successfully!']);
            } else {
                return response()->json(['error' => 'An error is occured while saving R-Code! Please consult administration.']);
            }
        } else {
            return response()->json(['error' => 'An error is occured while saving R-Code! Please consult administration.']);
        }
    }

    /**
     * Stores r code as restaurant
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveRCodeAsRestaurant(Request $request)
    {
        $hash = explode('/', $request->hash)[1];
        if ($this->isRestaurantValid($hash)) {
            $booking = Booking::findOrFail($request->get('bookingId'));
            $rCode = null;
            if (is_null($booking->rCodeID)) {
                $rCode = new Rcode();
            } else {
                $rCode = Rcode::findOrFail($booking->rCodeID);
            }
            $rCode->rCode = $request->get('rCode');
            if ($rCode->save()) {
                $booking = Booking::findOrFail($request->get('bookingId'));
                $booking->rCodeID = $rCode->id;
                if ($booking->status == 4) {
                    $booking->status = 0;
                }
                $option = Option::where('referenceCode', $booking->optionRefCode)->first();
                if ($booking->save()) {
                    $travelers = json_decode($booking->travelers, true);
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => explode('-', $booking->bookingRefCode)[3],
                        'subject' => 'Booking is Approved!',
                        'name' => $travelers[0]['firstName'],
                        'surname' => $travelers[0]['lastName'],
                        'sendToCC' => false
                    ]);
                    $mail->data = json_encode($data);
                    $mail->to = $travelers[0]['email'];
                    $mail->blade = 'mail.booking-successful';
                    $mail->save();

                    $mailForSupplier = new Mails();
                    $data = [];
                    array_push($data,
                        [
                            'options' => $option->title,
                            'date' => $booking->date,
                            'hour' => json_decode($booking->hour, true)[0]['hour'],
                            'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                            'BKNCode' => explode('-', $booking->bookingRefCode)[3],
                            'subject' => 'Booking is Approved!',
                            'name' => $travelers[0]['firstName'],
                            'surname' => $travelers[0]['lastName'],
                            'sendToCC' => $booking->companyID == -1
                        ]
                    );
                    $mailForSupplier->data = json_encode($data);
                    $mailForSupplier->to = $booking->companyID == -1 ? 'contact@parisviptrips.com' : Supplier::findOrFail($booking->companyID)->email;
                    $mailForSupplier->blade = 'mail.booking-successful-for-creator';
                    $mailForSupplier->save();
                    return response()->json(['success' => 'R-Code is saved successfully!']);
                } else {
                    return response()->json(['error' => 'An error is occured while saving R-Code! Please consult administration.']);
                }
            } else {
                return response()->json(['error' => 'An error is occured while saving R-Code! Please consult administration.']);
            }
        }
        return response()->json(['error' => 'An error is occured while saving R-Code! Please consult administration.']);
    }

    /**
     * Checks if restaurant is valid for supplier
     *
     * @param $hash
     * @return bool
     */
    public function isRestaurantValid($hash)
    {
        $suppliers = Supplier::where('isRestaurant', 1)->get();
        $validationArr = [];
        foreach ($suppliers as $supplier) {
            $hashStr = $supplier->mailHash;
            if ($hash == $hashStr) {
                array_push($validationArr, true);
            } else {
                array_push($validationArr, false);
            }
        }
        return in_array(true, $validationArr);
    }

    /**
     * Returns a view page for bookings of restaurant
     *
     * @param $refCode
     * @param $hash
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookingsRestaurant($refCode, $hash)
    {
        $option = Option::where('referenceCode', $refCode)->first();
        $suppliers = Supplier::where('isRestaurant', 1)->where('id', $option->rCodeID)->get();
        foreach ($suppliers as $supplier) {
            $md5emailFromDB = $supplier->mailHash;
            if ($md5emailFromDB == $hash) {
                $createdBy = is_null($supplier->createdBy) || $supplier->createdBy == -1 ? -1 : $supplier->createdBy;
                $bookings = Booking::where('companyID', '=', $createdBy)->where('status', 4)->where('optionRefCode', $refCode)->get();
                $options = Option::all();
                $invoices = Invoice::all();
                return view('panel.bookings.index-for-restaurant', ['invoices' => $invoices, 'bookings' => $bookings, 'options' => $options]);
            }
        }
    }

    /**
     * View for adding comment to a booking
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addComment($id)
    {
        return view('panel.bookings.comment',
            [
                'id' => $id
            ]
        );
    }

    /**
     * Stores comment for bookings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeComment(Request $request)
    {
        $booking = Booking::findOrFail($request->bookingID);
        $booking->adminComment = $request->bookingComment;
        if ($booking->save()) {
            $option = Option::where('referenceCode', $booking->optionRefCode)->first();
            $travelers = json_decode($booking->travelers, true);
            $mail = new Mails();
            $data = [];
            array_push($data, [
                'options' => $option->title,
                'date' => $booking->date,
                'hour' => json_decode($booking->hour, true)[0]['hour'],
                'BKNCode' => $booking->bookingRefCode,
                'subject' => 'Booking is successful!',
                'name' => $travelers[0]['firstName'],
                'surname' => $travelers[0]['lastName'],
                'sendToCC' => false
            ]);
            $mail->data = json_encode($data);
            $mail->to = $travelers[0]['email'];
            $mail->blade = 'mail.booking-successful';
            $mail->save();

            return redirect('/bookings');
        }
    }

    /**
     * View for special reference code for bookings
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function specialRefCode($id)
    {
        $booking = Booking::findOrFail($id);
        return view('panel.bookings.specialrefcode',
            [
                'booking' => $booking
            ]
        );
    }

    /**
     * Stores special reference code for bookings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeSpecialRefCode(Request $request)
    {
        $booking = Booking::findOrFail($request->bookingID);
        $booking->specialRefCode = $request->specialRefCode;
        $booking->save();

        //return redirect('/bookings');
        return redirect('/close-window');
    }

    public function mailInformation()
    {
        $bookings = Booking::all();
        $mails = Mails::where('blade', 'mail.booking-reminder')->where('to', '!=', 'contact@parisviptrips.com')->orderBy('updated_at', 'DESC')->get();
        return view('panel.bookings.mail-information', ['bookings' => $bookings, 'mails' => $mails]);
    }

    public function mails() {
        return view('panel.mails');
    }

    public function boookings_extra_file_imports(Request $request)
    {
        $base_url_for_guide_image = "https://cityzore.s3.eu-central-1.amazonaws.com/booking-files/";
        $oMerger = PDFMerger::init();

        if ($request->hasFile('file')) {

            $files = $request->file;
            $sayac = 0;
            $pdfCounter = 0;
            $baseNameForSinglePDF = "";
            foreach ($files as $file) {
                if (!($file->getMimeType() == "image/jpeg" || $file->getMimeType() == "image/png" || $file->getMimeType() == "application/pdf" || $file->getMimeType() == "application/vnd.ms-excel"))
                    return response()->json(["status" => "error", "message" => "one or more selected files have unsupported extensions"]);

                if($file->getMimeType() == "application/pdf") {
                    $handle = fopen($file, "r");
                    $contents = fread($handle, filesize($file));
                    fclose($handle);
                }

                if($file->getMimeType() == "application/pdf" && !stristr($contents, "/Encrypt")) {
                    $oMerger->addPDF($file, 'all');
                    $pdfCounter++;

                    $baseNameForSinglePDF = $request->filename[$sayac];
                } else {

                $fileName = $file->getClientOriginalName();
                $fileName = str_replace(' ', '-', $fileName); // Replaces all spaces with hyphens.

                $fileName = preg_replace('/[^A-Za-z0-9_.\-]/', '', $fileName); // Removes special chars.

                $fileName = str_random(32) . "_" . $fileName;
                $s3 = Storage::disk('s3');
                $filePath = '/booking-files/' . $fileName;
                $stream = fopen($file->getRealPath(), 'r+');
                $s3->put($filePath, $stream);

                $booking_image = new BookingImage();
                $booking_image->booking_id = $request->booking_id;
                $booking_image->image_name = $base_url_for_guide_image . $fileName;
                $booking_image->image_base_name = $request->filename[$sayac];
                $booking_image->save();

                }

                $sayac++;
            }

            if($pdfCounter > 0) {
                $curr = Carbon::now()->timestamp;
                $fileName = $pdfCounter == 1 ? (str_random(32) . $baseNameForSinglePDF . '.pdf') : (str_random(32) . '_Merged-Document-' . $curr . '.pdf');
                $fileName = str_replace(' ', '-', $fileName); // Replaces all spaces with hyphens.
                $fileName = preg_replace('/[^A-Za-z0-9_.\-]/', '', $fileName); // Removes special chars.

                $oMerger->merge();
                $oMerger->save($fileName);

                $s3 = Storage::disk('s3');
                $filePath = '/booking-files/' . $fileName;
                $stream = fopen(base_path().'/public/'.$fileName, 'r+');
                $s3->put($filePath, $stream);

                $booking_image = new BookingImage();
                $booking_image->booking_id = $request->booking_id;
                $booking_image->image_name = $base_url_for_guide_image . $fileName;
                $booking_image->image_base_name = $pdfCounter == 1 ? ($baseNameForSinglePDF) : ('Merged-Document-' . $curr);
                $booking_image->save();

                unlink(base_path().'/public/'.$fileName);
            }


            $now = Carbon::now();
            $booking = Booking::findOrFail($request->booking_id);


            $bookingDate = Carbon::createFromFormat("Y-m-d H:i:s", $booking->dateForSort);
            if (($bookingDate->diffInHours($now) < 24 && ($now->timestamp < $bookingDate->timestamp))) {


                $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();

                if (!is_null($option->customer_mail_templates) && !empty(json_decode($option->customer_mail_templates, true)["en"])) {
                    $traveler = json_decode($booking->travelers, true)[0];

                    if (strpos($booking->dateTime, "dateTime") === false) {
                        $meetingDateTime = Carbon::parse($booking->dateTime)->format("d/m/Y H:i:s");
                    } else {
                        $meetingDateTime = $booking->date . " " . json_decode($booking->hour, true)[0]["hour"];
                    }


                    $mailTemplate = json_decode($option->customer_mail_templates, true)["en"];
                    $mailTemplate = str_replace("#NAME SURNAME#", $traveler["firstName"] . " " . $traveler['lastName'], $mailTemplate);
                    $mailTemplate = str_replace("#SENDER#", "Paris Business & Travel", $mailTemplate);
                    $mailTemplate = str_replace("#DATE#", $meetingDateTime, $mailTemplate);


                    $mailTemplate = nl2br($mailTemplate);


                    $mail = new Mails();
                    $mail->bookingID = $booking->id;

                    $data = [];
                    array_push($data,
                        [
                            'dateForSort' => $booking->dateForSort,
                            'options' => $option->title,
                            'date' => $booking->date,
                            'hour' => !empty(json_decode($booking->hour, true)[0]['hour']) ? json_decode($booking->hour, true)[0]['hour'] : null,
                            'subject' => 'Upcoming Booking Announcements',
                            'name' => $traveler['firstName'],
                            'surname' => $traveler['lastName'],
                            'sendToCC' => false,
                            'template' => $mailTemplate,
                            'booking_id' => $booking->id

                        ]
                    );
                    $mail->data = json_encode($data);
                    $mail->to = $traveler['email'];
                    //$mail->to = 'suha31416@gmail.com';
                    $mail->status = 0;
                    $mail->blade = 'mail.booking-reminder';


                    $mail->save();

                }


            }

        }


        return response()->json(["status" => "success", "message" => "Selected Files Imported Successfully!"]);


    }


    protected function convertPersontoCorrectFormat($persons)
    {

        $arr = [];
        foreach ($persons as $category => $count) {
            $arr[$category] = (int)$count;
        }
        return json_encode($arr);
    }


    public function ajax(Request $request)
    {
        switch ($request->action) {


            case 'insert_layout_for_access_checkins_modal':


                $booking = Booking::findOrFail($request->booking_id);


                if (auth()->guard("supplier")->check()) {
                    $ownerID = auth()->guard("supplier")->user()->id;
                    if ($booking->companyID !== $ownerID && $booking->companyID != -1) {
                        return false;
                    }
                }


                $view = view("panel-partials.ajax-pages.bookings.booking-index-modal-for-access-checkins", compact('booking'))->render();
                return response()->json(['view' => $view]);

                break;


            case 'insert_access_checkins_form_data':


                $targetBooking = Booking::where('status', 0)->get()->filter(function ($model) use ($request) {

                    //$parts = explode('-', $model->bookingRefCode);
                    //$lastOne = $parts[count($parts)-1];

                    $bookingRefCode = $model->bookingRefCode;
                    $requestRefCode=$request->refCode;
                    if(str_contains($requestRefCode,'-')) $requestRefCode=str_replace('-','',$requestRefCode);
                    if(substr($requestRefCode,0,2)=="BR") $requestRefCode=substr($requestRefCode,0,2).'-'.substr($requestRefCode,2);

                    if ((($model->gygBookingReference == $requestRefCode) || ($model->bookingRefCode == $requestRefCode) || preg_match("/" . $requestRefCode . "$/", $bookingRefCode))) {
                        if ((strpos($requestRefCode, "BKN") === false && strpos($requestRefCode, "BR-") === false && strpos($requestRefCode, "PAR-") === false && strpos($requestRefCode, "BOKUN") === false && strpos($requestRefCode, "GYG") === false)) {
                            return false;
                        }
                        return true;
                    }
                });

                if ($targetBooking->count() == 0) return response()->json(['status' => 'error', 'message' => 'undefined Booking RefCode']);


                $booking = Booking::findOrFail($request->booking_id);


                if ($targetBooking->first()->id !== $booking->id) {
                    return response()->json(['status' => 'error', 'message' => 'Access Denied!']);
                }

                if (auth()->guard("supplier")->check()) {
                    $user = auth()->guard("supplier")->user();
                    $ownerID = $user->id;
                    $ownerEmail = $user->email;
                    if ($booking->companyID !== $ownerID && $booking->companyID != -1) {
                        return response()->json(["status" => "error", "message" => "Access Denied!"]);
                    }
                } else {
                    $user = auth()->guard("admin")->user();
                    $ownerID = $user->id;
                    $ownerEmail = $user->email;
                }


                $bookingItems = json_decode($booking->bookingItems, true);
                $pricing = $booking->bookingOption->pricings()->first();
                $ignoredCategories = $pricing->ignoredCategories ? json_decode($pricing->ignoredCategories, true) : [];
                $ignoredCategories = array_map('strtoupper', $ignoredCategories);
                $totalTicket = 0;

                foreach ($request->category as $key => $value) {
                    $totalTicket += (int)$value;
                }
                if ($totalTicket <= 0) {
                    return response()->json(["status" => "error", "message" => "total number of tickets cannot be equal to zero or cannot < zero"]);
                }

                if ($booking->check()->count()) {

                    $booking->check()->delete();

                }

                $result = $user->checkins()->create([
                    'booking_id' => $request->booking_id,
                    'email' => $ownerEmail,
                    'role' => 'Others',
                    'person' => $this->convertPersontoCorrectFormat($request->category),
                    'ticket' => $totalTicket,
                    'status' => 1


                ]);


                if ($result) {
                    $voucher_url = "https://www.cityzore.com/print-pdf-frontend/" . $this->cryptRelated->encrypt($booking->id);
                    return response()->json(["status" => "success", "message" => "Data Changed Successfully!", "voucher_url" => $voucher_url, "ref_code" => $request->refCode]);
                }

                return response()->json(["status" => "error", "message" => "An Error Occurred!"]);

            case 'turn_invoice_check':

                $booking_id = $request->booking_id;
                $booking = Booking::findOrFail($booking_id);

                if (is_null($booking->invoice_check) || $booking->invoice_check == 0) {
                    $booking->invoice_check = 1;
                } else {
                    $booking->invoice_check = 0;
                }
                if ($booking->save()) {
                    return response()->json(["status" => "success", "invoice_status" => $booking->invoice_check]);
                }
                return response(["status" => "error", "message" => "An Error Occurred!"]);


                break;

            case 'change_customer_template_message_language':
                $booking = Booking::findOrFail($request->booking_id);
                $customerFullName = $booking->fullName;
                $myName = auth()->guard("admin")->user()->name;
                $lang = $request->lang;


                $customer_mail_templates = $booking->bookingOption->customer_mail_templates ? json_decode($booking->bookingOption->customer_mail_templates, true) : [];
                $customer_whatsapp_templates = $booking->bookingOption->customer_whatsapp_templates ? json_decode($booking->bookingOption->customer_whatsapp_templates, true) : [];

                $messageForMail = '';
                if (!empty($customer_mail_templates[$lang])) {
                    $message = $customer_mail_templates[$lang];

                    if (strpos($message, "#NAME SURNAME#") !== false) {
                        $message = str_replace("#NAME SURNAME#", $customerFullName, $message);
                    }

                    if (strpos($message, "#SENDER#") !== false) {
                        $message = str_replace("#SENDER#", $myName, $message);
                    }

                    $messageForMail = $message;
                }

                $message = '';
                if (!empty($customer_whatsapp_templates[$lang])) {
                    $message = $customer_whatsapp_templates[$lang];

                    if (strpos($message, "#NAME SURNAME#") !== false) {
                        $message = str_replace("#NAME SURNAME#", $customerFullName, $message);
                    }

                    if (strpos($message, "#SENDER#") !== false) {
                        $message = str_replace("#SENDER#", $myName, $message);
                    }
                }
                $bookingExtraFiles = Booking::findOrFail($request->booking_id)->extra_files;
                $message .= "\n\n";
                if ($bookingExtraFiles->count()) {
                    $sayac = 1;
                    foreach ($bookingExtraFiles as $file) {
                        $message .= "\n\n" . $file->image_base_name . ": " . $file->image_name . "";
                        $sayac++;
                    }
                }

                return response()->json(["status" => "success", "message" => $message, 'messageForMail' => $messageForMail]);

                break;

            case 'insert_layout_for_customer-contact_modal':
                $booking = Booking::findOrFail($request->booking_id);


                $view = view("panel-partials.ajax-pages.bookings.booking-index-modal-for-customer-contact", compact('booking'))->render();
                return response()->json(['view' => $view]);


                break;

            case 'booking-customer-contact-send-mail-operation':
                $code=md5(rand());
                $data = [];
                $data["booking_id"] = $request->booking_id;
                $data["mail_to"] = $request->mail_to;
                $data["mail_message"] = $request->mail_message;
                $data["mail_title"] = $request->mail_title;
                $data["mail_code"] = $code;

                $response = $this->mailOperations->sendMailForBookingContacts($data, "mail.booking_information_for_customer", $data["mail_to"]);

                if ($response) {
                    return response()->json(["status" => "success", "message" => "Mail Has Been Sent Successfully!"]);
                }
                return response()->json(["error" => "success", "message" => "something Wrong!"]);


                break;

            case 'insert_layout_for_extra_booking_files_modal':
                $booking = Booking::findOrFail($request->booking_id);


                $view = view("panel-partials.ajax-pages.bookings.booking-index-modal-for-extra-file-imports", compact('booking'))->render();
                return response()->json(['view' => $view]);


                break;

            case 'insert_layout_for_invoice_number_modal':
                $booking = Booking::findOrFail($request->booking_id);

                $view = view("panel-partials.ajax-pages.bookings.booking-index-modal-for-invoice-number-imports", compact('booking'))->render();
                return response()->json(['view' => $view]);

            case 'insert_layout_for_invoice_number_modal_new_file':
                $booking_id = $request->booking_id;
                $user_id = Auth::user()->id;
                $booking = Booking::findOrFail($booking_id);

                if(BookingInvoice::where('src',$request->fileName)->exists()) return response()->json(['error' => "This File Added Before. You Should Change Name Then Upload File"]);


                $data = $request->raw_file;
                $image_array_1 = explode(";", $data);
                $image_array_2 = explode(",", $image_array_1[1]);

                $data = base64_decode($image_array_2[1]);
                $fileName_raw = $request->fileName;
                $slug = $fileName_raw;
                $putResult = Storage::disk('s3')->put('invoices/' . $slug, $data);
                if ($putResult) {
                    $dbResult = BookingInvoice::insert([
                        'booking_id' => $booking_id,
                        'invoice_number' => null,
                        'src' => $slug,
                        'type' => $request->fileType,
                        'user_id' => $user_id,
                        'status' => 1,
                    ]);
                    if ($dbResult) {

                        $view = view("panel-partials.ajax-pages.bookings.booking-index-modal-for-invoice-number-imports", compact('booking'))->render();
                        return response()->json(['view' => $view]);
                    } else {
                        return response()->json(['error' => "File Write Failed Database"]);

                    }


                } else {
                    return response()->json(['error' => "File put Failed.(S3)"]);
                }

//                $result=$booking;
//                $view = view("panel-partials.ajax-pages.bookings.booking-index-modal-for-invoice-number-imports", compact('booking'))->render();
//                return response()->json(['view' => $view]);

            case 'delete_extra_booking_file':
                $file = BookingImage::findOrFail($request->file_id);

                if ($file->delete()) {
                    Storage::disk('s3')->delete('booking-files/' . $file->image_name);
                    return response()->json(['status' => '1', 'success' => 'File Deleted Successfully']);
                }
                return response()->json(['status' => '0', 'error' => 'An Error Occurred!']);
                break;

            case 'insert_booking_invoice_numbers':
                $booking = Booking::findOrFail($request->booking_id);

                foreach ($request->invoices as $invoice) {
                    $booking->invoice_numbers()->create([
                        "invoice_number" => $invoice,
                        "type" => 1,
                        "src" => null,

                    ]);

                }

                return redirect()->back()->with(['success' => 'invoice numbers added succesfully']);

                break;

            case 'delete_booking_invoice':

                $invoice = BookingInvoice::findOrFail($request->invoice_id);
                if ($invoice->type == 1) {
                    if ($invoice->delete()) {
                        return response()->json(['status' => '1', 'success' => 'Invoice Number Deleted Successfully']);
                    }
                    return response()->json(['status' => '0', 'error' => 'Number Not Deleted in Database']);
                } else {
                    $file_status = Storage::disk('s3')->delete('/invoices/' . $invoice->src);
                    if ($file_status) {
                        if ($invoice->delete()) {
                            return response()->json(['status' => '1', 'success' => 'Invoice File Deleted Successfully']);
                        }
                        return response()->json(['status' => '0', 'error' => 'File Not Deleted in Database']);
                    } else {
                        return response()->json(['status' => '0', 'error' => 'File Not Deleted in Storage']);

                    }
                }
            case 'get_platforms':
                $platforms = Platform::all();
                return response()->json(['data' => $platforms]);
            case 'platform_status':
                $saveStatus=Platform::where('id',$request->platformID)->update([
                   'status'=>intval($request->platform_status)
                ]);
                $user = Auth::guard('admin')->user();
                $logDetail=$user->name.' '.$request->platformID.' number of Platform Changed '.(intval($request->platform_status) ? 'Active':'Passive');


                $adminLog = new AdminLog();
                $adminLog->userID = $user->id;
                $adminLog->page = 'Platform Index';
                $adminLog->url = $request->fullUrl();
                $adminLog->action = 'Changed Status';
                $adminLog->details = $logDetail;
                $adminLog->tableName = 'platform';
                $adminLog->columnName = 'status';

                $saveStatus ? $adminLog->result = 'successful' : $adminLog->result = 'failed';
                $adminLog->save();

                return response()->json(['saveStatus' => $saveStatus]);
            default:
                # code...
                break;
        }
    }

    public function onGoings() {
        $products = Product::pluck('title', 'id');
        return view('panel.bookings.on-goings', compact('products'));
    }

    public function downloadExtraFile($id) {
        $bookingImage = BookingImage::where('id', $id)->first();

        $file_url = $bookingImage->image_name;
        $extension = explode('.', $file_url)[count(explode('.', $file_url))-1];
        $content = file_get_contents($file_url);
        file_put_contents(public_path($bookingImage->image_base_name . '.' . $extension), $content);

        $file = public_path($bookingImage->image_base_name . '.' . $extension);
        return response()->download($file)->deleteFileAfterSend(true);
    }

    public function checkMailForCustomer(Request $request) {
        $contactMailLog = \App\BookingContactMailLog::where('booking_id', $request->bookingID)->orderBy('id', 'desc')->first();
        $checkInformation = json_decode($contactMailLog->check_information, true);
        $checkInformation["status"] = true;
        $checkInformation["checker"] = auth()->user()->name;
        $checkInformation["check_date"] = \Carbon\Carbon::now()->format('d/m/Y H:i');

        $contactMailLog->check_information = json_encode($checkInformation);
        $contactMailLog->save();

        return response()->json(['status' => true]);
    }

    public function bookingDetail(Request $request)
    {
        try {
            $booking = Booking::with(['contactBooking', 'extra_files', 'invoc', 'bookingOption', 'invoice_numbers'])
                ->withCount(['contactBooking', 'extra_files'])
                ->where('id', $request->id)
                ->first();

            $platforms = Platform::where('status', 1)->get();

            $attr = $request->attr ?? '';

            return view('panel.bookings.component.inner-overlay', compact('booking', 'attr', 'platforms'));

        } catch (\Exception $exception) {
            return response()->json([
                'message' => "Something went wrong. Please share BKN REF CODE: {$request->id} with IT"
            ], 400);
        }
    }

    public function updateBookingDetail(Request $request)
    {
        try {
            $booking = Booking::with('invoc')->where('id', $request->id)->first();
            $travelPeople=json_decode($booking->travelers)[0];
            $user = Auth::guard('admin')->user();
            $referenceCode = $booking->gygBookingReference ?? $this->apiRelated->explodeBookingRefCode($booking->bookingRefCode)['bkn'];
            $rDate=Carbon::createFromFormat('m/d/Y', $request->dateTime)->format('Y-m-d');
            $logDetail=$user->name.' '.$referenceCode.' reference code of Booking Updated';
            $isChanged=false;
            $action='Update';

            if ($request->firstName != $travelPeople->firstName){
                $logDetail.= ', changed Name '.$travelPeople->firstName.' to '.$request->firstName;
                $isChanged=true;
            }
            if ($request->lastName != $travelPeople->lastName){
                $logDetail.= ', changed LastName '.$travelPeople->lastName.' to '.$request->lastName;
                $isChanged=true;
            }
            if ($request->email != $travelPeople->email){
                $logDetail.= ', changed Email '.$travelPeople->email.' to '.$request->email;
                $isChanged=true;
            }
            if ($request->phoneNumber != $travelPeople->phoneNumber){
                $logDetail.= ', changed PhoneNumber '.$travelPeople->phoneNumber.' to '.$request->phoneNumber;
                $isChanged=true;
            }
            if ($rDate != Carbon::make($booking->bookingDateTime['org'])->format('Y-m-d')) {
                $logDetail.= ', changed Date '.$booking->bookingDateTime['org'].' to '.$rDate;
                $isChanged=true;
            }
            if (intval($request->platformID)!=$booking->platformID){
                $logDetail.= ', changed Platform '. $request->platformID .' to '. $request->platformID;
                $isChanged=true;
            }

            if ($booking->ivoc) {
                if ($request->companyAddress!=$booking->invoc->companyAddress){
                    $logDetail.= ', changed CompanyAddress '.($booking->invoc->companyAddress ==null ? 'Null':$booking->invoc->companyAddress).' to '.$request->companyAddress;
                    $isChanged=true;
                }
            }
            if ($request->companyAddress!=null &&$booking->invoc!=null) $booking->invoc->companyAddress = $request->companyAddress;

            $booking->travelerHotel = $request->travelerHotel;
            $booking->totalPrice = $request->totalPrice;
            $booking->travelers = json_encode([
                [
                    'email' => $request->email,
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'phoneNumber' => $request->phoneNumber
                ]
            ]);
            $booking->fullName = $request->firstName. ' '. $request->lastName;
            $date = $request->get('dateTime');
            if (!is_null($booking->date)) $booking->date = $date;
            if (!is_null($booking->hour)) {
                $hours = json_decode($booking->hour, true);

                foreach ($hours as $ind => $hour) {
                    if ($request->has('hourTo_' . $ind)){
                        $old=$hours[$ind]["hour"];
                        $hours[$ind]["hour"] = $request->get('hourFrom_' . $ind) . " - " . $request->get('hourTo_' . $ind);
                        if ($old!=$hours[$ind]["hour"]){
                            $logDetail.= ', changed Hour'.$ind.' '.$old.' to '.$hours[$ind]["hour"];
                            $isChanged=true;
                        }
                    }

                    else{
                        $old=$hours[$ind]["hour"];
                        $hours[$ind]["hour"] = $request->get('hourFrom_' . $ind);
                        if ($old!=$hours[$ind]["hour"]){
                            $logDetail.= ', changed Hour'.$ind.' '.$old.' to '.$hours[$ind]["hour"];
                            $isChanged=true;
                        }
                    }

                }

                $booking->hour = json_encode($hours);
            }

            if (!is_null($booking->dateForSort)) {
                if (!is_null($booking->hour)) {

                    if (count(json_decode($booking->hour, 1)) > 1) {
                        $arr = array_column(json_decode($booking->hour, 1), 'hour');

                        $minHour = min($arr);

                        $minHour = explode('-', $minHour);

                        $dateTime = Carbon::parse($booking->dateForSort)->format('Y-m-d'). ' '.str_replace(' ', '', $minHour[0]).':00';

                    }else{

                        $arr = json_decode($booking->hour, 1)[0]['hour'];

                        $minHour = explode('-' , $arr);


                        $dateTime = Carbon::make($booking->dateForSort)->format('Y-m-d'). ' ' . str_replace(' ', '', $minHour[0]). ':00';

                    }

                }else{

                    $dateTime = Carbon::make($booking->dateTime)->format('Y-m-d H:i:s');

                }

                $booking->dateForSort = $dateTime ?? $booking->dateForSort;
            }

            if ($this->isJSON($booking->dateTime)) {
                $dateTime = json_decode($booking->dateTime, true);
                foreach ($dateTime as $ind => $dt) {
                    $dateTime[$ind]["dateTime"] = explode("T", $dateTime[$ind]["dateTime"]);
                    $dateTime[$ind]["dateTime"][0] = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
                    $dateTime[$ind]["dateTime"][1] = explode("+", $dateTime[$ind]["dateTime"][1]);
                    if ($dateTime[$ind]["dateTime"][1][0] != "00:00:00")
                        $dateTime[$ind]["dateTime"][1][0] = $request->get('hourFrom_' . $ind) . ":00";

                    $dateTime[$ind]["dateTime"][1] = implode("+", $dateTime[$ind]["dateTime"][1]);
                    $dateTime[$ind]["dateTime"] = implode("T", $dateTime[$ind]["dateTime"]);
                }
                $dateTime = json_encode($dateTime);
            } else {
                $dateTime = $booking->dateTime;
                $dateTime = explode("T", $dateTime);
                $dateTime[0] = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
                $dateTime[1] = explode("+", $dateTime[1]);
                if ($dateTime[1][0] != "00:00:00")
                    $dateTime[1][0] = $request->get('hourFrom_0') . ":00";

                $dateTime[1] = implode("+", $dateTime[1]);
                $dateTime = implode("T", $dateTime);
            }

            $booking->dateTime = $dateTime;
            $booking->platformID = intval($request->platformID);
            if ($isChanged) {
                $adminLog = new AdminLog();
                $adminLog->userID = $user->id;
                $adminLog->page = 'Booking Edit';
                $adminLog->url = $request->fullUrl();
                $adminLog->action = $action;
                $adminLog->details = $logDetail;
                $adminLog->tableName = 'bookings invoce';
                $adminLog->columnName = 'bookings.*, companyAddress';
                $adminLog->save();
            }

            $booking->save();

            return response()->json([
                'message' => 'Booking Update Successfull'
            ]);
        } catch (\Exception $exception) {

            return response([
                'message' => 'An error has occurred'
            ], 400);
        }

    }

    public function sendMailToCustomer(Request $request)
    {
        try {
            $files = BookingImage::where('booking_id', $request->booking_id)->pluck('image_base_name')->toArray();
            $files = $files ? implode(',', $files) : null;

            $contactMail = new BookingContactMailLog();
            $contactMail->sender_id = Auth::guard('admin')->id();
            $contactMail->booking_id = $request->booking_id;
            $contactMail->mail_message = $request->mail_message;
            $contactMail->mail_title = $request->mail_title;
            $contactMail->mail_to = $request->mail_to;
            $contactMail->logMessage = 'Mail Has Been Queued!';
            $contactMail->code = md5(rand());
            $contactMail->files = $files;
            $contactMail->check_information = json_encode([
                'status' => false,
                'checker' => null,
                'check_date' => null,
            ]);

            $mail = new Mails();
            $mail->to = $request->mail_to;
            $mail->bookingID = $request->booking_id;
            $mail->status = 0;
            $mail->data = json_encode([
                [
                    'subject' => $request->mail_title,
                    'message' => $request->mail_message,
                    'action' => 'booking-customer-contact-send-mail-operation',
                ]
            ]);
            $mail->blade = 'bookings.mail-information';

            $contactMail->save();
            $mail->save();

            return response()->json([
                'message' => 'Mail Has Been Queued!'
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'message' => $exception->getMessage()
            ], 400);

        }

    }

    public function checkCustomerMail(Request $request)
    {
        try {
            $contactMailLog = BookingContactMailLog::where('booking_id', $request->booking_id)->orderBy('id', 'desc')->first();

            $contactMailLog->check_information = json_encode([
                'status' => true,
                'checker' => Auth::guard('admin')->user()->name,
                'check_date' => Carbon::now()->format('d/m/Y H:i')
            ]);

            $contactMailLog->save();

            return response()->json([
                'message' => 'Checked has successfully'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error has occurred: ' . $exception->getMessage()
            ], 400);
        }

    }

    public function addCommentToBooking(Request $request)
    {
        try {

            $booking = Booking::with('bookingOption')->where('id', $request->booking_id)->first();

            $booking->adminComment = $request->comment;

            if ($booking->save()) {
                $travelers = json_decode($booking->travelers, true);
                $mail = new Mails();
                $mail->data = json_encode([
                    'options' => $booking->bookingOption->title,
                    'date' => $booking->date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $booking->bookingRefCode,
                    'subject' => 'Booking is successful!',
                    'name' => $travelers[0]['firstName'],
                    'surname' => $travelers[0]['lastName'],
                    'sendToCC' => false
                ]);
                $mail->bookingID = $booking->id;
                $mail->to = $travelers[0]['email'];
                $mail->blade = 'mail.booking-successful';
                $mail->save();
            }

            return response()->json([
                'message' => 'Comment added successfully'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error has occurred: '. $exception->getMessage()
            ]);
        }
    }

    public function specialRefCodev2(Request $request)
    {

        try {
            $booking = Booking::findOrFail($request->booking_id);
            $booking->specialRefCode = empty(trim($request->specialRefCode)) ? null : $request->specialRefCode;
            $booking->save();

            return response()->json([
                'message' => 'Special Ref. Code changed successfully'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error has occurred: '. $exception->getMessage()
            ], 400);
        }
    }

    public function importInvoiceNumber(Request $request)
    {
        try {

            $booking    = Booking::findOrFail($request->booking_id);
            $user       = Auth::user();

            if ($request->has('invoice_number') && !is_null($request->invoice_number)) {

                $booking->invoice_numbers()->create([
                    "invoice_number" => $request->invoice_number,
                    "type" => 1,
                    "src" => null,
                ]);

                return response()->json([
                    'message' => 'Invoice number added successfully'
                ]);
            }

            if (!$request->hasFile('invoice_file')) {
                throw new \Exception('Please add a number or file', 400);
            }

            $mimeTypes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (!in_array($request->file('invoice_file')->getClientMimeType(), $mimeTypes)) {
                throw new \Exception('Allowable types jpeg,png,webp,pdf', 400);
            }
            $fileName   = $request->file('invoice_file')->getClientOriginalName();

            switch ($request->file('invoice_file')->getClientOriginalExtension()) {
                case 'png':
                    $fileType = 12;
                    break;
                case 'pdf':
                    $fileType = 21;
                    break;
                case 'jpeg':
                    $fileType = 11;
                    break;
                case 'webp':
                    $fileType = 13;
                    break;
            }

            if(BookingInvoice::where('src', $fileName)->exists())
                throw new \Exception('This File Added Before. You Should Change Name Then Upload File', 400);


            if (Storage::disk('s3')->put('invoices/' . $fileName, file_get_contents($request->file('invoice_file')))) {

                $booking->invoice_numbers()->create([
                    'invoice_number' => null,
                    'src' => $fileName,
                    'type' => $fileType ?? null,
                    'user_id' => $user->id,
                    'status' => 1,
                ]);

            }else{
                throw new \Exception('An error has occurred', 400);
            }


            return response()->json([
                'message' => 'Invoice added successfully'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error has occurred. Msg: ' . $exception->getMessage()
            ], 400);
        }
    }

    public function deleteImportedInvoice(Request $request)
    {
        try {
            if ($request->has('invoice_id') && is_null($request->invoice_id))
                throw new \Exception('', 400);

            $invoice = BookingInvoice::findOrFail($request->invoice_id);

            if ($invoice->type != 1)
                Storage::disk('s3')->delete('/invoices/' . $invoice->src);

            $invoice->delete();

            return response()->json([
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error has occurred. Msg: '. $exception->getMessage()
            ]);
        }
    }

}
