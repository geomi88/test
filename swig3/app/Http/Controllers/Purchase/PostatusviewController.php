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

class PostatusviewController extends Controller {

    public function index(Request $request) {
        try {
            $paginate = Config::get("app.PAGINATE");
            if ($request->ajax()) {
                $searchbyponumber = Input::get("searchbyponumber");
                $searchbyreq_code = Input::get("searchbyreq_code");
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                $created_at_from = (Input::get("created_at_from") ? date("Y-m-d",strtotime(Input::get("created_at_from"))) : '');
                $created_at_to = (Input::get("created_at_to") ? date("Y-m-d",strtotime(Input::get("created_at_to"))) : '');
                $purchase_order = DB::table("ac_purchase_order as po")
                        ->leftjoin("requisition as req","req.id","=","po.requisition_id")
                        ->select("po.order_code as order_no","po.created_at","po.id","req.requisition_code as requisition_number",
                                DB::raw("CASE when po.order_type = 1 THEN 'Other' WHEN po.order_type = 2 THEN 'Local' WHEN po.order_type = 3 THEN 'Import' END as po_type"))
                        ->when($searchbyponumber, function($qry) use($searchbyponumber){
                            return $qry->where("po.order_code", "like", "%$searchbyponumber%");
                        })
                        ->when($searchbyreq_code, function($qry) use($searchbyreq_code){
                            return $qry->where("req.requisition_code", "like", "%$searchbyreq_code%");
                        })
                        ->when($created_at_from, function($qry) use ($created_at_from){
                            return $qry->whereraw("(date_format(po.created_at,'%Y-%m-%d') >= '$created_at_from')");
                        })
                        ->when($created_at_to, function($qry) use ($created_at_to){
                            return $qry->whereraw("(date_format(po.created_at,'%Y-%m-%d') <= '$created_at_to')");
                        })
                        ->where("po.mailed_status","=","1")
                        ->orderBy("po.created_at", "DESC")
                        ->paginate($paginate);
                return view("purchase/view_postatus/results",["po_details" => $purchase_order]);
            } else {
                $purchase_order = DB::table("ac_purchase_order as po")
                        ->leftjoin("requisition as req","req.id","=","po.requisition_id")
                        ->select("po.order_code as order_no","po.created_at","po.id","req.requisition_code as requisition_number",
                                DB::raw("CASE when po.order_type = 1 THEN 'Other' WHEN po.order_type = 2 THEN 'Local' WHEN po.order_type = 3 THEN 'Import' END as po_type"))
                        ->where("po.mailed_status","=","1")
                        ->orderBy("po.created_at", "DESC")
                        ->paginate($paginate);
                return view("purchase/view_postatus/index", ["po_details" => $purchase_order]);
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('purchase');
        }
    }

    public function view($id) {
        try{
            $enc_id = \Crypt::decrypt($id);
            $purchase_details = DB::table("ac_purchase_order as po")
                    ->select("po.id","po.order_code","po.payment_advice_id","po.requisition_id",
                            DB::raw("date_format(po.created_at,'%d-%m-%Y') as created_at"),
                            DB::raw("if(to_ledger_id IS NOT NULL ,(select first_name FROM ac_party ap where ap.id = po.to_ledger_id), '') as supplier_name"),
                            DB::raw("(select if((ri.rfq_id IS NOT NULL AND ri.rfq_id > 0),'has_rfq','') FROM requisition_items ri where ri.requisition_id = po.requisition_id LIMIT 1) as rfq_exists"))
                    ->where("po.id","=","$enc_id")
                    ->first();
            $payment_id = $purchase_details->payment_advice_id;
            $requisition_id = $purchase_details->requisition_id;
            $previous_status = DB::table("st_po_order_status as spo")
                    ->select("spo.id","spo.po_status",
                            DB::raw("IF(spo.updated_by IS NOT NULL,(select e.first_name FROM employees e where e.id = spo.updated_by),'') as done_by"),
                            DB::raw("date_format(spo.created_at,'%d-%m-%Y %h:%i:%s') as updated_on"))
                    ->orderBy("spo.created_at","DESC")
                    ->where("spo.purchase_order_id","=","$enc_id")
                    ->where("spo.status","=","1")
                    ->get();
            $Requisition_list = DB::table("requisition as r")
                    ->select("r.requisition_code","r.id",
                            DB::raw("(select SUM(pad.pay_amount) FROM ac_payment_advice_details pad where pad.requisition_id = r.id AND (select apa.status FROM ac_payment_advice apa where apa.id =  pad.payment_advice_id) = 2) as total_price"))
                    ->where("r.id","=","$requisition_id")
                    ->get();
            $rfq_list = DB::table("requisition_items as ri")
                    ->leftjoin("rfq as r","r.id","=","ri.rfq_id")
                    ->select("r.rfq_code","r.id")
                    ->where("ri.requisition_id","=","$requisition_id")
                    ->get();
            return view("purchase/view_postatus/view",
                    [   
                        'po_details'=>$purchase_details,
                        'prev_details'=>$previous_status,
                        'req_details'=>$Requisition_list,
                        'rfq_details'=>$rfq_list
                    ]);
        }
        catch(\Exception $e){
            Toastr::error("Something went wrong",$title= null, $options = []);
            return Redirect::to("purchase/view_postatus");
        }
    }
    
    public function pay_details(){
        $id = \Crypt::decrypt(Input::get("data"));
        dd($id);
    }

}
