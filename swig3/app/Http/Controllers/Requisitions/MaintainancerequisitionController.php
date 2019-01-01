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
use App\Models\Requisition_items;
use App\Models\Requisitionitemhistory;
use App\Models\Requisitiondocuments;
use App\Services\Commonfunctions;
use Customhelper;
use Exception;
use DB;
use PDF;

class MaintainancerequisitionController extends Controller {
    
    public function add(){
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $common = new Commonfunctions();
            $requisitioncode = $common->generateRequisitionCode();
            
            $branches = DB::table('master_resources')
                    ->select('id','name','branch_code')
                    ->where('resource_type','=','BRANCH')
                    ->where('status','=',1)
                    ->get();
            
            return view('requisitions/maintainance_requisition/add',array('requisitioncode'=>$requisitioncode,'branches'=>$branches));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function store(){ 
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $created_by = Session::get('login_id');
            }
            
            $input = Input::all();
            
            $arraData = $input['arrData'];
            $arrData = json_decode($arraData);
            
            $subfolder = "requisitiondocs";
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            
            $doc_url = '';
            if (Input::hasFile('req_doc')) {
                $req_doc = Input::file('req_doc');
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
            
            $arrApprover = $common->getNextLevelApprover("Maintainance Requisition",1,$created_by);
            
            ///////////////////// insert in to requisition ///////////////////////
            $reqmodel = new Requisition();
            $reqmodel->requisition_type = $arrApprover['reqtypeid'];
            $reqmodel->company_id = $company_id;
            $reqmodel->requisition_code = $requisitioncode;
            $reqmodel->title = $arrData->title;
            $reqmodel->branch_id = $arrData->centerSelect;
            $reqmodel->description = $arrData->description;
            $reqmodel->total_price = $arrData->price;
            $reqmodel->outstanding_amount = $arrData->price;
            $reqmodel->created_by = $created_by;
            $reqmodel->next_level = 1;
            $reqmodel->next_approver_id = $arrApprover['approver_id'];
            $reqmodel->is_settled = 2;
            $reqmodel->convert_to_payment = 2;
            $reqmodel->payment_generated = 2;
            $reqmodel->status = 1;
            $reqmodel->save();
            
            $requisition_id = $reqmodel->id;
            
             ///////////////////// insert in to requisition history ///////////////////////
            $reqhistmodel = new Requisitionhistory();
            $reqhistmodel->requisition_id = $requisition_id;
            $reqhistmodel->requisition_code = $requisitioncode;
            $reqhistmodel->title = $arrData->title;
            $reqhistmodel->description = $arrData->description;
            $reqhistmodel->total_price = $arrData->price;
            $reqhistmodel->created_by = $created_by;
            $reqhistmodel->next_level = 1;
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = $arrApprover['status'];
            $reqhistmodel->save();
            
            if($doc_url!=''){
                $reqdocmodel = new Requisitiondocuments();
                $reqdocmodel->requisition_id = $requisition_id;
                $reqdocmodel->level = 0;
                $reqdocmodel->created_by = $created_by;
                $reqdocmodel->doc_url = $doc_url;
                $reqdocmodel->save();
            }
            
            /*------------Send notification----------------*/
            $title = $requisitioncode." : ".$arrData->title;
            $to=$arrApprover['approver_id'];
            $result=$common->notifyNextActionTaker("Maintainance Requisition",$title,$to,$requisition_id,1);
            
            Toastr::success('Requisition Made Successfully !', $title = null, $options = []);
            return 1;
            
        } catch (Exception $ex) {
                        
            if($ex->getMessage()=="No_hierarchy"){
                Toastr::error('Requisition Hierarchy Not Set!', $title = null, $options = []);
            }else{
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            }
            return -1;
        }
    }
    
    public function get_cost_center(){
        try {
            $center = Input::get('center');
            if($center=="Staff House"){
                $center="STAFF_HOUSE";
            }
            
            $centers = DB::table('master_resources')
                    ->select('id','name','branch_code')
                    ->where('resource_type','=',$center)
                    ->where('status','=',1)
                    ->get();
            
            return $centers;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/outbox');
        }
    }
    
    public function view($id) {
        try{
            $id = \Crypt::decrypt($id);
            
            $pageData = array();
            $requisitiondata = DB::table('requisition')
                    ->select('requisition.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"),'master_resources.name','master_resources.branch_code','master.name as job_position','employees.username as empcode')
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->leftjoin('master_resources','master_resources.id','=','requisition.branch_id')
                    ->leftjoin('master_resources as master','master.id','=','employees.job_position')
                    ->where(['requisition.id' => $id])->first(); 
            
            $center_type = DB::table('master_resources')
                    ->select('resource_type')
                    ->where(['id' => $requisitiondata->branch_id])->first();
            $pageData['center_type'] = $center_type->resource_type;
            
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
      
            
            return view('requisitions/maintainance_requisition/view',array('pageData'=>$pageData,'documents'=>$documents));
            
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
                    ->select('requisition.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"),'master_resources.name')
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->leftjoin('master_resources','master_resources.id','=','employees.job_position')
                    ->where(['requisition.id' => $id])->first();
            
            $center_type = DB::table('master_resources')
                    ->select('resource_type')
                    ->where(['id' => $requisitiondata->branch_id])->first();
            $pageData['center_type'] = $center_type->resource_type;
    
            $pageData['action_takers'] = DB::table('requisition_activity as req_a')
                    ->select('req_a.actor_id','req_a.created_at','req_a.action','req_a.comments', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"),DB::raw("case when action=1 then 'Approved' when action=3 then 'Rejected' end as action"))
                    ->leftjoin('employees as empl','req_a.actor_id', '=', 'empl.id')
                    ->where('req_a.requisition_id', '=', $id)->where('req_a.status','=', 1)->get();
            
            $documents = DB::table('requisition_docs')
                    ->select('requisition_docs.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('employees', 'requisition_docs.created_by','=','employees.id')
                    ->where(['requisition_docs.requisition_id' => $id])->get();
                        
            $pageData['branches'] = DB::table('master_resources')
                    ->select('id','name','branch_code')
                    ->where('resource_type','=',$center_type->resource_type)
                    ->where('status','=',1)
                    ->get();
            
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
            
            return view('requisitions/maintainance_requisition/action_view',array('pageData'=>$pageData,'isactiontaken'=>$isactiontaken,'documents'=>$documents)); 
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
            
            $input = Input::all();
            
            $arraData = $input['arrData'];
            $arrData = json_decode($arraData);
            
            $common = new Commonfunctions();
            $requisition_id = $arrData->requisition_id;
            
            $validatemsg=$common->checkrequisitionvalidations($requisition_id);
            
            if($validatemsg!="success"){
                Toastr::error($validatemsg, $title = null, $options = []);
                return 1;
            }
            
            $subfolder = "requisitiondocs";
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            
            $doc_url = '';
            if (Input::hasFile('req_doc')) {
                $req_doc = Input::file('req_doc');
                $extension = time() . '.' . $req_doc->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world.'/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($req_doc), 'public');
                $doc_url = Storage::disk('s3')->url($filePath);
            }         
            
            $requisition_data = DB::table('requisition')
                    ->select('requisition.*')
                    ->where(['id' => $requisition_id])->first();

            $next_level = $requisition_data->next_level + 1;
            
            $arrApprover = $common->getNextLevelApprover("Maintainance Requisition",$next_level,$requisition_data->created_by);
            
            ///////////////////// modify requisition ///////////////////////
            DB::table('requisition')
                ->where(['id' => $requisition_id])
                ->update([
                            'title' => $arrData->title,
                            'description' => $arrData->description,
                            'total_price' => $arrData->amount,
                            'outstanding_amount' => $arrData->amount,
                            'edited_by' => $login_id,
                            'next_level' => $arrApprover['next_level'],
                            'next_approver_id' => $arrApprover['approver_id'],
                            'status' => $arrApprover['status'],
                        ]);
            
//            if($arrApprover['status']==4 && $arrApprover['dopayment']==1){
//                $result=$common->convertToPaymentAdvice($requisition_id);
//            }
            
            ///////////////////// insert in to requisition history ///////////////////////
            $reqhistmodel = new Requisitionhistory();
            $reqhistmodel->requisition_id = $requisition_id;
            $reqhistmodel->requisition_code = $arrData->requisition_code;
            $reqhistmodel->title = $arrData->title;
            $reqhistmodel->description = $arrData->description;
            $reqhistmodel->total_price = $arrData->amount;
            $reqhistmodel->created_by = $requisition_data->created_by;
            $reqhistmodel->edited_by = $login_id;
            $reqhistmodel->next_level = $arrApprover['next_level'];
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = $arrApprover['status'];
            $reqhistmodel->save();
            
            
            $comments=$arrData->comments;
            
            $result=$common->saveRequisitionActivity($arrData->requisition_code,$requisition_id,1,$comments,$login_id);
            
            if($doc_url!=''){
                $reqdocmodel = new Requisitiondocuments();
                $reqdocmodel->requisition_id = $requisition_id;
                $reqdocmodel->level = $requisition_data->next_level;
                $reqdocmodel->created_by = $login_id;
                $reqdocmodel->doc_url = $doc_url;
                $reqdocmodel->save();
            }
            
            /*------------Send notification----------------*/   
            $title = $arrData->requisition_code." : ".$arrData->title;
            if($arrApprover['approver_id']!=null){
                $to=$arrApprover['approver_id'];
                $result=$common->notifyNextActionTaker("Maintainance Requisition",$title,$to,$requisition_id,2);
            }

            $to1=$requisition_data->created_by;
            $result=$common->notifyCreatedBy("Maintainance Requisition",$title,$to1,$requisition_id,1);
           
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
            $result=$common->notifyCreatedBy("Maintainance Requisition",$title,$to,$requisition_id,2);
            
            Toastr::success('Requisition Rejected Successfully !', $title = null, $options = []);
            return 1;
            
            } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
}