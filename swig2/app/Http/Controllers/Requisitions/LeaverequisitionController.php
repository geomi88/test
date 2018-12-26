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
use App\Helpers\CategoryHierarchy;
use App\Models\Requisition;
use App\Models\Requisitionhistory;
use App\Models\Requisition_activity;
use App\Models\Requisitiondocuments;
use App\Models\Requisition_items;
use App\Models\Requisitionitemhistory;
use App\Services\Commonfunctions;
use Customhelper;
use Exception;
use DB;
use PDF;

class LeaverequisitionController extends Controller {
    
    public function add() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

           
            $common = new Commonfunctions();
            $requisitioncode = $common->generateRequisitionCode();
            
            return view('requisitions/leave_requisition/add', array('requisitioncode'=>$requisitioncode));
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

            if (Session::get('login_id')) {
                $created_by = Session::get('login_id');
            }
            
            $arraData = Input::get('arrData');
            $arrData = json_decode($arraData);
            
            if ($arrData->leave_from != '') {
                    $leave_from = explode('-', $arrData->leave_from);
                    $leave_from = $leave_from[2] . '-' . $leave_from[1] . '-' . $leave_from[0];
                }
                if ($arrData->leave_to  != '') {
                    $leave_to = explode('-', $arrData->leave_to);
                    $leave_to = $leave_to[2] . '-' . $leave_to[1] . '-' . $leave_to[0];
                }
            
            $subfolder = "leave_requistition_docs";
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            
            $doc_url = '';
            if (Input::hasFile('attach_document')) {
                $req_doc = Input::file('attach_document');
                $extension = time() . '.' . $req_doc->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world.'/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($req_doc), 'public');
                $doc_url = Storage::disk('s3')->url($filePath);
            }
            
            $common = new Commonfunctions();
            $requisitioncode=$arrData->requisition_code;
            
            $duplicatecounts = DB::table('requisition')->whereRaw("requisition_code='$requisitioncode'")->count();
            if($duplicatecounts>0){
                $requisitioncode = $common->generateRequisitionCode();
            }
            
            $arrApprover = $common->getNextLevelApprover("Leave Requisition",1,$created_by);

            ///////////////////// insert in to requisition ///////////////////////
            $reqmodel = new Requisition();
            $reqmodel->requisition_type = $arrApprover['reqtypeid'];
            $reqmodel->company_id = $company_id;
            $reqmodel->requisition_code = $requisitioncode;
          //  $reqmodel->party_id = $arrData->supplier_id;
           // $reqmodel->isallproducts = $arrData->isallproducts;
            $reqmodel->title = $arrData->title;
            $reqmodel->description = $arrData->description;
          //  $reqmodel->total_price = $arrData->total_price;
          //  $reqmodel->outstanding_amount = $arrData->total_price;
            $reqmodel->created_by = $created_by;
            $reqmodel->next_level = 1;
            $reqmodel->next_approver_id = $arrApprover['approver_id'];
            $reqmodel->is_settled = 2;
            $reqmodel->convert_to_payment = 2;
            $reqmodel->status = 1;
            $reqmodel->leave_from = $leave_from;
            $reqmodel->leave_to = $leave_to;
            $reqmodel->leave_length =$arrData->leave_length;
            $reqmodel->save();
            
            $requisition_id = $reqmodel->id;
            
            ///////////////////// insert in to requisition history ///////////////////////
            $reqhistmodel = new Requisitionhistory();
            $reqhistmodel->requisition_id = $requisition_id;
            $reqhistmodel->requisition_code = $requisitioncode;
          //  $reqhistmodel->party_id = $arrData->supplier_id;
          //  $reqhistmodel->isallproducts = $arrData->isallproducts;
            $reqhistmodel->title = $arrData->title;
            $reqhistmodel->description = $arrData->description;
           // $reqhistmodel->total_price = $arrData->total_price;
           // $reqhistmodel->outstanding_amount = $arrData->total_price;
            $reqhistmodel->created_by = $created_by;
            $reqhistmodel->next_level = 1;
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = 1;
            $reqhistmodel->leave_from = $leave_from;
            $reqhistmodel->leave_to = $leave_to;
             $reqhistmodel->leave_length  =$arrData->leave_length;
           $reqhistmodel->save();
           
        /* Adding Doc file path to database */  
           
           if($doc_url!=''){
                $reqdocmodel = new Requisitiondocuments();
                $reqdocmodel->requisition_id = $requisition_id;
                $reqdocmodel->level = 0;
                $reqdocmodel->created_by = $created_by;
                $reqdocmodel->doc_url = $doc_url;
                $reqdocmodel->save();
            }
          
        /*  Ends  */ 
            /*------------Send notification----------------*/
            $title = $requisitioncode." : ".$arrData->title;
            $to=$arrApprover['approver_id'];
           $result=$common->notifyNextActionTaker("Leave Requisition",$title,$to,$requisition_id,1);
        
            Toastr::success('Requisition Made Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $ex) {
            if($ex->getMessage()=="No_hierarchy"){
                Toastr::error('Requisition Hierarchy Not Set!', $title = null, $options = []);
            }else{
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            }
           return $ex;
        }
    }


    public function view($id) {
         try{
            $id = \Crypt::decrypt($id);
            
            $pageData = array();
           
             $requisitiondata = DB::table('requisition')
                    ->select('requisition.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"),'master_resources.name as jobposition','employees.username as empcode')
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->leftjoin('master_resources', 'employees.job_position','=','master_resources.id')
                    ->where(['requisition.id' => $id])->first();
           
            $pageData['action_takers'] = DB::table('requisition_activity as req_a')
                    ->select('req_a.created_at','req_a.action','req_a.comments', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"),DB::raw("case when action=1 then 'Approved' when action=3 then 'Rejected' end as action"))
                    ->leftjoin('employees as empl','req_a.actor_id', '=', 'empl.id')
                    ->where('req_a.requisition_id', '=', $id)->where('req_a.status','=', 1)->get();
           
            $documents = DB::table('requisition_docs')
                    ->select('requisition_docs.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('employees', 'requisition_docs.created_by','=','employees.id')
                    ->where(['requisition_docs.requisition_id' => $id])->get();
            
            
            $next_action_takers_list = array();
            if($requisitiondata->status!=5){
                $pending_actions = array();
                if($requisitiondata->next_level != NULL){
                $pending_actions = DB::table('requisition_hierarchy as rh')
                        ->select('rh.approver_type', 'rh.approver_id', 'rh.level')
                        ->where('rh.requisition_type_id','=', $requisitiondata->requisition_type)
                        ->where('rh.level','>=',$requisitiondata->next_level)
                        ->where('rh.status','=', 1)
                        ->orderby('rh.level','ASC')
                        ->get();
                }

                $topmanager_details= DB::table("employees as empl")
                        ->select('empl.top_manager_id as top_manager_id',DB::raw("concat(tm.first_name, ' ', tm.alias_name) as name"))
                        ->join('employees as tm','empl.top_manager_id','=','tm.id')
                        ->where('empl.id','=',$requisitiondata->created_by)
                        ->first();

                foreach($pending_actions as $pending_action){  

                    if($pending_action->approver_type=='TOP_MANAGER'){ // Top manager details we have already fetch on top
                         $pending_actions_takers['name'] = $topmanager_details->name;
                         $pending_actions_takers['id'] = $topmanager_details->top_manager_id;
                         $pending_actions_takers['action'] = "Waiting";
                    }else if($pending_action->approver_type == 'EMPLOYEE'){ 
                        $emp_data = DB::table('employees as empl')
                                ->select(DB::raw("concat(empl.first_name, ' ', empl.alias_name) as name"),'empl.id as empl_id')
                                ->where('empl.id', '=',$pending_action->approver_id)->first();

                        $pending_actions_takers['name'] = $emp_data->name;
                        $pending_actions_takers['id'] = $emp_data->empl_id;
                        $pending_actions_takers['action'] = "Waiting";
                    }

                     array_push($next_action_takers_list, $pending_actions_takers);
                }
            }
            $pageData['next_action_takers_list'] = $next_action_takers_list; 
            $pageData['requisitiondata'] = $requisitiondata;
            
            return view('requisitions/leave_requisition/view',array('pageData'=>$pageData,'documents'=>$documents));
            
     } catch (\Exception $e) {
       
          Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
           return Redirect::to('requisitions/outbox');
        }
    }

     public function action_view($id) {
        try {
            $id = \Crypt::decrypt($id);
            $common = new Commonfunctions();
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $pageData = array();
           $requisitiondata = DB::table('requisition')
                    ->select('requisition.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->where(['requisition.id' => $id])->first();
            
            $pageData['action_takers'] = DB::table('requisition_activity as req_a')
                    ->select('req_a.actor_id','req_a.created_at','req_a.action','req_a.comments', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"),DB::raw("case when action=1 then 'Approved' when action=3 then 'Rejected' end as action"))
                    ->leftjoin('employees as empl','req_a.actor_id', '=', 'empl.id')
                    ->where('req_a.requisition_id', '=', $id)->where('req_a.status','=', 1)->get();
            
            $suppliers = DB::table('ac_general_ledgers')
                    ->select('ac_general_ledgers.name','ac_general_ledgers.code','ac_general_ledgers.id','ac_general_ledgers.alias_name')
                    ->where(['ac_general_ledgers.status' => 1])
                    ->orderby('ac_general_ledgers.name', 'ASC')
                    ->get();
            $documents = DB::table('requisition_docs')
                    ->select('requisition_docs.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('employees', 'requisition_docs.created_by','=','employees.id')
                    ->where(['requisition_docs.requisition_id' => $id])->get();
            
            
            $next_action_takers_list = array();
            if($requisitiondata->status!=5){
                $pending_actions = array();
                if($requisitiondata->next_level != NULL){
                $pending_actions = DB::table('requisition_hierarchy as rh')
                        ->select('rh.approver_type', 'rh.approver_id', 'rh.level')
                        ->where('rh.requisition_type_id','=', $requisitiondata->requisition_type)
                        ->where('rh.level','>=',$requisitiondata->next_level)
                        ->where('rh.status','=', 1)
                        ->orderby('rh.level','ASC')
                        ->get();
                }

                $topmanager_details= DB::table("employees as empl")
                        ->select('empl.top_manager_id as top_manager_id',DB::raw("concat(tm.first_name, ' ', tm.alias_name) as name"))
                        ->join('employees as tm','empl.top_manager_id','=','tm.id')
                        ->where('empl.id','=',$requisitiondata->created_by)
                        ->first();

                foreach($pending_actions as $pending_action){  

                    if($pending_action->approver_type=='TOP_MANAGER'){ // Top manager details we have already fetch on top
                         $pending_actions_takers['name'] = $topmanager_details->name;
                         $pending_actions_takers['id'] = $topmanager_details->top_manager_id;
                         $pending_actions_takers['action'] = "Waiting";
                    }else if($pending_action->approver_type == 'EMPLOYEE'){ 
                        $emp_data = DB::table('employees as empl')
                                ->select(DB::raw("concat(empl.first_name, ' ', empl.alias_name) as name"),'empl.id as empl_id')
                                ->where('empl.id', '=',$pending_action->approver_id)->first();

                        $pending_actions_takers['name'] = $emp_data->name;
                        $pending_actions_takers['id'] = $emp_data->empl_id;
                        $pending_actions_takers['action'] = "Waiting";
                    }

                     array_push($next_action_takers_list, $pending_actions_takers);
                }
            }
            
            $pageData['requisitiondata'] = $requisitiondata;
            $pageData['next_action_takers_list'] = $next_action_takers_list;
                       
            $isactiontaken=$common->showactionbotton($id);
            
            return view('requisitions/leave_requisition/action_view',array('pageData'=>$pageData,'isactiontaken'=>$isactiontaken,'suppliers'=>$suppliers,'documents'=>$documents)); 
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/inbox');
        }
    }
    
   
    public function approve_requisition() {
       try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $arraData = Input::get('arrData');
            $arrData = json_decode($arraData);
            
            $common = new Commonfunctions();
            $requisition_id = $arrData->requisition_id;
            
            $validatemsg=$common->checkrequisitionvalidations($requisition_id);
            
            if($validatemsg!="success"){
                Toastr::error($validatemsg, $title = null, $options = []);
                return 1;
            }
            
            $requisition_data = DB::table('requisition')
                    ->select('requisition.*')
                    ->where(['id' => $requisition_id])->first();

            $next_level = $requisition_data->next_level + 1;
            
            $arrApprover = $common->getNextLevelApprover("Leave Requisition",$next_level,$requisition_data->created_by);

           
            
           
            ///////////////////// modify requisition ///////////////////////
            DB::table('requisition')
                ->where(['id' => $requisition_id])
                ->update([
                           
                            'title' => $arrData->title,
                            'description' => $arrData->description,
                            'edited_by' => $login_id,
                            'next_level' => $arrApprover['next_level'],
                            'next_approver_id' => $arrApprover['approver_id'],
                            'status' => $arrApprover['status'],
                            'leave_from' =>$arrData->leave_from,
                            'leave_to' => $arrData->leave_to,
                            'leave_length' =>$arrData->leave_length,
                        ]);
           
//            if($arrApprover['status']==4 && $arrApprover['dopayment']==1){
//                $result=$common->convertToPaymentAdvice($requisition_id);
//            }
//            
            ///////////////////// insert in to requisition history ///////////////////////
            $reqhistmodel = new Requisitionhistory();
            $reqhistmodel->requisition_id = $requisition_id;
            $reqhistmodel->requisition_code = $arrData->requisition_code;
            //$reqhistmodel->party_id = $arrData->supplier_id;
            
            $reqhistmodel->title = $arrData->title;
            $reqhistmodel->description = $arrData->description;
          //  $reqhistmodel->total_price = $arrData->amount;
            $reqhistmodel->created_by = $requisition_data->created_by;
            $reqhistmodel->edited_by = $login_id;
            $reqhistmodel->next_level = $arrApprover['next_level'];
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = $arrApprover['status'];
            $reqhistmodel->leave_from = $arrData->leave_from;
            $reqhistmodel->leave_to = $arrData->leave_to;
            $reqhistmodel->leave_length  =$arrData->leave_length;
            $reqhistmodel->save();
         
            $comments=$arrData->comments;
            
            
           
            $result=$common->saveRequisitionActivity($arrData->requisition_code,$requisition_id,1,$comments,$login_id);
            
            /*------------Send notification----------------*/   
            $title = $arrData->requisition_code." : ".$arrData->title;
            if($arrApprover['approver_id']!=null){
                $to=$arrApprover['approver_id'];
                $result=$common->notifyNextActionTaker("Leave Requisition",$title,$to,$requisition_id,2);
            }

            $to1=$requisition_data->created_by;
            $result=$common->notifyCreatedBy("Leave Requisition",$title,$to1,$requisition_id,1);
           
            Toastr::success('Requisition Approved Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
   
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    
    

    public function reject_requisition() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $common = new Commonfunctions();
            $requisition_id = Input::get('requisition_id');
            $requisition_code = Input::get('requisition_code');
            $reject_reason = Input::get('reject_reason');
            
             $validatemsg=$common->checkrequisitionvalidations($requisition_id);
            
            if($validatemsg!="success"){
                Toastr::error($validatemsg, $title = null, $options = []);
                return 1;
            }
            
            $result=$common->saveRequisitionActivity($requisition_code,$requisition_id,3,$reject_reason,$login_id);
            
            DB::table('requisition')
                    ->where('id', $requisition_id)
                    ->update(['status' => 5, 'next_approver_id' => NULL, 'next_level' => NULL]);
            
            /*------------Send notification----------------*/   
            $requisition = DB::table('requisition')
                    ->select('requisition.created_by', 'requisition.title')
                    ->where(['id' => $requisition_id])->first();
            
            $title = $requisition_code." : ".$requisition->title;
            $to=$requisition->created_by;
            $result=$common->notifyCreatedBy("Leave Requisition",$title,$to,$requisition_id,2);
            
            Toastr::success('Requisition Rejected Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
        // echo   $e->getMessage();
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function getinventorydata() {
        try {
            $productid = Input::get('productid');
            $arrData = array();
            
            $inventorydata = DB::table('inventory')
                    ->select('inventory.*','units.name as primaryunitname')
                    ->leftjoin('units', 'inventory.primary_unit','=','units.id')
                    ->whereRaw("inventory.status=1 AND inventory.id=$productid")
                    ->first();
            
            $altunits = DB::table('inventory_alternate_units')
                    ->select('inventory_alternate_units.*','units.name as altunitname')
                    ->leftjoin('units', 'inventory_alternate_units.unit_id','=','units.id')
                    ->whereRaw("inventory_alternate_units.inventory_item_id=$productid")
                    ->get();
            
            $common = new Commonfunctions();
            $branchStock = $common->getBranchInventoryStock($productid);
            $warehosueStock = $common->getWarehouseInventoryStock($productid);
            $type = 'Inventory';
            $year = date('Y');
            $month = date("m");
            $quarter = '';
            if ($month < 4) {
                $quarter = 1;
            }
            if ($month > 3 && $month < 7) {
                $quarter = 2;
            }
            if ($month > 6 && $month < 10) {
                $quarter = 3;
            }
            if ($month > 9) {
                $quarter = 4;
            }
            $budgetDetails = $common->getBudgetInfo($year,$quarter,$type,$productid);
            $usedBudget = $common->usedQuantityBudgetInfo($productid);
          //  $arrData=array('inventorydata'=>$inventorydata,'altunits'=>$altunits);
            $arrData=array('inventorydata'=>$inventorydata,'altunits'=>$altunits,'branchStock'=>$branchStock,'warehosueStock'=>$warehosueStock,'budgetDetails'=>$budgetDetails,'usedBudget'=>$usedBudget);
            
            return \Response::json($arrData);
        } catch (\Exception $e) {
            return -1;
        }
    }

}