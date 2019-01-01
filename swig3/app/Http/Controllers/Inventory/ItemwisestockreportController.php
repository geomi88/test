<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use Customhelper;
use Exception;
use PDF;
use DB;

class ItemwisestockreportController extends Controller {

    public function index(Request $request) {
        try {
            $inventory_list = DB::table("inventory")
                    ->select("id", "product_code", "name")
                    ->where("status", "=", 1)
                    ->get();
            return view("inventory/itemwisestockreport/index", [
                "inventory" => $inventory_list,
                "inv_report_list" => [],
                "primary_total" => [],
                "Alt_total" => [],
                "total_val" => ''
            ]);
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory');
        }
    }

    public function inventoryunit() {
        try {
            $str = '';
            $Inventory_id = Input::get("inventory_id");
            $primary_unit = DB::table("inventory as a")
                    ->select("a.primary_unit", "b.name")
                    ->leftjoin("units as b", "b.id", "=", "a.primary_unit")
                    ->where("a.id", "=", $Inventory_id)
                    ->first();
            $units = DB::table("inventory_alternate_units as a")
                    ->select("a.unit_id", "b.name")
                    ->leftjoin("units as b", "b.id", "=", "a.unit_id")
                    ->where("a.inventory_item_id", "=", $Inventory_id)
                    ->get();
            $str .= '<option value="PRIMARY" selected>' . $primary_unit->name . '</option>';
            foreach ($units as $row) {
                $str .= '<option value="' . $row->unit_id . '">' . $row->name . '</option>';
            }
            return $str;
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function get_stock_list(){
        try{
            $inventory_id = Input::get("inventory_id");
            $location_id = Input::get("location_id");
            $unit_id = Input::get("unit_id");
            $primary_total = DB::table("inventory as a")
                        ->select("b.name",
                                DB::raw("1 as conv_value"))
                        ->leftjoin("units as b", "b.id", "=", "a.primary_unit")
                        ->where("a.id", "=", $inventory_id)
                        ->first();
            $Alt_total = DB::table("inventory_alternate_units as a")
                        ->select(DB::raw('COALESCE(a.conversion_value ,1) as conv_value'),"b.name")
                        ->leftjoin("units as b", "b.id", "=", "a.unit_id")
                        ->where("a.inventory_item_id", "=", $inventory_id)
                        ->get();
            
            if($unit_id == 'PRIMARY'){
                $conversion_value = DB::table("inventory as a")
                        ->select("b.name",DB::raw("1 as conv_value"))
                        ->leftjoin("units as b", "b.id", "=", "a.primary_unit")
                        ->where("a.id", "=", $inventory_id)
                        ->first();
            }
            else{
                $conversion_value = DB::table("inventory_alternate_units as a")
                        ->select(DB::raw('COALESCE(a.conversion_value ,1) as conv_value'),"b.name")
                        ->leftjoin("units as b", "b.id", "=", "a.unit_id")
                        ->where("a.unit_id", "=", $unit_id)
                        ->where("a.inventory_item_id", "=", $inventory_id)
                        ->first();
            }
            $unit = $conversion_value->name;
            if($conversion_value->conv_value > 0){
                $conv_value = $conversion_value->conv_value;
            }
            else{
                return -2;
            }
            
            $inv_report_list = DB::table("st_stock_info as a")
                    ->select("b.name", "a.stock_area_id", 
                            DB::raw("if(b.branch_code != '',CONCAT(' : ',b.branch_code),'') as branch_code"), 
                            DB::raw("SUM(a.stock_remaining) as stock_remaining"))
                    ->leftjoin("master_resources as b", "b.id", "=", "a.stock_area_id")
                    ->where("a.item_id", "=", $inventory_id)
                    ->where("a.status", "=", 1)
                    ->when(($location_id != ''), function($qry) use($location_id) {
                        if ($location_id == 1) {
                            return $qry->where("a.stock_area", "=", 1);
                        } else if ($location_id == 2) {
                            return $qry->where("a.stock_area", "=", 2);
                        }
                    })
                    ->groupBy("a.stock_area_id")
                    ->Having("stock_remaining", ">", 0)
                    ->get();
                    
            return view("inventory/itemwisestockreport/view", [
                "inv_report_list" => $inv_report_list,
                "conv_value" => $conv_value,
                "detailed_list" => [],
                "unit" => $unit,
                "total_val" => '',
                "primary_total" => $primary_total,
                "Alt_total" => $Alt_total
            ]);
        }
        catch(\Exception $e){
            return -1;
        }
    }
    
    public function get_detailed_view() {
        try {
            $inv_type = Input::get("inv_type");
            $inventory = Input::get("inventory");
            $unit_id = Input::get("unit_id");
            $unit = Input::get("unit");
            $conversion = Input::get("conversion");
            $detailed_list = DB::table("st_stock_info as a")
                    ->select("a.batch_code", "a.stock_area_id", "a.stock_remaining", DB::raw("date_format(a.mfg_date,'%d-%m-%Y') as mfg_date"), DB::raw("date_format(a.exp_date,'%d-%m-%Y') as exp_date"))
                    ->where("a.stock_area_id", "=", $inv_type)
                    ->where("a.item_id", "=", $inventory)
                    ->where("a.status", "=", 1)
                    ->where("a.stock_remaining", ">", 0)
                    ->groupby("a.id")
                    ->orderBy("a.created_at", "DESC")
                    ->get();
            return view("inventory/itemwisestockreport/detailedview", [
                "detailed_list" => $detailed_list,
                "conversion" => $conversion,
                "unit" => $unit
            ]);
        } catch (\Exception $e) {
            return -1;
        }
    }

}
