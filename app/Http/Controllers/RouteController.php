<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Route;
use App\RouteLocalization;
use App\Language;
use App\AttractionTranslation;
use App\Attraction;


class RouteController extends Controller
{

    /**
     * One static parameter (without en routes)
     *
     * @param Request $request
     * @param $lang
     * @param $param
     * @return mixed
     */
    public function routeLocalization(Request $request, $lang, $param)
    {

        if (session()->get('userLanguage') != 'en') {
            $language = Language::where('code', session()->get('userLanguage'))->first();
            $routeLocalization = RouteLocalization::where('route', $param)->where('languageID', $language->id)->first();
            $route = Route::findOrFail($routeLocalization->routeID);
            $controller = new $route->controller();
            $function = $route->function;

            return $controller->$function($request);
        }
    }

    /**
     * One static parameter (only en routes)
     *
     * @param Request $request
     * @param $param
     * @return mixed
     */
    public function routeLocalizationForEnglish(Request $request, $param)
    {
        $route = Route::where('route', $param)->first();
        $controller = new $route->controller();
        $function = $route->function;

        return $controller->$function($request);
    }

    /**
     * One static one dynamic parameter (without en routes) for attraction
     *
     * @param Request $request
     * @param $lang
     * @param $staticParam
     * @param $dynamicParam
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function routeLocalizationForAttraction(Request $request, $lang, $staticParam, $dynamicParam)
    {

        $reqUriArray = explode('-', $dynamicParam);
        $countArray = count($reqUriArray);
        unset($reqUriArray[$countArray-1]);
        $reqUri = implode('-', $reqUriArray);



        $attractionId = preg_replace('/[^0-9]/', '', $dynamicParam);

        if (empty($attractionId))
        {

            $attraction = AttractionTranslation::where('slug', $dynamicParam)->first();

            $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
            $lang = \App\Language::where('code', $langCode)->first();
            $routeTrans = RouteLocalization::where('routeID', 27)->where('languageID', $lang->id)->first();

            if ($routeTrans)
            {
                $routeTrans = $routeTrans->route;

            }
            else
            {
                $routeTrans = 'attraction';
            }


            return redirect($lang->code.'/'.  $routeTrans.'/'.$attraction->slug.'-'.$attraction->attractionID, 301);
        }


        $attractionTranslation = AttractionTranslation::where('slug', $reqUri)->first();
        if ($attractionTranslation) {
            $attraction = Attraction::findOrFail($attractionTranslation->attractionID);
            $route = Route::where('route', 'attraction')->first();
            $controller = new $route->controller();
            $function = $route->function;
            return $controller->$function(session()->get('userLanguage'), $attractionTranslation->slug."-".$attraction->id);
        }

        $controller = new Product\ProductController();
        return $controller->getProduct($request, session()->get('userLanguage'), $staticParam, $dynamicParam);
    }

}

