<?php

namespace App\Http\Middleware;

use Closure;
use App\Language;

class RedirectLocale
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



        $langCodes = [];
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $languages = Language::select('code')->where('isActive', 1)->where('code', '!=', 'en')->get();

        foreach ($languages as $language) {
            array_push($langCodes, $language->code);
        }

        $segments = $request->segments();
        if (count($segments) == 0 && $langCode != 'en') {
            return redirect('/'.$langCode);
        }

        if (count($segments) > 0) {

            if($segments[0] == "home-attraction-tours"){
                return $next($request);
            }

            if (in_array($segments[0], $langCodes)) {
                session()->put('userLanguage', $segments[0]);
            } else {

                if(strpos(request()->headers->get('referer'), 'cityzore') === false){
                    session()->forget('userLanguage');
                    $langCode = "en";
                }

                if ($langCode != 'en') {
                    return redirect('/'.$langCode.'/'.$request->path());
                } else {
                    session()->put('userLanguage', 'en');
                }
            }
        }

        return $next($request);
    }
}
