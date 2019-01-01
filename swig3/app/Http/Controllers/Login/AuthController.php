<?php

namespace App\Http\Controllers\Login;

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
use DB;

class AuthController extends Controller {

    var $employees;

    public function __construct() {
        $this->employees = Employee::all();
    }

    public function index() {
        $companies = DB::table('companies')
                ->where('status', '=', 1)
                ->get();
        return view('login/index', array('title' => '', 'description' => '', 'page' => '', 'companies' => $companies));
    }

    public function login() {
        $data = Input::all();

        $userdata = array(
            'username' => Input::get('username'),
            'password' => Input::get('password'),
            'company' => Input::get('company'),
            'status' => 1
        );
        if (Auth::validate($userdata)) {

            if (Auth::attempt($userdata)) {
                $employee_data = DB::table('employees')
                        ->select('employees.*')
                        ->where(['username' => Input::get('username'),'status' => 1])->first();
                Session::set('login_id', $employee_data->id);
                Session::set('company', Input::get('company'));
                Toastr::success('Welcome!  '. $employee_data->first_name, $title = null, $options = []);
//                if ($employee_data->privilege_status == 1) {
//                    
//                    return Redirect::intended('dashboard/managementconsole');
//                }else{
                    return Redirect::intended('dashboard');
//                }
            }
        } else {
            // if any error send back with message.
            Toastr::error('Something is Wrong. Please check with the admin.', $title = null, $options = []);
            return Redirect::to('/');
        }
    }

    public function employees() {
        return view('employees', array('title' => 'Employees Listing', 'description' => '', 'page' => 'employees', 'employees' => $this->employees));
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect::to('/');
    }

}
