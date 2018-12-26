<?php

namespace App\Http\Controllers\Taxation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Usermodule;
use App\Models\Country;
use App\Models\Document;
use DB;
use Mail;

class TaxationController extends Controller {

//    public function index(Request $request,$id='') {
//        $paginate = Config::get('app.PAGINATE');
//        
//        $strUrl=url()->current();
//        $job_id='';
//        $country_id='';
//        if($id){
//            if (strpos($strUrl, 'employeewithcountry') !== false) {
//                $country_id = \Crypt::decrypt($id);
//            }else{
//                $job_id = \Crypt::decrypt($id);
//            }
//        }
//        
//        
//        $employees = DB::table('employees')
//                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
//                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
//                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
//                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
//                ->where('employees.status', '!=', 2)
//                ->where('employees.admin_status', '=', 0)
//                ->when($job_id, function ($query) use ($job_id) {
//                    return $query->where('employees.job_position', '=', $job_id);
//                })
//                ->when($country_id, function ($query) use ($country_id) {
//                    return $query->where('employees.nationality', '=', $country_id);
//                })
//                ->orderby('employees.created_at', 'DESC')
//                ->paginate($paginate);
//        $job_positions = DB::table('master_resources')
//                        ->select('master_resources.*')
//                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
//                        ->orderby('name', 'ASC')->get();
//        $countries = DB::table('country')
//                        ->select('country.*')
//                        ->orderby('name', 'ASC')->get();
//        
//        $divisions = DB::table('master_resources')
//                    ->select('master_resources.*')
//                    ->where(['resource_type' => 'DIVISION', 'status' => 1])
//                    ->orderby('name', 'ASC')
//                    ->get();
//        
//        if ($request->ajax()) {
//            $paginate = Input::get('pagelimit');
//            if ($paginate == "") {
//                $paginate = Config::get('app.PAGINATE');
//            }
//            $search_key = Input::get('search_key');
//            $searchbyph = Input::get('searchbyph');
//            $searchbycode = Input::get('searchbycode');
//            $searchbyemail = Input::get('searchbyemail');
//            $job_position = Input::get('job_position');
//            $division = Input::get('division');
//            $country = Input::get('country');
//            $employees = DB::table('employees')
//                     ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
//                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
//                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
//                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
//                 ->where('employees.status', '!=', 2)
//                     ->where('employees.admin_status', '=', 0)
//                    ->when($search_key, function ($query) use ($search_key) {
//                        return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
//                    })
//                    ->when($job_position, function ($query) use ($job_position) {
//                        return $query->where('employees.job_position', '=', $job_position);
//                    })
//                    ->when($country, function ($query) use ($country) {
//                        return $query->where('employees.nationality', '=', $country);
//                    })
//                     ->when($searchbyemail, function ($query) use ($searchbyemail) {
//                        return $query->whereRaw("(employees.email like '$searchbyemail%')");
//                    })
//                     ->when($searchbyph, function ($query) use ($searchbyph) {
//                         if($searchbyph==';'){ $searchbyph=0;}
//                        return $query->whereRaw("(employees.mobile_number like '$searchbyph%')");
//                    })
//                     ->when($searchbycode, function ($query) use ($searchbycode) {
//                        return $query->whereRaw("(employees.username like '$searchbycode%')");
//                    })
//                     ->when($division, function ($query) use ($division) {
//                        return $query->where('employees.division', '=', $division);
//                    })
//                    ->orderby('employees.created_at', 'DESC')
//                    ->paginate($paginate);
//            return view('employee/searchresults', array('employees' => $employees));
//        }
//        return view('employee/index', array('employees' => $employees, 'job_positions' => $job_positions, 'countries' => $countries,"job_id"=>$job_id,"divisions"=>$divisions,'country_id'=>$country_id));
//    }

    public function add(Request $request,$id='') {
        
         $paginate = Config::get('app.PAGINATE');
         $taxfunctions = Config::get('app.TAX_FUNCTIONS');
        if (Session::get('company')) {
            $companies = Session::get('company');
        }
        $tax_id="";
        $taxlist = DB::table('master_resources')
                ->select('name', 'tax_percent', 'tax_applicable_from','status')
                ->where('status', '=', 1)
                ->where('resource_type', '=', 'TAX')
               ->paginate($paginate);
        
        $tax_type = DB::table('master_resources')
               ->select('master_resources.name as tax_type', 'master_resources.id as tax_id')->distinct()
               ->where('status', '=', 1)
               ->where('resource_type', '=', 'TAX')
               ->get();
        
        
         if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $tax_type = Input::get('tax_type');
            $tax_percent = Input::get('tax_percent');
            $tax_value = Input::get('tax_value');
            $created = Input::get('start_date');
            if ($created != '') {
                $created = explode('-', $created);
                $created = $created[2] . '-' . $created[1] . '-' . $created[0];
            }
            
            $taxlist = DB::table('master_resources')
                ->select('name', 'tax_percent', 'tax_applicable_from','status')
                ->where('status', '=', 1)
                ->where('resource_type', '=', 'TAX')
                ->when($tax_type, function ($query) use ($tax_type) {
                        return $query->where('master_resources.id', '=', $tax_type);
                    })
                    ->when($tax_value, function ($query) use ($tax_value, $tax_percent) {
                        return $query->whereRaw("master_resources.tax_percent $tax_percent $tax_value");
                    })
                    
                     ->when($created, function ($query) use ($created) {
                        return $query->whereRaw("date(master_resources.tax_applicable_from) >= '$created'");
                    })
                    ->orderby('master_resources.created_at', 'DESC')
                    ->paginate($paginate);
                    
                    
            return view('taxation/tax/searchresults', array('taxlist'=>$taxlist));
        }
        
          return view('taxation/tax/index', array('taxlist'=>$taxlist,'tax_type'=>$tax_type,'tax_id'=>$tax_id,'taxfunctions'=>$taxfunctions));
   
    }

    public function store() {

      
            $masterresourcemodel = new Masterresources();
            $masterresourcemodel->resource_type = 'TAX';
            $masterresourcemodel->name = Input::get('tax_name');
            $name= Input::get('tax_name');
            $masterresourcemodel->tax_percent = Input::get('tax_percent');
            $taxDate = Input::get('tax_date');
            $tax_type = Input::get('tax_type');
             $masterresourcemodel->tax_function=$tax_type;
            $taxDate = explode('-',$taxDate);
         $taxDate = $taxDate[2].'-'.$taxDate[1].'-'.$taxDate[0];
         
            
            $masterresourcemodel->tax_applicable_from = $taxDate;
            
            $masterresourcemodel->status = 1;
            
//             $tax_dup_data = DB::table('master_resources')
//                            ->select('name')
//                            ->where('name', '=', $name)
//                            ->where('status', '=', 1)
//                            ->get();  
             
             $tax_dup_data = array();//dummy for count
         
        if(count($tax_dup_data)>0){
          Toastr::error('Already Exist!', $title = null, $options = []);
            return Redirect::to('taxation/tax');
        } else{
           $saved =  $masterresourcemodel->save(); 
        if($saved){
            Toastr::success('Successfully Added!', $title = null, $options = []);
            return Redirect::to('taxation/tax');
    }   
        
    }
    }

   

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('employees')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Employee Successfully Disabled', $title = null, $options = []);
            return Redirect::to('employee');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('employee');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('employees')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Employee Successfully Enabled', $title = null, $options = []);
            return Redirect::to('employee');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('employee');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('employees')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Employee Successfully Deleted', $title = null, $options = []);
            return Redirect::to('employee');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('employee');
        }
    }

   
    public function forbidden() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
         return view('dashboard/forbidden');
    }

    public function logout() {
        Auth::logout();
        return Redirect::to('/');
    }

    public function searchemployee() {
        
    }

    public function fetch_notification_count() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $notify_counts = DB::table('notifications')
                        ->where('data->to', $login_id)->where('read_at', NULL)->count();
        return $notify_counts;
    }

    public function fetch_notification() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $notification = DB::table('notifications')
                        ->select('notifications.*')
                        ->where('data->to', $login_id)->orderBy('created_at', 'DESC')->get();
        $noti = array();
        //print_r($notification);
        foreach ($notification as $notifications) {
            $details = json_decode($notifications->data);
            $url='';
            if ($notifications->read_at != NULL) {
                $old = 'old';
            } else {
                $old = "";
            }
            if($details->category=='Leave Requisition')
            {
                $url='leave_requisition';
            }
            if($details->category=='General Requisition')
            {
                $url='general_requisition';
            }
            if($details->category=='Maintenance Requisition')
            {
                $url='maintenance_requisition';
            }
            
            
            $vid = $notifications->id;
            
          $public_path= url('/');
          
          if ($details->category == 'Inventory Request') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/' . $details->type . '/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else if ($details->category == 'Analyst Discussion') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/' . $details->type . '/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else if ($details->category == 'To do Notification') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/' . $details->type . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else if ($details->category == 'Assigend Task') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/dashboard/assign_task/edit/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/requisition/' . $url . '/' . $details->type . '/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            
        }
    }

    public function mark_notification() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $id = Input::get('id');
        $notification = DB::table('notifications')
                ->where('id', $id)
                ->update(['read_at' => date("Y-m-d")]);
    }

    
    
    
   

}