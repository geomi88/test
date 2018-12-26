<?php

namespace App\Http\Controllers\Cms\Orders;

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

class OrdersController extends Controller {

    public function index() {
        try {
            $paginate = 10;

            $orders = DB::table('orders')
                    ->select('products.id as productId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'seller.firstName as sellerFirstName', 'seller.lastName as sellerLastName', 'buyer.firstName as buyerFirstName', 'buyer.lastName as buyerLastName', 'orders.shippingStatus', 'orders.created_at', 'orders.sellingPrice', 'orders.id', 'orders.orderNumber')
                    ->leftjoin('products', 'products.id', '=', 'orders.productId')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users as seller', 'seller.id', '=', 'products.userId')
                    ->leftjoin('users as buyer', 'buyer.id', '=', 'orders.userId')
                    ->orderby('orders.created_at', 'DESC')
                    ->paginate($paginate);
            return view('orders/index', array('orders' => $orders));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

    public function search() {
        $search_key = Input::get('search_key');

        if ($search_key != "") {

            $products = DB::table('orders')
                            ->select('products.id as productId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'seller.firstName as sellerFirstName', 'seller.lastName as sellerLastName', 'buyer.firstName as buyerFirstName', 'buyer.lastName as buyerLastName', 'orders.shippingStatus', 'orders.created_at', 'orders.sellingPrice', 'orders.id', 'orders.orderNumber')
                            ->leftjoin('products', 'products.id', '=', 'orders.productId')
                            ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                            ->leftjoin('users as seller', 'seller.id', '=', 'products.userId')
                            ->leftjoin('users as buyer', 'buyer.id', '=', 'orders.userId')
                            ->whereRaw("orders.orderNumber LIKE '$search_key%' or CONCAT(buyer.firstName,' ',buyer.lastName) LIKE '$search_key%' or CONCAT(seller.firstName,' ',seller.lastName) LIKE '$search_key%'")
                            ->paginate(10)->setPath('');
            $pagination = $products->appends(array(
                'search_key' => Input::get('search_key')
            ));
            if (count($products) > 0)
                return view('orders/search')->withDetails($products)->withQuery($search_key);
        }
        return view('orders/search')->withMessage('No Details found. Try to search again !')->withQuery($search_key);
    }

    public function changeshippingstatus() {
        $order_id = Input::get('order_id');
        $shipping_status = Input::get('shipping_status');
        //update the database
        DB::table('orders')->where("id", "=", $order_id)->update(["shippingStatus" => $shipping_status]);
        if ($shipping_status == 1 || $shipping_status == 2) {
            $order = DB::table('orders')
                    ->select('orders.id as orderId','users.deviceToken', 'users.deviceOS','users.id as toId', 'products.name','products.id as productId')
                    ->leftjoin('users', 'users.id', '=', 'orders.userId')
                    ->leftjoin('products', 'products.id', '=', 'orders.productId')
                    ->where(['orders.id' => $order_id])
                    ->first();
            $deviceToken = $order->deviceToken;
            $productName = $order->name;
            
            $user_notificationmodel = new User_notification();
            $user_notificationmodel->toId = $order->toId;
            $user_notificationmodel->productId = $order->productId;
            $user_notificationmodel->orderId = $order->orderId;
            
            
            if ($shipping_status == 1) {
                $body = "Your order containing $productName has been shipped";
                $title = "Product shipped";
                $user_notificationmodel->notificationType = 2;
                $message_array['notificationType'] = 2;
            }
            if ($shipping_status == 2) {
                $body = "Your order containing $productName has been delivered";
                $title = "Product delivered";
                $user_notificationmodel->notificationType = 3;
                $message_array['notificationType'] = 3;
            }
            $user_notificationmodel->save();
            $message_array['id'] = $order_id;
            $message_array['body'] = $body;
            $message_array['title'] = $title;
            $message_array['sound'] = "default";
            $message_array['color'] = "#203E78";
            $usermodel = new User();
            $usermodel->sendPush($deviceToken, $message_array);
        }
        return 1;
    }

    public function show($id) {

        $order_details = DB::table('orders')
                ->select('products.id as productId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'seller.firstName as sellerFirstName', 'seller.lastName as sellerLastName', 'buyer.firstName as buyerFirstName', 'buyer.lastName as buyerLastName', 'orders.shippingStatus', 'orders.created_at', 'orders.sellingPrice', 'orders.id', 'orders.orderNumber', 'orders.paymentType', 'orders.fullName', 'orders.buildingNumber', 'orders.streetNumber', 'orders.zoneNumber', 'orders.phoneNumber')
                ->leftjoin('products', 'products.id', '=', 'orders.productId')
                ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                ->leftjoin('users as seller', 'seller.id', '=', 'products.userId')
                ->leftjoin('users as buyer', 'buyer.id', '=', 'orders.userId')
                ->where(['orders.id' => $id])
                ->first();


        return view('orders/view', array('order_details' => $order_details));
    }

}
