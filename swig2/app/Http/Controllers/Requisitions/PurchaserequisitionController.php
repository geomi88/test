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
use App\Services\Paymentadvice;
use Customhelper;
use Exception;
use DB;
use PDF;

class PurchaserequisitionController extends Controller {
    
    public function add() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $suppliers = DB::table('ac_party')
                    ->select('ac_party.first_name','ac_party.code','ac_party.id','ac_party.alias_name')
                    ->where(['ac_party.party_type' => 'Supplier', 'ac_party.status' => 1])
                    ->orderby('ac_party.first_name', 'ASC')
                    ->get();
            
            $common = new Commonfunctions();
            $requisitioncode = $common->generateRequisitionCode();
            
            return view('requisitions/purchase_requisition/add', array('suppliers'=>$suppliers,'requisitioncode'=>$requisitioncode));
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
         
            $arraItems = $input["arrItems"];
            $arrItems = json_decode($arraItems);
            
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
            
            $arrApprover = $common->getNextLevelApprover("Purchase Requisition",1,$created_by);
            
            if($arrData->delivery_date){
                $delivery_date=date("Y-m-d",  strtotime($arrData->delivery_date));
            }else{
                $delivery_date=NULL;
            }
            
            ///////////////////// insert in to requisition ///////////////////////
            $reqmodel = new Requisition();
            $reqmodel->requisition_type = $arrApprover['reqtypeid'];
            $reqmodel->company_id = $company_id;
            $reqmodel->requisition_code = $requisitioncode;
            $reqmodel->party_id = $arrData->supplier_id;
            $reqmodel->party_type = $arrData->party_type;
            $reqmodel->isallproducts = $arrData->isallproducts;
            $reqmodel->title = $arrData->title;
            $reqmodel->description = $arrData->description;
            $reqmodel->total_price = $arrData->total_price;
            $reqmodel->total_vat = $arrData->total_vat;
            $reqmodel->outstanding_amount = $arrData->total_price;
            
            $reqmodel->payment_mode = $arrData->payment_mode;
            $reqmodel->creditdays = $arrData->creditdays;
            $reqmodel->payment_terms = $arrData->payment_terms;
            $reqmodel->delivery_place = $arrData->delivery_place;
            $reqmodel->delivery_date = $delivery_date;
            
            $reqmodel->created_by = $created_by;
            $reqmodel->next_level = 1;
            $reqmodel->next_approver_id = $arrApprover['approver_id'];
            $reqmodel->is_settled = 2;
            $reqmodel->convert_to_payment = 2;
            $reqmodel->order_type = 2;
            $reqmodel->status = 1;
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
            $reqhistmodel->total_vat = $arrData->total_vat;
            $reqhistmodel->outstanding_amount = $arrData->total_price;
            $reqhistmodel->created_by = $created_by;
            $reqhistmodel->next_level = 1;
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = 1;
            $reqhistmodel->save();
            
            foreach ($arrItems as $item) {
                ///////////////////// insert in to requisition items ///////////////////////
                $reqitemsmodel = new Requisition_items();
                $reqitemsmodel->requisition_code = $requisitioncode;
                $reqitemsmodel->requisition_id = $requisition_id;
                $reqitemsmodel->requisition_item_id = $item->product_id;
                $reqitemsmodel->item_type = 1;
                $reqitemsmodel->item_type_id = 1;//discuss
                if ($item->isprimary == 1) {
                    $reqitemsmodel->purchase_in_primary_unit = 1;
                } else {
                    $reqitemsmodel->purchase_in_primary_unit = 0;
                }
                
                $reqitemsmodel->alternate_unit_id = $item->unit_id;
                $reqitemsmodel->quantity = $item->quantity;
                $reqitemsmodel->unit_price = $item->priceperunit;
                $reqitemsmodel->total_price = $item->price;
                $reqitemsmodel->level =1;
                if($item->qty_in_primary==""){
                     $item->qty_in_primary=$item->quantity;
                }
                $reqitemsmodel->qty_in_primary = $item->qty_in_primary;
                $reqitemsmodel->rfq_id = $item->rfq_id;
                $reqitemsmodel->rfq_code = $item->rfq_code;
                $reqitemsmodel->save();
                
                ///////////////////// insert in to requisition items history ///////////////////////
                $reqitemshistmodel = new Requisitionitemhistory();
                $reqitemshistmodel->requisition_code = $requisitioncode;
                $reqitemshistmodel->requisition_id = $requisition_id;
                $reqitemshistmodel->requisition_item_id = $item->product_id;
                $reqitemshistmodel->item_type = 1;
                $reqitemshistmodel->item_type_id = 1;//discuss
                if ($item->isprimary == 1) {
                    $reqitemshistmodel->purchase_in_primary_unit = 1;
                } else {
                    $reqitemshistmodel->purchase_in_primary_unit = 0;
                }
                $reqitemshistmodel->alternate_unit_id = $item->unit_id;
                $reqitemshistmodel->quantity = $item->quantity;
                $reqitemshistmodel->unit_price = $item->priceperunit;
                $reqitemshistmodel->total_price = $item->price;
                $reqitemshistmodel->level = 1;
                $reqitemshistmodel->qty_in_primary = $item->qty_in_primary;
                $reqitemshistmodel->save();
            }
            
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
            $result=$common->notifyNextActionTaker("Purchase Requisition",$title,$to,$requisition_id,1);
        
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
            $type="Supplier";
            $requisitiondata = DB::table('requisition')
                    ->select('requisition.*','ac_party.first_name','ac_party.alias_name','ac_party.code','master_resources.name as jobposition','employees.username as empcode',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('ac_party', 'requisition.party_id','=','ac_party.id')
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->leftjoin('master_resources', 'employees.job_position','=','master_resources.id')
                    ->where(['requisition.id' => $id])->first();

            $requisition_items = DB::table('requisition_items')
                        ->select('requisition_items.*','units.name as unitname','inventory.name as product_name','inventory.product_code')
                        ->leftjoin('inventory','requisition_items.requisition_item_id', '=','inventory.id')
                        ->leftjoin('units','requisition_items.alternate_unit_id', '=','units.id')
                        ->where(['requisition_items.requisition_id' => $id])->get();
        
            $action_takers = DB::table('requisition_activity as req_a')
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

            $supplier = DB::table('ac_party')
                    ->select('ac_party.*','country.name as nationality','bc.name as bankcountry')
                    ->leftjoin('country', 'ac_party.nationality','=','country.id')
                    ->leftjoin('country as bc', 'ac_party.bank_country','=','bc.id')
                    ->whereRaw("ac_party.id=$requisitiondata->party_id")
                    ->first();

            $supplier->creditlimitformated=Customhelper::numberformatter($supplier->credit_limit);
            
       //     $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
            $strQuarter = $requisitiondata->created_at; 
            $arrQua = explode(" ", $strQuarter); 
            $date = $arrQua[0]; 
            $dat = explode("-", $date); 
            $Year = $dat[0];   
            $Month = $dat[1];
           // $Quarter = $arrMonthQuarter["$Month"];
            $type="Supplier";
            
            $common = new Commonfunctions();
            $budgetdata = $common->getBudgetInfo($Year,$type,$requisitiondata->party_id);
            $usedData = $common->usedPriceInfoOfLedger($requisitiondata->party_id,$type);
            
             if(count($budgetdata)>0){
                $budgetdata->format_balance = Customhelper::numberformatter($budgetdata->price-$usedData);
                $budgetdata->format_used = Customhelper::numberformatter($usedData);
                $budgetdata->format_initial=Customhelper::numberformatter($budgetdata->price);
                $budgetdata->usedData=$usedData;
            }else{
                $budgetdata=array();
            }
           
            return view('requisitions/purchase_requisition/view', array('requisitiondata'=>$requisitiondata,'requisition_items'=>$requisition_items,'action_takers'=>$action_takers,'next_action_takers_list'=>$next_action_takers_list,'supplierdata'=>$supplier,'budgetdata'=>$budgetdata,'documents'=>$documents));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/outbox');
        }
    }

    public function action_view($id) {
        try {
            $id = \Crypt::decrypt($id);
            $type="Supplier";
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $requisitiondata = DB::table('requisition')
                    ->select('requisition.*','ac_party.first_name','ac_party.alias_name','ac_party.code',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('ac_party', 'requisition.party_id','=','ac_party.id')
                    ->leftjoin('employees', 'requisition.created_by','=','employees.id')
                    ->where(['requisition.id' => $id])->first();

            $requisition_items= DB::table('requisition_items as rqit')
                            ->select('rqit.requisition_item_id as product_id', 'inventory.product_code as product_code', 'inventory.name as product_name',
                                    'rqit.quantity as quantity', 'rqit.alternate_unit_id as unit_id', 'units.name as unitname', 'rqit.unit_price as priceperunit',
                                    'rqit.qty_in_primary as qty_in_primary', 'rqit.total_price as price', 'purchase_in_primary_unit as isprimary','rqit.rfq_id','rqit.rfq_code')
                            ->leftjoin('inventory', 'rqit.requisition_item_id', '=', 'inventory.id')
                            ->leftjoin('units', 'rqit.alternate_unit_id', '=', 'units.id')
                            ->where(['rqit.requisition_id' => $id])->get();
            
            $usedBudget = array(); 
            $allsuppliers = DB::table('ac_party')
                    ->select('ac_party.first_name','ac_party.code','ac_party.id','ac_party.alias_name')
                    ->where(['ac_party.party_type' => 'Supplier', 'ac_party.status' => 1])
                    ->orderby('ac_party.first_name', 'ASC')
                    ->get();

            $supplierdata = DB::table('ac_party')
                    ->select('ac_party.*','country.name as nationality','bc.name as bankcountry')
                    ->leftjoin('country', 'ac_party.nationality','=','country.id')
                    ->leftjoin('country as bc', 'ac_party.bank_country','=','bc.id')
                    ->whereRaw("ac_party.id=$requisitiondata->party_id")
                    ->first();
            
            $supplierdata->creditlimitformated=Customhelper::numberformatter($supplierdata->credit_limit);
            
            $action_takers = DB::table('requisition_activity as req_a')
                    ->select('req_a.actor_id','req_a.created_at','req_a.action','req_a.comments', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"),DB::raw("case when action=1 then 'Approved' when action=3 then 'Rejected' end as action"))
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
            
            ///////////////// Budget information/////////////
           // $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
            $strQuarter = $requisitiondata->created_at; 
            $arrQua = explode(" ", $strQuarter); 
            $date = $arrQua[0]; 
            $dat = explode("-", $date); 
            $Year = $dat[0];   
            $Month = $dat[1];
          //  $Quarter = $arrMonthQuarter["$Month"];
            
           
            $common = new Commonfunctions();
           // foreach($requisition_items_list as $reqItems) {
            for($i=0;$i<count($requisition_items);$i++){
                $type1 = 'Inventory';
                $productid =$requisition_items[$i]->product_id;
                $usedBudget = $common->usedQuantityBudgetInfo($productid);
                $budgetDetails = $common->getBudgetInfo($Year,$type1,$productid);
                
                $remQuantity=($budgetDetails->quantity)-($usedBudget->quantity);
                $remPrice=($budgetDetails->price)-($usedBudget->totalPrice);
                
                
                $requisition_items[$i]->initialPrice=$budgetDetails->price;
                $requisition_items[$i]->initialQuantity=$budgetDetails->quantity;
               // $requisition_items[$i]->usedQuantity=$usedBudget->quantity;
               // $requisition_items[$i]->usedPrice=$usedBudget->totalPrice;
                $requisition_items[$i]->remQuantity=$remQuantity;
                $requisition_items[$i]->remPrice=$remPrice;
                
                if($requisition_items[$i]->rfq_id){
                    $rfqid = \Crypt::encrypt($requisition_items[$i]->rfq_id);
                    $strurl=url('requisitions/rfq/view/' . $rfqid);
                    $requisition_items[$i]->rfq_url=$strurl;
                }else{
                    $requisition_items[$i]->rfq_url='';
                }
            }
            
            $budgetdata = $common->getBudgetInfo($Year,"Supplier",$requisitiondata->party_id);
            $usedData = $common->usedPriceInfoOfLedger($requisitiondata->party_id,$type);
            
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
            
            $isactiontaken=$common->showactionbotton($id);
            
            return view('requisitions/purchase_requisition/action_view', array('requisitiondata'=>$requisitiondata,'requisition_items'=>$requisition_items,'supplierdata'=>$supplierdata,'suppliers'=>$allsuppliers,'action_takers'=>$action_takers,'next_action_takers_list'=>$next_action_takers_list,'budgetdata'=>$budgetdata,'usedData'=>$usedData,'documents'=>$documents,'isactiontaken'=>$isactiontaken));
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
         
            $arraItems = $input["arrItems"];
            $arrItems = json_decode($arraItems);
            
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
            
            $arrApprover = $common->getNextLevelApprover("Purchase Requisition",$next_level,$requisition_data->created_by);
            
            if($arrData->delivery_date){
                $delivery_date=date("Y-m-d",  strtotime($arrData->delivery_date));
            }else{
                $delivery_date=NULL;
            }
            
            ///////////////////// modify requisition ///////////////////////
            DB::table('requisition')
                ->where(['id' => $requisition_id])
                ->update([
                            'party_id' => $arrData->supplier_id,
                            'title' => $arrData->title,
                            'description' => $arrData->description,
                            'total_price' => $arrData->total_price,
                            'total_vat' => $arrData->total_vat,
                            'outstanding_amount' => $arrData->total_price,
                            'edited_by' => $login_id,
                            'next_level' => $arrApprover['next_level'],
                            'next_approver_id' => $arrApprover['approver_id'],
                            'status' => $arrApprover['status'],
                            'payment_mode' => $arrData->payment_mode,
                            'creditdays' => $arrData->creditdays,
                            'payment_terms' => $arrData->payment_terms,
                            'delivery_place' => $arrData->delivery_place,
                            'delivery_date' => $delivery_date,
                        ]);
            
            
            if($arrApprover['status']==4 && $arrApprover['dopayment']==1){
                $result=$common->convertToPaymentAdvice($requisition_id);
            }
            
            if($arrApprover['status']==4 && $arrApprover['makePurchaseOrder']==1){
                $payment = new Paymentadvice();
                $result3 = $payment->generatePurchaseOrder($requisition_id,2);
            }
            
            ///////////////////// insert in to requisition history ///////////////////////
            $reqhistmodel = new Requisitionhistory();
            $reqhistmodel->requisition_id = $requisition_id;
            $reqhistmodel->requisition_code = $arrData->requisition_code;
            $reqhistmodel->party_id = $arrData->supplier_id;
            $reqhistmodel->isallproducts = $requisition_data->isallproducts;
            $reqhistmodel->title = $arrData->title;
            $reqhistmodel->description = $arrData->description;
            $reqhistmodel->total_price = $arrData->total_price;
            $reqhistmodel->total_vat = $arrData->total_vat;
            $reqhistmodel->created_by = $requisition_data->created_by;
            $reqhistmodel->edited_by = $login_id;
            $reqhistmodel->next_level = $arrApprover['next_level'];
            $reqhistmodel->next_approver_id = $arrApprover['approver_id'];
            $reqhistmodel->status = $arrApprover['status'];
            $reqhistmodel->save();
            
            
            
            DB::table('requisition_items')
                    ->where(['requisition_id' => $requisition_id])
                    ->delete();
            
            DB::table('requisition_items_history')
                    ->where(['requisition_id' => $requisition_id])
                    ->update(['status'=>0]);
            
            foreach ($arrItems as $item) {
                ///////////////////// insert in to requisition items ///////////////////////
                $reqitemsmodel = new Requisition_items();
                $reqitemsmodel->requisition_code = $arrData->requisition_code;
                $reqitemsmodel->requisition_id = $requisition_id;
                $reqitemsmodel->requisition_item_id = $item->product_id;
                $reqitemsmodel->item_type = 1;
                $reqitemsmodel->item_type_id = 1;//discuss
                if ($item->isprimary == 1) {
                    $reqitemsmodel->purchase_in_primary_unit = 1;
                } else {
                    $reqitemsmodel->purchase_in_primary_unit = 0;
                }
                $reqitemsmodel->alternate_unit_id = $item->unit_id;
                $reqitemsmodel->quantity = $item->quantity;
                $reqitemsmodel->unit_price = $item->priceperunit;
                $reqitemsmodel->total_price = $item->price;
                $reqitemsmodel->level =$arrApprover['next_level'];
                $reqitemsmodel->qty_in_primary = $item->qty_in_primary;
                $reqitemsmodel->rfq_id = $item->rfq_id;
                $reqitemsmodel->rfq_code = $item->rfq_code;
                $reqitemsmodel->save();
                
                ///////////////////// insert in to requisition items history ///////////////////////
                $reqitemshistmodel = new Requisitionitemhistory();
                $reqitemshistmodel->requisition_code = $arrData->requisition_code;
                $reqitemshistmodel->requisition_id = $requisition_id;
                $reqitemshistmodel->requisition_item_id = $item->product_id;
                $reqitemshistmodel->item_type = 1;
                $reqitemshistmodel->item_type_id = 1;//discuss
                if ($item->isprimary == 1) {
                    $reqitemshistmodel->purchase_in_primary_unit = 1;
                } else {
                    $reqitemshistmodel->purchase_in_primary_unit = 0;
                }
                $reqitemshistmodel->alternate_unit_id = $item->unit_id;
                $reqitemshistmodel->quantity = $item->quantity;
                $reqitemshistmodel->unit_price = $item->priceperunit;
                $reqitemshistmodel->total_price = $item->price;
                $reqitemshistmodel->level= $arrApprover['next_level'];
                $reqitemshistmodel->qty_in_primary = $item->qty_in_primary;
                $reqitemshistmodel->save();
            }
          
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
                $result=$common->notifyNextActionTaker("Purchase Requisition",$title,$to,$requisition_id,2);
            }

            $to1=$requisition_data->created_by;
            $result=$common->notifyCreatedBy("Purchase Requisition",$title,$to1,$requisition_id,1);
           
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
            $result=$common->notifyCreatedBy("Purchase Requisition",$title,$to,$requisition_id,2);
            
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
            
            $budgetDetails = $common->getBudgetInfo($year,$type,$productid);
            $usedBudget = $common->usedQuantityBudgetInfo($productid);
            
            
            if(count($budgetDetails)>0){
                 $budgetDetails->balance =($budgetDetails->price-$usedBudget->totalPrice);
                $budgetDetails->format_balance = Customhelper::numberformatter($budgetDetails->price-$usedBudget->totalPrice);
                $budgetDetails->format_used = Customhelper::numberformatter($usedBudget->totalPrice);
                $budgetDetails->format_initial=Customhelper::numberformatter($budgetDetails->price);
                $budgetDetails->format_usedData=Customhelper::numberformatter($usedBudget->totalPrice);
                $budgetDetails->usedData=$usedBudget->totalPrice;
                $budgetDetails->usedQuantity=$usedBudget->quantity;
            }else{
                $budgetDetails=array();
            }
            
          //  $arrData=array('inventorydata'=>$inventorydata,'altunits'=>$altunits);
            $arrData=array('inventorydata'=>$inventorydata,'altunits'=>$altunits,'branchStock'=>$branchStock,'warehosueStock'=>$warehosueStock,'budgetDetails'=>$budgetDetails);
            
            return \Response::json($arrData);
        } catch (\Exception $e) {
            return -1;
        }
    }
    
       public function getinventorydataQuarter() {
        try {
            $productid = Input::get('productid');
            
            $strQuarter = Input::get('createddate'); 
            $arrQua = explode(" ", $strQuarter); 
            $date = $arrQua[0]; 
            $dat = explode("-", $date); 
            $year = $dat[0];   
            $month = $dat[1];
          
            
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
          
            $budgetDetails = $common->getBudgetInfo($year,$type,$productid);
            $usedBudget = $common->usedQuantityBudgetInfoQuarter($productid,$year);
            
            
            if(count($budgetDetails)>0){
                 $budgetDetails->balance =($budgetDetails->price-$usedBudget->totalPrice);
                $budgetDetails->format_balance = Customhelper::numberformatter($budgetDetails->price-$usedBudget->totalPrice);
                $budgetDetails->format_used = Customhelper::numberformatter($usedBudget->totalPrice);
                $budgetDetails->format_initial=Customhelper::numberformatter($budgetDetails->price);
                $budgetDetails->format_usedData=Customhelper::numberformatter($usedBudget->totalPrice);
                $budgetDetails->usedData=$usedBudget->totalPrice;
                $budgetDetails->usedQuantity=$usedBudget->quantity;
            }else{
                $budgetDetails=array();
            }
            
          //  $arrData=array('inventorydata'=>$inventorydata,'altunits'=>$altunits);
            $arrData=array('inventorydata'=>$inventorydata,'altunits'=>$altunits,'branchStock'=>$branchStock,'warehosueStock'=>$warehosueStock,'budgetDetails'=>$budgetDetails);
            
            return \Response::json($arrData);
        } catch (\Exception $e) {
           // echo $e->getMessage();
            return -1;
        }
    }


}