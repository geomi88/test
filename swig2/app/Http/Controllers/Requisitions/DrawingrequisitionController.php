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
use Excel;

class DrawingrequisitionController extends Controller {
    
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
            
            return view('requisitions/drawing_requisition/add', array('suppliers'=>$suppliers,'requisitioncode'=>$requisitioncode));
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
            
            $arrApprover = $common->getNextLevelApprover("Owner Drawings Requisition",1,$created_by);

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
           $result=$common->notifyNextActionTaker("Owner Drawings Requisition",$title,$to,$requisition_id,1);
        
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
            
          return view('requisitions/drawing_requisition/view',array('pageData'=>$pageData,'documents'=>$documents));
            
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
            
            return view('requisitions/drawing_requisition/action_view',array('pageData'=>$pageData,'isactiontaken'=>$isactiontaken,'suppliers'=>$suppliers,'budgetdata'=>$budgetdata,'usedData'=>$usedData,'documents'=>$documents)); 
            
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
            
            $arrApprover = $common->getNextLevelApprover("Owner Drawings Requisition",$next_level,$requisition_data->created_by);

           
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
                $result=$common->notifyNextActionTaker("Owner Drawings Requisition",$title,$to,$requisition_id,2);
            }

            $to1=$requisition_data->created_by;
            $result=$common->notifyCreatedBy("Owner Drawings Requisition",$title,$to1,$requisition_id,1);
           
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
            $result=$common->notifyCreatedBy("Owner Drawings Requisition",$title,$to,$requisition_id,2);
            
            Toastr::success('Requisition Rejected Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function paymentadvicelist(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $requisitions = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*','fromdetails.first_name as fromname','fromdetails.alias_name as fromalias','todetails.first_name as toname','todetails.alias_name as toalias')
                    ->leftjoin('ac_accounts as fromdetails', function($join) {
                            $join->on('fromdetails.type_id', '=', 'ac_payment_advice.payment_from_id');
                            $join->on('fromdetails.type', '=', 'ac_payment_advice.from_ledger_type');
                    })
                    ->leftjoin('ac_accounts as todetails', function($join) {
                        $join->on('todetails.type_id', '=', 'ac_payment_advice.payment_to_id');
                        $join->on('todetails.type', '=', 'ac_payment_advice.to_ledger_type');
                    })
                    ->where('ac_payment_advice.is_owner_drawing_requsition','=',1)
                    ->whereRaw("ac_payment_advice.status IN (1,2,3)")
                    ->orderby('ac_payment_advice.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchpaidto = Input::get('searchpaidto');
                $searchpaidby = Input::get('searchpaidby');
                $sortpaidto = Input::get('sortpaidto');
                $sortpaidby = Input::get('sortpaidby');
                $sortordcode = Input::get('sortordcode');

                $status = Input::get('status');

            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortpaidby == '' && $sortordcode=='' && $sortpaidto== '') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*','fromdetails.first_name as fromname','fromdetails.alias_name as fromalias','todetails.first_name as toname','todetails.alias_name as toalias')
                        ->leftjoin('ac_accounts as fromdetails', function($join) {
                            $join->on('fromdetails.type_id', '=', 'ac_payment_advice.payment_from_id');
                            $join->on('fromdetails.type', '=', 'ac_payment_advice.from_ledger_type');
                        })
                        ->leftjoin('ac_accounts as todetails', function($join) {
                            $join->on('todetails.type_id', '=', 'ac_payment_advice.payment_to_id');
                            $join->on('todetails.type', '=', 'ac_payment_advice.to_ledger_type');
                        })
                        ->where('ac_payment_advice.is_owner_drawing_requsition','=',1)
                        ->whereRaw("ac_payment_advice.status IN (1,2,3)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '%$searchbycode%' ");
                        })
                        ->when($searchpaidby, function ($query) use ($searchpaidby) {
                            return $query->whereRaw("fromdetails.first_name like '$searchpaidby%' ");
                        })
                         ->when($searchpaidto, function ($query) use ($searchpaidto) {
                            return $query->whereRaw("todetails.first_name like '$searchpaidto%' ");
                        })
                         ->when($status, function ($query) use ($status) {
                            return $query->whereRaw("ac_payment_advice.status= $status ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
                        })
                        ->when($sortpaidto, function ($query) use ($sortpaidto) {
                            return $query->orderby('todetails.first_name', $sortpaidto);
                        }) 
                        ->when($sortpaidby, function ($query) use ($sortpaidby) {
                            return $query->orderby('fromdetails.first_name', $sortpaidby);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
               return view('requisitions/drawing_payments/result', array('requisitions' => $requisitions));
            }
            return view('requisitions/drawing_payments/list', array('requisitions' => $requisitions));
       } catch (\Exception $e) {
          
          
           Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
          return Redirect::to('requisitions');
        }
    }
    
    public function exportdata_paymentadvicelist() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchpaidto = Input::get('searchpaidto');
        $searchpaidby = Input::get('searchpaidby');
        $sortpaidto = Input::get('sortpaidto');
        $sortpaidby = Input::get('sortpaidby');
        $sortordcode = Input::get('sortordcode');

        $status = Input::get('status');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }

        $sortOrdDefault = '';
        if ($sortpaidby == '' && $sortordcode == '' && $sortpaidto == '') {
            $sortOrdDefault = 'DESC';
        }

        $requisitions = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*','fromdetails.first_name as fromname','fromdetails.alias_name as fromalias','todetails.first_name as toname','todetails.alias_name as toalias')
                        ->leftjoin('ac_accounts as fromdetails', function($join) {
                            $join->on('fromdetails.type_id', '=', 'ac_payment_advice.payment_from_id');
                            $join->on('fromdetails.type', '=', 'ac_payment_advice.from_ledger_type');
                        })
                        ->leftjoin('ac_accounts as todetails', function($join) {
                            $join->on('todetails.type_id', '=', 'ac_payment_advice.payment_to_id');
                            $join->on('todetails.type', '=', 'ac_payment_advice.to_ledger_type');
                        })
                        ->where('ac_payment_advice.is_owner_drawing_requsition','=',1)
                        ->whereRaw("ac_payment_advice.status IN (1,2,3)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '%$searchbycode%' ");
                        })
                        ->when($searchpaidby, function ($query) use ($searchpaidby) {
                            return $query->whereRaw("fromdetails.first_name like '$searchpaidby%' ");
                        })
                         ->when($searchpaidto, function ($query) use ($searchpaidto) {
                            return $query->whereRaw("todetails.first_name like '$searchpaidto%' ");
                        })
                         ->when($status, function ($query) use ($status) {
                            return $query->whereRaw("ac_payment_advice.status= $status ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
                        })
                        ->when($sortpaidto, function ($query) use ($sortpaidto) {
                            return $query->orderby('todetails.first_name', $sortpaidto);
                        }) 
                        ->when($sortpaidby, function ($query) use ($sortpaidby) {
                            return $query->orderby('fromdetails.first_name', $sortpaidby);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                        })
                        ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Owner Drawings Payment Advices', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('Owner Drawings Payment Advices');

                $excel->sheet('Owner Drawings Payment Advices', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Owner Drawings Payment Advices');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Payment Advice Code', 'Paid By', 'Paid To', 'Amount', 'Status'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        if ($requisitions[$i]->status == 2)
                            $status = 'Approved';
                        elseif ($requisitions[$i]->status == 1)
                            $status = 'Pending';
                        else 
                            $status = 'Rejected';

                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->payment_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->fromname.' '.$requisitions[$i]->fromalias);
                        $sheet->setCellValue('C' . $chrRow, $requisitions[$i]->toname.' '.$requisitions[$i]->toalias);
                        $sheet->setCellValue('D' . $chrRow, $requisitions[$i]->total_amount);
                        $sheet->setCellValue('E' . $chrRow, $status);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }
    
    public function drawingrequisitionlist(Request $request){
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name as req_name')
                        ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
                        ->where('requisition_types.name','=','Owner Drawings Requisition')
                      //  ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status IN (1,4,5)")
                        ->orderby('requisition.created_at', 'DESC')
                        ->paginate($paginate);
            $requisitionTypes = DB::table('requisition_types')
                    ->select('requisition_types.*')
                    ->where('requisition_types.name','=','Owner Drawings Requisition')
                    ->where('requisition_types.status','=',1)
                    ->get();
            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbytitle = Input::get('searchbytitle');
                $status= Input::get('status');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                 $searchbytype = Input::get('searchbytype');
                $sortordtitle = Input::get('sortordtitle');
                $sortordcode = Input::get('sortordcode');
                $sortordtype = Input::get('sortordtype');

                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }
            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name as req_name')
                        ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
                        ->where('requisition_types.name','=','Owner Drawings Requisition')
                      //  ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status  IN (1,4,5)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
                        })
                        ->when($searchbytitle, function ($query) use ($searchbytitle) {
                            return $query->whereRaw("requisition.title like '$searchbytitle%' ");
                        })
                        ->when($status, function ($query) use ($status) {
                            return $query->whereRaw("requisition.status= $status ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortordtype, function ($query) use ($sortordtype) {
                            return $query->orderby('requisition_types.name', $sortordtype);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/drawing_requisition/result', array('requisitions' => $requisitions));
            }
            return view('requisitions/drawing_requisition/list', array('requisitions' => $requisitions,'requisitionTypes'=>$requisitionTypes));
        } catch (\Exception $e) {
      echo $e->getMessage(); die();
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function exportdata() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbytitle = Input::get('searchbytitle');
        $status = Input::get('status');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $searchbytype = Input::get('searchbytype');
        $sortordtitle = Input::get('sortordtitle');
        $sortordcode = Input::get('sortordcode');
        $sortordtype = Input::get('sortordtype');

        if ($createdatfrom != '') {
            $createdatfrom = explode('-', $createdatfrom);
            $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
        }
        if ($createdatto != '') {
            $createdatto = explode('-', $createdatto);
            $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
        }

        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }

        $sortOrdDefault = '';
        if ($sortordtitle == '' && $sortordcode == '' && $sortordtype == '') {
            $sortOrdDefault = 'DESC';
        }

        $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name as req_name')
                        ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
                        ->where('requisition_types.name','=','Owner Drawings Requisition')
                      //  ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status  IN (1,4,5)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
                        })
                        ->when($searchbytitle, function ($query) use ($searchbytitle) {
                            return $query->whereRaw("requisition.title like '$searchbytitle%' ");
                        })
                        ->when($status, function ($query) use ($status) {
                            return $query->whereRaw("requisition.status= $status ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortordtype, function ($query) use ($sortordtype) {
                            return $query->orderby('requisition_types.name', $sortordtype);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.created_at', $sortOrdDefault);
                        })
                        ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Owner Drawing Requsition List', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('Owner Drawing Requsition List');

                $excel->sheet('Owner Drawing Requsition List', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Owner Drawing Requsition List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requsition Code', 'Title', 'Date', 'Status'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        if ($requisitions[$i]->status == 4)
                            $status = 'Approved';
                        elseif ($requisitions[$i]->status == 5)
                            $status = 'Rejected';
                        else
                            $status = 'Pending';

                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->created_at)));
//                        $sheet->setCellValue('D' . $chrRow, $requisitions[$i]->req_name);
                        $sheet->setCellValue('D' . $chrRow, $status);

                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } 
    }
}