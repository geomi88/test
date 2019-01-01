<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Agents;

use DB;

class AuthController extends Controller {

   

    public function __construct() {
       
    }

    public function index() {
       
        return view('admin/login/login', array('title' => '', 'description' => '', 'page' => ''));
    }

    public function login(Request $request) {
       try{
        $data = Input::all();

        $userdata = array(
            'email' => Input::get('username'),
            'password' => Input::get('password'),
            'status' => '1'
        );
       
        if (Auth::validate($userdata)) {
            
            if (Auth::attempt($userdata)) {
                $user_id = Auth::user()->id;
               
                //$user = Auth::user();
                //$user_id = Auth::id($user);   
                $admin_data = DB::table('admin')
                        ->select('admin.*')
                       // ->where(['username' => Input::get('username'),'status' => '1','role'=>'1'])->first();
                        ->where(['email' => Input::get('username'),'status' => '1','role'=>'1'])->first();
             
                //Session::set('login_id', $admin_data->id);
                //Session::set('adminLogin', true);
                
                session(['login_id' => $admin_data->id]);
                session(['adminLogin' => true]);
                session(['agentLogin' => false]); session(['adminUser' => $admin_data]);
               
               // Toastr::success('Welcome!  '. $admin_data->username, $title = null, $options = []);
//                if ($employee_data->privilege_status == 1) {
//                    
//                    return Redirect::intended('dashboard/managementconsole');
//                }else{
                    return Redirect::to('admin/dashboard');
//                }
            }
        } else {
           
            // if any error send back with message.
           Toastr::error('Invalid Username or Password!', $title = null, $options = []);
          return Redirect::to('admin/login');
        }
       }  catch (Exception $ex){
            Toastr::error('Something went wrong!', $title = null, $options = []);
          return Redirect::to('admin/login');
           
       }
    }

    public function agentIndex() {
       
        return view('agent/login/login', array('title' => '', 'description' => '', 'page' => ''));
    }
    
    public function agentLogin(Request $request) {
        try {
            $data = Input::all();

            $userpost = array(
                'email' => Input::get('username'),
                'status' => '1',
                'user_type' => '1'
            );

           
            $agents = new Agents;
            $queryUser = $agents::where($userpost);
            $checkuserCount = $queryUser->count();
            if ($checkuserCount > 0) {
                $user_data = $queryUser->first();
                $password = Input::get('password');
                if (Hash::check($password, $user_data->password)) {
                    $user_id = $user_data->id;
                    $request->session()->put('adminLogin', false);
                    $request->session()->put('agentLogin', true);
                    $request->session()->put('agentUser', $user_data);
                    $request->session()->put('agentId', $user_id);

                    Toastr::success('Successfully Logged in!', $title = null, $options = []);
                    return Redirect::to('agent/dashboard');
                } else {
                    Toastr::error('Invalid User!', $title = null, $options = []);
                    return Redirect::to('agent/login');
                }
            } else {
                Toastr::error('Invalid User!', $title = null, $options = []);
                return Redirect::to('agent/login');
            }
        } catch (Exception $ex) {

            Toastr::error('Something went wrong!', $title = null, $options = []);
            return Redirect::to('agent/login');
        }
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect::to('admin/login');
    }


    public function ownerIndex() {
       
        return view('owner/login/login', array('title' => '', 'description' => '', 'page' => ''));
    }
    
    public function ownerLogin(Request $request) {
        try {
            $data = Input::all();

            $userpost = array(
                'email' => Input::get('username'),
                'status' => '1',
                'user_type' => '2'
            );

           
            $agents = new Agents;
            $queryUser = $agents::where($userpost);
            $checkuserCount = $queryUser->count();
            if ($checkuserCount > 0) {
                $user_data = $queryUser->first();
                $password = Input::get('password');
                if (Hash::check($password, $user_data->password)) {
                    $user_id = $user_data->id;
                    $request->session()->put('adminLogin', false);
                    $request->session()->put('agentLogin', false);
                    $request->session()->put('ownerLogin', true);
                    $request->session()->put('ownerUser', $user_data);
                    $request->session()->put('ownerId', $user_id);

                    Toastr::success('Successfully Logged in!', $title = null, $options = []);
                    return Redirect::to('owner/property-listing');
                } else {
                    Toastr::error('Invalid User!', $title = null, $options = []);
                    return Redirect::to('owner/login');
                }
            } else {
                Toastr::error('Invalid User!', $title = null, $options = []);
                return Redirect::to('owner/login');
            }
        } catch (Exception $ex) {

            Toastr::error('Something went wrong!', $title = null, $options = []);
            return Redirect::to('owner/login');
        }
    }
}
