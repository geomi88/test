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
use App\Notifications\AssignTaskNotification;
use Illuminate\Support\Facades\Hash;
use App\Models\Task_history;
use App\Models\Tasks;
use Illuminate\Support\Facades\Config;
use App\Models\Usermodule;
use App;
use DB;
use PDF;
use Excel;

class AssigntaskController extends Controller

{
    public function index(Request $request,$id='') {
        try{
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $empid='';
            $empdata=array();
            $arrId=array();
            if($id){
                $empid = \Crypt::decrypt($id);
                $arrId = explode(",", $empid);

                $empdata = DB::table('employees')
                    ->select('employees.id as emp_id', 'employees.first_name as emp_name','employees.username as code',db::raw("replace(job_position.name,'_',' ') as designation"))
                    ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                    ->whereIn("employees.id",$arrId)
                    ->get();

            }
            
            $arrJobPosSettings=array();
            $usermodulemodel=new Usermodule();
            $arrJobPosSettings=$usermodulemodel->getjobpositionsettings($login_id,'Assign Task'); 
            
            $curDate=date('Y-m-d');
            $employees = DB::table('employees')
                    ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name',
                            db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id AND task_type NOT IN(3,5)) as new_count"),
                            db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id AND task_type NOT IN(3,5)) as pending_count"))
                    ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                    ->when($arrJobPosSettings, function ($query) use ($arrJobPosSettings) {
                        return $query->whereIn("employees.job_position",$arrJobPosSettings);
                     })
                    ->where('employees.status', '!=', 2)
                    ->orderby('employees.first_name', 'ASC')
                    ->paginate($paginate);

            $job_positions = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                            ->when($arrJobPosSettings, function ($query) use ($arrJobPosSettings) {
                                return $query->whereIn("master_resources.id",$arrJobPosSettings);
                             })
                            ->orderby('name', 'ASC')
                            ->get();

            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                $search_key = Input::get('search_key');
                $job_position = Input::get('job_position');
                $searchbycode = Input::get('searchbycode');
                $sortordname = Input::get('sortordname');
                $sortordjob = Input::get('sortordjob');
                $sortordcode = Input::get('sortordcode');

                $strids = Input::get('strids');
                $strids=  rtrim($strids, ",");
                $arrId = explode(",", $strids);

                $sortOrdDefault='';
                if($sortordname=='' && $sortordjob=='' && $sortordcode==''){
                    $sortOrdDefault='ASC';
                }

                $employees = DB::table('employees')
                       ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name',
                               db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id AND task_type NOT IN(3,5)) as new_count"),
                               db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id AND task_type NOT IN(3,5)) as pending_count"))
                       ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                       ->where('employees.status', '!=', 2)
                        ->when($arrJobPosSettings, function ($query) use ($arrJobPosSettings) {
                            return $query->whereIn("employees.job_position",$arrJobPosSettings);
                         })
                       ->when($search_key, function ($query) use ($search_key) {
                           return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
                       })
                       ->when($job_position, function ($query) use ($job_position) {
                           return $query->where('employees.job_position', '=', $job_position);
                       })
                       ->when($searchbycode, function ($query) use ($searchbycode) {
                           return $query->whereRaw("(employees.username like '$searchbycode%')");
                       })
                       ->when($sortordname, function ($query) use ($sortordname) {
                           return $query->orderby('employees.first_name', $sortordname);
                       })
                       ->when($sortordjob, function ($query) use ($sortordjob) {
                           return $query->orderby('job_position.name', $sortordjob);
                       })
                       ->when($sortordcode, function ($query) use ($sortordcode) {
                           return $query->orderby('employees.username', $sortordcode);
                       })
                       ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                           return $query->orderby('employees.first_name', $sortOrdDefault);
                       })
                       ->paginate($paginate);

                return view('dashboard/assign_task/emp_result', array('employees' => $employees,'arrid'=>$arrId));
            }
            return view('dashboard/assign_task/employe_list', array('employees' => $employees, 'job_positions' => $job_positions,'empdata'=>  json_encode($empdata),'arrid'=>$arrId));
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/assign_task');
        }
    }
    
    public function assigntasksingleemployee() {
        try{
             
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $start_date  = Input::get('start_date');
            $end_date  = Input::get('end_date');
            $start_time  = Input::get('start_time');
            $end_time  = Input::get('end_time');
            $isalldaytask  = Input::get('alldaytask');
           
            $start_date = explode('-',$start_date);
            $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];

            $end_date = explode('-',$end_date);
            $end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];
                
            if($isalldaytask){
                $start_date=$start_date.' 00:00:00';
                $end_date=$end_date.' 00:00:00';
                $isalldaytask=1;
            }else{
                $start_date=$start_date.' '.$start_time.':00';
                $end_date=$end_date.' '.$end_time.':00';
                $isalldaytask=0;
            }
            
            $title=Input::get('title');
            $emp_id=Input::get('emp_id');
            
            $model = new Tasks();
            $model->title = $title;
            $model->description = Input::get('description');
            $model->start_date = $start_date;
            $model->end_date = $end_date;
            $model->owner_id  = $emp_id;
            $model->assigned_by = $login_id;
            $model->is_all_day_task = $isalldaytask;
            $model->priority = 0;
            $model->status = 1;
            $model->task_type = 2;
            $model->save();
            
            $latest_id=$model->id;
            $historyModal=new Task_history();

            $historyModal->task_id=$model->id;
            $historyModal->status=1;
            $historyModal->save();
            
            //Notification starts
            $type = '';
            $from = '';
            $to = (int)$emp_id;
            $category = 'Assigend Task';
            $message = 'Task ' . $title . ' .has been assigned to you';
            // Notification to next level
            Auth::user()->notify(new AssignTaskNotification($from, $to, $message, $category, $latest_id, $type));
            //Notification ends
            
            Toastr::success('Task Assigned Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard/assign_task');
        
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/assign_task');
        }
    }
    
    public function assigntaskmultiemployee() {
        try{
             
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $start_date  = Input::get('start_date');
            $end_date  = Input::get('end_date');
            $start_time  = Input::get('start_time');
            $end_time  = Input::get('end_time');
            $isalldaytask  = Input::get('alldaytask');
            
            $strEmpIds=Input::get('emp_ids');
            $strEmpIds=rtrim($strEmpIds, ",");
            
            $arrEmpIds=explode(",", $strEmpIds);
            
            $start_date = explode('-',$start_date);
            $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0];

            $end_date = explode('-',$end_date);
            $end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0];
                
            if($isalldaytask){
                $start_date=$start_date.' 00:00:00';
                $end_date=$end_date.' 00:00:00';
                $isalldaytask=1;
            }else{
                $start_date=$start_date.' '.$start_time.':00';
                $end_date=$end_date.' '.$end_time.':00';
                $isalldaytask=0;
            }
          
            foreach ($arrEmpIds as $empid) {
                $model = new Tasks();
                $title=Input::get('title');
                $model->title = Input::get('title');
                $model->description = Input::get('description');
                $model->start_date = $start_date;
                $model->end_date = $end_date;
                $model->owner_id  = $empid;
                $model->assigned_by = $login_id;
                $model->is_all_day_task = $isalldaytask;
                $model->priority = 0;
                $model->status = 1;
                $model->task_type = 2;
                $model->save();
                
                $latest_id=$model->id;

                $historyModal=new Task_history();

                $historyModal->task_id=$model->id;
                $historyModal->status=1;
                $historyModal->save();
                
                //Notification starts
                $type = '';
                $from = '';
                $to = (int)$empid;
                $category = 'Assigend Task';
                $message = 'Task ' . $title . ' .has been assigned to you';
                // Notification to next level
                Auth::user()->notify(new AssignTaskNotification($from, $to, $message, $category, $latest_id, $type));
                //Notification ends
            
            }
            
            Toastr::success('Task Assigned Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard/assign_task');
        
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/assign_task');
        }
    }
    
    public function single_employee($empid) {
        $empid = \Crypt::decrypt($empid);
        
        $employe = DB::table('employees')
                ->select('employees.*','p.name as designation','country.name as country_name', 'country.flag_128 as flag_name')
                ->leftjoin('master_resources as p', 'p.id', '=', 'employees.job_position')
                ->leftjoin('country', 'employees.nationality', '=','country.id')
                ->where(['employees.id'=>$empid])
                ->first();
        
        return view('dashboard/assign_task/single_employee',array("employe"=>$employe));
    }
    
    public function multiple_employee() {
        try{

            $assigntask = Input::get('empdetails');
            return view('dashboard/assign_task/multiple_employees',array('arrjson'=>$assigntask));
        
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/assign_task');
        }
    }
    
    public function backtoemplist() {
        try{

            $assigntask = Input::get('empdetails');
            $arrData=  json_decode($assigntask);
            
            $strid='';
            if(count($arrData)>0){
                foreach ($arrData as $value) {
                    $strid=$strid.$value->emp_id.',';
                }
            }
            
            $strid=rtrim($strid, ',');
            $dn = \Crypt::encrypt($strid);
            return Redirect::to('dashboard/assign_task/back/' . $dn);
        
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/assign_task');
        }
    }
    
    public function edit($id)
    {
        $dn = \Crypt::decrypt($id);
         $task = DB::table('tasks')
                ->select('tasks.*',db::raw("concat(employees.first_name,' ',employees.alias_name) as assignedname"))
                ->leftjoin('employees', 'tasks.assigned_by', '=', 'employees.id')
                ->where(['tasks.id' => $dn])                            
                ->first(); 
          
        return view('dashboard/assign_task/edit', array('task' => $task));
           
    }
    
    public function update() {
        try {
            $id=Input::get('planid');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $oldstatus  = Input::get('oldstatus');
            $newstatus  = Input::get('task_status');
            
            if($newstatus){
                $tasks = DB::table('tasks')
                    ->where(['id' => $id])                            
                    ->update(['status'=>$newstatus]);
            }
            
            if($newstatus!='' && $newstatus!=$oldstatus){
                $historyModal=new Task_history();

                $historyModal->task_id=$id;
                $historyModal->status=$newstatus;
                $historyModal->save();
            }
            
            Toastr::success('Task Updated Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }
    
    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $arrJobPosSettings=array();
        $usermodulemodel=new Usermodule();
        $arrJobPosSettings=$usermodulemodel->getjobpositionsettings($login_id,'Assign Task');
            
        $curDate=date('Y-m-d');
        $excelorpdf = Input::get('excelorpdf');
        $search_key = Input::get('search_key');
        $job_position = Input::get('job_position');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordjob = Input::get('sortordjob');
        $sortordcode = Input::get('sortordcode');

        $sortOrdDefault='';
        if($sortordname=='' && $sortordjob=='' && $sortordcode==''){
            $sortOrdDefault='ASC';
        }

        $employees = DB::table('employees')
               ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name',
                       db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id AND task_type NOT IN(3,5)) as new_count"),
                       db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id AND task_type NOT IN(3,5)) as pending_count"))
               ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
               ->where('employees.status', '!=', 2)
               ->when($arrJobPosSettings, function ($query) use ($arrJobPosSettings) {
                        return $query->whereIn("employees.job_position",$arrJobPosSettings);
                     })
               ->when($search_key, function ($query) use ($search_key) {
                   return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
               })
               ->when($job_position, function ($query) use ($job_position) {
                   return $query->where('employees.job_position', '=', $job_position);
               })
               ->when($searchbycode, function ($query) use ($searchbycode) {
                   return $query->whereRaw("(employees.username like '$searchbycode%')");
               })
               ->when($sortordname, function ($query) use ($sortordname) {
                   return $query->orderby('employees.first_name', $sortordname);
               })
               ->when($sortordjob, function ($query) use ($sortordjob) {
                   return $query->orderby('job_position.name', $sortordjob);
               })
               ->when($sortordcode, function ($query) use ($sortordcode) {
                   return $query->orderby('employees.username', $sortordcode);
               })
               ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                   return $query->orderby('employees.first_name', $sortOrdDefault);
               })
               ->get();
            
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('employeelist', function($excel) use($employees){
                 // Set the title
                $excel->setTitle('Employee List');
                
                $excel->sheet('Employee List', function($sheet) use($employees){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Employee List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Employee Name',"Job Position","Employee Code",'New Tasks','Pending Tasks'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($employees);$i++){
                        $employee_name = $employees[$i]->first_name." ".$employees[$i]->alias_name;
//                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('A'.$chrRow, $employee_name);
                        $sheet->setCellValue('B'.$chrRow, str_replace('_',' ',$employees[$i]->job_position_name));
                        $sheet->setCellValue('C'.$chrRow, $employees[$i]->username);
                        $sheet->setCellValue('D'.$chrRow, $employees[$i]->new_count);
                        $sheet->setCellValue('E'.$chrRow, $employees[$i]->pending_count);
                            
                        $sheet->cells('A'.$chrRow.':E'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });
                        
                        $chrRow++;
                    }

                });
                
            })->export('xls');
            
        } else{

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>Project Name</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:Arial;">
                <section id="container">
                <div style="text-align:center;"><h1>Employee List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Job Position</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code </td>
                                <td style="padding:10px 5px;color:#fff;"> New Tasks </td>
                                <td style="padding:10px 5px;color:#fff;"> Pending Tasks </td>
                            </tr>
                        </thead>
                        <tbody class="employee_list" id="employee_list" >';
            $slno=0;
            foreach ($employees as $employee) {
                
                $slno++;
                $employee_name = $employee->first_name." ".$employee->alias_name;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . str_replace('_',' ',$employee->job_position_name) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->new_count . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->pending_count . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_emp_list.pdf');
        }
    }

    
}