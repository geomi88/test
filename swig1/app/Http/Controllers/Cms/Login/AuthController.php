<?php

namespace App\Http\Controllers\Cms\Login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use DB;

class AuthController extends Controller {

    public function login() {
        
        
        //Toastr::error('Invalid Username or Password!', $title = null, $options = []);
        return view('login');
    }

    public function logout() {
        
        Session::flush();
        Auth::logout();
        return Redirect::to('/');
    }

    public function checklogin() {
        
        if (Input::get('username') != ''){
        $username = Input::get('username');
        $password = Input::get('password');
        $admin = DB::table('admin')
                ->select('admin.*')
                ->where('admin.username', '=', $username)
                ->first();
       

        if (count($admin) > 0 && Hash::check(Input::get('password'),$admin->password)) {
            Session::put('user', 'asd');
            return Redirect::to('/dashboard');
        } else {
            Toastr::error('Invalid Username or Password!', $title = null, $options = []);
            return Redirect::to('/');
        }
        }
        //Toastr::error('Invalid Username or Password!', $title = null, $options = []);
        return view('login');
    }
}
