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
use App\Services\Stockfunctions;
use Customhelper;
use Exception;
use DB;
use PDF;
use App;
use Mail;

class OpeningstockController extends Controller {


    public function add() {
        
        $inventorydata = DB::table('inventory')
            ->select('product_code','name','id')
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
        $batchcode = $stokfuns->generateGrnCode(2);
            
        return view('purchase/opening_stock/add', array('inventorydata'=>$inventorydata,'warehouses'=>$warehouses,'branches'=>$branches,'batchcode'=>$batchcode));
    }
    
    public function updateopeningstock() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $created_by = Session::get('login_id');
            }

            $input = Input::all();

            $arraData = $input['arrData'];
            $stockData = json_decode($arraData);

            $arrStockList = $input["arrStockList"];
            $arrStockItems = json_decode($arrStockList);

            $batchcode = $stockData->batchcode;
            $stokfuns = new Stockfunctions();
            
            $duplicatecounts = DB::table('st_batch_master')->whereRaw("batch_code='$batchcode'")->count();
            if ($duplicatecounts > 0) {
                $batchcode = $stokfuns->generateGrnCode(2);
            }

            DB::beginTransaction();
            ///////////////////// insert in to batch master ///////////////////////
            $batchmodel = new Batch_master();
            $batchmodel->batch_code = $batchcode;
            $batchmodel->batch_type = 2;
            $batchmodel->created_by = $created_by;
            $batchmodel->save();

            $batchid = $batchmodel->id;
            $stockData->batch_id=$batchid;
            $stockData->batch_code=$batchcode;
            
            ///////////////////// update stock ///////////////////////
            $stokfuns->updateOPStock($stockData,$arrStockItems);
            
            DB::commit();
            
            Toastr::success('Stock Updated Successfully !', $title = null, $options = []);
            return 1;
            
        } catch (\Exception $ex) {
            DB::rollBack();
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    
    public function getinventoryunits() {
        try {
            $productid = Input::get('productid');
            
            $inventorydata = DB::table('inventory')
                    ->select('inventory.primary_unit','units.name as primaryunitname','track_manufacturing','track_expiry',
                            'inventory.company_id as itemcompany')
                    ->leftjoin('units', 'inventory.primary_unit','=','units.id')
                    ->whereRaw("inventory.status=1 AND inventory.id=$productid")
                    ->first();
            
            $altunits = DB::table('inventory_alternate_units')
                    ->select('inventory_alternate_units.*','units.name as altunitname')
                    ->leftjoin('units', 'inventory_alternate_units.unit_id','=','units.id')
                    ->whereRaw("inventory_alternate_units.inventory_item_id=$productid")
                    ->get();
            
            
            $arrData=array('inventorydata'=>$inventorydata,'altunits'=>$altunits);
            
            return \Response::json($arrData);
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
                    ->whereRaw("stock_area_id=$stockareaid AND item_id=$itemid AND stock_source=2 AND stock_remaining>0")
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
