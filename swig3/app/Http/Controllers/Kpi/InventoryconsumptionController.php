<?php

namespace App\Http\Controllers\Kpi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use Illuminate\Support\Facades\Config;
use App\Models\Company;
use App\Models\Branch_physical_stock;
use App\Models\Module;
use App\Models\Usermodule;
use App;
use DB;
use Mail;
use Exception;
use Customhelper;


class InventoryconsumptionController extends Controller {

    public function index() {
        try {

            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('employees.id', '=', $login_id)
                    ->first();

            $loggintype = $employee_details->name;
            if($loggintype != "Sales_analyst" && $employee_details->admin_status != 1){
                throw new Exception("No_permission");
            }
            
            $branches = DB::table('branches_to_analyst')
                    ->select('branches_to_analyst.*', 'det.id as branch_id', 'det.name as branch_name', 'det.branch_code as branch_code')
                    ->join('master_resources as det', 'det.id', '=', 'branches_to_analyst.branch_id')
                    ->where(['analyst_id' => Session::get('login_id')])
                    ->whereRaw("branches_to_analyst.status=1")
                    ->get();
            
            $products = DB::table('inventory')
                            ->select('inventory.id', 'inventory.product_code', 'inventory.name')
                            ->where('status', '=', 1)
                            ->where('company_id', '=', $company_id)
                            ->orderby('product_code', 'ASC')->get();

            return view('kpi/inventory_consumption/index', array('branches' => $branches, 'products' => $products));
        } catch (\Exception $e) {
            
            if ($e->getMessage() == "No_permission") {
                Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
                return Redirect::to('kpi');
            }
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('kpi');
        }
    }

    public function search() {
        try {
            $branchid = Input::get('branchid');
            $productid = Input::get('productid');
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');

            $from_date = explode('-', $from_date);
            $from_date = $from_date[2] . '-' . $from_date[1] . '-' . $from_date[0];

            $to_date = explode('-', $to_date);
            $to_date = $to_date[2] . '-' . $to_date[1] . '-' . $to_date[0];

            $stockDataFrom = DB::table('branch_physical_stock')
                    ->select('branch_physical_stock.physical_stock')
                    ->whereraw("id=(select max(id) from branch_physical_stock where stock_area_id = $branchid AND stock_area=0 AND date(created_at)<='$from_date')")
                    ->first();

            $stockDataTo = DB::table('branch_physical_stock')
                    ->select('branch_physical_stock.physical_stock')
                    ->whereraw("id=(select max(id) from branch_physical_stock where stock_area_id = $branchid  AND stock_area=0  AND date(created_at)<='$to_date')")
                    ->first();
            $arrStockFrom = array();
            if (count($stockDataFrom) > 0) {
                $arrStockFrom = json_decode($stockDataFrom->physical_stock, true);
            }

            $arrStockTo = array();
            if (count($stockDataTo) > 0) {
                $arrStockTo = json_decode($stockDataTo->physical_stock, true);
            }

            $arrReturn = array();
            $consumptionRate = 0;
            $consumptionPrice = 0;
            $fromQty = 0;
            $toQty = 0;
            
//            if (count($arrStockFrom) > 0 || count($arrStockTo) > 0) {

                $item_requests = DB::table('item_request')
                        ->select('item_request.id', 'det.approved_quantity as approved_quantity')
                        ->leftjoin('item_request_details as det', 'item_request.id', '=', 'det.item_request_id')
                        ->whereRaw("item_request.request_status='Completed' AND item_request.branch_id = $branchid AND det.inventory_id=$productid AND det.in_final_order!=0")
                        ->whereRaw("date(item_request.updated_at)>='$from_date' AND date(item_request.updated_at)<='$to_date'")
                        ->get();

                $inventory = DB::table('inventory')
                        ->select('inventory.id', 'inventory.product_code', 'inventory.name', db::raw("COALESCE(inventory.price,0) as price"))
                        ->whereRaw("inventory.id=$productid")
                        ->first();
 
                $sumQty = $item_requests->sum('approved_quantity');

                if (key_exists($productid, $arrStockFrom)) {
                    $fromQty = $arrStockFrom[$productid];
                }

                if (key_exists($productid, $arrStockTo)) {
                    $toQty = $arrStockTo[$productid];
                }

                $consumptionRate = $sumQty + $fromQty - $toQty;
                $consumptionPrice = $consumptionRate * $inventory->price;
                $arrReturn = array("productCode" => $inventory->product_code,
                    "name" => $inventory->name,
                    "consumptionRate" => Customhelper::numberformatter($consumptionRate),
                    "consumptionPrice" => Customhelper::numberformatter($consumptionPrice));
//            }
           
            if (count($arrReturn) > 0) {
                return \Response::json($arrReturn);
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('kpi');
        }
    }

}
