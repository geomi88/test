<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Model\Admin;

class LoginController extends Controller {
    /*
     * Loads Login view
     */

    protected $redirectTo = 'admin/dashboard';

    public function getLogin(Request $request) {
        try {
            $username = "admin";
            $password = "qwerty";
            //$2y$10$/GRlLaJaCGJ2OuZ9bDsy7uSeFMIgQtzYeilOUhhwjPqh/XhAbPQxG

            $userpost = array(
                'username' => $username, //Input::get('username'),
                'status' => '1'
            );
            $rule = [
                'username' => 'required',
                'password' => 'required',
            ];
            $messages = [
                'required' => 'Enter :attribute',
            ];
            /* $validator = Validator::make($request->all(), $rule, $messages);
              if ($validator->fails()) {
              Session::flash('flash-message', 'Enter the fields');
              return redirect()->back()->withErrors($validator);
              } */
            $flashmesage = 'Wrong Username';
            $admin = new Admin;
            $queryUser = $admin::where($userpost);
            $checkuserCount = $queryUser->count();
            if ($checkuserCount > 0) {
                $flashmesage = 'Wrong Password';
                $user_data = $queryUser->first();
                if (Hash::check($password, $user_data->password)) {
                    $user_id = $user_data->id;
                    $request->session()->put('admin_session', true);
                    $request->session()->put('admin_data', $user_data);
                    return redirect($this->redirectTo);
                } else {
                    $flashmesage = "Invalid Password";
                }
            }
            Session::flash('flash-message', $flashmesage);
            return Redirect::to('admin/login')->withInput();
        } catch (Exception $ex) {
            Session::flash('flash-message', "Sever Error");
            return Redirect::to('admin/login')->withInput();
        }
    }

    public function login() {
        $data = Input::all();

        $userdata = array(
            'username' => Input::get('username'),
            'password' => Input::get('password'),
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
            Toastr::error('Invalid Username or Password!', $title = null, $options = []);
            return Redirect::to('/');
        }
    }
}
