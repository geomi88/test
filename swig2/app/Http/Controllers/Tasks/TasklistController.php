<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Company;
use App\Models\Tasks;
use App\Models\Task_history;
use DB;
use App;
use PDF;
use Excel;

class TasklistController extends Controller {

    public function index(Request $request) {
        try{
            $paginate=Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
           
            $tasks = DB::table('tasks')
                    ->select('tasks.*',db::raw("concat(employees.first_name,' ',employees.alias_name) as assignedname"))
                    ->leftjoin('employees', 'tasks.assigned_by', '=', 'employees.id')
                    ->whereRaw("tasks.status!=0 AND owner_id=$login_id AND task_type=2")
                    ->orderby('start_date','ASC')
                    ->paginate($paginate);

            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $searchbytitle = Input::get('searchbytitle');
                $startfrom = Input::get('startdatefrom');
                $startto = Input::get('startdateto');
                $endfrom = Input::get('enddatefrom');
                $endto = Input::get('enddateto');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $searchbystatus = Input::get('searchbystatus');
                
                $sortorderstart = Input::get('sortorderstart');
                $sortorderend = Input::get('sortorderend');
                $sortordtitle = Input::get('sortordtitle');
                $sortordercreated = Input::get('sortordercreated');

                if ($startfrom != '') {
                    $startfrom = explode('-', $startfrom);
                    $startfrom = $startfrom[2] . '-' . $startfrom[1] . '-' . $startfrom[0];
                }
                if ($endfrom != '') {
                    $endfrom = explode('-', $endfrom);
                    $endfrom = $endfrom[2] . '-' . $endfrom[1] . '-' . $endfrom[0];
                }

                if ($startto != '') {
                    $startto = explode('-', $startto);
                    $startto = $startto[2] . '-' . $startto[1] . '-' . $startto[0];
                }
                if ($endto != '') {
                    $endto = explode('-', $endto);
                    $endto = $endto[2] . '-' . $endto[1] . '-' . $endto[0];
                }
                
                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }

                $sortOrdDefault='';
                if($sortordtitle=='' && $sortorderstart=='' && $sortorderend=='' && $sortordercreated==''){
                    $sortOrdDefault='ASC';
                }

            $tasks = DB::table('tasks')
                    ->select('tasks.*',db::raw("concat(employees.first_name,' ',employees.alias_name) as assignedname"))
                    ->leftjoin('employees', 'tasks.assigned_by', '=', 'employees.id')
                    ->whereRaw("tasks.status!=0 AND owner_id=$login_id AND  task_type=2")
                    ->when($searchbytitle, function ($query) use ($searchbytitle) {
                        return $query->whereRaw("(tasks.title like '$searchbytitle%')");
                    })
                    ->when($startfrom, function ($query) use ($startfrom) {
                        return $query->whereRaw("date(tasks.start_date) >= '$startfrom' ");
                    })
                    ->when($startto, function ($query) use ($startto) {
                        return $query->whereRaw("date(tasks.start_date) >= '$startto' ");
                    })
                    ->when($endfrom, function ($query) use ($endfrom) {
                        return $query->whereRaw("date(tasks.end_date)<= '$endfrom' ");
                    })
                    ->when($endto, function ($query) use ($endto) {
                        return $query->whereRaw("date(tasks.end_date)<= '$endto' ");
                    })
                    ->when($searchbystatus, function ($query) use ($searchbystatus) {
                        return $query->whereRaw("tasks.status=$searchbystatus");
                    })
                    ->when($createdatfrom, function ($query) use ($createdatfrom) {
                        return $query->whereRaw("date(tasks.created_at)>= '$createdatfrom' ");
                    })
                    ->when($createdatto, function ($query) use ($createdatto) {
                        return $query->whereRaw("date(tasks.created_at)<= '$createdatto' ");
                    })
                    ->when($sortordtitle, function ($query) use ($sortordtitle) {
                        return $query->orderby('tasks.title', $sortordtitle);
                    })
                    ->when($sortorderstart, function ($query) use ($sortorderstart) {
                        return $query->orderby('tasks.start_date', $sortorderstart);
                    })
                    ->when($sortorderend, function ($query) use ($sortorderend) {
                        return $query->orderby('tasks.end_date', $sortorderend);
                    })
                    ->when($sortordercreated, function ($query) use ($sortordercreated) {
                        return $query->orderby('tasks.created_at', $sortordercreated);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('tasks.start_date', $sortOrdDefault);
                    })
                    ->paginate($paginate);

                return view('dashboard/task_list/task_result', array('tasks' => $tasks));
            }

            return view('dashboard/task_list/index', array('tasks' => $tasks));
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
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
        
        return view('dashboard/task_list/edit', array('task' => $task));
           
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
            return Redirect::to('dashboard/task_list');
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }
    
    
    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbytitle = Input::get('searchbytitle');
        $startfrom = Input::get('startdatefrom');
        $startto = Input::get('startdateto');
        $endfrom = Input::get('enddatefrom');
        $endto = Input::get('enddateto');
        $searchbystatus = Input::get('searchbystatus');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');

        $sortorderstart = Input::get('sortorderstart');
        $sortorderend = Input::get('sortorderend');
        $sortordtitle = Input::get('sortordtitle');
        $sortordercreated = Input::get('sortordercreated');
        
        if ($startfrom != '') {
            $startfrom = explode('-', $startfrom);
            $startfrom = $startfrom[2] . '-' . $startfrom[1] . '-' . $startfrom[0];
        }
        if ($endfrom != '') {
            $endfrom = explode('-', $endfrom);
            $endfrom = $endfrom[2] . '-' . $endfrom[1] . '-' . $endfrom[0];
        }

        if ($startto != '') {
            $startto = explode('-', $startto);
            $startto = $startto[2] . '-' . $startto[1] . '-' . $startto[0];
        }
        if ($endto != '') {
            $endto = explode('-', $endto);
            $endto = $endto[2] . '-' . $endto[1] . '-' . $endto[0];
        }
        
        if ($createdatfrom != '') {
            $createdatfrom = explode('-', $createdatfrom);
            $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
        }
        if ($createdatto != '') {
            $createdatto = explode('-', $createdatto);
            $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
        }
                
        $sortOrdDefault='';
        if($sortordtitle=='' && $sortorderstart=='' && $sortorderend=='' && $sortordercreated==''){
            $sortOrdDefault='ASC';
        }

        $tasks = DB::table('tasks')
                ->select('tasks.*',db::raw("concat(employees.first_name,' ',employees.alias_name) as assignedname"))
                ->leftjoin('employees', 'tasks.assigned_by', '=', 'employees.id')
                ->whereRaw("tasks.status!=0 AND owner_id=$login_id AND task_type=2")
                ->when($searchbytitle, function ($query) use ($searchbytitle) {
                    return $query->whereRaw("(tasks.title like '$searchbytitle%')");
                })
                ->when($startfrom, function ($query) use ($startfrom) {
                    return $query->whereRaw("date(tasks.start_date) >= '$startfrom' ");
                })
                ->when($startto, function ($query) use ($startto) {
                    return $query->whereRaw("date(tasks.start_date) >= '$startto' ");
                })
                ->when($endfrom, function ($query) use ($endfrom) {
                    return $query->whereRaw("date(tasks.end_date)<= '$endfrom' ");
                })
                ->when($endto, function ($query) use ($endto) {
                    return $query->whereRaw("date(tasks.end_date)<= '$endto' ");
                })
                ->when($searchbystatus, function ($query) use ($searchbystatus) {
                    return $query->whereRaw("tasks.status=$searchbystatus");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(tasks.created_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(tasks.created_at)<= '$createdatto' ");
                })
                ->when($sortordtitle, function ($query) use ($sortordtitle) {
                    return $query->orderby('tasks.title', $sortordtitle);
                })
                ->when($sortorderstart, function ($query) use ($sortorderstart) {
                    return $query->orderby('tasks.start_date', $sortorderstart);
                })
                ->when($sortorderend, function ($query) use ($sortorderend) {
                    return $query->orderby('tasks.end_date', $sortorderend);
                })
                ->when($sortordercreated, function ($query) use ($sortordercreated) {
                    return $query->orderby('tasks.created_at', $sortordercreated);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('tasks.start_date', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('tasks', function($excel) use($tasks){
                 // Set the title
                $excel->setTitle('Tasks');
                
                $excel->sheet('Tasks', function($sheet) use($tasks){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Task List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:H3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Title',"Start Date","End Date",'Status','Created Date','Assigned By','Description'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:H5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($tasks);$i++){
                        
                        if ($tasks[$i]->is_all_day_task) {
                            $start=date("d-m-Y", strtotime($tasks[$i]->start_date));
                            $end=date("d-m-Y", strtotime($tasks[$i]->end_date));
                        } else {
                            $start=date("d-m-Y H:i:s", strtotime($tasks[$i]->start_date));
                            $end=date("d-m-Y H:i:s", strtotime($tasks[$i]->end_date));
                        }
                        
                        if($tasks[$i]->status==3){
                            $status="Completed";
                        }else if($tasks[$i]->status==2){
                            $status="Pending";
                        }else{
                            $status="New";
                        }
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $tasks[$i]->title);
                        $sheet->setCellValue('C'.$chrRow, $start);
                        $sheet->setCellValue('D'.$chrRow, $end);
                        $sheet->setCellValue('E'.$chrRow, $status);
                        $sheet->setCellValue('F'.$chrRow, date("d-m-Y H:i:s", strtotime($tasks[$i]->created_at)));
                        $sheet->setCellValue('G'.$chrRow, $tasks[$i]->assignedname);
                        $sheet->setCellValue('H'.$chrRow, $tasks[$i]->description);
                            
                        $sheet->cells('A'.$chrRow.':H'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Task List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Title</td>
                                <td style="padding:10px 5px;color:#fff;"> Start Date</td>
                                <td style="padding:10px 5px;color:#fff;"> End Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Status </td>
                                <td style="padding:10px 5px;color:#fff;"> Created Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Assigned By </td>
                                <td style="padding:10px 5px;color:#fff;"> Description </td>
                            </tr>
                        </thead>
                        <tbody class="taskbody" id="taskbody" >';
            $slno=0;
            foreach ($tasks as $task) {
                if ($task->is_all_day_task) {
                    $start=date("d-m-Y", strtotime($task->start_date));
                    $end=date("d-m-Y", strtotime($task->end_date));
                } else {
                    $start=date("d-m-Y H:i:s", strtotime($task->start_date));
                    $end=date("d-m-Y H:i:s", strtotime($task->end_date));
                }
                
                if($task->status==3){
                    $status="Completed";
                }else if($task->status==2){
                    $status="Pending";
                }else{
                    $status="New";
                }
    
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $task->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $start . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $end . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $status . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y H:i:s", strtotime($task->created_at)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $task->assignedname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $task->description . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_tasks.pdf');
        }
    }

}
