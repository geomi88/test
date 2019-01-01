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
use App\Models\Usermodule;
use App;
use DB;
use PDF;
use Excel;

class ViewtodoController extends Controller

{
    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $arrJobPosSettings = array();
            $usermodulemodel = new Usermodule();
            $arrJobPosSettings = $usermodulemodel->getjobpositionsettings($login_id, 'View To Do');

            $curDate = date('Y-m-d');
            $employees = DB::table('employees')
                    ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 
                            db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5))as new_count"), 
                            db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5))as pending_count"))
                    ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                    ->where('employees.status', '!=', 2)
                    ->when($arrJobPosSettings, function ($query) use ($arrJobPosSettings) {
                        return $query->whereIn("employees.job_position",$arrJobPosSettings);
                     })
                    ->orderby('employees.first_name', 'ASC')
                    ->paginate($paginate);

            $job_positions = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                            ->when($arrJobPosSettings, function ($query) use ($arrJobPosSettings) {
                                return $query->whereIn("master_resources.id",$arrJobPosSettings);
                             })
                            ->orderby('name', 'ASC')->get();

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

                $sortOrdDefault = '';
                if ($sortordname == '' && $sortordjob == '' && $sortordcode == '') {
                    $sortOrdDefault = 'ASC';
                }

                $employees = DB::table('employees')
                        ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5))as new_count"), db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5))as pending_count"))
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

                return view('dashboard/view_todo/emp_result', array('employees' => $employees));
            }
            return view('dashboard/view_todo/employees_list', array('employees' => $employees, 'job_positions' => $job_positions));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/view_todo');
        }
    }
    
    public function gettodo($empid) {
        $empid = \Crypt::decrypt($empid);
        $curDate=date('Y-m-d');
                
        $new_tasks = DB::table('tasks')
                ->select('tasks.*',db::raw("(case when task_type=0 then 'clstodocolor' when task_type=1 then 'clsplancolor' when task_type=2 then 'clsassignedtask' when task_type=4 then 'clsagendacolor' end) as strColorClass"))
                ->whereRaw("status=1 AND owner_id=$empid AND date(start_date)<='$curDate' AND task_type NOT IN(3,5)")
                ->orderby('priority','ASC')
                ->orderby('created_at','DESC')
                ->get();
        
        $pending_tasks = DB::table('tasks')
                ->select('tasks.*',db::raw("(case when task_type=0 then 'clstodocolor' when task_type=1 then 'clsplancolor' when task_type=2 then 'clsassignedtask' when task_type=4 then 'clsagendacolor' end) as strColorClass"))
                ->whereRaw("status=2 AND owner_id=$empid AND date(start_date)<='$curDate' AND task_type NOT IN(3,5)")
                ->orderby('priority','ASC')
                ->orderby('created_at','DESC')
                ->get();
        
        $days_ago = date('Y-m-d', strtotime('-15 days', strtotime("$curDate")));
        
        $completed_tasks = DB::table('tasks')
                ->select('tasks.*',db::raw("(case when task_type=0 then 'clstodocolor' when task_type=1 then 'clsplancolor' when task_type=2 then 'clsassignedtask' when task_type=4 then 'clsagendacolor' end) as strColorClass"))
                ->whereRaw("status=3 AND owner_id=$empid AND date(start_date)<='$curDate' AND task_type NOT IN(3,5) AND date(start_date)>='$days_ago'")
                ->orderby('updated_at','DESC')
                ->get();
        
        $employe = DB::table('employees')
                ->select('employees.*','p.name as designation','country.name as country_name', 'country.flag_128 as flag_name')
                ->leftjoin('master_resources as p', 'p.id', '=', 'employees.job_position')
                ->leftjoin('country', 'employees.nationality', '=','country.id')
                ->where(['employees.id'=>$empid])
                ->first();
        
        $strUrl=url()->current();
        $backbutton='';
        if (strpos($strUrl, 'get_todo') !== false) {
            $backbutton='show';
        }
            
        return view('dashboard/view_todo/view_todo',array("new_tasks"=>$new_tasks,"pending_tasks"=>$pending_tasks,"completed_tasks"=>$completed_tasks,"employe"=>$employe,'backbutton'=>$backbutton));
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
    
    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $arrJobPosSettings = array();
        $usermodulemodel = new Usermodule();
        $arrJobPosSettings = $usermodulemodel->getjobpositionsettings($login_id, 'View To Do');
            
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
                       db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5))as new_count"),
                       db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id AND date(start_date)<='$curDate' AND task_type NOT IN(3,5))as pending_count"))
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