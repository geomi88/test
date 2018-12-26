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
use App\Models\Stock_info;
use App\Models\Stock_history;
use Customhelper;
use Exception;
use DB;
use PDF;
use Mail;

class Stockfunctions {

    public function generateGrnCode($type) {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $employee = DB::table('employees')
            ->select('employees.username as usercode')
            ->where(['id'=>$login_id])
            ->first();
        
        if($type==1){
            $strStart='GRN';
        }
        
        if($type==2){
            $strStart='OPS';
        }
        
        if($type==3){
            $strStart='PHS';
        }
        $maxid = DB::table('st_batch_master')->where(['created_by'=>$login_id])->max('id')+1;
        $batchcode="$strStart-".$employee->usercode."-".$maxid;
        $batchcode= strtoupper($batchcode);
        return $batchcode;
    }
    
    public function convertQtyToPrimaryUnit($stockitems) {
        try {
        
            foreach ($stockitems as $item) {
                if($item->isprimary==0){
                    $invetoryinfo = DB::table('inventory_alternate_units')
                           ->select('conversion_value')
                           ->whereRaw("inventory_item_id=$item->itemid AND unit_id=$item->unitid")
                           ->first();

                    if($invetoryinfo){
                        if($invetoryinfo->conversion_value==null){
                            return array('msg'=>'Invalid conversion value for unit, please visit inventory!');
                        }
                        $qtyinprimary=$item->quantity*$invetoryinfo->conversion_value;
                        $item->qtyinprimary=round($qtyinprimary,2);
                    }else{
                        return array('msg'=>'Unit does not exist for this item!');
                    }

                }else{
                    $item->qtyinprimary=$item->quantity;
                }
             }
             
             return array('msg'=>'Success','stockitems'=>$stockitems);
             
        } catch (\Exception $ex) {
           
            return array('msg'=>'Sorry there was some problem!');
        }
  
    }
    
    public function updateStock($stockData,$arrStockItems) {
        
        if (Session::get('login_id')) {
            $created_by = Session::get('login_id');
        }

        foreach ($arrStockItems as $item) {

            if (isset($item->mfddate) && $item->mfddate) {
                $mfddate = date("Y-m-d", strtotime($item->mfddate));
            } else {
                $mfddate = NULL;
            }

            if (isset($item->expdate) && $item->expdate) {
                $expdate = date("Y-m-d", strtotime($item->expdate));
            } else {
                $expdate = NULL;
            }

            /////////////////Enter to stock/////////////////////////
            $stockmodel = new Stock_info();
            $stockmodel->purchase_order_id = $stockData->po_id;
            $stockmodel->batch_id = $stockData->batch_id;
            $stockmodel->batch_code = $stockData->batch_code;
            $stockmodel->stock_area = 1;
            $stockmodel->stock_area_id = $stockData->warehouse;
            $stockmodel->item_id = $item->itemid;
            $stockmodel->purchase_quantity = $item->quantity;
            $stockmodel->purchase_unit = $item->unitid;

            $stockmodel->is_primary_unit = $item->isprimary;
            $stockmodel->company_id = $item->itemcompany;
            $stockmodel->stock_source = 1;
            $stockmodel->quantity_in_primary_unit = $item->qtyinprimary;
            $stockmodel->stock_remaining = $item->qtyinprimary;
            $stockmodel->mfg_date = $mfddate;
            $stockmodel->exp_date = $expdate;
            $stockmodel->unit_price = $item->unitprice;
            $stockmodel->updated_by = $created_by;
            $stockmodel->status = 1;

            $stockmodel->save();

            /////////////////Enter to stock history/////////////////////////
            $stockhistmodel = new Stock_history();
            $stockhistmodel->purchase_order_id = $stockData->po_id;
            $stockhistmodel->batch_id = $stockData->batch_id;
            $stockhistmodel->batch_code = $stockData->batch_code;
            $stockhistmodel->stock_area = 1;
            $stockhistmodel->stock_area_id = $stockData->warehouse;
            $stockhistmodel->item_id = $item->itemid;
            $stockhistmodel->purchase_quantity = $item->quantity;
            $stockhistmodel->purchase_unit = $item->unitid;

            $stockhistmodel->is_primary_unit = $item->isprimary;
            $stockhistmodel->company_id = $item->itemcompany;
            $stockhistmodel->stock_source = 1;
            $stockhistmodel->quantity_in_primary_unit = $item->qtyinprimary;
            $stockhistmodel->stock_remaining = $item->qtyinprimary;
            $stockhistmodel->mfg_date = $mfddate;
            $stockhistmodel->exp_date = $expdate;
            $stockhistmodel->unit_price = $item->unitprice;
            $stockhistmodel->updated_by = $created_by;
            $stockhistmodel->status = 1;

            $stockhistmodel->save();
        }

        if ($stockData->stock_entered != 1) {
            DB::table('ac_purchase_order')
                    ->where(['id' => $stockData->po_id])
                    ->update(['stock_entered' => 1]);
        }
        
        return 1;
    }
    
    
    public function updateOPStock($stockData,$arrStockItems) {
        
        if (Session::get('login_id')) {
            $created_by = Session::get('login_id');
        }

        foreach ($arrStockItems as $item) {

            if (isset($item->mfgdate) && $item->mfgdate) {
                $mfddate = date("Y-m-d", strtotime($item->mfgdate));
            } else {
                $mfddate = NULL;
            }

            if (isset($item->expdate) && $item->expdate) {
                $expdate = date("Y-m-d", strtotime($item->expdate));
            } else {
                $expdate = NULL;
            }

            /////////////////Enter to stock/////////////////////////
            $stockmodel = new Stock_info();
            $stockmodel->purchase_order_id = NULL;
            $stockmodel->batch_id = $stockData->batch_id;
            $stockmodel->batch_code = $stockData->batch_code;
            $stockmodel->stock_area = $item->stockarea;
            $stockmodel->stock_area_id = $item->stockareaid;
            $stockmodel->item_id = $item->itemid;
            $stockmodel->purchase_quantity = $item->quantity;
            $stockmodel->purchase_unit = $item->unitid;

            $stockmodel->is_primary_unit = $item->isprimary;
            $stockmodel->company_id = $item->itemcompany;
            $stockmodel->stock_source = 2;
            $stockmodel->quantity_in_primary_unit = $item->qtyinprimary;
            $stockmodel->stock_remaining = $item->qtyinprimary;
            $stockmodel->mfg_date = $mfddate;
            $stockmodel->exp_date = $expdate;
            $stockmodel->unit_price = $item->unitprice;
            $stockmodel->updated_by = $created_by;
            $stockmodel->status = 1;

            $stockmodel->save();

            /////////////////Enter to stock history/////////////////////////
            $stockhistmodel = new Stock_history();
            $stockhistmodel->purchase_order_id = NULL;
            $stockhistmodel->batch_id = $stockData->batch_id;
            $stockhistmodel->batch_code = $stockData->batch_code;
            $stockhistmodel->stock_area = $item->stockarea;
            $stockhistmodel->stock_area_id = $item->stockareaid;
            $stockhistmodel->item_id = $item->itemid;
            $stockhistmodel->purchase_quantity = $item->quantity;
            $stockhistmodel->purchase_unit = $item->unitid;

            $stockhistmodel->is_primary_unit = $item->isprimary;
            $stockhistmodel->company_id = $item->itemcompany;
            $stockhistmodel->stock_source = 2;
            $stockhistmodel->quantity_in_primary_unit = $item->qtyinprimary;
            $stockhistmodel->stock_remaining = $item->qtyinprimary;
            $stockhistmodel->mfg_date = $mfddate;
            $stockhistmodel->exp_date = $expdate;
            $stockhistmodel->unit_price = $item->unitprice;
            $stockhistmodel->updated_by = $created_by;
            $stockhistmodel->status = 1;

            $stockhistmodel->save();
        }

        return 1;
    }

    public function updatePHStock($stockData) {
       
        if (Session::get('login_id')) {
            $created_by = Session::get('login_id');
        }
        
        if (isset($stockData->mfgdate) && $stockData->mfgdate) {
            $mfddate = date("Y-m-d", strtotime($stockData->mfgdate));
        } else {
            $mfddate = NULL;
        }

        if (isset($stockData->expdate) && $stockData->expdate) {
            $expdate = date("Y-m-d", strtotime($stockData->expdate));
        } else {
            $expdate = NULL;
        }

        /////////////////Enter to stock/////////////////////////
        $stockmodel = new Stock_info();
        $stockmodel->purchase_order_id = NULL;
        $stockmodel->batch_id = $stockData->batch_id;
        $stockmodel->batch_code = $stockData->batch_code;
        $stockmodel->stock_area = $stockData->stockarea;
        $stockmodel->stock_area_id = $stockData->stockareaid;
        $stockmodel->item_id = $stockData->item_id;
        $stockmodel->purchase_quantity = $stockData->quantity;
        $stockmodel->purchase_unit = $stockData->unitid;

        $stockmodel->is_primary_unit = $stockData->isprimary;
        $stockmodel->company_id = $stockData->itemcompany;
        $stockmodel->stock_source = 3;
        $stockmodel->quantity_in_primary_unit = $stockData->qtyinprimary;
        $stockmodel->stock_remaining = $stockData->qtyinprimary;
        $stockmodel->mfg_date = $mfddate;
        $stockmodel->exp_date = $expdate;
        $stockmodel->unit_price = $stockData->unitprice;
        $stockmodel->updated_by = $created_by;
        $stockmodel->status = 1;

        $stockmodel->save();

        /////////////////Enter to stock history/////////////////////////
        $stockhistmodel = new Stock_history();
        $stockhistmodel->purchase_order_id = NULL;
        $stockhistmodel->batch_id = $stockData->batch_id;
        $stockhistmodel->batch_code = $stockData->batch_code;
        $stockhistmodel->stock_area = $stockData->stockarea;
        $stockhistmodel->stock_area_id = $stockData->stockareaid;
        $stockhistmodel->item_id = $stockData->item_id;
        $stockhistmodel->purchase_quantity = $stockData->quantity;
        $stockhistmodel->purchase_unit = $stockData->unitid;

        $stockhistmodel->is_primary_unit = $stockData->isprimary;
        $stockhistmodel->company_id = $stockData->itemcompany;
        $stockhistmodel->stock_source = 3;
        $stockhistmodel->quantity_in_primary_unit = $stockData->qtyinprimary;
        $stockhistmodel->stock_remaining = $stockData->qtyinprimary;
        $stockhistmodel->mfg_date = $mfddate;
        $stockhistmodel->exp_date = $expdate;
        $stockhistmodel->unit_price = $stockData->unitprice;
        $stockhistmodel->updated_by = $created_by;
        $stockhistmodel->status = 1;

        $stockhistmodel->save();

        return 1;
    }

}
