<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class checkAffiliate
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
      
       $serverName = $request->server('SERVER_NAME');
       $refererLink = $request->header('referer'); 
       $query = $request->query('affiliate'); 

         

       //if (!is_null($refererLink) && strpos($refererLink, $serverName) === false) {
        
          if(!is_null($query)){
           if($user = User::where('affiliate_unique', $query)->first()){
              session()->put('affiliate_user_id', $user->id);
                
             }
           }
           
      // }

     
           
      
       
        return $next($request);
    }
}
