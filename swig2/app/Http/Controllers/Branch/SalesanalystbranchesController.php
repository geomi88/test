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
use App\Models\Employees;
use Illuminate\Support\Facades\Config;
use App\Models\Company;
use App\Models\Branches_to_analyst;
use App\Models\Module;
use App\Models\Usermodule;
use App;
use DB;
use Mail;
use Exception;

class SalesanalystbranchesController extends Controller {

    public function index(Request $request) {
        try {

            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
//        $loggedin_employee_details = DB::table('employees')
//            ->select('employees.*', 'master_resources.name')
//            ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
//            ->where('employees.id', '=', Session::get('login_id'))
//            ->first();
//        
//        if ($loggedin_employee_details->name != 'Cashier' && $loggedin_employee_details->admin_status != 1) {
//            throw new Exception("No_permission");
//        }
//        
         
                
          
          
            $salesanalysts = DB::table('employees')
                    ->select('employees.first_name', 'employees.alias_name', 'employees.id')
                    ->leftjoin('master_resources as m', 'employees.job_position', '=', 'm.id')
                    ->where(['m.name' => 'Sales_Analyst', 'm.company_id' => $company_id])
                    ->where('employees.status', '=', 1)
                    ->get();

            return view('branch/sales_analyst_branches/index', array('salesanalysts' => $salesanalysts));
        } catch (\Exception $e) {

            if ($e->getMessage() == "No_permission") {
               Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
               return Redirect::to('branch');
            }

            if ($e->getMessage() == "No_allocation") {
               Toastr::error('No Branch Allocated!', $title = null, $options = []);
               return Redirect::to('branch');
            }

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('branch');
        }
    }

    public function getanalystbranches() {
        try {
            $analystid = Input::get('analystid');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            
            $branches = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'BRANCH', 'company_id' => $company_id,'status'=>1])
                ->orderby("branch_code","ASC")
                ->get();
           
            $analystbranches = DB::table('branches_to_analyst')
                ->select('branches_to_analyst.*')
                ->whereRaw("analyst_id=$analystid AND status=1")
                ->pluck("branch_id")->toArray();
        
          
           /* $arrOtherAllocatedBranches = DB::table('branches_to_analyst')
                    ->select('branches_to_analyst.*','employee.first_name as first_name','employee.alias_name as alias_name')
                    ->join('employees as employee', 'employee.id', '=', 'branches_to_analyst.analyst_id')
                    ->whereRaw("analyst_id!=$analystid AND status=1")
                    ->pluck("branch_id","first_name","alias_name")->toArray();
            */
           
             $OtherAllocatedBranch = DB::table('branches_to_analyst')
                    ->select('branches_to_analyst.*','employees.first_name as first_name','employees.last_name as alias_name')
                    ->join('employees', 'employees.id', '=', 'branches_to_analyst.analyst_id')
                    ->whereRaw("analyst_id!=$analystid AND branches_to_analyst.status=1")
                    ->get();
             
              $arrOtherAllocatedBranches=array();
          
               for($i=0;$i<count($OtherAllocatedBranch);$i++){
                   $name=$OtherAllocatedBranch[$i]->first_name.' '.$OtherAllocatedBranch[$i]->alias_name;
               $arrOtherAllocatedBranches[$OtherAllocatedBranch[$i]->branch_id]= $name;
          
               }
              
             
            $arrReturn = array();
            foreach ($branches as $objBranch) {
                $strChkProperty = '';
                $strStatus = '';
                $empName='';
                if(in_array($objBranch->id, $analystbranches)){
                    $strChkProperty = "checked";
                    $strStatus = "Assigned";
                    $empName="You";
                }else if(array_key_exists($objBranch->id, $arrOtherAllocatedBranches)){
                    $strChkProperty = "disabled";
                    $strStatus = "Allocated";
                    $empName=$arrOtherAllocatedBranches[$objBranch->id];
                }else{
                    $strStatus = "Free";
                }
                
                $arrReturn[]= array(
                    "id" => $objBranch->id,
                    "branch_code" => $objBranch->branch_code,
                    "name" => $objBranch->name,
                    "strChkProperty" => $strChkProperty,
                    "strStatus" => $strStatus,
                    "empName" => $empName,
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
    
    public function store() {
        try {
            
            $branchid = Input::get('branchid');
            $analystid = Input::get('analystid');
            $status = Input::get('status');
            
            $saveddata = DB::table('branches_to_analyst')
                        ->select('branches_to_analyst.*')
                        ->whereRaw("branch_id=$branchid AND analyst_id=$analystid")
                        ->first();
            
             if(count($saveddata)==0){
                $analystmodel = new Branches_to_analyst();
                $analystmodel->branch_id = $branchid;
                $analystmodel->analyst_id = $analystid;
                $analystmodel->status = $status;
                $analystmodel->save();
                $strMsg = "Allocated";
             }else{
                 if($saveddata->status == 0){
                     $branchupdate = DB::table('branches_to_analyst')
                        ->whereRaw("branch_id=$branchid AND analyst_id=$analystid")
                        ->update(['status' => 1]);
                     
                     $strMsg = "Allocated";
                 }else{
                     $branchupdate = DB::table('branches_to_analyst')
                        ->whereRaw("branch_id=$branchid AND analyst_id=$analystid")
                        ->update(['status' => 0]);
                     
                     $strMsg = "Deallocated";
                 }
             }
            
            Toastr::success("Branch $strMsg Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

}
