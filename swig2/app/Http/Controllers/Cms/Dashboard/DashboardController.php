<?php

namespace App\Http\Controllers\Cms\Dashboard;

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
use App\Models\Admin;

class DashboardController extends Controller {

    public function index() {
        $categories_count = DB::table('categories')
                ->select(db::raw('COUNT(*) AS categoriesCount'))
                ->first();
        $categories_count = $categories_count->categoriesCount;
        if($categories_count < 1)
        {
            $categories_count = 0;
        }
        
        $orders_count = DB::table('orders')
                ->select(db::raw('COUNT(*) AS ordersCount'))
                ->first();
        $orders_count = $orders_count->ordersCount;
        if($orders_count < 1)
        {
            $orders_count = 0;
        }
        
        $sales_count = DB::table('orders')
                ->select(db::raw('sum(sellingPrice) AS salesCount'))
                ->first();
        $sales_count = $sales_count->salesCount;
        if($sales_count < 1)
        {
            $sales_count = 0;
        }
        
        $users_count = DB::table('users')
                ->select(db::raw('COUNT(*) AS usersCount'))
                ->first();
        $users_count = $users_count->usersCount;
        if($users_count < 1)
        {
            $users_count = 0;
        }

        $orders = DB::table('orders')
                ->select('products.id as productId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'seller.firstName as sellerFirstName', 'seller.lastName as sellerLastName', 'buyer.firstName as buyerFirstName', 'buyer.lastName as buyerLastName', 'orders.shippingStatus', 'orders.created_at', 'orders.sellingPrice', 'orders.id', 'orders.orderNumber')
                ->leftjoin('products', 'products.id', '=', 'orders.productId')
                ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                ->leftjoin('users as seller', 'seller.id', '=', 'products.userId')
                ->leftjoin('users as buyer', 'buyer.id', '=', 'orders.userId')
                ->orderby('orders.created_at', 'DESC')
                ->limit(5)
                ->get();
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        $month_sales = DB::table('orders')
                ->select('orders.id', db::raw('sum(sellingPrice) AS sales'),'orders.sellingPrice','orders.created_at')
                ->whereRaw("orders.created_at BETWEEN '$first_day' and '$last_day' group by date(orders.created_at)")
                ->get();

        $graph_area = array();
        $full_area_graph_data = array();

        foreach ($month_sales as $sale) {
            $sale_created_date = $sale->created_at;
            $sale_created_date = explode(" ", $sale_created_date);
            $sale_created_date = $sale_created_date[0];

            $row['x'] = ((int) strtotime($sale_created_date)) * 1000;
            $row['y'] = (int) $sale->sales;

            array_push($graph_area, $row);
        }

        $full_row['name'] = "Sale";
        $full_row['data'] = $graph_area;
        $full_row['color'] = '#02838e';


        array_push($full_area_graph_data, $full_row);

        $full_area_graph_data = array_values($full_area_graph_data);
        $full_area_graph_data = json_encode($full_area_graph_data);

        return view('dashboard/index', array('orders' => $orders, 'categories_count' => $categories_count, 'orders_count' => $orders_count, 'sales_count' => $sales_count, 'users_count' => $users_count,'full_area_graph_data' => $full_area_graph_data));
    }

}
