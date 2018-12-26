<?php

namespace App\Http\Controllers\Kpi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Pos_sale;
use App\Models\Masterresources;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;
use Customhelper;
use Illuminate\Encryption\EncryptionServiceProvider;

class IndexController extends Controller {
 protected $config = [];
 
 
    public function index() {
        $employee_details = Employee::where('id', Session::get('login_id'))->first();

        $loggedin_details = DB::table('employees')
                ->select('employees.*', 'master_resources.name as job_position')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', Session::get('login_id'))
                ->first();
        $user_sub_modules = Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->whereRaw("modules.parent_id in (select id from modules where name='KPI Analysis')")->get();
        return view('branchsales/index', array('employee_details' => $employee_details, 'loggedin_details' => $loggedin_details, 'user_sub_modules' => $user_sub_modules));
    }

    public function dashboard() {
        $search_key = Input::get('search_key');
        $selected_year = Input::get('selected_year');
        $selected_quarter = Input::get('selected_quarter');
        $search_branch= Input::get('search_branch');
        $notifications = DB::table('notifications')
                        ->select('notifications.*','analyst_discussion.id as discussion_parent_id','analyst_discussion.subject','employees.first_name')
                        ->leftjoin('analyst_discussion', 'analyst_discussion.id', '=', 'notifications.data->notifiable_id')
                        ->join('employees', 'employees.id', '=', 'notifications.data->from')
                        ->where('notifications.data->to', Session::get('login_id'))
                        ->where('notifications.data->category','Analyst Discussion')
                        ->orderBy('notifications.created_at', 'DESC')->get();
        return view('kpi/dashboard', array('search_key' => $search_key,'notifications' => $notifications, 'selected_year' => $selected_year, 'selected_quarter' => $selected_quarter,'search_branch'=>$search_branch));
    }
    
    public function filterbranch(Request $request){
        if($request->ajax()){
        
               
        $employee_id = Session::get('login_id');
        $employee_data = DB::table('employees')
                        ->select('employees.*')
                        ->where(['id' => $employee_id])->first();
        
        
            
            $search_key = Input::get('search_key');
            $search_branch = Input::get('search_branch'); 
            
         
            
        if ($search_key == '') {
            $search_key = "Month";
        }
        $current_year = date('Y');
        $current_quarter = ceil(date('n') / 3);
        $current_date = date('Y-m-d');
        /* $search_date = date('Y-m-d', strtotime($current_date) - (30 * 24 * 3600));
          $previous_start_date = date('Y-m-d', strtotime($current_date) - (60 * 24 * 3600));
          $previous_end_date = date('Y-m-d', strtotime($current_date) - (31 * 24 * 3600));

          if ($search_key == 'Week') {
          $search_date = date('Y-m-d', strtotime($current_date) - (7 * 24 * 3600));
          $previous_start_date = date('Y-m-d', strtotime($current_date) - (14 * 24 * 3600));
          $previous_end_date = date('Y-m-d', strtotime($current_date) - (8 * 24 * 3600));
          }
          if ($search_key == 'Quarter') {
          $search_date = date('Y-m-d', strtotime($current_date) - (90 * 24 * 3600));
          $previous_start_date = date('Y-m-d', strtotime($current_date) - (180 * 24 * 3600));
          $previous_end_date = date('Y-m-d', strtotime($current_date) - (91 * 24 * 3600));
          } */
        $search_date = date('Y-m-01', strtotime($current_date));
        $previous_start_date = date('Y-m-01', strtotime('-1 month', strtotime($search_date)));
        $previous_end_date = date('Y-m-31', strtotime('-1 month', strtotime($search_date)));
        if ($search_key == 'Week') {
            $search_date = date('Y-m-d', strtotime($current_date) - (7 * 24 * 3600));
            $previous_start_date = date('Y-m-d', strtotime($current_date) - (14 * 24 * 3600));
            $previous_end_date = date('Y-m-d', strtotime($current_date) - (8 * 24 * 3600));
        }
        if ($search_key == 'Quarter') {

            if ($current_quarter == 1) {
                $search_date = "$current_year-01-01";
            }
            if ($current_quarter == 2) {
                $search_date = "$current_year-04-01";
            }
            if ($current_quarter == 3) {
                $search_date = "$current_year-07-01";
            }
            if ($current_quarter == 4) {
                $search_date = "$current_year-10-01";
            }
            $previous_start_date = date('Y-m-d', strtotime($search_date) - (180 * 24 * 3600));
            $previous_end_date = date('Y-m-d', strtotime($search_date) - (91 * 24 * 3600));
        }
        if ($employee_data->privilege_status == 1) {
            $total_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->groupby(DB::raw("pos_sales.branch_id"))
                    ->orderby('total', 'DESC')
                    ->whereRaw("date(pos_sales.pos_date) > '$search_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->when($search_branch, function ($query) use ($search_branch) {
                        return $query->whereRaw("master_resources.name like '$search_branch%'");
                    })
                    ->get();

        } else {
            $total_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->groupby(DB::raw("pos_sales.branch_id"))
                    ->orderby('total', 'DESC')
                    ->whereRaw("date(pos_sales.pos_date) > '$search_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and  pos_sales.branch_id in (select branch_id from branches_to_analyst where analyst_id = $employee_id and status = 1)")
                   ->when($search_branch, function ($query) use ($search_branch) {
                        return $query->whereRaw("master_resources.name like '$search_branch%'");
                    })
                    ->get();
        }
        $branches_sales = array();
        foreach ($total_sales as $total_sale) {
            $branch_id = $total_sale->branch_id;
            $previous_total_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id'))
                    ->whereRaw("date(pos_sales.pos_date) > '$previous_start_date' and date(pos_sales.pos_date) < '$previous_end_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id and pos_sales.status=1")
                    ->first();
            if ($total_sale->total > $previous_total_sales->total) {
                $row['profit'] = "plus";
            } else {
                $row['profit'] = "down";
            }
            $row['branch_id'] = $total_sale->branch_id;
            $row['total'] = $total_sale->total;
            $row['branch_name'] = $total_sale->branch_name;
            $row['encode_id'] = \Crypt::encrypt($total_sale->branch_id);
            array_push($branches_sales, $row);
        }

   
            
      
      return \Response::json(array('config' =>  $this->config, 'branches_sales' => $branches_sales));
      
        
            
        
    }

    }
}
