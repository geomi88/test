<?php

namespace App\Http\Controllers\Cms\Users;

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
use App\Models\User_notification;

class UsersController extends Controller {

    public function index() {
        try {
            $paginate = 10;
            $users = DB::table('users')
                            ->select('users.*')
                            ->where(['otp_verified' => 1, 'status' => 1, 'user_type' => 1])
                            ->orderby('created_at', 'DESC')->paginate($paginate);
            return view('users/index', array('users' => $users));
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


            Toastr::success('Successfully Deleted the User', $title = null, $options = []);
            return Redirect::to('users');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('users');
        }
    }

    public function search() {
        $search_key = Input::get('search_key');
        if ($search_key != "") {
            //$users = User::where('firstName', 'LIKE', $search_key . '%')->orWhere('lastName', 'LIKE', $search_key . '%')->orWhere('userCode', 'LIKE', $search_key . '%')->orWhere('email', 'LIKE', $search_key . '%')->paginate(5)->setPath('');

            $users = DB::table('users')
                            ->select('*')
                            ->whereRaw("(firstName LIKE '$search_key%' or lastName LIKE '$search_key%' or userCode LIKE '$search_key%' or email LIKE '$search_key%') and user_type=1")
                            ->paginate(10)->setPath('');
            $pagination = $users->appends(array(
                'search_key' => Input::get('search_key')
            ));
            if (count($users) > 0)
                return view('users/search')->withDetails($users)->withQuery($search_key);
        }
        return view('users/search')->withMessage('No Details found. Try to search again !')->withQuery($search_key);
    }

    public function edit($id) {

        $user_details = DB::table('users')
                ->where(['id' => $id])
                ->first();

        return view('users/edit', array('user_details' => $user_details));
    }

    public function checkemail() {
        $user_id = Input::get('user_id');
        $email = Input::get('email');
        $user_details = DB::table('users')
                ->whereRaw("email = '$email' and id != $user_id")
                ->first();

        if (count($user_details) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkphone() {
        $user_id = Input::get('user_id');
        $phoneNumber = Input::get('phoneNumber');
        $user_details = DB::table('users')
                ->whereRaw("phoneNumber = '$phoneNumber' and id != $user_id")
                ->first();

        if (count($user_details) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function update() {
        $user_id = Input::get('user_id');
        $firstName = Input::get('firstName');
        $lastName = Input::get('lastName');
        $email = Input::get('email');
        $phoneNumber = Input::get('phoneNumber');
        $update_user = DB::table('users')
                ->where(['id' => $user_id])
                ->update(['firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'phoneNumber' => $phoneNumber]);
        Toastr::success('Successfully updated the user', $title = null, $options = []);
        return Redirect::to("users/edit/$user_id");
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect::to('/');
    }

    public function notification(Request $request) {
        $paginate = 10;
        $arrId = array();
        $users = DB::table('users')
                        ->select('users.*')
                        //->where(['otp_verified' => 1, 'status' => 1])
                        ->whereRaw("otp_verified = 1 and status = 1 and (user_type = 1 or user_type = 2)")
                        ->orderby('created_at', 'DESC')->paginate($paginate);
        if ($request->ajax()) {
            $strids = Input::get('strids');
            $strids = rtrim($strids, ",");
            $arrId = explode(",", $strids);
            $users = DB::table('users')
                            ->select('users.*')
                            //->where(['otp_verified' => 1, 'status' => 1, 'user_type' => 1])
                            ->whereRaw("otp_verified = 1 and status = 1 and (user_type = 1 or user_type = 2)")
                            ->orderby('created_at', 'DESC')->paginate($paginate);
            return view('users/user_result', array('users' => $users, 'arrid' => $arrId));
        }
        return view('users/notification', array('users' => $users, 'arrid' => $arrId));
    }

    public function sendnotification() {
        $allusersstatus = Input::get('allusersstatus');
        $guestusersstatus = Input::get('guestusersstatus');
        $bothusersstatus = Input::get('bothusersstatus');
        $message = Input::get('message');
        $deviceTokens = array();
        if ($bothusersstatus == 1) {
            $users = DB::table('users')
                    ->select('users.deviceToken', 'users.id')
                    ->whereRaw("users.status = 1")
                    ->get();
            $message_array['id'] = '';
            $message_array['body'] = $message;
            $message_array['title'] = 'Public Notification';
            $message_array['sound'] = "default";
            $message_array['color'] = "#203E78";
            
            foreach ($users as $user_details) {
                $deviceToken = $user_details->deviceToken;


                //$usermodel = new User();
                //$usermodel->sendPush($deviceToken, $message_array);
                if($deviceToken != NULL)
                {
                array_push($deviceTokens, $deviceToken);
                }
                $user_notificationmodel = new User_notification();
                $user_notificationmodel->notificationType = 5;
                $user_notificationmodel->toId = $user_details->id;
                $user_notificationmodel->publicMessage = $message;
                $user_notificationmodel->save();
            }
        }
        else if ($allusersstatus == 1) {
            $users = DB::table('users')
                    ->select('users.deviceToken', 'users.id')
                    ->whereRaw("users.user_type = 1 or users.user_type = 2")
                    ->get();
            $message_array['id'] = '';
            $message_array['body'] = $message;
            $message_array['title'] = 'Public Notification';
            $message_array['sound'] = "default";
            $message_array['color'] = "#203E78";
            
            foreach ($users as $user_details) {
                $deviceToken = $user_details->deviceToken;


                //$usermodel = new User();
                //$usermodel->sendPush($deviceToken, $message_array);
                if($deviceToken != NULL)
                {
                array_push($deviceTokens, $deviceToken);
                }
                $user_notificationmodel = new User_notification();
                $user_notificationmodel->notificationType = 5;
                $user_notificationmodel->toId = $user_details->id;
                $user_notificationmodel->publicMessage = $message;
                $user_notificationmodel->save();
            }
        }
        else if ($guestusersstatus == 1) {
            $users = DB::table('users')
                    ->select('users.deviceToken', 'users.id')
                    ->whereRaw("users.user_type = 3")
                    ->get();
            $message_array['id'] = '';
            $message_array['body'] = $message;
            $message_array['title'] = 'Public Notification';
            $message_array['sound'] = "default";
            $message_array['color'] = "#203E78";
            
            foreach ($users as $user_details) {
                $deviceToken = $user_details->deviceToken;


                //$usermodel = new User();
                //$usermodel->sendPush($deviceToken, $message_array);
                if($deviceToken != NULL)
                {
                array_push($deviceTokens, $deviceToken);
                }
                $user_notificationmodel = new User_notification();
                $user_notificationmodel->notificationType = 5;
                $user_notificationmodel->toId = $user_details->id;
                $user_notificationmodel->publicMessage = $message;
                $user_notificationmodel->save();
            }
        }
        else {
            $user_ids = Input::get('user_ids');
            $user_ids = json_decode($user_ids);
            $message_array['id'] = '';
            $message_array['body'] = $message;
            $message_array['title'] = 'Public Notification';
            $message_array['sound'] = "default";
            $message_array['color'] = "#203E78";
            foreach ($user_ids as $user_id) {
                $user_details = DB::table('users')
                        ->select('users.deviceToken', 'users.id')
                        ->where(['users.id' => $user_id->user_id])
                        ->first();
                $deviceToken = $user_details->deviceToken;
                if($deviceToken != NULL)
                {
                array_push($deviceTokens, $deviceToken);
                }
                //$usermodel = new User();
                //$usermodel->sendPush($deviceToken, $message_array);


                $user_notificationmodel = new User_notification();
                $user_notificationmodel->notificationType = 5;
                $user_notificationmodel->toId = $user_details->id;
                $user_notificationmodel->publicMessage = $message;
                $user_notificationmodel->save();
            }
        }
        if (count($deviceTokens) > 0) {
            $usermodel = new User();
            $usermodel->sendMultiPush($deviceTokens, $message_array);
        }
        Toastr::success('Notification send successfully', $title = null, $options = []);
        return Redirect::to('users/notification');
    }

}
