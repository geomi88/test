<?php

namespace App\Http\Controllers\Branchsales;

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

class PhysicalstockController extends Controller {

    public function index(Request $request) {
        try{
        
        $loggedin_employee_details = DB::table('employees')
            ->select('employees.*', 'master_resources.name')
            ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
            ->where('employees.id', '=', Session::get('login_id'))
            ->first();
        
        if ($loggedin_employee_details->name != 'Cashier' && $loggedin_employee_details->admin_status != 1) {
            throw new Exception("No_permission");
        }
        
        $products = DB::table('inventory')
                ->select('inventory.*')
                ->whereraw('inventory.status != 2')
                ->orderby('inventory.created_at', 'DESC')
                ->get();
            
        $branch_details = DB::table('resource_allocation')
                ->select('resource_allocation.*', 'branch_details.id as branch_id','branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund', 'shift_details.name as shift_name','shift_details.id as job_shift_id')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->join('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                ->where(['employee_id' => Session::get('login_id')])
                ->where('active', '=', 1)->first();
        
        $date = date('Y-m-d');
        $stockData = array();
        if(count($branch_details) != 0){
            $stockData = DB::table('branch_physical_stock')
                 ->select('branch_physical_stock.*')
                 ->whereraw("stock_area_id = $branch_details->branch_id AND shift_id = $branch_details->job_shift_id  AND stock_area = 0   AND created_at like '$date%'")
                 ->first();
        }
        

        $stockSavedData = array();
        $stockPreviousData = array();
        $savedStockId = '';
        if(count($stockData) != 0 ){
            $stockSavedData = json_decode($stockData->physical_stock,true);
            $savedStockId = $stockData->id;
        } else{
            
            $stockPrevious = DB::table('branch_physical_stock')
                 ->select('branch_physical_stock.*')
                 ->whereraw("id=(select max(id) from branch_physical_stock where stock_area_id = $branch_details->branch_id and stock_area=0)")
                 ->first();
         
            if(count($stockPrevious) != 0 ){
                $stockPreviousData = json_decode($stockPrevious->physical_stock,true);
            }

        }
       
        if (count($branch_details)==0 && $loggedin_employee_details->admin_status != 1) {
            throw new Exception("No_allocation");
        } 
        
        $empstatus = $loggedin_employee_details->admin_status;
        
        return view('branchsales/physical_stock/index', array('products' => $products,'branch_details' => $branch_details,'stockSavedData' => $stockSavedData,'empstatus'=>$empstatus,'savedStockId' => $savedStockId,'stockPreviousData'=>$stockPreviousData));
        
       } catch (\Exception $e) {
          
           if($e->getMessage()=="No_permission"){
               Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
               return Redirect::to('branchsales');
           }
           
           if($e->getMessage()=="No_allocation"){
               Toastr::error('No Branch Allocated!', $title = null, $options = []);
               return Redirect::to('branchsales');
           }
           
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('branchsales');
        }
    }

   
    public function store() {
        try {
            
            $savedStockId = Input::get('savedStockId');
            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            $arrStock = array();
            foreach ($arraData as $stock) {
                $arrStock[$stock->product_id]=(int)$stock->quantity;
            }
            
            if($savedStockId != ''){
                $delete = DB::table('branch_physical_stock')
                    ->where('id', '=', $savedStockId)
                    ->delete();
            }
            
            $arrStockSave= json_encode($arrStock);

            $pstockmodel = new Branch_physical_stock();
           // $pstockmodel->branch_id = Input::get('branch_id');
            $pstockmodel->stock_area_id = Input::get('branch_id');
            $pstockmodel->shift_id = Input::get('shift_id');
            $pstockmodel->physical_stock = $arrStockSave;
            $pstockmodel->stock_area = 0;
            $pstockmodel->save();
            
            Toastr::success('Stock Successfully Saved!', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

}
