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

class PlanController extends Controller {

    public function index(Request $request,$id='') {
        try{
            $paginate=Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $empid='';
            if($id){
                $empid = \Crypt::decrypt($id);
                $login_id=$empid;
            }
            
            $curDate=date('Y-m-d');
            $plans = DB::table('tasks')
                    ->select('tasks.*')
                    ->whereRaw("status!=0 AND owner_id=$login_id AND date(start_date)>'$curDate' AND task_type=1")
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
                $empid = Input::get('empid');
                if($empid){
                    $login_id=$empid;
                }
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

                $plans = DB::table('tasks')
                    ->select('tasks.*')
                    ->whereRaw("status!=0 AND owner_id=$login_id AND date(start_date)>'$curDate' AND task_type=1")
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

                return view('dashboard/plan/plan_result', array('plans' => $plans,'empid'=>$empid));
            }

            return view('dashboard/plan/index', array('plans' => $plans,'empid'=>$empid));
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }
    
    public function getplanprint() {
        try{
            $paginate=Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $start = Input::get('startDate');
            $end = Input::get('endDate');
            $task_type = Input::get('task_type');
                
            $curDate=date('Y-m-d');
            
            $tasks1 = DB::table('tasks')
                ->select('tasks.*')
                ->join('meeting_attendees', 'tasks.id', '=', 'meeting_attendees.meeting_id')
                ->whereRaw("meeting_attendees.user_id=$login_id AND (date(end_date)>='$curDate' AND task_type IN (3)) AND status!=0")
                ->whereRaw("((date(start_date) BETWEEN '$start' AND '$end') OR (date(end_date) BETWEEN '$start' AND '$end') OR (date(start_date) < '$start' AND date(end_date) > '$end'))")
                ->when($task_type, function ($query) use ($task_type) {
                        return $query->where('tasks.task_type', '=', $task_type);
                });
                
            $tasks2 = DB::table('tasks')
                ->select('tasks.*')
                ->join('training_attendees', 'tasks.id', '=', 'training_attendees.training_id')
                ->whereRaw("training_attendees.user_id=$login_id AND (date(end_date)>='$curDate' AND task_type IN (5)) AND status!=0")
                ->whereRaw("((date(start_date) BETWEEN '$start' AND '$end') OR (date(end_date) BETWEEN '$start' AND '$end') OR (date(start_date) < '$start' AND date(end_date) > '$end'))")
                ->when($task_type, function ($query) use ($task_type) {
                        return $query->where('tasks.task_type', '=', $task_type);
                });
                
            $plans = DB::table('tasks')
                ->select('tasks.*')
                ->whereRaw("owner_id=$login_id AND ((date(start_date)>'$curDate' AND task_type IN (1,3,4)) OR (task_type=2 AND status!=3)) AND status!=0")
                ->whereRaw("((date(start_date) BETWEEN '$start' AND '$end') OR (date(end_date) BETWEEN '$start' AND '$end') OR (date(start_date) < '$start' AND date(end_date) > '$end'))")
                ->union($tasks1)
                ->union($tasks2)
                ->when($task_type, function ($query) use ($task_type) {
                        return $query->where('tasks.task_type', '=', $task_type);
                })
                ->orderby('start_date','ASC')
                ->get();
            
            
            return view('dashboard/result', array('plans' => $plans));
            
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

    public function createplan()
    {  
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        return view('dashboard/plan/createplan', array("plandate"=>""));
            
    }
    public function saveplan() {
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
             
            $model = new Tasks();
            $model->title = Input::get('title');
            $model->description = Input::get('description');
            $model->start_date = $start_date;
            $model->end_date = $end_date;
            $model->owner_id  = $login_id;
            $model->is_all_day_task = $isalldaytask;
            $model->priority = 0;
            $model->status = 1;
            $model->task_type = 1;
            $model->save();

            $historyModal=new Task_history();

            $historyModal->task_id=$model->id;
            $historyModal->status=1;
            $historyModal->save();
            
            Toastr::success('Plan Added Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard/plan');
        
        }catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/plan');
        }
    }
    
    public function edit($id)
    {
        $dn = \Crypt::decrypt($id);
        $plan = DB::table('tasks')
                ->where(['id' => $dn])                            
                ->first();      
          
        return view('dashboard/plan/edit', array('plan' => $plan));
           
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
            
            if($newstatus==''){
                $tasks = DB::table('tasks')
                    ->where(['id' => $id])                            
                    ->update([
                                'title' => Input::get('title'),
                                'description'=>Input::get('description'),
                                'start_date'=>$start_date,
                                'end_date'=>$end_date,
                                'owner_id'=>$login_id,
                                'is_all_day_task'=>$isalldaytask
                            ]);
            }else{
                $tasks = DB::table('tasks')
                    ->where(['id' => $id])                            
                    ->update([
                                'title' => Input::get('title'),
                                'description'=>Input::get('description'),
                                'start_date'=>$start_date,
                                'end_date'=>$end_date,
                                'owner_id'=>$login_id,
                                'is_all_day_task'=>$isalldaytask,
                                'status'=>$newstatus
                            ]);
            }
            
            
            if($newstatus!='' && $newstatus!=$oldstatus){
                $historyModal=new Task_history();

                $historyModal->task_id=$id;
                $historyModal->status=$newstatus;
                $historyModal->save();
            }
            
            Toastr::success('Plan Updated Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard/plan');
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/plan');
        }
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
            return Redirect::to('dashboard/plan');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/plan');
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
        
        $curDate=date('Y-m-d');
        
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
        
        $empid = Input::get('empid');
        if($empid){
            $login_id=$empid;
        }
        
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

        $plans = DB::table('tasks')
            ->select('tasks.*')
            ->whereRaw("status!=0 AND owner_id=$login_id AND date(start_date)>'$curDate' AND task_type=1")
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
            
            Excel::create('plans', function($excel) use($plans){
                 // Set the title
                $excel->setTitle('Plans');
                
                $excel->sheet('Plans', function($sheet) use($plans){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Plans');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Title',"Start Date","End Date",'Status','Created Date','Description'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
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
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $plans[$i]->title);
                        $sheet->setCellValue('C'.$chrRow, $start);
                        $sheet->setCellValue('D'.$chrRow, $end);
                        $sheet->setCellValue('E'.$chrRow, $status);
                        $sheet->setCellValue('F'.$chrRow, date("d-m-Y H:i:s", strtotime($plans[$i]->created_at)));
                        $sheet->setCellValue('G'.$chrRow, $plans[$i]->description);
                            
                        $sheet->cells('A'.$chrRow.':G'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Plans</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
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
    
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $plan->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $start . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $end . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $status . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y H:i:s", strtotime($plan->created_at)) . '</td>
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
            return $pdf->download('mtg_plans.pdf');
        }
    }

}
