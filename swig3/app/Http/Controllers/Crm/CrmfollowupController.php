<?php

namespace App\Http\Controllers\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use App\Masterresources;
use App\Models\Crm_feedback;
use App\Models\Crm_feedback_followup;
use DB;

class CrmfollowupController extends Controller {

    public function index(Request $Request) {
        try {
            if ($Request->ajax()) {
                $paginate = Input::get("pagelimit");
                if (empty($paginate)) {
                    $paginate = Config::get("app.PAGINATE");
                }
                $searchbyname = Input::get('searchbyname');
                $mobile = Input::get('mobile');
                $status = Input::get('status');      
                $datefrom = Input::get('datefrom');
                $dateto = Input::get('dateto');

                if ($datefrom != '') {
                    $datefrom = explode('-', $datefrom);
                    $datefrom = $datefrom[2] . '-' . $datefrom[1] . '-' . $datefrom[0];
                }

                if ($dateto != '') {
                    $dateto = explode('-', $dateto);
                    $dateto = $dateto[2] . '-' . $dateto[1] . '-' . $dateto[0];
                }
                $data = DB::table("crm_feedbacks as a")
                        ->select(DB::raw("(select b.name FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                        . "a.customer_name,a.mobile_number,a.customer_comment,a.created_at,a.id,a.is_closed,"
                                        . "if(a.created_by > 0,(select c.first_name FROM employees as c where c.id=a.created_by),'-') as created_by"))
                        ->join('crm_feedback_follwup','crm_feedback_follwup.crm_feedback_id','=','a.id')
                        ->whereraw("a.status=1")
                        ->when($searchbyname, function($query) use ($searchbyname) {
                            return $query->whereraw("(a.customer_name like '%$searchbyname%')");
                        })
                        ->when($mobile, function($query) use ($mobile) {
                            return $query->whereraw("(a.mobile_number like '%$mobile%')");
                        })
                        ->when($datefrom, function ($query) use ($datefrom) {
                            return $query->whereRaw("date(a.created_at) >= '$datefrom' ");
                        })
                        ->when($dateto, function ($query) use ($dateto) {
                            return $query->whereRaw("date(a.created_at) <= '$dateto' ");
                        })
                        ->when($status, function ($query) use ($status) {
                            if($status=="-1"){$status=2;}
                            return $query->where('a.is_closed', '=', $status);
                        })
                        ->orderby("a.id", "DESC")
                        ->groupby('a.id')
                        ->paginate($paginate);
                return view("crm/crm_followups/result", array("all_feedback" => $data));
            } else {
                $paginate = Config::get("app.PAGINATE");
                $data = DB::table("crm_feedbacks as a")
                        ->select(DB::raw("(select b.name FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                        . "a.customer_name,a.mobile_number,a.customer_comment,a.created_at,a.id,a.is_closed,"
                                        . "if(a.created_by > 0,(select c.first_name FROM employees as c where c.id=a.created_by),'-') as created_by"))
                        ->join('crm_feedback_follwup','crm_feedback_follwup.crm_feedback_id','=','a.id')
                        ->whereraw("a.status=1")
                        ->orderby("a.id", "DESC")
                        ->groupby('a.id')
                        ->paginate($paginate);
                return view("crm/crm_followups/list", array("all_feedback" => $data));
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('crm');
        }
    }
}