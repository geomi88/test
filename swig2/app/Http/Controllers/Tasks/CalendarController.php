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
use App\Models\Module;
use App\Models\Usermodule;
use App;
use DB;
use Mail;
use Calendar;


class CalendarController extends Controller

{

    public function index()

    {

        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $user_sub_modules = Usermodule::where('employee_id', $login_id)->join('modules', 'modules.id', '=', 'user_modules.module_id')
                            ->whereRaw("modules.parent_id in (select id from modules where name='Calendar')")->orderby('modules.id','ASC')->get();
        $admin_status = DB::table('employees')
                    ->select('employees.admin_status as admin_status')
                    ->whereRaw("id=$login_id")
                    ->value("admin_status");
         
        $curDate=date('Y-m-d');
        
        $tasks1 = DB::table('tasks')
            ->select('tasks.id as id','tasks.title as title','tasks.start_date as start_date','tasks.end_date as end_date','tasks.is_all_day_task as is_all_day_task','tasks.task_type as task_type')
            ->join('meeting_attendees', 'tasks.id', '=', 'meeting_attendees.meeting_id')
            ->whereRaw("meeting_attendees.user_id=$login_id AND (date(end_date)>='$curDate' AND task_type IN (3)) AND status!=0");
        
        $tasks2 = DB::table('tasks')
            ->select('tasks.id as id','tasks.title as title','tasks.start_date as start_date','tasks.end_date as end_date','tasks.is_all_day_task as is_all_day_task','tasks.task_type as task_type')
            ->join('training_attendees', 'tasks.id', '=', 'training_attendees.training_id')
            ->whereRaw("training_attendees.user_id=$login_id AND (date(end_date)>='$curDate' AND task_type IN (5)) AND status!=0");
       
        $tasks = DB::table('tasks')
            ->select('tasks.id as id','tasks.title as title','tasks.start_date as start_date','tasks.end_date as end_date','tasks.is_all_day_task as is_all_day_task','tasks.task_type as task_type')
            ->whereRaw("owner_id=$login_id AND ((date(end_date)>'$curDate' AND task_type IN (1,3,4)) OR (task_type=2 AND status!=3)) AND status!=0")
            ->union($tasks1)
            ->union($tasks2)
            ->get();
    
        $events=array();
        foreach ($tasks as $task) {
            if($task->is_all_day_task){
                $is_all_day_task=true;
                $start=substr($task->start_date, 0, 10);
                $date = strtotime("+1 day", strtotime("$task->end_date"));
                $end=date("Y-m-d", $date);
            }else{
                $is_all_day_task=false;
                $start=$task->start_date;
                $end=$task->end_date;
            }
            
            $clsname='';
            $taskId=\Crypt::encrypt($task->id);
            if($task->task_type==2){
                $clsname="classassigned";
                $url="dashboard/assign_task/edit/".$taskId;
            }else if($task->task_type==1){
                $clsname="classown";
                $begindate=substr($task->start_date, 0, 10);
                $tomorrow = date("Y-m-d", strtotime("+1 day"));
                if(strtotime($begindate)<=strtotime($curDate)){
                    $start=$tomorrow;
                    $url="dashboard/todo";
                }else{
                    $url="dashboard/plan/edit/".$taskId;
                }
            }else if($task->task_type==3){
                $clsname="classmeeting";
                $url="meeting/view/".$taskId;
            }else if($task->task_type==4){
                $clsname="classagenda";
                 $url="meeting/agenda/edit/".$taskId;
            }else if($task->task_type==5){
                $clsname="classtraining";
                 $url="training/view/".$taskId;
            }
            
            
            
            $events[]=array(
                        'title'=>$task->title,
                        'id'=>$task->id,
                        'start'=>$start,
                        'end'=>$end,
                        'allDay'=>$is_all_day_task,
                        'url'=>$url,
                        'className'=>$clsname
                        );
        }
        $events= array_values($events);
        $tasks = json_encode($events);
        
        return view('dashboard/index',array('tasks'=>$tasks,'user_sub_modules'=>$user_sub_modules,'admin_status'=>$admin_status,'plans'=>array()));

    }
    
    public function getevents()
    {

        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $task_type = Input::get('task_type');
         
        $curDate=date('Y-m-d');
        
         $tasks1 = DB::table('tasks')
            ->select('tasks.id as id','tasks.title as title','tasks.start_date as start_date','tasks.end_date as end_date','tasks.is_all_day_task as is_all_day_task','tasks.task_type as task_type')
            ->join('meeting_attendees', 'tasks.id', '=', 'meeting_attendees.meeting_id')
            ->whereRaw("meeting_attendees.user_id=$login_id AND (date(end_date)>='$curDate' AND task_type IN (3)) AND status!=0")
            ->when($task_type, function ($query) use ($task_type) {
                    return $query->where('tasks.task_type', '=', $task_type);
            });
            
        $tasks2 = DB::table('tasks')
            ->select('tasks.id as id','tasks.title as title','tasks.start_date as start_date','tasks.end_date as end_date','tasks.is_all_day_task as is_all_day_task','tasks.task_type as task_type')
            ->join('training_attendees', 'tasks.id', '=', 'training_attendees.training_id')
            ->whereRaw("training_attendees.user_id=$login_id AND (date(end_date)>='$curDate' AND task_type IN (5)) AND status!=0")
            ->when($task_type, function ($query) use ($task_type) {
                    return $query->where('tasks.task_type', '=', $task_type);
            });
            
        $tasks = DB::table('tasks')
            ->select('tasks.id as id','tasks.title as title','tasks.start_date as start_date','tasks.end_date as end_date','tasks.is_all_day_task as is_all_day_task','tasks.task_type as task_type')
            ->whereRaw("owner_id=$login_id AND ((date(end_date)>'$curDate' AND task_type IN (1,3,4)) OR (task_type=2 AND status!=3)) AND status!=0")
            ->union($tasks1)
            ->union($tasks2)
            ->when($task_type, function ($query) use ($task_type) {
                    return $query->where('tasks.task_type', '=', $task_type);
            })
            ->get();
        
        $events=array();
        foreach ($tasks as $task) {
            if($task->is_all_day_task){
                $is_all_day_task=true;
                $start=substr($task->start_date, 0, 10);
                $date = strtotime("+1 day", strtotime("$task->end_date"));
                $end=date("Y-m-d", $date);
            }else{
                $is_all_day_task=false;
                $start=$task->start_date;
                $end=$task->end_date;
            }
            
            $clsname='';
            $taskId=\Crypt::encrypt($task->id);
            if($task->task_type==2){
                $clsname="classassigned";
                $url="dashboard/assign_task/edit/".$taskId;
            }else if($task->task_type==1){
                $clsname="classown";
                $begindate=substr($task->start_date, 0, 10);
                $tomorrow = date("Y-m-d", strtotime("+1 day"));
                if(strtotime($begindate)<=strtotime($curDate)){
                    $start=$tomorrow;
                    $url="dashboard/todo";
                }else{
                    $url="dashboard/plan/edit/".$taskId;
                }
            }else if($task->task_type==3){
                $clsname="classmeeting";
                $url="meeting/view/".$taskId;
            }else if($task->task_type==4){
                $clsname="classagenda";
                $url="meeting/agenda/edit/".$taskId;
            }else if($task->task_type==5){
                $clsname="classtraining";
                 $url="training/view/".$taskId;
            }
            
            $events[]=array(
                        'title'=>$task->title,
                        'id'=>$task->id,
                        'start'=>$start,
                        'end'=>$end,
                        'allDay'=>$is_all_day_task,
                        'url'=>$url,
                        'className'=>$clsname
                        );
        }
        $tasks= array_values($events);
        
        return \Response::json(array('tasks'=>$tasks));

    }
    
    public function createplan($date)
    {  
        return view('dashboard/plan/createplan', array("plandate"=>$date));
    }
    
}