<?php

namespace App\Http\Middleware;

use Closure;
use App\GuideToken;


class GuideCheck
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
        $token = $this->getToken($request);
        //return response()->json(GuideToken::where('token', $token)->exists());

        if(!$token) return response()->json(['status' => 'error', 'message' => 'invalidToken']);
        if(strlen($token) !== 64) return response()->json(['statusCode'=>400, 'status' => 'error', 'error' => [ 'message' => 'invalidToken' ]], 400);

        if(!(GuideToken::where('token', $token)->whereDate('until_validdate', '>=', date('Y-m-d'))->exists())) return response()->json(['statusCode'=>400,  'status' => 'error', 'error' => [ 'message' => 'invalidToken']], 400);


        return $next($request);
    }



       protected function getToken($request){
        if(response()->json(!empty($request->header('Authorization')) && count(explode(' ', $request->header('Authorization'))) == 2 )){
        return explode(' ', $request->header('Authorization'))[1] ?? 0;
        }
        return false;
    }
}
