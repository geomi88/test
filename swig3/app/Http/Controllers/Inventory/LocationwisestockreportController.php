<?php

namespace App\Http\Controllers\Inventory;

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
use Customhelper;
use Exception;
use DB;
use App;
use Mail;

class LocationwisestockreportController extends Controller {

    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            
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
            
            $inventorydata = DB::table('inventory')
                ->select('product_code','name','id')
                ->whereRaw("status=1")
                ->get();
               
            return view('inventory/locationwisestockreport/index', array('stockdata' => [],'warehouses'=>$warehouses,'branches'=>$branches,'inventorydata'=>$inventorydata));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory');
        }
    }
    
    public function getstocklist(){
        try{
            $stockareaid = Input::get("stockareaid");
            $itemid = Input::get("itemid");
            
            $stockData = DB::table('st_stock_info as st')
                ->select('inv.id as item_id', 'inv.name','inv.product_code','units.name as unitname',
                        DB::raw("SUM(st.stock_remaining) as stock_remaining"))
                ->leftjoin("inventory as inv","st.item_id","=","inv.id")
                ->leftjoin("units","inv.primary_unit","=","units.id")
                ->when($itemid, function ($query) use ($itemid) {
                       return $query->whereRaw("st.item_id=$itemid");
                })
                ->where("st.stock_area_id","=",$stockareaid)
                ->where("st.status","=",1)
                ->groupBy("st.item_id")
                ->Having("stock_remaining",">",0)
                ->get();
           
            return view("inventory/locationwisestockreport/result",[
                "stockdata"=>$stockData]);
            
        }catch(\Exception $e){
            return -1;
        }
    }
    
    public function getbatchlist(){
        try{
            $item_id = Input::get("item_id");
            $stockareaid = Input::get("stockareaid");
            $batch_list = DB::table("st_stock_info as st")
                    ->select("st.batch_code","st.stock_remaining","st.mfg_date","st.exp_date")
                    ->where("st.stock_area_id","=",$stockareaid)
                    ->where("st.item_id","=",$item_id)
                    ->whereRaw("st.stock_remaining>0")
                    ->orderBy('st.created_at','DESC')
                    ->get();
            
            $item_det = DB::table('inventory')
                    ->select('inventory.id','primary_unit','units.name as unitname')
                    ->leftjoin('units', 'inventory.primary_unit','=','units.id')
                    ->whereRaw("inventory.id=$item_id")
                    ->first();
            
            $altunits = DB::table('inventory_alternate_units as altunit')
                    ->select('altunit.*','units.name as altunitname')
                    ->leftjoin('units', 'altunit.unit_id','=','units.id')
                    ->whereRaw("altunit.inventory_item_id=$item_id AND altunit.conversion_value!='' AND altunit.conversion_value IS NOT NULL")
                    ->get();
                        
            return view("inventory/locationwisestockreport/batchlist",[
                "batch_list"=>$batch_list,'item_det'=>$item_det,'altunits'=>$altunits
            ]);
            
        }catch(\Exception $e){
            return -1;
        }
    }
    
}
