<?php

namespace App\Services;

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
use App\Notifications\RequisitionNotification;
use Kamaln7\Toastr\Facades\Toastr;
use App\Helpers\CategoryHierarchy;
use App\Models\Requisition_activity;
use App\Models\Branch_physical_stock;
use App\Models\Requisition_items;
use Customhelper;
use Exception;
use DB;
use PDF;
use Mail;

class Commonfunctions {

    public function autocompleteemployees($searchkey) {
        $data = array();
        if($searchkey != ''){
            $data = DB::table('employees')
                    ->select('employees.id','username as code','m.name as jobposition','employees.first_name','employees.alias_name')
                    ->leftjoin('master_resources as m', 'employees.job_position','=','m.id')
                    ->whereRaw("employees.status=1 AND (username like '$searchkey%' OR first_name like '$searchkey%')")
                    ->orderby('employees.first_name','ASC')
                    ->get();
        }

        return $data;
    }

    public function autocompleteinventory($searchkey, $preferredonly, $supplierid) {

        $arrData = array();
        if($searchkey != ''){
            $arrprefferedprod = array();
            if ($preferredonly == 1) {
                $arrprefferedprod = DB::table('ac_preferred_prducts')
                                ->select('product_id')
                                ->whereRaw("status=1 AND supplier_id=$supplierid")
                                ->get()->pluck('product_id')->toarray();
            }
            
            $arrData = DB::table('inventory')
                    ->select('product_code','name','id')
                    ->whereRaw("status=1 AND (product_code like '$searchkey%' OR name like '$searchkey%')")
                    ->when(count($arrprefferedprod)>0, function ($query) use ($arrprefferedprod) {
                        return $query->whereIn('id',$arrprefferedprod);
                    })->get();
        }
        
        return $arrData;
    }
    
    public function getrfqdata($searchkey,$supplierid) {

        $arrData = array();
        if($searchkey != ''){
            
            $arrData = DB::table('rfq')
                    ->select('rfq_code','id')
                    ->whereRaw("status=1 AND confirm_status=1 AND rfq_code='$searchkey'")
                    ->when($supplierid, function ($query) use ($supplierid) {
                        return $query->whereRaw("rfq.supplier_id=$supplierid");
                    })
                    ->first();
                    
            if($arrData!=''){
                $id = \Crypt::encrypt($arrData->id);
                $strurl=url('requisitions/rfq/view/' . $id);
                $arrData->rfqurl=$strurl;
                
                $arrDetails = DB::table('rfq_items')
                    ->select('rfq_items.*','inventory.name','inventory.product_code')
                    ->leftjoin('inventory', 'rfq_items.item_id','=','inventory.id')
                    ->whereRaw("rfq_id=$arrData->id")
                    ->first();
                
                $arrData->arrDetails=$arrDetails;
               
            }
            
        
        }
        
      
        return $arrData;
    }
    
    public function getsupplierdata($supplierid) {

        $supplierid = Input::get('supplier_id');
        $year = date('Y');
        $month = date("m");
   
        $type="Supplier";
       
        $supplier = DB::table('ac_party')
                ->select('ac_party.*','country.name as nationality','bc.name as bankcountry')
                ->leftjoin('country', 'ac_party.nationality','=','country.id')
                ->leftjoin('country as bc', 'ac_party.bank_country','=','bc.id')
                ->whereRaw("ac_party.id=$supplierid")
                ->first();
        $budgetdata = $this->getBudgetInfo($year,$type,$supplierid);
        $usedData = $this->usedPriceInfoOfLedger($supplierid,$type);
        
        if(count($budgetdata)>0){
                $budgetdata->format_balance = Customhelper::numberformatter($budgetdata->price-$usedData);
                $budgetdata->format_used = Customhelper::numberformatter($usedData);
                $budgetdata->format_initial=Customhelper::numberformatter($budgetdata->price);
                $budgetdata->usedData=$usedData;
            }else{
                $budgetdata=array();
            }
        
        $supplier->creditlimitformated=Customhelper::numberformatter($supplier->credit_limit);
        $arrData=array("supplierdata"=>$supplier,"budgetdata"=>$budgetdata);

        return $arrData;
    }

    public function generateRequisitionCode() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $employee = DB::table('employees')
            ->select('employees.username as usercode')
            ->where(['id'=>$login_id])
            ->first();
        
        $maxid = DB::table('requisition')->where(['created_by'=>$login_id])->max('id')+1;
        $requisitioncode="RQ-".$employee->usercode."-".$maxid;
        $requisitioncode= strtoupper($requisitioncode);
        return $requisitioncode;
    }
    
    public function generateRfqCode() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $employee = DB::table('employees')
            ->select('employees.username as usercode')
            ->where(['id'=>$login_id])
            ->first();
        
        $maxid = DB::table('rfq')->where(['created_by'=>$login_id])->max('id')+1;
        $rfqcode="RFQ-".$employee->usercode."-".$maxid;
        $rfqcode= strtoupper($rfqcode);
        
        return $rfqcode;
    }

    public function getNextLevelApprover($requisitiontype, $next_level, $created_by) {

        $reqtype = DB::table('requisition_types as rq')
                        ->select('rq.id as type_id', 'rq.do_payment as do_payment','make_purchase_order')
                        ->whereRaw("name='$requisitiontype' AND status=1")->first();

        $reqtypeid = $reqtype->type_id;

        $req_hierarchy_details = DB::table('requisition_hierarchy')
                ->select('requisition_hierarchy.*')
                ->where(['requisition_type_id' => $reqtypeid, 'level' => $next_level, 'status' => 1])->first();
        
        if($next_level==1 && count($req_hierarchy_details)==0){
            throw new Exception("No_hierarchy");
        }   

        $status=1;
        $approver_id=NULL;
        if(count($req_hierarchy_details)>0){
            if($req_hierarchy_details->approver_type=="TOP_MANAGER"){
                $manager = DB::table('employees')->select('employees.top_manager_id as manager_id')
                                ->whereRaw("employees.id=$created_by")->first();

                $approver_id = $manager->manager_id;
            } else {
                $approver_id = $req_hierarchy_details->approver_id;
            }
        } else {
            $status = 4;
            $next_level = NULL;
        }

        $arrData = array("reqtypeid" => $reqtypeid, "approver_id" => $approver_id, "next_level" => $next_level, "status" => $status, "dopayment" => $reqtype->do_payment,"makePurchaseOrder" => $reqtype->make_purchase_order);

        return $arrData;
    }

    // $type=1 for first level, $type=2 for intermediatelevel
    public function notifyNextActionTaker($category, $title, $to, $requisition_id, $type) {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $view = 'action_view';
        $from = $login_id;
        $empdata = DB::table('employees')
                        ->select('id', 'employees.first_name')
                        ->where(['id' => $login_id])->first();

        if ($type == 1) {
            $message = 'Request ' . $title . ' From ' . $empdata->first_name . ' Please review & update the status for the request.';
        } else if ($type == 2) {
            $message = 'Request ' . $title . ' was approved by ' . $empdata->first_name . '. Please revise and update the status.';
        }
        Auth::user()->notify(new RequisitionNotification($from, $to, $message, $category, $requisition_id, $view));
        
        // send email 
        $this->sendmail($to,$category,$message);
        
        return 1;
    }

    // $type=1 for approval, $type=2 for rejection
    public function notifyCreatedBy($category, $title, $to, $requisition_id, $type) {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $view = 'view';
        $from = $login_id;
        $empdata = DB::table('employees')
                        ->select('id', 'employees.first_name')
                        ->where(['id' => $login_id])->first();

        if ($type == 1) {
            $message = 'Your request ' . $title . ' was approved by ' . $empdata->first_name . '.';
        } else if ($type == 2) {
            $message = 'Your request ' . $title . ' was rejected by ' . $empdata->first_name . '. Please revise & request again.';
        }
        Auth::user()->notify(new RequisitionNotification($from, $to, $message, $category, $requisition_id, $view));
        
        // send email 
        $this->sendmail($to,$category,$message);
        
        return 1;
    }

    public function saveRequisitionActivity($code, $id, $action, $comments, $actor) {
        $req_activity_model = new Requisition_activity();
        $req_activity_model->requisition_code = $code;
        $req_activity_model->requisition_id = $id;
        $req_activity_model->action = $action;
        $req_activity_model->comments = $comments;
        $req_activity_model->actor_id = $actor;
        $req_activity_model->status = 1;
        $req_activity_model->save();

        return 1;
    }

    public function convertToPaymentAdvice($requisition_id) {

        $reqtype = DB::table('requisition_types as rq')
                        ->select('rq.id as type_id')
                        ->whereRaw("name='Payment Advice'")->first();

        $req_hierarchy = array();
        if (count($reqtype) > 0) {
            $req_hierarchy = DB::table('requisition_hierarchy')
                            ->select('requisition_hierarchy.approver_id')
                            ->where(['requisition_type_id' => $reqtype->type_id, 'level' => 1, 'status' => 1])->first();
            $result = $this->notifyNextActionTakerPaymentAdvice("Make Payment", $requisition_id, $req_hierarchy->approver_id, $requisition_id, 3);
        }

        if (count($req_hierarchy) > 0) {
            DB::table('requisition')
                    ->where(['id' => $requisition_id])
                    ->update(['next_approver_id' => $req_hierarchy->approver_id, 'convert_to_payment' => 1]);
        }

        return 1;
    }

    public function generatePaymentAdviceCode() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $employee = DB::table('employees')
            ->select('employees.username as usercode')
            ->where(['id'=>$login_id])
            ->first();
        
        $maxid = DB::table('ac_payment_advice')->where(['created_by'=>$login_id])->max('id')+1;
        $paymentcode="PA-".$employee->usercode."-".$maxid;
        $paymentcode= strtoupper($paymentcode);
        return $paymentcode;
    }
    
    public function autocompletegeneralledgers($searchkey) {
        $data = array();
        if($searchkey != ''){
            $data = DB::table('ac_accounts')
                    ->select('ac_accounts.type_id as accountid','code','ac_accounts.first_name','ac_accounts.type')
                    ->whereRaw("ac_accounts.status=1 AND type='General Ledger' AND (code like '$searchkey%' OR first_name like '$searchkey%')")
                    ->orderby('ac_accounts.first_name','ASC')
                    ->get();
        }

        return $data;
    }
    
    public function getInventoryPhysicalStockById($invId) {


        $invPhysicalStock = DB::table('branch_physical_stock')
                ->select('branch_physical_stock.*')
                ->whereraw("id IN (SELECT max(id) FROM branch_physical_stock GROUP BY stock_area_id,stock_area) order by stock_area_id desc")
                ->get();
        $jsonArray = array();
        $totalInvStock = 0;
        foreach ($invPhysicalStock as $stockDetails) {

            $jsonArray = json_decode($stockDetails->physical_stock, true);

            if (array_key_exists($invId, $jsonArray)) {
                $totalInvStock+= $jsonArray[$invId];
            }
        }
        
      $totalInventoryInStock = Customhelper::numberformatter($totalInvStock);
        return $totalInventoryInStock;
    }
/*
 * returns the budget information of current quarter
 */
  /*  public function currentQuarterBudgetInfoOfLedger($ledger_id) {
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
        $result = DB::table('ac_budget')
                ->select('*')
                ->where([['budget_type_id','=',$ledger_id],['year','=',$year],['quarter','=',$quarter]])
                ->get();
        return $result;
    }  */
    /*
     * returns the total used amount for a ledger in current quarter
     */
    public function usedPriceInfoOfLedger($ledger_id,$type){
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        $startDate='';
        $endDate='';
        if($currentYear!=""){
            $startDate=$currentYear.'-01-01';
         
            $endDate=$currentYear.'-12-31';
        }
        
        if($type=="Employee"){
            
        $priceInfo = DB::table('requisition')
                ->where([['general_ledger','=',$ledger_id],['party_type','=',$type],['status','=',4]])
                ->whereBetween('created_at', array($startDate, $endDate))
                ->sum('requisition.total_price');
        }else{
           $priceInfo = DB::table('requisition')
                ->where([['party_id','=',$ledger_id],['party_type','=',$type],['status','=',4]])
                ->whereBetween('created_at', array($startDate, $endDate))
                ->sum('requisition.total_price');
          
        }
        return $priceInfo;
    }
    
    
   /*
     * returns the total amount that is in processing stage for a ledger in current quarter
     */
    public function processingAmountInfoOfLedger($ledger_id){
        $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
        $currentMonth = date('m');
        $currentYear = date('Y');
        $currentQuarter = $arrMonthQuarter["$currentMonth"];
        $startDate='';
        $endDate='';
        if($currentQuarter==1){
            $startDate='2018-01-01';
            $endDate='2018-03-31';
        }elseif ($currentQuarter==2) {
            $startDate='2018-04-01';
            $endDate='2018-06-30';
        }elseif ($currentQuarter==3) {
            $startDate='2018-07-01';
            $endDate='2018-09-30';
        }  else {
            $startDate='2018-08-01';
            $endDate='2018-12-31';
        }
        $priceInfo = DB::table('requisition')
                ->where([['party_id','=',$ledger_id],['status','=',1]])
                ->whereBetween('created_at', array($startDate, $endDate))
                ->sum('requisition.total_price');
        return $priceInfo;
    }
     /*
     * returns the total used quantity for a ledger in current quarter(quantity and price)
     */
    public function usedQuantityBudgetInfo($inventory_id){
     
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        $startDate='';
        $endDate='';
        if($currentYear!=""){
            $startDate=$currentYear.'-01-01';
            $endDate=$currentYear.'-12-31';
        }
        $quantityInfo = DB::table('requisition')
                ->leftjoin('requisition_items', function($join) use ($inventory_id){
                                $join->on('requisition_items.requisition_id', '=', 'requisition.id');
                                $join->where('requisition_items.requisition_item_id', '=', $inventory_id);
                         })
                ->where('requisition.status','=',4)
                ->whereBetween('requisition.created_at', array($startDate, $endDate))
                ->first(
                        array(
                          DB::raw('COALESCE(SUM(requisition_items.qty_in_primary),0) as quantity'),
                          DB::raw('COALESCE(SUM(requisition_items.total_price),0) as totalPrice')
                        )
                      );
        return $quantityInfo;        
    }
    /*
     * budget info based on year, quarter and type
     */
    public function getBudgetInfo($year,$type,$ledger_id){
         
        if($type=='Inventory'){
                $accounts = DB::table('inventory')
                        ->select('inventory.name as first_name','inventory.product_code as code','inventory.id as type_id','ac_budget.price_budget as price','ac_budget.id as budget_id','ac_budget.quantity_budget as quantity',DB::raw('"" as last_name'))
                        ->join('ac_budget', function($join) use ($type,$year,$ledger_id){
                                $join->on('ac_budget.budget_type_id', '=', 'inventory.id');
                                $join->where('ac_budget.status', '=', 1);
                                $join->where('ac_budget.budget_type', '=', $type);
                                $join->where('ac_budget.year', '=', $year);
                                
                                $join->where('ac_budget.budget_type_id', '=', $ledger_id);
                         })
                        ->orderby('ac_budget.price_budget','asc')
                        ->orderby('inventory.name','asc')
                        ->first();
            }elseif($type=='Branch'||$type=='Warehouse'||$type=='Office'){
                $accounts = DB::table('master_resources')
                        ->select('master_resources.name as first_name','master_resources.branch_code as code','master_resources.id as type_id','ac_budget.price_budget as price','ac_budget.id as budget_id','ac_budget.quantity_budget as quantity',DB::raw('"" as last_name'))
                        ->join('ac_budget', function($join) use ($type,$year,$ledger_id){
                                $join->on('ac_budget.budget_type_id', '=', 'master_resources.id');
                                $join->where('ac_budget.status', '=', 1);
                                $join->where('ac_budget.budget_type', '=', $type);
                                $join->where('ac_budget.year', '=', $year);

                                $join->where('ac_budget.budget_type_id', '=', $ledger_id);
                         })
                        ->where('master_resources.resource_type', '=', $type)
                        ->orderby('ac_budget.price_budget','asc')
                        ->orderby('master_resources.name','asc')
                        ->first();
            }else{
                $accounts = DB::table('ac_accounts')
                        ->select('ac_accounts.first_name as first_name','ac_accounts.last_name as last_name','ac_accounts.code as code','ac_accounts.type_id as type_id','ac_budget.price_budget as price','ac_budget.id as budget_id','ac_budget.quantity_budget as quantity')
                        ->join('ac_budget', function($join) use ($type,$year,$ledger_id){
                                $join->on('ac_budget.budget_type_id', '=', 'ac_accounts.type_id');
                                $join->where('ac_budget.status', '=', 1);
                                $join->where('ac_budget.budget_type', '=', $type);
                                $join->where('ac_budget.year', '=', $year);

                                $join->where('ac_budget.budget_type_id', '=', $ledger_id);
                         })
                        ->where('ac_accounts.type', '=', $type)
//                        ->where('ac_accounts.status', '=', 1)
                        ->orderby('ac_budget.price_budget','asc')
                        ->orderby('ac_accounts.first_name','asc')
                        ->first();            
            } 
            return $accounts;
    }


   public function getBranchInventoryStock($invId) {


        $invPhysicalStock = DB::table('branch_physical_stock')
                ->select('branch_physical_stock.*')
                ->whereraw("id IN (SELECT max(id) FROM branch_physical_stock where stock_area=0 GROUP BY stock_area_id,stock_area) order by stock_area_id desc")
                ->get();
        $jsonArray = array();
        $totalInvStock = 0;
        foreach ($invPhysicalStock as $stockDetails) {

            $jsonArray = json_decode($stockDetails->physical_stock, true);

            if (array_key_exists($invId, $jsonArray)) {
                $totalInvStock+= $jsonArray[$invId];
            }
        }
        
      $totalInventoryInBranchStock =$totalInvStock;
        return $totalInventoryInBranchStock;
    }
    
     public function getWarehouseInventoryStock($invId) {


        $invPhysicalStock = DB::table('branch_physical_stock')
                ->select('branch_physical_stock.*')
                ->whereraw("id IN (SELECT max(id) FROM branch_physical_stock where stock_area=1 GROUP BY stock_area_id,stock_area) order by stock_area_id desc")
                ->get();
        $jsonArray = array();
        $totalInvStock = 0;
        foreach ($invPhysicalStock as $stockDetails) {

            $jsonArray = json_decode($stockDetails->physical_stock, true);

            if (array_key_exists($invId, $jsonArray)) {
                $totalInvStock+= $jsonArray[$invId];
            }
        }
        
      $totalInventoryInWarehouseStock = $totalInvStock;
        return $totalInventoryInWarehouseStock;
    }  
    
    
    public function revertRequisition($requisitiontype,$req_hierarchy_details) {

        
        


        $requisitions = DB::table('requisition')
                ->whereRaw("requisition.status=1 AND requisition_type=$requisitiontype")
                ->get();

        $updateRequisitionArray = array();
        $updateRequisitionActivityArray = array();
        $updateRequisitionHistoryArray = array();
        $updateRequisitionItemHistoryArray = array();
        $insertRequisitionItemArray = array();
        $requisitionid = "";
        $updateRequisitionHistoryApprover = array();
        
        foreach ($requisitions as $existing_requisition) {
            
            
             
            
            
            $requisitionid = $existing_requisition->id;
            $getRequisition = DB::table('requisition_history')
                    ->whereRaw("status=1 AND requisition_id=$requisitionid and next_level=1")
                    ->first();

            
            

            if (count($getRequisition) > 0) {
                
              
            $status=1;
        $approver_id=NULL;
        $created_by=$getRequisition->created_by;
        if(count($req_hierarchy_details)>0){
            if($req_hierarchy_details->approver_type=="TOP_MANAGER"){
                $manager = DB::table('employees')->select('employees.top_manager_id as manager_id')
                                ->whereRaw("employees.id=$created_by")->first();

                $approver_id = $manager->manager_id;
            } else {
                $approver_id = $req_hierarchy_details->approver_id;
            }
        } else {
            $status = 4;
            $next_level = NULL;
        }
              
                
                $updateRequisitionArray['next_approver_id'] = $approver_id;
                $updateRequisitionHistoryApprover['next_approver_id']= $approver_id;
                $updateRequisitionArray['next_level'] = 1;
                $updateRequisitionArray['edited_by'] = NULL;

                DB::table('requisition')
                        ->whereRaw("requisition.status=1 AND id=$requisitionid")
                        ->update($updateRequisitionArray);


                $updateRequisitionHistoryArray['status'] = 6; //deleted

                DB::table('requisition_history')
                        ->whereRaw("requisition_id=$requisitionid and next_level>1")
                        ->update($updateRequisitionHistoryArray);

                DB::table('requisition_history')
                        ->whereRaw("requisition_id=$requisitionid and next_level=1")
                        ->update($updateRequisitionHistoryApprover);


                DB::table('requisition_items')
                        ->whereRaw("requisition_id=$requisitionid")
                        ->delete();


                $getRequisitionHistoryItems = DB::table('requisition_items_history')
                        ->whereRaw("level=1 AND requisition_id=$requisitionid")
                        ->get();

                foreach ($getRequisitionHistoryItems as $updateItems) {

                 

                    $reqitemsmodel = new Requisition_items();
                    $reqitemsmodel->requisition_code = $updateItems->requisition_code;
                    $reqitemsmodel->requisition_id = $requisitionid;
                    $reqitemsmodel->requisition_item_id = $updateItems->requisition_item_id;
                    $reqitemsmodel->item_type = $updateItems->item_type;
                    $reqitemsmodel->item_type_id = $updateItems->item_type; //discuss
                    $reqitemsmodel->purchase_in_primary_unit = $updateItems->purchase_in_primary_unit; //discuss

                    $reqitemsmodel->alternate_unit_id = $updateItems->alternate_unit_id;
                    $reqitemsmodel->quantity = $updateItems->quantity;
                    $reqitemsmodel->unit_price = $updateItems->unit_price;
                    $reqitemsmodel->total_price = $updateItems->total_price;
                    $reqitemsmodel->level = 1;
                    
                    $reqitemsmodel->save();
                }

                $updateRequisitionItemHistoryArray['status']=0;
                  DB::table('requisition_items_history')
                        ->whereRaw("requisition_id=$requisitionid and level>1")
                        ->update($updateRequisitionItemHistoryArray);

                  $updateRequisitionActivityArray['status']=2;
                   DB::table('requisition_activity')
                        ->whereRaw("requisition_id=$requisitionid")
                        ->update($updateRequisitionActivityArray);

                //remove requisition item from requisition item where requisition_id
            }
        }


        return;
    }

    
     public function getgeneralledgerdata($supplierid) {

          
        $year = date('Y');
        $month = date("m");
       
        $type="General Ledger";
      
      
        $budgetdata = $this->getBudgetInfo($year,$type,$supplierid);
        $type1="Employee";
        $usedData = $this->usedPriceInfoOfLedger($supplierid,$type1);
     
        $arrData=array("budgetdata"=>$budgetdata,'useddata'=>$usedData);

        return $arrData;
    }
    
    // $type=1 for first level, $type=2 for intermediatelevel
    public function notifyNextActionTakerPaymentAdvice($category,$code, $to, $requisition_id, $type) {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $view = 'view';
        $from = $login_id;
        $empdata = DB::table('employees')
                        ->select('id', 'employees.first_name')
                        ->where(['id' => $login_id])->first();

        if ($type == 1) {
            $message = 'Payment Request ' . $code . ' From ' . $empdata->first_name . ' Please review & update the status for the request.';
        } else if ($type == 2) {
            $message = 'Payment Request ' . $code . ' was approved by ' . $empdata->first_name . '. Please revise and update the status.';
        } else if($type == 3){
            $data = DB::table('requisition')
                        ->select('requisition_code', 'title')
                        ->where(['id' => $requisition_id])->first();
            $message = 'Please review and make payment for requsition ' . $data->requisition_code." : ".$data->title;
        }
        Auth::user()->notify(new RequisitionNotification($from, $to, $message, $category, $requisition_id, $view));
        
        // send email
        if($type!=3){
            $this->sendmail($to,$category,$message);
        }
        
        return 1;
    }

    // $type=1 for approval, $type=2 for rejection
    public function notifyCreatedByPaymentAdvice($category, $title, $to, $requisition_id, $type) {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $view = 'view';
        $from = $login_id;
        $empdata = DB::table('employees')
                        ->select('id', 'employees.first_name')
                        ->where(['id' => $login_id])->first();

        if ($type == 1) {
            $message = 'Payment request ' . $title . ' was approved by ' . $empdata->first_name . '.';
        } else if ($type == 2) {
            $message = 'Payment request ' . $title . ' was rejected by ' . $empdata->first_name . '. Please revise & request again.';
        }
        Auth::user()->notify(new RequisitionNotification($from, $to, $message, $category, $requisition_id, $view));
        
        // send email 
        $this->sendmail($to,$category,$message);
        
        return 1;
    }

    public function getTransactionHistory($requisition_code) {
        
        $history = DB::table('ac_payment_advice_details as acdet')
                ->select('acdet.*','pa.payment_code','employees.first_name','pa.payment_type','ac_transaction.created_at as trandate')
                ->leftjoin('ac_payment_advice as pa','acdet.payment_advice_id','=','pa.id')
                ->leftjoin('employees','pa.created_by','=','employees.id')
                ->leftjoin('ac_transaction', function($join){
                        $join->on('pa.id','=','ac_transaction.payment_advice_id');
                        $join->on('pa.payment_code','=','ac_transaction.payment_code');
                 })
                ->whereRaw("pa.status=2 AND acdet.requisition_code='$requisition_code'")
                ->orderby('ac_transaction.created_at','DESC')
                ->get();
                 
        $strhtml='';
        $strhtmlprint='';
        if(count($history)>0){
            foreach ($history as $hist) {
                $date=date('d-m-Y',strtotime($hist->trandate));
                $amount=Customhelper::numberformatter($hist->pay_amount);
                if($hist->payment_type==1){
                    $modofpay="Cheque";
                }else if($hist->payment_type==2){
                    $modofpay="Cash";
                }else{
                    $modofpay="Online";
                }
                $strhtml=$strhtml."<tr><td>$date</td><td>$hist->payment_code</td><td>$modofpay</td><td>$hist->first_name</td><td class='amountAlign'>$amount</td></tr>";
                $strhtmlprint=$strhtmlprint."<tr><td>$date</td><td>$hist->payment_code</td><td>$modofpay</td><td>$hist->first_name</td><td style='text-align:right;'>$amount</td></tr>";
            }
            $subtotal=$history->sum("pay_amount");
            $subtotalformated=Customhelper::numberformatter($subtotal);
            $strhtml=$strhtml."<tr><td></td><td></td><td></td><td>Total Payment</td><td class='amountAlign'>$subtotalformated</td></tr>";
            $strhtmlprint=$strhtmlprint."<tr><td></td><td></td><td></td><td>Total Payment</td><td style='text-align:right;'>$subtotalformated</td></tr>";
        }else{
            $strhtml='<tr><td colspan="5">No records found</td><tr>';
            $strhtmlprint='<tr><td colspan="5">No records found</td><tr>';
        }
        
        $requisition = DB::table('requisition')
                ->select('requisition.total_price','requisition.id as req_id')
                ->whereRaw("requisition_code='$requisition_code'")
                ->first();
        
        $requisition->totalformated=Customhelper::numberformatter($requisition->total_price);
        $arrData=array("requisition"=>$requisition,"history"=>$strhtml,"printhtml"=>$strhtmlprint);

        return $arrData;
    }

 public function usedQuantityBudgetInfoQuarter($inventory_id,$year){
       if($year==""){
        $currentYear = date('Y');
        }else{
             $currentYear =$year;
                }
      
        $startDate='';
        $endDate='';
        if($currentYear!=""){
            $startDate=$currentYear.'-01-01';
            $endDate=$currentYear.'-12-31';
        }
        $quantityInfo = DB::table('requisition')
                ->leftjoin('requisition_items', function($join) use ($inventory_id){
                                $join->on('requisition_items.requisition_id', '=', 'requisition.id');
                                $join->where('requisition_items.requisition_item_id', '=', $inventory_id);
                         })
                ->where('requisition.status','=',4)
                ->whereBetween('requisition.created_at', array($startDate, $endDate))
                ->first(
                        array(
                          DB::raw('COALESCE(SUM(requisition_items.qty_in_primary),0) as quantity'),
                          DB::raw('COALESCE(SUM(requisition_items.total_price),0) as totalPrice')
                        )
                      );
                       
        return $quantityInfo;        
    }
    
    public function usedPriceInfoOfLedgerQuarter($year,$month,$type,$ledger_id){
        
        if($year==""){
        $currentYear = date('Y');
        }else{
             $currentYear =$year;
                }
      
        $startDate='';
        $endDate='';
        if($currentYear!=""){
            $startDate=$currentYear.'-01-01';
            $endDate=$currentYear.'-12-31';
        }
       
         if($type=="Employee"){
        $priceInfo = DB::table('requisition')
                ->where([['general_ledger','=',$ledger_id],['party_type','=',$type],['status','=',4]])
                ->whereBetween('created_at', array($startDate, $endDate))
                ->sum('requisition.total_price');
         }else{
            $priceInfo = DB::table('requisition')
                ->where([['party_id','=',$ledger_id],['party_type','=',$type],['status','=',4]])
                ->whereBetween('created_at', array($startDate, $endDate))
                ->sum('requisition.total_price');
           
         }
        return $priceInfo;
    }
    
    
     public function getGeneralLedgerdataQuarter($year,$month,$type,$supplierid) {

        $budgetdata = $this->getBudgetInfo($year,$type,$supplierid);
        $usedData = $this->usedPriceInfoOfLedgerQuarter($year,$month,$type,$supplierid);
        
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
            
            
      
        $arrData=array("budgetdata"=>$budgetdata,'useddata'=>$usedData);

        return $arrData;
    }


    public function getRequestViewUrlFromPaymentAdvice($requisitiontype) {
        $strrequesturl='';
        if($requisitiontype=="Purchase Requisition"){
            $strrequesturl="requisitions/payment_advice/purchase_requisition/view";
        }else if($requisitiontype=="Advance Payment Requisition"){
            $strrequesturl="requisitions/payment_advice/advance_requisition/view";
        }else if($requisitiontype=="Service Requisition"){
            $strrequesturl="requisitions/payment_advice/service_request/view";
        }else if($requisitiontype=="Import Purchase Requisition"){
            $strrequesturl="requisitions/payment_advice/import_purchase_requisition/view";
        }else if($requisitiontype=="Owner Drawings Requisition"){
            $strrequesturl="requisitions/drawing_requsition_payment_advice/view";
        }
        
        return $strrequesturl;
    }
    
    public function getNextLevelApproverForPayment($requisitiontype, $next_level, $created_by) {

        $reqtype = DB::table('requisition_types as rq')
                        ->select('rq.id as type_id')
                        ->whereRaw("name='$requisitiontype'")->first();

        $reqtypeid = $reqtype->type_id;

        $req_hierarchy_details = DB::table('requisition_hierarchy')
                ->select('requisition_hierarchy.*')
                ->where(['requisition_type_id' => $reqtypeid, 'level' => $next_level, 'status' => 1])->first();

        $status=1;
        $approver_id=NULL;
        if(count($req_hierarchy_details)>0){
            if($req_hierarchy_details->approver_type=="TOP_MANAGER"){
                $manager = DB::table('employees')->select('employees.top_manager_id as manager_id')
                                ->whereRaw("employees.id=$created_by")->first();

                $approver_id = $manager->manager_id;
            } else {
                $approver_id = $req_hierarchy_details->approver_id;
            }
        } else {
            $status = 2;
            $next_level = NULL;
        }

        $arrData = array("approver_id" => $approver_id, "next_level" => $next_level, "status" => $status);

        return $arrData;
    }
    
    public function checkLevelAuthenticationForPayment($current_level) {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $reqtype = DB::table('requisition_types as rq')
                        ->select('rq.id as type_id')
                        ->whereRaw("name='Payment Advice'")->first();

        $reqtypeid = $reqtype->type_id;

        $hierarchy = DB::table('requisition_hierarchy')
                ->select('requisition_hierarchy.approver_id')
                ->where(['requisition_type_id' => $reqtypeid, 'level' => $current_level, 'status' => 1])->first();
        
        if(count($hierarchy)>0){
            if($hierarchy->approver_id!=$login_id){
                return -1;
            }
        }else{
            return -1;
        }
        
        return 1;
    }
    
    public function sendmail($to,$strsubject,$strmessage) {

//        $emptodata = DB::table('employees')
//                ->select('employees.email','employees.contact_email')
//                ->where(['id' => $to])->first();
//        
//        if($emptodata->email){
//            $email=$emptodata->email;
//        }else{
//            $email=$emptodata->contact_email;
//        }
//       
//        Mail::send('emailtemplates.requisition', ['email' => $email, 'strmessage' => $strmessage,'web_url'=>''], function($message)use ($email,$strsubject) {
//            $message->to($email)->subject($strsubject);
//        });

        return 1;
    }
    
    
    public function revertPaymentAdvice($requisitiontype, $req_hierarchy_details) {
         
        $payment_advices = DB::table('ac_payment_advice')
                ->whereRaw("ac_payment_advice.status=1")
                ->get();

        $updatePaymentAdviceArray = array();
        $updatePaymentAdviceActivityArray = array();

        $paymentAdviceid = "";

        foreach ($payment_advices as $existing_paymentAdvice) {

            $paymentAdviceid = $existing_paymentAdvice->id;

            $status = 1;
            $approver_id = NULL;

            if (count($req_hierarchy_details) > 0) {
                $approver_id = $req_hierarchy_details->approver_id;
                $next_level = 2;
                $updatePaymentAdviceArray['next_approver_id'] = $approver_id;
                $updatePaymentAdviceArray['level'] = 2;
            } else {
                
            }
            
            DB::table('ac_payment_advice')
                    ->whereRaw("ac_payment_advice.status=1 AND id=$paymentAdviceid")
                    ->update($updatePaymentAdviceArray);

            $updatePaymentAdviceActivityArray['status'] = 2;
            DB::table('ac_payment_advice_activity')
                    ->whereRaw("payment_advice_id=$paymentAdviceid")
                    ->update($updatePaymentAdviceActivityArray);
         
        }


        return;
    }

    public function checkrequisitionvalidations($requisition_id) {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $requisition_data = DB::table('requisition')
                    ->select('requisition.*')
                    ->where(['id' => $requisition_id])->first();
        
        $reqtypeid=$requisition_data->requisition_type;
        $current_level=$requisition_data->next_level;
        
        $req_hierarchy_details = DB::table('requisition_hierarchy')
                ->select('requisition_hierarchy.*')
                ->where(['requisition_type_id' => $reqtypeid, 'level' => $current_level, 'status' => 1])->first();
        
        if(count($req_hierarchy_details)>0){
            if($req_hierarchy_details->approver_type=="TOP_MANAGER"){
                $manager = DB::table('employees')->select('employees.top_manager_id as manager_id')
                                ->whereRaw("employees.id=$requisition_data->created_by")->first();

                $approver_id = $manager->manager_id;
            } else {
                $approver_id = $req_hierarchy_details->approver_id;
            }
            
            if ($approver_id != $login_id) {
                $errmessage="You are not authorised to do this action !";
                return $errmessage;
            }
            
        } else {
            $errmessage="You are not authorised to do this action !";
            return $errmessage;
        }
        
        if($requisition_data->status==4){
            $errmessage="Requisition already approved !";
            return $errmessage;
        }
        
        if($requisition_data->status==5){
            $errmessage="Requisition already rejected !";
            return $errmessage;
        }
        
        if($requisition_data->next_level==null || $requisition_data->next_approver_id==null){
            $errmessage="Action taker is missing, please contact admin !";
            return $errmessage;
        }
        
        return "success";
    }
    
    public function showactionbotton($requisitionid) {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $requisition_data = DB::table('requisition')
                    ->select('requisition.*')
                    ->where(['id' => $requisitionid])->first();
        
        $reqtypeid=$requisition_data->requisition_type;
        $current_level=$requisition_data->next_level;
        
        $req_hierarchy_details = DB::table('requisition_hierarchy')
                ->select('requisition_hierarchy.*')
                ->where(['requisition_type_id' => $reqtypeid, 'level' => $current_level, 'status' => 1])->first();
        
        if(count($req_hierarchy_details)>0){
            if($req_hierarchy_details->approver_type=="TOP_MANAGER"){
                $manager = DB::table('employees')->select('employees.top_manager_id as manager_id')
                                ->whereRaw("employees.id=$requisition_data->created_by")->first();

                $approver_id = $manager->manager_id;
            } else {
                $approver_id = $req_hierarchy_details->approver_id;
            }
            
            if ($approver_id != $login_id) {
                return "No";
            }
            
        } else {
            return "No";
        }
        
        return "Yes";
    }
    
    public function checkpaymentvalidation($paymentid) {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $reqtype = DB::table('requisition_types as rq')
                ->select('rq.id as type_id')
                ->whereRaw("name='Payment Advice'")->first();
        
        $payment_data = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*')
                    ->where(['id' => $paymentid])->first();
        
        $current_level=$payment_data->level;
        $reqtypeid = $reqtype->type_id;
        
        $req_hierarchy_details = DB::table('requisition_hierarchy')
                ->select('requisition_hierarchy.*')
                ->where(['requisition_type_id' => $reqtypeid, 'level' => $current_level, 'status' => 1])->first();
        
        if(count($req_hierarchy_details)>0){
            $approver_id = $req_hierarchy_details->approver_id;
            if ($approver_id != $login_id) {
                $errmessage="You are not authorised to do this action !";
                return $errmessage;
            }
            
        } else {
            $errmessage="You are not authorised to do this action !";
            return $errmessage;
        }
        
        if($payment_data->status==2){
            $errmessage="Payment already approved !";
            return $errmessage;
        }
        
        if($payment_data->status==3){
            $errmessage="Payment already rejected !";
            return $errmessage;
        }
        
        if($payment_data->level==null){
            $errmessage="Action taker is missing, please contact admin !";
            return $errmessage;
        }
        
        return "success";
    }
    
}
