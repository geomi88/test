<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Usermodule;
use DB;

class HrController extends Controller {

    var $employees;

    

    public function index() {
        $employee_details = Employee::where('id', Session::get('login_id'))->first();
        $user_sub_modules = Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->whereRaw("modules.parent_id in (select id from modules where name='Hr')")->get();
        return view('hr/index', array('employee_details' => $employee_details, 'user_sub_modules' => $user_sub_modules));
    }

    
}
