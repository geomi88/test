<?php

namespace App\Http\Controllers\Branch;

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

class BranchphysicalstockController extends Controller {

    public function index(Request $request) {
        try{
            
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $loggedin_employee_details = DB::table('employees')
                ->select('employees.*', 'master_resources.name')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', $login_id)
                ->first();

            if ($loggedin_employee_details->name != 'Sales_analyst' && $loggedin_employee_details->name != 'Supervisor' && $loggedin_employee_details->admin_status != 1) {
                throw new Exception("No_permission");
            }

            if ($loggedin_employee_details->name == 'Supervisor') {
                    $branch_details = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.id as branch_id', 'branch_details.branch_code as branch_code')
                            ->join('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->where(['employee_id' => $login_id])
                            ->where('active', '=', 1)
                            ->get();
                } else if ($loggedin_employee_details->name == 'Sales_analyst') {
                    $branch_details = DB::table('branches_to_analyst')
                            ->select('branches_to_analyst.*', 'branch_details.id as branch_id', 'branch_details.branch_code as branch_code')
                            ->join('master_resources as branch_details', 'branch_details.id', '=', 'branches_to_analyst.branch_id')
                            ->where(['analyst_id' => $login_id])
                            ->where('branches_to_analyst.status', '=', 1)
                            ->get();
                }

            if (count($branch_details)==0 && $loggedin_employee_details->admin_status != 1) {
                throw new Exception("No_allocation");
            } 

            return view('branch/branch_physical_stock/index', array('branch_details' => $branch_details));
        
       } catch (\Exception $e) {
          
           if($e->getMessage()=="No_permission"){
               Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
               return Redirect::to('branch');
           }
           
           if($e->getMessage()=="No_allocation"){
               Toastr::error('No Branch Allocated!', $title = null, $options = []);
               return Redirect::to('branch');
           }
           
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('branch');
        }
    }

    public function getphysicalstock() {
        try{

            $branchid = Input::get('branchid');
            
            $products = DB::table('inventory')
                ->select('inventory.*')
                ->whereraw('inventory.status != 2')
                ->orderby('inventory.created_at', 'DESC')
                ->get();
                   
            $to_date = date('Y-m-d');
            $stockData = array();
            
            $stockData = DB::table('branch_physical_stock')
                 ->select('branch_physical_stock.*')
                 ->whereraw("id=(select max(id) from branch_physical_stock where stock_area_id = $branchid AND stock_area=0 AND date(created_at)<='$to_date')")
                 ->first();

            $stockSavedData = array();
            if(count($stockData) != 0 ){
                $stockSavedData = json_decode($stockData->physical_stock,true);
                $strDate = date("d-m-Y", strtotime($stockData->created_at));
            }else{
                $strDate = '';
            }
            
            $arrReturn = array();
            foreach ($products as $product){
                       
                if(key_exists($product->id, $stockSavedData)){
                    $txtvalue = $stockSavedData[$product->id];
                }else{
                    $txtvalue = 0;
                }
                
                $arrReturn[]=array(
                            "productCode"=>$product->product_code,
                            "name"=>$product->name,
                            "quantity"=>$txtvalue,
                            "strDate"=>$strDate,
                            );
                
            }           
            
            if (count($arrReturn) > 0) {
                return \Response::json($arrReturn);
            } else {
                return -1;
            }
            
       } catch (\Exception $e) {
          
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('branch');
        }
    }

}
