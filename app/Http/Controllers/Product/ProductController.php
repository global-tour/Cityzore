<?php

namespace App\Http\Controllers\Product;
use App\RouteLocalization;
use App\AttractionTranslation;
use App\Cart;
use App\City;
use App\CityTranslation;
use App\Comment;
use App\Commission;
use App\CountryTranslation;
use App\FAQ;
use App\Http\Controllers\Helpers\TimeRelatedFunctions;
use App\Language;
use App\Option;
use App\Page;
use App\Product;
use App\Avdate;
use App\ProductGallery;
use App\SpecialOffers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Helpers\CommonFunctions;
use DateInterval;
use DatePeriod;
use App\Http\Controllers\Helpers\ApiRelated;
use App\Http\Controllers\Helpers\TootbusRelated;
use App\Config;
use App\Currency;
use App\Attraction;
use App\Country;
use App\ProductTranslation;
use App\Wishlist;
use App\ProdMetaTagsTrans;
use Illuminate\Support\Facades\Storage;
use App\OldProductTranslation;
use App\OldProduct;
use App\TootbusConnection;
use App\Av;
use App\Booking;
use DB;
use App\OptionTranslation;

class ProductController extends Controller
{

    public $apiRelated;
    public $timeRelatedFunctions;
    public $commonFunctions;
    public $requestCategories;

    public function __construct()
    {
        $this->apiRelated = new ApiRelated();
        $this->timeRelatedFunctions = new TimeRelatedFunctions();
        $this->commonFunctions = new CommonFunctions();
        $this->requestCategories = [];
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productPage()
    {
        $categoriesOfProduct = Product::select('category')->where('isPublished', '1')->where('isDraft', '0')->distinct('category')->get();
        $categories = [];
        foreach ($categoriesOfProduct as $category) {
            array_push($categories, $category['category']);
        }

        $attractionsOfProduct = Product::select('attractions')->where('isPublished', '1')->where('isDraft', '0')->distinct('attractions')->get();
        $uniqueAttrIds = [];
        foreach ($attractionsOfProduct as $attr) {
            $attrIds = json_decode($attr['attractions'], true);
            foreach ($attrIds as $id) {
                if (!in_array($id, $uniqueAttrIds)) {
                    array_push($uniqueAttrIds, $id);
                }
            }
        }
        $attractions = Attraction::whereIn('id', $uniqueAttrIds)->get();

        return view('frontend.all-products', ['categories' => $categories, 'attractions' => $attractions]);
    }

    /**
     * @param $lang
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function attractionPage($lang, $attractionId)
    {


        $categoriesOfProduct = Product::select('category')->where('isPublished', '1')->where('isDraft', '0')->distinct('category')->get();
        $categories = [];
        foreach ($categoriesOfProduct as $category) {
            array_push($categories, $category['category']);


        }
        /* $attractionId = explode("-", $attractionId);
         $attractionId = count($attractionId)-1;
         $attraction = Attraction::findOrFail($attractionId);*/

        $path = request()->path();
        $arr = explode('/', $path);
        $slug = $arr[count($arr)-1];
        $slugArr = explode('-', $slug);
        $lastItem = array_pop($slugArr);
        $originalSlug = join("-", $slugArr);



        //burası sonradan eklendi

        $translationData  = AttractionTranslation::where("slug",$originalSlug)->first();
        $attraction = Attraction::findOrFail($translationData->attractionID);


        $attractionID = $attraction->id == $lastItem ? $attraction->id : $lastItem;


        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;
        $routeTrans = RouteLocalization::where('routeID', 27)->where('languageID', $langID)->first();
        if ($routeTrans) {
            $routeTrans = $routeTrans->route;
        } else {
            $routeTrans = 'attraction';
        }


        if ($langCode != "en") {

            $targetAttractionTranslation = AttractionTranslation::where('attractionID', $translationData->attarctionID)->where('languageID', $langID)->first();

            if ($targetAttractionTranslation) {


                if ($targetAttractionTranslation->slug != urldecode(($originalSlug))) {
                    return redirect($langCode . '/' . $routeTrans . '/' . $targetAttractionTranslation->slug . '-' . $attractionID, 301);
                }


            } else {

            }


        }


        return view('frontend.attractions', ['categories' => $categories, 'attraction' => $attraction]);
    }


    public function paginateAttractionPage($lang, $slug)
    {


        $categories_from_form = request()->category;
        $prices_from_form = request()->price;
        $fromDate_from_form = request()->from_date;
        $toDate_from_form = request()->to_date;
        $sort_from_form = request()->sort;


        if (!empty($fromDate_from_form) && !empty($toDate_from_form)) {
            $fromDate_from_form = Carbon::createFromFormat('d/m/Y', request()->from_date)->format('Y-m-d');
            $toDate_from_form = Carbon::createFromFormat('d/m/Y', request()->to_date)->format('Y-m-d');
        }


        $categoriesOfProduct = Product::select('category')->where('isPublished', '1')->where('isDraft', '0')->distinct('category')->get();
        $categories = [];
        foreach ($categoriesOfProduct as $category) {
            array_push($categories, $category['category']);
        }
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;

        $slugArr = explode("-", $slug);
        array_pop($slugArr);
        $slug = join("-", $slugArr);


        $searchURL = "attraction/" . $slug;


        $productOrder = [1];
        if (Page::where('url', "like", "%" . $searchURL . "%")->count()) {
            $productOrder = Page::where('url', "like", "%" . $searchURL . "%")->first()->productOrder;
            if (!empty($productOrder) && $productOrder != "null") {
                $productOrder = json_decode($productOrder, true);
            } else {
                $productOrder = [1];
            }
        }
        $idss = implode(',', array_reverse($productOrder));


        if ($langCode == 'en') {
            $attraction = Attraction::where("slug", $slug)->first();
            $attractionID = $attraction->id;
        } else {
            $attraction = AttractionTranslation::where("slug", $slug)->first();

            $attractionID = $attraction->attractionID;
        }


        $products = Product::whereJsonContains('attractions', ['' . $attractionID . ''])->where('isPublished', '1')->where('isDraft', '0')->where(function ($q) use ($categories_from_form, $langCode) {


            if (!empty($categories_from_form)) {
                $counter = 0;

                foreach ($categories_from_form as $category) {
                    if ($langCode != 'en')
                        $category = \App\Category::where('id', \App\CategoryTranslation::where('categoryName', $category)->first()->categoryID)->first()->categoryName;
                    array_push($this->requestCategories, $category);
                    $queryStr = $counter === 0 ? 'where' : 'orWhere';
                    $q->$queryStr('category', $category);
                    $counter++;
                }

            }

        })->orderByRaw(DB::raw("FIELD(id, $idss) desc"))->get()->filter(function ($model) use ($prices_from_form) {
            $min_price = $this->commonFunctions->getMinPrice($model->id);
            $specialOffer = $this->commonFunctions->getOfferPercentage($model);


            if ($specialOffer) {
                $min_price = ($min_price - $min_price * ($specialOffer / 100));
            }

            if (!empty($prices_from_form)) {
                $control = 0;
                foreach ($prices_from_form as $pr) {
                    $prArr = explode("-", $pr);
                    $prMin = (int)$prArr[0];
                    $prMax = (int)$prArr[1];

                    if ($min_price >= $prMin && $min_price <= $prMax) {
                        $control = 1;
                    }
                }
                if ($control) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return true;
            }


        });


        if (!empty($fromDate_from_form) && !empty($toDate_from_form)) {


            $availabilities = [];
            $options = [];
            $productsBetweenDates = [];
            $productsIdArr = [];

            $avdates = Avdate::whereHas("av", function ($q) {
                $q->where("id", 1);
            })->where(function ($q) use ($fromDate_from_form, $toDate_from_form) {
                $q->where('valid_from', '<=', $fromDate_from_form);
                $q->where('valid_to', '>=', $toDate_from_form);

            })->orWhere(function ($q) use ($fromDate_from_form, $toDate_from_form) {

                $q->where('valid_from', '>=', $fromDate_from_form);
                $q->where('valid_to', '<=', $toDate_from_form);
            })
                ->orWhere(function ($q) use ($fromDate_from_form, $toDate_from_form) {

                    $q->where('valid_from', '>=', $fromDate_from_form);
                    $q->where('valid_from', '<=', $toDate_from_form);
                })
                ->orWhere(function ($q) use ($fromDate_from_form, $toDate_from_form) {

                    $q->where('valid_to', '>=', $fromDate_from_form);
                    $q->where('valid_to', '<=', $toDate_from_form);
                })
                ->get();

            //dd($avdates);


            foreach ($avdates as $avdate) {
                if (Carbon::parse($avdate->valid_to)->timestamp < Carbon::now()->timestamp) {
                    continue;
                }

                $availability = $avdate->av()->first();


                if (!empty($availability)) {
                    $control = [];

                    $jsonq = $this->apiRelated->prepareJsonQ();
                    if ($availability->availabilityType == "Starting Time") {
                        $res = $jsonq->json($availability->hourly == "[]" ? "[{}]" : $availability->hourly);
                    } else {
                        $res = $jsonq->json($availability->daily == "[]" ? "[{}]" : $availability->daily);
                    }
                    $disabled_days = json_decode($availability->disabledDays, true);
                    $start_date = \Carbon\Carbon::parse($avdate->valid_from);
                    $end_date = \Carbon\Carbon::parse($avdate->valid_to);
                    while (!$start_date->eq($end_date)) {
                        $result = $res->where("day", "=", $start_date->format("d/m/Y"))->where("isActive", "=", 1)->where("ticket", "!=", 0)->get();
                        if (count($result) > 0) {
                            $result_day = $result[key($result)]["day"];
                            $result_timestamp = Carbon::createFromFormat("d/m/Y", $result_day)->timestamp;
                            $today_timestamp = Carbon::now()->timestamp;
                            if ($result_timestamp >= $today_timestamp) {
                                if (!in_array($start_date->format("d/m/Y"), $disabled_days)) {
                                    $control[] = 1;
                                } else {
                                    $control[] = 0;
                                }
                            }
                        } else {
                            $control[] = 0;
                        }
                        $start_date->addDay();

                    }
                    $res->reset();
                }
                if ($availability && array_sum($control) > 0) {
                    array_push($availabilities, $availability);
                }
            }
            foreach ($availabilities as $availability) {
                $optionsOfAv = $availability->options()->get();
                foreach ($optionsOfAv as $optionOfAv) {
                    array_push($options, $optionOfAv);
                }
            }
            foreach ($options as $option) {
                $productsOfOpt = $option->products()->get();
                foreach ($productsOfOpt as $productOfOpt) {
                    if ($productOfOpt->isPublished == 1 && $productOfOpt->isDraft == 0) {
                        array_push($productsBetweenDates, $productOfOpt);
                    }
                }
            }
            $productsBetweenDates = array_values($this->commonFunctions->unique_multidimensional_array($productsBetweenDates, 'id'));

            $products = $products->filter(function ($model) use ($productsBetweenDates) {

                $productID = $model->id;
                $control = 0;

                foreach ($productsBetweenDates as $prbd) {
                    if ($prbd->id == $productID) {
                        $control = 1;
                        break;
                    }

                }
                if ($control) {
                    return true;
                } else {
                    return false;
                }

            });

        }

        $products = $products->map(function ($model) {
            $price = $this->commonFunctions->getMinPrice($model->id);
            $rating = $model->rate;
            $specialOffer = $this->commonFunctions->getOfferPercentage($model);

            return [
                'sortPrice' => !$specialOffer ? $price : ($price - $price * ($specialOffer / 100)),
                'price' => $price,
                'rating' => $rating,
                'special' => (int)$specialOffer,
                'm' => $model
            ];

        });

        if (!empty($sort_from_form)) {
            if ($sort_from_form == "price-asc") {
                $products = $products->sortBy('sortPrice');
            }

            if ($sort_from_form == "price-desc") {
                $products = $products->sortByDesc('sortPrice');
            }

            if ($sort_from_form == "rating-desc") {
                $products = $products->sortByDesc('rating');
            }
        }

        $original = collect($products)->paginate(10);

        return view('frontend.paginate-attractions', ['categories' => $categories, 'attraction' => $attraction, 'attractionID' => $attractionID, 'products' => $original, 'requestCategories' => $this->requestCategories]);
    }


    public function paginateAttractionPageOtherLanguage(Request $request, $lang, $slug)
    {

        $categoriesOfProduct = Product::select('category')->where('isPublished', '1')->where('isDraft', '0')->distinct('category')->get();
        $categories = [];
        foreach ($categoriesOfProduct as $category) {
            array_push($categories, $category['category']);
        }
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;

        $slugArr = explode("-", $slug);
        array_pop($slugArr);
        $slug = join("-", $slugArr);


        if ($langCode == 'en') {
            $attraction = Attraction::where("slug", $slug)->first();
            $attractionID = $attraction->id;
        } else {
            $attraction = AttractionTranslation::where("slug", $slug)->first();

            $attractionID = $attraction->attractionID;
        }


        if ($langCode == 'en') {

            $products = Product::whereJsonContains('attractions', ['' . $attractionID . ''])->where('isPublished', '1')->where('isDraft', '0')->paginate(6);


        } else {

            $products = Product::whereJsonContains('attractions', ['' . $attractionID . ''])->where('isPublished', '1')->where('isDraft', '0')->paginate(6);

        }


        return view('frontend.paginate-attractions', ['categories' => $categories, 'attraction' => $attraction, 'products' => $products]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function allAttractionsPage()
    {

        $attractions = Attraction::where('isActive', 1)->get();
        $categoriesOfProduct = Product::select('category')->where('isPublished', '1')->where('isDraft', '0')->distinct('category')->get();
        $categories = [];
        foreach ($categoriesOfProduct as $category) {
            array_push($categories, $category['category']);
        }
        return view('frontend.allAttractions', ['categories' => $categories, 'attractions' => $attractions]);
    }

    /**
     * @param Request $request
     * @param $lang
     * @param $location
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getProduct(Request $request, $lang, $location, $slug)
    {
        // This function is created only for testing purposes. It will be deleted after availability is truly functional.
        // product_id must be changed for testing S, O, S+S, O+O, S+O
        $products = Product::where('isDraft', '=', '0')->where('isPublished', '=', '1');
        $product = Product::where('url', $location . '/' . $slug)->first();

        // Check for broken and old urls (without id at the end)
        if (is_null($product)) {
            if ($lang != 'en') {
                $isOldProductTranslationUrl = OldProductTranslation::where('oldUrl', $location . '/' . $slug)->first();
                if ($isOldProductTranslationUrl) {
                    $newProductTranslation = ProductTranslation::findOrFail($isOldProductTranslationUrl->productTranslationID);
                    return redirect('/' . $lang . '/' . $newProductTranslation->url, 301);
                }
            }

            $isOldProductUrl = OldProduct::where('oldUrl', $location . '/' . $slug)->first();
            if ($isOldProductUrl) {
                $newProduct = Product::findOrFail($isOldProductUrl->productID);
                return redirect('/' . $newProduct->url, 301);
            }
        }

        // Check for product translations
        if (is_null($product)) {
            $isProductTranslationSlug = ProductTranslation::where('url', $location . '/' . $slug)->first();
            if (!is_null($isProductTranslationSlug)) {
                $product = Product::findOrFail($isProductTranslationSlug->productID);
            }
        }

        // Check for broken links
        if (is_null($product)) {
            $explodedURL = explode('-', $slug);
            $explodedProductID = end($explodedURL);

            if ($lang != 'en') {
                $langForBrokenLinkUser = Language::where('code', $lang)->first();
                // If it's broken and not english, search in translations
                $isItTranslated = ProductTranslation::where('productID', $explodedProductID)->where('languageID', $langForBrokenLinkUser->id)->first();
                if ($isItTranslated) {
                    return redirect('/' . $lang . '/' . $isItTranslated->url, 301);
                }
            }

            // If it's broken and not on translations, search in products
            $isThisBrokenLinkNewProduct = Product::findOrFail($explodedProductID);
            if ($isThisBrokenLinkNewProduct) {
                return redirect('/' . $isThisBrokenLinkNewProduct->url, 301);
            }
        }

        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $language = Language::where('code', $langCode)->first();
        if (is_null($product) || $product->isDraft == 1 || $product->supplierPublished == 0) {
            abort(404);
        }
        $productTranslation = ProductTranslation::where('productID', $product->id)->where('languageID', $language->id)
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
        $id = $product->id;
        $isThereProdMetaTagsTranslation = ProdMetaTagsTrans::where('productID', $product->id)
            ->where('languageID', $language->id)->first();
        $metaTag = null;
        if ($isThereProdMetaTagsTranslation) {
            $metaTag = $isThereProdMetaTagsTranslation;
        } else {
            $metaTag = $product->metaTag()->first() ? $product->metaTag()->first() : null;
        }

        $comments = Comment::where('productID', $id)->where('status', '1')->orderBy('created_at', 'desc')->get();
        $options = $product->options()->where('isPublished', '=', 1)->get();
        $prices = [];
        $ignoredCategoriesArray = [];
        foreach ($options as &$o) {
            $specials = SpecialOffers::where('productID', '=', $product->id)->where('optionID', $o->id)->first();


            if (auth()->check()) {
                if (auth()->guard("web")->user()->commission != 0 || !is_null(auth()->guard("web")->user()->commission)) {
                    $specials = null;
                }
            }


            $pricing = $o->pricings()->first();
            $ignoredCategories = json_decode($pricing->ignoredCategories, true);
            array_push($ignoredCategoriesArray, $ignoredCategories);
            $price = json_decode($pricing->adultPrice, true)[0];
            array_push($prices, $price);

            if($specialOfferForThisOption = $this->commonFunctions->getOfferPercentageForSpecificOption($product, $o)){
               $modifiedPrice = Currency::calculateCurrencyForVisitor($price) - Currency::calculateCurrencyForVisitor($price)*((int)$specialOfferForThisOption/100);
            }else{
               $modifiedPrice = Currency::calculateCurrencyForVisitor($price);
            }
            $o['modifiedPrice'] = (float)$modifiedPrice;
            $o['price'] = $price;
        }
        $options = $options->sortBy('modifiedPrice')->values();
        //dd($options);
        $minPrices = min($prices);
        $image = ProductGallery::where('id', $product->coverPhoto)->first() ? ProductGallery::where('id', $product->coverPhoto)->first()->src : ProductGallery::findOrFail(53)->src;
        $productImages = $product->productGalleries()->get();
        $productImagesOrdered = $productImages;
        if (!is_null($product->imageOrder)) {
            $imageOrder = json_decode($product->imageOrder, true);
            $productImagesOrdered = [];

            foreach ($productImages as $i => $productImage) {
                $prodIm = $productImages->where('id', $imageOrder[$i])->first();
                if ($prodIm) {
                    $productImagesOrdered[$i] = $prodIm;
                }
            }
        }
        $optionName = null;
        $totalPrice = null;
        $cart = null;
        $user = Auth::guard('web')->user();

        if ($user) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
        } else {
            $cart = Cart::where('userID', '=', session()->get('uniqueID'))->where('status', '=', 0)->get();
        }

        $cartCount = count($cart);

        $items = [];
        $ids = [];
        $totalPrice = [];
        foreach ($cart as $c) {
            array_push($items, json_decode($c->bookingItems, true));
            array_push($ids, json_decode($c->id, true));
            array_push($totalPrice, json_decode($c->totalPrice, true));
        }

        $optionsTranslation = [];
        foreach ($options as $opt) {
            $optionID = $opt->id;
            $optionName = $options->where('id', '=', $optionID)->first()->title;

            $optionTranslation = OptionTranslation::where('optionID', $opt->id)->where('languageID', $language->id)
                ->where(function ($query) {
                    $query->where('title', '!=', null)
                        ->where('description', '!=', null)
                        ->where('meetingComment', '!=', null)
                        ->where('included', '!=', null)
                        ->where('notIncluded', '!=', null)
                        ->where('knowBeforeYouGo', '!=', null);
                })->first();

            array_push($optionsTranslation, $optionTranslation);
        }
        $euro = Currency::findOrFail(2);
        $euroValue = $euro->value;
        $wishlist = null;
        if (auth()->guard('web')->check()) {
            $wishlist = Wishlist::where('userID', auth()->guard('web')->user()->id)
                ->where('productID', $product->id)->first();
        }

        $productTempArray = [];
        $productAttraction = [];
        foreach (json_decode($product->attractions, true) as $attraction) {
            foreach ($products->where('isSpecial', '!=', 1)->get() as $productForYouMightAlsoLike) {
                foreach (array(json_decode($productForYouMightAlsoLike->attractions, true)) as $otherProductAttraction) {
                    if ($otherProductAttraction == $attraction) {
                        array_push($productTempArray, $productForYouMightAlsoLike);
                    }
                }
            }
            array_push($productAttraction, Attraction::findOrFail($attraction));
        }

        if (count($productTempArray) >= 4) {
            $keysArray = array_rand($productTempArray, 4);
            $youMightAlsoLikeArray = [];
            foreach ($keysArray as $key) {
                array_push($youMightAlsoLikeArray, $productTempArray[$key]);
            }
        } else {
            $youMightAlsoLikeArray = $products->take(4)->get();
        }

        $translationArray = json_encode([
            "ADULT" => __("adult"),
            "YOUTH" => __("youth"),
            "CHILD" => __("child"),
            "INFANT" => __('infant'),
            "EUCITIZEN" => __('euCitizen'),
            "noAvailableSlots" => __('noAvailableSlots')

        ]);

        return view('frontend.product', [
            'items' => $items,
            'cart' => $cart,
            'product' => $product,
            'productTranslation' => $productTranslation,
            'youMightAlsoLikeArray' => $youMightAlsoLikeArray,
            'options' => $options,
            'image' => $image,
            'productImages' => $productImagesOrdered,
            'optionName' => $optionName,
            'totalPrice' => $totalPrice,
            'ids' => $ids,
            'cartCount' => $cartCount,
            'minPrices' => $minPrices,
            'prices' => $prices,
            'comments' => $comments,
            'euroValue' => $euroValue,
            'metaTag' => $metaTag,
            'specials' => $specials,
            'wishlist' => $wishlist,
            'translationArray' => $translationArray,
            'ignoredCategories' => $ignoredCategoriesArray,
            'productAttraction' => $productAttraction,
            'optionsTranslation' => $optionsTranslation
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function getAvailableTimesNew(Request $request)
    {
        $option = Option::findOrFail($request->productOption);


        if ($option->tootbus()->count()) {

            try {


                $product_body = json_decode($option->tootbus->body, true);


                //$toot_q = $this->apiRelated->prepareJsonQ();
                //$res = $toot_q->json($option->tootbus->body);
                // $product_body["availabilityType"];
                //return response()->json($res->from("options")->where("id", $option->tootbus->tootbus_option_id)->first()["units"]);
                $formattedDate = Carbon::createFromFormat("d/m/Y", $request->selectedDate)->format("Y-m-d");
                $tootbusRelated = new TootbusRelated();
                $tootbusAvailabilityResponse = $tootbusRelated->checkAvailability($option->tootbus->tootbus_product_id, $option->tootbus->tootbus_option_id, $formattedDate, 0);

                if ($tootbusAvailabilityResponse["status"] === false) {
                    return response()->json(["status" => false, "message" => $tootbusAvailabilityResponse["message"]]);
                }


                if ($tootbusAvailabilityResponse["status"] === true) {
                    $option_av_for_tootbus = $option->avs()->first();


// önce hedef günün  verilerini pasif hale getireiyoruz daha sonra apiden gelen istekle güncelleyeceğiz (start)
                    if ($product_body["availabilityType"] === "START_TIME") {
                        $res_json_decoded = json_decode($option_av_for_tootbus->hourly, true);
                        $toot_q = $this->apiRelated->prepareJsonQ();
                        $res = $toot_q->json($option_av_for_tootbus->hourly);
                        $results = $res->where("day", "=", $request->selectedDate)->get();


                        foreach ($results as $key => $r) {
                            $res_json_decoded[$key]["isActive"] = 0;
                        }
                        $option_av_for_tootbus->hourly = json_encode($res_json_decoded);


                    } else {

                        $res_json_decoded = json_decode($option_av_for_tootbus->daily, true);
                        $toot_q = $this->apiRelated->prepareJsonQ();
                        $res = $toot_q->json($option_av_for_tootbus->daily);
                        $results = $res->where("day", "=", $request->selectedDate)->get();

                        foreach ($results as $key => $r) {
                            $res_json_decoded[$key]["isActive"] = 0;
                        }
                        $option_av_for_tootbus->daily = json_encode($res_json_decoded);


                    }


// önce hedef günün  verilerini pasif hale getireiyoruz daha sonra apiden gelen istekle güncelleyeceğiz (end)

                    $tootbus_response_message = json_decode($tootbusAvailabilityResponse["message"], true);
                    if (count($tootbus_response_message) == 0) {
                        return response()->json(["status" => "0", "message" => "There is No Availability for This Day"]);
                    }

                    foreach ($tootbus_response_message as $toot) {

                        if ($product_body["availabilityType"] === "START_TIME") {  // if Has Start Time
                            //$res->reset();
                            $day = Carbon::parse($toot["id"])->format("d/m/Y");
                            $hour = Carbon::parse($toot["id"])->format("H:i");

                            $toot_q = $this->apiRelated->prepareJsonQ();
                            $res = $toot_q->json(json_encode($res_json_decoded));
                            $result = $res->where("day", "=", $day)->where("hour", "=", $hour)->get();
                            //return response()->json(key($result));
                            if (count($result) == 1) {
                                $key_q = key($result);

                                $res_json_decoded[$key_q]["ticket"] = $toot["capacity"] === null ? 9999 : $toot["capacity"];
                                $res_json_decoded[$key_q]["isActive"] = $toot["available"] === true ? 1 : 0;
                                $res_json_decoded[$key_q]["sold"] = !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0;
                                $res_json_decoded[$key_q]["hour"] = $hour;
                                $res_json_decoded[$key_q]["day"] = $day;
                                //return response()->json($res_json_decoded);

                            } else {

                                $res_json_decoded[] = [
                                    "id" => $toot["id"],
                                    "day" => $day,
                                    "hour" => $hour,
                                    "ticket" => $toot["capacity"] === null ? 9999 : $toot["capacity"],
                                    "sold" => count($result) == 1 && !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0,
                                    "isActive" => $toot["available"] === true ? 1 : 0
                                ];

                            }


                        } else { // if Has Opening Hours


                            if (count($toot["openingHours"]) == 0) {
                                $toot["openingHours"][] = ["from" => "00:00", "to" => "23:59"];
                            }


                            foreach ($toot["openingHours"] as $h) {
                                $day = Carbon::parse($toot["id"])->format("d/m/Y");


                                $toot_q = $this->apiRelated->prepareJsonQ();
                                $res = $toot_q->json(json_encode($res_json_decoded));
                                $result = $res->where("day", "=", $day)->where("hourFrom", "=", $h["from"])->where("hourTo", "=", $h["to"])->get();


                                if (count($result) == 1) {
                                    $key_q = key($result);
                                    $res_json_decoded[$key_q]["ticket"] = $toot["capacity"] === null ? 9999 : $toot["capacity"];
                                    $res_json_decoded[$key_q]["isActive"] = $toot["available"] === true ? 1 : 0;
                                    $res_json_decoded[$key_q]["sold"] = !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0;
                                    $res_json_decoded[$key_q]["hourFrom"] = $h["from"];
                                    $res_json_decoded[$key_q]["hourTo"] = $h["to"];
                                    $res_json_decoded[$key_q]["day"] = $day;

                                } else {

                                    $res_json_decoded[] = [
                                        "id" => $toot["id"],
                                        "day" => $day,
                                        "hourFrom" => $h["from"],
                                        "hourTo" => $h["to"],
                                        "ticket" => $toot["capacity"] === null ? 9999 : $toot["capacity"],
                                        "sold" => count($result) == 1 && !empty($result[key($result)]["sold"]) ? $result[key($result)]["sold"] : 0,
                                        "isActive" => $toot["available"] === true ? 1 : 0
                                    ];

                                }


                            }


                        } // end of else


                    }
                    $res->reset();

                    if ($product_body["availabilityType"] === "START_TIME") {
                        $option_av_for_tootbus->hourly = json_encode($res_json_decoded);
                    } else {
                        $option_av_for_tootbus->daily = json_encode($res_json_decoded);
                    }

                    if ($option_av_for_tootbus->save()) {

                    }


                }

            } catch (\Exception $e) {
                return response()->json(["status" => "0", "message" => $e->getMessage()]);
            }

        } // end of tootbuscount


        $commission = auth()->guard('web')->check() ? Commission::where('optionID', $request->productOption)->where('commissionerID', auth()->guard('web')->user()->id)->first() : 0;


        if ($commission) {

            if ($option->supplierID != -1) {
                $supp = \App\Supplier::findOrFail($option->supplierID);

                if ($supp->comission) {

                    if ($supp->comission < $commission->commission) {
                        $commission->commission = $supp->comission;
                    }

                }
            }

        }


        $maxPersonCount = $option->maxPerson;
        $pricing = $option->pricings()->first();
        $option_availabilities = $option->avs()->orderBy('isLimitless', 'ASC')->get();
        $mixedAv = null;
        $mixedAv['availability_names'] = [];
        $mixedAv['availability_types'] = [];
        $mixedAv['valid_weekly_datetimes'] = [];
        $mixedAv['only_selected_times'] = [];
        $ticketCount = $request->ticket;
        if (strpos($request->selectedDate, '.')) {
            $selectedDate = str_replace('.', '/', $request->selectedDate);
        } else {
            $selectedDate = $request->selectedDate;
        }
        $productID = $request->productID;

        // Getting tickets and loop through tickets for valid ticket count for selected date
        foreach ($option_availabilities as $i => $availability) {
            if ($availability->isLimitless == 1) {
                $ticketCount = 0;
            }
            // if max person count is less than ticket count don't show available hours
            if ($maxPersonCount != '' && $maxPersonCount < $ticketCount) {
                return response()->json(['mixedAv' => null]);
            }
            $validTimes = [];
            array_push($mixedAv['availability_names'], $availability->name);
            array_push($mixedAv['availability_types'], $availability->availabilityType);
            if ($availability->avTicketType == '1') {
                $hourly = json_decode($availability->hourly, true);
                $jsonq = $this->apiRelated->prepareJsonQ();
                $res = $jsonq->json($availability->hourly);
                $result = $res->where('day', '=', $selectedDate)
                    ->where('ticket', '>=', $ticketCount)
                    ->where('isActive', '=', 1)
                    ->get();
                $keys = array_keys($result);
                if (count($keys) > 0) {
                    foreach ($keys as $k) {
                        $cutOfTime = $this->timeRelatedFunctions->cutOfTimeCalculator($option, $hourly[$k]['hour'], $selectedDate, $productID);
                        if ($cutOfTime['time'] - $cutOfTime['cutOfTimeAtomic'] >= time()) {
                            array_push($validTimes, ['hourFrom' => $hourly[$k]['hour'], "id" => !empty($hourly[$k]['id']) ? $hourly[$k]['id'] : '']);
                        }
                    }
                } else {
                    return response()->json(['mixedAv' => null]);
                }
                usort($validTimes, function ($a, $b) {
                    return strtotime($a['hourFrom']) > strtotime($b['hourFrom']);
                });
                array_push($mixedAv['only_selected_times'], $validTimes);
                $validTimes = [];
                $res->reset();
            } else if ($availability->avTicketType == '2') {
                $daily = json_decode($availability->daily, true);
                $jsonq = $this->apiRelated->prepareJsonQ();
                $res = $jsonq->json($availability->daily);
                $result = $res->where('day', '=', $selectedDate)
                    ->where('ticket', '>=', $ticketCount)
                    ->where('isActive', '=', 1)
                    ->get();
                $keys = array_keys($result);
                if (count($keys) > 0) {
                    foreach ($keys as $k) {
                        $cutOfTime = $this->timeRelatedFunctions->cutOfTimeCalculator($option, $daily[$k]['hourTo'], $selectedDate, $productID);
                        if ($cutOfTime['time'] - $cutOfTime['cutOfTimeAtomic'] >= time()) {
                            array_push($validTimes, ['hourFrom' => $daily[$k]['hourFrom'], 'hourTo' => $daily[$k]['hourTo'], "id" => !empty($daily[$k]['id']) ? $daily[$k]['id'] : '']);
                        }
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
                            $cutOfTime = $this->timeRelatedFunctions->cutOfTimeCalculator($option, $daily[$k]['hourTo'], $selectedDate, $productID);
                            if ($cutOfTime['time'] - $cutOfTime['cutOfTimeAtomic'] >= time()) {
                                array_push($validTimes, ['hourFrom' => $daily[$k]['hourFrom'], 'hourTo' => $daily[$k]['hourTo'], "id" => !empty($daily[$k]['id']) ? $daily[$k]['id'] : '']);
                            }
                        }
                    }
                } else {
                    return response()->json(['mixedAv' => null]);
                }
                array_push($mixedAv['only_selected_times'], $validTimes);
                $validTimes = [];
                $res->reset();
            } else if ($availability->avTicketType == '4' && $availability->isLimitless == 0) {
                $jsonq = $this->apiRelated->prepareJsonQ();
                $res = $jsonq->json($availability->barcode);
                $result = $res->where('dayFrom', 'dateLte', $selectedDate)
                    ->where('dayTo', 'dateGte', $selectedDate)
                    ->where('ticket', '>=', $ticketCount)
                    ->get();
                if (count($result) == 1) {
                    if ($availability->availabilityType == 'Starting Time') {
                        $desiredCol = $availability->hourly;
                        $hourColKey = 'hour';
                    } else {
                        $desiredCol = $availability->daily;
                        $hourColKey = 'hourTo';
                    }

                    $desiredColDecoded = json_decode($desiredCol, true);
                    $jsonq2 = $this->apiRelated->prepareJsonQ();
                    $res2 = $jsonq2->json($desiredCol);
                    $result2 = $res2->where('day', '=', $selectedDate)
                        ->where('isActive', '=', 1)
                        ->get();
                    $keys = array_keys($result2);
                    if (count($keys) > 0) {
                        foreach ($keys as $k) {
                            $cutOfTime = $this->timeRelatedFunctions->cutOfTimeCalculator($option, $desiredColDecoded[$k][$hourColKey], $selectedDate, $productID);
                            if ($cutOfTime['time'] - $cutOfTime['cutOfTimeAtomic'] >= time()) {
                                if ($availability->availabilityType == 'Starting Time') {
                                    array_push($validTimes, ['hourFrom' => $desiredColDecoded[$k]['hour'], "id" => !empty($desiredColDecoded[$k]['id']) ? $desiredColDecoded[$k]['id'] : '']);
                                } else {
                                    array_push($validTimes, ['hourFrom' => $desiredColDecoded[$k]['hourFrom'], 'hourTo' => $desiredColDecoded[$k]['hourTo'], "id" => !empty($desiredColDecoded[$k]['id']) ? $desiredColDecoded[$k]['id'] : '']);
                                }
                            }
                        }
                    }

                    array_push($mixedAv['only_selected_times'], $validTimes);
                    $validTimes = [];
                    $res->reset();
                } else {
                    return response()->json(['mixedAv' => null]);
                }
            }
        }

        $specialOffers = SpecialOffers::where('productID', $request->productID)->where('optionID', $request->productOption)->first();

        if (auth()->check()) {
            if (auth()->guard("web")->user()->commission != 0 || !is_null(auth()->guard("web")->user()->commission)) {
                $specialOffers = null;
            }
        }

        $specials = [];
        if ($specialOffers && (!is_null($specialOffers->dateRange) || !is_null($specialOffers->weekDay) || !is_null($specialOffers->randomDay) || !is_null($specialOffers->dateTimes))) {
            $jsonq = $this->apiRelated->prepareJsonQ();
            $dateTimesDecoded = json_decode($specialOffers->dateTimes, true);
            $randomDayDecoded = json_decode($specialOffers->randomDay, true);
            $weekDayDecoded = json_decode($specialOffers->weekDay, true);
            $dateRangeDecoded = json_decode($specialOffers->dateRange, true);
            $onlySelectedTimes = $mixedAv['only_selected_times'];
            if (!is_null($specialOffers->dateTimes) && count($dateTimesDecoded) > 0 && $option->isMixed == 0) {
                foreach ($onlySelectedTimes as $ost) {
                    foreach ($ost as $i => $o) {
                        $res = $jsonq->json($specialOffers->dateTimes);
                        $result = $res->where('day', '=', $request->selectedDate)->where('hour', '=', $o['hourFrom'])->get();
                        if (count($result) > 0) {
                            $key = key($result);
                            array_push($specials, $dateTimesDecoded[$key]);
                        }
                        $res->reset();
                    }
                }
            }
            if (!is_null($specialOffers->randomDay) && count($randomDayDecoded) > 0) {
                $res = $jsonq->json($specialOffers->randomDay);
                $result = $res->where('day', $request->selectedDate)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    array_push($specials, $randomDayDecoded[$key]);
                }
                $res->reset();
            }
            if (!is_null($specialOffers->weekDay) && count($weekDayDecoded) > 0) {
                $datetime = DateTime::createFromFormat('d/m/Y', $request->selectedDate);
                $dayName = strtolower($datetime->format('l'));
                $res = $jsonq->json($specialOffers->weekDay);
                $result = $res->where('dayName', $dayName)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    array_push($specials, $weekDayDecoded[$key]);
                }
                $res->reset();
            }
            if (!is_null($specialOffers->dateRange) && count($dateRangeDecoded) > 0) {
                $res = $jsonq->json($specialOffers->dateRange);
                $result = $res->where('from', 'dateLte', $request->selectedDate)
                    ->where('to', 'dateGte', $request->selectedDate)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    array_push($specials, $dateRangeDecoded[$key]);
                }
                $res->reset();
            }
        }

        $translationArray = [
            'chooseTime' => __('chooseTime'),
            'totalPrice' => __('totalPrice'),
            'adult' => __('adult'),
            'youth' => __('youth'),
            'child' => __('child'),
            'infant' => __('infant'),
            'person' => __('person')
        ];

        return response()->json(['status' => "1", 'error' => __('getAvailableTimesError'), 'mixedAv' => $mixedAv, 'pricing' => $pricing, 'specials' => $specials, 'commission' => $commission, 'translationArray' => $translationArray]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function getAvailableDatesNew(Request $request)
    {
        $option = Option::findOrFail($request->productOption);

        $specialOffer = SpecialOffers::where('optionID', $option->id)->where('productID', $request->productID)->first();

        if ($option->bigBus()->count()) {

        }
        if (auth()->check()) {
            if (auth()->guard("web")->user()->commission != 0 || !is_null(auth()->guard("web")->user()->commission)) {
                $specialOffer = null;
            }
        }

        // Calculating Cut of Time
        $cutOfTime = $option->cutOfTime;
        $cutOfTimeDate = $option->cutOfTimeDate;
        $cotDateString = 'days';
        if ($cutOfTimeDate == 'h') {
            $cotDateString = 'hours';
        } elseif ($cutOfTimeDate == 'm') {
            $cotDateString = 'minutes';
        }
        $cotString = $cutOfTime . ' ' . $cotDateString;
        $cotDateTime = new DateTime('now + ' . $cotString);
        $cotDateTime->setTimezone(new \DateTimeZone($option->products()->first()->countryName->timezone));
        $cotDay = $cotDateTime->format('d/m/Y');
        $cotHour = $cotDateTime->format('H:i');
        //////

        $maxPersonCount = $option->maxPerson;
        $minPersonCount = $option->minPerson;
        $pricing = $option->pricings()->first();
        $option_availabilities = $option->avs()->orderBy('isLimitless', 'ASC')->get();
        $avdatesMin = [];
        $avdatesMax = [];
        $disabledDates = [];
        $ticketCount = $request->ticket;
        $invalidDates = [];

        // if max person count is less than ticket count don't show available dates
        if ($maxPersonCount != '' && $maxPersonCount < $request->ticket) {
            return response()->json(['min' => [], 'max' => [], 'errorType' => 'Max. Person Count']);
        }

        foreach ($option_availabilities as $i => $availability) {
            if ($availability->hourly == '[]' && $availability->daily == '[]' && $availability->dateRange == '[]' && $availability->barcode == '[]') {
                return (['error' => 'There is no availability for this option !']);
            }
            $avdate = $availability->avdates()->get();
            array_push($avdatesMin, $avdate->min('valid_from'));
            array_push($avdatesMax, $avdate->max('valid_to'));
            array_push($disabledDates, json_decode($availability->disabledDays, true));
            $disabledDates = $this->commonFunctions->flatten($disabledDates);

            // disabled = disabled + disabledYears
            $disabledYears = json_decode($availability->disabledYears, true);
            if (!is_null($disabledYears)) {
                foreach ($disabledYears as $dy) {
                    $begin = DateTime::createFromFormat('Y-m-d', $dy . '-01-01');
                    $begin = $begin->format('d/m/Y');
                    $begin = DateTime::createFromFormat('d/m/Y', $begin);
                    $end = DateTime::createFromFormat('Y-m-d', $dy . '-12-31');
                    $end = $end->format('d/m/Y');
                    $end = DateTime::createFromFormat('d/m/Y', $end);
                    $interval = new DateInterval('P1D');
                    $end->add($interval);
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $dt) {
                        $formattedDt = $dt->format('d/m/Y');
                        array_push($disabledDates, $formattedDt);
                    }
                }
            }
            //////////

            // disabled = disabled + disabledMonths
            $disabledMonths = json_decode($availability->disabledMonths, true);
            $minMonth = DateTime::createFromFormat('Y-m-d', $avdate->min('valid_from'));
            $minYear = $minMonth->format('Y');
            $maxMonth = DateTime::createFromFormat('Y-m-d', $avdate->max('valid_to'));
            $maxYear = $maxMonth->format('Y');
            if (!is_null($disabledMonths)) {
                $yearRange = range($minYear, $maxYear);
                foreach ($yearRange as $rangedYear) {
                    foreach ($disabledMonths as $dM) {
                        $begin = DateTime::createFromFormat('Y-m-d', $rangedYear . '-' . $dM . '-01');
                        $begin = $begin->format('d/m/Y');
                        $begin = DateTime::createFromFormat('d/m/Y', $begin);
                        $end = DateTime::createFromFormat('Y-m-d', $rangedYear . '-' . $dM . '-01')->modify('last day of this month');
                        $end = $end->format('d/m/Y');
                        $end = DateTime::createFromFormat('d/m/Y', $end);
                        $interval = new DateInterval('P1D');
                        $end->add($interval);
                        $period = new DatePeriod($begin, $interval, $end);
                        foreach ($period as $dt) {
                            $formattedDt = $dt->format('d/m/Y');
                            array_push($disabledDates, $formattedDt);
                        }
                    }
                }
            }
            /////////

            // disabled = disabled + weekDays
            $disabledWeekDays = json_decode($availability->disabledWeekDays, true);
            $startDate = new DateTime($avdate->min('valid_from'));
            $endDate = new DateTime($avdate->max('valid_to'));
            if (!is_null($disabledWeekDays)) {
                foreach ($disabledWeekDays as $dwd) {
                    while ($startDate <= $endDate) {
                        if (strtolower($startDate->format('l')) == $dwd) {
                            array_push($disabledDates, $startDate->format('d/m/Y'));
                        }
                        $startDate->modify('+1 day');
                    }
                    $startDate = new DateTime($avdate->min('valid_from'));
                }
            }
            ///////

            // Ticket Count and Cut of Time Validation
            if ($availability->isLimitless == 1) {
                $ticketCount = 0;
            }

            $validDates = [];
            if ($availability->avTicketType == '1') {
                $hourly = json_decode($availability->hourly, true);
                if (count($hourly) == 0) {
                    return response()->json(['min' => [], 'max' => []]);
                } else {
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $res = $jsonq->json($availability->hourly);
                    $result = $res->where('ticket', '>=', $ticketCount)
                        ->where('day', 'dateGte', $cotDay)
                        ->where('isActive', '=', 1)
                        ->get();
                    $keys = array_keys($result);
                    foreach ($keys as $k) {
                        if ($hourly[$k]['day'] != $cotDay) {
                            array_push($validDates, $hourly[$k]['day']);
                        } else {
                            if ($hourly[$k]['hour'] >= $cotHour) {
                                array_push($validDates, $hourly[$k]['day']);
                            }
                        }
                    }
                    $validDates = array_values(array_unique($validDates));
                    // Loop through min and max dates and if there is not ticket >= ticket from db, append that date to disabled dates
                    $begin = DateTime::createFromFormat('Y-m-d', $avdate->min('valid_from'));
                    $begin = $begin->format('d/m/Y');
                    $begin = DateTime::createFromFormat('d/m/Y', $begin);
                    $end = DateTime::createFromFormat('Y-m-d', $avdate->max('valid_to'));
                    $end = $end->format('d/m/Y');
                    $end = DateTime::createFromFormat('d/m/Y', $end);
                    $interval = new DateInterval('P1D');
                    $end->add($interval);
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $dt) {
                        $formattedDt = $dt->format('d/m/Y');
                        if (!in_array($formattedDt, $validDates)) {
                            array_push($invalidDates, $formattedDt);
                        }
                    }
                    $res->reset();
                    //////
                }
            } else if ($availability->avTicketType == '2') {
                $daily = json_decode($availability->daily, true);
                if (count($daily) == 0) {
                    return response()->json(['min' => [], 'max' => []]);
                } else {
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $res = $jsonq->json($availability->daily);
                    $result = $res->where('ticket', '>=', $ticketCount)
                        ->where('day', 'dateGte', $cotDay)
                        ->where('isActive', '=', 1)
                        ->get();
                    $keys = array_keys($result);
                    foreach ($keys as $k) {
                        if ($daily[$k]['day'] != $cotDay) {
                            array_push($validDates, $daily[$k]['day']);
                        } else {
                            if ($daily[$k]['hourTo'] >= $cotHour) {
                                array_push($validDates, $daily[$k]['day']);
                            }
                        }
                    }
                    $validDates = array_values(array_unique($validDates));
                    // Loop through min and max dates and if there is not ticket >= ticket from db, append that date to disabled dates
                    $begin = DateTime::createFromFormat('Y-m-d', $avdate->min('valid_from'));
                    $begin = $begin->format('d/m/Y');
                    $begin = DateTime::createFromFormat('d/m/Y', $begin);
                    $end = DateTime::createFromFormat('Y-m-d', $avdate->max('valid_to'));
                    $end = $end->format('d/m/Y');
                    $end = DateTime::createFromFormat('d/m/Y', $end);
                    $interval = new DateInterval('P1D');
                    $end->add($interval);
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $dt) {
                        $formattedDt = $dt->format('d/m/Y');
                        if (!in_array($formattedDt, $validDates)) {
                            array_push($invalidDates, $formattedDt);
                        }
                    }
                    $res->reset();
                }
            } else if ($availability->avTicketType == '3') {
                $dateRange = json_decode($availability->dateRange, true);
                if (count($dateRange) == 0) {
                    return response()->json(['min' => [], 'max' => []]);
                } else {
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $res = $jsonq->json($availability->dateRange);
                    $result = $res->where('ticket', '>=', $ticketCount)->get();
                    $keys = array_keys($result);
                    foreach ($keys as $k) {
                        $dayFrom = DateTime::createFromFormat('d/m/Y', $dateRange[$k]['dayFrom']);
                        if ($dayFrom < $cotDateTime) {
                            $dayFrom = DateTime::createFromFormat('d/m/Y', $cotDay);
                        }
                        $dayTo = DateTime::createFromFormat('d/m/Y', $dateRange[$k]['dayTo']);
                        $interval = new DateInterval('P1D');
                        $dayTo->add($interval);
                        $period = new DatePeriod($dayFrom, $interval, $dayTo);
                        foreach ($period as $dt) {
                            if ($dt->format('d/m/Y') == $cotDay) {
                                foreach ($avdate as $avd) {
                                    $isThisAvdate = Avdate::where('id', $avd->id)->where('valid_from', 'dateLte', $dt->format('Y-m-d'))
                                        ->where('valid_to', 'dateGte', $dt->format('Y-m-d'))->first();
                                    if ($isThisAvdate) {
                                        $jsonq2 = $this->apiRelated->prepareJsonQ();
                                        $res2 = $jsonq2->json($availability->daily);
                                        $result2 = $res2->where('hourFrom', 'timeGte', $cotHour)->get();
                                        if (count($result2) == 1) {
                                            array_push($validDates, $dt->format('d/m/Y'));
                                        }
                                        $res2->reset();
                                    }
                                }
                            } else {
                                array_push($validDates, $dt->format('d/m/Y'));
                            }
                        }
                    }
                    $validDates = array_values(array_unique($validDates));
                    $begin = DateTime::createFromFormat('Y-m-d', $avdate->min('valid_from'));
                    $begin = $begin->format('d/m/Y');
                    $begin = DateTime::createFromFormat('d/m/Y', $begin);
                    $end = DateTime::createFromFormat('Y-m-d', $avdate->max('valid_to'));
                    $end = $end->format('d/m/Y');
                    $end = DateTime::createFromFormat('d/m/Y', $end);
                    $interval = new DateInterval('P1D');
                    $end->add($interval);
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $dt) {
                        $formattedDt = $dt->format('d/m/Y');
                        if (!in_array($formattedDt, $validDates)) {
                            array_push($invalidDates, $formattedDt);
                        }
                    }
                    $res->reset();
                }
            } else if ($availability->avTicketType == '4' && $availability->isLimitless != 1) {
                $barcode = json_decode($availability->barcode, true);
                if (count($barcode) == 0) {
                    return response()->json(['min' => [], 'max' => []]);
                } else {
                    $jsonq = $this->apiRelated->prepareJsonQ();
                    $res = $jsonq->json($availability->barcode);
                    $result = $res->where('ticket', '>=', $ticketCount)->get();
                    $keys = array_keys($result);
                    foreach ($keys as $k) {
                        $dayFrom = DateTime::createFromFormat('d/m/Y', $barcode[$k]['dayFrom']);
                        if ($dayFrom < $cotDateTime) {
                            $dayFrom = DateTime::createFromFormat('d/m/Y', $cotDay);
                        }
                        $dayTo = DateTime::createFromFormat('d/m/Y', $barcode[$k]['dayTo']);
                        $interval = new DateInterval('P1D');
                        $dayTo->add($interval);
                        $period = new DatePeriod($dayFrom, $interval, $dayTo);
                        foreach ($period as $dt) {
                            foreach ($avdate as $avd) {
                                $isThisAvdate = Avdate::where('id', $avd->id)->where('valid_from', '<=', $dt->format('Y-m-d'))
                                    ->where('valid_to', '>=', $dt->format('Y-m-d'))->first();
                                if ($isThisAvdate) {
                                    if ($availability->availabilityType == 'Starting Time') {
                                        $requestedCol = $availability->hourly;
                                        $queryStr = 'hour';
                                    } else {
                                        $requestedCol = $availability->daily;
                                        $queryStr = 'hourTo';
                                    }
                                    $jsonq2 = $this->apiRelated->prepareJsonQ();
                                    $res2 = $jsonq2->json($requestedCol);
                                    if ($dt->format('d/m/Y') == $cotDay)
                                        $result2 = $res2->where($queryStr, 'timeGte', $cotHour)->where('isActive', 1)->where('day', $dt->format('d/m/Y'))->get();
                                    else {
                                        $result2 = $res2->where('isActive', 1)->where('day', $dt->format('d/m/Y'))->get();
                                    }
                                    if (count($result2) > 0) {
                                        array_push($validDates, $dt->format('d/m/Y'));
                                    }
                                }
                            }
                        }
                    }
                    $validDates = array_values(array_unique($validDates));
                    $begin = DateTime::createFromFormat('Y-m-d', $avdate->min('valid_from'));
                    $begin = $begin->format('d/m/Y');
                    $begin = DateTime::createFromFormat('d/m/Y', $begin);
                    $end = DateTime::createFromFormat('Y-m-d', $avdate->max('valid_to'));
                    $end = $end->format('d/m/Y');
                    $end = DateTime::createFromFormat('d/m/Y', $end);
                    $interval = new DateInterval('P1D');
                    $end->add($interval);
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $dt) {
                        $formattedDt = $dt->format('d/m/Y');
                        if (!in_array($formattedDt, $validDates)) {
                            array_push($invalidDates, $formattedDt);
                        }
                    }
                    $res->reset();
                }
            } else if (is_null($availability->avTicketType)) {
                return response()->json(['min' => [], 'max' => []]);
            }
            //////
        }

        $typeToPrice = [];
        $dayToPrice = [];
        $daysSpecOffs = [];
        $dayToTimestamp = [];
        $specials = SpecialOffers::where('productID', '=', $request->productID)->where('optionID', '=', $request->productOption)->first();


        if (auth()->check()) {
            if (auth()->guard("web")->user()->commission != 0 || !is_null(auth()->guard("web")->user()->commission)) {
                $specials = null;
            }
        }


        if ($specials) {
            $dtSpecial = json_decode($specials->dateTimes, true);
            if (!is_null($dtSpecial)) {
                foreach ($dtSpecial as $dt) {
                    if (Carbon::createFromFormat('d/m/Y', $dt['day'])->timestamp >= Carbon::today()->timestamp) {
                        if ($dt['isActive'] == 1) {
                            array_push($daysSpecOffs, $dt['day']);
                            $dayToPrice[$dt['day']] = $dt["discountType"] == "money" ? Currency::calculateCurrencyForVisitor($dt['discount']) : $dt['discount'];
                            $typeToPrice[$dt['day']] = $dt['discountType'];
                        }
                    }
                }
            }
            $rdSpecial = json_decode($specials->randomDay, true);
            if (!is_null($rdSpecial)) {
                foreach ($rdSpecial as $rd) {
                    if (Carbon::createFromFormat('d/m/Y', $rd['day'])->timestamp >= Carbon::today()->timestamp) {
                        if ($rd['isActive'] == 1) {
                            array_push($daysSpecOffs, $rd['day']);
                            $dayToPrice[$rd['day']] = $rd["discountType"] == "money" ? Currency::calculateCurrencyForVisitor($rd['discount']) : $rd['discount'];
                            $typeToPrice[$rd['day']] = $rd['discountType'];
                        }
                    }
                }
            }
            $wdSpecial = json_decode($specials->weekDay, true);
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
                foreach ($wdSpecial as $wd) {
                    for ($i = strtotime(ucfirst($wd['dayName']), $minDate); $i <= $maxDate; $i = strtotime('+1 week', $i)) {
                        if ($i >= Carbon::today()->timestamp) {
                            if ($wd['isActive'] == 1) {
                                array_push($daysSpecOffs, date('d/m/Y', $i));
                                $dayToPrice[date('d/m/Y', $i)] = $wd["discountType"] == "money" ? Currency::calculateCurrencyForVisitor($wd['discount']) : $wd['discount'];
                                $typeToPrice[date('d/m/Y', $i)] = $wd["discountType"];
                            }
                        }
                    }
                }
            }
            $drSpecial = json_decode($specials->dateRange, true);
            if (!is_null($drSpecial)) {
                foreach ($drSpecial as $dr) {
                    $from = $dr['from'];
                    $to = $dr['to'];
                    $datePeriod = $this->timeRelatedFunctions->returnDates($from, $to);
                    foreach ($datePeriod as $date) {
                        if ($date->getTimestamp() >= Carbon::today()->timestamp) {
                            if ($dr['isActive'] == 1) {
                                array_push($daysSpecOffs, $date->format('d/m/Y'));
                                $dayToPrice[$date->format('d/m/Y')] = $dr["discountType"] == "money" ? Currency::calculateCurrencyForVisitor($dr['discount']) : $dr['discount'];
                                $typeToPrice[$date->format('d/m/Y')] = $dr['discountType'];
                            }
                        }
                    }
                }
            }
        }

        $daysSpecOffs = array_values(array_unique($daysSpecOffs));

        $dayToTimestamp = array_map(function ($item) {
            $items = array_reverse(explode('/', $item));
            $item = join('/', $items);

            return strtotime($item);
        }, $daysSpecOffs);
        sort($dayToTimestamp);

        $daysSpecOffs = array_map(function ($item) {
            return date('d/m/Y', $item);
        }, $dayToTimestamp);


        array_push($disabledDates, $invalidDates);
        $disabledDates = $this->commonFunctions->flatten($disabledDates);
        $disabledDates = array_values(array_unique($disabledDates));
        $avdates['min'] = max($avdatesMin);
        $avdates['max'] = min($avdatesMax);
        $avdates['disabledDates'] = $disabledDates;

        // Get desired currency value
        $currency = Currency::findOrFail($request->visitorCurrencyCode);
        $currencyValue = $currency->value;


        $data = [
            'success',
            'avdates' => $avdates,
            'pricing' => $pricing,
            'desiredValue' => $currencyValue,
            'maxPersonCount' => $maxPersonCount,
            'specialOffer' => $specialOffer,
            'specialDays' => json_encode($daysSpecOffs),
            'dayToPrice' => json_encode($dayToPrice),
            'typeToPrice' => json_encode($typeToPrice)
        ];

        if ($minPersonCount != '' && $minPersonCount > $request->ticket) {
            $data['errorType'] = 'Min. Person Count';
        }

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function getPricingAndSpecialOffer(Request $request)
    {
        $option = Option::findOrFail($request->productOption);
        $pricing = $option->pricings()->first();

        $specialOffers = SpecialOffers::where('productID', $request->productID)->where('optionID', $request->productOption)->first();

        if (auth()->check()) {
            if (auth()->guard("web")->user()->commission != 0 || !is_null(auth()->guard("web")->user()->commission)) {
                $specialOffers = null;
            }
        }

        $specials = [];
        if ($specialOffers && (!is_null($specialOffers->dateRange) || !is_null($specialOffers->weekDay) || !is_null($specialOffers->randomDay) || !is_null($specialOffers->dateTimes))) {
            $jsonq = $this->apiRelated->prepareJsonQ();
            $dateTimesDecoded = json_decode($specialOffers->dateTimes, true);
            $randomDayDecoded = json_decode($specialOffers->randomDay, true);
            $weekDayDecoded = json_decode($specialOffers->weekDay, true);
            $dateRangeDecoded = json_decode($specialOffers->dateRange, true);
            if (!is_null($specialOffers->dateTimes) && count($dateTimesDecoded) > 0 && $option->isMixed == 0) {
                $res = $jsonq->json($specialOffers->dateTimes);
                $result = $res->where('day', '=', $request->selectedDate)->where('hour', '=', $request->hour)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    array_push($specials, $dateTimesDecoded[$key]);
                }
                $res->reset();
            } else if (!is_null($specialOffers->randomDay) && count($randomDayDecoded) > 0) {
                $res = $jsonq->json($specialOffers->randomDay);
                $result = $res->where('day', $request->selectedDate)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    array_push($specials, $randomDayDecoded[$key]);
                }
                $res->reset();
            } else if (!is_null($specialOffers->weekDay) && count($weekDayDecoded) > 0) {
                $datetime = DateTime::createFromFormat('d/m/Y', $request->selectedDate);
                $dayName = strtolower($datetime->format('l'));
                $res = $jsonq->json($specialOffers->weekDay);
                $result = $res->where('dayName', $dayName)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    array_push($specials, $weekDayDecoded[$key]);
                }
                $res->reset();
            } else if (!is_null($specialOffers->dateRange) && count($dateRangeDecoded) > 0) {
                $res = $jsonq->json($specialOffers->dateRange);
                $result = $res->where('from', 'dateLte', $request->selectedDate)
                    ->where('to', 'dateGte', $request->selectedDate)->get();
                if (count($result) > 0) {
                    $key = key($result);
                    array_push($specials, $dateRangeDecoded[$key]);
                }
                $res->reset();
            }
        }


        $translationArray = [
            'chooseTime' => __('chooseTime'),
            'totalPrice' => __('totalPrice'),
            'adult' => __('adult'),
            'youth' => __('youth'),
            'child' => __('child'),
            'infant' => __('infant'),
            'person' => __('person')
        ];

        return response()->json(['pricing' => $pricing, 'specials' => $specials, 'translationArray' => $translationArray]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function specialOffers()
    {

        $productOrder = Page::find(3)->productOrder;
        if (!empty($productOrder)) {
            $productOrder = json_decode($productOrder, true);
        } else {
            $productOrder = [1];
        }
        $idss = implode(',', array_reverse($productOrder));

        $specialOffers = SpecialOffers::where('weekDay', 'like', '%"isActive":1%')->orWhere('dateRange', 'like', '%"isActive":1%')->get();
        $soProducts = SpecialOffers::where(function ($query) {
            $query->where('weekDay', 'like', '%"isActive":1%')->orWhere('dateRange', 'like', '%"isActive":1%');
        })->get();

        $pids = [];


        if (auth()->check()) {
            if (auth()->guard("web")->user()->commission != 0 || !is_null(auth()->guard("web")->user()->commission)) {
                $specialOffers = null;
                $soProducts = [];
            }
        }


        // Döngüde dateRange içerisindeki diziyi from ve to indexlerindeki tarihleri alıp
        // from bugüne eşit veya küçük ise
        // to bugüne eşit veya büyük ise
        // $pids dizisine ürün idsini ekle
        // değil ise döngüden çık
        foreach ($soProducts as $sop) {
            $dateRange = json_decode($sop->dateRange, true);

            foreach ($dateRange as $item) {
                $from = Carbon::parse(str_replace('/', '-', $item['from']));
                $to = Carbon::parse(str_replace('/', '-', $item['to']));
                //if ($from <= Carbon::now() && $to >= Carbon::now()) {   // Special offerlar için  kontrol şartını değiştiriyoruz
                if ($to >= Carbon::now()) {
                    array_push($pids, $sop['productID']);
                } else {
                    break;
                }
            }


        }


        $products = Product::where('isPublished', '1')->where('isDraft', '0')->whereIn('id', $pids)->orderByRaw(DB::raw("FIELD(id, $idss) desc"))->get();

        return view('frontend.special-offers', ['products' => $products, 'specialOffers' => $specialOffers]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact()
    {
        return view('frontend.contact');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function faq()
    {
        $faqs = FAQ::with('translate')->get();

        return view('frontend.faq', ['faqs' => $faqs]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function aboutUs()
    {
        return view('frontend.about-us');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function terms()
    {
        return view('frontend.terms-and-conditions');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function checkAvailability(Request $request)
    {




        $perPage = 15;
        $columns = ['*'];
        $pageName = 'page';
        $pagiPage = $request->page;
        $totalProduct = 0;

        $langCode = session()->get('userLanguage') ? session()->get('userLanguage') : 'en';
        $searchType = empty($request->searchType) ? "misc" : $request->searchType;
        $searchValue = $request->q;
        if ($searchValue != '' && $searchType == '') {
            return response()->json(['successful' => __('productsFetchedSuccessfully'), 'products' => []]);
        }
        $from = $request->from;
        $to = $request->to;
        $categories = $request->categories;
        $categoriesArr = explode(' | ', $categories);
        $attractions = $request->attractions;
        $attractionsArr = explode(' | ', $attractions);

        if ($searchType == 'attraction') {
            if ($langCode == 'en') {
                $searchAttraction = Attraction::where('name', 'like', '%' . $searchValue . '%')->first();
                array_push($attractionsArr, $searchAttraction->id);
                $attractions = $searchAttraction->id;
            } else {
                $searchAttraction = AttractionTranslation::where('name', 'like', '%' . $searchValue . '%')->first();
                array_push($attractionsArr, $searchAttraction->id);
                $attractions = $searchAttraction->id;
            }
        }

        if ($searchType == 'country') {
            if ($langCode == 'en') {
                $searchCountry = Country::where('countries_name', 'like', '%' . $searchValue . '%')->first();
            } else {
                $countryTranslation = CountryTranslation::where('countries_name', 'like', '%' . $searchValue . '%')->first();
                if ($countryTranslation) {
                    $searchCountry = Country::findOrFail($countryTranslation->countryID);
                } else {
                    $searchCountry = Country::where('countries_name', 'like', '%' . $searchValue . '%')->first();
                }
            }
        }

        if ($searchType == 'city') {
            if ($langCode == 'en') {
                $searchCity = City::where('name', 'like', '%' . $searchValue . '%')->first();
            } else {
                $cityTranslation = CityTranslation::where('name', 'like', '%' . $searchValue . '%')->first();
                if ($cityTranslation) {
                    $searchCity = City::findOrFail($cityTranslation->cityID);
                } else {
                    $searchCity = City::where('name', 'like', '%' . $searchValue . '%')->first();
                }
            }
        }

        $prices = $request->prices;
        $sortType = $request->sortType;

        if ($from == '' && $to == '') {
            $page = Page::findOrFail(2);
            $products = [];
            $productOrder = json_decode($page->productOrder, true);
            if ($productOrder == null) {
                $productOrder = [];
            }
            $unsortedProducts = Product::whereNotIn('id', $productOrder)->where('isPublished', '1')->where('isDraft', '0')->where('isSpecial', '!=', 1)->paginate($perPage, $columns, $pageName, $pagiPage);
            $paginatorView = view('frontend.product_paginator', ['paginator' => $unsortedProducts])->render();
            foreach ($productOrder as $productID) {
                array_push($products, Product::findOrFail($productID));
            }
            foreach ($unsortedProducts as $product) {
                array_push($products, $product);
            }

            if ($categories != '') {

                $products = Product::where('isPublished', '1')->where('isDraft', '0')->where('isSpecial', '!=', 1);
                $products = $products->where(function ($query) use ($categoriesArr) {
                    foreach ($categoriesArr as $cat) {
                        $query->orWhere('category', $cat);
                    }
                })->paginate($perPage, $columns, $pageName, $pagiPage);
                $totalProduct = $products->total();

                $paginatorView = view('frontend.product_paginator', ['paginator' => $products])->render();

            }
            if ($attractions != '') {
                $products = [];
                foreach ($attractionsArr as $attr) {
                    switch ($attr) {
                        case 1: // Eiffel Tower
                            $pageID = 4;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        case 2: // Seine Cruise
                            $pageID = 5;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        case 3: // Big Bus
                            $pageID = 7;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        case 4: // Cabaret Show
                            $pageID = 8;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        case 5: // Louvre Museum
                            $pageID = 6;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        case 6: // Versailles Palace
                            $pageID = 9;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        case 13: // Disneyland
                            $pageID = 10;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        case 24: // Museums / Exhibitions
                            $pageID = 11;
                            $page = Page::findOrFail($pageID);
                            $productOrder = json_decode($page->productOrder, true);
                            break;
                        default: // Others
                            $productOrder = [];
                            break;
                    }
                    if ($productOrder == null) {
                        $productOrder = [];
                    }
                    foreach ($productOrder as $productID) {
                        array_push($products, Product::findOrFail($productID));
                    }
                    $unsortedProducts = Product::whereNotIn('id', $productOrder)->where('attractions', 'like', '%"' . $attr . '"%')
                        ->where('isPublished', '1')->where('isDraft', '0')->where('isSpecial', '!=', 1)->paginate($perPage, $columns, $pageName, $pagiPage);
                    $totalProduct = $unsortedProducts->total();
                    $paginatorView = view('frontend.product_paginator', ['paginator' => $unsortedProducts])->render();
                    foreach ($unsortedProducts as $product) {
                        array_push($products, $product);
                    }
                }
            }
            if ($searchType == 'country') {
                $products = Product::where('isPublished', '1')->where('isDraft', '0')->where('isSpecial', '!=', 1);
                $products = $products->where('country', $searchCountry->id)->paginate($perPage, $columns, $pageName, $pagiPage);
                $totalProduct = $products->total();
                $paginatorView = view('frontend.product_paginator', ['paginator' => $products])->render();
            }
            if ($searchType == 'city') {
                $products = Product::where('isPublished', '1')->where('isDraft', '0')->where('isSpecial', '!=', 1);
                $products = $products->where('city', $searchCity->name)->paginate($perPage, $columns, $pageName, $pagiPage);
                $totalProduct = $products->total();
                $paginatorView = view('frontend.product_paginator', ['paginator' => $products])->render();
            }
            if ($searchType == 'misc') {
                $products = Product::where('isPublished', '1')->where('isDraft', '0')->where('isSpecial', '!=', 1);
                $products = $products->where(function ($query) use ($searchValue) {
                    $query->where('title', 'like', '%' . $searchValue . '%')
                        ->orWhere('shortDesc', 'like', '%' . $searchValue . '%')
                        ->orWhereHas("options", function ($q) use ($searchValue) {
                            $q->where("title", "like", '%' . $searchValue . '%');
                        });
                    //->orWhere('fullDesc', 'like', '%'.$searchValue.'%')
                    //->orWhere('highlights', 'like', '%'.$searchValue.'%')
                    //->orWhere('included', 'like', '%'.$searchValue.'%')
                    //->orWhere('notIncluded', 'like', '%'.$searchValue.'%')
                    //->orWhere('knowBeforeYouGo', 'like', '%'.$searchValue.'%')
                    //->orWhere('cancelPolicy', 'like', '%'.$searchValue.'%');
                })->paginate($perPage, $columns, $pageName, $pagiPage);
                $totalProduct = $products->total();
                $paginatorView = view('frontend.product_paginator', ['paginator' => $products])->render();
            }
            $products = $this->prepareProducts($products, $prices, $sortType);
            $allAttraction = Attraction::all();

            $commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
            $attractionTranslationModel = new \App\AttractionTranslation();

            $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
            $language = \App\Language::where('code', $langCode)->first();
            $langID = $language->id;
            $langCodeForUrl = $langCode == 'en' ? '' : $langCode;
            foreach ($allAttraction as $attraction) {
                $attraction["onFailUrl"] = url($langCodeForUrl . '/' . $commonFunctions->getRouteLocalization('attraction') . '/' . $commonFunctions->getAttractionLocalization($attraction, $language)) . '-' . $attraction->id;

                $attractionTranslation = $attractionTranslationModel->where('languageID', $langID)->where('attractionID', $attraction->id)->first();
                if ($attractionTranslation)
                    $attraction["onFailName"] = $attractionTranslation->name;
                else
                    $attraction["onFailName"] = $attraction->name;
            }

            if ($request->prices) {
                $totalProduct = count($products);
            }


            if (count($products) <= 0) {
                return response()->json(['failed' => __('noProductsAccordingToFilters'), 'attractions' => $allAttraction, 'paginator' => $paginatorView]);
            } else {
                return response()->json(['successful' => __('productsFetchedSuccessfully'), 'products' => $products, 'paginator' => $paginatorView, 'totalProduct' => $totalProduct]);
            }

        } else if ($from != '' && $to != '') {
            // From - To is certain, category and attractions is optional selected
            $availabilities = [];
            $options = [];
            $products = [];

            $avdates = Avdate::where('valid_from', '<=', $from)->where('valid_to', '>=', $to)->get();

            foreach ($avdates as $avdate) {
                $availability = $avdate->av()->first();
                if ($availability) {
                    array_push($availabilities, $availability);
                }
            }

            foreach ($availabilities as $availability) {
                $optionsOfAv = $availability->options()->get();
                foreach ($optionsOfAv as $optionOfAv) {
                    array_push($options, $optionOfAv);
                }
            }

            foreach ($options as $option) {
                $productsOfOpt = $option->products();
                if ($categories != '') {
                    $productsOfOpt = $productsOfOpt->where(function ($query) use ($categoriesArr) {
                        foreach ($categoriesArr as $cat) {
                            $query->orWhere('category', $cat);
                        }
                    });
                }
                if ($attractions != '') {
                    $productsOfOpt = $productsOfOpt->where(function ($query) use ($attractionsArr) {
                        foreach ($attractionsArr as $att) {
                            $query->orWhere('attractions', 'like', '%"' . $att . '"%');
                        }
                    });
                }
                if ($searchType == 'country') {
                    $productsOfOpt = $productsOfOpt->where('country', $searchCountry->id);
                }
                if ($searchType == 'city') {
                    $productsOfOpt = $productsOfOpt->where('city', 'like', '%' . $searchValue . '%');
                }
                if ($searchType == 'misc') {
                    $productsOfOpt = $productsOfOpt->where(function ($query) use ($searchValue) {
                        $query->where('title', 'like', '%' . $searchValue . '%')
                            ->orWhere('shortDesc', 'like', '%' . $searchValue . '%')
                            ->orWhereHas("options", function ($q) use ($searchValue) {
                                $q->where("title", "like", '%' . $searchValue . '%');
                            });

                        //->orWhere('fullDesc', 'like', '%'.$searchValue.'%')
                        //->orWhere('highlights', 'like', '%'.$searchValue.'%')
                        //->orWhere('included', 'like', '%'.$searchValue.'%')
                        //->orWhere('notIncluded', 'like', '%'.$searchValue.'%')
                        //->orWhere('knowBeforeYouGo', 'like', '%'.$searchValue.'%')
                        //->orWhere('cancelPolicy', 'like', '%'.$searchValue.'%');
                    });
                }

                $productsOfOpt = $productsOfOpt->paginate($perPage, $columns, $pageName, $pagiPage);
                $totalProduct = count($productsOfOpt);
                $paginatorView = view('frontend.product_paginator', ['paginator' => $productsOfOpt])->render();

                foreach ($productsOfOpt as $productOfOpt) {
                    if ($productOfOpt->isPublished == 1 && $productOfOpt->isDraft == 0 && $productOfOpt->isSpecial != 1) {
                        array_push($products, $productOfOpt);
                    }
                }
            }

            $products = array_values($this->commonFunctions->unique_multidimensional_array($products, 'id'));
            $products = $this->prepareProducts($products, $prices, $sortType);
            $totalProduct = count($products);

            return response()->json(['successful' => __('productsFetchedSuccessfully'), 'products' => $products, 'paginator' => $paginatorView, 'totalProduct' => $totalProduct]);
        }
    }

    public function newCheckAvailability(Request $request)
    {

    }

    /**
     * @param $products
     * @param $prices
     * @param $sortType
     * @return array
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     * @throws \Nahid\JsonQ\Exceptions\FileNotFoundException
     * @throws \Nahid\JsonQ\Exceptions\InvalidJsonException
     */
    public function prepareProducts($products, $prices, $sortType)
    {
        $preparedProducts = [];
        $currencyIcon = session()->get('currencyIcon');
        $commissionerEarns = 0;
        $specialOfferPrice = 0;
        $isCommissioner = auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1';
        $coverPhoto = 'default_product.jpg';
        $pricesArr = [];
        if ($prices != '') {
            $pricesArr = explode(' | ', $prices);
        }
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $language = Language::where('code', $langCode)->first();
        foreach ($products as $product) {
            $productTranslation = ProductTranslation::where('productID', $product->id)->where('languageID', $language->id)
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

            $offerPercentage = $this->commonFunctions->getOfferPercentage($product);
            $isThereCoverPhoto = $product->productGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first();
            if ($isThereCoverPhoto) {
                $coverPhoto = $isThereCoverPhoto->src;
            }
            if ($isCommissioner) {
                $specialOffer = $this->commonFunctions->getOfferPercentage($product);
                if ($specialOffer != 0) {
                    $specialOfferPrice = Currency::calculateCurrencyForVisitor($this->commonFunctions->getMinPrice($product->id)) - (($this->commonFunctions->getMinPrice($product->id)) * ($this->commonFunctions->getOfferPercentage($product)) / 100);
                    $specialOfferPrice = number_format((float)$specialOfferPrice, 2, '.', '');
                    $commissionerEarns = Currency::calculateCurrencyForVisitor(($this->commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                } else {
                    $commissionerEarns = Currency::calculateCurrencyForVisitor(Currency::calculateCurrencyForVisitor($this->commonFunctions->getMinPrice($product->id)) - Currency::calculateCurrencyForVisitor(($this->commonFunctions->getCommissionMinPrice($product, auth()->guard('web')->user()->id))));
                }
            } else {
                $specialOffer = $this->commonFunctions->getOfferPercentage($product);
                if ($specialOffer != 0) {
                    $specialOfferPrice = Currency::calculateCurrencyForVisitor($this->commonFunctions->getMinPrice($product->id)) - (($this->commonFunctions->getMinPrice($product->id)) * ($this->commonFunctions->getOfferPercentage($product)) / 100);
                    $specialOfferPrice = number_format((float)$specialOfferPrice, 2, '.', '');
                }
            }
            $normalPrice = Currency::calculateCurrencyForVisitor($this->commonFunctions->getMinPrice($product->id));
            $productItem = [
                'item' => $product,
                'productTranslation' => $productTranslation,
                'misc' => [
                    'offerPercentage' => $offerPercentage,
                    'isThereCoverPhoto' => $isThereCoverPhoto,
                    'coverPhoto' => $coverPhoto,
                    'currencyIcon' => $currencyIcon,
                    'commissionerEarns' => $commissionerEarns,
                    'specialOfferPrice' => $specialOfferPrice,
                    'normalPrice' => $normalPrice,
                    'specialOffer' => $specialOffer,
                    'isCommissioner' => $isCommissioner
                ]
            ];
            if (count($pricesArr) > 0) {
                $isValidProd = [];
                foreach ($pricesArr as $price) {
                    if ($price != '100+') {
                        $pItemMin = explode('-', $price)[0];
                        $pItemMax = explode('-', $price)[1];
                        if ($specialOffer != 0) {
                            if ($specialOfferPrice > $pItemMin && $specialOfferPrice < $pItemMax) {
                                array_push($isValidProd, true);
                            } else {
                                array_push($isValidProd, false);
                            }
                        } else {
                            if ($normalPrice > $pItemMin && $normalPrice < $pItemMax) {
                                array_push($isValidProd, true);
                            } else {
                                array_push($isValidProd, false);
                            }
                        }
                    } else {
                        if ($specialOffer != 0) {
                            if ($specialOfferPrice > 100) {
                                array_push($isValidProd, true);
                            } else {
                                array_push($isValidProd, false);
                            }
                        } else {
                            if ($normalPrice > 100) {
                                array_push($isValidProd, true);
                            } else {
                                array_push($isValidProd, false);
                            }
                        }
                    }
                }
                if (in_array(true, $isValidProd)) {
                    array_push($preparedProducts, $productItem);
                }
            } else {
                array_push($preparedProducts, $productItem);
            }
        }
        if ($sortType != 'recommended') {
            if ($sortType == 'priceAsc') {
                usort($preparedProducts, function ($a, $b) {
                    return $a['misc']['normalPrice'] > $b['misc']['normalPrice'];
                });
            }
            if ($sortType == 'priceDesc') {
                usort($preparedProducts, function ($a, $b) {
                    return $a['misc']['normalPrice'] < $b['misc']['normalPrice'];
                });
            }
            if ($sortType == 'ratingDesc') {
                usort($preparedProducts, function ($a, $b) {
                    return $a['item']['rate'] > $b['item']['rate'];
                });
            }
        }
        return $preparedProducts;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchSpecific(Request $request)
    {
        $q = $request->q;
        $dateFrom = $request->has('dateFrom') ? $request->dateFrom : Carbon::now()->format('d/m/Y');
        $dateTo = $request->has('dateTo') ? $request->dateTo : Carbon::now()->format('d/m/Y');
        $type = $request->type;
        $wordsArray = explode(' ', $q);
        $isCommissioner = auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1';

        $langCode = session()->get('userLanguage') ? session()->get('userLanguage') : 'en';

        if ($request->has('m')) {

            switch ($request->m) {
                case 'Country':
                    $country = Country::where('countries_name', 'like', '%' . $q . '%')->first();
                    $items = Product::where('isPublished', '1')
                        ->where('isDraft', '0')
                        ->where('isSpecial', '!=', 1)
                        ->with(['productCover'])
                        ->where('country', $country->id);
                    break;
                case 'City':
                    $items = Product::where('isPublished', '1')
                        ->where('isDraft', '0')
                        ->where('isSpecial', '!=', 1)
                        ->with(['productCover'])
                        ->where('city', 'like', '%' . $q. '%');
                    break;
                default:
                    $items = Product::where('isPublished', '1')
                        ->where('isDraft', '0')
                        ->where('isSpecial', '!=', 1)
                        ->with(['productCover'])
                        ->where(function($q) use ($wordsArray){
                            foreach ($wordsArray as $w){
                                $q->where('title', 'like', '%'. $w .'%');
                            }
                        });
                    break;
            }

        }else{

            $items = Product::where('isPublished', '1')
                ->where('isDraft', '0')
                ->where('isSpecial', '!=', 1)
                ->with(['productCover', 'translations']);

            if ($langCode != 'en') {
                $items->whereHas('translations', function($q) use ($wordsArray){
                    foreach ($wordsArray as $w){
                        $q->where('title', 'like', '%'. $w .'%');
                    }
                });
            }else{
                $items->where(function($q) use ($wordsArray){
                    foreach ($wordsArray as $w){
                        $q->where('title', 'like', '%'. $w .'%');
                    }
                });
            }

        }

        $total = $items->count();
        $p = $items->paginate(15);

        $categoriesOfProduct = Product::select('category')->where('isPublished', '1')
            ->where(function($q) use ($wordsArray){
                foreach ($wordsArray as $w){
                    $q->where('title', 'like', '%'. $w .'%');
                }
            })->where('isDraft', '0')->distinct('category')->get();

        $categories = [];
        foreach ($categoriesOfProduct as $category) {
            array_push($categories, $category['category']);
        }

        $attractionsOfProduct = Product::select('attractions')->where('isPublished', '1')->where('isDraft', '0')->distinct('attractions')->get();
        $uniqueAttrIds = [];
        foreach ($attractionsOfProduct as $attr) {
            $attrIds = json_decode($attr['attractions'], true);
            foreach ($attrIds as $id) {
                if (!in_array($id, $uniqueAttrIds)) {
                    array_push($uniqueAttrIds, $id);
                }
            }
        }
        $attractions = Attraction::whereIn('id', $uniqueAttrIds)->get();

        return view('frontend.search',
            [
                'categories' => $categories,
                'attractions' => $attractions,
                'q' => $q,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'type' => $type,
                'items' => $p,
                'total' => $total
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRemoveWishlist(Request $request)
    {
        if (!auth()->guard('web')->check()) {
            return response()->json(['isLoggedIn' => false, 'wishListLoggedInError' => __('wishListLoggedInError'), 'success' => __('wishListLoggedInError')]);
        }

        $wishlistType = $request->wishlistType;
        $user = auth()->guard('web')->user();
        if ($wishlistType == 'add') {
            $wishlist = new Wishlist();
            $wishlist->userID = $user->id;
            $wishlist->productID = $request->productID;
            $wishlist->save();

            return response()->json(['isLoggedIn' => true, 'success' => __("addWishListSuccess"), "removeButton" => __('removeFromWishListButton')]);
        }

        if ($wishlistType == 'remove') {
            Wishlist::where('userID', $user->id)->where('productID', $request->productID)->delete();

            return response()->json(['isLoggedIn' => true, 'success' => 'Remove WishList Success', "addButton" => __("addToWishListButton")]);
        }

    }

    /**
     * @param $file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function downloadProductFile($file)
    {
        $fs = Storage::disk('s3');
        $stream = $fs->readStream('/product-files/' . $file);
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            "Content-Type" => $fs->getMimetype('/product-files/' . $file),
            "Content-Length" => $fs->getSize('/product-files/' . $file),
            "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
        ]);
    }

}
