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
use App\Services\Commonfunctions;
use App\Services\Paymentadvice;
use Customhelper;
use Exception;
use DB;
use PDF;
use App;
use Excel;
use Mail;

class PoactionlistController extends Controller {

    public function index(Request $request) {
        try {
            $paginate = Config::get("app.PAGINATE");
            if ($request->ajax()) {
                $paginate = Input::get("pagelimit");
                if(empty($paginate)){
                    $paginate = Config::get("app.PAGINATE");
                }
                $searchbyponumber = Input::get("searchbyponumber");
                $fromdata = (Input::get("fromdata") ? date("Y-m-d", strtotime(Input::get("fromdata"))) : '');
                $todate = (Input::get("todate") ? date("Y-m-d", strtotime(Input::get("todate"))) : '');
                $Action_list = DB::table("st_po_actions as stp")
                        ->selectraw("stp.*,"
                                . "(select apo.order_code FROM ac_purchase_order apo where apo.id = stp.purchase_order_id) as purchase_number,"
                                . "(select first_name FROM employees e where e.id = stp.updated_by) as created_by,"
                                . "case when stp.actions = 1 THEN 'Sent To Supplier' WHEN stp.actions=2 THEN 'Sent To Warehouse' when stp.actions = 3 THEN 'Cancelled' END as action_made")
                        ->when($searchbyponumber, function($qry) use ($searchbyponumber){
                            return $qry->whereraw("((select apo.order_code FROM ac_purchase_order apo where apo.id = stp.purchase_order_id) LIKE '%$searchbyponumber%')");
                        })
                        ->when($fromdata, function($qry) use ($fromdata){
                            return $qry->whereraw("(date_format(stp.created_at,'%Y-%m-%d') >= '$fromdata' )");
                        })
                        ->when($todate, function($qry) use ($todate){
                            return $qry->whereraw("(date_format(stp.created_at,'%Y-%m-%d') <= '$todate' )");
                        })
                        ->orderBy("stp.created_at","DESC")
                        ->paginate($paginate);
                return view("requisitions/po_action_list/results", array("action_list" => $Action_list));
            } else {
                $Action_list = DB::table("st_po_actions as stp")
                        ->selectraw("stp.*,"
                                . "(select apo.order_code FROM ac_purchase_order apo where apo.id = stp.purchase_order_id) as purchase_number,"
                                . "(select first_name FROM employees e where e.id = stp.updated_by) as created_by,"
                                . "case when stp.actions = 1 THEN 'Sent To Supplier' WHEN stp.actions=2 THEN 'Sent To Warehouse' when stp.actions = 3 THEN 'Cancelled' END as action_made")
                        ->orderBy("stp.created_at","DESC")
                        ->paginate($paginate);
                return view("requisitions/po_action_list/index", array("action_list" => $Action_list));
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }

}
