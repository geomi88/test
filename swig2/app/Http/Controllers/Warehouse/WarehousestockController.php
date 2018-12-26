<?php

namespace App\Http\Controllers\Warehouse;

use App\Events\RequisitionSubmitted;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Item_request;
use App\Models\Item_request_details;
use App\Models\Item_request_tracking;
use App\Notifications\InventoryNotifications;
use App\Models\Usermodule;
use App\Models\Branch_physical_stock;
use DB;
use PDF;
use App;
use Exception;

class WarehousestockController extends Controller {

    public function index(Request $request) {
        try {

            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $loggedin_employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('employees.id', '=', Session::get('login_id'))
                    ->first();




            $loggintype = $loggedin_employee_details->name;
            $empstatus = $loggedin_employee_details->admin_status;

            //if ($loggintype != 'Warehouse_Manager' &&  $employee_details->admin_status != 1) {
            // if ($loggintype != 'Warehouse_Manager' ) {
            if ($loggintype != 'Warehouse_Manager' &&  $employee_details->admin_status != 1) {

                throw new Exception("No_permission");
            }

            $warehouse = DB::table('master_resources')
                            ->select('master_resources.name', 'master_resources.id')
                            ->where('status', '=', 1)
                            ->where('resource_type', '=', 'WAREHOUSE')
                            ->where('warehouse_manager', '=', $login_id)
                            ->orderby('name', 'ASC')
                            ->get()->toArray();



            if (count($warehouse) > 1) {


                $products = array();


                $stockData = array();
                $stockSavedData = array();
                $stockPreviousData = array();
                $savedStockId = '';
                $warehouse_id = "";
            } else {


                $warehouse_id = $warehouse[0]->id;

                $products = DB::table('inventory')
                        ->select('inventory.*')
                        ->whereraw('inventory.status != 2')
                        ->whereraw("inventory.warehouse_id =$warehouse_id")
                        ->orderby('inventory.created_at', 'DESC')
                        ->get();

                if (count($products) == 0) {

                    throw new Exception("No_products_exist");
                }



                $date = date('Y-m-d');
                $stockData = array();
                if (count($products) != 0) {

                    $stockData = DB::table('branch_physical_stock')
                            ->select('branch_physical_stock.*')
                            ->whereraw("stock_area_id = $warehouse_id  AND stock_area = 1  AND created_at like '$date%'")
                            ->first();
                }


                $stockSavedData = array();
                $stockPreviousData = array();
                $savedStockId = '';
                if (count($stockData) != 0) {
                    $stockSavedData = json_decode($stockData->physical_stock, true);
                    $savedStockId = $stockData->id;
                } else {

                    $stockPrevious = DB::table('branch_physical_stock')
                            ->select('branch_physical_stock.*')
                            ->whereraw("id=(select max(id) from branch_physical_stock where stock_area_id = $warehouse_id AND stock_area=1)")
                            ->first();


                    if (count($stockPrevious) != 0) {
                        $stockPreviousData = json_decode($stockPrevious->physical_stock, true);
                    }
                }
            }

            return view('warehouse/warehouse_physical_stock/index', array('products' => $products, 'stockSavedData' => $stockSavedData, 'empstatus' => $empstatus, 'savedStockId' => $savedStockId, 'stockPreviousData' => $stockPreviousData, 'warehouse' => $warehouse, 'warehouseId' => $warehouse_id));
        } catch (\Exception $e) {


            if ($e->getMessage() == "No_permission") {
                Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
                return Redirect::to('warehouse');
            }

            if ($e->getMessage() == "No_allocation") {
                Toastr::error('No Branch Allocated!', $title = null, $options = []);
                return Redirect::to('warehouse');
            }

            if ($e->getMessage() == "No_products_exist") {
                Toastr::error('No Products Exist!', $title = null, $options = []);
                return Redirect::to('warehouse');
            }

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('warehouse');
        }
    }

    public function get_physical_stock(Request $request) {

        if ($request->ajax()) {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $loggedin_employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('employees.id', '=', Session::get('login_id'))
                    ->first();


            $loggintype = $loggedin_employee_details->name;
            $empstatus = $loggedin_employee_details->admin_status;


            $warehouse_id = Input::get('warehouseId');

            $products = DB::table('inventory')
                    ->select('inventory.*')
                    ->whereraw('inventory.status != 2')
                    ->whereraw("inventory.warehouse_id =$warehouse_id")
                    ->orderby('inventory.created_at', 'DESC')
                    ->get();


            if (count($products) == 0) {

                throw new Exception("No_products_exist");
            }



            $date = date('Y-m-d');
            $stockData = array();
            if (count($products) != 0) {
                $stockData = DB::table('branch_physical_stock')
                        ->select('branch_physical_stock.*')
                        ->whereraw("stock_area_id = $warehouse_id  AND stock_area = 1  AND created_at like '$date%'")
                        ->first();
            }


            $stockSavedData = array();
            $stockPreviousData = array();
            $savedStockId = '';
            if (count($stockData) != 0) {
                $stockSavedData = json_decode($stockData->physical_stock, true);
                $savedStockId = $stockData->id;
            } else {

                $stockPrevious = DB::table('branch_physical_stock')
                        ->select('branch_physical_stock.*')
                        ->whereraw("id=(select max(id) from branch_physical_stock where stock_area_id = $warehouse_id AND stock_area=1)")
                        ->first();

                if (count($stockPrevious) != 0) {
                    $stockPreviousData = json_decode($stockPrevious->physical_stock, true);
                }
            }

            return view('warehouse/warehouse_physical_stock/search_results', array('products' => $products, 'stockSavedData' => $stockSavedData, 'stockPreviousData' => $stockPreviousData, 'warehouseId' => $warehouse_id, 'savedStockId' => $savedStockId));
        }
    }

    public function store() {
        try {

            $savedStockId = Input::get('savedStockId');
            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            $arrStock = array();
            foreach ($arraData as $stock) {
                $arrStock[$stock->product_id] = (int) $stock->quantity;
            }

            if ($savedStockId != '') {
                $delete = DB::table('branch_physical_stock')
                        ->where('id', '=', $savedStockId)
                        ->delete();
            }

            $arrStockSave = json_encode($arrStock);

            $pstockmodel = new Branch_physical_stock();
            $pstockmodel->stock_area_id = Input::get('branch_id');
            $pstockmodel->shift_id = Input::get('shift_id');
            $pstockmodel->physical_stock = $arrStockSave;
            $pstockmodel->stock_area = 1;
            $pstockmodel->save();

            Toastr::success('Stock Successfully Saved!', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

   
    

}
