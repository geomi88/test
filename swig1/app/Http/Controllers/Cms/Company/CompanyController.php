<?php

namespace App\Http\Controllers\Cms\Company;

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
use App\Models\User;

class CompanyController extends Controller {

    public function index() {
        try {
            $paginate = 10;
            $companies = DB::table('users')
                            ->select('users.*')
                            ->where(['status' => 1, 'user_type' => 2])
                            ->orderby('created_at', 'DESC')->paginate($paginate);
            return view('company/index', array('companies' => $companies));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

    public function delete($id) {
        try {
            //$id = \Crypt::decrypt($id);
            $user = DB::table('users')
                    ->where(['id' => $id])
                    ->update(['status' => 0]);


            Toastr::success('Successfully Deleted the company', $title = null, $options = []);
            return Redirect::to('company');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('company');
        }
    }

    public function search() {
        $search_key = Input::get('search_key');
        if ($search_key != "") {
            //$users = User::where('firstName', 'LIKE', $search_key . '%')->orWhere('lastName', 'LIKE', $search_key . '%')->orWhere('userCode', 'LIKE', $search_key . '%')->orWhere('email', 'LIKE', $search_key . '%')->paginate(5)->setPath('');

            $users = DB::table('users')
                            ->select('*')
                            ->whereRaw("(companyName LIKE '$search_key%' or firstName LIKE '$search_key%' or lastName LIKE '$search_key%' or userCode LIKE '$search_key%' or email LIKE '$search_key%') and user_type=2")
                            ->paginate(10)->setPath('');
            $pagination = $users->appends(array(
                'search_key' => Input::get('search_key')
            ));
            if (count($users) > 0)
                return view('company/search')->withDetails($users)->withQuery($search_key);
        }
        return view('company/search')->withMessage('No Details found. Try to search again !')->withQuery($search_key);
    }

    public function edit($id) {

        $company_details = DB::table('users')
                ->where(['id' => $id])
                ->first();

        return view('company/edit', array('company_details' => $company_details));
    }

    public function checkemail() {
        $user_id = Input::get('user_id');
        $email = Input::get('email');
        if($user_id > 0)
        {
            $user_details = DB::table('users')
                ->whereRaw("email = '$email' and id != $user_id")
                ->first();
        }
        else
        {
            $user_details = DB::table('users')
                ->whereRaw("email = '$email'")
                ->first();
        }
        if (count($user_details) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkphone() {
        $user_id = Input::get('user_id');
        $phoneNumber = Input::get('phoneNumber');
        if($user_id > 0)
        {
            $user_details = DB::table('users')
                ->whereRaw("phoneNumber = '$phoneNumber' and id != $user_id")
                ->first();
        }
        else
        {
            $user_details = DB::table('users')
                ->whereRaw("phoneNumber = '$phoneNumber'")
                ->first();
        }
        if (count($user_details) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function update() {
        $company_id = Input::get('company_id');
        $firstName = Input::get('firstName');
        $lastName = Input::get('lastName');
        $email = Input::get('email');
        $phoneNumber = Input::get('phoneNumber');
        $phoneNumber = str_replace('+', '', $phoneNumber);
        $companyName = Input::get('companyName');
        $companyDescription = Input::get('companyDescription');
        
        $companyLogo = Input::file('companyLogo');
        if(isset($companyLogo)) {
        $filename = time() . $companyLogo->getClientOriginalName();
        $path = public_path() . '/uploads/company/';
        $companyLogo->move($path, $filename);
        $companyLogo = '/uploads/company/'.$filename;
        
        $update_company = DB::table('users')
                ->where(['id' => $company_id])
                ->update(['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'phoneNumber' => $phoneNumber, 'companyName' => $companyName, 'companyDescription' => $companyDescription, 'companyLogo' => $companyLogo]);

        }
        
        else {
        $update_company = DB::table('users')
                ->where(['id' => $company_id])
                ->update(['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'phoneNumber' => $phoneNumber, 'companyName' => $companyName, 'companyDescription' => $companyDescription]);
        }
        Toastr::success('Successfully updated the company', $title = null, $options = []);
        return Redirect::to("company/edit/$company_id");
    }

    public function add() {

        return view('company/add');
    }

    public function store() {
        $firstName = Input::get('firstName');
        $lastName = Input::get('lastName');
        $email = Input::get('email');
        $phoneNumber = Input::get('phoneNumber');
        $phoneNumber = str_replace('+', '', $phoneNumber);
        $password = Input::get('password');
        $companyName = Input::get('companyName');
        $companyDescription = Input::get('companyDescription');


        $companyLogo = Input::file('companyLogo');
        $filename = time() . $companyLogo->getClientOriginalName();
        $path = public_path() . '/uploads/company/';
        $companyLogo->move($path, $filename);
        $companyLogo = '/uploads/company/'.$filename;
        
        
        
        $tested = [];
            $unique = false;
            do {

                // Generate random string of characters
                $random = strtoupper(chr(97 + mt_rand(0, 25))) . mt_rand(10000001, 99999999);
                
                // Check if it's already testing
                // If so, don't query the database again
                if (in_array($random, $tested)) {
                    continue;
                }

                // Check if it is unique in the database
                $count = DB::table('users')->where('userCode', '=', $random)->count();

                // Store the random character in the tested array
                // To keep track which ones are already tested
                $tested[] = $random;

                // String appears to be unique
                if ($count == 0) {
                    // Set unique to true to break the loop
                    $unique = true;
                }

                // If unique is still false at this point
                // it will just repeat all the steps until
                // it has generated a random string of characters
            } while (!$unique);

            $userCode = $random;
        
        
        $usermodel = new User();
        $usermodel->firstName = $firstName;
        $usermodel->lastName = $lastName;
        $usermodel->email = $email;
        $usermodel->phoneNumber = $phoneNumber;
        $usermodel->password = Hash::make($password);
        $usermodel->userCode = $userCode;
        $usermodel->companyName = $companyName;
        $usermodel->companyDescription = $companyDescription;
        $usermodel->companyLogo = $companyLogo;
        $usermodel->user_type = 2;
        $usermodel->otp_verified = 1;
        $usermodel->status = 1;
        $usermodel->save();

        Toastr::success('Successfully added the company', $title = null, $options = []);
        return Redirect::to("company");
    }

    public function checkname() {
        $user_id = Input::get('user_id');
        $companyName = Input::get('companyName');
        if($user_id > 0)
        {
            $user_details = DB::table('users')
                ->whereRaw("companyName = '$companyName' and id != $user_id")
                ->first();
        }
        else
        {
            $user_details = DB::table('users')
                ->whereRaw("companyName = '$companyName'")
                ->first();
        }
        if (count($user_details) > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}
