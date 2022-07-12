<?php

namespace App\Http\Controllers;


use App\BlogPost;
use App\City;
use App\CityTranslation;
use App\CountryTranslation;
use App\Http\Controllers\Helpers\AmpRouter;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Attraction;
use App\Page;
use App\Product;
use App\Country;
use App\Language;
use App\ProductTranslation;
use App\AttractionTranslation;
use App\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;


class HomeController extends Controller
{
    public $commonFunctions, $ampRouter;

    public function __construct()
    {
        $this->commonFunctions = new CommonFunctions();
        $this->ampRouter = new AmpRouter();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function index()
    {
        $products = Product::with('translations')->where('isDraft', '=', 0)->where('isPublished', '=', 1)->where('isSpecial', '!=', 1);
        $page = Page::findOrFail(1);
        $productOrder = json_decode($page->productOrder, true) == null ? [] : json_decode($page->productOrder, true);
        $homePageProduct = [];
        $homePageProductID = [];

        if ($productOrder)

            
        {
            $ids = implode(',', $productOrder);
            $sortPageProduct = Product::whereIn('id', $productOrder)->orderByRaw(DB::raw("FIELD(id, $ids)"))->get();
        }

        if(empty($sortPageProduct)){
            $sortPageProduct = Product::where("id", 1)->get();
        }

        foreach($sortPageProduct as $sp){
            array_push($homePageProduct, $sp);
            array_push($homePageProductID, $sp->id);
        }

       if (count($homePageProduct) < 8) {
            foreach ($products->get() as $product) {
                if (!in_array($product->id, $homePageProductID) && count($homePageProduct) < 8) {
                    array_push($homePageProduct, $product);
                }
            }
        }


        $productsForSeine = $products->where('attractions', 'like','%\"1\"%')->take(4)->get();
        $activeAttractions = Attraction::with('translations')->where('isActive', 1)->get();
        return $this->ampRouter->view('frontend.home', ['homePageProduct' => $homePageProduct, 'productsForSeine' => $productsForSeine, 'activeAttractions' => $activeAttractions]);
    }

    /**
     * Anasayfadaki  Top attraction kısmındaki ajax işlemini çalıştırır
     *
     * @param Request $request
     */
    public function homeAttractionTours(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric|min:1'
        ]);

        $attractionModel = Attraction::with('translations')->findOrFail($request->id);
        //return response($attractionModel);

        $view = view('frontend-partials.home-attraction', [
            'attractionModel' => $attractionModel,
            'attraction' => $attractionModel,
        ])->render();

        return response($view);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function privacyPolicy()
    {
        return view('frontend.privacy-policy');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchVarious(Request $request)
    {
        $productsArray  = [];
        $wordsArray = explode( ' ', $request->value);
        $langCode = session()->get('userLanguage') ? session()->get('userLanguage') : 'en';
        if ($langCode == 'en') {
            $countries = Country::select('countries_name')->where('countries_name', 'like', $request->value.'%')->get();
            $cities = City::select('name')->where('name', 'like', '%'.$request->value.'%')->pluck('name');
            $products = Product::select('id', 'title', 'url')->where('isPublished', '1')
                ->where('isDraft', '0')->where('isSpecial', '!=', 1);
            $uniquenessKey = 'id';
            $attractions = Attraction::select('name')->where('isActive', 1)->where('name', 'like', $request->value.'%')->get();
        } else {
            $language = Language::where('code', $langCode)->first();
            $countries = CountryTranslation::select('countries_name')->where('countries_name', 'like', $request->value.'%')
                ->where('languageID', $language->id)->get();
            if (!$countries) {
                $countries = Country::select('countries_name')->where('countries_name', 'like', $request->value.'%')->get();
            }
            $cities = CityTranslation::select('name')->where('name', 'like', '%'.$request->value.'%')
                ->where('languageID', $language->id)->pluck('name');
            if (!$cities) {
                $cities = City::select('name')->where('name', 'like', '%'.$request->value.'%')->pluck('name');
            }
            $products = ProductTranslation::select('productID', 'title', 'url')
                ->where('languageID', $language->id);
            $uniquenessKey = 'productID';
            $attractions = AttractionTranslation::select('name')->where('name', 'like', $request->value.'%')->get();
        }

        foreach ($wordsArray as $word) {
            $products->where('title', 'like', '%'.$word.'%');
        }
        $products = $products->get();
        foreach ($products as $p) {
            array_push($productsArray, $p);
        }
        $arr = $this->commonFunctions->unique_multidimensional_array($productsArray, $uniquenessKey);
        $products = array_values($arr);

        return response()->json(
            [
                'successful' => 'Fetched successfully',
                'countries' => $countries,
                'cities' => $cities,
                'products' => $products,
                'attractions' => $attractions
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cities()
    {
        $cities = Product::select('city')->where('isPublished', '1')->where('isDraft', '0')->distinct('city')->get();

        return view('frontend.cities', ['cities'=>$cities]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function citiesPage(Request $request)
    {
        $products = Product::where('isDraft', '=', 0)->where('isPublished', '=', 1)->where('isSpecial', '!=', 1)->where('city', '=', 'Paris')->limit(8)->get();
        $cities = Product::select('city')->where('isPublished', '1')->where('isDraft', '0')->distinct('city')->get();
        $attractions = Attraction::where('isActive', 1)->get();
        return view('frontend.cities-page', ['products' => $products, 'attractions' => $attractions, 'cities'=>$cities]);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMap()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $now = Carbon::now()->toAtomString();
        $content = view('frontend.sitemap', compact('products','now'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapEn()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-en', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapTr()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-tr', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapFr()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-fr', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapRu()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-ru', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapEs()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-es', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapDe()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-de', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapIt()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-it', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function siteMapPt()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-pt', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    public function siteMapNl()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '=', 0)->orderBy('updated_at', 'DESC')->get();
        $attractions= Attraction::where('isActive', 1)->get();
        $now = Carbon::now()->toAtomString();
        $blogPosts = BlogPost::all();
        $content = view('frontend.sitemap-nl', compact('products','now', 'attractions', 'blogPosts'));
        return response($content)->header('Content-Type', 'application/xml');
    }

    public function mobileAPK()
    {
        $pdf = PDF::loadView('pdfs.multiple-tickets', $data, $data2);
        return $pdf->download('GlobalTicketsp.pdf');
    }

}

