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

class AdvancepaymentrequisitionController extends Controller {
    
    public function add() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $suppliers = DB::table('ac_general_ledgers')
                    ->select('ac_general_ledgers.name','ac_general_ledgers.code','ac_general_ledgers.id','ac_general_ledgers.alias_name')
                    ->where(['ac_general_ledgers.status' => 1])
                    ->orderby('ac_general_ledgers.name', 'ASC')
                    ->get();
            
            $common = new Commonfunctions();
            $requisitioncode = $common->generateRequisitionCode();
            
            return view('requisitions/advance_payment_requisition/add', array('suppliers'=>$suppliers,'requisitioncode'=>$requisitioncode));
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
            
            $arrApprover = $common->getNextLevelApprover("Advance Payment Requisition",1,$created_by);

            ///////////////////// insert in to requisition ///////////////////////
            $reqmodel = new Requisition();
            $reqmodel->requisition_type = $arrApprover['reqtypeid'];
            $reqmodel->company_id = $company_id;
            $reqmodel->requisition_code = $requisitioncode;
            $reqmodel->party_id = $created_by;
            //$reqmodel->party_type = $arrData->party_type;
            $reqmodel->party_type = 'Employee';
            $reqmodel->isallproducts = $arrData->isallproducts;
            $reqmodel->title = $arrData->title;
            $reqmodel->description = $arrData->description;
            $reqmodel->total_price = $arrData->total_price;
            $reqmodel->outstanding_amount = $arrData->total_price;
            $reqmodel->created_by = $created_by;
            $reqmodel->next_level = 1;
            $reqmodel->next_approver_id = $arrApprover['approver_id'];
            $reqmodel->is_settled = 2;
            $reqmodel->convert_to_payment = 2;
            $reqmodel->status = 1;
            $reqmodel->general_ledger = $arrData->supplier_id;
            $reqmodel->save();
            
            $requisition_id = $reqmodel->id;
            
            ///////////////////// insert in to requisition history ///////////////////////
            $reqhistmodel = new Requisitionhistory();
            $reqhistmodel->requisition_id = $requisition_id;
            $reqhistmodel->requisition_code = $requisitioncode;
            $reqhistmodel->party_id = $arrData->supplier_id;
            $reqhistmodel->isallproducts = $arrData->isallproducts;
            $reqhistmodel->title = $arrData->title;
            $reqhistmodel->description = $arrData->description;
            $reqhistmodel->total_price = $arrData->total_price;
            $reqhistmodel->outstanding_amount = $arrData->total_price;
            $reqhistmodel->created_by = $created_by;
            $reqhistmodel->next_level = 1;
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = 1;
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
           $result=$common->notifyNextActionTaker("Advance Payment Requisition",$title,$to,$requisition_id,1);
        
            Toastr::success('Requisition Made Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $ex) {
            if($ex->getMessage()=="No_hierarchy"){
                Toastr::error('Requisition Hierarchy Not Set!', $title = null, $options = []);
            }else{
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            }
            return -1;
        }
    }


    public function view($id) {
         try{
            $id = \Crypt::decrypt($id);
            
            $pageData = array();
           
             $requisitiondata = DB::table('requisition')
                    ->select('requisition.*','ac_general_ledgers.name as first_name','ac_general_ledgers.alias_name','ac_general_ledgers.code',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"),'master_resources.name as jobposition','employees.username as empcode')
                    ->leftjoin('ac_accounts', function($join) {
                            $join->on('requisition.party_id', '=', 'ac_accounts.type_id');
                            $join->on('requisition.party_type', '=', 'ac_accounts.type');
                    })
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->leftjoin('ac_general_ledgers', 'requisition.general_ledger','=','ac_general_ledgers.id')
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
            
          return view('requisitions/advance_payment_requisition/view',array('pageData'=>$pageData,'documents'=>$documents));
            
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
                    ->select('requisition.*','ac_accounts.first_name','ac_accounts.alias_name','ac_accounts.code',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('ac_accounts', function($join) {
                            $join->on('requisition.party_id', '=', 'ac_accounts.type_id');
                            $join->on('requisition.party_type', '=', 'ac_accounts.type');
                    })
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->where(['requisition.id' => $id])->first();
                    
                    
           //$arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
            $strQuarter = $requisitiondata->created_at; 
            $arrQua = explode(" ", $strQuarter); 
            $date = $arrQua[0]; 
            $dat = explode("-", $date); 
            $Year = $dat[0];   
            $Month = $dat[1];
          //  $Quarter = $arrMonthQuarter["$Month"];
            $type="General Ledger";
            
            
            //$budgetdata = $common->getGeneralLedgerdataQuarter($Year,$Month,$type,$requisitiondata->party_id);
            
           $type1="Employee";
        $budgetdata = $common->getBudgetInfo($Year,$type,$requisitiondata->general_ledger);
        $usedData = $common->usedPriceInfoOfLedgerQuarter($Year,$Month,$type1,$requisitiondata->general_ledger);
        
          if(count($budgetdata)>0){
                $format_balance = Customhelper::numberformatter($budgetdata->price-$usedData);
                $format_used = Customhelper::numberformatter($usedData);
                $budgetdata->format_initial=Customhelper::numberformatter($budgetdata->price);
                $budgetdata->format_balance=$format_balance;
                $budgetdata->format_used=$format_used;
                $budgetdata->usedData=$usedData;
                $budgetdata->pending=($budgetdata->price-$usedData);
            }else{
                $budgetdata=array();
            }
            
         
            
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
            
            return view('requisitions/advance_payment_requisition/action_view',array('pageData'=>$pageData,'isactiontaken'=>$isactiontaken,'suppliers'=>$suppliers,'budgetdata'=>$budgetdata,'usedData'=>$usedData,'documents'=>$documents)); 
            
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
            
            $arrApprover = $common->getNextLevelApprover("Advance Payment Requisition",$next_level,$requisition_data->created_by);

           
            ///////////////////// modify requisition ///////////////////////
            DB::table('requisition')
                ->where(['id' => $requisition_id])
                ->update([
                           // 'party_id' => $arrData->supplier_id,
                           // 'party_id' => $arrData->supplier_id,
                           'title' => $arrData->title,
                            'description' => $arrData->description,
                            'total_price' => $arrData->amount,
                            'outstanding_amount' => $arrData->amount,
                            'edited_by' => $login_id,
                            'next_level' => $arrApprover['next_level'],
                            'next_approver_id' => $arrApprover['approver_id'],
                            'status' => $arrApprover['status'],
                        ]);
           
            if($arrApprover['status']==4 && $arrApprover['dopayment']==1){
                $result=$common->convertToPaymentAdvice($requisition_id);
            }
            
            ///////////////////// insert in to requisition history ///////////////////////
            $reqhistmodel = new Requisitionhistory();
            $reqhistmodel->requisition_id = $requisition_id;
            $reqhistmodel->requisition_code = $arrData->requisition_code;
            //$reqhistmodel->party_id = $arrData->supplier_id;
            $reqhistmodel->party_id =$requisition_data->created_by;
            
            $reqhistmodel->title = $arrData->title;
            $reqhistmodel->description = $arrData->description;
            $reqhistmodel->total_price = $arrData->amount;
            $reqhistmodel->created_by = $requisition_data->created_by;
            $reqhistmodel->edited_by = $login_id;
            $reqhistmodel->next_level = $arrApprover['next_level'];
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = $arrApprover['status'];
            $reqhistmodel->save();
         
            $result=$common->saveRequisitionActivity($arrData->requisition_code,$requisition_id,1,$arrData->comments,$login_id);
            
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
                $result=$common->notifyNextActionTaker("Advance Payment Requisition",$title,$to,$requisition_id,2);
            }

            $to1=$requisition_data->created_by;
            $result=$common->notifyCreatedBy("Advance Payment Requisition",$title,$to1,$requisition_id,1);
           
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
            $result=$common->notifyCreatedBy("Advance Payment Requisition",$title,$to,$requisition_id,2);
            
            Toastr::success('Requisition Rejected Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
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