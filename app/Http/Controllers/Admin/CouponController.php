<?php

namespace App\Http\Controllers\Admin;

use App\Attraction;
use App\Cart;
use App\Coupon;
use App\Http\Controllers\Helpers\MailOperations;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Mails;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Country;

class CouponController extends Controller
{

    public $mailOperations;
    public $timeRelatedFunctions;
    public $commonFunctions;

    public function __construct()
    {
        $this->mailOperations = new MailOperations();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->commonFunctions = new CommonFunctions();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $coupons = Coupon::all();
        return view('panel.coupons.index', ['coupons' => $coupons]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $product = Product::all();
        $countries = Country::all();
        $attraction = Attraction::where('isActive', 1)->get();
        $users = User::all();
        return view('panel.coupons.create', [
            'product' => $product,
            'countries' => $countries,
            'attraction' => $attraction,
            'users' => $users
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function optionSelect(Request $request)
    {
        $product = Product::findOrFail($request->productID);
        $options = $product->options()->get();
        return ['options' => $options];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function saveNewCoupon(Request $request)
    {
        $sameCouponNameCount = Coupon::where('couponCode', $request->couponCode)->count();
        $coupon = new Coupon();
        $coupon->productID = $request->couponType == 1 ? $request->productID : null;
        $coupon->type = $request->couponType;
        $coupon->lastSelect = $request->couponType == 4 || $request->couponType == 5 ? null : $request->lastSelect;
        $coupon->discountType = $request->discountTypeVal;
        $coupon->maxUsability = $request->maxUsability;
        $coupon->startingDate = $request->startingDate;
        $coupon->endingDate = $request->endingDate;
        $coupon->discount = $request->discount;
        $coupon->isUsed = 0;
        $coupon->couponCode = $request->couponCode;

        if($request->productID) {
            if(!$request->lastSelect)
                return ['error' => 'Option selection is required. Please select an option'];
        }

        if ($request->discountTypeVal == 'percent') {
            if (strtotime($request->endingDate) >= strtotime($request->startingDate) && $request->maxUsability > 0 && $request->discount < 100) {
                if ($sameCouponNameCount > 0) {
                    return ['error' => 'Your coupon code has been used from another coupon. Please change and try again'];
                } else {
                    $coupon->save();
                }
            } else {
                return ['error' => 'Please check your values again!'];
            }
        } elseif ($request->discountTypeVal == 'net rate') {
            if (strtotime($request->endingDate) >= strtotime($request->startingDate) && $request->maxUsability > 0) {
                if ($sameCouponNameCount > 0) {
                    return ['error' => 'Your coupon code has been used from another coupon. Please change and try again'];
                } else {
                    $coupon->save();
                }
            } else {
                return ['error' => 'Please check again your starting and ending times.'];
            }
        }

        $this->sendMailWhenNewCouponCreated($coupon);

        return [
            'Success' => 'Success',
        ];
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function useCoupon(Request $request)
    {
        $couponResponse = [];
        $userID = Auth::guard('web')->check() ? Auth::guard('web')->user()->id : session()->get('uniqueID');
        $cart = Cart::where('userID', '=', $userID)->where('status', '=', 0)->get();
        $coupon = Coupon::where('couponCode', '=', $request->couponCode)->first();
        if (!$coupon) {
            return response()->json(['failed' => __('incorrectCouponCode')]);
        }
        if ($coupon->type == 6 && ($userID != $coupon->lastSelect)) {
            return response()->json(['failed' => __('incorrectCouponCode')]);
        }
        $startingDate = strtotime($coupon->startingDate);
        $endingDate = strtotime($coupon->endingDate);

        if(session()->has('totalPriceWithDiscount')){
            return response()->json(['failed' => __('the coupon alrady used!')]);
        }
        session()->forget('totalPriceWithDiscount');
        foreach ($cart as $c) {
            $product = Product::findOrFail($c->productID);
            $productID = $c->productID;
            $optionID = $c->optionID;
            $country = $product->country;
            $attractions = json_decode($product->attractions, true);
            $user = null;
            if (Auth::guard('web')->check()) {
                $user = true;
            }
            
            if ($startingDate <= \Carbon\Carbon::today()->timestamp && \Carbon\Carbon::today()->timestamp <= $endingDate) {
                if ($coupon->isUsed == 0 && $coupon->countOfUsing < $coupon->maxUsability) {
                    if ($coupon->type == 1 && $productID == $coupon->productID && $optionID == $coupon->lastSelect) {
                        $couponResponse[] = $this->cartPriceCalculatorWithCoupon($c->id, $coupon->id);
                    } elseif ($coupon->type == 2 && $country == $coupon->lastSelect) {
                        $couponResponse[] = $this->cartPriceCalculatorWithCoupon($c->id, $coupon->id);
                    } elseif ($coupon->type == 3 && in_array($coupon->lastSelect, $attractions)) {
                        $couponResponse[] = $this->cartPriceCalculatorWithCoupon($c->id, $coupon->id);
                    } elseif ($coupon->type == 4 && Auth::guard()->check()) {
                        $couponResponse[] = $this->cartPriceCalculatorWithCoupon($c->id, $coupon->id);
                    } elseif ($coupon->type == 5) {
                        $couponResponse[] = $this->cartPriceCalculatorWithCoupon($c->id, $coupon->id);
                    } elseif ($coupon->type == 6) {
                        $couponResponse[] = $this->cartPriceCalculatorWithCoupon($c->id, $coupon->id);
                    }else{
                        return response()->json(['failed' => __('incorrectCouponCode')]);
                    }
                } else {
                    $coupon->isUsed = 1;
                    $coupon->save();
                    return response()->json(['failed' => __('incorrectCouponCode')]);
                }
            } else {
                $coupon->isUsed = 1;
                $coupon->save();
                return response()->json(['failed' => 'The coupon you have tried to use is expired']);
            }
        }

        if(in_array(true, $couponResponse)){
            session()->forget('totalPriceWithDiscount');
        }



        return [
            'success' => __('couponUsedSuccessfully'),
            'coupon' => $coupon,
            'user' => $user,
            'country' => $country,
            'lastSelect' => $coupon->lastSelect,
            'attractions' => $attractions,
            'productID' => $productID,
            'optionID' => $optionID,
            'couponResponse' => $couponResponse
        ];
    }

    /**
     * Calculates cart price after using coupon and stores it in session
     *
     * @param $cartID
     * @param $couponID
     */
    public function cartPriceCalculatorWithCoupon($cartID, $couponID)
    {
        $control = false;
        $cart = Cart::findOrFail($cartID);
        $coupon = Coupon::findOrFail($couponID);
        $totalPrice = $cart->totalPriceWOSO;

            if ($coupon->discountType == 'percent') {
                $newPrice = ($totalPrice - ($totalPrice * $coupon->discount / 100));


            } else if ($coupon->discountType == 'net rate' && $coupon->discount < $totalPrice) {
                $newPrice = $totalPrice - $coupon->discount;
            }
            $newPrice = $this->commonFunctions->roundUp($newPrice, 2); // 123.456 to 123.46

             // check after discount price still greater than speccial offer prica

            if($cart->totalPrice <= $newPrice){
                $newPrice = $this->commonFunctions->roundUp($cart->totalPrice, 2); // 123.456 to 123.46
                $control = true;
                return $control;
            }



        $general = [];
        $couponArr = [];
        $couponArr["id"] = $coupon->id;
        $couponArr["cartID"] = $cart->id;
        $couponArr["type"] = $coupon->type;
        $couponArr["couponCode"] = $coupon->couponCode;
        $couponArr["productID"] = $coupon->productID;
        $couponArr["lastSelect"] = $coupon->lastSelect;
        $couponArr["discountType"] = $coupon->discountType;
        $couponArr["countOfUsing"] = $coupon->countOfUsing;
        $couponArr["maxUsability"] = $coupon->maxUsability;
        $couponArr["startingDate"] = $coupon->startingDate;
        $couponArr["endingDate"] = $coupon->endingDate;
        $couponArr["discount"] = $coupon->discount;
        $couponArr["isUsed"] = $coupon->isUsed;
        $couponArr["invalidDiscount"] =  $control;

        $couponOperations = ["coupon" => $couponArr, "newPrice" => $newPrice, "cartID" => $cart->id];

         if (!(is_null((session()->get('totalPriceWithDiscount'))))) {
            $general = json_decode(session()->get('totalPriceWithDiscount'), true);
        }
        array_push($general, $couponOperations);

        $couponOperations_encoded = json_encode($general);


        session()->put('totalPriceWithDiscount', $couponOperations_encoded);

        $cart->coupon = $couponOperations_encoded;
        $cart->save();

        return $control;





    }

    /**
     * Sends mail when a new coupon is created
     *
     * @param $coupon
     */
    public function sendMailWhenNewCouponCreated($coupon)
    {
        if ($coupon->type == 4) {
            $users = User::all();
            foreach ($users as $u) {
                $mail = new Mails();
                $data = [];
                array_push($data, [
                    'couponCode' => $coupon->couponCode,
                    'subject' => 'New Coupon For Our Users ! ',
                    'name' => $u->firstName,
                    'surname' => $u->surname,
                    'sendToCC' => false
                ]);
                $mail->data = json_encode($data);
                $mail->to = $u->email;
                $mail->blade = 'mail.new-coupon-for-users';
                $mail->save();
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function deleteUsedCoupon(Request $request)
    {
        $userID = Auth::guard('web')->check() ? Auth::guard('web')->user()->id : session()->get('uniqueID');
        $cart = Cart::where('userID', '=', $userID)->where('status', '=', 0)->get();


        foreach($cart as $c){
            $c->coupon = null;
            $c->save();
        }

        $usedCoupon = Coupon::findOrFail($request->couponID);
        $discount =  $usedCoupon->discount;
        session()->forget('totalPriceWithDiscount');
        return ['discount' => $discount];
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect('/coupons');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function edit(Request $request)
    {

        $coupon = Coupon::findOrFail($request->couponID);
        $couponCodeNewVal = $request->couponCodeNewVal;
        $discount = $request->discountNewVal;
        $maxUsability = $request->maxUsabilityNewVal;
        $startingDate = $request->startingDateNewVal;
        $endingDate = $request->endingDateNewVal;



        if ($maxUsability <= $coupon->countOfUsing) {
            return ['error' => 'The maximum usability must be bigger than count of using. Please check again !'];
        }

        if ($startingDate > $endingDate) {
            return ['error' => 'Please check your dates again. Your ending date must be closer to today than starting date !'];
        }

        if(coupon::where('couponCode', $couponCodeNewVal)->where('id', "<>", $coupon->id)->count()){
          return ['error' => 'Your coupon code has been used from another coupon. Please change your code !'];
        }


        $coupon->discount = $discount;
        $coupon->maxUsability = $maxUsability;
        $coupon->startingDate = $startingDate;
        $coupon->endingDate = $endingDate;
        $coupon->couponCode = $couponCodeNewVal;

        if($coupon->save()){
            return ["success" => "Your Updated Coupon Successfully!"];
        }

    }

}
