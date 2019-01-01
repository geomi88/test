<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use Illuminate\Support\Facades\Config;
use App\Models\Company;
use App\Models\Tasks;
use App\Models\Task_history;
use App\Models\Module;
use App\Models\Usermodule;
use App;
use DB;


class TodoController extends Controller

{

    public function todo() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $curDate=date('Y-m-d');
                
        $new_tasks = DB::table('tasks')
                ->select('tasks.*',db::raw("(case when task_type=0 then 'clstodocolor' when task_type=1 then 'clsplancolor' when task_type=2 then 'clsassignedtask' when task_type=4 then 'clsagendacolor' end) as strColorClass"))
                ->whereRaw("status=1 AND owner_id=$login_id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5)")
                ->orderby('priority','ASC')
                ->orderby('created_at','DESC')
                ->get();
        
        $pending_tasks = DB::table('tasks')
                ->select('tasks.*',db::raw("(case when task_type=0 then 'clstodocolor' when task_type=1 then 'clsplancolor' when task_type=2 then 'clsassignedtask' when task_type=4 then 'clsagendacolor' end) as strColorClass"))
                ->whereRaw("status=2 AND owner_id=$login_id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5)")
                ->orderby('priority','ASC')
                ->orderby('created_at','DESC')
                ->get();
        
        $days_ago = date('Y-m-d', strtotime('-15 days', strtotime("$curDate")));
        
        $completed_tasks = DB::table('tasks')
                ->select('tasks.*',db::raw("(case when task_type=0 then 'clstodocolor' when task_type=1 then 'clsplancolor' when task_type=2 then 'clsassignedtask' when task_type=4 then 'clsagendacolor' end) as strColorClass"))
                ->whereRaw("status=3 AND owner_id=$login_id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5) AND date(start_date)>='$days_ago'")
                ->orderby('updated_at','DESC')
                ->get();
        
        return view('dashboard/todo',array("new_tasks"=>$new_tasks,"pending_tasks"=>$pending_tasks,"completed_tasks"=>$completed_tasks));
    }
    
    public function deletefromusermdulestemp1() {
        
        
        $emps = DB::table('employees')
                ->select('id')
                ->whereRaw("status!=2")
                ->get();
      
         $delete = DB::table('user_modules')
                ->whereRaw("module_id=189")
                ->delete();
        
        foreach ($emps as $emp) {
            $historyModal=new Usermodule();
               
            $historyModal->module_id=189;
            $historyModal->employee_id=$emp->id;
            $historyModal->save();
        }
        
        die("Success1");
        
    }
    
    public function savetodo() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $model = new Tasks();
        $title=Input::get('title');

        $start_date  = date("Y-m-d");
        $end_date  = date("Y-m-d");
        
        $model->title = $title;
        $model->description = Input::get('description');
        $model->start_date = $start_date;
        $model->end_date = $end_date;
        $model->owner_id  = $login_id;
        $model->is_all_day_task = 0;
        $model->priority = 0;
        $model->status = 1;
        $model->task_type = 0;
        $model->save();
        
        $historyModal=new Task_history();
               
        $historyModal->task_id=$model->id;
        $historyModal->status=1;
        $historyModal->save();
            
        return Redirect::to('dashboard/todo');
    }
        
    public function change_status() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $taskid = Input::get('taskid');
            $status = Input::get('status');
           
            $tasks = DB::table('tasks')
                    ->where(['id' => $taskid])
                    ->update(['status' => $status]);
            
            $historyModal=new Task_history();
               
            $historyModal->task_id=$taskid;
            $historyModal->status=$status;
            $historyModal->save();
            
            return 1;
            //        return Redirect::to('dashboard/todo');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/todo');
        }
    }
    
    public function complete_task() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $taskid = Input::get('taskid');
           
            $tasks = DB::table('tasks')
                    ->where(['id' => $taskid])
                    ->update(['status' => 3]);
            
            $historyModal=new Task_history();
               
            $historyModal->task_id=$taskid;
            $historyModal->status=3;
            $historyModal->save();
            
            Toastr::success('Task Completed Successfully', $title = null, $options = []);
            return 1;
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/todo');
        }
    }
    
    public function get_task_history() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $taskid = Input::get('taskid');

        $history = DB::table('task_history')
                ->where(['task_id' => $taskid])
                ->orderby('created_at','DESC')
                ->orderby('id','DESC')
                ->get();
                   
        $tasks = DB::table('tasks')
                ->select('tasks.*')
                ->where(['id' => $taskid])
                ->first();
        
        $strHtml='';
        if(count($tasks)>0){
            $strStart=  substr($tasks->start_date, -8);
            $strEnd=  substr($tasks->end_date, -8);
            
            if(($strStart!="00:00:00" || $strEnd!="00:00:00") || ($strStart=="00:00:00" && $strEnd=="00:00:00" && $tasks->is_all_day_task==true)){
                $datStart=date('d-m-Y H:i:s',strtotime($tasks->start_date));
                $datEnd=date('d-m-Y H:i:s',strtotime($tasks->end_date));
                $strHtml="<p>Plan Start Date : $datStart</p>";
                $strHtml.="<p>Plan End Date &nbsp&nbsp: $datEnd</p>";
            }
        }
        
        foreach ($history as $data) {
            $date=date('d-m-Y H:i:s',strtotime($data->created_at));

            if ($data->status == 3) {
                $status="Completed";
            } else if ($data->status == 2) {
                $status="Pending";
            } else if ($data->status == 1) {
                $status="New";
            } else if ($data->status == 0) {
                $status="Deleted";
            }

            $strHtml.="<p>$date :: $status</p>";
        }

        echo $strHtml;
        
    }

    public function change_priority() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
       
        $arrNewTaskPy=Input::get('arrNewTaskPys');
        $arrPendingTaskPy=Input::get('arrPendingTaskPys');
        
        $arrNewTaskPys=json_decode($arrNewTaskPy);
        $arrPendingTaskPys=json_decode($arrPendingTaskPy);
        
        foreach ($arrNewTaskPys as $arrNewTaskPy) {
             $tasks = DB::table('tasks')
                ->where(['id' => $arrNewTaskPy->taskid])
                ->update(['priority' => $arrNewTaskPy->taskindex]);
        }
        
        foreach ($arrPendingTaskPys as $arrPendingTaskPy) {
             $tasks = DB::table('tasks')
                ->where(['id' => $arrPendingTaskPy->taskid])
                ->update(['priority' => $arrPendingTaskPy->taskindex]);
        }

        return 1;
//        return Redirect::to('dashboard/todo');
    }
    
    
    
    public function deletetodo() {
        try {
            $taskid = Input::get('taskid');
            $tasks = DB::table('tasks')
                    ->where(['id' => $taskid])
                    ->update(['status' => 0]);
            
            $historyModal=new Task_history();
               
            $historyModal->task_id=$taskid;
            $historyModal->status=0;
            $historyModal->save();
            
            return 1;
        } catch (\Exception $e) {
            
            return -1;
        }
    }
    
}