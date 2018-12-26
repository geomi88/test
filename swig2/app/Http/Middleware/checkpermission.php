<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Usermodule;
use App\Models\Module;
class checkpermission {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $employee_details = Employee::where('id', Session::get('login_id'))->first();
        $current_url = $request->getRequestUri();
        //$current_url = str_replace( '/mtg/public/', '',$current_url);
        $current_url = ltrim($current_url, '/');
        $check_module = Module::where('url', $current_url)->first();
        if(isset($check_module))
        {
        $user_sub_module = Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->whereRaw("modules.url = '$current_url'")->get();
        if ($employee_details->admin_status != 1) {
            if (count($user_sub_module) < 1) {
                return Redirect::to('forbidden');
            }
        }
        }
        
        return $next($request);
    }

}
