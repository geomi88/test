<?php

namespace App\Http\Controllers\Cms\Products;

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

class ProductsController extends Controller {

    public function index() {
        try {
            $paginate = 10;
            $products = DB::table('products')
                            ->select('products.*', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName')
                            ->leftjoin('users', 'users.id', '=', 'products.userId')
                            ->whereRaw("products.delete_status = 0")
                            ->orderby('created_at', 'DESC')->paginate($paginate);
            return view('products/index', array('products' => $products));
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

            $products = DB::table('products')
                            ->select('products.*', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName')
                            ->leftjoin('users', 'users.id', '=', 'products.userId')
                            ->whereRaw("(products.name LIKE '$search_key%' or products.price = '$search_key' or CONCAT(users.firstName,' ',users.lastName) LIKE '$search_key%') and products.delete_status = 0")
                            ->paginate(10)->setPath('');
            $pagination = $products->appends(array(
                'search_key' => Input::get('search_key')
            ));
            if (count($products) > 0)
                return view('products/search')->withDetails($products)->withQuery($search_key);
        }
        return view('products/search')->withMessage('No Details found. Try to search again !')->withQuery($search_key);
    }

    public function verify() {
        $prod_id = Input::get('product_id');
        $isVerified = Input::get('is_verified');
        //update the database
        if ($isVerified != 3) {
            DB::table('products')->where("id", "=", $prod_id)->update(["isVerified" => $isVerified]);
        }

        if ($isVerified == 1 || $isVerified == 2) {
            $product = DB::table('products')
                    ->select('users.deviceToken', 'users.deviceOS', 'products.name', 'users.id as toId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['products.id' => $prod_id])
                    ->first();
            $deviceToken = $product->deviceToken;
            $productName = $product->name;

            $user_notificationmodel = new User_notification();
            $user_notificationmodel->toId = $product->toId;
            $user_notificationmodel->productId = $prod_id;
            if ($isVerified == 1) {
                $body = "Your Product $productName has been approved";
                $title = "Product approved";
                $user_notificationmodel->notificationType = 1;
                $message_array['notificationType'] = 1;
            }
            if ($isVerified == 2) {
                $body = "Your Product $productName has been verified";
                $title = "Product verified";
                $user_notificationmodel->notificationType = 4;
                $message_array['notificationType'] = 4;
            }
            $message_array['id'] = $prod_id;
            $message_array['body'] = $body;
            $message_array['title'] = $title;
            $message_array['sound'] = "default";
            $message_array['color'] = "#203E78";

            $usermodel = new User();
            $usermodel->sendPush($deviceToken, $message_array);


            $user_notificationmodel->save();
        }
        return 1;
    }

    public function show($id) {
        $product = DB::table('products')
                ->select('products.id as productId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'categories.name as categoryName', 'products.quantity as quantity', 'products.featuredStatus as featuredStatus')
                ->leftjoin('users', 'users.id', '=', 'products.userId')
                ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                ->where(['products.id' => $id])
                ->first();

        $product_images = DB::table('product_images')
                ->select('product_images.image')
                ->where(['product_images.productId' => $id])
                ->get();
        $product->images = $product_images;

        return view('products/view', array('product_details' => $product));
    }

    public function deals() {
        try {
            $paginate = 10;
            $products = DB::table('products')
                            ->select('products.*', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName')
                            ->leftjoin('users', 'users.id', '=', 'products.userId')
                            ->whereRaw("products.dealStatus = 1 and products.delete_status = 0")
                            ->orderby('created_at', 'DESC')->paginate($paginate);
            return view('products/deals', array('products' => $products));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

    public function searchdeals() {
        $search_key = Input::get('search_key');

        if ($search_key != "") {

            $products = DB::table('products')
                            ->select('products.*', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName')
                            ->leftjoin('users', 'users.id', '=', 'products.userId')
                            ->whereRaw("(products.name LIKE '$search_key%' or products.price = '$search_key' or CONCAT(users.firstName,' ',users.lastName) LIKE '$search_key%') and products.dealStatus = 1 and products.delete_status = 0")
                            ->paginate(10)->setPath('');
            $pagination = $products->appends(array(
                'search_key' => Input::get('search_key')
            ));
            if (count($products) > 0)
                return view('products/searchdeals')->withDetails($products)->withQuery($search_key);
        }
        return view('products/search')->withMessage('No Details found. Try to search again !')->withQuery($search_key);
    }

    public function changefeatured() {
        $product_id = Input::get('product_id');
        $is_featured = Input::get('is_featured');
        //update the database
        DB::table('products')->where("id", "=", $product_id)->update(["featuredStatus" => $is_featured]);


        return 1;
    }

    public function addreason() {
        $product_id = Input::get('product_id');
        $rejectReason = Input::get('rejectReason');
        //update the database
        DB::table('products')->where("id", "=", $product_id)->update(["rejectReason" => $rejectReason, "isVerified" => 3]);

        $product = DB::table('products')
                ->select('users.deviceToken', 'users.deviceOS', 'products.name', 'users.id as toId')
                ->leftjoin('users', 'users.id', '=', 'products.userId')
                ->where(['products.id' => $product_id])
                ->first();
        $deviceToken = $product->deviceToken;
        $productName = $product->name;

        $user_notificationmodel = new User_notification();
        $user_notificationmodel->toId = $product->toId;
        $user_notificationmodel->productId = $product_id;

        $body = "Your Product $productName has been Rejected.Reason : $rejectReason";
        $title = "Product Rejected";
        $user_notificationmodel->notificationType = 6;
        $message_array['notificationType'] = 6;


        $message_array['id'] = $product_id;
        $message_array['body'] = $body;
        $message_array['title'] = $title;
        $message_array['sound'] = "default";
        $message_array['color'] = "#203E78";

        $usermodel = new User();
        $usermodel->sendPush($deviceToken, $message_array);


        $user_notificationmodel->save();
        Toastr::success('Reject Reason Added', $title = null, $options = []);
        return Redirect::to('products');
    }

}
