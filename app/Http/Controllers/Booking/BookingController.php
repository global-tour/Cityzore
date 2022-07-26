<?php

namespace App\Http\Controllers\Booking;

use App\Admin;
use App\Barcode;
use App\Booking;
use App\BookingConfirmation;
use App\BookingLog;
use App\Cart;
use App\Coupon;
use App\Events\StatusLiked;
use App\ExternalPayment;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Http\Controllers\PaymentController;
use App\Http\Requests\PaymentRequest;
use App\Invoice;
use App\Mails;
use App\Option;
use App\Platform;
use App\Product;
use App\ProductGallery;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\MailOperations;
use App\Currency;
use App\Http\Controllers\Helpers\BigBusRelated;
use App\Http\Controllers\Helpers\TootbusRelated;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Wishlist;
use App\Country;
use App\BookingRecord;
use App\SpecialOffers;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;


class  BookingController extends Controller
{

    public $refCodeGenerator;
    public $cartController;
    public $timeRelatedFunctions;
    public $apiRelated;
    public $tootbusRelated;
    public $mailOperations;
    public $commonFunctions;
    public $bigBusRelated;

    public function __construct()
    {
        $this->refCodeGenerator = new RefCodeGenerator();
        $this->cartController = new CartController();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->apiRelated = new ApiRelated();
        $this->tootbusRelated = new TootbusRelated();
        $this->mailOperations = new MailOperations();
        $this->commonFunctions = new CommonFunctions();
        $this->bigBusRelated = new BigBusRelated();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function profile()
    {
        if (Auth::guard('web')->check()) {
            $bookings = Booking::with(['bookingOption' => function ($q) {
                return $q->select('cancelPolicyTime', 'cancelPolicyTimeType', 'referenceCode');
            }])->where('userID', '=', Auth::guard('web')->user()->id)->orWhere('affiliateID', auth()->guard('web')->user()->id)->get();
            return view('frontend.profile', ['bookings' => $bookings]);
        }
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langCodeForUrl = $langCode == 'en' ? '' : $langCode;

        return redirect($langCodeForUrl . '/login');
    }

    /**
     * Preview before bookIt.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkOut(Request $request)
    {
        session()->forget('totalPriceWithDiscount');
        $clientIp = '';
        if (Auth::user()) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
        } else {
            $clientIp = session()->get('uniqueID');
            $cart = Cart::where('userID', '=', $clientIp)->where('status', '=', 0)->get();
        }


        $control = 0;
        $disabled_items = [];

        foreach ($cart as $c) {
            if ($c->is_tootbus == 1) {
                $diff_minutes = Carbon::now()->diffInMinutes($c->updated_at);

                if ($diff_minutes > 30) {


                    $data = [];
                    $data["expirationMinutes"] = 40;

                    $res = $this->tootbusRelated->extend(json_decode($c->tootbus_booking_response, true)["data"]["uuid"], $data);
                    if ($res["status"] === false) {
                        return response()->json(["status" => 0, "message" => $res["message"], "disabled_items" => []]);
                    }
                    $decoded_res = json_decode($res["message"], true);

                    if ($decoded_res["status"] !== "ON_HOLD") {
                        $c->status = 1;
                        $c->save();
                        $disabled_items[] = $c->id;
                        $control = 1;
                    } else {
                        $c->tootbus_booking_response = json_encode(["type" => "extend item", "data" => $decoded_res]);
                        $c->save();
                    }
                }

            }
        }


        if ($control) {
            return response()->json(["status" => 0, "message" => "you have expired card items please re-add existing items to card and continue!", "disabled_items" => $disabled_items]);
        }


        $images = [];
        $largestContactInfoArray = [];
        if (count($cart) > 0) {
            $totalPrice = 0;
            $totalCommission = 0;
            $totalPriceWOSO = 0;
            $contactForAllTravelers = 0;
            $totalTicketCount = 0;
            foreach ($cart as $c) {
                $totalTicketCount += $c->ticketCount;
                $totalPrice += $c->totalPrice;
                $totalPriceWOSO += $c->totalPriceWOSO;
                $product = Product::findOrFail($c->productID);
                $image = $product->productGalleries()->first()->src;
                array_push($images, $image);
                $totalCommission += $c->totalCommission;
                $option = Option::findOrFail($c->optionID);
                $contactInformationFields = json_decode($option->contactInformationFields, true);
                if (!is_null($contactInformationFields)) {
                    foreach ($contactInformationFields as $cif) {
                        array_push($largestContactInfoArray, ['title' => $cif['title'], 'name' => $cif['name'], 'isRequired' => $cif['isRequired'], 'optionID' => $c->optionID]);
                    }
                }
                if ($option->contactForAllTravelers == 1) {
                    $contactForAllTravelers = $option->contactForAllTravelers;
                }
            }
            $largestContactInfoArray = $this->commonFunctions->unique_multidimensional_array($largestContactInfoArray, 'title');

            // Payment related parameters
            $clientId = env('PAYMENT_CLIENT_ID', '190217122');
            if (is_null(session()->get('totalPriceWithDiscount'))) {
                $amount = $totalPrice;
            } else {
                $amount = 0;
                foreach (json_decode(session()->get('totalPriceWithDiscount'), true) as $key => $value) {
                    $amount += (float)$value["newPrice"];
                }


            }

            $okUrl = env('PAYMENT_OK_URL', 'https://cityzore.com/booking-successful');
            $failUrl = env('PAYMENT_FAIL_URL', 'https://cityzore.com/booking-failed');
            $currentUrl = $request->url();
            if (strpos($currentUrl, 'www') != false) {
                $okUrl = 'https://www.cityzore.com/booking-successful';
                $failUrl = 'https://www.cityzore.com/booking-failed';
            }
            $oid = $this->refCodeGenerator->invoiceGenerator();
            $rnd = microtime();
            $taksit = '';
            $islemtipi = env('PAYMENT_TYPE', 'Auth');
            $storekey = env('PAYMENT_STOREKEY', 'NamyeluS3+-*/');

            $hashstr = $clientId . $oid . $amount . $okUrl . $failUrl . $islemtipi . $taksit . $rnd . $storekey;
            $hash = base64_encode(pack('H*', sha1($hashstr)));

            $currencyCodes = [
                '1' => '840', '2' => '978', '3' => '826', '4' => '949',
                '5' => '756', '6' => '124', '7' => '643', '8' => '784', '9' => '578',
                '10' => '392', '11' => '356', '12' => '203', '13' => '944', '14' => '410',
                '15' => '634', '16' => '764'
            ];
            $currencyID = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2;
            $currencyCode = $currencyCodes[$currencyID];

            $countries = Country::all();

            $translationArray = json_encode([
                'checkYourCommission0' => __('checkYourCommission0'),
                'checkYourCommission1' => __('checkYourCommission1'),
                'cartShareFailed0' => __('cartShareFailed0'),
                'cartShareFailed1' => __('cartShareFailed1'),
                'cartShareSuccess0' => __('cartShareSuccess0'),
                'cartShareResponse' => __('cartShareResponse'),
                'cartShareLink' => __('cartShareLink'),
                'redirectHomePage' => __('redirectHomePage'),

            ]);
            $platforms = Platform::where('status', 1)->get();
            return view('frontend.checkout',
                [
                    'cart' => $cart,
                    'totalPrice' => $totalPrice,
                    'totalPriceWOSO' => $totalPriceWOSO,
                    'clientId' => $clientId,
                    'amount' => $amount,
                    'okUrl' => $okUrl,
                    'failUrl' => $failUrl,
                    'oid' => $oid,
                    'rnd' => $rnd,
                    'taksit' => $taksit,
                    'islemtipi' => $islemtipi,
                    'storekey' => $storekey,
                    'hashstr' => $hashstr,
                    'hash' => $hash,
                    'clientUniqueId' => $clientIp,
                    'images' => $images,
                    'totalCommission' => $totalCommission,
                    'currencyCode' => $currencyCode,
                    'largestContactInfoArray' => $largestContactInfoArray,
                    'contactForAllTravelers' => $contactForAllTravelers,
                    'totalTicketCount' => $totalTicketCount,
                    'countries' => $countries,
                    'translationArray' => $translationArray,
                    'success' => __('checkOutSuccess'),
                    'platforms' => $platforms
                ]
            );
        } else {
            return view('errors.empty-cart', ['error' => __('checkOutError')]);
        }
    }

    /**
     * Make booking from cart that is status 0.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookIt(Request $request)
    {
        if (auth()->user()) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
            $userID = auth()->user()->id;
        } else {
            $cart = Cart::where('userID', '=', session()->get('uniqueID'))->where('status', '=', 0)->get();
            $userID = null;
        }

        $totalPrice = 0;
        $timeRelated = new TimeRelatedFunctions();
        $timeZone = 'Europe/Paris';
        $booking = null;
        $bookings = [];
        // Big Bus product variables
        $bigBusRefCode = null;
        $isOkToBook = ['true'];
        $bbBookingResultJsonData = null;
        //

        foreach ($cart as $c) {
            $dateTimeArr = [];
            $optionID = $c->optionID;
            $option = Option::with('bigBus')->where('id', '=', $optionID)->first();
            if (!is_null($c->productID)) {
                $product = Product::findOrFail($c->productID);
                $timeZone = $product->countryName->timezone;
            }

            foreach (json_decode($c->hour, true) as $h) {
                if (strlen($h['hour']) < 6) {
                    $arr = ['dateTime' => $timeRelated->convertDmyToYmdWithHour($c->date, $h['hour'], $timeZone)];
                    array_push($dateTimeArr, $arr);
                } else {
                    $arr = ['dateTime' => $timeRelated->convertDmyToYmdWithHour($c->date, "00:00", $timeZone)];
                    array_push($dateTimeArr, $arr);
                }
            }

            $supplier = $option->supplier()->first();
            $supplierID = -1;
            if ($supplier) {
                $supplierID = $supplier->id;
            }
            $totalPrice += $c->totalPrice;
            $optionRefCode = $option->referenceCode;
            $booking = new Booking();


            $booking->affiliateID = $c->affiliate_id;


            $restaurant = null;

            $restaurant = Supplier::where('isRestaurant', 1)->where('id', $option->rCodeID)->first();
            if (!is_null($restaurant) && $restaurant->isActive == 1) {
                //$booking->status = 4;
                $booking->status = 0;
            } else {
                $booking->status = 0;
            }
            $traveler = $this->createTraveler($request, $c);


            // is cart has coupon (start)

            if (!is_null($c->coupon)) {
                $totalNewPrice = 0;
                $booking->coupon = $c->coupon;
                foreach (json_decode($c->coupon, true) as $key => $value) {
                    $totalNewPrice += (float)$value["newPrice"];
                }

            } else {
                $totalNewPrice = $c->totalPrice;
            }

            // is cart has coupon (end)


            $booking->affiliateID = $c->affiliate_id;
            $booking->productRefCode = explode('-', $c->referenceCode)[0];
            $booking->optionRefCode = $optionRefCode;
            $booking->reservationRefCode = $c->referenceCode;
            $booking->bookingRefCode = $this->refCodeGenerator->refCodeGeneratorForBooking($c->referenceCode);
            $booking->bookingItems = $c->bookingItems;
            $booking->language = 'tr';
            $booking->travelers = json_encode($traveler);
            $booking->fullName = $request->firstName . ' ' . $request->lastName;
            $booking->travelerHotel = $request->hotel;
            $booking->comment = $request->comment;
            $booking->totalPrice = !is_null($c->tempTotalPrice) ? $c->tempTotalPrice : $totalNewPrice;
            $booking->userID = $userID;
            $booking->companyID = $supplier ? $supplierID : -1;
            $booking->date = $c->date;
            $booking->hour = $c->hour;
            $booking->dateTime = json_encode($dateTimeArr);
            $avID = [];
            $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
            foreach ($bAvs as $bAv) {
                array_push($avID, $bAv->id);
            }
            $booking->avID = json_encode($avID);

            $c->date = str_replace('.', '/', $c->date);

            if (json_decode($c->hour, 1) > 1) {
                $arr = array_column(json_decode($c->hour, 1), 'hour');

                $minHour = min($arr);

                $minHour = explode('-', $minHour);
            }else{

                $arr = json_decode($c->hour, 1)[0]['hour'];

                $minHour = explode('-' , $arr);
            }

            $date = Carbon::createFromFormat('d/m/Y', $c->date);
            $asExpected = $date->format('Y-m-d');
            $booking->dateForSort = $asExpected. ' ' . str_replace(' ', '', $minHour[0]).':00';

            if (!($request->platformID == 0))
                $booking->platformID = intval($request->platformID);
            else
                $booking->platformID = 2;
            // if option has tootbus api (start)

            if ($c->is_tootbus == 1) {


                $booking->is_tootbus = 1;


                $data = [];
                $data["resellerReference"] = $c->referenceCode;

                $res = $this->tootbusRelated->confirm(json_decode($c->tootbus_booking_response, true)["data"]["uuid"], $data);
                if ($res["status"] === false) {
                    $booking->tootbus_booking_response = json_encode(["type" => "Error confirm", "data" => $res["message"]]);
                }
                $decoded_res = json_decode($res["message"], true);


                $booking->tootbus_booking_response = json_encode(["type" => "confirm Booking", "data" => $decoded_res]);


            }


            // if option has tootbus api (end)

            // If option has big bus external id send booking request to Big Bus API, else resume normal operations
            if (!is_null($option->bigBus)) {

                /**
                 * Token For Test
                 *
                 **/
                $bigBus = $this->bigBusRelated
                    ->setClient();

                $bigBusDate = Carbon::make(str_replace('/', '-', $c->date))->format('Y-m-d');

                $bigBusAvailabilityResponse = $bigBus->checkAvailability($option->bigBus->product_id, $bigBusDate);

                if ($bigBusAvailabilityResponse['status'] && count($bigBusAvailabilityResponse['data']) && $bigBusAvailabilityResponse['data'][0]['available']) {
                    $bigBus->setLog($booking->id, 'availability', $bigBusAvailabilityResponse['data']);

                    $uuid = Uuid::uuid4();

                    foreach (json_decode($booking->bookingItems, 1) as $item) {
                        for ($i = 0; $i < $item['count']; $i++) {
                            $unitItems[]['unitId'] = json_decode($option->bigBus->units, 1)[$item['category']]['id'];
                        }
                    }

                    $data = [
                        'uuid'                  => $uuid,
                        'expirationMinutes'     => 75,
                        'productId'             => $option->bigBus->product_id,
                        'optionId'              => 'DEFAULT',
                        'availabilityId'        => $bigBusAvailabilityResponse['data'][0]['id'],
                        'unitItems'             => $unitItems
                    ];

                    $bigbusReserveResponse = $bigBus->reserve($data);

                    if ($bigbusReserveResponse['status'] && count($bigbusReserveResponse)) {
                        $bigBus->setLog($booking->id, 'reservation', $bigbusReserveResponse['data']);

                        $data = [
                            'emailReceipt' => true,
                            'resellerReference' => null,
                            'contact'           => [
                                'fullName'      => $booking->fullName,
                                'emailAdress'   => json_decode($booking->travelers, 1)[0]['email'] ?? '',
                                'phoneNumber'   => json_decode($booking->travelers, 1)[0]['phone'] ?? '',
                                'locales'       => [$booking->language],
                                'country'       => "FR"
                            ]
                        ];

                        $bigBusConfirmResponse = $bigBus->confirm($bigbusReserveResponse['data']['id'], $data);

                        if ($bigBusConfirmResponse['status'] && count($bigBusConfirmResponse['data'])) {
                            $bigBus->setLog($booking->id, 'confirm', $bigBusConfirmResponse['data']);
                            $booking->bigBusRefCode = $bigBusConfirmResponse['data']['uuid'];

                            array_push($isOkToBook, 'true');
                        }else{
                            array_push($isOkToBook, 'false');
                        }
                    }else{
                        array_push($isOkToBook, 'false');
                    }

                }else{
                    array_push($isOkToBook, 'false');
                }

            }

            $booking->currencyID = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2;
            $categoryAndCountInfo = $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems);

            if (!in_array('false', $isOkToBook)) {
                if ($booking->save()) {
                    $copyBook = collect($booking->toArray())->except(["tootbus_booking_response"]);
                    event(new StatusLiked("New booking on " . $booking->date . " - " . json_decode($booking->hour, true)[0]['hour'] . ' for ' . $option->referenceCode . ' ', $copyBook, 'CITYZORE_BOOKING'));

                    array_push($bookings, $booking);
                    $invoice = new Invoice();
                    $invoice->paymentMethod = 'COMMISSION';
                    $invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
                    $invoice->bookingID = $booking->id;
                    $invoice->companyID = $option->supplierID;
                    $invoice->save();
                    $booking->invoiceID = $invoice->id;
                    $booking->save();
                    $c->status = 2;
                    $c->save();
                    // If option has no Big Bus ID continue normal operations, else create new barcodes for Big Bus
                    if (is_null($option->bigBus) && $c->is_tootbus == 0) {
                        $availabilities = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
                        foreach ($availabilities as $av) {
                            $ticketType = $av->ticketType()->first();
                            if (!is_null($ticketType)) {
                                if ($ticketType->id == 30) {
                                    $barcode = Barcode::where(function ($q) {
                                        $q->where('ticketType', '=', 30)->orWhere('ticketType', '=', 31);
                                    })->where('isUsed', '=', 0)->where('isReserved', '=', 1)
                                        ->where('cartID', $c->id)
                                        ->where('isExpired', '<>', 1)
                                        ->where('ownerID', '=', $booking->companyID)->get();
                                } else {
                                    $barcode = Barcode::where('ticketType', '=', $ticketType->id)
                                        ->where('isUsed', '=', 0)->where('isReserved', '=', 1)
                                        ->where('cartID', $c->id)
                                        ->where('isExpired', '<>', 1)
                                        ->where('ownerID', '=', $booking->companyID)->get();
                                }
                                foreach ($barcode as $b) {
                                    $b->cartID = $c->id;
                                    $b->bookingID = $booking->id;
                                    $b->isUsed = 1;
                                    $b->save();
                                }
                            }
                        }
                    } else {


                        if (!is_null($option->bigBus)) {  // if bigbus
                            // $bbProducts = $bbBookingResultJsonData->products;
                            // $bbProductsJson = response()->json($bbProducts);
                            // $bbProductsJsonData = $bbProductsJson->getData();
                            // $bbProductsProduct = $bbProductsJsonData->product;
                            $bbItems = $bigBusConfirmResponse['data']['unitItems'];
                            foreach ($bbItems as $bbKey => $bbItem) {
                                $barcode = new Barcode();
                                $barcode->ticketType = 3;
                                $barcode->code = $bbItems[$bbKey]['ticket']['deliveryOptions'][0]['deliveryValue'];
                                $barcode->isUsed = 1;
                                $barcode->isReserved = 1;
                                $barcode->isExpired = 0;
                                $barcode->searchableTicketType = 'Big Bus Ticket';
                                $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                                $barcode->ownerID = -1;
                                $barcode->cartID = $c->id;
                                $barcode->bookingID = $booking->id;
                                $barcode->save();


                            }
                        } else { // if tootbus

                            $barcode = new Barcode();
                            $barcode->ticketType = 24;
                            $barcode->code = $decoded_res["voucher"]["deliveryOptions"][0]["deliveryValue"];
                            $barcode->isUsed = 1;
                            $barcode->isReserved = 1;
                            $barcode->isExpired = 0;
                            $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                            $barcode->ownerID = -1;
                            $barcode->cartID = $c->id;
                            $barcode->bookingID = $booking->id;
                            $barcode->save();


                        }
                    }

                    $this->mailForBookings($optionID, $booking->id, $this->apiRelated->explodeBookingRefCode($booking->bookingRefCode)['bkn'], $request);

                    $this->commonFunctions->changeSoldCount($c, '+');
                }
            } else {
                $bookingLogs = new BookingLog();
                $bookingLogs->userID = $c->userID;
                $bookingLogs->cartID = $c->id;
                $bookingLogs->optionID = $c->optionID;
                $bookingLogs->code = 'Failed (Big Bus API)';
                $bookingLogs->save();
                return view('frontend.booking-failed',
                    [
                        'errorCode' => '-1',
                        'errorMessage' => 'Payment is successfully made but Big Bus API response was not successful. Please contact us to solve this issue.'
                    ]
                );
            }

            $couponID = $request->couponIDHidden;

            if ($couponID) {
                $coupon = Coupon::findOrFail($couponID);
                $coupon->countOfUsing += 1;
                $coupon->save();
            }

        }

        $checkIfBookingHasTicketType = 0;
        foreach ($bookings as $b) {
            $avs = $b->bookingOption->avs;
            foreach ($avs as $av) {
                $ticketTypes = $av->ticketType;
                foreach ($ticketTypes as $ticketType) {
                    if ($ticketType)
                        $checkIfBookingHasTicketType = 1;
                }
            }
        }

        return view('frontend.booking-successful', ['bookings' => $bookings, 'checkIfBookingHasTicketType' => $checkIfBookingHasTicketType]);
    }

    /**
     * Sends booking confirmation mail
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookingConfirmation(Request $request)
    {
        $confirmationCode = $request->confirmationCode;
        $bookingConfirmation = BookingConfirmation::where('confirmationCode', '=', $confirmationCode)->where('isUsed', '=', 0)->first();
        if (!is_null($bookingConfirmation)) {
            $booking = Booking::findOrFail($bookingConfirmation->bookingID);
            $booking->status = 0;
            $avID = [];
            $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
            foreach ($bAvs as $bAv) {
                array_push($avID, $bAv->id);
            }
            $booking->avID = json_encode($avID);
            $bookingConfirmation->isUsed = 1;
            $bookingConfirmation->save();
            if ($booking->save()) {
                $traveler = json_decode($booking->travelers, true)[0];
                $option = Option::where('referenceCode', '=', $booking->optionRefCode)->first();
                $supplier = $option->supplier()->first();
                $supplierID = -1;
                if ($supplier) {
                    $supplierID = $supplier->id;
                }
                $BKNCode = explode('-', $booking->bookingRefCode)[3];
                $restaurant = null;
                if (!is_null($option->rCodeID)) {
                    $restaurant = Supplier::where('isRestaurant', 1)->where('id', $option->rCodeID)->first();
                }
                $invoice = new Invoice();
                $invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
                $invoice->paymentMethod = "COMISSION";
                $invoice->bookingID = $booking->id;
                $invoice->companyID = $supplier ? $supplierID : -1;
                $invoice->save();
                $booking->invoiceID = $invoice->id;
                $booking->save();
                $cart = Cart::where('referenceCode', '=', $booking->reservationRefCode)->first();
                $cart->status = 2;
                $cart->save();

                if (Auth::guard('web')->check()) {
                    if (!is_null(Auth::guard('web')->user()->ccEmail)) {
                        $email = Auth::guard('web')->user()->ccEmail;
                    } else {
                        $email = Auth::guard('web')->user()->email;
                    }
                } else {
                    $email = $request->email;
                }

                $mail = new Mails();
                if (is_null($option->rCodeID)) {
                    //Mail for company
                    if ($supplierID == -1) {
                        $data = [];
                        array_push($data, [
                                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                'options' => $option->title,
                                'date' => $booking->date,
                                'hour' => json_decode($booking->hour, true)[0]['hour'],
                                'BKNCode' => $BKNCode,
                                'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'from' => Platform::where('id', $booking->platformID)->value('name'),
                                'sendToCC' => true
                            ]
                        );
                        $mail->data = json_encode($data);
                        $mail->to = $email;
                        $mail->blade = 'mail.booking-successful-for-creator';
                        $mail->save();
                    } else {
                        $data = [];
                        array_push($data,
                            [
                                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                'options' => $option->title,
                                'date' => $booking->date,
                                'hour' => json_decode($booking->hour, true)[0]['hour'],
                                'BKNCode' => $BKNCode,
                                'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'from' => Platform::where('id', $booking->platformID)->value('name'),
                                'sendToCC' => false
                            ]
                        );
                        $mail->data = json_encode($data);
                        $mail->to = $supplier->email;
                        $mail->blade = 'mail.booking-successful-for-creator';

                        // Mail for admin (contact@parisviptrips.com)
                        $mail = new Mails();
                        $data = [];
                        array_push($data, [
                            'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                            'options' => $option->title,
                            'date' => $booking->date,
                            'hour' => json_decode($booking->hour, true)[0]['hour'],
                            'BKNCode' => $BKNCode,
                            'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                            'name' => $traveler['firstName'],
                            'surname' => $traveler['lastName'],
                            'sendToCC' => false,
                            'supplierCompanyName' => $supplier->companyName
                        ]);
                        $mail->data = json_encode($data);
                        $mail->to = 'contact@parisviptrips.com';
                        $mail->blade = 'mail.supplier-booking-successful-for-creator';
                        $mail->save();
                    }
                    // Mail for customer
                    $data = [];
                    array_push($data,
                        [
                            'booking_id' => $booking->id,
                            'options' => $option->title,
                            'date' => $booking->date,
                            'hour' => json_decode($booking->hour, true)[0]['hour'],
                            'BKNCode' => $BKNCode,
                            'subject' => $BKNCode . ' - ' . 'Booking is successful!',
                            'name' => $traveler['firstName'],
                            'surname' => $traveler['lastName'],
                            'sendToCC' => false
                        ]
                    );
                    $mail->data = json_encode($data);
                    $mail->to = $traveler['email'];
                    $mail->blade = 'mail.booking-successful';
                } else {
                    if ($restaurant->isActive == 1) {
                        // Mail for customer
                        $data = [];
                        array_push($data,
                            [
                                'options' => $option->title,
                                'date' => $booking->date,
                                'BKNCode' => $BKNCode,
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'sendToCC' => false
                            ]
                        );
                        $mail->data = json_encode($data);
                        $mail->to = $traveler['email'];
                        $mail->blade = 'mail.booking-pending';

                        // Mail for restaurant
                        $data = [];
                        array_push($data,
                            [
                                'options' => $option->title,
                                'date' => $booking->date,
                                'BKNCode' => $BKNCode,
                                'subject' => $BKNCode . ' - ' . 'R-Code is required!',
                                'hash' => md5($restaurant->email . env('HASH_STRING')),
                                'refCode' => $option->referenceCode,
                                'sendToCC' => false
                            ]
                        );
                        $mail->data = json_encode($data);
                        $mail->to = $restaurant->email;
                        $mail->blade = 'mail.restaurant';
                        //Mail for company
                        if ($supplierID == -1) {
                            $data = [];
                            array_push($data, [
                                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                'options' => $option->title,
                                'date' => $booking->date,
                                'hour' => json_decode($booking->hour, true)[0]['hour'],
                                'BKNCode' => $BKNCode,
                                'subject' => $BKNCode . ' - ' . 'Booking is Pending !',
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'from' => Platform::where('id', $booking->platformID)->value('name'),
                                'sendToCC' => true
                            ]);
                            $mail->data = json_encode($data);
                            $mail->to = $email;
                            $mail->blade = 'mail.booking-successful-for-creator';
                        } else {
                            $data = [];
                            array_push($data, [
                                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                'options' => $option->title,
                                'date' => $booking->date,
                                'hour' => json_decode($booking->hour, true)[0]['hour'],
                                'BKNCode' => $BKNCode,
                                'subject' => $BKNCode . ' - ' . 'Booking is Pending !',
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'from' => Platform::where('id', $booking->platformID)->value('name'),
                                'sendToCC' => false
                            ]);
                            $mail->data = json_encode($data);
                            $mail->to = $supplier->email;
                            $mail->blade = 'mail.booking-successful-for-creator';
                        }
                    } else {
                        // Mail for customer
                        $data = [];
                        array_push($data, [
                            'booking_id' => $booking->id,
                            'options' => $option->title,
                            'date' => $booking->date,
                            'hour' => json_decode($booking->hour, true)[0]['hour'],
                            'BKNCode' => $BKNCode,
                            'subject' => $BKNCode . ' - ' . 'Booking is Successful !',
                            'name' => $traveler['firstName'],
                            'surname' => $traveler['lastName'],
                            'sendToCC' => false
                        ]);
                        $mail->data = json_encode($data);
                        $mail->to = $traveler['email'];
                        $mail->blade = 'mail.booking-successful';

                        //Mail for company
                        if ($supplierID == -1) {
                            $data = [];
                            array_push($data, [
                                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                'options' => $option->title,
                                'date' => $booking->date,
                                'hour' => json_decode($booking->hour, true)[0]['hour'],
                                'BKNCode' => $BKNCode,
                                'subject' => $BKNCode . ' - ' . 'Successful Booking!',
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'from' => Platform::where('id', $booking->platformID)->value('name'),
                                'sendToCC' => true
                            ]);
                            $mail->data = json_encode($data);
                            $mail->to = $email;
                            $mail->blade = 'mail.booking-successful-for-creator';
                        } else {
                            $data = [];
                            array_push($data, [
                                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                                'date' => $booking->date,
                                'options' => $option->title,
                                'hour' => json_decode($booking->hour, true)[0]['hour'],
                                'BKNCode' => $BKNCode,
                                'subject' => $BKNCode . ' - ' . 'Booking is Successful !',
                                'name' => $traveler['firstName'],
                                'surname' => $traveler['lastName'],
                                'from' => Platform::where('id', $booking->platformID)->value('name'),
                                'sendToCC' => false
                            ]);
                            $mail->data = json_encode($data);
                            $mail->to = $supplier->email;
                            $mail->blade = 'mail.booking-successful-for-creator';
                        }
                    }
                }

                $mail->save();

                Barcode::where('isUsed', 1)->where('isReserved', 1)
                    ->where('cartID', $cart->id)
                    ->where('isExpired', '<>', 1)
                    ->where('ownerID', $supplierID)->take($cart->ticketCount)
                    ->update(['bookingID' => $booking->id]);

                $checkIfBookingHasTicketType = 0;
                $avs = $option->avs;
                foreach ($avs as $av) {
                    $ticketTypes = $av->ticketType;
                    foreach ($ticketTypes as $ticketType) {
                        if ($ticketType)
                            $checkIfBookingHasTicketType = 1;
                    }
                }

                return view('frontend.booking-successful', ['bookingConfirmation' => $bookingConfirmation, 'option' => $option, 'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems), 'checkIfBookingHasTicketType' => $checkIfBookingHasTicketType]);
            }
        } else {
            abort(404);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookingSuccessful(Request $request)
    {

        $clientIp = $request->get('clientUniqueId');
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $oid = $request->get('oid');
        $booking = null;
        // Big Bus Product Variables
        $bigBusRefCode = null;
        $isOkToBook = ['true'];
        $bbBookingResultJsonData = null;
        //

        if (Auth::user()) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
            $userID = Auth::user()->id;
        } else {
            $cart = Cart::where('userID', '=', $clientIp)->where('status', '=', 0)->get();
            $userID = null;
        }

        $totalPrice = 0;
        $timeRelated = new TimeRelatedFunctions();
        $timeZone = 'Europe/Paris';
        $bookings = [];
        foreach ($cart as $c) {
            $dateTimeArr = [];
            if (!is_null($c->productID)) {
                $product = Product::findOrFail($c->productID);
                $timeZone = $product->countryName->timezone;
            }
            foreach (json_decode($c->hour, true) as $h) {
                if (strlen($h['hour']) < 6) {
                    $arr = ['dateTime' => $timeRelated->convertDmyToYmdWithHour($c->date, $h['hour'], $timeZone)];
                    array_push($dateTimeArr, $arr);
                } else {
                    $arr = ['dateTime' => $timeRelated->convertDmyToYmdWithHour($c->date, "00:00", $timeZone)];
                    array_push($dateTimeArr, $arr);
                }
            }

            $optionID = $c->optionID;
            $option = Option::where('id', '=', $optionID)->first();
            $supplier = $option->supplier()->first();
            $supplierID = -1;
            if ($supplier) {
                $supplierID = $supplier->id;
            }
            $totalPrice += $c->totalPrice;
            $optionRefCode = $option->referenceCode;

            $booking = new Booking();
            $booking->affiliateID = $c->affiliate_id;
            $restaurant = null;
            if (is_null($option->rCodeID)) {
                $booking->status = 0;
            } else {
                $restaurant = Supplier::where('isRestaurant', 1)->where('id', $option->rCodeID)->first();
                //$booking->status = $restaurant->isActive == 1 ? 4 : 0;
                $booking->status = $restaurant->isActive == 1 ? 0 : 0;
            }

            $traveler = $this->createTraveler($request, $c);


            // is cart has coupon (start)

            if (!is_null($c->coupon)) {
                $totalNewPrice = 0;
                $booking->coupon = $c->coupon;
                foreach (json_decode($c->coupon, true) as $key => $value) {
                    $totalNewPrice += (float)$value["newPrice"];
                }

            } else {
                $totalNewPrice = $c->totalPrice;
            }

            // is cart has coupon (end)

            $booking->productRefCode = explode('-', $c->referenceCode)[0];
            $booking->optionRefCode = $optionRefCode;
            $booking->reservationRefCode = $c->referenceCode;
            $booking->bookingRefCode = $this->refCodeGenerator->refCodeGeneratorForBooking($c->referenceCode);
            $booking->bookingItems = $c->bookingItems;
            $booking->language = 'tr';
            $booking->travelers = json_encode($traveler);
            $booking->fullName = $firstName . ' ' . $lastName;
            $booking->travelerHotel = $request->hotel;
            $booking->comment = $request->comment;
            $booking->totalPrice = !is_null($c->tempTotalPrice) ? $c->tempTotalPrice : $totalNewPrice;
            $booking->userID = $userID;
            $booking->companyID = $supplier ? $supplierID : -1;
            $booking->date = $c->date;
            $booking->hour = $c->hour;
            $booking->affiliateID = $c->affiliate_id;
            $booking->dateTime = json_encode($dateTimeArr);
            $date = Carbon::createFromFormat('d/m/Y', $c->date);
            $asExpected = $date->format('Y-m-d');
            $booking->dateForSort = $asExpected;
            $booking->platformID = 2;
            $booking->deviceType = $request->deviceType;

            $this->useSpecialOfferOnBooking($option, $c->productID, $c->date);

            $avID = [];
            $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
            foreach ($bAvs as $bAv) {
                array_push($avID, $bAv->id);
            }
            $booking->avID = json_encode($avID);


            // if option has tootbus api (start)

            if ($c->is_tootbus == 1) {


                $booking->is_tootbus = 1;


                $data = [];
                $data["resellerReference"] = $c->referenceCode;

                $res = $this->tootbusRelated->confirm(json_decode($c->tootbus_booking_response, true)["data"]["uuid"], $data);
                if ($res["status"] === false) {
                    $booking->tootbus_booking_response = json_encode(["type" => "Error confirm", "data" => $res["message"]]);
                }
                $decoded_res = json_decode($res["message"], true);


                $booking->tootbus_booking_response = json_encode(["type" => "confirm Booking", "data" => $decoded_res]);


            }


            // if option has tootbus api (end)


            // If option has big bus external id send booking request to Big Bus API, else resume normal operations
            if (!is_null($option->bigBusID)) {

                $bigBus = $this->bigBusRelated->setClient();

                $bigBusDate = Carbon::make(str_replace('/', '-', $c->date))->format('Y-m-d');

                $bigBusAvailabilityResponse = $bigBus->checkAvailability($option->bigBus->product_id, $bigBusDate);

                if ($bigBusAvailabilityResponse['status'] && count($bigBusAvailabilityResponse['data']) && $bigBusAvailabilityResponse['data'][0]['available']) {

                    $bigBus->setLog($booking->id, 'availability', $bigBusAvailabilityResponse['data']);

                    $uuid = Uuid::uuid4();

                    foreach (json_decode($booking->bookingItems, 1) as $item) {
                        for ($i = 0; $i < $item['count']; $i++) {
                            $unitItems[]['unitId'] = json_decode($option->bigBus->units, 1)[$item['category']]['id'];
                        }
                    }

                    $data = [
                        'uuid'                  => $uuid,
                        'expirationMinutes'     => 75,
                        'productId'             => $option->bigBus->product_id,
                        'optionId'              => 'DEFAULT',
                        'availabilityId'        => $bigBusAvailabilityResponse['data'][0]['id'],
                        'unitItems'             => $unitItems
                    ];

                    $bigbusReserveResponse = $bigBus->reserve($data);

                    if ($bigbusReserveResponse['status'] && count($bigbusReserveResponse)) {
                        $bigBus->setLog($booking->id, 'reservation', $bigbusReserveResponse['data']);

                        $data = [
                            'emailReceipt' => true,
                            'resellerReference' => null,
                            'contact'           => [
                                'fullName'      => $booking->fullName,
                                'emailAdress'   => json_decode($booking->travelers, 1)[0]['email'] ?? '',
                                'phoneNumber'   => json_decode($booking->travelers, 1)[0]['phone'] ?? '',
                                'locales'       => [$booking->language],
                                'country'       => "FR"
                            ]
                        ];

                        $bigBusConfirmResponse = $bigBus->confirm($bigbusReserveResponse['data']['id'], $data);

                        if ($bigBusConfirmResponse['status'] && count($bigBusConfirmResponse['data'])) {

                            $bigBus->setLog($booking->id, 'confirm', $bigBusConfirmResponse['data']);

                            $booking->bigBusRefCode = $bigBusConfirmResponse['data']['uuid'];

                            $isOkToBook[] = 'true';
                        }else{
                            $isOkToBook[] = 'false';
                        }
                    }else{
                        $isOkToBook[] = 'false';
                    }
                }else{
                    $isOkToBook[] = 'false';
                }
            }

            $booking->currencyID = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2;
            if (!in_array('false', $isOkToBook)) {
                if ($booking->save()) {
                    $copyBook = collect($booking->toArray())->except(["tootbus_booking_response"]);
                    event(new StatusLiked("New booking on " . $booking->date . " - " . json_decode($booking->hour, true)[0]['hour'] . ' for ' . $option->referenceCode . ' ', $copyBook, 'CITYZORE_BOOKING'));
                    array_push($bookings, $booking);
                    $c->status = 2;
                    $c->save();
                    $invoice = new Invoice();
                    $invoice->paymentMethod = "CREDIT CARD";
                    $invoice->bookingID = $booking->id;
                    $invoice->companyID = $booking->companyID;
                    //$invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
                    $invoice->referenceCode = $oid;
                    $invoice->save();
                    $booking->invoiceID = $invoice->id;
                    $booking->save();
                    // If option has no Big Bus ID continue normal operations, else create new barcodes for Big Bus
                    if (is_null($option->bigBusID) && $c->is_tootbus == 0) {
                        $availabilities = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
                        foreach ($availabilities as $av) {
                            $ticketType = $av->ticketType()->first();
                            if (!is_null($ticketType)) {
                                $barcode = Barcode::where('ticketType', '=', $ticketType->id)->where('isUsed', '=', 0)
                                    ->where('isReserved', '=', 1)->where('isExpired', '<>', 1)->where('ownerID', '=', $booking->companyID)->get();
                                foreach ($barcode as $b) {
                                    $b->cartID = $c->id;
                                    $b->bookingID = $booking->id;
                                    $b->isUsed = 1;
                                    $b->save();
                                }
                            }
                        }

                        Barcode::where('isUsed', 1)->where('isReserved', 1)
                            ->where('cartID', $c->id)
                            ->where('isExpired', '<>', 1)
                            ->where('ownerID', $supplierID)->take($c->ticketCount)
                            ->update(['bookingID' => $booking->id]);
                    } else {


                        if (!is_null($option->bigBusID)) {  // if bigbus
                            // $bbProducts = $bbBookingResultJsonData->products;
                            // $bbProductsJson = response()->json($bbProducts);
                            // $bbProductsJsonData = $bbProductsJson->getData();
                            // $bbProductsProduct = $bbProductsJsonData->product;
                            $bbItems = $bigBusConfirmResponse['data']['unitItems'];
                            foreach ($bbItems as $bbKey => $bbItem) {
                                $barcode = new Barcode();
                                $barcode->ticketType = 3;
                                $barcode->code = $bbItems[$bbKey]['ticket']['deliveryOptions'][0]['deliveryValue'];
                                $barcode->isUsed = 1;
                                $barcode->isReserved = 1;
                                $barcode->isExpired = 0;
                                $barcode->searchableTicketType = 'Big Bus Ticket';
                                $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                                $barcode->ownerID = -1;
                                $barcode->cartID = $c->id;
                                $barcode->bookingID = $booking->id;
                                $barcode->save();


                            }
                        } else { // if tootbus

                            $barcode = new Barcode();
                            $barcode->ticketType = 24;
                            $barcode->code = $decoded_res["voucher"]["deliveryOptions"][0]["deliveryValue"];
                            $barcode->isUsed = 1;
                            $barcode->isReserved = 1;
                            $barcode->isExpired = 0;
                            $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                            $barcode->ownerID = -1;
                            $barcode->cartID = $c->id;
                            $barcode->bookingID = $booking->id;
                            $barcode->save();


                        }
                    }

                    $bookingLogs = new BookingLog();
                    $bookingLogs->userID = $c->userID;
                    $bookingLogs->cartID = $c->id;
                    $bookingLogs->optionID = $c->optionID;
                    $bookingLogs->code = 'Success';
                    $bookingLogs->processID = $oid;
                    $bookingLogs->save();
                    $this->commonFunctions->changeSoldCount($c, '+');
                }
            } else {
                $bookingLogs = new BookingLog();
                $bookingLogs->userID = $c->userID;
                $bookingLogs->cartID = $c->id;
                $bookingLogs->optionID = $c->optionID;
                $bookingLogs->code = 'Success';
                $bookingLogs->processID = $oid;
                $bookingLogs->save();
                return view('frontend.booking-failed',
                    [
                        'errorCode' => '-1',
                        'errorMessage' => 'Payment is successfully made but Big Bus API response was not successful. Please contact us to solve this issue.'
                    ]
                );
            }

            $this->mailForBookings($optionID, $booking->id, $this->apiRelated->explodeBookingRefCode($booking->bookingRefCode)['bkn'], $request);
        }

        $couponID = $request->couponIDHidden;

        if ($couponID) {
            $coupon = Coupon::findOrFail($couponID);
            $coupon->countOfUsing += 1;
            $coupon->save();
        }

        $checkIfBookingHasTicketType = 0;
        foreach ($bookings as $b) {
            $avs = $b->bookingOption->avs;
            foreach ($avs as $av) {
                $ticketTypes = $av->ticketType;
                foreach ($ticketTypes as $ticketType) {
                    if ($ticketType)
                        $checkIfBookingHasTicketType = 1;
                }
            }
        }

        return view('frontend.booking-successful',
            [
                'option' => $option,
                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                'bookings' => $bookings,
                'checkIfBookingHasTicketType' => $checkIfBookingHasTicketType
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookingFailed(Request $request)
    {
        $errorCode = $request->get('ProcReturnCode');
        $errorMessage = $request->get('ErrMsg');
        $userID = $request->get('clientUniqueId');
        if (Auth::user()) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
        } else {
            $cart = Cart::where('userID', '=', $userID)->where('status', '=', 0)->get();
            $userID = null;
        }

        foreach ($cart as $c) {
            $bookingLogs = new BookingLog();
            $bookingLogs->userID = $c->userID;
            $bookingLogs->cartID = $c->id;
            $bookingLogs->code = '[' . $errorCode . ']' . '-' . $errorMessage;
            $bookingLogs->optionID = $c->optionID;
            $bookingLogs->processID = $request->get('oid');
            if ($bookingLogs->save()) {
                $mail = new Mails();
                // Mail for customer
                $data = [];
                array_push($data, [
                    'subject' => 'Payment Failed!',
                    'processID' => $bookingLogs->processID,
                    'userID' => $bookingLogs->userID,
                    'optionTitle' => $bookingLogs->option->title,
                    'code' => $bookingLogs->code,
                    'paymentDate' => $bookingLogs->created_at,
                    'cartID' => $bookingLogs->id,
                    'sendToCC' => false
                ]);
                $mail->data = json_encode($data);
                $mail->to = 'contact@parisviptrips.com';
                $mail->blade = 'mail.booking-failed';
                $mail->save();
            }
        }

        return view('frontend.booking-failed',
            [
                'errorCode' => $errorCode,
                'errorMessage' => $errorMessage
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function externalPaymentSuccessful(Request $request)
    {
        $payment = ExternalPayment::findOrFail($request->get('paymentid'));
        $payment->is_paid = 1;
        $payment->invoiceID = $request->get('oid');
        if ($payment->save()) {
            $mail = new Mails();
            // Mail for customer
            $data = [];
            array_push($data, [
                'subject' => 'Payment is successful!',
                'payment_email' => $payment->email,
                'payment_price' => $payment->price,
                'payment_message' => $payment->message,
                'payment_reference_code' => $payment->referenceCode,
                'sendToCC' => true
            ]);
            $mail->data = json_encode($data);
            $mail->to = $request->get('email');
            $mail->blade = 'mail.payment-successful-for-creator';

            // Mail for restaurant
            $data = [];
            array_push($data, [
                'subject' => 'Payment is successful!',
                'payment_email' => $payment->email,
                'payment_message' => $payment->message,
                'payment_price' => $payment->price,
                'payment_reference_code' => $payment->referenceCode,
                'sendToCC' => true
            ]);
            $mail->to = $payment->creatorEmail;
            $mail->blade = 'mail.payment-successful-for-creator';
            $mail->save();
        }
        return view('frontend.external-payment-successful');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function externalPaymentFailed(Request $request)
    {
        $errorCode = $request->get('ProcReturnCode');
        $errorMessage = $request->get('ErrMsg');
        return view('frontend.external-payment-failed',
            [
                'errorCode' => $errorCode,
                'errorMessage' => $errorMessage
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function creditCardDetails(Request $request)
    {
        $bookingRecord = new BookingRecord();
        $bookingRecord->platform = $request->fullUrl();
        $bookingRecord->name = $request->firstName;
        $bookingRecord->surname = $request->lastName;
        $bookingRecord->email = $request->email;
        $bookingRecord->country_code = $request->countryCode;
        $bookingRecord->phone_number = $request->phone;
        $bookingRecord->datetime = Carbon::now();
        $bookingRecord->client_id = Auth::check() ? Auth::user()->id : $request->clientUniqueId;
        $bookingRecord->request = json_encode($request->except('_token'));
        $bookingRecord->save();

        if ($request->registerCheck == '1') {
            $emailOnBooking = $request->get('email');
            $isRegisteredUser = User::where('email', $emailOnBooking)->first();

            if (is_null($isRegisteredUser)) {
                $user = new User();
                $user->name = $request->get('firstName');
                $user->surname = $request->get('lastName');
                $user->email = $emailOnBooking;
                $rndPass = $this->commonFunctions->generateRandomString(8);
                $user->password = Hash::make($rndPass);
                $user->countryCode = $request->get('countryCode');
                $user->phoneNumber = $request->get('phone');
                $user->isActive = 1;
                if ($user->save()) {
//                    $this->mailOperations->sendMail(
//                        [
//                            'subject' => 'Welcome to Cityzore!',
//                            'name' => $request->get('firstName'),
//                            'surname' => $request->get('lastName'),
//                            'email' => $request->get('email'),
//                            'password' => $rndPass,
//                            'sendToCC' => false
//                        ],
//                        'mail.register-on-checkout');

                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'subject' => 'Welcome to Cityzore!',
                        'name' => $request->get('firstName'),
                        'surname' => $request->get('lastName'),
                        'email' => $request->get('email'),
                        'sendToCC' => false
                    ]);
                    $mail->to = $request->email;
                    $mail->data = json_encode($data);
                    $mail->blade = 'mail.register-on-checkout';
                    $mail->status = 0;
                    $mail->save();
                }
            }
        }
        if (auth()->check() && auth()->guard('web')->user()->id) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
        } else {
            $clientIp = session()->get('uniqueID');
            $cart = Cart::where('userID', '=', $clientIp)->where('status', '=', 0)->get();
        }
        $totalPrice = 0;
        $totalPriceWOSO = 0;
        $images = [];
        foreach ($cart as $c) {
            if ($request->isCommissionChanged == 1) {
                // change totalCommission and totalPriceWOSO if commission value is changed
                $c->totalCommission = $request->totalCommission;
                $c->totalPriceWOSO = $request->amount;
                $c->save();
                //
            }

            if (!is_null($c->tempTotalPrice)) {
                $totalPrice += $c->tempTotalPrice;
            } else {
                $totalPrice += $c->totalPrice;
            }
            $totalPriceWOSO += $c->totalPriceWOSO;
            $product = Product::findOrFail($c->productID);
            $coverPhoto = $product->coverPhoto;
            $image = ProductGallery::findOrFail($coverPhoto)->src;
            array_push($images, $image);
        }
        $clientid = $request->clientid;
        if (is_null(session()->get('totalPriceWithDiscount'))) {
            $amount = $request->amount;
        } else {
            $amount = 0;
            foreach (json_decode(session()->get('totalPriceWithDiscount'), true) as $key => $value) {
                $amount += (float)$value["newPrice"];
            }
        }
        $currencyCodes = [
            '1' => '840', '2' => '978', '3' => '826', '4' => '949',
            '5' => '756', '6' => '124', '7' => '643', '8' => '784', '9' => '578',
            '10' => '392', '11' => '356', '12' => '203', '13' => '944', '14' => '410',
            '15' => '634', '16' => '764'
        ];
        $oldCurrencyID = array_search($request->currency, $currencyCodes);
        if (gettype($oldCurrencyID) == 'boolean') {
            $oldCurrencyID = 2;
        }
        $amount = Currency::calculateCurrencyForVisitor($amount, $oldCurrencyID);
        $oid = $request->oid;
        $okUrl = $request->okUrl;
        $failUrl = $request->failUrl;
        $islemtipi = $request->islemtipi;
        $taksit = $request->taksit;
        $rnd = $request->rnd;
        $storetype = $request->storetype;
        $refreshtime = $request->refreshtime;
        $lang = $request->lang;
        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $email = $request->email;
        $hotel = $request->hotel;
        $phone = $request->phone;
        $comment = $request->comment;
        $country = $request->countryCode;
        $city = $request->city;
        $streetline = $request->streetline;
        $clientUniqueId = $request->clientUniqueId;
        $storekey = env('PAYMENT_STOREKEY', 'NamyeluS3+-*/');
        $hashstr = $clientid . $oid . $amount . $okUrl . $failUrl . $islemtipi . $taksit . $rnd . $storekey;
        $hash = base64_encode(pack('H*', sha1($hashstr)));
        $currencyID = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2;
        $currencyCode = $currencyCodes[$currencyID];

        $couponID = $request->couponIDHidden;
        $largestContactInfoArray = $request->largestContactInfoArray;

        $translationArray = json_encode([
            "checkYourCreditCardDetails" => __('checkYourCreditCardDetails')
        ]);

        if (count($cart) > 0) {
            return view('frontend.credit-card-details',
                [
                    'clientid' => $clientid,
                    'amount' => $amount,
                    'oid' => $oid,
                    'okUrl' => $okUrl,
                    'failUrl' => $failUrl,
                    'islemtipi' => $islemtipi,
                    'taksit' => $taksit,
                    'rnd' => $rnd,
                    'hash' => $hash,
                    'storetype' => $storetype,
                    'refreshtime' => $refreshtime,
                    'lang' => $lang,
                    'currencyCode' => $currencyCode,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'email' => $email,
                    'hotel' => $hotel,
                    'phone' => $phone,
                    'comment' => $comment,
                    'country' => $country,
                    'city' => $city,
                    'streetline' => $streetline,
                    'clientUniqueId' => $clientUniqueId,
                    'totalPrice' => $totalPrice,
                    'totalPriceWOSO' => $totalPriceWOSO,
                    'images' => $images,
                    'cart' => $cart,
                    'couponID' => $couponID,
                    'largestContactInfoArray' => $largestContactInfoArray,
                    'translationArray' => $translationArray
                ]
            );
        } else {
            abort(404);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function commissions()
    {
        if (auth()->guard('web')->check()) {
            $bookings = Booking::where('status', 0)->where(function ($q) {
                $q->where('userID', auth()->guard('web')->user()->id);
                $q->orWhere('affiliateID', auth()->guard('web')->user()->id);

            })->get()->groupBy(function ($d) {
                return Carbon::parse($d->dateForSort)->format('Y-m');
            });
            //dd($bookings);
            $totalCommission = 0;
            $totalPayment = 0;
            $priceArray = [];
            foreach ($bookings as $date => $bb) {
                $priceArray[$date]['totalPayment'] = 0;
                $priceArray[$date]['totalCommission'] = 0;
                foreach ($bb as $b) {
                    if (is_null($b->affiliateID)) {
                        $cart = Cart::where('referenceCode', $b->reservationRefCode)->where('status', 2)->first();

                        $total_p = $cart->tempTotalPrice ? $cart->tempTotalPrice : $cart->totalPrice;
                        $total_c = $cart->tempCommission ? $cart->tempCommission : $cart->totalCommission;


                        $totalPayment += \App\Currency::calculateCurrencyForVisitorForEveryItem($total_p, $cart->currencyID);
                        $totalCommission += \App\Currency::calculateCurrencyForVisitorForEveryItem($total_c, $cart->currencyID);

                        $priceArray[$date]['totalPayment'] = ($priceArray[$date]['totalPayment']) + \App\Currency::calculateCurrencyForVisitorForEveryItem($total_p, $cart->currencyID);
                        $priceArray[$date]['totalCommission'] = ($priceArray[$date]['totalCommission']) + \App\Currency::calculateCurrencyForVisitorForEveryItem($total_c, $cart->currencyID);
                    } else {
                        $cart = Cart::where('referenceCode', $b->reservationRefCode)->where('status', 2)->first();

                        $total_p = $cart->tempTotalPrice ? $cart->tempTotalPrice : $cart->totalPrice;
                        $total_c = $cart->tempCommission ? $cart->tempCommission : $cart->totalCommission;


                        $totalPayment += \App\Currency::calculateCurrencyForVisitorForEveryItem($cart->totalPrice, $cart->currencyID);
                        $priceArray[$date]['totalPayment'] = ($priceArray[$date]['totalPayment']) + \App\Currency::calculateCurrencyForVisitorForEveryItem($total_p, $cart->currencyID);

                        if (auth()->guard('web')->user()->whereHas('commission', function ($q) use ($cart) {
                            $q->where('optionID', $cart->optionID);

                        })->exists()) {


                            $commission = auth()->guard('web')->user()->commission()->where('optionID', $cart->optionID)->first()->commission ?? 0;


                        } else {
                            $commission = auth()->guard('web')->user()->commission ?? 0;

                        }


                        $commission_price = (\App\Currency::calculateCurrencyForVisitorForEveryItem($total_p, $cart->currencyID) * ($commission / 100));

                        $totalCommission += $commission_price;

                        $priceArray[$date]['totalCommission'] = ($priceArray[$date]['totalCommission']) + $commission_price;

                    }


                }

            }
            return view('frontend.commissions', ['bookings' => $bookings, 'totalCommission' => $totalCommission, 'totalPayment' => $totalPayment, 'priceArray' => $priceArray]);
        }

        return view('auth.login');
    }

    /**
     * @param $optionID
     * @param $bookingID
     * @param $BKNCode
     * @param $request
     */
    public function mailForBookings($optionID, $bookingID, $BKNCode, $request)
    {
        $option = Option::findOrFail($optionID);
        $booking = Booking::findOrFail($bookingID);
        $supplierID = $option->supplierID;
        $supplier = null;
        if ($supplierID != -1) {
            $supplier = Supplier::findOrFail($supplierID);
        } else {
            $supplier = Admin::findOrFail(1);
        }
        $restaurant = Supplier::where('id', $option->rCodeID)->first();
        $traveler = json_decode($booking->travelers, true)[0];

        $commissioner = null;
        if (auth()->guard('web')->check()) {
            $user = auth()->guard('web')->user();
            if (!is_null($user->commission)) {
                $commissioner = $user->companyName;
            }
        }
        if (is_null($option->rCodeID)) {
            //Mail for company
            if ($supplierID == -1) {
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                    'options' => $option->title,
                    'date' => $booking->date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $BKNCode,
                    'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                    'name' => $traveler['firstName'],
                    'surname' => $traveler['lastName'],
                    'from' => Platform::where('id', $booking->platformID)->value('name'),
                    'sendToCC' => true,
                    'commissioner' => $commissioner
                ]);
                $mail->data = json_encode($data);
                $mail->to = $supplier->email;
                $mail->blade = 'mail.booking-successful-for-creator';
                $mail->save();
            } else {
                // Mail for supplier
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                    'options' => $option->title,
                    'date' => $booking->date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $BKNCode,
                    'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                    'name' => $traveler['firstName'],
                    'surname' => $traveler['lastName'],
                    'from' => Platform::where('id', $booking->platformID)->value('name'),
                    'sendToCC' => false,
                    'commissioner' => $commissioner,
                ]);
                $mail->data = json_encode($data);
                $mail->to = $supplier->email;
                $mail->blade = 'mail.booking-successful-for-creator';
                $mail->save();

                // Mail for admin (contact@parisviptrips.com)
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                    'options' => $option->title,
                    'date' => $booking->date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $BKNCode,
                    'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                    'name' => $traveler['firstName'],
                    'surname' => $traveler['lastName'],
                    'sendToCC' => false,
                    'commissioner' => $commissioner,
                    'supplierCompanyName' => $supplier->companyName
                ]);
                $mail->data = json_encode($data);
                $mail->to = 'contact@parisviptrips.com';
                $mail->blade = 'mail.supplier-booking-successful-for-creator';
                $mail->save();
            }
            // Mail for customer
            $mail = new Mails();
            $code = md5(rand());
            $data = [];
            array_push($data, [
                'booking_id' => $booking->id,
                'options' => $option->title,
                'date' => $booking->date,
                'hour' => json_decode($booking->hour, true)[0]['hour'],
                'BKNCode' => $BKNCode,
                'subject' => $BKNCode . ' - ' . 'Booking is successful!',
                'name' => $traveler['firstName'],
                'surname' => $traveler['lastName'],
                'sendToCC' => false,
                'mail_code' => $code,
            ]);
            $mail->data = json_encode($data);
            $mail->code = $code;
            $mail->to = $traveler['email'];
            $mail->blade = 'mail.booking-successful';
            $mail->save();
        } else {
            if ($restaurant->isActive == 1) {
                // Mail for customer
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'options' => $option->title,
                    'date' => $booking->date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $BKNCode,
                    'name' => $traveler['firstName'],
                    'surname' => $traveler['lastName'],
                    'subject' => 'Booking is Pending',
                    'sendToCC' => false
                ]);
                $mail->data = json_encode($data);
                $mail->to = $traveler['email'];
                $mail->blade = 'mail.booking-pending';
                $mail->save();

                // Mail for restaurant
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'options' => $option->title,
                    'date' => $booking->date,
                    'BKNCode' => $BKNCode,
                    'subject' => $BKNCode . ' - ' . 'R-Code is required!',
                    'hash' => md5($restaurant->email . env('HASH_STRING')),
                    'refCode' => $option->referenceCode,
                    'sendToCC' => false
                ]);
                $mail->data = json_encode($data);
                $mail->to = $restaurant->email;
                $mail->blade = 'mail.restaurant';
                $mail->save();
                //Mail for company
                if ($supplierID == -1) {
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'subject' => $BKNCode . ' - ' . 'Booking is Pending !',
                        'name' => $traveler['firstName'],
                        'surname' => $traveler['lastName'],
                        'sendToCC' => true,
                        'commissioner' => !is_null(Auth::guard('web')->user()->commission) ? Auth::guard('web')->user()->companyName : null,
                    ]);
                    $mail->data = json_encode($data);
                    $mail->to = 'contact@parisviptrips.com';
                    $mail->blade = 'mail.booking-pending-for-creator';
                    $mail->save();
                } else {
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'subject' => $BKNCode . ' - ' . 'Booking is Pending !',
                        'name' => $traveler['firstName'],
                        'surname' => $traveler['lastName'],
                        'sendToCC' => false,
                        'commissioner' => !is_null(Auth::guard('web')->user()->commission) ? Auth::guard('web')->user()->companyName : null,
                    ]);
                    $mail->data = json_encode($data);
                    $mail->to = $supplier->email;
                    $mail->blade = 'mail.booking-pending-for-creator';
                    $mail->save();

                    // Mail for admin (contact@parisviptrips.com)
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                        'name' => $traveler['firstName'],
                        'surname' => $traveler['lastName'],
                        'sendToCC' => false,
                        'supplierCompanyName' => $supplier->companyName
                    ]);
                    $mail->data = json_encode($data);
                    $mail->to = 'contact@parisviptrips.com';
                    $mail->blade = 'mail.supplier-booking-successful-for-creator';
                    $mail->save();
                }
            } else {
                if ($supplierID == -1) {
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'subject' => $BKNCode . ' - ' . 'Booking is Pending !',
                        'name' => $traveler['firstName'],
                        'surname' => $traveler['lastName'],
                        'sendToCC' => true,
                        'commissioner' => !is_null(Auth::guard('web')->user()->commission) ? Auth::guard('web')->user()->companyName : null,
                    ]);
                    $mail->data = json_encode($data);
                    $mail->to = 'contact@parisviptrips.com';
                    $mail->blade = 'mail.booking-pending-for-creator';
                    $mail->save();
                } else {
                    // Mail for company
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                        'name' => $traveler['firstName'],
                        'surname' => $traveler['lastName'],
                        'from' => Platform::where('id', $booking->platformID)->value('name'),
                        'sendToCC' => true,
                        'commissioner' => !is_null(Auth::guard('web')->user()->commission) ? Auth::guard('web')->user()->companyName : null,
                    ]);
                    $mail->data = json_encode($data);
                    $mail->to = $supplier->email;
                    $mail->blade = 'mail.booking-successful-for-creator';
                    $mail->save();
                    // Mail for admin (contact@parisviptrips.com)
                    $mail = new Mails();
                    $data = [];
                    array_push($data, [
                        'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                        'options' => $option->title,
                        'date' => $booking->date,
                        'hour' => json_decode($booking->hour, true)[0]['hour'],
                        'BKNCode' => $BKNCode,
                        'subject' => $BKNCode . ' - ' . 'Successful Booking !',
                        'name' => $traveler['firstName'],
                        'surname' => $traveler['lastName'],
                        'sendToCC' => false,
                        'supplierCompanyName' => $supplier->companyName
                    ]);
                    $mail->data = json_encode($data);
                    $mail->to = 'contact@parisviptrips.com';
                    $mail->blade = 'mail.supplier-booking-successful-for-creator';
                    $mail->save();
                }
                // Mail for customer
                $mail = new Mails();
                $code = md5(rand());
                $data = [];
                array_push($data, [
                    'options' => $option->title,
                    'date' => $booking->date,
                    'hour' => json_decode($booking->hour, true)[0]['hour'],
                    'BKNCode' => $BKNCode,
                    'subject' => $BKNCode . ' - ' . 'Booking is successful!',
                    'name' => $traveler['firstName'],
                    'surname' => $traveler['lastName'],
                    'sendToCC' => false,
                    'mail_code' => $code,
                ]);
                $mail->data = json_encode($data);
                $mail->code = $code;
                $mail->to = $traveler['email'];
                $mail->blade = 'mail.booking-successful';
                $mail->save();
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function creditCardDetailsForSharedCart(Request $request)
    {
        $cartIds = json_decode($request->cartIds, true);
        $carts = Cart::whereIn('id', $cartIds)->where('status', 6)->where('shareEmail', $request->email)->get();
        $amount = 0;
        $images = [];
        foreach ($carts as $cart) {
            if (!is_null($cart->tempTotalPrice)) {
                $totalPrice = $cart->tempTotalPrice;
            } else {
                $totalPrice = $cart->totalPrice;
            }
            $amount += $totalPrice;
            $product = Product::findOrFail($cart->productID);
            $coverPhoto = $product->coverPhoto;
            $image = ProductGallery::findOrFail($coverPhoto)->src;
            array_push($images, $image);
        }

        // Payment related parameters
        $clientid = env('PAYMENT_CLIENT_ID', '190217122');
        $okUrl = env('SHARED_OK_URL', 'https://cityzore.com/booking-successful-for-shared-cart');
        $failUrl = env('PAYMENT_FAIL_URL', 'https://cityzore.com/booking-failed');
        $currentUrl = $request->url();
        if (strpos($currentUrl, 'www') != false) {
            $okUrl = 'https://www.cityzore.com/booking-successful-for-shared-cart';
            $failUrl = 'https://www.cityzore.com/booking-failed';
        }
        $oid = $this->refCodeGenerator->invoiceGenerator();
        $rnd = microtime();
        $taksit = '';
        $islemtipi = env('PAYMENT_TYPE', 'Auth');
        $storekey = env('PAYMENT_STOREKEY', 'NamyeluS3+-*/');
        $hashstr = $clientid . $oid . $amount . $okUrl . $failUrl . $islemtipi . $taksit . $rnd . $storekey;
        $hash = base64_encode(pack('H*', sha1($hashstr)));
        $currencyCodes = [
            '1' => '840', '2' => '978', '3' => '826', '4' => '949',
            '5' => '756', '6' => '124', '7' => '643', '8' => '784', '9' => '578',
            '10' => '392', '11' => '356', '12' => '203', '13' => '944', '14' => '410',
            '15' => '634', '16' => '764'
        ];
        $currencyID = $request->currencyID;
        $currencyCode = $currencyCodes[$currencyID];
        $currency = Currency::findOrFail($currencyID);
        $currencySymbol = $currency->iconClass;
        $travelerFirstName = str_replace('|', ' ', $request->tfn);
        $travelerLastName = str_replace('|', ' ', $request->tln);
        $travelerPhone = $request->tp;
        return view('frontend.cc-details-for-shared-cart',
            [
                'carts' => $carts,
                'images' => $images,
                'clientid' => $clientid,
                'amount' => $amount,
                'oid' => $oid,
                'okUrl' => $okUrl,
                'failUrl' => $failUrl,
                'islemtipi' => $islemtipi,
                'taksit' => $taksit,
                'rnd' => $rnd,
                'hash' => $hash,
                'storetype' => '3d_pay',
                'refreshtime' => '5',
                'lang' => 'en',
                'currencyCode' => $currencyCode,
                'currencySymbol' => $currencySymbol,
                'firstName' => $travelerFirstName,
                'lastName' => $travelerLastName,
                'phone' => $travelerPhone,
                'email' => $request->email,
                'cartIds' => $request->cartIds,
                'userID' => $request->userID,
                'currencyID' => $currencyID
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookingSuccessfulForSharedCart(Request $request)
    {
        $cartIds = json_decode($request->get('cartIds'), true);
        $userID = $request->get('userID');
        $email = $request->get('email');
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $currencyID = $request->get('currencyID');
        $booking = null;
        // Big Bus Product Variables
        $bigBusRefCode = null;
        $isOkToBook = ['true'];
        $bbBookingResultJsonData = null;
        //
        $cart = Cart::where('id', $cartIds)->get();
        $totalPrice = 0;
        $timeRelated = new TimeRelatedFunctions();
        $timeZone = 'Europe/Paris';
        $bookings = [];
        foreach ($cart as $c) {
            $dateTimeArr = [];
            if (!is_null($c->productID)) {
                $product = Product::findOrFail($c->productID);
                $timeZone = $product->countryName->timezone;
            }

            foreach (json_decode($c->hour, true) as $h) {
                if (strlen($h['hour']) < 6) {
                    $arr = ['dateTime' => $timeRelated->convertDmyToYmdWithHour($c->date, $h['hour'], $timeZone)];
                    array_push($dateTimeArr, $arr);
                } else {
                    $arr = ['dateTime' => $timeRelated->convertDmyToYmdWithHour($c->date, "00:00", $timeZone)];
                    array_push($dateTimeArr, $arr);
                }
            }

            $optionID = $c->optionID;
            $option = Option::where('id', '=', $optionID)->first();
            $supplier = $option->supplier()->first();
            $supplierID = -1;
            if ($supplier) {
                $supplierID = $supplier->id;
            }
            $totalPrice += $c->totalPrice;
            $optionRefCode = $option->referenceCode;

            $booking = new Booking();
            $booking->affiliateID = $c->affiliate_id;
            $restaurant = null;
            if (is_null($option->rCodeID)) {
                $booking->status = 0;
            } else {
                $restaurant = Supplier::where('isRestaurant', 1)->where('id', $option->rCodeID)->first();
                //$booking->status = $restaurant->isActive == 1 ? 4 : 0;
                $booking->status = $restaurant->isActive == 1 ? 0 : 0;
            }

            $traveler = $this->createTraveler($request, $c);


            // is cart has coupon (start)

            if (!is_null($c->coupon)) {
                $totalNewPrice = 0;
                $booking->coupon = $c->coupon;
                foreach (json_decode($c->coupon, true) as $key => $value) {
                    $totalNewPrice += (float)$value["newPrice"];
                }

            } else {
                $totalNewPrice = $c->totalPrice;
            }

            // is cart has coupon (end)

            $booking->productRefCode = explode('-', $c->referenceCode)[0];
            $booking->optionRefCode = $optionRefCode;
            $booking->reservationRefCode = $c->referenceCode;
            $booking->bookingRefCode = $this->refCodeGenerator->refCodeGeneratorForBooking($c->referenceCode);
            $booking->bookingItems = $c->bookingItems;
            $booking->language = 'tr';
            $booking->travelers = json_encode($traveler);
            $booking->fullName = $firstName . ' ' . $lastName;
            $booking->travelerHotel = $request->hotel;
            $booking->comment = $request->comment;
            $booking->totalPrice = !is_null($c->tempTotalPrice) ? $c->tempTotalPrice : $totalNewPrice;
            $booking->userID = $userID;
            $booking->companyID = $supplier ? $supplierID : -1;
            $booking->date = $c->date;
            $booking->hour = $c->hour;
            $booking->affiliateID = $c->affiliate_id;
            $booking->dateTime = json_encode($dateTimeArr);
            $date = Carbon::createFromFormat('d/m/Y', $c->date);
            $asExpected = $date->format('Y-m-d');
            $booking->dateForSort = $asExpected;
            $booking->platformID = 2;

            $this->useSpecialOfferOnBooking($option, $c->productID, $c->date);

            $avID = [];
            $bAvs = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
            foreach ($bAvs as $bAv) {
                array_push($avID, $bAv->id);
            }
            $booking->avID = json_encode($avID);

            // if option has tootbus api (start)
            if ($c->is_tootbus == 1) {

                $booking->is_tootbus = 1;

                $data = [];
                $data["resellerReference"] = $c->referenceCode;

                $res = $this->tootbusRelated->confirm(json_decode($c->tootbus_booking_response, true)["data"]["uuid"], $data);
                if ($res["status"] === false) {
                    $booking->tootbus_booking_response = json_encode(["type" => "Error confirm", "data" => $res["message"]]);
                }
                $decoded_res = json_decode($res["message"], true);


                $booking->tootbus_booking_response = json_encode(["type" => "confirm Booking", "data" => $decoded_res]);
            }
            // if option has tootbus api (end)
            // If option has big bus external id send booking request to Big Bus API, else resume normal operations
            if (!is_null($option->bigBusID)) {
                $items = json_decode($booking->bookingItems, true);
                $itemsForBigBus = [];
                $categories = ['ADULT' => 'Adult', 'CHILD' => 'Child'];
                foreach ($items as $item) {
                    array_push($itemsForBigBus, ['category' => $categories[$item['category']], 'quantity' => $item['count']]);
                }
                $dateOfTravel = $booking->dateForSort;
                $bookingRefCode = $booking->bookingRefCode;
                $productId = $option->bigBusID;
                $bigBusResponse = $this->bigBusRelated->reserveAndConfirmBooking($productId, $booking, $dateOfTravel, $itemsForBigBus);
                // $bbResponseJson = response()->json($bigBusResponse);
                // $bbResponseJsonData =  $bbResponseJson->getData();
                // $bbStatus = $bbResponseJsonData->status;
                if ($bigBusResponse['status']) {
                    array_push($isOkToBook, 'true');
                    // $bbBookingResult = $bbResponseJsonData->bookingResult;
                    // $bbBookingResultJson = response()->json($bbBookingResult);
                    // $bbBookingResultJsonData = $bbBookingResultJson->getData();
                    // $bigBusRefCode = $bbBookingResultJsonData->bookingReference;
                    $booking->bigBusRefCode = $bigBusResponse['data']['uuid'];
                } else {
                    array_push($isOkToBook, 'false');
                }
            }

            $booking->currencyID = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2;
            if (!in_array('false', $isOkToBook)) {
                if ($booking->save()) {
                    $copyBook = collect($booking->toArray())->except(["tootbus_booking_response"]);
                    event(new StatusLiked("New booking on " . $booking->date . " - " . json_decode($booking->hour, true)[0]['hour'] . ' for ' . $option->referenceCode . ' ', $copyBook, 'CITYZORE_BOOKING'));
                    array_push($bookings, $booking);
                    $c->status = 2;
                    $c->save();
                    $invoice = new Invoice();
                    $invoice->paymentMethod = "CREDIT CARD";
                    $invoice->bookingID = $booking->id;
                    $invoice->companyID = $booking->companyID;
                    //$invoice->referenceCode = $this->refCodeGenerator->invoiceGenerator();
                    $invoice->save();
                    $booking->invoiceID = $invoice->id;
                    $booking->save();
                    // If option has no Big Bus ID continue normal operations, else create new barcodes for Big Bus
                    if (is_null($option->bigBusID) && $c->is_tootbus == 0) {
                        $availabilities = Option::where('referenceCode', '=', $booking->optionRefCode)->first()->avs()->get();
                        foreach ($availabilities as $av) {
                            $ticketType = $av->ticketType()->first();
                            if (!is_null($ticketType)) {
                                $barcode = Barcode::where('ticketType', '=', $ticketType->id)->where('isUsed', '=', 0)
                                    ->where('isReserved', '=', 1)->where('isExpired', '<>', 1)->where('ownerID', '=', $booking->companyID)->get();
                                foreach ($barcode as $b) {
                                    $b->cartID = $c->id;
                                    $b->bookingID = $booking->id;
                                    $b->isUsed = 1;
                                    $b->save();
                                }
                            }
                        }

                        Barcode::where('isUsed', 1)->where('isReserved', 1)
                            ->where('cartID', $c->id)
                            ->where('isExpired', '<>', 1)
                            ->where('ownerID', $supplierID)->take($c->ticketCount)
                            ->update(['bookingID' => $booking->id]);
                    } else {


                        if (!is_null($option->bigBusID)) {  // if bigbus
                            // $bbProducts = $bbBookingResultJsonData->products;
                            // $bbProductsJson = response()->json($bbProducts);
                            // $bbProductsJsonData = $bbProductsJson->getData();
                            // $bbProductsProduct = $bbProductsJsonData->product;
                            $bbItems = $bigBusResponse['data']['unitItems'];
                            foreach ($bbItems as $bbKey => $bbItem) {
                                $barcode = new Barcode();
                                $barcode->ticketType = 3;
                                $barcode->code = $bbItems[$bbKey]['ticket']['deliveryOptions'][0]['deliveryValue'];
                                $barcode->isUsed = 1;
                                $barcode->isReserved = 1;
                                $barcode->isExpired = 0;
                                $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                                $barcode->ownerID = -1;
                                $barcode->cartID = $c->id;
                                $barcode->bookingID = $booking->id;
                                $barcode->save();


                            }
                        } else { // if tootbus

                            $barcode = new Barcode();
                            $barcode->ticketType = 24;
                            $barcode->code = $decoded_res["voucher"]["deliveryOptions"][0]["deliveryValue"];
                            $barcode->isUsed = 1;
                            $barcode->isReserved = 1;
                            $barcode->isExpired = 0;
                            $barcode->endTime = date('d/m/Y', strtotime('+1 years'));
                            $barcode->ownerID = -1;
                            $barcode->cartID = $c->id;
                            $barcode->bookingID = $booking->id;
                            $barcode->save();


                        }
                    }

                    $bookingLogs = new BookingLog();
                    $bookingLogs->userID = $c->userID;
                    $bookingLogs->cartID = $c->id;
                    $bookingLogs->optionID = $c->optionID;
                    $bookingLogs->code = 'Success';
                    $bookingLogs->processID = null;
                    $bookingLogs->save();
                    $this->commonFunctions->changeSoldCount($c, '+');
                }
            } else {
                $bookingLogs = new BookingLog();
                $bookingLogs->userID = $c->userID;
                $bookingLogs->cartID = $c->id;
                $bookingLogs->optionID = $c->optionID;
                $bookingLogs->code = 'Success';
                $bookingLogs->processID = null;
                $bookingLogs->save();
                return view('frontend.booking-failed',
                    [
                        'errorCode' => '-1',
                        'errorMessage' => 'Payment is successfully made but Big Bus API response was not successful. Please contact us to solve this issue.'
                    ]
                );
            }
        }

        $this->mailForBookings($optionID, $booking->id, $this->apiRelated->explodeBookingRefCode($booking->bookingRefCode)['bkn'], $request);

        $couponID = $request->couponIDHidden;

        if ($couponID) {
            $coupon = Coupon::findOrFail($couponID);
            $coupon->countOfUsing += 1;
            $coupon->save();
        }

        $checkIfBookingHasTicketType = 0;
        foreach ($bookings as $b) {
            $avs = $b->bookingOption->avs;
            foreach ($avs as $av) {
                $ticketTypes = $av->ticketType;
                foreach ($ticketTypes as $ticketType) {
                    if ($ticketType)
                        $checkIfBookingHasTicketType = 1;
                }
            }
        }
        return view('frontend.booking-successful',
            [
                'option' => $option,
                'categoryAndCountInfo' => $this->apiRelated->getCategoryAndCountInfo($booking->bookingItems),
                'bookings' => $bookings,
                'checkIfBookingHasTicketType' => $checkIfBookingHasTicketType
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function myCoupons()
    {
        if (Auth::guard('web')->check()) {
            $coupons = Coupon::where('endingDate', '>=', date("Y-m-d"))->where('type', 4)
                ->orWhere('endingDate', '>=', date("Y-m-d"))->where('type', 5)
                ->orwhere(function ($data) {
                    $data->where('type', 6)->where('lastSelect', auth()->guard('web')->user()->id)->where('endingDate', '>=', date("Y-m-d"));
                })
                ->get()->filter(function ($q) {
                    if ($q->maxUsability > $q->countOfUsing)
                        return true;
                    return false;
                });

            return view('frontend.coupons', ['coupons' => $coupons]);
        }
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langCodeForUrl = $langCode == 'en' ? '' : $langCode;

        return redirect($langCodeForUrl . '/login');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function myWishlist()
    {
        if (Auth::guard('web')->check()) {
            $wishlists = Wishlist::where('userID', auth()->guard('web')->user()->id)->get();

            return view('frontend.wishlists', ['wishlists' => $wishlists]);
        }

        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langCodeForUrl = $langCode == 'en' ? '' : $langCode;

        return redirect($langCodeForUrl . '/login');
    }

    /**
     * @param Request $request
     * @param $cart
     * @return array
     */
    public function createTraveler(Request $request, $cart)
    {
        $largestContactInfoArray = $request->largestContactInfoArray;
        $contactForAllTravelers = Option::findOrFail($cart->optionID)->contactForAllTravelers;
        $totalTicketCount = $request->totalTicketCount;
        $traveler = [];
        if (count(is_countable(json_decode($largestContactInfoArray, true)) ? json_decode($largestContactInfoArray, true) : []) > 0) {
            if ($contactForAllTravelers == 1) {
                for ($k = 0; $k < $totalTicketCount; $k++) {
                    for ($i = 0; $i < count(is_countable(json_decode($largestContactInfoArray, true)) ? json_decode($largestContactInfoArray, true) : []); $i++) {
                        $name = json_decode($request->largestContactInfoArray, true)[$i]['name'] . $k;
                        $email = 'email' . $k;
                        $firstName = 'firstName' . $k;
                        $lastName = 'lastName' . $k;
                        $phone = 'phone' . $k;
                        $countryCode = 'countryCode' . $k;
                        $splitTitle = preg_split('\' +\'', json_decode($largestContactInfoArray, true)[$i]['title']);
                        if (count($splitTitle) == 1) {
                            $title = ucfirst($splitTitle[$i]);
                        } elseif (count($splitTitle) > 1) {
                            for ($j = 1; $j < count($splitTitle); $j++) {
                                $titleUcFirst = implode((array)ucfirst($splitTitle[$j - 1])) . implode(array(ucfirst($splitTitle[$j])));
                                $title = $splitTitle[0] . $titleUcFirst;
                            }
                        }

                        if (json_decode($largestContactInfoArray, true)[$i]['optionID'] == $cart->optionID) {
                            $travelers[$title] = $request->$name;
                        }
                        $travelers["email"] = $request->$email;
                        $travelers['firstName'] = $request->$firstName;
                        $travelers['lastName'] = $request->$lastName;
                        $travelers['phoneNumber'] = $request->$phone;

                        array_push($traveler, $travelers);
                    }
                }
            } else {
                for ($i = 0; $i < count(is_countable(json_decode($largestContactInfoArray, true)) ? json_decode($largestContactInfoArray, true) : []); $i++) {
                    $name = json_decode($request->largestContactInfoArray, true)[$i]['name'];
                    $splitTitle = preg_split('\' +\'', json_decode($largestContactInfoArray, true)[$i]['title']);
                    if (count($splitTitle) == 1) {
                        $title = ucfirst($splitTitle[$i]);
                    } elseif (count($splitTitle) > 1) {
                        for ($j = 1; $j < count($splitTitle); $j++) {
                            $titleUcFirst = implode((array)ucfirst($splitTitle[$j - 1])) . implode(array(ucfirst($splitTitle[$j])));
                            $title = $splitTitle[0] . $titleUcFirst;
                        }
                    }

                    if (json_decode($largestContactInfoArray, true)[$i]['optionID'] == $cart->optionID) {
                        $travelers[$title] = $request->$name;
                    }

                    $travelers["email"] = $request->email;
                    $travelers['firstName'] = $request->firstName;
                    $travelers['lastName'] = $request->lastName;
                    $travelers['phoneNumber'] = $request->countryCode . $request->phone;

                    array_push($traveler, $travelers);
                }
            }
        } else {
            if ($contactForAllTravelers == 1) {
                for ($i = 0; $i < $totalTicketCount; $i++) {
                    $travelers["email"] = $request->email . $i;
                    $travelers['firstName'] = $request->firstName . $i;
                    $travelers['lastName'] = $request->lastName . $i;
                    $travelers['phoneNumber'] = $request->countryCode . $i . $request->phone . $i;

                    array_push($traveler, $travelers);
                }
            } else {
                $travelers["email"] = $request->email;
                $travelers['firstName'] = $request->firstName;
                $travelers['lastName'] = $request->lastName;
                $travelers['phoneNumber'] = $request->countryCode . $request->phone;
            }

            array_push($traveler, $travelers);
        }

        return $traveler;
    }

    public function bookingRecord(Request $request)
    {
        $bookingRecord = new BookingRecord();
        $bookingRecord->name = $request->name;
        $bookingRecord->surname = $request->surname;
        $bookingRecord->email = $request->email;
        $bookingRecord->country_code = $request->country_code;
        $bookingRecord->phone_number = $request->phone_number;
        $bookingRecord->datetime = Carbon::now();
        $bookingRecord->client_id = $request->client_id;
        $bookingRecord->save();
    }

    public function useSpecialOfferOnBooking($option, $productID, $bookingDate)
    {
        $specialOffer = SpecialOffers::where('productID', $productID)->where('optionID', $option->id)->first();
        $bookingOfferLocation = [];

        if ($specialOffer) {
            $dtSpecial = json_decode($specialOffer->dateTimes, true);
            if (!is_null($dtSpecial)) {
                foreach ($dtSpecial as $ind => $dt) {
                    if (Carbon::createFromFormat('d/m/Y', $dt['day'])->timestamp >= Carbon::today()->timestamp) {
                        if ($dt['isActive'] == 1) {
                            if ($dt['day'] == $bookingDate) {
                                $bookingOfferLocation[$dt['day']] = "dateTimes." . $ind;
                                break;
                            }
                        }
                    }
                }
            }
            $rdSpecial = json_decode($specialOffer->randomDay, true);
            if (!is_null($rdSpecial)) {
                foreach ($rdSpecial as $ind => $rd) {
                    if (Carbon::createFromFormat('d/m/Y', $rd['day'])->timestamp >= Carbon::today()->timestamp) {
                        if ($rd['isActive'] == 1) {
                            if ($rd['day'] == $bookingDate) {
                                $bookingOfferLocation[$rd['day']] = "randomDay." . $ind;
                                break;
                            }
                        }
                    }
                }
            }
            $wdSpecial = json_decode($specialOffer->weekDay, true);
            $availabilities = $option->avs()->get();
            $minDate = '';
            $maxDate = '';
            foreach ($availabilities as $availability) {
                $minDate = $availability->avdates()->min('valid_from');
                $maxDate = $availability->avdates()->max('valid_to');
            }
            $minDate = strtotime($minDate);
            $maxDate = strtotime($maxDate);

            if (!is_null($wdSpecial)) {
                foreach ($wdSpecial as $ind => $wd) {
                    for ($i = strtotime(ucfirst($wd['dayName']), $minDate); $i <= $maxDate; $i = strtotime('+1 week', $i)) {
                        if ($i >= Carbon::today()->timestamp) {
                            if ($wd['isActive'] == 1) {
                                if ($i == Carbon::createFromFormat('d/m/Y', $bookingDate)->timestamp) {
                                    $bookingOfferLocation[date('d/m/Y', $i)] = "weekDay." . $ind;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            $drSpecial = json_decode($specialOffer->dateRange, true);
            if (!is_null($drSpecial)) {
                foreach ($drSpecial as $ind => $dr) {
                    $from = $dr['from'];
                    $to = $dr['to'];
                    $datePeriod = $this->timeRelatedFunctions->returnDates($from, $to);
                    foreach ($datePeriod as $date) {
                        if ($date->getTimestamp() >= Carbon::today()->timestamp) {
                            if ($dr['isActive'] == 1) {
                                if ($date->getTimestamp() == Carbon::createFromFormat('d/m/Y', $bookingDate)->timestamp) {
                                    $bookingOfferLocation[$date->format('d/m/Y')] = "dateRange." . $ind;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($bookingOfferLocation as $offerItem) {
            $col = explode(".", $offerItem)[0];
            $index = explode(".", $offerItem)[1];

            $dtCol = json_decode($specialOffer->$col, true);
            if (isset($dtCol[$index]["maximumUsability"]) && $dtCol[$index]["maximumUsability"] > 0) {
                $dtCol[$index]["used"]++;
                if ($dtCol[$index]["used"] >= $dtCol[$index]["maximumUsability"])
                    $dtCol[$index]["isActive"] = 0;

                $specialOffer->$col = json_encode($dtCol);
                $specialOffer->save();
            }
        }
    }

    public function authorizeNetAPI(Request $request)
    {
        $payment = new PaymentController();

        $validation = Validator::make($request->all(), $payment->validation, $payment->message);

        if ($validation->fails()) {
            return back()->withErrors($validation->errors());
        }

        try {

            $payment->setNumber($request->cc_number);
            $payment->setExpiryMonth($request->expiry_month);
            $payment->setExpiryYear($request->expiry_year);
            $payment->setCvv($request->cvv);
            $payment->setCreditCard();
            $payment->setAmount($request->amount);
            $payment->setCurrency('USD');
            $payment->setTransactionID(rand(100000000, 999999999));
            $resp = $payment->authorize();

            dd($resp);
        } catch (\Exception $exception) {

            return redirect()->back()->withInput($request->input())->with('error', $exception->getMessage());

        }
    }

}
