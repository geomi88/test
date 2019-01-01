<?php

namespace App\Http\Controllers\Purchase;

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
use App\Models\Batch_master;
use App\Models\Stock_info;
use App\Models\Stock_history;
use App\Services\Stockfunctions;
use Customhelper;
use Exception;
use DB;
use PDF;
use App;
use Mail;

class PhysicalstockController extends Controller {


    public function add() {
        
        $inventorydata = DB::table('inventory')
            ->select('product_code','name','id','primary_unit')
            ->whereRaw("status=1")
            ->get();
        
        $warehouses = DB::table('master_resources')
            ->select('master_resources.id', 'master_resources.name')
            ->where(['resource_type' => 'WAREHOUSE'])
            ->where('status', '=', 1)
            ->orderby('name', 'ASC')->get();
        
        $branches = DB::table('master_resources')
            ->select('master_resources.id', 'branch_code','master_resources.name')
            ->where(['resource_type' => 'BRANCH'])
            ->where('status', '=', 1)
            ->orderby('name', 'ASC')->get();
                    
        $stokfuns = new Stockfunctions();
        $batchcode = $stokfuns->generateGrnCode(3);
            
        return view('purchase/physical_stock/add', array('inventorydata'=>$inventorydata,'warehouses'=>$warehouses,'branches'=>$branches,'batchcode'=>$batchcode,'inventorybaseinfo'=>array(),'altunits'=>'','batches'=>array()));
    }
    
    public function updatephysicalstock() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $created_by = Session::get('login_id');
            }

            $input = Input::all();

            $arraData = $input['stockNewData'];
            $stockNewData = json_decode($arraData);

            $arrStockList = $input["arrStockList"];
            $arrStockItems = json_decode($arrStockList);
            
            DB::beginTransaction();
            
            //////////////////Update physical stock/////////////////////
            foreach ($arrStockItems as $stock) {
                $result=DB::table('st_stock_info')
                        ->where(['id' => $stock->stockid])
                        ->update(['stock_remaining' => $stock->qtyinprimary,'stock_source'=>3]);
                
                if($result){
                
                    $rowdata= Stock_info::find($stock->stockid);
                 
                    /////////////////Enter to stock history/////////////////////////
                    $stockhistmodel = new Stock_history();
                    $stockhistmodel->purchase_order_id = $rowdata->purchase_order_id;
                    $stockhistmodel->batch_id = $rowdata->batch_id;
                    $stockhistmodel->batch_code = $rowdata->batch_code;
                    $stockhistmodel->stock_area = $rowdata->stock_area;
                    $stockhistmodel->stock_area_id = $rowdata->stock_area_id;
                    $stockhistmodel->item_id = $rowdata->item_id;
                    $stockhistmodel->purchase_quantity = $rowdata->purchase_quantity;
                    $stockhistmodel->purchase_unit = $rowdata->purchase_unit;

                    $stockhistmodel->is_primary_unit = $rowdata->is_primary_unit;
                    $stockhistmodel->company_id = $rowdata->company_id;
                    $stockhistmodel->stock_source = 3;
                    $stockhistmodel->quantity_in_primary_unit = $rowdata->quantity_in_primary_unit;
                    $stockhistmodel->stock_remaining = $stock->qtyinprimary;
                    $stockhistmodel->mfg_date = $rowdata->mfg_date;
                    $stockhistmodel->exp_date = $rowdata->exp_date;
                    $stockhistmodel->unit_price = $rowdata->unit_price;
                    $stockhistmodel->updated_by = $created_by;
                    $stockhistmodel->status = 1;

                    $stockhistmodel->save();
                }
            }
            
            
            if(!empty($stockNewData)){
            
                ///////////////////Create new physical stock///////////////////////
                $batchcode = $stockNewData->batchcode;
                $stokfuns = new Stockfunctions();

                $duplicatecounts = DB::table('st_batch_master')->whereRaw("batch_code='$batchcode'")->count();
                if ($duplicatecounts > 0) {
                    $batchcode = $stokfuns->generateGrnCode(3);
                }
                
                ///////////////////// insert in to batch master ///////////////////////
                $batchmodel = new Batch_master();
                $batchmodel->batch_code = $batchcode;
                $batchmodel->batch_type = 3;
                $batchmodel->created_by = $created_by;
                $batchmodel->save();

                $batchid = $batchmodel->id;
                $stockNewData->batch_id=$batchid;
                $stockNewData->batch_code=$batchcode;

                ///////////////////// update stock ///////////////////////
                $stokfuns->updatePHStock($stockNewData);
            }
            
            DB::commit();
            
            Toastr::success('Stock Updated Successfully !', $title = null, $options = []);
            return 1;
            
        } catch (\Exception $ex) {
            DB::rollBack();
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    
    public function getbatches() {
        try {
            $item_id = Input::get('item_id');
            $stockareaid = Input::get('stockarea_id');
            
            $inventorydata = DB::table('inventory')
                    ->select('inventory.primary_unit','units.name as primaryunitname','inventory.company_id as itemcompany')
                    ->leftjoin('units', 'inventory.primary_unit','=','units.id')
                    ->whereRaw("inventory.status=1 AND inventory.id=$item_id")
                    ->first();
            
            $altunits = DB::table('inventory_alternate_units')
                    ->select('inventory_alternate_units.*','units.name as altunitname')
                    ->leftjoin('units', 'inventory_alternate_units.unit_id','=','units.id')
                    ->whereRaw("inventory_alternate_units.inventory_item_id=$item_id")
                    ->get();
            
            $altunithtml='<option value="'.$inventorydata->primary_unit.'" attrconversionval="1">'.$inventorydata->primaryunitname.'</option>';
            foreach ($altunits as $unit){
                $altunithtml.='<option value="'.$unit->unit_id.'" attrconversionval="'.$unit->conversion_value.'">'.$unit->altunitname.'</option>';
            }
            
            $batches = DB::table('st_stock_info')
                    ->select('st_stock_info.id as stock_id','batch_code','purchase_quantity','stock_remaining','units.name as unitname')
                    ->leftjoin('units', 'st_stock_info.purchase_unit','=','units.id')
                    ->whereRaw("stock_area_id=$stockareaid AND item_id=$item_id AND stock_remaining>0")
                    ->get();
            
            return view('purchase/physical_stock/result', array('inventorybaseinfo'=>$inventorydata,'batches'=>$batches,'altunits'=>$altunithtml));
            
        } catch (\Exception $e) {
            return -1;
        }
    }
    
    public function getprevopeningstock() {
        try {
            $itemid = Input::get('itemid');
            $stockareaid = Input::get('stockareaid');
            $stockarea = Input::get('stockarea');
           
            $openingstocks = DB::table('st_stock_info')
                    ->select('batch_code','purchase_quantity','units.name as unitname','m.name as areaname',
                            'm.branch_code as areacode')
                    ->leftjoin('units', 'st_stock_info.purchase_unit','=','units.id')
                    ->leftjoin('master_resources as m', 'st_stock_info.stock_area_id','=','m.id')
                    ->whereRaw("stock_area_id=$stockareaid AND item_id=$itemid AND stock_source=2")
                    ->get();
            
            if(count($openingstocks)>0){
                $strHtmlBasic = '<tr><td>Warehouse/ Branch Name :</td><td>'.$openingstocks[0]->areaname.'</td><td></td></tr><tr><td>Batch No</td><td>Unit</td><td>Stock</td></tr>';
                foreach ($openingstocks as $stock) {
                    $strHtmlBasic .= '<tr><td>'.$stock->batch_code.'</td><td>'.$stock->unitname.'</td><td>'.$stock->purchase_quantity.'</td></tr>';
                }
                
            }else{
                return -1;
            }
                        
            echo $strHtmlBasic;
            
        } catch (\Exception $e) {
            return -1;
        }
    }
    
}
