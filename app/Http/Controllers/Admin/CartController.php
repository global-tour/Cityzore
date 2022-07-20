<?php

namespace App\Http\Controllers\Admin;

use App\Cart;
use App\Events\StatusLiked;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Http\Controllers\Helpers\MailOperations;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Mails;
use App\Option;
use App\Product;
use App\ProductGallery;
use App\Barcode;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Nahid\JsonQ\Jsonq;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\TootbusRelated;
use App\Commission;
use App\Currency;
use App\User as Uuser;


class CartController extends Controller
{

    public $timeRelatedFunctions;
    public $apiRelated;
    public $tootbusRelated;
    public $commonFunctions;
    public $mailOperations;

    public function __construct()
    {
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->apiRelated = new ApiRelated();
        $this->tootbusRelated = new TootbusRelated();
        $this->commonFunctions = new CommonFunctions();
        $this->mailOperations = new MailOperations();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('frontend.cart');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCart()
    {
        $uniqueID = Session::get('uniqueID');
        if (Auth::user()) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
        } else {
            $cart = Cart::where('userID', '=', $uniqueID)->where('status', '=', 0)->get();
        }


        $itemCount = count($cart);
        $totalPrice = 0;
        return view('frontend.cart', ['totalPrice' => $totalPrice, 'cart' => $cart, 'itemCount' => $itemCount, 'uniqueID' => $uniqueID]);
    }

    /**
     * Adds items to cart
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function addToCart(Request $request)
    {


        $option = Option::findOrFail($request->productOption);
        $supplierID = $option->supplierID;
        $productID = $request->productID;
        $product = Product::findOrFail($productID);
        $optionRefCode = $option->referenceCode;
        $productRefCode = $product->referenceCode;
        $refCodeGenerator = new refCodeGenerator();
        $cartRefCode = $refCodeGenerator->refCodeGeneratorForCart();
        $availability = $option->avs()->get();
        if (is_null((session()->get('uniqueID')))) {
            $randomNumber = $this->commonFunctions->getRandomNumber(10);
            session()->put('uniqueID', $randomNumber);
        }
        $uniqueID = Session::get('uniqueID');
        $ticketTypes = [];
        $commissionAmount = 0;
        $commission = 0;
        $specials = $request->specials == 'null' ? null : $request->specials;


        if ($option->supplierID != -1) {
            $supp = \App\Supplier::findOrFail($option->supplierID);


        }

        if (auth()->check() && auth()->guard('web')->user()->isActive == 1 && !is_null(auth()->guard('web')->user()->commission)) {
            $optionHasCommission = Commission::where('optionID', $option->id)->where('commissionerID', auth()->guard('web')->user()->id);
            if ($optionHasCommission->count() == 1) {


                $commission = $optionHasCommission->first()->commission;

                if ($option->supplierID != -1) {
                    if ($supp->comission) {

                        if ($supp->comission < $commission) {
                            $commission = $supp->comission;
                        }

                    }
                }

            } else {


                $commission = Auth::guard('web')->user()->commission;


                if ($option->supplierID != -1) {
                    if ($supp->commissioner_commission) {


                        $commission = $supp->commissioner_commission;


                    }
                }

            }
        }

        foreach ($availability as $av) {
            array_push($ticketTypes, $av->ticketType()->first());
        }
        $image = ProductGallery::where('id', '=', $product->coverPhoto)->first()->src;
        $ticketCount = $request->adultCount + $request->youthCount + $request->childCount + $request->infantCount + $request->euCitizenCount;

        $totalPriceWOSO = $request->totalPriceWOSO;
        $totalPrice = $request->totalPrice;

        // Pricing calculate for per person type.
        if (auth()->check() && auth()->guard('web')->user()->isActive == 1) {
            $commissionAmount = ($totalPrice * $commission / 100);

            if (auth()->guard('web')->user()->commissionType == 'money')
                $commissionAmount = $commission;
        }


        if (session()->has('affiliate_user_id')) {
            $user_aff_id = session()->get('affiliate_user_id');
            $user_aff = Uuser::findOrFail($user_aff_id);

            $affComOpt = Commission::where('optionID', $option->id)->where('commissionerID', $user_aff_id);

            if ($affComOpt->count() == 1) {
                $commission = $optionHasCommission->first()->commission;
                $commissionAmount = ($totalPrice * $commission / 100);

                /*     if($option->supplierID != -1){
                      if($supp->comission){

                      if($supp->comission < $commission){
                        $commission = $supp->comission;
                     }

                     }
                 }*/


            } else {


                $commission = $user_aff->commission;
                $commissionAmount = ($totalPrice * $commission / 100);


                /*       if($option->supplierID != -1){
                    if($supp->commissioner_commission){


                      $commission = $supp->commissioner_commission;


                   }
                   }*/


            }


        }


        if (Auth::guard('web')->check()) {
            $userID = Auth::guard('web')->user()->id;
        } else {
            $userID = $uniqueID;
        }

        $bookingItems = [];

        $hours = [];
        foreach ($request->obj as $obj) {
            if (array_key_exists('hour', $obj)) {
                $hour = ["hour" => $obj['hour']];
            } else {
                $hour = ['hour' => '00:00'];
            }
            array_push($hours, $hour);
        }

        $oldCart = Cart::where('optionID', '=', $request->productOption)
            ->where('hour', '=', json_encode($hours))
            ->where('date', '=', $request->obj[0]['day'])
            ->where('status', '=', 0)
            ->where('userID', '=', $userID)
            ->first();

        // if new cart and old carts have same date and hour, they will be added.
        if ($oldCart) {
            $bookingItemsFromDB = json_decode($oldCart->bookingItems);
            $jsonq = new Jsonq();
            if ($request->adultCount) {
                if (count($bookingItemsFromDB) > 0) {
                    $res = $jsonq->json($oldCart->bookingItems);
                    $res->where('category', '=', 'ADULT')->get();
                    if ($res->count() > 0) {
                        $key = array_keys(json_decode($res->toJson(), true))[0];
                        $bookingItemsFromDB[$key]->count = json_encode($bookingItemsFromDB[$key]->count + $request->adultCount);
                    } else {
                        array_push($bookingItemsFromDB, ['category' => 'ADULT', 'count' => $request->adultCount]);
                    }
                    $res->reset();
                }


            }

            if ($request->euCitizenCount) {
                if (count($bookingItemsFromDB) > 0) {
                    $res = $jsonq->json($oldCart->bookingItems);
                    $res->where('category', '=', 'EU_CITIZEN')->get();
                    if ($res->count() > 0) {
                        $key = array_keys(json_decode($res->toJson(), true))[0];
                        $bookingItemsFromDB[$key]->count = json_encode($bookingItemsFromDB[$key]->count + $request->euCitizenCount);
                    } else {
                        array_push($bookingItemsFromDB, ['category' => 'EU_CITIZEN', 'count' => $request->euCitizenCount]);
                    }
                    $res->reset();
                }


                $oldCart->save();
            }
            if ($request->youthCount) {
                if (count($bookingItemsFromDB) > 0) {
                    $res = $jsonq->json($oldCart->bookingItems);
                    $res->where('category', '=', 'YOUTH')->get();
                    if ($res->count() > 0) {
                        $key = array_keys(json_decode($res->toJson(), true))[0];
                        $bookingItemsFromDB[$key]->count = json_encode($bookingItemsFromDB[$key]->count + $request->youthCount);
                    } else {
                        array_push($bookingItemsFromDB, ['category' => 'YOUTH', 'count' => $request->youthCount]);
                    }
                    $res->reset();
                }


                $oldCart->save();
            }
            if ($request->childCount) {
                if (count($bookingItemsFromDB) > 0) {
                    $res = $jsonq->json($oldCart->bookingItems);
                    $res->where('category', '=', 'CHILD')->get();
                    if ($res->count() > 0) {
                        $key = array_keys(json_decode($res->toJson(), true))[0];
                        $bookingItemsFromDB[$key]->count = json_encode($bookingItemsFromDB[$key]->count + $request->childCount);
                    } else {
                        array_push($bookingItemsFromDB, ['category' => 'CHILD', 'count' => $request->childCount]);
                    }
                    $res->reset();
                }


                $oldCart->save();
            }
            if ($request->infantCount) {
                if (count($bookingItemsFromDB) > 0) {
                    $res = $jsonq->json($oldCart->bookingItems);
                    $res->where('category', '=', 'INFANT')->get();
                    if ($res->count() > 0) {
                        $key = array_keys(json_decode($res->toJson(), true))[0];
                        $bookingItemsFromDB[$key]->count = json_encode($bookingItemsFromDB[$key]->count + $request->infantCount);
                    } else {
                        array_push($bookingItemsFromDB, ['category' => 'INFANT', 'count' => $request->infantCount]);
                    }
                    $res->reset();
                }


                $oldCart->save();
            }

            $oldCart->ticketCount += $ticketCount;
            $oldCart->totalPrice += $totalPrice;
            $oldCart->totalPriceWOSO += $totalPriceWOSO;
            $oldCart->totalCommission += $commissionAmount;
            $oldCart->bookingItems = json_encode($bookingItemsFromDB);
            $oldCart->specials = $specials;
            $oldCart->currencyID = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2;
            if (session()->has('affiliate_user_id')) {

                    $oldCart->affiliate_id = session()->get('affiliate_user_id');


                // reserve update if cart has tootbus (start)


                if ($oldCart->is_tootbus == 1) {


                    // set tootbus booking reserve here
                    $data = [];
                    $data["productId"] = $option->tootbus->tootbus_product_id;
                    $data["optionId"] = !empty($option->tootbus->tootbus_option_id) ? $option->tootbus->tootbus_option_id : "DEFAULT";
                    $data["availabilityId"] = $oldCart->dateTime;
                    $data["unitItems"] = [];
                    $units = ["ADULT" => "adult", "CHILD" => "child", "YOUTH" => "youth", "INFANT" => "infant"];

                    foreach ($bookingItemsFromDB as $item) {
                        for ($i = 0; $i < $item->count; $i++) {
                            $data["unitItems"][] = ["unitId" => $units[$item->category]];
                        }
                    }
                    //return response()->json(["status" => 0, "data" => $data]);

                    $reserveResponse = $this->tootbusRelated->reserveUpdate(json_decode($oldCart->tootbus_booking_response, true)["data"]["uuid"], $data);

                    if ($reserveResponse["status"] === false) {
                        return response()->json(["status" => 0, "message" => $reserveResponse["message"]]);
                    }

                    $oldCart->tootbus_booking_response = json_encode(["type" => "update reserve", "data" => json_decode($reserveResponse["message"], true)]);
                }


                // reserve update if cart has tootbus (end)


                $oldCart->save();
            }
            $oldCart->save();


            foreach ($ticketTypes as $ticketType) {
                if (!is_null($ticketType)) {
                    Barcode::where('isUsed', 0)->where('isReserved', 0)
                        ->where('isExpired', 0)->where('ownerID', $supplierID)
                        ->where('ticketType', $ticketType->id)->take($ticketCount)
                        ->update(['cartID' => $oldCart->id, 'isReserved' => 1]);
                }
            }
        } else {
            $cart = new Cart();
            $cart->referenceCode = $productRefCode . '-' . $optionRefCode . $cartRefCode;
            $cart->totalPrice = $totalPrice;
            $cart->totalPriceWOSO = $totalPriceWOSO;
            $cart->specials = $specials;
            $cart->totalCommission = $commissionAmount;
            $cart->maxCommission = $commissionAmount;
            $cart->status = 0;
            $cart->userID = $userID;
            $cart->isGYG = 0;
            $cart->optionID = $option->id;
            $cart->productID = $productID;
            $cart->date = $request->obj[0]['day'];


            $cart->currencyID = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2;


            if (session()->has('affiliate_user_id')) {

                    $cart->affiliate_id = session()->get('affiliate_user_id');

            }


            $hour = [];
            foreach ($request->obj as $obj) {
                if (array_key_exists('hour', $obj)) {
                    $hourArray = ['hour' => $obj['hour']];
                    array_push($hour, $hourArray);
                }
            }
            $cart->hour = json_encode($hour);
            $cart->ticketCount = $request->adultCount + $request->youthCount + $request->childCount + $request->infantCount + $request->euCitizenCount;

            if ($request->adultCount) {
                $adult = ['category' => 'ADULT', 'count' => $request->adultCount];
                array_push($bookingItems, $adult);
            }
            if ($request->euCitizenCount) {
                $euCitizen = ['category' => 'EU_CITIZEN', 'count' => $request->euCitizenCount];
                array_push($bookingItems, $euCitizen);
            }
            if ($request->youthCount) {
                $youth = ['category' => 'YOUTH', 'count' => $request->youthCount];
                array_push($bookingItems, $youth);
            }
            if ($request->childCount) {
                $child = ['category' => 'CHILD', 'count' => $request->childCount];
                array_push($bookingItems, $child);
            }
            if ($request->infantCount) {
                $infant = ['category' => 'INFANT', 'count' => $request->infantCount];
                array_push($bookingItems, $infant);
            }


            if (!($request->obj[0]['id'] == "undefined" || $request->obj[0]['id'] == null)) {
                $cart->dateTime = trim($request->obj[0]['id']);
                $cart->is_tootbus = 1;
                // set tootbus booking reserve here
                $data = [];
                $data["productId"] = $option->tootbus->tootbus_product_id;
                $data["optionId"] = !empty($option->tootbus->tootbus_option_id) ? $option->tootbus->tootbus_option_id : "DEFAULT";
                $data["availabilityId"] = $cart->dateTime;
                $data["unitItems"] = [];
                $units = ["ADULT" => "adult", "CHILD" => "child", "YOUTH" => "youth", "INFANT" => "infant"];

                foreach ($bookingItems as $item) {
                    for ($i = 0; $i < $item["count"]; $i++) {
                        $data["unitItems"][] = ["unitId" => $units[$item["category"]]];
                    }
                }
                //return response()->json(["status" => 0, "data" => $data]);

                $reserveResponse = $this->tootbusRelated->reserve($data);

                if ($reserveResponse["status"] === false) {
                    return response()->json(["status" => 0, "message" => $reserveResponse["message"]]);
                }

                $cart->tootbus_booking_response = json_encode(["type" => "Reserve", "data" => json_decode($reserveResponse["message"], true)]);
            }

            $cart->bookingItems = json_encode($bookingItems);

            $ticketCountDecrement = $this->ticketCountDecrement($request);
            if($ticketCountDecrement === false){
                return response()->json(['status' => 0, 'message' => 'Insufficient Number of Tickets']);
            }
            if ($cart->save()) {
                foreach ($ticketTypes as $ticketType) {
                    if (!is_null($ticketType)) {
                        if($ticketType->id == 30) {
                            Barcode::where('isUsed', 0)->where('isReserved', 0)
                                ->where('isExpired', 0)->where('ownerID', $supplierID)
                                ->where('ticketType', 30)->take($request->adultCount)
                                ->update(['cartID' => $cart->id, 'isReserved' => 1]);

                            Barcode::where('isUsed', 0)->where('isReserved', 0)
                                ->where('isExpired', 0)->where('ownerID', $supplierID)
                                ->where('ticketType', 31)->take($request->childCount)
                                ->update(['cartID' => $cart->id, 'isReserved' => 1]);
                        } else {
                            Barcode::where('isUsed', 0)->where('isReserved', 0)
                                ->where('isExpired', 0)->where('ownerID', $supplierID)
                                ->where('ticketType', $ticketType->id)->take($ticketCount)
                                ->update(['cartID' => $cart->id, 'isReserved' => 1]);
                        }
                    }
                }
            }
        }



        $cartItems = Cart::where('userID', '=', $userID)->where('status', '=', 0)->get();

        $translationArray = json_encode([
            "ADULT" => __("adult"),
            "YOUTH" => __("youth"),
            "CHILD" => __("child"),
            "INFANT" => __('infant'),
            "EUCITIZEN" => __('euCitizen'),
            'error' => __('addToCartError')
        ]);

        return response()->json(
            [
                'cartItems' => $cartItems,
                'count' => count($cartItems),
                'image' => $image,
                'optionTitle' => $option->title,
                'adultCount' => $request->adultCount,
                'youthCount' => $request->youthCount,
                'childCount' => $request->childCount,
                'infantCount' => $request->infantCount,
                'euCitizenCount' => $request->euCitizenCount,
                'totalPrice' => $totalPrice,
                'totalPriceWOSO' => $totalPriceWOSO,
                'uniqueID' => $uniqueID,
                'translationArray' => $translationArray,
                'temporaryTicketCount' => $ticketCountDecrement['temporaryTicketCount'] ?? 0,
                'totalTicketCount' => $ticketCountDecrement['totalTicketCount'] ?? 0
            ]
        );
    }

    /**
     * Deletes items from cart
     *
     * @param $langCode
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function deleteItemFromCart($langCode, $id, Request $request)
    {

        $this->ticketCountIncrement($request, $id);
        $cart = Cart::findOrFail($id);


        // starting security
        if (auth()->check()) {
            if ((int)$cart->userID !== auth()->user()->id)
                return redirect()->back()->with(['error' => 'You cant delete another users cart item!']);
        } else {

            if ((int)session()->get('uniqueID') !== (int)$cart->userID)
                return redirect()->back()->with(['error' => 'You cant delete another users cart item!']);
        }


        // ending security


        // Delete İtem if cart has tootbus (start)


        if ($cart->is_tootbus == 1) {


            // set tootbus booking reserve here
            $data = [];
            $data["reason"] = "Removed From Cart";


            $reserveResponse = $this->tootbusRelated->delete(json_decode($cart->tootbus_booking_response, true)["data"]["uuid"], $data);

            if ($reserveResponse["status"] === false) {
                $cart->tootbus_booking_response = json_encode(["type" => "error delete", "data" => $reserveResponse["message"]]);
            }

            $cart->tootbus_booking_response = json_encode(["type" => "delete", "data" => json_decode($reserveResponse["message"], true)]);
        }


        // Delete İtem if cart has tootbus (end)


        $cart->status = 1;
        if ($cart->save()) {

            Barcode::where('isUsed', 0)->where('isReserved', 1)
                ->where('isExpired', 0)
                ->where('cartID', $cart->id)->take($cart->ticketCount)
                ->update(['cartID' => null, 'isReserved' => 0]);
        }

        $previousURL = url()->previous();
        $langCodeForUrl = $langCode == 'en' ? '' : $langCode;

        if ($previousURL == env('APP_URL') . '/' . $langCodeForUrl . '/' . $this->commonFunctions->getRouteLocalization('credit-card-details')) {
            return redirect($langCodeForUrl . '/');
        }

        return redirect($langCodeForUrl . '/' . $this->commonFunctions->getRouteLocalization('cart'));
    }

    /**
     * Decrements ticket count after adding item to cart
     *
     * @param Request $request
     * @return array
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function ticketCountDecrement(Request $request)
    {
        $option = Option::findOrFail($request->productOption);
        $ticketRequest = $request->obj;
        $jsonq = $this->apiRelated->prepareJsonQ();
        $allCategories = ["adult", "youth", "child", "infant", "euCitizen"];
        $totalTicketCount = $request->adultCount + $request->youthCount + $request->childCount + $request->infantCount + $request->euCitizenCount;
        $temporaryTicketCount = $totalTicketCount;
        $availability = $option->avs()->get();
        $pricing = $option->pricings()->first();
        $ignoredCategories = json_decode($pricing->ignoredCategories, true);
        if (!is_null($ignoredCategories)) {
            foreach ($allCategories as $category) {
                if (in_array($category, $ignoredCategories)) {
                    $categoryName = $category . 'Count';
                    $temporaryTicketCount = $temporaryTicketCount - $request->$categoryName;
                }
            }
        }

        foreach ($availability as $av) {
            // if ticket is limitless don't use this function
            if ($av->isLimitless == 0) {
                $ticketHourlyDatabase = json_decode($av->hourly, true);
                $ticketDailyDatabase = json_decode($av->daily, true);
                $ticketDateRangeDatabase = json_decode($av->dateRange, true);
                $ticketBarcodeDatabase = json_decode($av->barcode, true);
                if ($av->avTicketType == 1 && count($ticketHourlyDatabase) > 0) {
                    foreach ($ticketRequest as $tic) {
                        $res = $jsonq->json($av->hourly);
                        $result = $res->where('day', '=', $tic['day'])->where('hour', '=', $tic['hour'])->get();
                        if (count($result) == 1) {
                            $key = key($result);
                            $ticketHourlyDatabase[$key]['ticket'] = $ticketHourlyDatabase[$key]['ticket'] - $temporaryTicketCount;
                            $ticketState = $ticketHourlyDatabase[$key]['ticket'];
                            if($ticketState < 0) return false;
                            $av->hourly = json_encode($ticketHourlyDatabase);
                            $av->save();
                            $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($tic['day'], $tic['hour'], 'Europe/Paris');
                            if ($ticketState < 5 && $isDateTimeValid) {
                                $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($tic['day'], $tic['hour'], 'Europe/Paris');
                                $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                foreach ($optionRefCodes as $orc) {
                                    $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                }
                            }
                            if ($ticketHourlyDatabase[$key]['ticket'] <= 5) {
                                event(new StatusLiked('Your last ' . $ticketHourlyDatabase[$key]['ticket'] . ' tickets left on ' . $tic['day'] . '-' . $tic['hour'] . ' for ' . $option->referenceCode, $option, 'TICKET_ALERT'));
                            }
                        }
                        $res->reset();
                    }
                }

                if ($av->avTicketType == 2 && count($ticketDailyDatabase) > 0) {
                    $res = $jsonq->json($av->daily);
                    $result = $res->where('day', '=', $ticketRequest[0]['day'])->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $ticketDailyDatabase[$key]['ticket'] -= $temporaryTicketCount;
                        $ticketState = $ticketDailyDatabase[$key]['ticket'];
                        if($ticketState < 0) return false;
                        $av->daily = json_encode($ticketDailyDatabase);
                        $av->save();
                        $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($ticketRequest[0]['day'], '00:00', 'Europe/Paris');
                        if ($ticketState < 5 && $isDateTimeValid) {
                            $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($ticketRequest[0]['day'], '00:00', 'Europe/Paris');
                            $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                            foreach ($optionRefCodes as $orc) {
                                $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                            }
                        }
                        if ($ticketDailyDatabase[$key]['ticket'] <= 5) {
                            event(new StatusLiked('Your last ' . $ticketDailyDatabase[$key]['ticket'] . ' tickets left on ' . $ticketRequest[0]['day'] . ' for ' . $option->referenceCode, $option, 'TICKET_ALERT'));
                        }
                    }
                    $res->reset();
                }

                if ($av->avTicketType == 3 && count($ticketDateRangeDatabase) > 0) {
                    $selectedDate = $ticketRequest[0]['day'];
                    $res = $jsonq->json($av->dateRange);
                    $result = $res->where('dayFrom', 'dateLte', $selectedDate)->where('dayTo', 'dateGte', $selectedDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $ticketDateRangeDatabase[$key]['ticket'] -= $temporaryTicketCount;
                        $ticketState = $ticketDateRangeDatabase[$key]['ticket'];
                        if($ticketState < 0) return false;
                        $av->dateRange = json_encode($ticketDateRangeDatabase);
                        $av->save();
                        $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($ticketRequest[0]['day'], '00:00', 'Europe/Paris');
                        if ($ticketState < 5 && $isDateTimeValid) {
                            $jsonq2 = $this->apiRelated->prepareJsonQ();
                            $res2 = $jsonq2->json($av->daily);
                            $result2 = $res2->where('day', '=', $ticketRequest[0]['day'])
                                ->where('isActive', '=', 1)
                                ->get();
                            if (count($result2) == 1) {
                                $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($ticketRequest[0]['day'], '00:00', 'Europe/Paris');
                                $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                foreach ($optionRefCodes as $orc) {
                                    $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                }
                            }
                        }
                        if ($ticketDateRangeDatabase[$key]['ticket'] <= 5) {
                            event(new StatusLiked('Your last ' . $ticketDateRangeDatabase[$key]['ticket'] . ' tickets left for ' . $option->referenceCode, $option, 'TICKET_ALERT'));
                        }
                    }
                    $res->reset();
                }

                if ($av->avTicketType == 4 && count($ticketBarcodeDatabase) > 0) {
                    $selectedDate = $request->obj[0]['day'];
                    $res = $jsonq->json($av->barcode);
                    $res->where('dayFrom', 'dateLte', $selectedDate)->where('dayTo', 'dateGte', $selectedDate)->get();
                    $resDecoded = json_decode($res->toJson(), true);
                    $key = key($resDecoded);
                    if (array_key_exists($key, $resDecoded)) {
                        $key = array_keys(json_decode($res->toJson(), true))[0];
                        $ticketBarcodeDatabase[$key]['ticket'] -= $temporaryTicketCount;
                        if($ticketBarcodeDatabase[$key]['ticket'] < 0) return false;
                        $av->barcode = json_encode($ticketBarcodeDatabase);
                        $av->save();
                        if ($ticketBarcodeDatabase[$key]['ticket'] <= 5) {
                            event(new StatusLiked('Your last ' . $ticketBarcodeDatabase[$key]['ticket'] . ' barcodes left for ' . $option->referenceCode, $option, 'TICKET_ALERT'));
                        }
                    }
                    $res->reset();
                    $ticketType = $av->ticketType()->first();
                    if (!is_null($ticketType)) {
                        $avsUsingThisTT = $ticketType->av()->where('supplierID', $av->supplierID)->where('id', '!=', $av->id)->get();
                        if (count($avsUsingThisTT) > 0) {
                            foreach ($avsUsingThisTT as $avUsingThisTT) {
                                $barcodeDecodedOfThisTicket = json_decode($avUsingThisTT->barcode, true);
                                if (count($barcodeDecodedOfThisTicket) > 0) {
                                    $barcodeDecodedOfThisTicket[0]['ticket'] -= $temporaryTicketCount;
                                    if($barcodeDecodedOfThisTicket[0]['ticket'] < 0) return false;
                                    $avUsingThisTT->barcode = json_encode($barcodeDecodedOfThisTicket);
                                    $avUsingThisTT->save();
                                }
                            }
                        }
                    }
                }
            }
        }

        return ['temporaryTicketCount' => $temporaryTicketCount, 'totalTicketCount' => $totalTicketCount];
    }

    /**
     * Increments ticket count after removing item from cart
     *
     * @param Request $request
     * @param $id
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function ticketCountIncrement(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $option = Option::where('referenceCode', '=', explode('-', $cart->referenceCode)[1])->first();
        $pricing = $option->pricings()->first();
        $bookingItems = json_decode($cart->bookingItems, true);
        $temporaryIgnoredCategoriesArray = json_decode($pricing->ignoredCategories, true);
        $ignoredCategoriesArray = [];
        $newTicketCount = $cart->ticketCount;
        if (!is_null($temporaryIgnoredCategoriesArray)) {
            foreach ($temporaryIgnoredCategoriesArray as $ignoredCategory) {
                if ($ignoredCategory == 'euCitizen') {
                    array_push($ignoredCategoriesArray, 'EU_CITIZEN');
                } else {
                    array_push($ignoredCategoriesArray, strtoupper($ignoredCategory));
                }
            }
            foreach ($ignoredCategoriesArray as $ignoredCategory) {
                foreach ($bookingItems as $bookingItem) {
                    if ($bookingItem['category'] == $ignoredCategory) {
                        $newTicketCount -= $bookingItem['count'];
                    }
                }
            }
        }
        $availability = $option->avs()->get();
        foreach ($availability as $av) {
            if ($av->isLimitless == 0) {
                $ticketHourlyDatabase = json_decode($av->hourly, true);
                $ticketDailyDatabase = json_decode($av->daily, true);
                $ticketDateRangeDatabase = json_decode($av->dateRange, true);
                $ticketBarcodeDatabase = json_decode($av->barcode, true);
                $selectedDate = $cart->date;
                $jsonq = $this->apiRelated->prepareJsonQ();
                $selectedHour = json_decode($cart->hour, true);
                if ($av->avTicketType == 1 && count($ticketHourlyDatabase) > 0) {
                    foreach ($selectedHour as $hour) {
                        $res = $jsonq->json($av->hourly);
                        $result = $res->where('day', '=', $selectedDate)
                            ->where('hour', '=', $hour['hour'])
                            ->get();
                        if (count($result) == 1) {
                            $key = key($result);
                            $ticketHourlyDatabase[$key]['ticket'] = $ticketHourlyDatabase[$key]['ticket'] + $newTicketCount;
                            $ticketState = $ticketHourlyDatabase[$key]['ticket'];
                            $av->hourly = json_encode($ticketHourlyDatabase);
                            $av->save();
                            $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($ticketHourlyDatabase[$key]['day'], $ticketHourlyDatabase[$key]['hour'], 'Europe/Paris');
                            if ($ticketState < 5 && $isDateTimeValid) {
                                $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($ticketHourlyDatabase[$key]['day'], $ticketHourlyDatabase[$key]['hour'], 'Europe/Paris');
                                $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                foreach ($optionRefCodes as $orc) {
                                    $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                }
                            }
                        }
                        $res->reset();
                    }
                } else if ($av->avTicketType == 2 && count($ticketDailyDatabase) > 0) {
                    $res = $jsonq->json($av->daily);
                    $result = $res->where('day', '=', $selectedDate)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $ticketDailyDatabase[$key]['ticket'] = $ticketDailyDatabase[$key]['ticket'] + $newTicketCount;
                        $ticketState = $ticketDailyDatabase[$key]['ticket'];
                        $av->daily = json_encode($ticketDailyDatabase);
                        $av->save();
                        $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($ticketDailyDatabase[$key]['day'], '00:00', 'Europe/Paris');
                        if ($ticketState < 5 && $isDateTimeValid) {
                            $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($ticketDailyDatabase[$key]['day'], '00:00', 'Europe/Paris');
                            $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                            foreach ($optionRefCodes as $orc) {
                                $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                            }
                        }
                    }
                    $res->reset();
                } else if ($av->avTicketType == 3 && count($ticketDateRangeDatabase) > 0) {
                    $res = $jsonq->json($av->dateRange);
                    $result = $res->where('dayFrom', 'dateLte', $selectedDate)
                        ->where('dayTo', 'dateGte', $selectedDate)
                        ->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $ticketDateRangeDatabase[$key]['ticket'] = $ticketDateRangeDatabase[$key]['ticket'] + $newTicketCount;
                        $ticketState = $ticketDateRangeDatabase[$key]['ticket'];
                        $av->dateRange = json_encode($ticketDateRangeDatabase);
                        $av->save();
                        $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($selectedDate, '00:00', 'Europe/Paris');
                        if ($ticketState < 5 && $isDateTimeValid) {
                            $jsonq2 = $this->apiRelated->prepareJsonQ();
                            $res2 = $jsonq2->json($av->daily);
                            $result2 = $res2->where('day', '=', $selectedDate)
                                ->where('isActive', '=', 1)
                                ->get();
                            if (count($result2) == 1) {
                                $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($selectedDate, '00:00', 'Europe/Paris');
                                $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                foreach ($optionRefCodes as $orc) {
                                    $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                }
                            }
                        }
                    }
                    $res->reset();
                } else if ($av->avTicketType == 4 && count($ticketBarcodeDatabase) > 0) {
                    $res = $jsonq->json($av->barcode);
                    $result = $res->where('dayFrom', 'dateLte', $selectedDate)->where('dayTo', 'dateGte', $selectedDate)->get();
                    if ($res->count() > 0) {
                        $key = key($result);
                        $ticketBarcodeDatabase[$key]['ticket'] = json_encode($ticketBarcodeDatabase[$key]['ticket'] + $newTicketCount);
                        $av->barcode = json_encode($ticketBarcodeDatabase);
                    }
                    $av->save();
                    $res->reset();
                    $ticketType = $av->ticketType()->first();
                    if (!is_null($ticketType)) {
                        $avsUsingThisTT = $ticketType->av()->where('supplierID', $av->supplierID)->where('id', '!=', $av->id)->get();
                        if (count($avsUsingThisTT) > 0) {
                            foreach ($avsUsingThisTT as $avUsingThisTT) {
                                $barcodeDecodedOfThisTicket = json_decode($avUsingThisTT->barcode, true);
                                if (count($barcodeDecodedOfThisTicket) > 0) {
                                    $barcodeDecodedOfThisTicket[0]['ticket'] += $newTicketCount;
                                    $avUsingThisTT->barcode = json_encode($barcodeDecodedOfThisTicket);
                                    $avUsingThisTT->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Updates cart after clicking update button on cart page
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function update(Request $request)
    {
        $cart = Cart::findOrFail($request->cartID);
        $option = Option::findOrFail($cart->optionID);
        $supplierID = $option->supplierID;
        $maxPersonCount = $option->maxPerson;
        $ticketRequest = json_decode($cart->hour, true);
        $jsonq = $this->apiRelated->prepareJsonQ();
        $adultCount = $request->adultCount;
        $youthCount = $request->youthCount;
        $childCount = $request->childCount;
        $infantCount = $request->infantCount;
        $euCitizenCount = $request->euCitizenCount;
        $pricing = $option->pricings()->first();
        $adultPrice = 0;
        $youthPrice = 0;
        $childPrice = 0;
        $infantPrice = 0;
        $euCitizenPrice = 0;
        $minPersonArr = json_decode($pricing->minPerson, true);
        $maxPersonArr = json_decode($pricing->maxPerson, true);
        $adultPriceArr = json_decode($pricing->adultPrice, true);
        $youthPriceArr = json_decode($pricing->youthPrice, true);
        $childPriceArr = json_decode($pricing->childPrice, true);
        $infantPriceArr = json_decode($pricing->infantPrice, true);
        $euCitizenPriceArr = json_decode($pricing->euCitizenPrice, true);
        foreach ($maxPersonArr as $index => $item) {
            if ($adultCount >= $minPersonArr[$index] && $adultCount <= $item) {
                $adultPrice = $adultCount * $adultPriceArr[$index];
            }
            if ($youthCount >= $minPersonArr[$index] && $youthCount <= $item) {
                $youthPrice = $youthCount * $youthPriceArr[$index];
            }
            if ($childCount >= $minPersonArr[$index] && $childCount <= $item) {
                $childPrice = $youthCount * $childPriceArr[$index];
            }
            if ($infantCount >= $minPersonArr[$index] && $infantCount <= $item) {
                $infantPrice = $infantCount * $infantPriceArr[$index];
            }
            if ($euCitizenCount >= $minPersonArr[$index] && $euCitizenCount <= $item) {
                $euCitizenPrice = $euCitizenCount * $euCitizenPriceArr[$index];
            }
        }
        $totalPrice = $adultPrice + $youthPrice + $childPrice + $infantPrice + $euCitizenPrice;
        $totalPrice = Currency::calculateCurrencyForVisitor($totalPrice);
        $newTicketCount = 0;
        $cartTicketCount = 0;
        $bookingItems = json_decode($cart->bookingItems, true);
        $temporaryIgnoredCategoriesArray = json_decode($pricing->ignoredCategories, true);
        $allCategories = ["ADULT", "YOUTH", "CHILD", "INFANT", "EU_CITIZEN"];
        $ignoredCategoriesArray = [];
        if (!is_null($temporaryIgnoredCategoriesArray)) {
            foreach ($temporaryIgnoredCategoriesArray as $ignoredCategory) {
                if ($ignoredCategory == 'euCitizen') {
                    array_push($ignoredCategoriesArray, 'EU_CITIZEN');
                } else {
                    array_push($ignoredCategoriesArray, strtoupper($ignoredCategory));
                }
            }
        }
        $arrayDiff = array_diff($allCategories, $ignoredCategoriesArray);
        foreach ($arrayDiff as $element) {
            foreach ($bookingItems as $bookingItem) {
                if ($element == $bookingItem['category']) {
                    if ($bookingItem['category'] == 'EU_CITIZEN') {
                        $newTicketCount += $request->euCitizenCount;
                    } else {
                        $categoryCount = strtolower($bookingItem['category']) . 'Count';
                        $newTicketCount += $request->$categoryCount;
                    }
                    $cartTicketCount += $bookingItem['count'];
                }
            }
        }
        $absTicketCount = $newTicketCount - $cartTicketCount;
        $availability = $option->avs()->get();
        $ticketTypes = [];
        $ticket = null;

        if (Auth::check() && !is_null(Auth::guard('web')->user()->commission)) {
            $commission = Auth::guard('web')->user()->commission;
        } else {
            $commission = 0;
        }

        $cart->totalPriceWOSO = $totalPrice;
        // If there is a Special Offer, it will be applied
        $specials = json_decode($cart->specials, true);
        if (!is_null($specials)) {
            $minType = $specials['minType'];
            $minimum = $specials['minimum'];
            $discountType = $specials['discountType'];
            $discount = $specials['discount'];

            if ($minType == 'minPerson') {
                if ($newTicketCount >= $minimum) {
                    $totalPrice = $this->getPriceForSpecialOffer($totalPrice, $discountType, $discount);
                }
            } else if ($minType == 'minCartTotal') {
                if ($totalPrice >= $minimum) {
                    $totalPrice = $this->getPriceForSpecialOffer($totalPrice, $discountType, $discount);
                }
            }
        }
        ///////

        $cart->totalPrice = $totalPrice;
        $totalCommission = $totalPrice * $commission / 100;
        $cart->totalCommission = $totalCommission;
        $cart->maxCommission = $totalCommission;
        foreach ($availability as $av) {
            array_push($ticketTypes, $av->ticketType()->first());
            $ticketHourlyDatabase = json_decode($av->hourly, true);
            $ticketDailyDatabase = json_decode($av->daily, true);
            $ticketDateRangeDatabase = json_decode($av->dateRange, true);
            $ticketBarcodeDatabase = json_decode($av->barcode, true);
            if ($av->isLimitless == 0) {
                if ($av->avTicketType == 1 && count($ticketHourlyDatabase) > 0) {
                    foreach ($ticketRequest as $tic) {
                        $res = $jsonq->json($av->hourly);
                        $result = $res->where('day', '=', $cart->date)
                            ->where('hour', '=', $tic['hour'])
                            ->get();
                        if (count($result) == 1) {
                            $key = key($result);
                            $ticketHourlyDatabase[$key]['ticket'] = $ticketHourlyDatabase[$key]['ticket'] - ($absTicketCount);
                            $ticketState = $ticketHourlyDatabase[$key]['ticket'];
                            if ($newTicketCount <= $maxPersonCount && $ticketHourlyDatabase[$key]['ticket'] >= 0) {
                                $av->hourly = json_encode($ticketHourlyDatabase);
                                $av->save();
                                $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($cart->date, $tic['hour'], 'Europe/Paris');
                                if ($ticketState < 5 && $isDateTimeValid) {
                                    $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($cart->date, $tic['hour'], 'Europe/Paris');
                                    $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                    foreach ($optionRefCodes as $orc) {
                                        $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                    }
                                }
                            } else {
                                $ticket = $ticketHourlyDatabase[$key]['ticket'];
                                return ['ticket' => $ticket];
                            }
                        }
                        $res->reset();
                    }
                }
                if ($av->avTicketType == 2 && count($ticketDailyDatabase) > 0) {
                    $res = $jsonq->json($av->daily);
                    $result = $res->where('day', '=', $cart->date)->get();
                    if (count($result) > 0) {
                        $key = key($result);
                        $ticketDailyDatabase[$key]['ticket'] = $ticketDailyDatabase[$key]['ticket'] - ($absTicketCount);
                        $ticketState = $ticketDailyDatabase[$key]['ticket'];
                        if ($newTicketCount <= $maxPersonCount && $ticketDailyDatabase[$key]['ticket'] >= 0) {
                            $av->daily = json_encode($ticketDailyDatabase);
                            $av->save();
                            $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($cart->date, '00:00', 'Europe/Paris');
                            if ($ticketState < 5 && $isDateTimeValid) {
                                $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($cart->date, '00:00', 'Europe/Paris');
                                $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                foreach ($optionRefCodes as $orc) {
                                    $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                }
                            }
                        } else {
                            $ticket = $ticketDailyDatabase[$key]['ticket'];
                            return ['ticket' => $ticket, 'error' => __('cartUpdateError')];
                        }
                        $res->reset();
                    }
                }

                if ($av->avTicketType == 3 && count($ticketDateRangeDatabase) > 0) {
                    $res = $jsonq->json($av->dateRange);
                    $result = $res->where('dayFrom', 'dateLte', $cart->date)
                        ->where('dayTo', 'dateGte', $cart->date)
                        ->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        $ticketDateRangeDatabase[$key]['ticket'] = $ticketDateRangeDatabase[$key]['ticket'] - ($absTicketCount);
                        $ticketState = $ticketDateRangeDatabase[$key]['ticket'];
                        if ($newTicketCount <= $maxPersonCount && $ticketDateRangeDatabase[$key]['ticket'] >= 0) {
                            $av->dateRange = json_encode($ticketDateRangeDatabase);
                            $av->save();
                            $isDateTimeValid = $this->timeRelatedFunctions->isDateTimeValid($cart->date, '00:00', 'Europe/Paris');
                            if ($ticketState < 5 && $isDateTimeValid) {
                                $jsonq2 = $this->apiRelated->prepareJsonQ();
                                $res2 = $jsonq2->json($av->daily);
                                $result2 = $res2->where('day', '=', $cart->date)
                                    ->where('isActive', '=', 1)
                                    ->get();
                                if (count($result2) == 1) {
                                    $dateTime = $this->timeRelatedFunctions->convertDmyToYmdWithHour($cart->date, '00:00', 'Europe/Paris');
                                    $optionRefCodes = $av->options()->where('connectedToApi', 1)->pluck('referenceCode');
                                    foreach ($optionRefCodes as $orc) {
                                        $this->apiRelated->saveNotificationToTable($orc, $dateTime, $ticketState);
                                    }
                                }
                            }
                        } else {
                            $ticket = $ticketDateRangeDatabase[$key]['ticket'];
                            return ['ticket' => $ticket];
                        }
                    }
                    $res->reset();
                }
            }

            if ($av->avTicketType == 4 && count($ticketBarcodeDatabase) > 0) {
                $res = $jsonq->json($av->barcode);
                $result = $res->where('dayFrom', 'dateLte', $cart->date)
                    ->where('dayTo', 'dateGte', $cart->date)
                    ->get();
                if (count($result) > 0) {
                    $key = key($result);
                    $ticketBarcodeDatabase[$key]['ticket'] = $ticketBarcodeDatabase[$key]['ticket'] - ($absTicketCount);
                    if ($newTicketCount <= $maxPersonCount && $ticketBarcodeDatabase[$key]['ticket'] >= 0) {
                        $av->barcode = json_encode($ticketBarcodeDatabase);
                        $av->save();
                        $ticketType = $av->ticketType()->first();
                        if (!is_null($ticketType)) {
                            $avsUsingThisTT = $ticketType->av()->where('supplierID', $av->supplierID)->where('id', '!=', $av->id)->get();
                            if (count($avsUsingThisTT) > 0) {
                                foreach ($avsUsingThisTT as $avUsingThisTT) {
                                    $barcodeDecodedOfThisTicket = json_decode($avUsingThisTT->barcode, true);
                                    if (count($barcodeDecodedOfThisTicket) > 0) {
                                        $barcodeDecodedOfThisTicket[0]['ticket'] = $barcodeDecodedOfThisTicket[0]['ticket'] - ($absTicketCount);
                                        $avUsingThisTT->barcode = json_encode($barcodeDecodedOfThisTicket);
                                        $avUsingThisTT->save();
                                    }
                                }
                            }
                        }
                    } else {
                        $ticket = $ticketBarcodeDatabase[$key]['ticket'];
                        return ['ticket' => $ticket, 'error' => __('cartUpdateError')];
                    }
                }
                $res->reset();
            }
        }

        $bookingItems = [];
        $cart->bookingItems = json_encode($bookingItems);
        if ($adultCount) {
            $adult = ['category' => 'ADULT', 'count' => $adultCount];
            array_push($bookingItems, $adult);
        }

        if ($euCitizenCount) {
            $euCitizen = ['category' => 'EU_CITIZEN', 'count' => $euCitizenCount];
            array_push($bookingItems, $euCitizen);
        }

        if ($youthCount) {
            $youth = ['category' => 'YOUTH', 'count' => $youthCount];
            array_push($bookingItems, $youth);
        }

        if ($childCount) {
            $child = ['category' => 'CHILD', 'count' => $childCount];
            array_push($bookingItems, $child);
        }

        if ($infantCount) {
            $infant = ['category' => 'INFANT', 'count' => $infantCount];
            array_push($bookingItems, $infant);
        }
        $cart->bookingItems = json_encode($bookingItems);
        $cart->ticketCount = $request->adultCount + $request->youthCount + $request->childCount + $request->infantCount + $request->euCitizenCount;


        // reserve update if cart has tootbus (start)


        if ($cart->is_tootbus == 1) {


            // set tootbus booking reserve here
            $data = [];
            $data["productId"] = $option->tootbus->tootbus_product_id;
            $data["optionId"] = !empty($option->tootbus->tootbus_option_id) ? $option->tootbus->tootbus_option_id : "DEFAULT";
            $data["availabilityId"] = $cart->dateTime;
            $data["unitItems"] = [];
            $units = ["ADULT" => "adult", "CHILD" => "child", "YOUTH" => "youth", "INFANT" => "infant"];

            foreach ($bookingItems as $item) {
                for ($i = 0; $i < $item["count"]; $i++) {
                    $data["unitItems"][] = ["unitId" => $units[$item["category"]]];
                }
            }
            //return response()->json(["status" => 0, "data" => $data]);

            $reserveResponse = $this->tootbusRelated->reserveUpdate(json_decode($cart->tootbus_booking_response, true)["data"]["uuid"], $data);

            if ($reserveResponse["status"] === false) {
                return response()->json(["status" => 0, "message" => $reserveResponse["message"]]);
            }

            $cart->tootbus_booking_response = json_encode(["type" => "reserve", "data" => json_decode($reserveResponse["message"], true)]);
        }


        // reserve update if cart has tootbus (end)


        if ($cart->save()) {
            foreach ($ticketTypes as $ticketType) {
                if (!is_null($ticketType)) {
                    if ($absTicketCount > 0) {
                        Barcode::where('isUsed', 0)->where('isReserved', 0)
                            ->where('isExpired', 0)->where('ownerID', $supplierID)
                            ->where('ticketType', $ticketType->id)->take($absTicketCount)
                            ->update(['cartID' => $cart->id, 'isReserved' => 1]);
                    } else if ($absTicketCount < 0) {
                        $absTicketCount = abs($absTicketCount);
                        Barcode::where('isReserved', 1)
                            ->where('isExpired', 0)->where('ownerID', $supplierID)
                            ->where('ticketType', $ticketType->id)->take($absTicketCount)
                            ->update(['cartID' => null, 'isReserved' => 0]);
                    }
                }
            }
        }

        return redirect('/cart');
    }

    /**
     * Function for getting price for products with special offer
     *
     * @param $totalPrice
     * @param $discountType
     * @param $discount
     * @return float|int|string
     */
    public function getPriceForSpecialOffer($totalPrice, $discountType, $discount)
    {
        if ($discountType == 'percentage') {
            $totalPrice = $totalPrice - (($totalPrice * $discount) / 100);
        } else if ($discountType == 'money') {
            $discount = Currency::calculateCurrencyForVisitor($discount);
            $totalPrice = $totalPrice - $discount;
        }
        return $totalPrice;
    }

    /**
     * If commissioner is logged in, gets new price values for cart according to the commission
     *
     * @param Request $request
     */
    public function newValuesForCart(Request $request)
    {
        $cart = Cart::findOrFail($request->cartID);
        if ($cart->maxCommission < $request->totalCommission) {
            $cart->tempCommission = $cart->maxCommission;
        } else {
            $cart->tempCommission = $request->totalCommission;
        }
        $cart->tempTotalPrice = $request->totalPrice;
        $cart->save();
    }

    /**
     * Function for sharing cart with another person via whatsapp
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareCart(Request $request)
    {
        $emailCheck = $request->emailCheck;
        $whatsappCheck = $request->whatsappCheck;
        if ($emailCheck == '1' || $whatsappCheck == '1') {
            $cartIds = json_decode($request->cartIds, true);
            $cartDetails = [];
            $currencyID = 2;
            $total = 0;
            $currencySymbols = ['1' => '$', '2' => '€', '3' => '£', '4' => '₺'];
            foreach ($cartIds as $id) {
                $cart = Cart::findOrFail($id);
                $product = Product::findOrFail($cart->productID);
                $referenceCode = $cart->referenceCode;
                $categoryAndCountInfo = '';
                $bookingItems = json_decode($cart->bookingItems, true);
                foreach ($bookingItems as $index => $bookingItem) {
                    $categoryAndCountInfo .= $bookingItem['count'] . ' ' . $bookingItem['category'];
                    if ($index != (count($bookingItems) - 1) && count($bookingItems) != 1) {
                        $categoryAndCountInfo .= ', ';
                    }
                }
                $productTitle = $product->title;
                $subTotal = $cart->totalPrice;
                if (!is_null($cart->tempTotalPrice)) {
                    $subTotal = $cart->tempTotalPrice;
                }
                $total += $subTotal;
                $currencyID = $cart->currencyID; // Assuming each cart object is using same currencyID
                array_push($cartDetails,
                    [
                        'productTitle' => $productTitle,
                        'referenceCode' => $referenceCode,
                        'categoryAndCountInfo' => $categoryAndCountInfo,
                        'subTotal' => $subTotal
                    ]
                );
                $cart->status = 6;
                $cart->shareEmail = $request->email;
                $cart->sharePhone = $request->phoneNumber;
                $cart->save();
            }
            $commissioner = auth()->guard('web')->user();
            $commissionerName = $commissioner->name;
            $commissionerSurname = $commissioner->surname;
            $phoneNumber = str_replace('+', '', $request->phoneNumber);
            $phoneNumber = str_replace(' ', '', $phoneNumber);
            $paymentLinkEmail = env('APP_URL', 'https://cityzore.com') . '/' . $this->commonFunctions->getRouteLocalization('credit-card-details-for-shared-cart') . '/?userID=' . $commissioner->id . '&eorw=e&currencyID=' . $currencyID . '&cartIds=' . $request->cartIds . '&tfn=' . $request->firstName . '&tln=' . $request->lastName . '&tp=' . $phoneNumber . '&email=' . $request->email;
            $paymentLinkEmail = str_replace(' ', '|', $paymentLinkEmail);
            // If eorw variable is not used, we may reduce payment links to one.
            $paymentLinkWhatsApp = env('APP_URL', 'https://cityzore.com') . '/' . $this->commonFunctions->getRouteLocalization('credit-card-details-for-shared-cart') . '/?userID=' . $commissioner->id . '&eorw=w&currencyID=' . $currencyID . '&cartIds=' . $request->cartIds . '&tfn=' . $request->firstName . '&tln=' . $request->lastName . '&tp=' . $phoneNumber . '&email=' . $request->email;
            $paymentLinkWhatsApp = str_replace(' ', '|', $paymentLinkWhatsApp);
            if ($emailCheck == '1') {
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'subject' => 'Cart is shared with you!',
                    'commissionerName' => $commissionerName,
                    'commissionerSurname' => $commissionerSurname,
                    'paymentLink' => $paymentLinkEmail,
                    'cartDetails' => $cartDetails,
                    'total' => $total,
                    'currencySymbol' => $currencySymbols[$currencyID],
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'sendToCC' => false
                ]);
                $mail->to = $request->email;
                $mail->data = json_encode($data);
                $mail->blade = 'mail.shared-cart';
                $mail->save();
            }

            return response()->json(['success' => __('cartShareSuccess1'), 'link' => $paymentLinkWhatsApp]);
        }

        return response()->json(['error' => __('cartShareUnexpectedError')]);
    }

    /**
     * Function for sending mail to user if he/she doesn't buy the products in 30 minutes.
     *
     */
    public function sendMailToUserWhenCart30MinutesHavePassed()
    {
        $carts = Cart::where('status', 0)->where('isGYG', 0)->whereNull('isBokun')->where('isSendBM', 0)->get();
        foreach ($carts as $cart) {
            $userID = $cart->userID;
            $optionTitle = Option::findOrFail($cart->optionID)->title;
            $created_at = strtotime($cart->created_at);
            $now = strtotime('now');
            if (strlen($userID) < 5 && $userID != null) {
                $user = \App\User::findOrFail($userID);
                if ($created_at + (30 * 60) <= $now) {

                    $this->mailOperations->sendMail(
                        [
                            'subject' => 'There are items in your cart!',
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'email' => $user->email,
                            'optionTitle' => $optionTitle,
                            'sendToCC' => false
                        ], 'mail.cart-reminder');
                    $cart->isSendBM = 1;
                    $cart->save();

                }
            }
        }
    }
}
