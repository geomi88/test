<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Models\User_rating;
use App\Models\Product;
use DB;
use Exception;
use Mail;

class UserController extends Controller {

    public function registration() {

        try {

            $firstName = Input::get('firstName');
            $lastName = Input::get('lastName');
            $email = Input::get('email');
            $phoneNumber = Input::get('phoneNumber');
            $password = Input::get('password');
            $deviceOS = Input::get('deviceOS');
            $deviceToken = Input::get('deviceToken');



            $user_data = DB::table('users')
                    ->select('id')
                    ->whereRaw("(email = '$email' or phoneNumber = '$phoneNumber') and status=1")
                    ->first();

            if (count($user_data) > 0) {

                $arrReturn['Result'] = array(
                    'status' => FALSE,
                    'error' => array('message' => 'Email or Phone already exists')
                );

                return \Response::json($arrReturn);
            }

            $otp_status = DB::table('users')
                    ->select('id')
                    ->whereRaw("(email = '$email' or phoneNumber = '$phoneNumber') and otp_verified=0")
                    ->first();
            if (count($otp_status) > 0) {

                $arrReturn['Result'] = array(
                    'status' => TRUE,
                    'response' => array('message' => 'OTP not verified', 'userId' => $otp_status->id)
                );

                return \Response::json($arrReturn);
            }


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


            $otp = mt_rand(10001, 99999);
            //$otp = 1234;
            $usermodel = new User();
            $usermodel->firstName = $firstName;
            $usermodel->lastName = $lastName;
            $usermodel->email = $email;
            $usermodel->phoneNumber = $phoneNumber;
            $usermodel->password = Hash::make($password);
            $usermodel->deviceOS = $deviceOS;
            $usermodel->deviceToken = $deviceToken;
            $usermodel->userCode = $userCode;
            $usermodel->otp = $otp;
            //$usermodel->status = 1;
            $usermodel->save();

            $userId = $usermodel->id;

            if ($userId > 0) {
                $url = "https://api.infobip.com/sms/1/text/single";
                $ch = curl_init($url);
                $data['from'] = "Safqa";
                $data['to'] = "+$phoneNumber";
                $data['text'] = "OTP : $otp";
                $payload = json_encode($data);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Basic QXBwbGFiUTpteWRhYXIxMjM0', 'Accept:application/json'));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Basic c2FmcWE6c2FmcWExMjM0', 'Accept:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                curl_close($ch);
            }

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('userId' => $userId)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {
            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }

    public function otpverification() {
        try {

            $userId = Input::get('userId');
            $otp = Input::get('otp');
            $deviceOS = Input::get('deviceOS');
            $deviceToken = Input::get('deviceToken');

            $result = DB::table('users')
                    ->select('id', 'otp_verified')
                    ->whereRaw("otp='$otp' AND id=$userId")
                    ->first();

            if (count($result) > 0) {

                $users = DB::table('users')
                        ->where(['id' => $userId])
                        ->update(['otp_verified' => 1, 'status' => 1, 'deviceOS' => $deviceOS, 'deviceToken' => $deviceToken]);

                $userData = DB::table('users as u')
                        ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo')
                        ->where(['u.id' => $userId])
                        ->first();

                $arrReturn['Result'] = array(
                    'status' => TRUE,
                    'response' => array('message' => "OTP verified successfully", 'userData' => $userData)
                );
            } else {

                $arrReturn['Result'] = array(
                    'status' => FALSE,
                    'error' => array('message' => 'Invalid OTP', 'userId' => $userId)
                );
            }

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }

    public function login() {
        try {

            $userdata = array(
                'phoneNumber' => Input::get('phoneNumber'),
                'password' => Input::get('password'),
                'status' => 1
            );

            if (Auth::validate($userdata)) {
                if (Auth::attempt($userdata)) {

                    $user = Auth::user();
                    $userId = Auth::id($user);


                    $userData = DB::table('users as u')
                            ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType', 'u.shippingFullName', 'u.shippingBuildingNumber', 'u.shippingStreetNumber', 'u.shippingZoneNumber', 'u.shippingPhoneNumber')
                            ->where(['u.id' => $userId, 'u.status' => 1])
                            ->first();


                    if ($userData->otp_verified == 1) {

                        $deviceOS = Input::get('deviceOS');
                        $deviceToken = Input::get('deviceToken');

                        $clear_device_tokens = DB::table('users')
                                ->where(['deviceToken' => $deviceToken])
                                ->update(['deviceToken' => '']);

                        $update_user = DB::table('users')
                                ->where(['id' => $userId])
                                ->update(['deviceOS' => $deviceOS, 'deviceToken' => $deviceToken]);

                        $arrReturn['Result'] = array(
                            'status' => TRUE,
                            'response' => array('userData' => $userData)
                        );
                    } else {
                        $arrReturn['Result'] = array(
                            'status' => FALSE,
                            'error' => array('message' => 'OTP not verified')
                        );
                    }


                    return \Response::json($arrReturn);
                }
            } else {

                $arrReturn['Result'] = array(
                    'status' => FALSE,
                    'error' => array('message' => 'Invalid user credentials')
                );
                return \Response::json($arrReturn);
            }
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function forgotpassword() {
        try {

            $email = Input::get('email');

            $userData = DB::table('users as u')
                    ->select('u.id', 'u.firstName', 'u.lastName')
                    ->whereRaw("u.email='$email' AND u.status=1")
                    ->first();

            if (count($userData) > 0) {
                $password = mt_rand(10000001, 99999999);
                $encrypted_password = Hash::make($password);
                $update_user = DB::table('users')
                        ->where(['email' => $email])
                        ->update(['password' => $encrypted_password]);
                $user_name = $userData->firstName . " " . $userData->lastName;
                Mail::send('emailtemplates.forgot_pwd', ['email' => $email, 'user_name' => $user_name, 'password' => $password], function($message)use ($email) {
                    $message->to($email)->subject('Forgot Password');
                });


                $arrReturn['Result'] = array(
                    'status' => TRUE,
                    'response' => array('message' => "Your New Password has been send to your mail id")
                );
            } else {

                $arrReturn['Result'] = array(
                    'status' => FALSE,
                    'error' => array('message' => 'Invalid user')
                );
            }

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }

    /* public function userprofile() {
      try {

      $userId = Input::get('userId');
      $userData = DB::table('users as u')
      ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
      ->where(['u.id' => $userId])
      ->first();
      $myProducts = DB::table('products')
      ->select('products.id as productId', 'products.name as productName','products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.userCode as sellerUserCode', 'products.isVerified','products.offerPrice','products.dealStatus','users.user_type as sellerUserType','products.quantity','products.acceptOfferStatus')
      ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
      ->leftjoin('users', 'users.id', '=', 'products.userId')
      ->where(['products.userId' => $userId])
      ->get();
      foreach ($myProducts as $product) {
      $product_images = DB::table('product_images')
      ->select('product_images.image')
      ->where(['product_images.productId' => $product->productId])
      ->get();
      $product->images = $product_images;
      }
      $myOrders = DB::table('orders')
      ->select('products.id as productId', 'products.name as productName','products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.userCode as sellerUserCode', 'products.isVerified','products.offerPrice','products.dealStatus','users.user_type as sellerUserType','products.quantity','products.acceptOfferStatus')
      ->leftjoin('products', 'products.id', '=', 'orders.productId')
      ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
      ->leftjoin('users', 'users.id', '=', 'products.userId')

      ->where(['orders.userId' => $userId])
      ->get();
      foreach ($myOrders as $product) {
      $product_images = DB::table('product_images')
      ->select('product_images.image')
      ->where(['product_images.productId' => $product->productId])
      ->get();
      $product->images = $product_images;
      }
      $profile['userData'] = $userData;
      $profile['myProducts'] = $myProducts;
      $profile['myOrders'] = $myOrders;

      $arrReturn['Result'] = array(
      'status' => TRUE,
      'response' => array('profile' => $profile)
      );

      return \Response::json($arrReturn);
      } catch (\Exception $e) {

      $arrReturn['Result'] = array(
      'status' => FALSE,
      'error' => array('message' => $e)
      );

      return \Response::json($arrReturn);
      }
      } */

    public function userprofile() {
        try {

            $userId = Input::get('userId');
            $product_model = new Product();
            $userData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
                    ->where(['u.id' => $userId])
                    ->first();

            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $userData->sellerRating = $average_rating;
            } else {
                $userData->sellerRating = 0;
            }

            $activeProducts = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['products.userId' => $userId, 'products.delete_status' => 0])
                    ->get();
            $activeProducts = $product_model->productdetails($activeProducts);
            $soldProducts = DB::table('orders')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('products', 'products.id', '=', 'orders.productId')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['products.userId' => $userId])
                    ->groupby('orders.productId')
                    ->get();
            $soldProducts = $product_model->productdetails($soldProducts);
            $profile['userData'] = $userData;
            $profile['activeProducts'] = $activeProducts;
            $profile['soldProducts'] = $soldProducts;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('profile' => $profile)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function companyprofile() {
        try {

            $userId = Input::get('userId');
            $product_model = new Product();
            $userData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
                    ->where(['u.id' => $userId])
                    ->first();
            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $userData->sellerRating = $average_rating;
            } else {
                $userData->sellerRating = 0;
            }

            $myProducts = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['products.userId' => $userId, 'products.delete_status' => 0])
                    ->get();
            $myProducts = $product_model->productdetails($myProducts);
            $deals = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->whereRaw("products.userId = $userId and products.dealStatus = 1 and products.delete_status = 0")
                    ->get();
            $deals = $product_model->productdetails($deals);
            $profile['userData'] = $userData;
            $profile['myProducts'] = $myProducts;
            $profile['deals'] = $deals;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('profile' => $profile)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function myorders() {
        try {

            $userId = Input::get('userId');
            $product_model = new Product();
            $myOrders = DB::table('orders')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus', 'orders.shippingStatus', 'orders.rateStatus', 'orders.id as orderId')
                    ->leftjoin('products', 'products.id', '=', 'orders.productId')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['orders.userId' => $userId])
                    //->groupby('orders.productId')
                    ->orderby('orders.created_at', 'DESC')
                    ->get();
            $myOrders = $product_model->productdetails($myOrders);

            $mysoldproducts = DB::table('orders')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus', 'orders.shippingStatus')
                    ->leftjoin('products', 'products.id', '=', 'orders.productId')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['products.userId' => $userId])
                    //->groupby('orders.productId')
                    ->orderby('orders.created_at', 'DESC')
                    ->get();
            $mysoldproducts = $product_model->productdetails($mysoldproducts);

            $orders['productsBought'] = $myOrders;
            $orders['productsSold'] = $mysoldproducts;
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('myOrders' => $orders)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function sellerprofile() {
        try {

            $userId = Input::get('userId');
            $product_model = new Product();
            $userData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
                    ->where(['u.id' => $userId])
                    ->first();
            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $userData->sellerRating = $average_rating;
            } else {
                $userData->sellerRating = 0;
            }
            $activeProducts = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->whereRaw("products.userId = $userId and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                    ->get();
            $activeProducts = $product_model->productdetails($activeProducts);

            $profile['userData'] = $userData;
            $profile['activeProducts'] = $activeProducts;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('profile' => $profile)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function companysellerprofile() {
        try {

            $userId = Input::get('userId');
            $product_model = new Product();
            $userData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
                    ->where(['u.id' => $userId])
                    ->first();
            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $userId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $userData->sellerRating = $average_rating;
            } else {
                $userData->sellerRating = 0;
            }
            $myProducts = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->whereRaw("products.userId = $userId and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                    ->get();
            $myProducts = $product_model->productdetails($myProducts);
            $deals = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->whereRaw("products.userId = $userId and products.dealStatus = 1 and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                    ->get();
            $deals = $product_model->productdetails($deals);
            $profile['userData'] = $userData;
            $profile['myProducts'] = $myProducts;
            $profile['deals'] = $deals;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('profile' => $profile)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function rateuser() {

        try {
            $ratedBy = Input::get('ratedBy');
            $ratedTo = Input::get('ratedTo');
            $rateStatus = Input::get('rateStatus');
            $orderId = Input::get('orderId');

            $rate_user = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedBy' => $ratedBy, 'user_ratings.ratedTo' => $ratedTo])->first();

            $update_order = DB::table('orders')
                    ->where(['id' => $orderId])
                    ->update(['rateStatus' => 1]);

            if (count($rate_user) > 0) {
                $rate_id = $rate_user->id;
                $ratingmodel = User_rating::find($rate_id);
                $ratingmodel->rateStatus = $rateStatus;
                $ratingmodel->save();
            } else {
                $ratingmodel = new User_rating();
                $ratingmodel->ratedBy = $ratedBy;
                $ratingmodel->ratedTo = $ratedTo;
                $ratingmodel->rateStatus = $rateStatus;
                $ratingmodel->save();
            }



            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('rateStatus' => $rateStatus)
            );
            return \Response::json($arrReturn);
        } catch (\Exception $e) {
            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function notifications() {
        try {

            $userId = Input::get('userId');
            $limit = 20;
            $offset = Input::get('offset');
            if($offset > 0)
            {
                $notifications = DB::table('user_notifications')
                    ->select('user_notifications.id as notificationId','user_notifications.productId', 'user_notifications.orderId', 'user_notifications.notificationType', 'products.name as productName', 'user_notifications.created_at as notificationDate', 'user_notifications.publicMessage', 'user_notifications.readStatus', 'products.rejectReason')
                    ->leftjoin('products', 'products.id', '=', 'user_notifications.productId')
                    ->leftjoin('orders', 'orders.id', '=', 'user_notifications.orderId')
                    ->whereRaw("user_notifications.toId = $userId and user_notifications.id < $offset")
                    ->orderby('user_notifications.created_at', 'DESC')
                    ->limit($limit)
                    ->get();
            }
            else
            {
                $notifications = DB::table('user_notifications')
                    ->select('user_notifications.id as notificationId','user_notifications.productId', 'user_notifications.orderId', 'user_notifications.notificationType', 'products.name as productName', 'user_notifications.created_at as notificationDate', 'user_notifications.publicMessage', 'user_notifications.readStatus', 'products.rejectReason')
                    ->leftjoin('products', 'products.id', '=', 'user_notifications.productId')
                    ->leftjoin('orders', 'orders.id', '=', 'user_notifications.orderId')
                    ->whereRaw("user_notifications.toId = $userId")
                    ->orderby('user_notifications.created_at', 'DESC')
                    ->limit($limit)    
                    ->get();
            }
            foreach ($notifications as $notification) {
                if ($notification->notificationType == 1) {
                    $productName = $notification->productName;
                    $notification->message = "Your Product $productName has been approved";
                }
                if ($notification->notificationType == 2) {
                    $productName = $notification->productName;
                    $notification->message = "Your order containing $productName has been shipped";
                }
                if ($notification->notificationType == 3) {
                    $productName = $notification->productName;
                    $notification->message = "Your order containing $productName has been delivered";
                }
                if ($notification->notificationType == 4) {
                    $productName = $notification->productName;
                    $notification->message = "Your Product $productName has been verified";
                }
                if ($notification->notificationType == 5) {
                    $notification->message = $notification->publicMessage;
                }
                if ($notification->notificationType == 6) {
                    $productName = $notification->productName;
                    $rejectReason = $notification->rejectReason;
                    $notification->message = "Your Product $productName has been rejected.Reason : $rejectReason";
                }
            }
            $update_read_status = DB::table('user_notifications')
                    ->whereRaw("toId = $userId")
                    ->update(['readStatus' => 1]);
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('notifications' => $notifications)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function profile() {
        try {

            $userId = Input::get('userId');
            $userData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType', 'u.shippingFullName', 'u.shippingBuildingNumber', 'u.shippingStreetNumber', 'u.shippingZoneNumber', 'u.shippingPhoneNumber', db::raw("(select count(*) from user_notifications where user_notifications.toId = $userId and user_notifications.readStatus = 0) as unreadNotificationsCount"))
                    ->where(['u.id' => $userId])
                    ->first();

            $profile['userData'] = $userData;


            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('profile' => $profile)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function productsbuysell() {
        try {

            $userId = Input::get('userId');
            $product_model = new Product();

            $myProducts = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus',db::raw("(select count(*) from conversations where conversations.productId = products.id and conversations.toId = $userId and conversations.readStatus = 0) as unreadMessagesCount "))
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['products.userId' => $userId, 'products.delete_status' => 0])
                    ->get();
            $myProducts = $product_model->productdetails($myProducts);
            /*$myOrders = DB::table('orders')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus',db::raw("(select count(*) from conversations where conversations.productId = orders.productId and conversations.toId = $userId and conversations.readStatus = 0) as unreadMessagesCount "))
                    ->leftjoin('products', 'products.id', '=', 'orders.productId')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['orders.userId' => $userId])
                    ->get();*/
            $myOrders = DB::table('conversations')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus',db::raw("(select count(*) from conversations where conversations.productId = products.id and conversations.toId = $userId and conversations.readStatus = 0) as unreadMessagesCount "))
                    ->leftjoin('products', 'products.id', '=', 'conversations.productId')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->whereRaw("(conversations.fromId = $userId or conversations.toId = $userId) and conversations.productId not in (select id from products where products.userId = $userId)")
                    ->get()->unique('productId');
            $myOrders = $myOrders->toArray();
            $myOrders = array_values($myOrders);
            $myOrders = $product_model->productdetails($myOrders);
            $profile['myProducts'] = $myProducts;
            $profile['myOrders'] = $myOrders;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('profile' => $profile)
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function logout() {
        try {

            $userId = Input::get('userId');
            $users = DB::table('users')
                    ->where(['id' => $userId])
                    ->update(['deviceOS' => '', 'deviceToken' => '']);



            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('message' => 'Logout')
            );

            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => $e)
            );

            return \Response::json($arrReturn);
        }
    }

    public function guestuser() {
        try {

            $deviceOS = Input::get('deviceOS');
            $deviceToken = Input::get('deviceToken');
            $userData = DB::table('users as u')
                            ->select('u.id as userId')
                            ->where(['u.deviceToken' => $deviceToken, 'u.deviceOS' => $deviceOS, 'u.user_type' => 3])
                            ->first();
            if(count($userData) < 1)
            {
                $clear_device_tokens = DB::table('users')
                    ->where(['deviceToken' => $deviceToken])
                    ->update(['deviceToken' => '', 'deviceOS' => '']);
                $usermodel = new User();
                $usermodel->deviceOS = $deviceOS;
                $usermodel->deviceToken = $deviceToken;
                $usermodel->user_type = 3;
                $usermodel->save();
                
                $userId = $usermodel->id;
            }
            else
            {
                $userId = $userData->userId;
            }

            
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('userId' => $userId)
            );



            return \Response::json($arrReturn);
        } catch (\Exception $e) {

            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }
    
    public function deletenotification() {

        try {
            $notificationId = Input::get('notificationId');
            

            $delete_notification = DB::table('user_notifications')
                    ->where(['user_notifications.id' => $notificationId])
                    ->delete();
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('message' => 'Success')
            );
            return \Response::json($arrReturn);
        } catch (\Exception $e) {
            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }
    
    public function deleteallnotifications() {

        try {
            $userId = Input::get('userId');
            

            $delete_notification = DB::table('user_notifications')
                    ->where(['user_notifications.toId' => $userId])
                    ->delete();
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('message' => 'Success')
            );
            return \Response::json($arrReturn);
        } catch (\Exception $e) {
            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }

}
