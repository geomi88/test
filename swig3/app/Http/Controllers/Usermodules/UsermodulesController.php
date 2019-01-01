<?php

namespace App\Http\Controllers\Usermodules;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Module;
use App\Models\Usermodule;
use DB;
use Mail;

class UsermodulesController extends Controller {

    public function index() {
        
    }

    public function add() {
        
    }

    public function store() {
        try {
              DB::table('user_modules')->where('employee_id', Session::get('employee_id'))->delete();
            $usermodulemodel = new Usermodule;

            $previlege_data = Input::all();
            if (isset($previlege_data['modules'])) {
                foreach ($previlege_data['modules'] as $module) {
                    $module_previlege['module_id'] = $module;
                    $module_previlege['employee_id'] = Session::get('employee_id');
                    $result = Usermodule::create($module_previlege);
                }
            }
            if (isset($previlege_data['sub_modules'])) {
                foreach ($previlege_data['sub_modules'] as $sub_module) {
                    
                    $module_details = DB::table('modules')
                            ->select('modules.*')
                            ->where(['id' => $sub_module])->first();
                    $module_id = $module_details->parent_id;
                    
                    $module_permission_status = DB::table('user_modules')
                            ->select('user_modules.*')
                            ->where(['module_id' => $module_id,'employee_id' => Session::get('employee_id')])->first();
                    if(count($module_permission_status)<1)
                    {
                      $sub_module_previlege['module_id'] = $module_id;
                      $sub_module_previlege['employee_id'] = Session::get('employee_id');
                      $result = Usermodule::create($sub_module_previlege);  
                    }
                    $sub_module_previlege['module_id'] = $sub_module;
                    $sub_module_previlege['employee_id'] = Session::get('employee_id');
                    $result = Usermodule::create($sub_module_previlege);
                    
                    $create_module_details = DB::table('modules')
                            ->select('modules.*')
                            ->where(['parent_id' => $sub_module])->first();
                    if(count($create_module_details)>0)
                    {
                    $create_module_id = $create_module_details->id;
                    $sub_module_previlege['module_id'] = $create_module_id;
                    $sub_module_previlege['employee_id'] = Session::get('employee_id');
                    $result = Usermodule::create($sub_module_previlege);
                    }
                }
            }

            $employee_details = DB::table('employees')
                            ->select('employees.*')
                            ->where(['id' => Session::get('employee_id')])->first();
            $username = $employee_details->username;
            $password = $employee_details->mobile_number;
            $email = $employee_details->email;
            if($email == NULL || $email == '')
            {
                $email = $employee_details->contact_email;
            }
            $employee_name = $employee_details->first_name." ".$employee_details->middle_name." ".$employee_details->last_name;
//            Mail::send('emailtemplates.employee_reg', ['email' => $email, 'username' => $username, 'password' => $password, 'employee_name' => $employee_name], function($message)use ($email) {
//                $message->to($email)->subject('Employee Registration');
//            });
//            Toastr::success('Employee Successfully Created!', $title = null, $options = []);
//            return Redirect::to('employee');
        } 
        catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
//            return Redirect::to('employee');
        }
    }
    
    public function update() {
        try {
            DB::table('user_modules')->where('employee_id', Session::get('employee_id'))->delete();
            $usermodulemodel = new Usermodule;

            $previlege_data = Input::all();
            if (isset($previlege_data['modules'])) {
                foreach ($previlege_data['modules'] as $module) {
                    $module_previlege['module_id'] = $module;
                    $module_previlege['employee_id'] = Session::get('employee_id');
                    $result = Usermodule::create($module_previlege);
                }
            }
            if (isset($previlege_data['sub_modules'])) {
                foreach ($previlege_data['sub_modules'] as $sub_module) {
                    
                    $module_details = DB::table('modules')
                            ->select('modules.*')
                            ->where(['id' => $sub_module])->first();
                    $module_id = $module_details->parent_id;
                    
                    $module_permission_status = DB::table('user_modules')
                            ->select('user_modules.*')
                            ->where(['module_id' => $module_id,'employee_id' => Session::get('employee_id')])->first();
                    if(count($module_permission_status)<1)
                    {
                      $sub_module_previlege['module_id'] = $module_id;
                      $sub_module_previlege['employee_id'] = Session::get('employee_id');
                      $result = Usermodule::create($sub_module_previlege);  
                    }
                    
                    
                    $sub_module_previlege['module_id'] = $sub_module;
                    $sub_module_previlege['employee_id'] = Session::get('employee_id');
                    $result = Usermodule::create($sub_module_previlege);
                    
                    $create_module_details = DB::table('modules')
                            ->select('modules.*')
                            ->where(['parent_id' => $sub_module])->first();
                    if(count($create_module_details)>0)
                    {
                    $create_module_id = $create_module_details->id;
                    $sub_module_previlege['module_id'] = $create_module_id;
                    $sub_module_previlege['employee_id'] = Session::get('employee_id');
                    $result = Usermodule::create($sub_module_previlege);
                    }
                }
            }

            
            Toastr::success('Employee Details Successfully Updated!', $title = null, $options = []);
            return Redirect::to('employee');
        } 
        catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('employee');
        }
    }

}
