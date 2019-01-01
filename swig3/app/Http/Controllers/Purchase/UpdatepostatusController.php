<?php

namespace App\Http\Controllers\Purchase;

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
use App\Models\Batch_master;
use App\Models\Stock_info;
use App\Services\Stockfunctions;
use Customhelper;
use Exception;
use DB;
use PDF;
use App;
use Mail;

class UpdatepostatusController extends Controller
{
    public function index(Request $request){
        try{
            $Action_list = [];
            $paginate = Config::get("app.PAGINATE");
            if($request->ajax()){
                $searchbyponumber = Input::get("searchbyponumber");
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                $fromdata = (Input::get("fromdata") ? date("Y-m-d", strtotime(Input::get("fromdata"))) : '');
                $todate = (Input::get("todate") ? date("Y-m-d", strtotime(Input::get("todate"))) : '');
                $Action_list = DB::table("st_po_actions as stp")
                        ->leftjoin("ac_purchase_order as apo","apo.id","=","stp.purchase_order_id")
                        ->select("stp.id","stp.purchase_order_id","apo.order_code as purchase_number","stp.created_at",
                                DB::raw("(select first_name FROM employees e where e.id = stp.updated_by) as created_by"),
                                DB::raw("case when stp.actions = 1 THEN 'Sent To Supplier' WHEN stp.actions=2 THEN 'Sent To Warehouse' when stp.actions = 3 THEN 'Cancelled' END as action_made"))
                        ->when($searchbyponumber, function($qry) use ($searchbyponumber){
                            return $qry->where("apo.order_code","LIKE","%$searchbyponumber%");
                        })
                        ->when($fromdata, function($qry) use ($fromdata){
                            return $qry->whereraw("(date_format(stp.created_at,'%Y-%m-%d') >= '$fromdata' )");
                        })
                        ->when($todate, function($qry) use ($todate){
                            return $qry->whereraw("(date_format(stp.created_at,'%Y-%m-%d') <= '$todate' )");
                        })
                        ->where("apo.mailed_status","=","1")
                        ->groupBy("stp.purchase_order_id")
                        ->orderBy("stp.id","DESC")
                        ->paginate($paginate);
                return view("purchase/update_po_status/results",["action_list"=>$Action_list]);
            }
            else{
                $Action_list = DB::table("st_po_actions as stp")
                        ->leftjoin("ac_purchase_order as apo","apo.id","=","stp.purchase_order_id")
                        ->select("stp.id","stp.purchase_order_id","apo.order_code as purchase_number","apo.created_at",
                                DB::raw("(select first_name FROM employees e where e.id = stp.updated_by) as created_by"),
                                DB::raw("case when stp.actions = 1 THEN 'Sent To Supplier' WHEN stp.actions=2 THEN 'Sent To Warehouse' when stp.actions = 3 THEN 'Cancelled' END as action_made"))
                        ->where("apo.mailed_status","=","1")
                        ->groupBy("stp.purchase_order_id")
                        ->orderBy("stp.id","DESC")
                        ->paginate($paginate);
                return view("purchase/update_po_status/index",["action_list"=>$Action_list]);
            }
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('purchase');
        }
    }
    public function view($id){
        try{
            $enc_id = \Crypt::decrypt($id);
            $purchase_order = DB::table("ac_purchase_order as po")
                    ->select("po.order_code","po.created_at")
                    ->where("po.id", "=", "$enc_id")
                    ->first();
            $order_history = DB::table("st_po_order_status as pos")
                    ->select("pos.po_status",
                            DB::raw("(select e.first_name FROM employees e where e.id = pos.updated_by) as name"),
                            DB::raw("date_format(pos.created_at,'%d-%m-%Y %h:%i:%s') as updated_date"))
                    ->where("pos.status","=","1")
                    ->where("pos.purchase_order_id","=","$enc_id")
                    ->orderBy("pos.created_at","DESC")
                    ->get();
            return view('purchase/update_po_status/view',['order_details'=>$purchase_order,"id"=>$id,"order_history"=>$order_history]);
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('purchase/update_po_status');
        }
    }
    
    public function update_status() {
        try {
            $id = \Crypt::decrypt(Input::get("id"));
            $status = Input::get("update_text");
            $login_id = Session::get("login_id");
            $insert = DB::table("st_po_order_status")
                    ->insert(["purchase_order_id" => $id, "po_status" => $status, "updated_by" => $login_id,"created_at"=>date('Y-m-d H:i:s')]);
            $order_history = DB::table("st_po_order_status as pos")
                    ->select("pos.po_status",
                            DB::raw("(select e.first_name FROM employees e where e.id = pos.updated_by) as name"),
                            DB::raw("date_format(pos.created_at,'%d-%m-%Y %h:%i:%s') as updated_date"))
                    ->where("pos.status","=","1")
                    ->where("pos.purchase_order_id","=","$id")
                    ->orderBy("pos.created_at","DESC")
                    ->get();
            return view("purchase/update_po_status/status_result",["order_history"=>$order_history]);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
}
