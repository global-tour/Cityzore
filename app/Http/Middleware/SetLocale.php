<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
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



        // Setting currency code here as well
        if (is_null(session()->get('currencyCode'))) {
            session()->put('currencyCode', 2);
            session()->put('currencyIcon', 'icon-cz-eur');
        }
        //
        App::setLocale(session()->get('userLanguage'));
        if (is_null(session()->get('userLanguage'))) {
            App::setLocale('en');
        }
        return $next($request);
    }
}
