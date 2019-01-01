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
use App\Models\Rfq;
use App\Models\Rfq_items;
use App\Services\Commonfunctions;
use Customhelper;
use Exception;
use DB;
use PDF;
use App;
use Mail;
use DateTime;

class RfqController extends Controller {
    
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
            $rfqcode= $common->generateRfqCode();
            
            
            return view('requisitions/rfq/add', array('suppliers'=>$suppliers,'rfqcode'=>$rfqcode));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('rfq');
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
            
//            $subfolder = "rfqdocs";
//            $s3 = \Storage::disk('s3');
//            $world = Config::get('app.WORLD');
            
            $doc_url = '';
//            if (Input::hasFile('req_doc')) {
//                $req_doc = Input::file('req_doc');
//                $extension = time() . '.' . $req_doc->getClientOriginalExtension();
//                $filePath = config('filePath.s3.bucket') . $world.'/';
//                $filePath = $filePath . $subfolder . '/' . $extension;
//                $s3filepath = $s3->put($filePath, file_get_contents($req_doc), 'public');
//                $doc_url = Storage::disk('s3')->url($filePath);
//            }
            
            $common = new Commonfunctions();
            $requisitioncode=$arrData->requisition_code;
            
            $duplicatecounts = DB::table('rfq')->whereRaw("rfq_code='$requisitioncode'")->count();
            if($duplicatecounts>0){
                $requisitioncode = $common->generateRfqCode();
            }
            
            if($arrData->planning_date){
                $planning_date=date("Y-m-d",  strtotime($arrData->planning_date));
            }else{
                $planning_date=NULL;
            }
            
            if($arrData->delivery_date){
                $delivery_date=date("Y-m-d",  strtotime($arrData->delivery_date));
            }else{
                $delivery_date=NULL;
            }
            
            if($arrData->stock_period_from){
                $stock_period_from=date("Y-m-d",  strtotime($arrData->stock_period_from));
            }else{
                $stock_period_from=NULL;
            }
            
            if($arrData->stock_period_to){
                $stock_period_to=date("Y-m-d",  strtotime($arrData->stock_period_to));
            }else{
                $stock_period_to=NULL;
            }
            
            if($arrData->forecast_from){
                $forecast_from=date("Y-m-d",  strtotime($arrData->forecast_from));
            }else{
                $forecast_from=NULL;
            }
            
            if($arrData->forecast_to){
                $forecast_to=date("Y-m-d",  strtotime($arrData->forecast_to));
            }else{
                $forecast_to=NULL;
            }
            
            if($arrData->last_purchase_date){
                $last_purchase_date=date("Y-m-d",  strtotime($arrData->last_purchase_date));
            }else{
                $last_purchase_date=NULL;
            }
            
            ///////////////////// insert in to rfq ///////////////////////
            $reqmodel = new Rfq();
            $reqmodel->company_id = $company_id;
            $reqmodel->rfq_code = $requisitioncode;
            $reqmodel->supplier_id = $arrData->supplier_id;
            $reqmodel->isallproducts = $arrData->isallproducts;
            $reqmodel->title = $arrData->title;
            $reqmodel->description = $arrData->description;
            $reqmodel->total_price = $arrData->total_price;
            $reqmodel->total_vat = $arrData->total_vat;
            
            $reqmodel->payment_mode = $arrData->payment_mode;
            $reqmodel->creditdays = $arrData->creditdays;
            $reqmodel->payment_terms = $arrData->payment_terms;
            $reqmodel->delivery_place = $arrData->delivery_place;
            $reqmodel->planning_date = $planning_date;
            $reqmodel->delivery_date = $delivery_date;
            
            $reqmodel->stock_in_hand = $arrData->stockinhand;
            $reqmodel->stock_period_from = $stock_period_from;
            $reqmodel->stock_period_to = $stock_period_to;
            $reqmodel->stock_total = $arrData->stocktotal;
            $reqmodel->forecast_from = $forecast_from;
            $reqmodel->forecast_to = $forecast_to;
            $reqmodel->product_spec = $arrData->productspec;
            $reqmodel->isapprovedsupplier = $arrData->approvedsupplier;
            $reqmodel->last_purchase_date = $last_purchase_date;
            $reqmodel->last_qty = $arrData->last_qty;
            $reqmodel->last_value = $arrData->last_value;
            
            $reqmodel->created_by = $created_by;
            $reqmodel->doc_url = $doc_url;

            $reqmodel->status = 1;
            $reqmodel->save();
            
            $rfq_id = $reqmodel->id;
            
            foreach ($arrItems as $item) {
                ///////////////////// insert in to rfq items ///////////////////////
                $reqitemsmodel = new Rfq_items();
                $reqitemsmodel->rfq_code = $requisitioncode;
                $reqitemsmodel->rfq_id = $rfq_id;
                $reqitemsmodel->item_id = $item->product_id;

                if ($item->isprimary == 1) {
                    $reqitemsmodel->purchase_in_primary_unit = 1;
                } else {
                    $reqitemsmodel->purchase_in_primary_unit = 0;
                }
                
                $reqitemsmodel->alternate_unit_id = $item->unit_id;
                $reqitemsmodel->quantity = $item->quantity;
                $reqitemsmodel->unit_price = $item->priceperunit;
                $reqitemsmodel->total_price = $item->price;
                if($item->qty_in_primary==""){
                     $item->qty_in_primary=$item->quantity;
                }
                $reqitemsmodel->qty_in_primary = $item->qty_in_primary;
                $reqitemsmodel->save();
             
            }
            
            Toastr::success('RFQ Created Successfully !', $title = null, $options = []);
            return 1;
            
        } catch (\Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function update() {
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
            $rfq_id = $arrData->requisition_id;
           
            
//            $subfolder = "requisitiondocs";
//            $s3 = \Storage::disk('s3');
//            $world = Config::get('app.WORLD');
            
            $doc_url = '';
//            if (Input::hasFile('req_doc')) {
//                $req_doc = Input::file('req_doc');
//                $extension = time() . '.' . $req_doc->getClientOriginalExtension();
//                $filePath = config('filePath.s3.bucket') . $world.'/';
//                $filePath = $filePath . $subfolder . '/' . $extension;
//                $s3filepath = $s3->put($filePath, file_get_contents($req_doc), 'public');
//                $doc_url = Storage::disk('s3')->url($filePath);
//            }
            
            if($arrData->planning_date){
                $planning_date=date("Y-m-d",  strtotime($arrData->planning_date));
            }else{
                $planning_date=NULL;
            }
            
            if($arrData->delivery_date){
                $delivery_date=date("Y-m-d",  strtotime($arrData->delivery_date));
            }else{
                $delivery_date=NULL;
            }
            
            if($arrData->stock_period_from){
                $stock_period_from=date("Y-m-d",  strtotime($arrData->stock_period_from));
            }else{
                $stock_period_from=NULL;
            }
            
            if($arrData->stock_period_to){
                $stock_period_to=date("Y-m-d",  strtotime($arrData->stock_period_to));
            }else{
                $stock_period_to=NULL;
            }
            
            if($arrData->forecast_from){
                $forecast_from=date("Y-m-d",  strtotime($arrData->forecast_from));
            }else{
                $forecast_from=NULL;
            }
            
            if($arrData->forecast_to){
                $forecast_to=date("Y-m-d",  strtotime($arrData->forecast_to));
            }else{
                $forecast_to=NULL;
            }
            
            if($arrData->last_purchase_date){
                $last_purchase_date=date("Y-m-d",  strtotime($arrData->last_purchase_date));
            }else{
                $last_purchase_date=NULL;
            }
            
            if($arrData->blnrfdstatus==1){
                $confirmed_by=$login_id;
            }else{
                $confirmed_by=NULL;
            }
            
            ///////////////////// modify requisition ///////////////////////
            DB::table('rfq')
                ->where(['id' => $rfq_id])
                ->update([
                            'supplier_id' => $arrData->supplier_id,
                            'description' => $arrData->description,
                            'total_price' => $arrData->total_price,
                            'total_vat' => $arrData->total_vat,
                            
                            'payment_mode' => $arrData->payment_mode,
                            'creditdays' => $arrData->creditdays,
                            'payment_terms' => $arrData->payment_terms,
                            'delivery_place' => $arrData->delivery_place,
                            'planning_date' => $planning_date,
                            'delivery_date' => $delivery_date,
                    
                            'stock_in_hand' => $arrData->stockinhand,
                            'stock_period_from' => $stock_period_from,
                            'stock_period_to' => $stock_period_to,
                            'stock_total' => $arrData->stocktotal,
                            'forecast_from' => $forecast_from,
                            'forecast_to' => $forecast_to,
                            'product_spec' => $arrData->productspec,
                            'isapprovedsupplier' => $arrData->approvedsupplier,
                            'last_purchase_date' => $last_purchase_date,
                            'last_qty' => $arrData->last_qty,
                            'last_value' => $arrData->last_value,
                            'confirm_status' => $arrData->blnrfdstatus,
                            'confirmed_by' => $confirmed_by,
                        ]);
           
            
            DB::table('rfq_items')
                ->where(['rfq_id' => $rfq_id])
                ->delete();
            
            foreach ($arrItems as $item) {
                 ///////////////////// insert in to rfq items ///////////////////////
                $reqitemsmodel = new Rfq_items();
                $reqitemsmodel->rfq_code = $arrData->rfq_code;
                $reqitemsmodel->rfq_id = $rfq_id;
                $reqitemsmodel->item_id = $item->product_id;

                if ($item->isprimary == 1) {
                    $reqitemsmodel->purchase_in_primary_unit = 1;
                } else {
                    $reqitemsmodel->purchase_in_primary_unit = 0;
                }
                
                $reqitemsmodel->alternate_unit_id = $item->unit_id;
                $reqitemsmodel->quantity = $item->quantity;
                $reqitemsmodel->unit_price = $item->priceperunit;
                $reqitemsmodel->total_price = $item->price;
                if($item->qty_in_primary==""){
                    $item->qty_in_primary=$item->quantity;
                }
                $reqitemsmodel->qty_in_primary = $item->qty_in_primary;
                $reqitemsmodel->save();
                
            }
    
           
            Toastr::success('RFQ Updated Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function delete($id){
        try {
             $dn=\Crypt::decrypt($id);
             DB::table('rfq_items')
                    ->where(['id' => $dn])                            
                    ->update(['status' => 2]);
             Toastr::success('RFQ Deleted Successfully', $title = null, $options = []);
             return Redirect::to('requisitions/editrfq');
        } catch(\Exception $e) {
             Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
             return Redirect::to('requisitions/editrfq');
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
    
    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            $rfqs = DB::table('rfq')
                    ->select('rfq.*','ac_party.code','ac_party.first_name')
                    ->leftjoin('ac_party','ac_party.id','=','rfq.supplier_id')
                    ->where('rfq.status','=',1)
                    ->where('rfq.confirm_status','!=',1)
                    ->orderby('rfq.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbyordcode = Input::get('searchbyordcode');
                $searchbytitle = Input::get('searchbytitle');
                $searchbysupcode = Input::get('searchbysupcode');
                $searchbysupname = Input::get('searchbysupname');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordpocode = Input::get('sortordpocode');
                $sortorddate = Input::get('sortorddate');
                $sortordsupcode = Input::get('sortordsupcode');
                $sortordsupname = Input::get('sortordsupname');

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
                if ($sortordpocode=='' && $sortorddate=='' && $sortordsupcode=='' && $sortordsupname=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $rfqs = DB::table('rfq')
                        ->select('rfq.*','ac_party.code','ac_party.first_name')
                        ->leftjoin('ac_party','ac_party.id','=','rfq.supplier_id')
                        ->where('rfq.status','=',1)
                        ->where('rfq.confirm_status','!=',1)
                        ->orderby('rfq.created_at', 'DESC')
                        ->when($searchbyordcode, function ($query) use ($searchbyordcode) {
                            return $query->whereRaw("rfq.rfq_code like '%$searchbyordcode%' ");
                        })
                        
                        ->when($searchbytitle, function ($query) use ($searchbytitle) {
                            return $query->whereRaw("rfq.title like '%$searchbytitle%' ");
                        })
                        ->when($searchbysupcode, function ($query) use ($searchbysupcode) {
                            return $query->whereRaw("ac_party.code like '$searchbysupcode%' ");
                        })
                        ->when($searchbysupname, function ($query) use ($searchbysupname) {
                            return $query->whereRaw("ac_party.first_name like '$searchbysupname%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(rfq.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(rfq.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordpocode, function ($query) use ($sortordpocode) {
                            return $query->orderby('rfq.id', $sortordpocode);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('rfq.created_at', $sortorddate);
                        })
                        ->when($sortordsupcode, function ($query) use ($sortordsupcode) {
                            return $query->orderby('ac_party.code', $sortordsupcode);
                        })
                        ->when($sortordsupname, function ($query) use ($sortordsupname) {
                            return $query->orderby('ac_party.first_name', $sortordsupname);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('rfq.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/rfq/results', array('rfqs' => $rfqs));
            }
            
            return view('requisitions/rfq/index', array('rfqs' => $rfqs));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/rfq');
        }
    }
    
     public function approvedlist(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            $rfqs = DB::table('rfq')
                    ->select('rfq.*','ac_party.code','ac_party.first_name',DB::raw("if(rfq.confirm_status=1,'Confirmed','Not Confirmed')  as rfq_status"))
                    ->leftjoin('ac_party','ac_party.id','=','rfq.supplier_id')
                    ->where('rfq.status','=',1)
                    ->where('rfq.confirm_status','=',1)
                    ->orderby('rfq.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbyordcode = Input::get('searchbyordcode');
                $searchbytitle = Input::get('searchbytitle');
                $searchbysupcode = Input::get('searchbysupcode');
                $searchbysupname = Input::get('searchbysupname');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordpocode = Input::get('sortordpocode');
                $sortorddate = Input::get('sortorddate');
                $sortordsupcode = Input::get('sortordsupcode');
                $sortordsupname = Input::get('sortordsupname');

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
                if ($sortordpocode=='' && $sortorddate=='' && $sortordsupcode=='' && $sortordsupname=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $rfqs = DB::table('rfq')
                        ->select('rfq.*','ac_party.code','ac_party.first_name',DB::raw("if(rfq.confirm_status=1,'Confirmed','Not Confirmed')  as rfq_status"))
                        ->leftjoin('ac_party','ac_party.id','=','rfq.supplier_id')
                        ->where('rfq.status','=',1)
                        ->where('rfq.confirm_status','=',1)
                        ->orderby('rfq.created_at', 'DESC')
                        ->when($searchbyordcode, function ($query) use ($searchbyordcode) {
                            return $query->whereRaw("rfq.rfq_code like '%$searchbyordcode%' ");
                        })
                        
                        ->when($searchbytitle, function ($query) use ($searchbytitle) {
                            return $query->whereRaw("rfq.title like '%$searchbytitle%' ");
                        })
                        ->when($searchbysupcode, function ($query) use ($searchbysupcode) {
                            return $query->whereRaw("ac_party.code like '$searchbysupcode%' ");
                        })
                        ->when($searchbysupname, function ($query) use ($searchbysupname) {
                            return $query->whereRaw("ac_party.first_name like '$searchbysupname%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(rfq.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(rfq.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordpocode, function ($query) use ($sortordpocode) {
                            return $query->orderby('rfq.id', $sortordpocode);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('rfq.created_at', $sortorddate);
                        })
                        ->when($sortordsupcode, function ($query) use ($sortordsupcode) {
                            return $query->orderby('ac_party.code', $sortordsupcode);
                        })
                        ->when($sortordsupname, function ($query) use ($sortordsupname) {
                            return $query->orderby('ac_party.first_name', $sortordsupname);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('rfq.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/rfq/results', array('rfqs' => $rfqs));
            }
            
            return view('requisitions/rfq/approvedlist', array('rfqs' => $rfqs));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/rfq');
        }
    }
    
    public function editindex(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            $rfqs = DB::table('rfq')
                    ->select('rfq.*','ac_party.code','ac_party.first_name')
                    ->leftjoin('ac_party','ac_party.id','=','rfq.supplier_id')
                    ->where('rfq.status','=',1)
                    ->where('rfq.confirm_status','=',2)
                    ->orderby('rfq.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbyordcode = Input::get('searchbyordcode');
                $searchbytitle = Input::get('searchbytitle');
                $searchbysupcode = Input::get('searchbysupcode');
                $searchbysupname = Input::get('searchbysupname');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordpocode = Input::get('sortordpocode');
                $sortorddate = Input::get('sortorddate');
                $sortordsupcode = Input::get('sortordsupcode');
                $sortordsupname = Input::get('sortordsupname');

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
                if ($sortordpocode=='' && $sortorddate=='' && $sortordsupcode=='' && $sortordsupname=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $rfqs = DB::table('rfq')
                        ->select('rfq.*','ac_party.code','ac_party.first_name')
                        ->leftjoin('ac_party','ac_party.id','=','rfq.supplier_id')
                        ->where('rfq.status','=',1)
                        ->where('rfq.confirm_status','=',2)
                        ->orderby('rfq.created_at', 'DESC')
                        ->when($searchbyordcode, function ($query) use ($searchbyordcode) {
                            return $query->whereRaw("rfq.rfq_code like '%$searchbyordcode%' ");
                        })
                        ->when($searchbytitle, function ($query) use ($searchbytitle) {
                            return $query->whereRaw("rfq.title like '%$searchbytitle%' ");
                        })
                        ->when($searchbysupcode, function ($query) use ($searchbysupcode) {
                            return $query->whereRaw("ac_party.code like '$searchbysupcode%' ");
                        })
                        ->when($searchbysupname, function ($query) use ($searchbysupname) {
                            return $query->whereRaw("ac_party.first_name like '$searchbysupname%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(rfq.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(rfq.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordpocode, function ($query) use ($sortordpocode) {
                            return $query->orderby('rfq.id', $sortordpocode);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('rfq.created_at', $sortorddate);
                        })
                        ->when($sortordsupcode, function ($query) use ($sortordsupcode) {
                            return $query->orderby('ac_party.code', $sortordsupcode);
                        })
                        ->when($sortordsupname, function ($query) use ($sortordsupname) {
                            return $query->orderby('ac_party.first_name', $sortordsupname);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('rfq.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/rfq/editresults', array('rfqs' => $rfqs));
            }
            
            return view('requisitions/rfq/editindex', array('rfqs' => $rfqs));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/rfq');
        }
    }
    
    public function view($id) {
        try{
            $id = \Crypt::decrypt($id);

            $rfqdata = DB::table('rfq')
                    ->select('rfq.*',
                            'supplier.first_name as supplierfname','supplier.alias_name as supplieraname','supplier.code as suppliercode',
                            'supplier.address as supplier_address','supplier_pin','country.name as supnation',
                            'supplier.bank_beneficiary_name','supplier.bank_iban_no','supplier.bank_branch_name',
                            'supplier.bank_account_number','supplier.bank_city','supplier.bank_swift_code','supplier.contact_person','supplier.contact_number',
                            'bank_nation.name as banknation','supplier.company_name','supplier.bank_name','supplier.cr_number',
                            'employees.username as empcode','employees.first_name as empname','employees.alias_name as empaname','master_resources.name as jobpos',
                            'approver.username as approvercode','approver.first_name as approvername','approver.alias_name as approveraname','m.name as approverjobpos',
                            db::raw("COALESCE(supplier.email,supplier.contact_email,'') as supemail"),db::raw("COALESCE(supplier.mobile_number,'') as supmob"))
                    ->leftjoin('ac_party as supplier', function($join){
                        $join->on('rfq.supplier_id','=','supplier.id')
                                ->whereRaw("supplier.party_type='Supplier'");
                        
                     })
                     ->leftjoin('country', 'supplier.nationality','=','country.id')
                     ->leftjoin('country as bank_nation', 'supplier.bank_country','=','bank_nation.id')
                     ->leftjoin('employees', 'rfq.created_by','=','employees.id')
                     ->leftjoin('employees as approver', 'rfq.confirmed_by','=','approver.id')
                     ->leftjoin('master_resources', 'employees.job_position','=','master_resources.id')
                     ->leftjoin('master_resources as m', 'approver.job_position','=','m.id')
                    ->where(['rfq.id' => $id])->first();
            
          
    
            $rfqitems = DB::table('rfq_items')
                    ->select('rfq_items.*','inventory.product_code','inventory.name as productname','units.name as unit')
                    ->leftjoin('inventory','rfq_items.item_id','=','inventory.id')
                    ->leftjoin('units','rfq_items.alternate_unit_id','=','units.id')
                    ->where('rfq_id','=',$rfqdata->id)
                    ->get();
            

                        
            $rfqdata->ordertotal=$rfqitems->sum('total_price');
            
            return view('requisitions/rfq/view', array('rfqdata'=>$rfqdata,'rfqitems'=>$rfqitems));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/rfq');
        }
    }
    

    
    public function edit($id) {
        try {
            $id = \Crypt::decrypt($id);
            $type="Supplier";
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $rfqdata = DB::table('rfq')
                    ->select('rfq.*',
                            'supplier.first_name as supplierfname','supplier.alias_name as supplieraname','supplier.code as suppliercode',
                            'supplier.address as supplier_address','supplier_pin','country.name as supnation',
                            'supplier.bank_beneficiary_name','supplier.bank_iban_no','supplier.bank_branch_name',
                            'supplier.bank_account_number','supplier.bank_city','supplier.bank_swift_code','supplier.contact_person','supplier.contact_number',
                            'bank_nation.name as banknation','supplier.company_name','supplier.bank_name','supplier.cr_number',
                            'employees.username as empcode','employees.first_name as empname','employees.alias_name as empaname','master_resources.name as jobpos',
                            db::raw("COALESCE(supplier.email,supplier.contact_email,'') as supemail"),db::raw("COALESCE(supplier.mobile_number,'') as supmob"))
                    ->leftjoin('ac_party as supplier', function($join){
                        $join->on('rfq.supplier_id','=','supplier.id')
                                ->whereRaw("supplier.party_type='Supplier'");
                        
                     })
                     ->leftjoin('country', 'supplier.nationality','=','country.id')
                     ->leftjoin('country as bank_nation', 'supplier.bank_country','=','bank_nation.id')
                     ->leftjoin('employees', 'rfq.created_by','=','employees.id')
                     ->leftjoin('master_resources', 'employees.job_position','=','master_resources.id')
                    ->where(['rfq.id' => $id])->first();
                     
            $fdate = $rfqdata->stock_period_from;
            $tdate = $rfqdata->stock_period_to;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');
            
            $rfqdata->indays = $days+1;
            
            $fromdate = $rfqdata->forecast_from;
            $todate = $rfqdata->forecast_to;
            $datetimef = new DateTime($fromdate);
            $datetimet = new DateTime($todate);
            $interva = $datetimef->diff($datetimet);
            $daysr = $interva->format('%a');
            
            $rfqdata->indaysrfq = $daysr+1;
    
            $rfqitems = DB::table('rfq_items as rqit')
                    ->select('rqit.item_id as product_id', 'inventory.product_code as product_code', 'inventory.name as product_name', 'rqit.quantity as quantity', 'rqit.alternate_unit_id as unit_id', 'units.name as unitname', 'rqit.unit_price as priceperunit', 'rqit.qty_in_primary as qty_in_primary', 'rqit.total_price as price', 'purchase_in_primary_unit as isprimary')
                    ->leftjoin('inventory','rqit.item_id','=','inventory.id')
                    ->leftjoin('units','rqit.alternate_unit_id','=','units.id')
                    ->where('rfq_id','=',$rfqdata->id)
                    ->get();
            
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
                    ->whereRaw("ac_party.id=$rfqdata->supplier_id")
                    ->first();
            
            $supplierdata->creditlimitformated=Customhelper::numberformatter($supplierdata->credit_limit);
//            
            $documents = DB::table('requisition_docs')
                    ->select('requisition_docs.*',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                    ->leftjoin('employees', 'requisition_docs.created_by','=','employees.id')
                    ->where(['requisition_docs.requisition_id' => $id])->get();
            
            ///////////////// Budget information/////////////
           // $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
            $strQuarter = $rfqdata->created_at; 
            $arrQua = explode(" ", $strQuarter); 
            $date = $arrQua[0]; 
            $dat = explode("-", $date); 
            $Year = $dat[0];   
            $Month = $dat[1];
          //  $Quarter = $arrMonthQuarter["$Month"];
            
           
            $common = new Commonfunctions();
            for($i=0;$i<count($rfqitems);$i++){
                $type1 = 'Inventory';
                $productid =$rfqitems[$i]->product_id;
                $usedBudget = $common->usedQuantityBudgetInfo($productid);
                $budgetDetails = $common->getBudgetInfo($Year,$type1,$productid);
                
                if($budgetDetails){
                    $remQuantity=($budgetDetails->quantity)-($usedBudget->quantity);
                    $remPrice=($budgetDetails->price)-($usedBudget->totalPrice);

                    $rfqitems[$i]->initialPrice=$budgetDetails->price;
                    $rfqitems[$i]->initialQuantity=$budgetDetails->quantity;

                    $rfqitems[$i]->remQuantity=$remQuantity;
                    $rfqitems[$i]->remPrice=$remPrice;
                }else{
                    $rfqitems[$i]->initialPrice=0;
                    $rfqitems[$i]->initialQuantity=0;

                    $rfqitems[$i]->remQuantity=0;
                    $rfqitems[$i]->remPrice=0;
                }
            }
            
            $budgetdata = $common->getBudgetInfo($Year,"Supplier",$rfqdata->supplier_id);
            $usedData = $common->usedPriceInfoOfLedger($rfqdata->supplier_id,$type);
            
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
            
            $arret=array('rfqdata'=>$rfqdata,'rfqitems'=>$rfqitems,'supplierdata'=>$supplierdata,'suppliers'=>$allsuppliers,'budgetdata'=>$budgetdata,'usedData'=>$usedData,'documents'=>$documents);
            
            return view('requisitions/rfq/edit',$arret);
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/editrfq');
        }
    }
    
    public function mailto_supplier() {
        try {
            $id = Input::get('rfq_id');
            $rfqdata = DB::table('rfq')
                    ->select('rfq.*',
                            'supplier.first_name as supplierfname','supplier.alias_name as supplieraname','supplier.code as suppliercode',
                            'supplier.address as supplier_address','supplier_pin','country.name as supnation',
                            'supplier.bank_beneficiary_name','supplier.bank_iban_no','supplier.bank_branch_name',
                            'supplier.bank_account_number','supplier.bank_city','supplier.bank_swift_code','supplier.contact_person','supplier.contact_number',
                            'bank_nation.name as banknation','supplier.company_name','supplier.bank_name','supplier.cr_number','supplier.contact_email as sendmail',
                            'employees.username as empcode','employees.first_name as empname','employees.alias_name as empaname','master_resources.name as jobpos',
                            'approver.username as approvercode','approver.first_name as approvername','approver.alias_name as approveraname','m.name as approverjobpos',
                            db::raw("COALESCE(supplier.email,supplier.contact_email,'') as supemail"),db::raw("COALESCE(supplier.mobile_number,'') as supmob"))
                    ->leftjoin('ac_party as supplier', function($join){
                        $join->on('rfq.supplier_id','=','supplier.id')
                                ->whereRaw("supplier.party_type='Supplier'");
                        
                    })
                    ->leftjoin('country', 'supplier.nationality','=','country.id')
                    ->leftjoin('country as bank_nation', 'supplier.bank_country','=','bank_nation.id')
                    ->leftjoin('employees', 'rfq.created_by','=','employees.id')
                    ->leftjoin('employees as approver', 'rfq.confirmed_by','=','approver.id')
                    ->leftjoin('master_resources', 'employees.job_position','=','master_resources.id')
                    ->leftjoin('master_resources as m', 'approver.job_position','=','m.id')
                    ->where(['rfq.id' => $id])
                    ->first();
          
    
            $rfqitems = DB::table('rfq_items')
                    ->select('rfq_items.*','inventory.product_code','inventory.name as productname','units.name as unit')
                    ->leftjoin('inventory','rfq_items.item_id','=','inventory.id')
                    ->leftjoin('units','rfq_items.alternate_unit_id','=','units.id')
                    ->where('rfq_id','=',$rfqdata->id)
                    ->get();

            if($rfqdata->sendmail){
                
                Mail::send('emailtemplates.plain', array('strmessage' => ''),function($message) use($rfqdata,$rfqitems)
                {
                    $pdf = PDF::loadView('rfq.print', array('rfqdata'=>$rfqdata,'rfqitems'=>$rfqitems));
                    $message->to($rfqdata->sendmail)->subject($rfqdata->rfq_code." / ".date('d-m-Y',  strtotime($rfqdata->created_at)));
                    $message->attachData($pdf->output(), 'rfq.pdf');
                });
                
                if (Mail::failures()) {
                    return -1;
                }
                
                DB::table('rfq')
                    ->where(['id' => $id])                            
                    ->update(['mailed_status' => 1]);
                
                return 1;
                
            }else{
                return -1;
            }
            
        } catch (\Exception $e) {
            
            return -1;
        }
    }
}