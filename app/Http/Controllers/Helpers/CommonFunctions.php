<?php

namespace App\Http\Controllers\Helpers;
use App\BlogPost;
use App\BlogTranslation;
use App\Avdate;
use App\City;
use App\Page;
use App\SpecialOffers;
use App\Commission;
use App\Currency;
use App\Http\Controllers\Controller;
use App\Cart;
use App\Option;
use App\Product;
use App\ProductGallery;
use App\ProductTranslation;
use App\User;
use App\Language;
use App\Country;
use DateInterval;
use DatePeriod;
use DateTime;
use Goutte\Client;
use Illuminate\Http\Request;
use App\OldProduct;
use App\OldProductTranslation;
use Nahid\JsonQ\Jsonq;
use App\Route;
use App\RouteLocalization;
use App\AttractionTranslation;
use App\Attraction;
use App\Av;
use App\Availability;
use Symfony\Component\HttpClient\HttpClient;


class CommonFunctions extends Controller
{
    /**
     * Flattens an array of multidimensional arrays to a one dimensional array
     *
     * @param array $array
     * @return array
     */
    public function flatten(array $array)
    {
        $return = [];
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    /**
     * Generates a random alphanumeric string
     *
     * @param $keyLength
     * @return string
     */
    public function generateRandomString($keyLength)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        $max = strlen($characters) - 1;
        $random_string_length = $keyLength;
        for ($i = 0; $i < $random_string_length; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        return $string;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCartItems(Request $request)
    {
        $clientIpOrId = auth()->check() ? auth()->user()->id : session()->get('uniqueID');
        $carts = Cart::where('userID', $clientIpOrId)->where('status', 0)->get();
        if (count($carts) > 0) {
            $images = [];
            $optionNames = [];
            foreach ($carts as $cart) {
                $product = Product::findOrFail($cart->productID);
                $coverPhoto = ProductGallery::where('id', $product->coverPhoto)->first();
                array_push($images, $coverPhoto->src);
                $option = Option::findOrFail($cart->optionID);
                array_push($optionNames, $option->title);
            }
            $translationArray = [
                'ADULT' => __('adult'),
                'YOUTH' => __('youth'),
                'CHILD' => __('child'),
                'INFANT' => __('infant'),
                'EU_CITIZEN' => __('euCitizen'),
            ];
            return response()->json(['success' => 'There are items in your cart', 'carts' => $carts, 'optionNames' => $optionNames, 'images' => $images, 'translationArray' => $translationArray]);
        }
        return response()->json(['error' => __('noItemInCart')]);
    }

    /**
     * @param $productID
     * @return mixed
     */
    public function getMinPrice($productID)
    {
        $product = Product::findOrFail($productID);
        return $this->getTheCheapestOffer($product)["price"];
    }

    /**
     * @param $productID
     * @return mixed
     */
    public function getMinPriceHome($productID)
    {
        $product = $productID;
        return $this->getTheCheapestOffer($product)["price"];
    }

    /**
     * @param $productID
     * @param $userID
     * @param null $specialOfferPrice
     * @return float|int|mixed
     */
    public function getCommissionMinPrice($productID, $userID, $specialOfferPrice = null)
    {
        $product = $productID;
        $options = $product->options()->get();
        $user = User::findOrFail($userID);
        if (!is_null($specialOfferPrice)) {
            $commissionPrice = ($specialOfferPrice / 100) * ($user->commission);
            return $commissionPrice;
        }
        $commissionPrices = [];
        foreach ($options as $option) {



            if($option->supplierID != -1){
            $supp = \App\Supplier::findOrFail($option->supplierID);


            }




            $commissions = Commission::where('commissionerID', '=', $userID)->where('optionID', '=', $option->id)->first();
            $pricing = $option->pricings()->first();
            $price = 0;
            if ($commissions) {


               if($option->supplierID != -1){


               if($supp->comission){

                if($supp->comission < $commissions->commission){
                    $commissions->commission = $supp->comission;
                    }

                }
            }


                if ($pricing) {
                    $price = json_decode($pricing->adultPrice, true)[0];
                }
                $commissionPrice = $price - (($price / 100) * ($commissions->commission));
                array_push($commissionPrices, $commissionPrice);
            } else {

                if($option->supplierID != -1){



                    if($supp->commissioner_commission){
                        $user->commission = $supp->commissioner_commission;

                    }
                 }





                if ($pricing) {
                    $price = json_decode($pricing->adultPrice, true)[0];
                }
                if($user->commissionType == 'percentage')
                    $commissionPrice = $price - (($price / 100) * ($user->commission));
                elseif($user->commissionType == 'money')
                    $commissionPrice = $price - $user->commission;
                array_push($commissionPrices, $commissionPrice);
            }
        }
        return min($commissionPrices);
    }












    public function getOfferPercentageForSpecificOption($product, $option)
    {
        if(auth()->guard("web")->check()){
          $commission =  auth()->guard("web")->user()->commission;
            if(!is_null($commission) || $commission != 0){
             return 0;
            }
        }

        $productID = $product->id;
        //$options = $product->options()->get();
        $offers = 0;
        $apiRelated = new ApiRelated();
        $todayAsDmy = date('d/m/Y');
        if ($option) {

                //array_push($offers, 0);
                $specialOffers = SpecialOffers::where('productID', $productID)->where('optionID', $option->id)->first();

                if ($specialOffers &&
                    (!is_null($specialOffers->dateRange) || !is_null($specialOffers->weekDay) || !is_null($specialOffers->randomDay) || !is_null($specialOffers->dateTimes))) {
                    if (!is_null($specialOffers->dateTimes)) {
                        $dateTimesDecoded = json_decode($specialOffers->dateTimes, true);
                        $jsonq = $apiRelated->prepareJsonQ();
                        $res = $jsonq->json($specialOffers->dateTimes);
                        $result = $res->where('day', 'dateGte', $todayAsDmy)->get();
                        if (count($result) >= 1) {
                            $keys = array_keys($result);
                            $percentages = [];
                            foreach ($keys as $key) {
                                $percentage = $this->calculatePercentage($dateTimesDecoded, $key, $productID);
                                if ($percentage >= 100) {
                                    array_push($percentages, 0);
                                } else {
                                    array_push($percentages, $percentage);
                                }
                            }
                            $offers = max($percentages);
                        } else {
                            //array_push($offers, 0);
                        }
                        $res->reset();
                    }

                    if (!is_null($specialOffers->randomDay)) {
                        $randomDayDecoded = json_decode($specialOffers->randomDay, true);
                        $jsonq = $apiRelated->prepareJsonQ();
                        $res = $jsonq->json($specialOffers->randomDay);
                        $result = $res->where('day', 'dateGte', $todayAsDmy)->get();
                        if (count($result) >= 1) {
                            $keys = array_keys($result);
                            $percentages = [];
                            foreach ($keys as $key) {
                                $percentage = $this->calculatePercentage($randomDayDecoded, $key, $productID);
                                if ($percentage >= 100) {
                                    array_push($percentages, 0);
                                } else {
                                    array_push($percentages, $percentage);
                                }
                            }
                            $offers = max($percentages);
                        } else {
                            //array_push($offers, 0);
                        }
                        $res->reset();
                    }

                    if (!is_null($specialOffers->weekDay)) {
                        $weekDayDecoded = json_decode($specialOffers->weekDay, true);
                        $jsonq = $apiRelated->prepareJsonQ();
                        $res = $jsonq->json($specialOffers->weekDay);
                        $result = $res->get();
                        if (count($result) >= 1) {
                            $keys = array_keys($result);
                            $percentages = [];
                            foreach ($keys as $key) {
                                $percentage = $this->calculatePercentage($weekDayDecoded, $key, $productID);
                                if ($percentage >= 100) {
                                    array_push($percentages, 0);
                                } else {
                                    array_push($percentages, $percentage);
                                }
                            }
                            $offers = max($percentages);
                        } else {
                            //array_push($offers, 0);
                        }
                        $res->reset();
                    }

                    if (!is_null($specialOffers->dateRange)) {



                   /*
                        $dateRangeDecoded = json_decode($specialOffers->dateRange, true);
                        $jsonq = $apiRelated->prepareJsonQ();
                        $res = $jsonq->json($specialOffers->dateRange);
                        $result = $res->where('from', 'dateLte', $todayAsDmy)->where('to', 'dateGte', $todayAsDmy)->get();
                        if (count($result) >= 1) {
                            $keys = array_keys($result);
                            foreach ($keys as $key) {
                                $percentage = $this->calculatePercentage($dateRangeDecoded, $key, $productID);
                                if ($percentage >= 100) {
                                    array_push($percentages, 0);
                                } else {
                                    array_push($percentages, $percentage);
                                }
                            }
                            array_push($offers, max($percentages));
                        } else {
                            array_push($offers, 0);
                        }
                        $res->reset();*/

                        // mehmet bey in iste??i do??rultusunda  special offers ??n tarih kontrol??nde k??????k bi de??i??iklik yap??yoruz

                            $dateRangeDecoded = json_decode($specialOffers->dateRange, true);
                        $jsonq = $apiRelated->prepareJsonQ();
                        $res = $jsonq->json($specialOffers->dateRange);
                        $result = $res->where('to', 'dateGte', $todayAsDmy)->get();
                        if (count($result) >= 1) {
                            $keys = array_keys($result);
                            $percentages = [];
                            foreach ($keys as $key) {
                                $percentage = $this->calculatePercentage($dateRangeDecoded, $key, $productID);
                                if ($percentage >= 100) {
                                    array_push($percentages, 0);
                                } else {
                                    array_push($percentages, $percentage);
                                }
                            }
                            $offers = max($percentages);
                        } else {
                            //array_push($offers, 0);
                        }
                        $res->reset();



                    } else {
                        //array_push($offers, 0);
                    }


                } else {
                    //array_push($offers, 0);
                }

            return $offers;
        } else {
            return 0;
        }
    }


















    /**
     * @param $productID
     * @return int|mixed
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function getOfferPercentage($product)
    {
        if(auth()->guard("web")->check()){
          $commission =  auth()->guard("web")->user()->commission;
            if(!is_null($commission) || $commission != 0){
             return 0;
            }
        }

        return $this->getTheCheapestOffer($product)["offer"];
    }

    ##########

    public function getTheCheapestOffer($product)
    {
        $productID = $product->id;
        $options = $product->options()->get();
        $apiRelated = new ApiRelated();
        $todayAsDmy = date('d/m/Y');
        $offerList = [];
        if (count($options) > 0) {
            foreach ($options as $option) {
                //array_push($offers, 0);
                $offers = 0;
                $specialOffers = SpecialOffers::where('productID', $productID)->where('optionID', $option->id)->first();

                $price = 0;
                $pricing = $option->pricings()->first();
                if($pricing)
                    $price = json_decode($pricing->adultPrice, true)[0];

                if(auth()->guard("web")->check() && !is_null(auth()->guard("web")->user()->commission) && auth()->guard("web")->user()->commission != 0){
                    array_push($offerList, ["price" => $price, "offer" => 0]);
                } else {
                    if ($specialOffers &&
                        (!is_null($specialOffers->dateRange) || !is_null($specialOffers->weekDay) || !is_null($specialOffers->randomDay) || !is_null($specialOffers->dateTimes))) {
                        if (!is_null($specialOffers->dateTimes)) {
                            $dateTimesDecoded = json_decode($specialOffers->dateTimes, true);
                            $jsonq = $apiRelated->prepareJsonQ();
                            $res = $jsonq->json($specialOffers->dateTimes);
                            $result = $res->where('day', 'dateGte', $todayAsDmy)->get();
                            if (count($result) >= 1) {
                                $keys = array_keys($result);
                                $percentages = [];
                                foreach ($keys as $key) {
                                    $percentage = $this->calculatePercentage4Cheapest($dateTimesDecoded, $key, $productID, $price);
                                    if ($percentage >= 100) {
                                        array_push($percentages, 0);
                                    } else {
                                        array_push($percentages, $percentage);
                                    }
                                }
                                $offers = max($percentages);
                            } else {
                                //array_push($offers, 0);
                            }
                            $res->reset();
                        }

                        if (!is_null($specialOffers->randomDay)) {
                            $randomDayDecoded = json_decode($specialOffers->randomDay, true);
                            $jsonq = $apiRelated->prepareJsonQ();
                            $res = $jsonq->json($specialOffers->randomDay);
                            $result = $res->where('day', 'dateGte', $todayAsDmy)->get();
                            if (count($result) >= 1) {
                                $keys = array_keys($result);
                                $percentages = [];
                                foreach ($keys as $key) {
                                    $percentage = $this->calculatePercentage4Cheapest($randomDayDecoded, $key, $productID, $price);
                                    if ($percentage >= 100) {
                                        array_push($percentages, 0);
                                    } else {
                                        array_push($percentages, $percentage);
                                    }
                                }
                                $offers = max($percentages);
                            } else {
                                //array_push($offers, 0);
                            }
                            $res->reset();
                        }

                        if (!is_null($specialOffers->weekDay)) {
                            $weekDayDecoded = json_decode($specialOffers->weekDay, true);
                            $jsonq = $apiRelated->prepareJsonQ();
                            $res = $jsonq->json($specialOffers->weekDay);
                            $result = $res->get();
                            if (count($result) >= 1) {
                                $keys = array_keys($result);
                                $percentages = [];
                                foreach ($keys as $key) {
                                    $percentage = $this->calculatePercentage4Cheapest($weekDayDecoded, $key, $productID, $price);
                                    if ($percentage >= 100) {
                                        array_push($percentages, 0);
                                    } else {
                                        array_push($percentages, $percentage);
                                    }
                                }
                                $offers = max($percentages);
                            } else {
                                //array_push($offers, 0);
                            }
                            $res->reset();
                        }

                        if (!is_null($specialOffers->dateRange)) {
                            $dateRangeDecoded = json_decode($specialOffers->dateRange, true);
                            $jsonq = $apiRelated->prepareJsonQ();
                            $res = $jsonq->json($specialOffers->dateRange);
                            $result = $res->where('to', 'dateGte', $todayAsDmy)->get();
                            if (count($result) >= 1) {
                                $keys = array_keys($result);
                                $percentages = [];
                                foreach ($keys as $key) {
                                    $percentage = $this->calculatePercentage4Cheapest($dateRangeDecoded, $key, $productID, $price);
                                    if ($percentage >= 100) {
                                        array_push($percentages, 0);
                                    } else {
                                        array_push($percentages, $percentage);
                                    }
                                }
                                $offers = max($percentages);
                            } else {
                                //array_push($offers, 0);
                            }
                            $res->reset();



                        } else {
                            //array_push($offers, 0);
                        }

                    } else {
                        //array_push($offers, 0);
                    }
                    array_push($offerList, ["price" => $price, "offer" => round($offers)]);
                }
            }

            $cheapestIndex = 0;
            $cheapestValue = 0;
            foreach($offerList as $ind => $olItem) {
                if($ind == 0)
                    $cheapestValue = $olItem["price"] - ($olItem["price"]*$olItem["offer"]/100);
                else {
                    if($olItem["price"] - ($olItem["price"]*$olItem["offer"]/100) < $cheapestValue)
                        $cheapestIndex = $ind;
                }
            }

            return $offerList[$cheapestIndex];
        } else {
            return 0;
        }
    }

    ##########

    /**
     * @param $decoded
     * @param $key
     * @param $productID
     * @return float|int
     */
    public function calculatePercentage($decoded, $key, $productID)
    {
        $discountType = $decoded[$key]['discountType'];
        $isActive = $decoded[$key]['isActive'];
        $isCommissioner = auth()->check() ? auth()->user()->commission : -1;
        $userType = $decoded[$key]['userType'];
        $pricing = $this->getMinPrice($productID);
        $percentage = 0;
        if ($isActive == 1) {
            if ($discountType == 'percentage') {
                if ($userType == 'All') {
                    $percentage = $decoded[$key]['discount'];
                } else if ($userType == 'Users' && $isCommissioner == null) {
                    $percentage = $decoded[$key]['discount'];
                } else if ($userType == 'Commissioners' && $isCommissioner != null) {
                    $percentage = $decoded[$key]['discount'];
                }
            } else {
                if ($userType == 'All') {
                    $discount = $decoded[$key]['discount'];
                    $percentage = (($discount / $pricing) * 100);
                } else if ($userType == 'Users' && $isCommissioner == null) {
                    $discount = $decoded[$key]['discount'];
                    $percentage = (($discount / $pricing) * 100);
                } else if ($userType == 'Commissioners' && $isCommissioner != null) {
                    $discount = $decoded[$key]['discount'];
                    $percentage = (($discount / $pricing) * 100);
                }
            }
        }
        return number_format($percentage, 3, '.', '');
    }

    public function calculatePercentage4Cheapest($decoded, $key, $productID, $pricing)
    {
        $discountType = $decoded[$key]['discountType'];
        $isActive = $decoded[$key]['isActive'];
        $isCommissioner = auth()->check() ? auth()->user()->commission : -1;
        $userType = $decoded[$key]['userType'];
        //$pricing = $this->getMinPrice($productID);
        $percentage = 0;
        if ($isActive == 1) {
            if ($discountType == 'percentage') {
                if ($userType == 'All') {
                    $percentage = $decoded[$key]['discount'];
                } else if ($userType == 'Users' && $isCommissioner == null) {
                    $percentage = $decoded[$key]['discount'];
                } else if ($userType == 'Commissioners' && $isCommissioner != null) {
                    $percentage = $decoded[$key]['discount'];
                }
            } else {
                if ($userType == 'All') {
                    $discount = $decoded[$key]['discount'];
                    $percentage = (($discount / $pricing) * 100);
                } else if ($userType == 'Users' && $isCommissioner == null) {
                    $discount = $decoded[$key]['discount'];
                    $percentage = (($discount / $pricing) * 100);
                } else if ($userType == 'Commissioners' && $isCommissioner != null) {
                    $discount = $decoded[$key]['discount'];
                    $percentage = (($discount / $pricing) * 100);
                }
            }
        }
        return number_format($percentage, 3, '.', '');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function uniqueIDForCart(Request $request)
    {
        // session()->flush();
        $uniqueID = $request->uniqueID;

        if (is_null((session()->get('uniqueID')))) {
            session()->put('uniqueID', $uniqueID);
        }

        return session()->get('uniqueID');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCurrencyCode(Request $request)
    {
        if ($request->has('currencyID') || !is_null($request->currencyID)) {
            $oldCurrencyID = session()->get('currencyCode');
            session()->put('currencyCode', $request->currencyID);
            $currency = Currency::findOrFail($request->currencyID);
            session()->put('currencyIcon', $currency->iconClass);

            // If there are items on cart, update them all
            $userID = session()->get('uniqueID');
            if (auth()->guard('web')->check()) {
                $userID = auth()->user()->id;
            }

            $carts = Cart::where('userID', $userID)->where('status', 0)->get();
            foreach ($carts as $cart) {
                $cart->currencyID = $request->currencyID;
                $cart->totalPrice = Currency::calculateCurrencyForVisitor($cart->totalPrice, $oldCurrencyID);
                $cart->totalPriceWOSO = Currency::calculateCurrencyForVisitor($cart->totalPriceWOSO, $oldCurrencyID);
                $cart->totalCommission = Currency::calculateCurrencyForVisitor($cart->totalCommission, $oldCurrencyID);
                $cart->maxCommission = Currency::calculateCurrencyForVisitor($cart->maxCommission, $oldCurrencyID);

               if($cart->tempTotalPrice){
                $cart->tempTotalPrice = Currency::calculateCurrencyForVisitor($cart->tempTotalPrice, $oldCurrencyID);
               }

                if($cart->tempCommission){
                $cart->tempCommission = Currency::calculateCurrencyForVisitor($cart->tempCommission, $oldCurrencyID);
               }


                $cart->save();
            }

            return response()->json(['success' => 'Currency Code is set successfully!', 'currencyCode' => session()->get('currencyCode'), 'currencyIcon' => session()->get('currencyIcon')]);
        } else {
            return response()->json(['error' => 'Error occured while changing currency. Please reload the page and try again.']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrencies(Request $request)
    {
        $currencies = Currency::where('isActive', 1)->get();
        return response()->json(['success' => 'Currencies successfully fetched', 'currencies' => $currencies]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setLocale(Request $request)
    {
        $isErrorPage = false;
        $lang = $request->lang;
        $oldLangCode = session()->get('userLanguage');
        session()->put('userLanguage', $lang);
        session()->put('detectedLocation', true);
        $language = Language::where('code', $lang)->first();
        $url = '';
        $allLanguages = Language::where('isActive', 1)->where('code', '!=', 'en')->get();
        $allLangCodesWoutEnglish = [];
        foreach ($allLanguages as $allLang) {
            array_push($allLangCodesWoutEnglish, '/'.$allLang->code);
        }
        $isRedirectable = false;
        if ($request->isProductPage == '1') {
            $currentUrl = $request->currentUrl;
            $product = Product::where('url', $currentUrl)->first();
            if ($product) {
                $productTranslation = ProductTranslation::where('productID', $product->id)
                    ->where('languageID', $language->id)
                    ->where(function ($query) {
                        $query->where('title', '!=', null)
                            ->where('shortDesc', '!=', null)
                            ->where('fullDesc', '!=', null)
                            ->where('highlights', '!=', null)
                            ->where('included', '!=', null)
                            ->where('notIncluded', '!=', null)
                            ->where('knowBeforeYouGo', '!=', null)
                            ->where('category', '!=', null)
                            ->where('cancelPolicy', '!=', null);
                    })->first();

                if ($productTranslation) {
                    $url = $productTranslation->url;
                } else {
                    $url = $product->url;
                }
            } else {
                $initialProdTranslated = ProductTranslation::where('url', $currentUrl)->first();
                $newProdTranslated = null;
                if ($initialProdTranslated) {
                    $newProdTranslated = ProductTranslation::where('productID', $initialProdTranslated->productID)
                        ->where('languageID', $language->id)
                        ->where(function ($query) {
                            $query->where('title', '!=', null)
                                ->where('shortDesc', '!=', null)
                                ->where('fullDesc', '!=', null)
                                ->where('highlights', '!=', null)
                                ->where('included', '!=', null)
                                ->where('notIncluded', '!=', null)
                                ->where('knowBeforeYouGo', '!=', null)
                                ->where('category', '!=', null)
                                ->where('cancelPolicy', '!=', null);
                        })->first();

                    if ($newProdTranslated) {
                        $url = $newProdTranslated->url;
                    } else {
                        $product = Product::findOrFail($initialProdTranslated->productID);
                        $url = $product->url;
                    }
                } else {
                    $product = Product::where('url', $currentUrl)->first();
                    $url = $product->url;
                }
            }
        } else if ($request->isAttractionPage == '1') {
            if ($oldLangCode != 'en') {
                if ($lang == 'en') {
                    $oldLanguage = Language::where('code', $oldLangCode)->first();
                    $currentAttractionSlug = explode('/', $request->currentUrl);
                    $currentAttractionSlug = end($currentAttractionSlug);
                    $slugArr = explode('-', $currentAttractionSlug);

                    unset($slugArr[count($slugArr)-1]);

                    $currentAttractionSlug = implode('-', $slugArr);



                    $attractionTranslation = AttractionTranslation::where('languageID', $oldLanguage->id)->where('slug', $currentAttractionSlug)->first();
                    $attraction = Attraction::findOrFail($attractionTranslation->attractionID);
                    $url = 'attraction/' . $attraction->slug;
                    $isRedirectable = true;
                } else {
                    $oldLanguage = Language::where('code', $oldLangCode)->first();

                    $currentAttractionSlug = explode('/', $request->currentUrl);
                    $currentAttractionSlug = end($currentAttractionSlug);



                    $slugArr = explode('-', $currentAttractionSlug);

                    unset($slugArr[count($slugArr)-1]);


                    $currentAttractionSlug = implode('-', $slugArr);


                    $attractionTranslation = AttractionTranslation::where('languageID', $oldLanguage->id)->where('slug', $currentAttractionSlug)->first();
                    $attractionID = $attractionTranslation->attractionID;
                    $newAttractionTranslation = AttractionTranslation::where('languageID', $language->id)->where('attractionID', $attractionID)->first();
                    $attractionRoute = Route::where('route', 'attraction')->first();
                    $currentAttractionLocalization = RouteLocalization::where('routeID', $attractionRoute->id)->where('languageID', $language->id)->first();
                    $url = $currentAttractionLocalization->route . '/' . $newAttractionTranslation->slug;
                    $isRedirectable = true;
                }
            } else {
                $currentAttractionSlug = explode('/', $request->currentUrl);
                $currentAttractionSlug = end($currentAttractionSlug);

                $slugArr = explode('-', $currentAttractionSlug);

                unset($slugArr[count($slugArr)-1]);
                $currentAttractionSlug = implode('-', $slugArr);

                $attraction = Attraction::where('slug', $currentAttractionSlug)->first();
                $attractionTranslation = AttractionTranslation::where('attractionID', $attraction->id)->where('languageID', $language->id)->first();
                $attractionRoute = Route::where('route', 'attraction')->first();
                $currentAttractionLocalization = RouteLocalization::where('languageID', $language->id)->where('routeID', $attractionRoute->id)->first();
                $url = $currentAttractionLocalization->route . '/' . $attractionTranslation->slug;
                $isRedirectable = true;
            }
        }else if($request->isBlogDetailPage == "1"){
          $currentURL = $request->currentUrl;
          $lang = $request->lang;
          $oldLangCode = $oldLangCode;
          $newLangID = Language::where('isActive', 1)->where('code',$lang)->first()->id;
          $urlArray = explode('/', $currentURL);
          //return response($urlArray);

          if($oldLangCode == "en"){
            array_shift($urlArray);
            array_shift($urlArray);
            $urlArray = '/'.join('/', $urlArray);
            //return response($urlArray);
            $blogPost = BlogPost::where('url', $urlArray)->first();
          }else{
            array_shift($urlArray);
            array_shift($urlArray);
            array_shift($urlArray);
            $urlArray = '/'.join('/', $urlArray);



            $blogPostTranslation = BlogTranslation::where('url', $urlArray)->first();
            $blogPost = BlogPost::findOrFail($blogPostTranslation->blogID);

          }

          if($lang == "en"){
            $url = 'blog' . $blogPost->url;
                $isRedirectable = true;

          }else{
            if(!empty($blogPost->translations)){
                $url = 'blog' . $blogPost->translations->url;
                $isRedirectable = true;
            }else{
                 $url = 'blog' . $blogPost->url;
                 $isRedirectable = false;
            }



          }

        }



         else if ($request->currentUrl == '/' || in_array($request->currentUrl, $allLangCodesWoutEnglish)) {
            //return response()->json("test");
            $url = $request->currentUrl;
        } else {
            if (is_null($oldLangCode)) {
                $currentUrl = str_replace('/', '', $request->currentUrl);
                $route = Route::where('route', $currentUrl)->first();
                $language = Language::where('code', $lang)->first();
                $routeLocalization = RouteLocalization::where('routeID', $route->id)->where('languageID', $language->id)->first();
                $url = $routeLocalization->route;
                $isRedirectable = true;
            }
            if (!is_null($oldLangCode) && $oldLangCode != 'en') {

                if ($lang == 'en') {
                    //return response()->json("test");
                    $oldLanguage = Language::where('code', $oldLangCode)->first();
                    $oldUrl = str_replace('/' . $oldLangCode . '/', '', $request->currentUrl);
                    $routeLocalization = RouteLocalization::where('languageID', $oldLanguage->id)->where('route', $oldUrl)->first();
                    if ($routeLocalization) {
                        $route = Route::findOrFail($routeLocalization->routeID);
                        $url = $route->route;
                        $isRedirectable = true;
                    }else{
                        $isErrorPage = true;
                        $url = $oldUrl;
                        $isRedirectable = true;
                    }
                } else {

                    $oldLanguage = Language::where('code', $oldLangCode)->first();
                    $oldUrl = str_replace('/' . $oldLangCode . '/', '', $request->currentUrl);
                    $routeLocalization = RouteLocalization::where('languageID', $oldLanguage->id)->where('route', $oldUrl)->first();


                    if(empty($routeLocalization)){
                        $isErrorPage = true;
                        $url = $oldUrl;
                        $isRedirectable = true;

                    }else{

                    $newRouteLocalization = RouteLocalization::where('languageID', $language->id)->where('routeID', $routeLocalization->routeID)->first();
                    if ($newRouteLocalization) {
                        $url = $newRouteLocalization->route;
                        $isRedirectable = true;
                    }

                    }




                }
            } else {

                $currentUrl = str_replace('/', '', $request->currentUrl);
                $route = Route::where('route', $currentUrl)->first();

              if(empty($route)){
                $isErrorPage = true;
                $url = $currentUrl;
                $isRedirectable = true;
                }else{
                $language = Language::where('code', $lang)->first();
                $routeLocalization = RouteLocalization::where('routeID', $route->id)->where('languageID', $language->id)->first();
                $url = $routeLocalization->route;
                $isRedirectable = true;
                }





            }
        }

        return response()->json(['success' => 'Language is set successfully', session()->get('userLanguage'), 'url' => $url, 'isRedirectable' => $isRedirectable, "isErrorPage" => $isErrorPage]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocales(Request $request)
    {
        $languages = Language::where('isActive', 1)->get();
        return response()->json(['success' => 'Languages successfully fetched', 'languages' => $languages]);
    }

    /**
     * Rounds up a decimal (like price) to the given order --> Example: 123.236 to 123.24
     *
     * @param $decimal
     * @param $order
     * @return float|int
     */
    public function roundUp($decimal, $order)
    {
        $multiplied = pow(10, $order);
        return ceil($decimal * $multiplied) / $multiplied;
    }

    /**
     * @param $array
     * @param $key
     * @return array
     */
    public function unique_multidimensional_array($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    /**
     * @param $id
     * @return int|mixed
     */
    public function calculateAttractionMinPrice($id)
    {
        $products = Product::where('attractions', 'like', '%"'.$id.'"%')
            ->where('isDraft', 0)->where('isPublished', 1)->get();
        $allOptions = [];
        foreach ($products as $product) {
            $options = $product->options()->get();
            foreach ($options as $opt) {
                array_push($allOptions, $opt);
            }
        }

        $allOptions = array_values($this->unique_multidimensional_array($allOptions, 'id'));
        $allPricings = [];
        foreach ($allOptions as $o) {
            if ($o->isPublished == 1) {
                $pricing = $o->pricings()->first(); // As there is one pricing per option
                if ($pricing) {
                    $adultPrice = json_decode($pricing->adultPrice, true);
                    array_push($allPricings, $adultPrice[0]);
                } else {
                    array_push($allPricings, 0);
                }
            }
        }
        if (count($allPricings) == 0) {
            return 0;
        }
        return min($allPricings);
    }

    /**
     * @param $productID
     */
    public function unsetProductFromProductOrderForOtherPage($productID)
    {
        $pages = Page::where('productOrder', 'like', '%"'.$productID.'"%')->get();
        foreach ($pages as $page) {
            $productOrder = json_decode($page->productOrder, true);
            if (($key = array_search($productID, $productOrder)) != false) {
                unset($productOrder[$key]);
                if (count($productOrder) == 0) {
                    $productOrder = null;
                    $page->productOrder = $productOrder;
                } else {
                    $page->productOrder = json_encode($productOrder);
                }
                $page->save();
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCities(Request $request)
    {
        $country = Country::findOrFail($request->countryID);
        $citiesArr = [];
        $cities = $country->cities;
        foreach ($cities as $c) {
            array_push($citiesArr, $c->name);
        }

        return ['cities' => $citiesArr];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttractionsByCity(Request $request)
    {
        $attractions = Attraction::where('cities', null)->orWhere('cities', 'like', '%'.$request->city.'%')->get();

        return response()->json(['success' => 'Successful', 'attractions' => $attractions]);
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getRouteLocalization($param)
    {
        $lang = session()->get('userLanguage') ? session()->get('userLanguage') : 'en';
        $route = Route::where('route', $param)->first();
        if ($lang == 'en') {
            $url = $route->route;
        } else {
            $language = Language::where('code', $lang)->first();
            $routeTranslation = RouteLocalization::where('routeID', $route->id)->where('languageID', $language->id)->first();
            $url = $routeTranslation->route;
        }
        return $url;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getAttractionLocalization($attraction, $language)
    {
        try {
            if ($language->code == 'en') {
                $url = $attraction->slug;
            } else {
                $attactionTranslation = $attraction->translations;
                $url = $attactionTranslation->slug;
            }
            return $url;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param $length
     * @return string
     */
    public function getRandomNumber($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    /**
     * @param $c
     * @param $type
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function changeSoldCount($c, $type)
    {
        $apiRelated = new ApiRelated();
        $usedAvs = Option::findOrFail($c->optionID)->avs()->get();
        $day = $c->date;
        $ticketCount = $c->ticketCount;
        $hourDecoded = json_decode($c->hour, true);
        foreach ($usedAvs as $i => $av) {
            if ($av->avTicketType == 1) {
                $hour = $hourDecoded[$i]['hour'];
                $hourlyDecoded = json_decode($av->hourly, true);
                $jsonq = $apiRelated->prepareJsonQ();
                $res = $jsonq->json($av->hourly);
                $result = $res->where('day', '=', $day)->where('hour', '=', $hour)->get();
                if (count($result) == 1) {
                    $key = key($result);
                    if ($type == '+') {
                        $hourlyDecoded[$key]['sold'] += $ticketCount;
                    } else {
                        $hourlyDecoded[$key]['sold'] -= $ticketCount;
                    }
                    $av->hourly = json_encode($hourlyDecoded);
                    $av->save();
                }
            }
            if ($av->avTicketType == 2) {
                $hourFromRaw = $hourDecoded[$i]['hour'];
                $hourFromExploded = explode(' - ', $hourFromRaw);
                $hourFrom = $hourFromExploded[0];
                $hourTo = $hourFromExploded[1];
                $dailyDecoded = json_decode($av->daily, true);
                $jsonq = $apiRelated->prepareJsonQ();
                $res = $jsonq->json($av->daily);
                $result = $res->where('day', '=', $day)->where('hourFrom', '=', $hourFrom)
                    ->where('hourTo', '=', $hourTo)->get();
                if (count($result) == 1) {
                    $key = key($result);
                    if ($type == '+') {
                        $dailyDecoded[$key]['sold'] += $ticketCount;
                    } else {
                        $dailyDecoded[$key]['sold'] -= $ticketCount;
                    }
                    $av->daily = json_encode($dailyDecoded);
                    $av->save();
                }
            }
            if (in_array($av->avTicketType, [3, 4])) {
                $column = $av->avTicketType == 3 ? $av->dateRange : $av->barcode;
                $columnDecoded = json_decode($column, true);
                $jsonq = $apiRelated->prepareJsonQ();
                $res = $jsonq->json($column);
                $result = $res->where('dayFrom', 'dateLte', $day)->where('dayTo', 'dateGte', $day)->get();
                if (count($result) == 1) {
                    $key = key($result);
                    if ($type == '+') {
                        $columnDecoded[$key]['sold'] += $ticketCount;
                    } else {
                        $columnDecoded[$key]['sold'] -= $ticketCount;
                    }
                    if ($av->avTicketType == 3) {
                        $av->dateRange = json_encode($columnDecoded);
                    } else if ($av->avTicketType == 4) {
                        $av->barcode = json_encode($columnDecoded);
                    }
                    $av->save();
                }

                if($av->availabilityType == "Starting Time") {
                    $hour = $hourDecoded[$i]['hour'];
                    $hourlyDecoded = json_decode($av->hourly, true);
                    $jsonq = $apiRelated->prepareJsonQ();
                    $res = $jsonq->json($av->hourly);
                    $result = $res->where('day', '=', $day)->where('hour', '=', $hour)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        if ($type == '+') {
                            $hourlyDecoded[$key]['sold'] += $ticketCount;
                        } else {
                            $hourlyDecoded[$key]['sold'] -= $ticketCount;
                        }
                        $av->hourly = json_encode($hourlyDecoded);
                        $av->save();
                    }
                } elseif($av->availabilityType == "Operating Hours") {
                    $hourFromRaw = $hourDecoded[$i]['hour'];
                    $hourFromExploded = explode(' - ', $hourFromRaw);
                    $hourFrom = $hourFromExploded[0];
                    $hourTo = $hourFromExploded[1];
                    $dailyDecoded = json_decode($av->daily, true);
                    $jsonq = $apiRelated->prepareJsonQ();
                    $res = $jsonq->json($av->daily);
                    $result = $res->where('day', '=', $day)->where('hourFrom', '=', $hourFrom)
                        ->where('hourTo', '=', $hourTo)->get();
                    if (count($result) == 1) {
                        $key = key($result);
                        if ($type == '+') {
                            $dailyDecoded[$key]['sold'] += $ticketCount;
                        } else {
                            $dailyDecoded[$key]['sold'] -= $ticketCount;
                        }
                        $av->daily = json_encode($dailyDecoded);
                        $av->save();
                    }
                }
            }
        }
    }


    public function showStarsforRate($rate){
        $rateHTML = '';

     if(!$rate == null){
       if(((int)$rate + 0.5) >= $rate && (int)$rate != $rate){

        for ($i=1; $i <= 5; $i++) {
           if(($i < $rate)){
            // tam y??ld??z
            $rateHTML.= '<i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>';

           }elseif(($i > $rate) && (ceil($rate) == $i)){
            $rateHTML.= '<i class="icon-cz-star-half" style="color: #ffad0c; font-size: 15px;"></i>';
           // yar??m y??ld??z
           }
           else{
            $rateHTML.= '<i class="icon-cz-star-empty" style="color: #ffad0c; font-size: 15px;"></i>';
           // bo?? y??ld??z
           }
        }

       }elseif(((int)$rate + 0.5) < $rate){

        for ($i=1; $i <= 5; $i++) {

            if($i <= ceil($rate)){
               $rateHTML.= '<i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>';
            }else{
                $rateHTML.= '<i class="icon-cz-star-empty" style="color: #ffad0c; font-size: 15px;"></i>';
            }

        }

       }else{

        for ($i=1; $i <= 5; $i++) {
            if($rate >= $i){
           $rateHTML.= '<i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>';
            }else{
           $rateHTML.= '<i class="icon-cz-star-empty" style="color: #ffad0c; font-size: 15px;"></i>';
            }
        }

       }
     }else{
      $rateHTML .= '<div style="font-size: 13px;vertical-align: text-bottom;padding-top: 3%;">No reviews yet</div>';
     }

     return $rateHTML;


    }

    public function getProductSkills($product=null):array{
      $responseArray = [];
        if(($product->options()->count()) == 0){
         return $responseArray;
        }




    foreach ($product->options()->get() as $opt) {
       if($opt->tourDuration){
        $responseArray["tourDuration"] = $opt->tourDuration;

        if($opt->tourDurationDate == "h"){
            $responseArray["tourDurationDate"] = (int)$responseArray["tourDuration"] <= 1 ? "Hour" : "Hours";

        }elseif($opt->tourDurationDate == "m"){
            $responseArray["tourDurationDate"] = (int)$responseArray["tourDuration"] <= 1 ? "Minute" : "Minutes";

        }else{
           $responseArray["tourDurationDate"] = (int)$responseArray["tourDuration"] <= 1 ? "Day" : "Days";
        }

       }

       if($opt->isFreeCancellation == 1){
        $responseArray["isFreeCancellation"] = $opt->isFreeCancellation;
       }

       if($opt->isSkipTheLine == 1){
        $responseArray["isSkipTheLine"] = $opt->isSkipTheLine;
       }

       if(!is_null($opt->guideInformation)){
        $responseArray["guideInformation"] = json_decode($opt->guideInformation, true);
       }
    }

    return $responseArray;

    }

}
