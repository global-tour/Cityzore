<?php

namespace App\Http\Controllers\Admin;

use App\Adminlog;
use App\Attraction;
use App\AttractionTranslation;
use App\Category;
use App\CategoryTranslation;
use App\City;
use App\CityTranslation;
use App\Country;
use App\CountryTranslation;
use App\FAQ;
use App\FAQTranslation;
use App\Language;
use App\MetaTag;
use App\SpecialOffers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Config;
use App\Currency;
use App\Page;
use App\Product;
use App\Option;
use App\ProductTranslation;
use App\OptionTranslation;
use App\ProdMetaTagsTrans;
use App\PageMetaTagsTrans;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Cocur\Slugify\Slugify;
use App\Route;
use App\RouteLocalization;
use App\BlogPost;
use App\BlogTranslation;
use App\BlogMetaTagsTrans;


class ConfigController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config()
    {
        $userID = auth()->guard('admin') ? -1 : auth()->user()->id;
        $config = Config::where('userID', $userID)->first();
        $currencies = Currency::where('isActive', 1)->get();
        $languages = Language::where('isActive', 1)->get();
        return view('panel.config.edit',
            [
                'config' => $config,
                'currencies' => $currencies,
                'languages' => $languages
            ]
        );
    }

       /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
       public function cacheConfig()
       {
        $data = "[]";
        if(Storage::disk('public')->exists('page_cache.json')){
         $data = Storage::disk('public')->get('page_cache.json');

        }
        $data = json_decode($data, true);

        return view('panel.config.page-cache', compact('data'));
       }


         public function saveCacheConfig(Request $request)
       {
         $pages = $request->except('_token');
         foreach ($pages as $page => $value) {
             if(!isset($value['cache_activation']) || intval($value['cache_time'] < 1)){
              unset($pages[$page]);
             }
         }
         Storage::disk('public')->put('page_cache.json', json_encode($pages));
         return redirect()->back()->with('message', 'Changes has Been Stored Successfully');
        
       }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveConfig(Request $request, $id)
    {
        $config = Config::findOrFail($id);
        $config->currencyID = $request->currencyID;
        $config->languageID = $request->languageID;
        if (auth()->guard('admin')->check()) {
            $config->couponDiscountType = $request->couponDiscountType;
            $config->couponDiscountAmount = $request->couponDiscountAmount;
            $config->meeting_distance = $request->meeting_distance;
        }
        $config->save();
        return redirect('/');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generalConfig()
    {
        return view('panel.config.general');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function seoForPages()
    {
        $pages = Page::all();
        return view('panel.config.seoforpages', ['pages' => $pages, 'type' => 'cz']);
    }


      public function seoForPagesPCT()
    {
        $pages = Page::on('mysql2')->get();
        return view('panel.config.seoforpages', ['pages' => $pages, 'type' => 'pct']);
    }



  public function seoForPagesPCTcom()
    {
        $pages = Page::on('mysql3')->get();
        return view('panel.config.seoforpages', ['pages' => $pages, 'type' => 'pctcom']);
    }




  public function seoForPagesCTP()
    {
        $pages = Page::on('mysql4')->get();
        return view('panel.config.seoforpages', ['pages' => $pages, 'type' => 'ctp']);
    }



    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changeMetaTags($id, $platform = 'cz')
    {
        if($platform == 'pct'){
            $page = Page::on('mysql2')->findOrFail($id);
        }else if ($platform == 'pctcom') {
            $page = Page::on('mysql3')->findOrFail($id);
        }else if ($platform == 'ctp') {
           $page = Page::on('mysql4')->findOrFail($id);
        }else{
         $page = Page::findOrFail($id);
        }
        
        return view('panel.config.changemetatags', ['page' => $page, 'platform' => $platform]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveMetaTags(Request $request, $id, $platform = 'cz')
    {

          if($platform == 'pct'){
            $page = Page::on('mysql2')->findOrFail($id);
        }else if ($platform == 'pctcom') {
            $page = Page::on('mysql3')->findOrFail($id);
        }else if ($platform == 'ctp') {
           $page = Page::on('mysql4')->findOrFail($id);
        }else{
         $page = Page::findOrFail($id);
        }
        



        $page->title = $request->title;
        $page->description = $request->description;
        $page->keywords = $request->keywords;
        $adminLog = new AdminLog();
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Meta Tags';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Save Meta Tags';
        $adminLog->details = $user->name . ' saved meta tags for ' . $page->name . ' page';
        $adminLog->tableName = 'pages';
        $adminLog->columnName = 'title, description, keywords';
        if ($page->save()) {
            $adminLog->result = 'successful';
            return redirect('/general-config/seo-for-pages');
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productTranslations()
    {
        $products = Product::where('isDraft', 0)->where('isSpecial', 0)->get();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.producttranslations', ['products' => $products, 'languages' => $languages, 'type' => 'cz']);
    }

    /**
     * Product translations page for pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productTranslationsPCT()
    {
        $products = Product::on('mysql2')->where('isDraft', 0)->where('isSpecial', 0)->get();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.producttranslations', ['products' => $products, 'languages' => $languages, 'type' => 'pct']);
    }

    /**
     * Product translations page for pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productTranslationsPCTcom()
    {
        $products = Product::on('mysql3')->where('isDraft', 0)->where('isSpecial', 0)->get();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.producttranslations', ['products' => $products, 'languages' => $languages, 'type' => 'pctcom']);
    }

    /**
     * Product translations page for citytours.paris
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productTranslationsCTP()
    {
        $products = Product::on('mysql4')->where('isDraft', 0)->where('isSpecial', 0)->get();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.producttranslations', ['products' => $products, 'languages' => $languages, 'type' => 'ctp']);
    }

    /**
     * Checks if an object(product, option etc.) is translated for all languages
     *
     * @param $id
     * @param $languageID
     * @param $type
     * @param string $platform
     * @return bool
     */
    public function isTranslated($id, $languageID, $type, $platform = '')
    {
        $isTranslated = null;
        if ($type == 'product') {
            $model = new ProductTranslation();
            if ($platform == 'pct') {
                $model->setConnection('mysql2');
            }
            elseif ($platform == 'pctcom') {
                $model->setConnection('mysql3');
            }
            elseif ($platform == 'ctp') {
                $model->setConnection('mysql4');
            }
            $isTranslated = $model->where('productID', $id)->where('languageID', $languageID)
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
                })
                ->first();
        }

        if ($type == 'option') {
            $isTranslated = OptionTranslation::where('optionID', $id)->where('languageID', $languageID)
                ->where(function ($query) {
                    $query->where('title', '!=', null)
                        ->where('description', '!=', null);
                })
                ->first();
        }

        if ($type == 'productmetatags') {
            $model = new ProdMetaTagsTrans();
            if ($platform == 'pct') {
                $model->setConnection('mysql2');
            }
            elseif ($platform == 'pctcom') {
                $model->setConnection('mysql3');
            }
            elseif ($platform == 'ctp') {
                $model->setConnection('mysql4');
            }
            $isTranslated = $model->where('productID', $id)->where('languageID', $languageID)
                ->where(function ($query) {
                    $query->where('title', '!=', null)
                        ->where('description', '!=', null)
                        ->where('keywords', '!=', null);
                })
                ->first();
        }

        if ($type == 'pagemetatags') {
            $model = new PageMetaTagsTrans();

             if ($platform == 'pct') {
                $model->setConnection('mysql2');
            }
            elseif ($platform == 'pctcom') {
                $model->setConnection('mysql3');
            }
            elseif ($platform == 'ctp') {
                $model->setConnection('mysql4');
            }

            $isTranslated = $model->where('pageID', $id)->where('languageID', $languageID)
                ->where(function ($query) {
                    $query->where('title', '!=', null)
                        ->where('description', '!=', null)
                        ->where('keywords', '!=', null);
                })
                ->first();


        }

        if ($type == 'category') {
            $isTranslated = CategoryTranslation::where('categoryID', $id)->where('languageID', $languageID)->where(function($query) {
                $query->where('categoryName', '!=', null);
            })->first();
        }

        if ($type == 'faq') {
            $isTranslated = FAQTranslation::where('faqID', $id)->where('languageID', $languageID)->where(function($query) {
                $query->where('question', '!=', null)->where('answer', '!=', null);
            })->first();
        }

        if ($type == 'attraction') {


              $model = new AttractionTranslation();
            if ($platform == 'pct') {
                $model->setConnection('mysql2');
            }
            elseif ($platform == 'pctcom') {
                $model->setConnection('mysql3');
            }
            elseif ($platform == 'ctp') {
            
                $model->setConnection('mysql4');
            }
            $isTranslated = $model->where('attractionID', $id)->where('languageID', $languageID)->where(function($query) {
                $query->where('name', '!=', null)
                    ->where('description', '!=', null)
                    ->where('slug', '!=', null);
                   
            })->first();


           
        }

        if ($type == 'route') {
            $isTranslated = RouteLocalization::where('routeID', $id)->where('languageID', $languageID)->where('route', '!=', null)->first();
        }

        if ($type == 'blog') {
            $model = new BlogTranslation();
            if ($platform == 'pct') {
                $model->setConnection('mysql2');
            }
            elseif ($platform == 'pctcom') {
                $model->setConnection('mysql3');
            }
            elseif ($platform == 'ctp') {
                $model->setConnection('mysql4');
            }
            $isTranslated = $model->where('blogID', $id)->where('languageID', $languageID)->where(function($query) {
                $query->where('title', '!=', null)
                    ->where('postContent', '!=', null)
                    ->where('category', '!=', null)
                   // ->where('slug', '!=', null)
                    ->where('url', '!=', null);
            })->first();
        }

        if ($type == 'blogmetatags') {
            $model = new BlogMetaTagsTrans();
            if ($platform == 'pct') {
                $model->setConnection('mysql2');
            }
            elseif ($platform == 'pctcom') {
                $model->setConnection('mysql3');
            }
            elseif ($platform == 'ctp') {
                $model->setConnection('mysql4');
            }
            $isTranslated = $model->where('blogID', $id)->where('languageID', $languageID)->where(function ($query) {
                $query->where('title', '!=', null)
                    ->where('description', '!=', null)
                    ->where('keywords', '!=', null);
            })->first();
        }

        if ($type == 'country') {
            $isTranslated = CountryTranslation::where('countryID', $id)->where('languageID', $languageID)->where(function($query) {
                $query->where('countries_name', '!=', null);
            })->first();
        }

        if ($type == 'city') {
            $isTranslated = CityTranslation::where('cityID', $id)->where('languageID', $languageID)->where(function($query) {
                $query->where('name', '!=', null);
            })->first();
        }

        return !is_null($isTranslated);
    }

    /**
     * @param $productID
     * @param $languageID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateProduct($productID, $languageID, Request $request)
    {
        $platform = $request->platform;
        $productTranslationModel = new ProductTranslation();
        $productModel = new Product();
        if ($platform == 'pct') {
            $productTranslationModel->setConnection('mysql2');
            $productModel->setConnection('mysql2');
        }

        elseif ($platform == 'pctcom') {
            $productTranslationModel->setConnection('mysql3');
            $productModel->setConnection('mysql3');
        }

        elseif ($platform == 'ctp') {
            $productTranslationModel->setConnection('mysql4');
            $productModel->setConnection('mysql4');
        }

        $productTranslation = $productTranslationModel->where('productID', $productID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $product = $productModel->findOrFail($productID);
        $options = $product->options()->get();
        $notTranslatedOptions = [];
        foreach ($options as $option) {
            if (!$this->isTranslated($option->id, $languageID, 'option')) {
                array_push($notTranslatedOptions, $option);
            }
        }

        return view('panel.config.translateproduct',
            [
                'productTranslation' => $productTranslation,
                'productID' => $productID,
                'languageID' => $languageID,
                'product' => $product,
                'languageToTranslate' => $languageToTranslate,
                'options' => $notTranslatedOptions,
                'type' => $platform
            ]
        );
    }

    /**
     * @param $productID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateProductForAll($productID, Request $request)
    {
        $product = Product::findOrFail($productID);
        $languages = Language::where('code', '!=', 'en')->get();
        $initialLanguage = Language::where('code', 'tr')->first(); // Turkish

        return view( 'panel.config.translateproductforall',
            [
                'product' => $product,
                'languages' => $languages,
                'initialLanguage' => $initialLanguage,
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductTranslation(Request $request)
    {
        $productID = $request->productID;
        $languageID = $request->languageID;
        $productTranslation = ProductTranslation::where('productID', $productID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $product = Product::findOrFail($productID);
        $options = $product->options()->get();
        $notTranslatedOptions = [];
        $translatedOptions = [];
        foreach ($options as $option) {
            if (!$this->isTranslated($option->id, $languageID, 'option')) {
                $notTranslatedOption = OptionTranslation::where('optionID', $option->id)->where('languageID', $languageID)->first();
                array_push($notTranslatedOptions, ['option' => $option, 'notTranslatedOption' => $notTranslatedOption]);
            } else {
                $translatedOption = OptionTranslation::where('optionID', $option->id)->where('languageID', $languageID)->first();
                array_push($translatedOptions, ['option' => $option, 'translatedOption' => $translatedOption]);
            }
        }

        return response()->json(
            [
                'productTranslation' => $productTranslation,
                'languageToTranslate' => $languageToTranslate,
                'product' => $product,
                'translatedOptions' => $translatedOptions,
                'notTranslatedOptions' => $notTranslatedOptions
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOptionTranslationForAll(Request $request)
    {
        $optionTranslation = OptionTranslation::where('optionID', $request->optionID)->where('languageID', $request->languageID)->first();
        $option = Option::findOrFail($request->optionID);
        if (!$optionTranslation) {
            $optionTranslation = new OptionTranslation();
            $optionTranslation->optionID = $request->optionID;
            $optionTranslation->languageID = $request->languageID;
        }
        $optionTranslation->title = $request->title;
        $optionTranslation->description = $request->description;
        $optionTranslation->meetingComment = $request->meetingComment;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($request->languageID)->name;
        $user = auth()->guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Option Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Option';
        $adminLog->details = $user->name . ' translated option to ' . $language;
        $adminLog->optionRefCode = $option->referenceCode;
        $adminLog->tableName = 'option_translations';
        if ($optionTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            return response()->json(['successful' => 'Saved Successfully!']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveProductTranslationForAll(Request $request)
    {


                 // parsing tagify to database
            $highlights = json_decode($request->highlights, true);
        $highlights = collect($highlights);
        $highlights = $highlights->map(function($row){
            return $row['value'];
        })->toArray();
        $highlights = implode('|', $highlights);


        $knowBeforeYouGo = json_decode($request->knowBeforeYouGo, true);
        $knowBeforeYouGo = collect($knowBeforeYouGo);
        $knowBeforeYouGo = $knowBeforeYouGo->map(function($row){
            return $row['value'];
        })->toArray();
        $knowBeforeYouGo = implode('|', $knowBeforeYouGo);


        $includes = json_decode($request->included, true);
        $includes = collect($includes);
        $includes = $includes->map(function($row){
            return $row['value'];
        })->toArray();
        $includes = implode('|', $includes);

        $notIncluded = json_decode($request->notIncluded, true);
        $notIncluded = collect($notIncluded);
        $notIncluded = $notIncluded->map(function($row){
            return $row['value'];
        })->toArray();
        $notIncluded = implode('|', $notIncluded);
         // parsing tagify to database


        $slugify = new Slugify();
        $product = Product::findOrFail($request->productID);
        $productTranslation = ProductTranslation::where('productID', $request->productID)->where('languageID', $request->languageID)->first();
        if (!$productTranslation) {
            $productTranslation = new ProductTranslation();
            $productTranslation->productID = $request->productID;
            $productTranslation->languageID = $request->languageID;
        }

        $url = Str::slug(strtolower($product->city), '-') . '/' . $slugify->slugify($request->title) . '-' . $request->productID;
        $productTranslation->url = $url;
        $productTranslation->title = $request->title;
        $productTranslation->shortDesc = $request->shortDesc;
        $productTranslation->fullDesc = $request->fullDesc;
        $productTranslation->highlights = $highlights;
        $productTranslation->included = $includes;
        $productTranslation->notIncluded = $notIncluded;
        $productTranslation->knowBeforeYouGo = $knowBeforeYouGo;
        $productTranslation->category = $request->category;
        $productTranslation->cancelPolicy = $request->cancelPolicy;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($request->languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Product Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Product';
        $adminLog->details = $user->name. ' translated product to '. $language;
        $adminLog->productRefCode = $product->referenceCode;
        $adminLog->tableName = 'product_translations';
        if ($productTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            return response()->json(['successful' => 'Saved Successfully!']);
        }

    }

    /**
     * @param Request $request
     * @param $productID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveProductTranslation(Request $request, $productID, $languageID)
    {

           // parsing tagify to database
            $highlights = json_decode($request->highlights, true);
        $highlights = collect($highlights);
        $highlights = $highlights->map(function($row){
            return $row['value'];
        })->toArray();
        $highlights = implode('|', $highlights);


        $knowBeforeYouGo = json_decode($request->knowBeforeYouGo, true);
        $knowBeforeYouGo = collect($knowBeforeYouGo);
        $knowBeforeYouGo = $knowBeforeYouGo->map(function($row){
            return $row['value'];
        })->toArray();
        $knowBeforeYouGo = implode('|', $knowBeforeYouGo);


        $includes = json_decode($request->included, true);
        $includes = collect($includes);
        $includes = $includes->map(function($row){
            return $row['value'];
        })->toArray();
        $includes = implode('|', $includes);

        $notIncluded = json_decode($request->notIncluded, true);
        $notIncluded = collect($notIncluded);
        $notIncluded = $notIncluded->map(function($row){
            return $row['value'];
        })->toArray();
        $notIncluded = implode('|', $notIncluded);
         // parsing tagify to database



        $type = $request->type;
        $slugify = new Slugify();
        $productModel = new Product();
        $productTranslationModel = new ProductTranslation();
        if ($type == 'pct') {
            $productModel->setConnection('mysql2');
            $productTranslationModel->setConnection('mysql2');
        }
        elseif ($type == 'pctcom') {
            $productModel->setConnection('mysql3');
            $productTranslationModel->setConnection('mysql3');
        }
        elseif ($type == 'ctp') {
            $productModel->setConnection('mysql4');
            $productTranslationModel->setConnection('mysql4');
        }
        $product = $productModel->findOrFail($productID);
        $productTranslation = $productTranslationModel->where('productID', $productID)->where('languageID', $languageID)->first();
        if (!$productTranslation) {
            $productTranslation = new ProductTranslation();
            if ($type == 'pct') {
                $productTranslation->setConnection('mysql2');
            }
            elseif ($type == 'pctcom') {
                $productTranslation->setConnection('mysql3');
            }
            elseif ($type == 'ctp') {
                $productTranslation->setConnection('mysql4');
            }
            $productTranslation->productID = $productID;
            $productTranslation->languageID = $languageID;
        }

        $url = Str::slug(strtolower($product->city), '-') . '/' . $slugify->slugify($request->title) . '-' . $productID;
        $productTranslation->url = $url;
        $productTranslation->title = $request->title;
        $productTranslation->shortDesc = $request->shortDesc;
        $productTranslation->fullDesc = $request->fullDesc;
        $productTranslation->highlights = $highlights;
        $productTranslation->included = $includes;
        $productTranslation->notIncluded = $notIncluded;
        $productTranslation->knowBeforeYouGo = $knowBeforeYouGo;
        $productTranslation->category = $request->category;
        $productTranslation->cancelPolicy = $request->cancelPolicy;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Product Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Product';
        $adminLog->details = $user->name. ' translated product to '. $language;
        $adminLog->productRefCode = $product->referenceCode;
        $adminLog->tableName = 'product_translations';
        if ($productTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=',$url)) > 1 ? explode('=',$url)[1] : 1;

            if($request->ajax()){
                return response()->json(['status' => 'success', 'message' => 'changes updated successfully!']);
            }
            return redirect('/general-config/product-translations?page='.$pageID);


        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function optionTranslations()
    {
        $options = Option::all();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.optiontranslations', ['options' => $options, 'languages' => $languages]);
    }

    /**
     * @param $optionID
     * @param $languageID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateOption($optionID, $languageID)
    {
        $optionTranslation = OptionTranslation::where('optionID', $optionID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $option = Option::findOrFail($optionID);

        return view('panel.config.translateoption',
            [
                'optionTranslation' => $optionTranslation,
                'optionID' => $optionID,
                'languageID' => $languageID,
                'option' => $option,
                'languageToTranslate' => $languageToTranslate
            ]
        );
    }

    /**
     * @param $categoryID
     * @param $languageID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateCategory($categoryID, $languageID)
    {
        $categoryTranslation = CategoryTranslation::where('categoryID', $categoryID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $category = Category::findOrFail($categoryID);
        return view('panel.config.translatecategory', [
            'categoryTranslation' => $categoryTranslation,
            'categoryID' => $categoryID,
            'languageID' => $languageID,
            'category' => $category,
            'languageToTranslate' => $languageToTranslate
        ]);
    }

    /**
     * @param $attractionID
     * @param $languageID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateAttraction($attractionID, $languageID, Request $request)
    {

           $platform = $request->platform;
        $attractionTranslationModel = new AttractionTranslation();
        $attractionModel = new Attraction();
        if ($platform == 'pct') {
            $attractionTranslationModel->setConnection('mysql2');
            $attractionModel->setConnection('mysql2');
        }
        elseif ($platform == 'pctcom') {
            $attractionTranslationModel->setConnection('mysql3');
            $attractionModel->setConnection('mysql3');
        }
        elseif ($platform == 'ctp') {
            $attractionTranslationModel->setConnection('mysql4');
            $attractionModel->setConnection('mysql4');
        }
       
        
        $attractionTranslation = $attractionTranslationModel->where('attractionID', $attractionID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $attraction = $attractionModel->findOrFail($attractionID);
        return view('panel.config.translateattraction', [
            'attractionTranslation' => $attractionTranslation,
            'attractionID' => $attractionID,
            'languageID' => $languageID,
            'attraction' => $attraction,
            'languageToTranslate' => $languageToTranslate,
            'type' => $platform
        ]);
    }

    /**
     * @param Request $request
     * @param $optionID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveOptionTranslation(Request $request, $optionID, $languageID)
    {
        $optionTranslation = OptionTranslation::where('optionID', $optionID)->where('languageID', $languageID)->first();
        $option = Option::findOrFail($optionID);
        if (!$optionTranslation) {
            $optionTranslation = new OptionTranslation();
            $optionTranslation->optionID = $optionID;
            $optionTranslation->languageID = $languageID;
        }
        $optionTranslation->title = $request->title;
        $optionTranslation->description = $request->description;
        $optionTranslation->meetingComment = $request->meetingComment;
        $optionTranslation->included = $request->included;
        $optionTranslation->notIncluded = $request->notIncluded;
        $optionTranslation->knowBeforeYouGo = $request->knowBeforeYouGo;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Option Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Option';
        $adminLog->details = $user->name. ' translated option to '. $language;
        $adminLog->optionRefCode = $option->referenceCode;
        $adminLog->tableName = 'option_translations';
        if ($optionTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=',$url)) > 1 ? explode('=',$url)[1] : 1;
            return redirect('/general-config/option-translations?page='.$pageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @param Request $request
     * @param $categoryID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveCategoryTranslation(Request $request, $categoryID, $languageID)
    {
        $categoryTranslation = CategoryTranslation::where('categoryID', $categoryID)->where('languageID', $languageID)->first();
        if (!$categoryTranslation) {
            $categoryTranslation = new CategoryTranslation();
            $categoryTranslation->categoryID = $categoryID;
            $categoryTranslation->languageID = $languageID;
        }
        $categoryTranslation->categoryName = $request->categoryName;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Category Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Category';
        $adminLog->details = $user->name. ' translated category to '. $language;
        $adminLog->tableName = 'category_translations';
        if ($categoryTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=',$url)) > 1 ? explode('=',$url)[1] : 1;
            return redirect('/general-config/category-translations?page='.$pageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @param Request $request
     * @param $attractionID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveAttractionTranslation(Request $request, $attractionID, $languageID)
    {
        $type = $request->type;
        $attractionTranslationModel = new AttractionTranslation();

         if ($type == 'pct') {
            $attractionTranslationModel->setConnection('mysql2');
        }
        elseif ($type == 'pctcom') {
            $attractionTranslationModel->setConnection('mysql3');
        }
        elseif ($type == 'ctp') {
            $attractionTranslationModel->setConnection('mysql4');
        }



        $slugify = new Slugify();
        $attractionTranslation = $attractionTranslationModel->where('attractionID', $attractionID)->where('languageID', $languageID)->first();
        if (!$attractionTranslation) {
            $attractionTranslation = new AttractionTranslation();

           if ($type == 'pct') {
            $attractionTranslation->setConnection('mysql2');
        }
        elseif ($type == 'pctcom') {
            $attractionTranslation->setConnection('mysql3');
        }
        elseif ($type == 'ctp') {
            $attractionTranslation->setConnection('mysql4');
        }
       

            $attractionTranslation->attractionID = $attractionID;
            $attractionTranslation->languageID = $languageID;
        }
        $attractionTranslation->name = $request->name;
        $attractionTranslation->slug = $slugify->slugify($request->name);
        $attractionTranslation->description = $request->description;
        $attractionTranslation->tags = $request->tags;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Attraction Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Attraction';
        $adminLog->details = $user->name. ' translated attraction to '. $language;
        $adminLog->tableName = 'attraction_translations';
        if ($attractionTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=',$url)) > 1 ? explode('=',$url)[1] : 1;



             if ($type == 'pct') {
                return redirect('/general-config/attraction-translations-pct?page='.$pageID);
            }

            elseif ($type == 'pctcom') {
                return redirect('/general-config/attraction-translations-pctcom?page='.$pageID);
            }

            elseif ($type == 'ctp') {
                return redirect('/general-config/attraction-translations-ctp?page='.$pageID);
            }

            return redirect('/general-config/attraction-translations?page='.$pageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getProductSort()
    {
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->get();
        $pages = Page::whereIn('id' , [1,2,3,4,5,6,7,8,9,10,11])->get();
        return view('panel.config.productsort', [
            'products' => $products,
            'pages' => $pages,
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function pageSelect(Request $request)
    {
        $pageID = $request->pageID;
        $page = Page::findOrFail($pageID);
        $finalProducts = [];
        $products = Product::where('isPublished', '=', 1)->where('isDraft', '=', 0)->where('isSpecial', '!=', 1)->get();
        $attractionID = 0;
        switch ($pageID) {
            case 3:
                $specialOffers = SpecialOffers::where('isActive', '=', 1)->get();
                $productIDs = [];
                if (count($specialOffers) > 0) {
                    foreach ($specialOffers as $specialOffer) {
                        array_push($productIDs, $specialOffer->productID);
                        $finalProducts = Product::whereIn('id', $productIDs)->get();
                    }
                } else
                    $finalProducts = null;
                break;
            case 4:
                //Eiffel Tower
                $attractionID = 1;
                break;
            case 5:
                //Seine Cruise
                $attractionID = 2;
                break;
            case 6:
                //Louvre Museum
                $attractionID = 5;
                break;
            case 7:
                //Big Bus
                $attractionID = 3;
                break;
            case 8:
                //Cabaret Show
                $attractionID = 4;
                break;
            case 9:
                //Versailles Palace
                $attractionID = 6;
                break;
            case 10:
                //Disneyland
                $attractionID = 13;
                break;
            case 11:
                //Museum / Exhibition
                $attractionID = 24;
                break;
            default:
                $finalProducts = $products;
        }

        if ($attractionID != 0) {
            foreach ($products as $product) {
                $productAttraction = json_decode($product->attractions, true);
                if (in_array($attractionID, $productAttraction)) {
                    array_push($finalProducts, $product);
                }
            }
        }

        return ['finalProducts' => $finalProducts, 'page' => $page];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function productSelect(Request $request)
    {
        $product = Product::findOrFail($request->productID);
        $page = Page::findOrFail($request->pageID);
        $productOrder = json_decode($page->productOrder, true);
        if ($productOrder != null && $productOrder != '[]') {
            if (in_array($request->productID, json_decode($page->productOrder, true))) {
                return ['SAME_PRODUCT' => 'SAME_PRODUCT'];
            }
        }

        return ['product' => $product];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function sendProductSort(Request $request)
    {
        $sortedProducts = $request->sortedProducts;
        $page = Page::findOrFail($request->pageID);
        $page->productOrder = json_encode($sortedProducts);
        $adminLog = new Adminlog();
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Product Sort';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Sorted Products';
        $adminLog->details = $user->name. ' sorted products for '. $page->name. ' page.';
        $adminLog->tableName = 'pages';
        $adminLog->columnName = 'productOrder';
        if ($page->save()) {
            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        return ['sortedProducts' => $sortedProducts, 'page' => $page];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getProductSortForAPage(Request $request)
    {
        $page = Page::findOrFail($request->pageID);
        $productOrder = $page->productOrder;
        $sortedProducts = [];
        foreach (json_decode($productOrder, true) as $productOrder) {
            $product = Product::findOrFail($productOrder);
            array_push($sortedProducts, $product);
        }

        return ['sortedProducts' => $sortedProducts];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function unsetSortedProduct(Request $request)
    {
        $productID = $request->productID;
        $page = Page::findOrFail($request->pageID);
        $productOrder = json_decode($page->productOrder, true);
        if (($key = array_search($productID, $productOrder)) != false) {
            unset($productOrder[$key]);
            if (count($productOrder) == 0) {
                $productOrder = null;
                $page->productOrder = $productOrder;
            } else {
                $page->productOrder = json_encode($productOrder);
            }
            $adminLog = new Adminlog();
            $user = Auth::guard('admin')->user();
            $adminLog->userID = $user->id;
            $adminLog->page = 'Unset Product Sort';
            $adminLog->url = $request->fullUrl();
            $adminLog->action = 'Unset Sorted Products';
            $adminLog->details = $user->name. ' unset sorted products for '. $page->name. ' page.';
            $adminLog->tableName = 'pages';
            $adminLog->columnName = 'productOrder';
            if ($page->save()) {
                $adminLog->result = 'successful';
            } else {
                $adminLog->result = 'failed';
            }
            $adminLog->save();
        }

        return ['productOrder' => $productOrder, 'page' => $page];
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productMetaTagsTranslations()
    {
        $metaTags = MetaTag::all();
        $products = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->product()->first())) {
                array_push($products, $metaTag->product()->first());
            }
        }

        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.prodmetatagstrans', ['products' => $products, 'languages' => $languages, 'type' => 'cz']);
    }

    /**
     * Product meta tags translations page for pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productMetaTagsTranslationsPCT()
    {
        $metaTags = MetaTag::on('mysql2')->get();
        $products = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->product()->first())) {
                array_push($products, $metaTag->product()->first());
            }
        }

        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.prodmetatagstrans', ['products' => $products, 'languages' => $languages, 'type' => 'pct']);
    }

    /**
     * Product meta tags translations page for pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productMetaTagsTranslationsPCTcom()
    {
        $metaTags = MetaTag::on('mysql3')->get();
        $products = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->product()->first())) {
                array_push($products, $metaTag->product()->first());
            }
        }

        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.prodmetatagstrans', ['products' => $products, 'languages' => $languages, 'type' => 'pctcom']);
    }

    /**
     * Product meta tags translations page for pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productMetaTagsTranslationsCTP()
    {
        $metaTags = MetaTag::on('mysql4')->get();
        $products = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->product()->first())) {
                array_push($products, $metaTag->product()->first());
            }
        }

        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.prodmetatagstrans', ['products' => $products, 'languages' => $languages, 'type' => 'ctp']);
    }

    /**
     * @param $productID
     * @param $languageID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateProductMetaTags($productID, $languageID, Request $request)
    {
        $platform = $request->platform;
        $prodMetaTagsTransModel = new ProdMetaTagsTrans();
        $productModel = new Product();
        if ($platform == 'pct') {
            $prodMetaTagsTransModel->setConnection('mysql2');
            $productModel->setConnection('mysql2');
        }
        elseif ($platform == 'pctcom') {
            $prodMetaTagsTransModel->setConnection('mysql3');
            $productModel->setConnection('mysql3');
        }
        elseif ($platform == 'ctp') {
            $prodMetaTagsTransModel->setConnection('mysql4');
            $productModel->setConnection('mysql4');
        }
        $prodMetaTagsTrans = $prodMetaTagsTransModel->where('productID', $productID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $product = $productModel->findOrFail($productID);
        $productMetaTags = $product->metaTag()->first();

        return view('panel.config.transprodmetatags',
            [
                'prodMetaTagsTrans' => $prodMetaTagsTrans,
                'productID' => $productID,
                'languageID' => $languageID,
                'product' => $product,
                'productMetaTags' => $productMetaTags,
                'languageToTranslate' => $languageToTranslate,
                'type' => $platform
            ]
        );
    }

    /**
     * @param Request $request
     * @param $productID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveProductMetaTagsTranslation(Request $request, $productID, $languageID)
    {
        $type = $request->type;
        $prodMetaTagsTransModel = new ProdMetaTagsTrans();
        $productModel = new Product();
        if ($type == 'pct') {
            $prodMetaTagsTransModel->setConnection('mysql2');
            $productModel->setConnection('mysql2');
        }
        elseif ($type == 'pctcom') {
            $prodMetaTagsTransModel->setConnection('mysql3');
            $productModel->setConnection('mysql3');
        }
        elseif ($type == 'ctp') {
            $prodMetaTagsTransModel->setConnection('mysql4');
            $productModel->setConnection('mysql4');
        }
        $prodMetaTagsTrans = $prodMetaTagsTransModel->where('productID', $productID)->where('languageID', $languageID)->first();
        $product = $productModel->findOrFail($productID);
        if (!$prodMetaTagsTrans) {
            $prodMetaTagsTrans = new ProdMetaTagsTrans();
            if ($type == 'pct') {
                $prodMetaTagsTrans->setConnection('mysql2');
            }
            elseif ($type == 'pctcom') {
                $prodMetaTagsTrans->setConnection('mysql3');
            }
            elseif ($type == 'ctp') {
                $prodMetaTagsTrans->setConnection('mysql4');
            }
            $prodMetaTagsTrans->productID = $productID;
            $prodMetaTagsTrans->languageID = $languageID;
        }
        $prodMetaTagsTrans->title = $request->title;
        $prodMetaTagsTrans->description = $request->description;
        $prodMetaTagsTrans->keywords = $request->keywords;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Product Meta Tags Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Product Meta Tags';
        $adminLog->productRefCode = $product->referenceCode;
        $adminLog->details = $user->name. ' translated product meta tags to '. $language;
        $adminLog->tableName = 'product_meta_tags_translations';
        if ($prodMetaTagsTrans->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=',$url)) > 1 ? explode('=',$url)[1] : 1;
            return redirect('/general-config/product-meta-tags-translations/?page='.$pageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pageMetaTagsTranslations()
    {
        $pages = Page::all();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.pagemetatagstrans', ['pages' => $pages, 'languages' => $languages, 'type' => 'cz']);
    }


      public function pageMetaTagsTranslationsPCT()
    {
        $pages = Page::on('mysql2')->get();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.pagemetatagstrans', ['pages' => $pages, 'languages' => $languages, 'type' => 'pct']);
    }


      public function pageMetaTagsTranslationsPCTcom()
    {
        $pages = Page::on('mysql3')->get();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.pagemetatagstrans', ['pages' => $pages, 'languages' => $languages, 'type' => 'pctcom']);
    }


      public function pageMetaTagsTranslationsCTP()
    {
        $pages = Page::on('mysql4')->get();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.pagemetatagstrans', ['pages' => $pages, 'languages' => $languages, 'type' => 'ctp']);
    }

    /**
     * @param $pageID
     * @param $languageID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translatePageMetaTags($pageID, $languageID, Request $request)
    {
        $platform = $request->platform;
        $languageToTranslate = Language::findOrFail($languageID);
       
        

        $pageMetaTransModel = new PageMetaTagsTrans();
        $pageModel = new Page();


           if ($platform == 'pct') {
            $pageMetaTransModel->setConnection('mysql2');
            //$pageModel->setConnection('mysql2');
        }
        elseif ($platform == 'pctcom') {
            $pageMetaTransModel->setConnection('mysql3');
            //$pageModel->setConnection('mysql3');
        }
        elseif ($platform == 'ctp') {
            $pageMetaTransModel->setConnection('mysql4');
            //$pageModel->setConnection('mysql4');
        }


        $pageMetaTagsTrans = $pageMetaTransModel->where('pageID', $pageID)->where('languageID', $languageID)->first();
        $page = $pageModel::findOrFail($pageID);

        return view('panel.config.transpagemetatags',
            [
                'pageMetaTagsTrans' => $pageMetaTagsTrans,
                'pageID' => $pageID,
                'languageID' => $languageID,
                'page' => $page,
                'languageToTranslate' => $languageToTranslate,
                'type' => $platform
            ]
        );
    }

    /**
     * @param Request $request
     * @param $pageID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function savePageMetaTagsTranslation(Request $request, $pageID, $languageID)
    {
        $platform = $request->type; 
        


       $pageMetaTagsTransModel = new PageMetaTagsTrans();
       $pageModel = new Page();

          if ($platform == 'pct') {
            $pageMetaTagsTransModel->setConnection('mysql2');
            //$pageModel->setConnection('mysql2');
        }
        elseif ($platform == 'pctcom') {
            $pageMetaTagsTransModel->setConnection('mysql3');
            //$pageModel->setConnection('mysql3');
        }
        elseif ($platform == 'ctp') {
            $pageMetaTagsTransModel->setConnection('mysql4');
            //$pageModel->setConnection('mysql4');
        }
       
        $page = $pageModel::findOrFail($pageID);
        $pageMetaTagsTrans = $pageMetaTagsTransModel->where('pageID', $pageID)->where('languageID', $languageID)->first();


        if (!$pageMetaTagsTrans) {
            $pageMetaTagsTrans = new PageMetaTagsTrans();


             if ($platform == 'pct') {
            $pageMetaTagsTrans->setConnection('mysql2');
            
        }
        elseif ($platform == 'pctcom') {
            $pageMetaTagsTrans->setConnection('mysql3');
           
        }
        elseif ($platform == 'ctp') {
            $pageMetaTagsTrans->setConnection('mysql4');
           
        }
         


            $pageMetaTagsTrans->pageID = $pageID;
            $pageMetaTagsTrans->languageID = $languageID;
        }
        $pageMetaTagsTrans->title = $request->title;
        $pageMetaTagsTrans->description = $request->description;
        $pageMetaTagsTrans->keywords = $request->keywords;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Page Meta Tags Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Page Meta Tags';
        $adminLog->details = $user->name. ' translated '.$page->name.' meta tags to '. $language;
        $adminLog->tableName = 'page_meta_tags_translations';
        if ($pageMetaTagsTrans->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $dtPageID = count(explode('=',$url)) > 1 ? explode('=',$url)[1] : 1;


             if ($platform == 'pct') {
                return redirect('/general-config/page-meta-tags-translations-pct?page='.$dtPageID);
            }

            elseif ($platform == 'pctcom') {
                return redirect('/general-config/page-meta-tags-translations-pctcom?page='.$dtPageID);
            }

            elseif ($platform == 'ctp') {
                return redirect('/general-config/page-meta-tags-translations-ctp?page='.$dtPageID);
            }


            return redirect('/general-config/page-meta-tags-translations?page='.$dtPageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function categoryTranslations()
    {
        $category = Category::all();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.categorytranslations', ['category' => $category, 'languages' => $languages]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function attractionTranslations()
    {
        $attraction = Attraction::all();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.attractiontranslations', ['attraction' => $attraction, 'languages' => $languages, 'type' => 'cz']);
    }

      public function attractionTranslationsPCT()
    {
        $attraction = Attraction::on('mysql2')->get();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.attractiontranslations', ['attraction' => $attraction, 'languages' => $languages, 'type' => 'pct']);
    }

      public function attractionTranslationsPCTcom()
    {
        $attraction = Attraction::on('mysql3')->get();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.attractiontranslations', ['attraction' => $attraction, 'languages' => $languages, 'type' => 'pctcom']);
    }

      public function attractionTranslationsCTP()
    {
        $attraction = Attraction::on('mysql4')->get();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.attractiontranslations', ['attraction' => $attraction, 'languages' => $languages, 'type' => 'ctp']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function routeTranslations()
    {
        $routes = Route::all();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.routetranslations', ['routes' => $routes, 'languages' => $languages]);
    }

    /**
     * @param $routeID
     * @param $languageID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateRoute($routeID, $languageID)
    {
        $routeLocalization = RouteLocalization::where('routeID', $routeID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $route = Route::findOrFail($routeID);

        return view('panel.config.translateroute',
            [
                'routeLocalization' => $routeLocalization,
                'routeID' => $routeID,
                'languageID' => $languageID,
                'route' => $route,
                'languageToTranslate' => $languageToTranslate
            ]
        );
    }

    /**
     * @param Request $request
     * @param $routeID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveRouteTranslation(Request $request, $routeID, $languageID)
    {
        $slugify = new Slugify();
        $routeLocalization = RouteLocalization::where('routeID', $routeID)->where('languageID', $languageID)->first();
        if (!$routeLocalization) {
            $routeLocalization = new RouteLocalization();
            $routeLocalization->routeID = $routeID;
            $routeLocalization->languageID = $languageID;
        }
        $routeLocalization->route = $slugify->slugify($request->route);
        if ($routeLocalization->save()) {
            $adminLog = new Adminlog();
            $language = Language::findOrFail($languageID)->name;
            $user = Auth::guard('admin')->user();
            $adminLog->userID = $user->id;
            $adminLog->page = 'Route Translation';
            $adminLog->url = $request->fullUrl();
            $adminLog->action = 'Translated Route';
            $adminLog->details = $user->name. ' translated route which id is' .$routeID. ' to '. $language;
            $adminLog->tableName = 'route_translations';
            $adminLog->result = 'successful';
            $adminLog->save();
            $attractionCount = Attraction::count();
            $attractionTranslationOfThatLanguage = AttractionTranslation::where('languageID', $languageID)->count();
            $routeCount = Route::count();
            $routeLocalizationOfThatLanguage = RouteLocalization::where('languageID', $languageID)->count();
            $language = Language::findOrFail($languageID);
            if ($attractionCount == $attractionTranslationOfThatLanguage && $routeCount == $routeLocalizationOfThatLanguage) {
                $language->isActive = 1;
            } else {
                $language->isActive = 0;
            }
            $language->save();
        }
        $url = $_SERVER['HTTP_REFERER'];
        $pageID = count(explode('=',$url)) > 1 ? explode('=',$url)[1] : 1;
        return redirect('/general-config/route-translations?page='.$pageID);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogTranslations()
    {
        $blogs = BlogPost::all();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogtranslations', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'cz']);
    }

    /**
     * Blog translations page for pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogTranslationsPCT()
    {
        $blogs = BlogPost::on('mysql2')->get();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogtranslations', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'pct']);
    }

    /**
     * Blog translations page for pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogTranslationsPCTcom()
    {
        $blogs = BlogPost::on('mysql3')->get();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogtranslations', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'pctcom']);
    }

    /**
     * Blog translations page for pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogTranslationsCTP()
    {
        $blogs = BlogPost::on('mysql4')->get();
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogtranslations', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'ctp']);
    }

    /**
     * @param $blogID
     * @param $languageID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateBlog($blogID, $languageID, Request $request)
    {
        $platform = $request->platform;
        $blogTranslationModel = new BlogTranslation();
        $blogModel = new BlogPost();
        if ($platform == 'pct') {
            $blogTranslationModel->setConnection('mysql2');
            $blogModel->setConnection('mysql2');
        }
        elseif ($platform == 'pctcom') {
            $blogTranslationModel->setConnection('mysql3');
            $blogModel->setConnection('mysql3');
        }
        elseif ($platform == 'ctp') {
            $blogTranslationModel->setConnection('mysql4');
            $blogModel->setConnection('mysql4');
        }
        $blogTranslation = $blogTranslationModel->where('blogID', $blogID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $blog = $blogModel->findOrFail($blogID);

        return view('panel.config.translateblog',
            [
                'blogTranslation' => $blogTranslation,
                'languageToTranslate' => $languageToTranslate,
                'blog' => $blog,
                'blogID' => $blogID,
                'languageID' => $languageID,
                'type' => $platform
            ]
        );
    }

    /**
     * @param Request $request
     * @param $blogID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveBlogTranslation(Request $request, $blogID, $languageID)
    {
        $categoryID = $request->categoryEnglish;

        $type = $request->type;
        $slugify = new Slugify();
        $blogTranslationModel = new BlogTranslation();
        if ($type == 'pct') {
            $blogTranslationModel->setConnection('mysql2');
        }
        elseif ($type == 'pctcom') {
            $blogTranslationModel->setConnection('mysql3');
        }
        elseif ($type == 'ctp') {
            $blogTranslationModel->setConnection('mysql4');
        }
        $blogTranslation = $blogTranslationModel->where('blogID', $blogID)->where('languageID', $languageID)->first();
        if (!$blogTranslation) {
            $blogTranslation = new BlogTranslation();
            if ($type == 'pct') {
                $blogTranslation->setConnection('mysql2');
            }
            elseif ($type == 'pctcom') {
                $blogTranslation->setConnection('mysql3');
            }
            elseif ($type == 'ctp') {
                $blogTranslation->setConnection('mysql4');
            }
            $blogTranslation->blogID = $blogID;
            $blogTranslation->languageID = $languageID;
        }
        $adminLog = new AdminLog();
        $user = Auth::guard('admin')->user();
        $language = Language::findOrFail($languageID)->name;
        $adminLog->userID = $user->id;
        $adminLog->page = 'Blog Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Blog';
        $adminLog->details = $user->name . ' translated blog to' . $language;
        $adminLog->tableName = 'blog_translations';
        $blogTranslation->title = $request->title;
        $blogTranslation->postContent = $request->postContent;

       

         $categorTranslation = CategoryTranslation::where("categoryID",$categoryID)->where("languageID", $languageID)->first();

        
       


        $blogTranslation->category = $slugify->slugify($categorTranslation->categoryName);
        $slug = $slugify->slugify($request->title);
        $blogTranslation->url = '/' . $blogTranslation->category . '/' . $slug;
        if ($blogTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            if ($type == 'pct') {
                return redirect('/general-config/blog-translations-pct');
            }

            elseif ($type == 'pctcom') {
                return redirect('/general-config/blog-translations-pctcom');
            }

            elseif ($type == 'ctp') {
                return redirect('/general-config/blog-translations-ctp');
            }

            return redirect('/general-config/blog-translations');
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogMetaTagsTranslations()
    {
        $metaTags = MetaTag::all();
        $blogs = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->blogPost()->first())) {
                array_push($blogs, $metaTag->blogPost()->first());
            }
        }
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogmetatagstrans', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'cz']);
    }

    /**
     * Blog meta tags translations page for pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogMetaTagsTranslationsPCT()
    {
        $metaTags = MetaTag::on('mysql2')->get();
        $blogs = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->blogPost()->first())) {
                array_push($blogs, $metaTag->blogPost()->first());
            }
        }
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogmetatagstrans', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'pct']);
    }

    /**
     * Blog meta tags translations page for pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogMetaTagsTranslationsPCTcom()
    {
        $metaTags = MetaTag::on('mysql3')->get();
        $blogs = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->blogPost()->first())) {
                array_push($blogs, $metaTag->blogPost()->first());
            }
        }
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogmetatagstrans', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'pctcom']);
    }

    /**
     * Blog meta tags translations page for citytours.paris
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blogMetaTagsTranslationsCTP()
    {
        $metaTags = MetaTag::on('mysql4')->get();
        $blogs = [];
        foreach ($metaTags as $metaTag) {
            if (!is_null($metaTag->blogPost()->first())) {
                array_push($blogs, $metaTag->blogPost()->first());
            }
        }
        $languages = Language::where('name', '!=', 'English')->get();

        return view('panel.config.blogmetatagstrans', ['blogs' => $blogs, 'languages' => $languages, 'type' => 'ctp']);
    }

    /**
     * @param $blogID
     * @param $languageID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateBlogMetaTags($blogID, $languageID, Request $request)
    {
        $platform = $request->platform;
        $blogMetaTagsTransModel = new BlogMetaTagsTrans();
        $blogModel = new BlogPost();
        if ($platform == 'pct') {
            $blogMetaTagsTransModel->setConnection('mysql2');
            $blogModel->setConnection('mysql2');
        }
        elseif ($platform == 'pctcom') {
            $blogMetaTagsTransModel->setConnection('mysql3');
            $blogModel->setConnection('mysql3');
        }
        elseif ($platform == 'ctp') {
            $blogMetaTagsTransModel->setConnection('mysql4');
            $blogModel->setConnection('mysql4');
        }
        $blogMetaTagsTrans = $blogMetaTagsTransModel->where('blogID', $blogID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $blog = $blogModel->findOrFail($blogID);
        $blogMetaTags = $blog->metaTag()->first();

        return view('panel.config.transblogmetatags',
            [
                'blogMetaTagsTrans' => $blogMetaTagsTrans,
                'blogID' => $blogID,
                'languageID' => $languageID,
                'blog' => $blog,
                'blogMetaTags' => $blogMetaTags,
                'languageToTranslate' => $languageToTranslate,
                'type' => $platform
            ]
        );
    }

    /**
     * @param Request $request
     * @param $blogID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveBlogMetaTagsTranslation(Request $request, $blogID, $languageID)
    {
        $type = $request->type;
        $blogMetaTagsTransModel = new BlogMetaTagsTrans();
        if ($type == 'pct') {
            $blogMetaTagsTransModel->setConnection('mysql2');
        }
        elseif ($type == 'pctcom') {
            $blogMetaTagsTransModel->setConnection('mysql3');
        }
        elseif ($type == 'ctp') {
            $blogMetaTagsTransModel->setConnection('mysql4');
        }
        $blogMetaTagsTrans = $blogMetaTagsTransModel->where('blogID', $blogID)->where('languageID', $languageID)->first();
        if (!$blogMetaTagsTrans) {
            $blogMetaTagsTrans = new BlogMetaTagsTrans();
            if ($type == 'pct') {
                $blogMetaTagsTrans->setConnection('mysql2');
            }
            elseif ($type == 'pctcom') {
                $blogMetaTagsTrans->setConnection('mysql3');
            }
            elseif ($type == 'ctp') {
                $blogMetaTagsTrans->setConnection('mysql4');
            }
            $blogMetaTagsTrans->blogID = $blogID;
            $blogMetaTagsTrans->languageID = $languageID;
        }
        $blogMetaTagsTrans->title = $request->title;
        $blogMetaTagsTrans->description = $request->description;
        $blogMetaTagsTrans->keywords = $request->keywords;
        $adminLog = new AdminLog();
        $user = Auth::guard('admin')->user();
        $language = Language::findOrFail($languageID)->name;
        $adminLog->userID = $user->id;
        $adminLog->page = 'Blog Meta Tags Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Blog Meta Tags';
        $adminLog->details = $user->name . ' translated blog meta tags to' . $language;
        $adminLog->tableName = 'blog_meta_tags_translations';
        if ($blogMetaTagsTrans->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            if ($type == 'pct') {
                return redirect('/general-config/blog-meta-tags-translations-pct');
            }
            elseif ($type == 'pctcom') {
                return redirect('/general-config/blog-meta-tags-translations-pctcom');
            }
            elseif ($type == 'ctp') {
                return redirect('/general-config/blog-meta-tags-translations-ctp');
            }
            return redirect('/general-config/blog-meta-tags-translations');
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function countryTranslations()
    {
        $countries = Country::all();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.countrytranslations', ['countries' => $countries, 'languages' => $languages]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cityTranslations()
    {
        $cities = City::all();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.citytranslations', ['cities' => $cities, 'languages' => $languages]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function faqTranslations()
    {
        $faqs = FAQ::all();
        $languages = Language::where('name', '!=', 'English')->get();
        return view('panel.config.faqtranslations', ['faqs' => $faqs, 'languages' => $languages, 'type' => 'cz']);
    }

    /**
     * @param $countryID
     * @param $languageID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateCountry($countryID, $languageID, Request $request)
    {
        $countryTranslation = CountryTranslation::where('countryID', $countryID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $country = Country::findOrFail($countryID);
        return view('panel.config.translatecountry', [
            'countryTranslation' => $countryTranslation,
            'countryID' => $countryID,
            'languageID' => $languageID,
            'country' => $country,
            'languageToTranslate' => $languageToTranslate
        ]);
    }

    /**
     * @param $cityID
     * @param $languageID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateCity($cityID, $languageID, Request $request)
    {
        $cityTranslation = CityTranslation::where('cityID', $cityID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $city = City::findOrFail($cityID);
        return view('panel.config.translatecity', [
            'cityTranslation' => $cityTranslation,
            'cityID' => $cityID,
            'languageID' => $languageID,
            'city' => $city,
            'languageToTranslate' => $languageToTranslate
        ]);
    }

    /**
     * @param $cityID
     * @param $languageID
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function translateFAQ($faqID, $languageID, Request $request)
    {
        $faqTranslation = FAQTranslation::where('faqID', $faqID)->where('languageID', $languageID)->first();
        $languageToTranslate = Language::findOrFail($languageID);
        $faq = FAQ::findOrFail($faqID);
        return view('panel.config.translatefaq', [
            'faqTranslation' => $faqTranslation,
            'faqID' => $faqID,
            'languageID' => $languageID,
            'faq' => $faq,
            'languageToTranslate' => $languageToTranslate
        ]);
    }

    /**
     * @param Request $request
     * @param $countryID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveCountryTranslation(Request $request, $countryID, $languageID)
    {
        $countryTranslation = CountryTranslation::where('countryID', $countryID)->where('languageID', $languageID)->first();
        if (!$countryTranslation) {
            $countryTranslation = new CountryTranslation();
            $countryTranslation->countryID = $countryID;
            $countryTranslation->languageID = $languageID;
        }
        $countryTranslation->countries_name = $request->countries_name;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Country Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated Country';
        $adminLog->details = $user->name. ' translated country to '. $language;
        $adminLog->tableName = 'country_translations';
        if ($countryTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;
            return redirect('/general-config/country-translations?page='.$pageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @param Request $request
     * @param $cityID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveCityTranslation(Request $request, $cityID, $languageID)
    {
        $cityTranslation = CityTranslation::where('cityID', $cityID)->where('languageID', $languageID)->first();
        if (!$cityTranslation) {
            $cityTranslation = new CityTranslation();
            $cityTranslation->cityID = $cityID;
            $cityTranslation->languageID = $languageID;
        }
        $cityTranslation->name = $request->name;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'City Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated City';
        $adminLog->details = $user->name. ' translated city to '. $language;
        $adminLog->tableName = 'city_translations';
        if ($cityTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;
            return redirect('/general-config/city-translations?page='.$pageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * @param Request $request
     * @param $cityID
     * @param $languageID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveFaqTranslation(Request $request, $faqID, $languageID)
    {
        $faqTranslation = FAQTranslation::where('faqID', $faqID)->where('languageID', $languageID)->first();
        if (!$faqTranslation) {
            $faqTranslation = new FAQTranslation();
            $faqTranslation->faqID = $faqID;
            $faqTranslation->languageID = $languageID;
        }
        $faqTranslation->question = $request->question;
        $faqTranslation->answer = $request->answer;
        $adminLog = new Adminlog();
        $language = Language::findOrFail($languageID)->name;
        $user = Auth::guard('admin')->user();
        $adminLog->userID = $user->id;
        $adminLog->page = 'FAQ Translation';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Translated FAQ';
        $adminLog->details = $user->name. ' translated faq to '. $language;
        $adminLog->tableName = 'faq_translations';
        if ($faqTranslation->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            $url = $_SERVER['HTTP_REFERER'];
            $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;
            return redirect('/general-config/faq-translations?page='.$pageID);
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }
    }

    /**
     * Updates banner image for homepage
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateHomeBanner()
    {
        $config = Config::where('userID', -1)->first();
        $homeBanner = $config->homeBanner;
        return view('panel.config.updatehomebanner', ['homeBanner' => $homeBanner]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postHomeBanner(Request $request)
    {
        $file = $request->file('homeBanner');
        $config = Config::where('userID', -1)->first();
        $config->homeBanner = $file->getClientOriginalName();
        if ($config->save()) {
            Storage::disk('s3')->put('website-images/' . $file->getClientOriginalName(), file_get_contents($file));
        }
        return redirect('general-config/update-home-banner');
    }

    /**
     * Updates banner image for pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateHomeBannerPCT()
    {
        $config = Config::on('mysql2')->where('userID', -1)->first();
        $homeBanner = $config->homeBanner;
        return view('panel.config.updatehomebannerpct', ['homeBanner' => $homeBanner]);
    }

    /**
     * Updates banner image for pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateHomeBannerPCTcom()
    {
        $config = Config::on('mysql3')->where('userID', -1)->first();
        $homeBanner = $config->homeBanner;
        return view('panel.config.updatehomebannerpct', ['homeBanner' => $homeBanner]);
    }

    /**
     * Updates banner image for citytours.paris
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateHomeBannerCTP()
    {
        $config = Config::on('mysql4')->where('userID', -1)->first();
        $homeBanner = $config->homeBanner;
        return view('panel.config.updatehomebanner', ['homeBanner' => $homeBanner]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postHomeBannerPCT(Request $request)
    {
        $file = $request->file('homeBanner');
        $config = Config::on('mysql2')->where('userID', -1)->first();
        $config->homeBanner = $file->getClientOriginalName();
        if ($config->save()) {
            Storage::disk('s3')->put('website-images/' . $file->getClientOriginalName(), file_get_contents($file));
        }
        return redirect('general-config/update-home-banner-pct');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postHomeBannerPCTcom(Request $request)
    {
        $file = $request->file('homeBanner');
        $config = Config::on('mysql3')->where('userID', -1)->first();
        $config->homeBanner = $file->getClientOriginalName();
        if ($config->save()) {
            Storage::disk('s3')->put('website-images/' . $file->getClientOriginalName(), file_get_contents($file));
        }
        return redirect('general-config/update-home-banner-pctcom');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postHomeBannerCTP(Request $request)
    {
        $file = $request->file('homeBanner');
        $config = Config::on('mysql4')->where('userID', -1)->first();
        $config->homeBanner = $file->getClientOriginalName();
        if ($config->save()) {
            Storage::disk('s3')->put('website-images/' . $file->getClientOriginalName(), file_get_contents($file));
        }
        return redirect('general-config/update-home-banner-ctp');
    }

}
