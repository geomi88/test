<?php

namespace App\Http\Controllers\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;

class IndexController extends Controller {

    public function index() {
        $employee_details = Employee::where('id', Session::get('login_id'))->first();
        
        $loggedin_details = DB::table('employees')
                ->select('employees.*','master_resources.name as job_position')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', Session::get('login_id'))
                ->first();
        $user_sub_modules = Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->whereRaw("modules.parent_id in (select id from modules where name='Branches')")->get();
        
        return view('branch/index', array('employee_details' => $employee_details,'loggedin_details' => $loggedin_details, 'user_sub_modules' => $user_sub_modules));
        
    }

}
