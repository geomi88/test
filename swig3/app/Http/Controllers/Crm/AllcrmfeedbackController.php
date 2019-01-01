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

class AllcrmfeedbackController extends Controller {

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
                        ->paginate($paginate);
                return view("crm/all_crm_feedback/result", array("all_feedback" => $data));
            } else {
                $paginate = Config::get("app.PAGINATE");
                $data = DB::table("crm_feedbacks as a")
                        ->select(DB::raw("(select b.name FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                        . "a.customer_name,a.mobile_number,a.customer_comment,a.created_at,a.id,a.is_closed,"
                                        . "if(a.created_by > 0,(select c.first_name FROM employees as c where c.id=a.created_by),'-') as created_by"))
                        ->whereraw("a.status=1")
                        ->orderby("a.id", "DESC")
                        ->paginate($paginate);
                return view("crm/all_crm_feedback/view", array("all_feedback" => $data));
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('crm/all_crm_feedback');
        }
    }

    public function show($id,$page) {
        try {
            $feedback_id = \Crypt::decrypt($id);
            $result = DB::table("crm_feedbacks as a")
                    ->select('emp.first_name as closed_name','emp.username as closed_code','a.updated_at as closed_date',DB::raw("a.id,a.customer_name,a.mobile_number,a.customer_comment,a.is_closed,"
                                    . "(select b.name FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                    . "if(a.created_by > 0,(select c.first_name FROM employees as c where c.id=a.created_by),'-') as created_by,"
                                    . "a.created_at"))
                    ->leftjoin('employees as emp','emp.id','=','a.closed_by')
                    ->whereraw("a.id = $feedback_id")
                    ->get();
            $followups = DB::table('crm_feedback_follwup as followup')
                    ->select('followup.followup','followup.created_at','employees.username','employees.first_name')
                    ->leftjoin('employees','employees.id','=','followup.created_by')
                    ->where('followup.crm_feedback_id','=',$feedback_id)
                    ->orderby('followup.created_at','ASC')
                    ->get();     

            return view("crm/all_crm_feedback/feedback_view", array("feedback_data" => $result,"followups" => $followups, "page" => $page));
        } catch (\Exception $e) { 
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('crm/all_crm_feedback');
        }
    }
    
    public function followup_store(){
        try {
            $data = Input::all();   
            
            $obj_feedback = new Crm_feedback;
            $obj_feedback->exists = true;
            $obj_feedback->id = $data['feedback_id'];
            $obj_feedback->is_closed = 3;
            $obj_feedback->save();
            
            $obj_followup = new Crm_feedback_followup;
            $obj_followup->crm_feedback_id = $data['feedback_id'];
            $obj_followup->followup = $data['follow_up'];
            $obj_followup->created_by = Session::get('login_id');
            $save = $obj_followup->save();
            $url = 'all_crm_feedback';
            $feed_id = \Crypt::encrypt($data['feedback_id']);
            if($save){
                Toastr::success('Feedback Followup Saved Successfully', $title = null, $options = []);
                return Redirect::to('crm/all_crm_feedback/feedback_view/'.$feed_id.'/'.$url);
            }else{
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('crm/all_crm_feedback/feedback_view/'.$feed_id.'/'.$url);
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('crm/all_crm_feedback');
        }
    }
    
    public function close_feedback_followup($id){
        try{
            $feedback_id = \Crypt::decrypt($id);
            $obj_feedback = new Crm_feedback;
            $obj_feedback->exists = TRUE;
            $obj_feedback->id = $feedback_id;
            $obj_feedback->is_closed = 2;
            $obj_feedback->closed_by = Session::get('login_id');
            $save = $obj_feedback->save();
            $url = 'all_crm_feedback';
            if($save){
                Toastr::success('Feedback Followup Closed Successfully', $title = null, $options = []);
                return Redirect::to('crm/all_crm_feedback/feedback_view/'.$id.'/'.$url);
            }else{
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('crm/all_crm_feedback/feedback_view/'.$id.'/'.$url);
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('crm/all_crm_feedback');
        }
    }

}
