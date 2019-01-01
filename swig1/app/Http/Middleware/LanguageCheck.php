<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Session;
use Closure;
use App;

class LanguageCheck
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
         if (!$request->session()->get('lang')) {

            session(['locale' => 'en']);
            session(['langName' => 'English']);
            session(['lang' => 'en']);
	    $lang='en';
		
        }else{
	   $lang=$request->session()->get('lang');
        }
        App::setlocale($lang);
        return $next($request);
    }
}
