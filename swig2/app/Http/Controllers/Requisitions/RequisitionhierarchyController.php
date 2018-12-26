<?php

namespace App\Http\Controllers\Requisitions;

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
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use App\Notifications\RequisitionNotification;
use App\Helpers\CategoryHierarchy;
use App\Models\Requisition_hierarchy;
use Customhelper;
use App\Services\Commonfunctions;
use DB;

class RequisitionhierarchyController extends Controller {

    public function add() {
        try {

            $requisitions = DB::table('requisition_types')
                        ->select('requisition_types.*')
                        ->whereRaw("requisition_types.status=1 or  requisition_types.status=2")
                        ->get();

           
            return view('requisitions/requisition_hierarchy/hierarchy', array('requisitions' => $requisitions));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function gethierarchy() {
        try {
            $requisitiontype = Input::get('requisitiontype');
            
            $data=array();
            if($requisitiontype){
                $hierarchy_list = DB::table('requisition_hierarchy as hier')
                        ->select('hier.*','emp.first_name','emp.username','m.name as job')
                        ->leftjoin('employees as emp', 'hier.approver_id','=','emp.id')
                        ->leftjoin('master_resources as m', 'emp.job_position','=','m.id')
                        ->whereRaw("hier.requisition_type_id=$requisitiontype AND hier.status=1")
                        ->orderby('hier.level','ASC')
                        ->get();
               
                
                 $requisition_details = DB::table('requisition_types')
                        ->select('requisition_types.*')
                        ->whereRaw("requisition_types.id=$requisitiontype")
                        ->first();
                $requisition_name=$requisition_details->name;
                
                foreach ($hierarchy_list as $value) {
                    if($value->approver_type=="TOP_MANAGER"){
                        $emp_id=-1;
                        $code='';
                        $name="Top Manager";
                        $approver_type="TOP_MANAGER";
                        $jobpos="Top Manager";
                    }else{
                        $emp_id=$value->approver_id;
                        $code=$value->username;
                        $name=$value->first_name;
                        $approver_type="EMPLOYEE";
                        $jobpos=$value->job;
                    }
                    $data[]=array("level"=>$value->level,"emp_id"=>$emp_id,"code"=>$code,"name"=>$name,"approver_type"=>$approver_type,"jobpos"=>$jobpos);
                }
            }
           
            $req_count = DB::table('requisition')
                ->whereRaw("requisition.status=1 AND requisition_type=$requisitiontype")->count();
            
            $arrReturn=array('hierarchy'=>$data,"req_count"=>$req_count,"requisition_name"=>$requisition_name);
            
            return \Response::json($arrReturn);
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }

    public function store() {
    try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            
            $common = new Commonfunctions();
            $requisitiontype = Input::get('requisitiontype');
            $arrLevelList = Input::get('arrLevelList');
            $arrLevelList = json_decode($arrLevelList);
            
            $requisitions_name = DB::table('requisition_types')
                        ->select('requisition_types.*')
                        ->whereRaw("requisition_types.id=$requisitiontype")
                        ->first();

            DB::table('requisition_hierarchy')
                    ->where(['requisition_type_id' => $requisitiontype])
                    ->update(['status'=>2]);
                    
            foreach ($arrLevelList as $level) {
                ///////////////////// insert in to requisition items ///////////////////////
                $reqlevelmodel = new Requisition_hierarchy();
                $reqlevelmodel->company_id = $company_id;
                $reqlevelmodel->requisition_type_id = $requisitiontype;
                $reqlevelmodel->level = $level->level;
                $reqlevelmodel->approver_type = $level->approver_type;
                if($level->emp_id==-1){
                    $reqlevelmodel->approver_id = null;
                }else{
                    $reqlevelmodel->approver_id = $level->emp_id;
                }
                
                $reqlevelmodel->save();
            }
            
             $req_hierarchy_details = DB::table('requisition_hierarchy')
                ->select('requisition_hierarchy.*')
                ->where(['requisition_type_id' => $requisitiontype, 'level' => 1, 'status' => 1])->first();
             
             if($requisitions_name->name=="Payment Advice"){
                 
                $req_hierarchy_details_payment = DB::table('requisition_hierarchy')
                    ->select('requisition_hierarchy.*')
                    ->where(['requisition_type_id' => $requisitiontype, 'level' => 2, 'status' => 1])->first();
               
                $revertRequisitions = $common->revertPaymentAdvice($requisitiontype,$req_hierarchy_details_payment);
                
                DB::table('requisition')
                    ->whereRaw("convert_to_payment=1 AND payment_generated=2 AND status=4")
                    ->update(['next_approver_id'=>$req_hierarchy_details->approver_id]);
                
            }else{
              
              $revertPaymentAdvice = $common->revertRequisition($requisitiontype,$req_hierarchy_details);
            }

            Toastr::success('Requisition Hierarchy Saved Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
         
           Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
           return -1;
       }
    }
    
   public function check_reqexist() {
        $requisitiontype = Input::get('requisitiontype');
        $req_count = DB::table('requisition')
                ->whereRaw("requisition.status=1 AND requisition_type=$requisitiontype")->count();
        return $req_count;
    }
}