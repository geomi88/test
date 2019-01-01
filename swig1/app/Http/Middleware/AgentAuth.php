<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Session;
use Closure;

class AgentAuth
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
         if (!$request->session()->exists('agentLogin')) {
            return redirect('agent/login');
        }
        if ($request->session()->exists('agentLogin')=="") {
            return redirect('agent/login');
        }
        return $next($request);
    }
}
