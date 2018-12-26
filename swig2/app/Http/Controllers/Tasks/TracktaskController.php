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

class TracktaskController extends Controller {

    public function index(Request $request) {
        try{
            $paginate=Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $plans = DB::table('tasks as t')
                    ->select('t.*','employees.first_name','employees.alias_name')
                    ->leftjoin('employees', 't.owner_id', '=','employees.id')
                    ->whereRaw("t.status!=0 AND assigned_by=$login_id AND task_type=2")
                    ->orderby('start_date','ASC')
                    ->paginate($paginate);
            
            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $searchbytitle = Input::get('searchbytitle');
                $searchbyname = Input::get('searchbyname');
                $startfrom = Input::get('startdatefrom');
                $startto = Input::get('startdateto');
                $endfrom = Input::get('enddatefrom');
                $endto = Input::get('enddateto');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $searchbystatus = Input::get('searchbystatus');
                
                $sortorderstart = Input::get('sortorderstart');
                $sortordname = Input::get('sortordname');
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
                if($sortordtitle=='' && $sortorderstart=='' && $sortorderend=='' && $sortordercreated=='' && $sortordname==''){
                    $sortOrdDefault='ASC';
                }

                $plans = DB::table('tasks as t')
                    ->select('t.*','employees.first_name','employees.alias_name')
                    ->leftjoin('employees', 't.owner_id', '=','employees.id')
                    ->whereRaw("t.status!=0 AND assigned_by=$login_id AND task_type=2")
                    ->when($searchbytitle, function ($query) use ($searchbytitle) {
                        return $query->whereRaw("(t.title like '$searchbytitle%')");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(employees.first_name like '$searchbyname%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$searchbyname%')");
                    })
                    ->when($startfrom, function ($query) use ($startfrom) {
                        return $query->whereRaw("date(t.start_date) >= '$startfrom' ");
                    })
                    ->when($startto, function ($query) use ($startto) {
                        return $query->whereRaw("date(t.start_date) >= '$startto' ");
                    })
                    ->when($endfrom, function ($query) use ($endfrom) {
                        return $query->whereRaw("date(t.end_date)<= '$endfrom' ");
                    })
                    ->when($endto, function ($query) use ($endto) {
                        return $query->whereRaw("date(t.end_date)<= '$endto' ");
                    })
                    ->when($searchbystatus, function ($query) use ($searchbystatus) {
                        return $query->whereRaw("t.status=$searchbystatus");
                    })
                    ->when($createdatfrom, function ($query) use ($createdatfrom) {
                        return $query->whereRaw("date(t.created_at)>= '$createdatfrom' ");
                    })
                    ->when($createdatto, function ($query) use ($createdatto) {
                        return $query->whereRaw("date(t.created_at)<= '$createdatto' ");
                    })
                    ->when($sortordtitle, function ($query) use ($sortordtitle) {
                        return $query->orderby('t.title', $sortordtitle);
                    })
                    ->when($sortorderstart, function ($query) use ($sortorderstart) {
                        return $query->orderby('t.start_date', $sortorderstart);
                    })
                    ->when($sortorderend, function ($query) use ($sortorderend) {
                        return $query->orderby('t.end_date', $sortorderend);
                    })
                    ->when($sortordercreated, function ($query) use ($sortordercreated) {
                        return $query->orderby('t.created_at', $sortordercreated);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('t.start_date', $sortOrdDefault);
                    })
                    ->paginate($paginate);

                return view('dashboard/track_task/track_result', array('plans' => $plans));
            }

            return view('dashboard/track_task/index', array('plans' => $plans));
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }
    
    public function edit($id)
    {
        $taskid = \Crypt::decrypt($id);
        $plan = DB::table('tasks as t')
                ->select('t.*','employees.first_name','employees.alias_name')
                ->leftjoin('employees', 't.owner_id', '=','employees.id')
                ->where(['t.id' => $taskid])                            
                ->first();      
          
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
            
            if(($strStart!="00:00:00" || $strEnd!="00:00:00") || ($strStart=="00:00:00" && $strEnd=="00:00:00" && $tasks->is_all_day_task==true) || $tasks->task_type==2){
                $datStart=date('d-m-Y H:i:s',strtotime($tasks->start_date));
                $datEnd=date('d-m-Y H:i:s',strtotime($tasks->end_date));
                $strHtml="<p>Plan Start Date : $datStart</p>";
                $strHtml.="<p>Plan End Date &nbsp&nbsp: $datEnd</p>";
            }
        }
        
        return view('dashboard/track_task/edit', array('plan' => $plan,'history'=>$history));
           
    }
     
    public function update() {
        try {
            $id=Input::get('planid');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $start_date  = Input::get('start_date');
            $end_date  = Input::get('end_date');
            $start_time  = Input::get('start_time');
            $end_time  = Input::get('end_time');
            $isalldaytask  = Input::get('alldaytask');
            $oldstatus  = Input::get('oldstatus');
            $newstatus  = Input::get('task_status');
           
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
            
           
            $tasks = DB::table('tasks')
                ->where(['id' => $id])                            
                ->update([
                            'title' => Input::get('title'),
                            'description'=>Input::get('description'),
                            'start_date'=>$start_date,
                            'end_date'=>$end_date,
                            'is_all_day_task'=>$isalldaytask
                        ]);
            
            
            Toastr::success('Task Updated Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard/track_task');
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/track_task');
        }
    }
    
    public function editcompletedtask($id)
    {
        $dn = \Crypt::decrypt($id);
        $task = DB::table('tasks')
                ->select('tasks.*',db::raw("concat(employees.first_name,' ',employees.alias_name) as assignedname"))
                ->leftjoin('employees', 'tasks.assigned_by', '=', 'employees.id')
                ->where(['tasks.id' => $dn])                            
                ->first();    
        
        return view('dashboard/track_task/completedtaskview', array('task' => $task));
           
    }
    
    public function deleteplan($id) {
        try {
            $taskid = \Crypt::decrypt($id);
            $tasks = DB::table('tasks')
                    ->where(['id' => $taskid])
                    ->update(['status' => 0]);
            
            $historyModal=new Task_history();
               
            $historyModal->task_id=$taskid;
            $historyModal->status=0;
            $historyModal->save();
            
            Toastr::success('Task Deleted Successfully', $title = null, $options = []);
            return Redirect::to('dashboard/track_task');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/track_task');
        }
    }
    
     // Generate PDF function
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $excelorpdf = Input::get('excelorpdf');
        $searchbytitle = Input::get('searchbytitle');
        $searchbyname = Input::get('searchbyname');
        $startfrom = Input::get('startdatefrom');
        $startto = Input::get('startdateto');
        $endfrom = Input::get('enddatefrom');
        $endto = Input::get('enddateto');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $searchbystatus = Input::get('searchbystatus');

        $sortorderstart = Input::get('sortorderstart');
        $sortordname = Input::get('sortordname');
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
        if($sortordtitle=='' && $sortorderstart=='' && $sortorderend=='' && $sortordercreated=='' && $sortordname==''){
            $sortOrdDefault='ASC';
        }

        $plans = DB::table('tasks as t')
            ->select('t.*','employees.first_name','employees.alias_name')
            ->leftjoin('employees', 't.owner_id', '=','employees.id')
            ->whereRaw("t.status!=0 AND assigned_by=$login_id AND task_type=2")
            ->when($searchbytitle, function ($query) use ($searchbytitle) {
                return $query->whereRaw("(t.title like '$searchbytitle%')");
            })
            ->when($searchbyname, function ($query) use ($searchbyname) {
                return $query->whereRaw("(employees.first_name like '$searchbyname%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$searchbyname%')");
            })
            ->when($startfrom, function ($query) use ($startfrom) {
                return $query->whereRaw("date(t.start_date) >= '$startfrom' ");
            })
            ->when($startto, function ($query) use ($startto) {
                return $query->whereRaw("date(t.start_date) >= '$startto' ");
            })
            ->when($endfrom, function ($query) use ($endfrom) {
                return $query->whereRaw("date(t.end_date)<= '$endfrom' ");
            })
            ->when($endto, function ($query) use ($endto) {
                return $query->whereRaw("date(t.end_date)<= '$endto' ");
            })
            ->when($searchbystatus, function ($query) use ($searchbystatus) {
                return $query->whereRaw("t.status=$searchbystatus");
            })
            ->when($createdatfrom, function ($query) use ($createdatfrom) {
                return $query->whereRaw("date(t.created_at)>= '$createdatfrom' ");
            })
            ->when($createdatto, function ($query) use ($createdatto) {
                return $query->whereRaw("date(t.created_at)<= '$createdatto' ");
            })
            ->when($sortordtitle, function ($query) use ($sortordtitle) {
                return $query->orderby('t.title', $sortordtitle);
            })
            ->when($sortorderstart, function ($query) use ($sortorderstart) {
                return $query->orderby('t.start_date', $sortorderstart);
            })
            ->when($sortorderend, function ($query) use ($sortorderend) {
                return $query->orderby('t.end_date', $sortorderend);
            })
            ->when($sortordercreated, function ($query) use ($sortordercreated) {
                return $query->orderby('t.created_at', $sortordercreated);
            })
            ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                return $query->orderby('t.start_date', $sortOrdDefault);
            })
            ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('assigned_tasks', function($excel) use($plans){
                 // Set the title
                $excel->setTitle('Assigned Task List');
                
                $excel->sheet('Assigned Task List', function($sheet) use($plans){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Assigned Task List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:H3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Assigned To','Title',"Start Date","End Date",'Status','Created Date','Description'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:H5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($plans);$i++){
                        
                        if ($plans[$i]->is_all_day_task) {
                            $start=date("d-m-Y", strtotime($plans[$i]->start_date));
                            $end=date("d-m-Y", strtotime($plans[$i]->end_date));
                        } else {
                            $start=date("d-m-Y H:i:s", strtotime($plans[$i]->start_date));
                            $end=date("d-m-Y H:i:s", strtotime($plans[$i]->end_date));
                        }
                        
                        if($plans[$i]->status==3){
                            $status="Completed";
                        }else if($plans[$i]->status==2){
                            $status="Pending";
                        }else{
                            $status="New";
                        }
                        
                        $employeename=$plans[$i]->first_name." ".$plans[$i]->alias_name;
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $employeename);
                        $sheet->setCellValue('C'.$chrRow, $plans[$i]->title);
                        $sheet->setCellValue('D'.$chrRow, $start);
                        $sheet->setCellValue('E'.$chrRow, $end);
                        $sheet->setCellValue('F'.$chrRow, $status);
                        $sheet->setCellValue('G'.$chrRow, date("d-m-Y", strtotime($plans[$i]->created_at)));
                        $sheet->setCellValue('H'.$chrRow, $plans[$i]->description);
                            
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
                <div style="text-align:center;"><h1>Assigned Task List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Assigned To</td>
                                <td style="padding:10px 5px;color:#fff;"> Title</td>
                                <td style="padding:10px 5px;color:#fff;"> Start Date</td>
                                <td style="padding:10px 5px;color:#fff;"> End Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Status </td>
                                <td style="padding:10px 5px;color:#fff;"> Created Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Description </td>
                            </tr>
                        </thead>
                        <tbody class="planbody" id="planbody" >';
            $slno=0;
            foreach ($plans as $plan) {
                if ($plan->is_all_day_task) {
                    $start=date("d-m-Y", strtotime($plan->start_date));
                    $end=date("d-m-Y", strtotime($plan->end_date));
                } else {
                    $start=date("d-m-Y H:i:s", strtotime($plan->start_date));
                    $end=date("d-m-Y H:i:s", strtotime($plan->end_date));
                }
                
                if($plan->status==3){
                    $status="Completed";
                }else if($plan->status==2){
                    $status="Pending";
                }else{
                    $status="New";
                }
                
                $employeename=$plan->first_name." ".$plan->alias_name;
                
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employeename . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $plan->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $start . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $end . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $status . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($plan->created_at)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $plan->description . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_track_task.pdf');
        }
    }

}
