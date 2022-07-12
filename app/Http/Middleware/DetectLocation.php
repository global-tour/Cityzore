<?php

namespace App\Http\Middleware;

use App\Currency;
use App\Language;
use Closure;
use Stevebauman\Location\Facades\Location;

class DetectLocation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (session()->exists('detectedLocation') && session()->get('detectedLocation')) {
            return $next($request);
        }

        if ($location = Location::get()) {

            $language   = explode(',', $request->server('HTTP_ACCEPT_LANGUAGE'));
            $code       = strtoupper($location->currency['code']);
            $currency   = Currency::where('isActive', 1)->where('currency', $code)->first();
            $langDetect = Language::where('isActive', 1)->where('code', strtolower($language[0]))->first();

            if (!$currency) {
                session()->put('currencyCode', 1);
                session()->put('currencyIcon', 'icon-cz-usd');
            }else{
                session()->put('currencyCode', $currency->id);
                session()->put('currencyIcon', $currency->iconClass);
            }

            session()->put('detectedLocation', false);

            if (!$langDetect) {
                session()->put('userLanguage', 'en');
            }else{
                session()->put('userLanguage', $langDetect->code);
                session()->put('detectedLocation', true);

                if ($langDetect->code != 'en') {
                        return redirect('/'. $langDetect->code . '/'. $request->path());
                }

                session()->put('userLanguage', 'en');
            }
        }

        return $next($request);
    }
}
