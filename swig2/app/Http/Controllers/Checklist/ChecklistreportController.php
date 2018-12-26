<?php

namespace App\Http\Controllers\Checklist;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use App\Models\Company;
use App\Models\Masterresources;
use DB;
use App;
use PDF;
use Excel;

class ChecklistreportController extends Controller {

    public function index(Request $request,$id='') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $rating_id='';
        if($id){
            $rating_id = \Crypt::decrypt($id);
        }
        
        $checklist_categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'CHECK_LIST_CATEGORY')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name', 'ASC')
                ->get();
        
        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();
        
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')
                ->get();
                             
        
        $checklistentries = DB::table('checklist_entry')
                ->select('checklist_entry.*','list.checkpoint as checkpoint','category.name as maincategory','branch.name as br_name','branch.branch_code as br_code','employees.username as username','employees.first_name as first_name','employees.alias_name as alias_name','jobpos.name as job_position')
                ->leftjoin('check_list as list', 'checklist_entry.checkpoint_id', '=', 'list.id')
                ->leftjoin('master_resources as category', 'list.category_id', '=', 'category.id')
                ->leftjoin('master_resources as branch', 'checklist_entry.branch_id', '=', 'branch.id')
                ->leftjoin('employees', 'checklist_entry.employee_id', '=', 'employees.id')
                ->leftjoin('master_resources as jobpos', 'employees.job_position', '=', 'jobpos.id')
                ->where('checklist_entry.status', '=', 1)
                ->when($rating_id, function ($query) use ($rating_id) {
                    return $query->where('checklist_entry.rating', '=', "$rating_id");
                })
                ->orderby('category.name', 'ASC')
                ->orderby('checklist_entry.entry_date', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbycategory = Input::get('searchbycategory');
            $searchbypoint = Input::get('searchbypoint');
            $startfrom = Input::get('start_date');
            $endfrom = Input::get('end_date');
            $searchbybranch = Input::get('searchbybranch');
            $searchbyrating = Input::get('searchbyrating');
            
            $search_key = Input::get('search_key');
            $job_position = Input::get('job_position');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordjob = Input::get('sortordjob');
            $sortordcode = Input::get('sortordcode');
            
            $sortordcategory = Input::get('sortordcategory');
            $sortordpoint = Input::get('sortordpoint');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordrating = Input::get('sortordrating');
            
            $sortOrdDefault='';
            if($sortordbranch=='' && $sortorddate=='' && $sortordpoint=='' && $sortordcategory=='' && $sortordrating=='' ){
                $sortOrdDefault='ASC';
            }
            
            if ($startfrom != '') {
                $startfrom = explode('-', $startfrom);
                $startfrom = $startfrom[2] . '-' . $startfrom[1] . '-' . $startfrom[0];
            }
            if ($endfrom != '') {
                $endfrom = explode('-', $endfrom);
                $endfrom = $endfrom[2] . '-' . $endfrom[1] . '-' . $endfrom[0];
            }
                
            $checklistentries = DB::table('checklist_entry')
                    ->select('checklist_entry.*','list.checkpoint as checkpoint','category.name as maincategory','branch.name as br_name','branch.branch_code as br_code','employees.username as username','employees.first_name as first_name','employees.alias_name as alias_name','jobpos.name as job_position')
                    ->leftjoin('check_list as list', 'checklist_entry.checkpoint_id', '=', 'list.id')
                    ->leftjoin('master_resources as category', 'list.category_id', '=', 'category.id')
                    ->leftjoin('master_resources as branch', 'checklist_entry.branch_id', '=', 'branch.id')
                    ->leftjoin('employees', 'checklist_entry.employee_id', '=', 'employees.id')
                    ->leftjoin('master_resources as jobpos', 'employees.job_position', '=', 'jobpos.id')
                    ->where('checklist_entry.status', '=', 1)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
                    })
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(employees.username like '$searchbycode%')");
                    })
                    ->when($searchbycategory, function ($query) use ($searchbycategory) {
                        return $query->whereRaw("(list.category_id=$searchbycategory)");
                    })
                    ->when($searchbypoint, function ($query) use ($searchbypoint) {
                        return $query->whereRaw("(list.checkpoint like '$searchbypoint%')");
                    })
                    ->when($startfrom, function ($query) use ($startfrom) {
                        return $query->whereRaw("date(checklist_entry.entry_date) >= '$startfrom' ");
                    })
                    ->when($endfrom, function ($query) use ($endfrom) {
                        return $query->whereRaw("date(checklist_entry.entry_date)<= '$endfrom' ");
                    })
                    ->when($searchbybranch, function ($query) use ($searchbybranch) {
                        return $query->whereRaw("checklist_entry.branch_id=$searchbybranch");
                    })
                    ->when($searchbyrating, function ($query) use ($searchbyrating) {
                        return $query->whereRaw("checklist_entry.rating='$searchbyrating'");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('employees.first_name', $sortordname);
                    })
                    ->when($sortordjob, function ($query) use ($sortordjob) {
                        return $query->orderby('jobpos.name', $sortordjob);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('employees.username', $sortordcode);
                    })
                    ->when($sortordcategory, function ($query) use ($sortordcategory) {
                        return $query->orderby('category.name', $sortordcategory);
                    })
                    ->when($sortordpoint, function ($query) use ($sortordpoint) {
                        return $query->orderby('list.checkpoint', $sortordpoint);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('checklist_entry.entry_date', $sortorddate);
                    })
                    
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordrating, function ($query) use ($sortordrating) {
                        return $query->orderby('checklist_entry.rating', $sortordrating);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('category.name', $sortOrdDefault)->orderby('checklist_entry.entry_date', 'DESC');
                        
                    })
                    ->paginate($paginate);

            return view('checklist/checklist_report/result', array('checklistentries'=>$checklistentries));
        }

        return view('checklist/checklist_report/index', array('checklist_categories' => $checklist_categories,'allbranches'=>$allbranches,'checklistentries'=>$checklistentries,'job_positions'=>$job_positions,'rating_id'=>$rating_id));
    }

    public function getdetails($id) {
        $check_id = \Crypt::decrypt($id);
        
        $checklistentry = DB::table('checklist_entry')
                ->select('checklist_entry.*','list.checkpoint as checkpoint','category.name as maincategory','emp.first_name as first_name','emp.alias_name as alias_name','branch.name as br_name','branch.branch_code as br_code')
                ->leftjoin('employees as emp', 'checklist_entry.employee_id', '=', 'emp.id')
                ->leftjoin('check_list as list', 'checklist_entry.checkpoint_id', '=', 'list.id')
                ->leftjoin('master_resources as category', 'list.category_id', '=', 'category.id')
                ->leftjoin('master_resources as branch', 'checklist_entry.branch_id', '=', 'branch.id')
                ->where('checklist_entry.status', '=', 1)
                ->where('checklist_entry.id', '=', $check_id)
                ->first();
        
        return view('checklist/checklist_report/show', array('checklistentry'=>$checklistentry));
    }

        // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
            $searchbycategory = Input::get('searchbycategory');
            $searchbypoint = Input::get('searchbypoint');
            $startfrom = Input::get('start_date');
            $endfrom = Input::get('end_date');
            $searchbybranch = Input::get('searchbybranch');
            $searchbyrating = Input::get('searchbyrating');
            
            $search_key = Input::get('search_key');
            $job_position = Input::get('job_position');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordjob = Input::get('sortordjob');
            $sortordcode = Input::get('sortordcode');
            
            $sortordcategory = Input::get('sortordcategory');
            $sortordpoint = Input::get('sortordpoint');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordrating = Input::get('sortordrating');
            
            $sortOrdDefault='';
            if($sortordbranch=='' && $sortorddate=='' && $sortordpoint=='' && $sortordcategory=='' && $sortordrating=='' ){
                $sortOrdDefault='ASC';
            }
            
            if ($startfrom != '') {
                $startfrom = explode('-', $startfrom);
                $startfrom = $startfrom[2] . '-' . $startfrom[1] . '-' . $startfrom[0];
            }
            if ($endfrom != '') {
                $endfrom = explode('-', $endfrom);
                $endfrom = $endfrom[2] . '-' . $endfrom[1] . '-' . $endfrom[0];
            }
                
            $checklistentries = DB::table('checklist_entry')
                    ->select('checklist_entry.*','list.checkpoint as checkpoint','category.name as maincategory','branch.name as br_name','branch.branch_code as br_code','employees.username as username','employees.first_name as first_name','employees.alias_name as alias_name','jobpos.name as job_position')
                    ->leftjoin('check_list as list', 'checklist_entry.checkpoint_id', '=', 'list.id')
                    ->leftjoin('master_resources as category', 'list.category_id', '=', 'category.id')
                    ->leftjoin('master_resources as branch', 'checklist_entry.branch_id', '=', 'branch.id')
                    ->leftjoin('employees', 'checklist_entry.employee_id', '=', 'employees.id')
                    ->leftjoin('master_resources as jobpos', 'employees.job_position', '=', 'jobpos.id')
                    ->where('checklist_entry.status', '=', 1)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
                    })
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(employees.username like '$searchbycode%')");
                    })
                    ->when($searchbycategory, function ($query) use ($searchbycategory) {
                        return $query->whereRaw("(list.category_id=$searchbycategory)");
                    })
                    ->when($searchbypoint, function ($query) use ($searchbypoint) {
                        return $query->whereRaw("(list.checkpoint like '$searchbypoint%')");
                    })
                    ->when($startfrom, function ($query) use ($startfrom) {
                        return $query->whereRaw("date(checklist_entry.entry_date) >= '$startfrom' ");
                    })
                    ->when($endfrom, function ($query) use ($endfrom) {
                        return $query->whereRaw("date(checklist_entry.entry_date)<= '$endfrom' ");
                    })
                    ->when($searchbybranch, function ($query) use ($searchbybranch) {
                        return $query->whereRaw("checklist_entry.branch_id=$searchbybranch");
                    })
                    ->when($searchbyrating, function ($query) use ($searchbyrating) {
                        return $query->whereRaw("checklist_entry.rating='$searchbyrating'");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('employees.first_name', $sortordname);
                    })
                    ->when($sortordjob, function ($query) use ($sortordjob) {
                        return $query->orderby('jobpos.name', $sortordjob);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('employees.username', $sortordcode);
                    })
                    ->when($sortordcategory, function ($query) use ($sortordcategory) {
                        return $query->orderby('category.name', $sortordcategory);
                    })
                    ->when($sortordpoint, function ($query) use ($sortordpoint) {
                        return $query->orderby('list.checkpoint', $sortordpoint);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('checklist_entry.entry_date', $sortorddate);
                    })
                    
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordrating, function ($query) use ($sortordrating) {
                        return $query->orderby('checklist_entry.rating', $sortordrating);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('category.name', $sortOrdDefault)->orderby('checklist_entry.entry_date', 'DESC');
                        
                    })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('CheckListreport', function($excel) use($checklistentries){
                 // Set the title
                $excel->setTitle('Check List Report');
                
                $excel->sheet('Check List Report', function($sheet) use($checklistentries){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Check List Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:H3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array("Employee Code",'Employee Name',"Job Position",'Category Name','Check Point','Date','Branch','Rating'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:H5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($checklistentries);$i++){
                        $employee_name = $checklistentries[$i]->first_name." ".$checklistentries[$i]->alias_name;
                        $date=date("d-m-Y", strtotime($checklistentries[$i]->entry_date));
                        
                        $sheet->setCellValue('A'.$chrRow, $checklistentries[$i]->username);
                        $sheet->setCellValue('B'.$chrRow, $employee_name);
                        $sheet->setCellValue('C'.$chrRow, str_replace('_',' ',$checklistentries[$i]->job_position));
                        $sheet->setCellValue('D'.$chrRow, $checklistentries[$i]->maincategory);
                        $sheet->setCellValue('E'.$chrRow, $checklistentries[$i]->checkpoint);
                        $sheet->setCellValue('F'.$chrRow, $date);
                        $sheet->setCellValue('G'.$chrRow, $checklistentries[$i]->br_code.":".$checklistentries[$i]->br_name);
                        $sheet->setCellValue('H'.$chrRow, $checklistentries[$i]->rating);
                            
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
                <div style="text-align:center;"><h1>Check List Report</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 5px;color:#fff;"> Employee Code </td>
                             <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Job Position</td>
                                <td style="padding:10px 5px;color:#fff;"> Category Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Check Point </td>
                                <td style="padding:10px 5px;color:#fff;"> Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Branch </td>
                                <td style="padding:10px 5px;color:#fff;"> Rating </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($checklistentries as $cat) {
                $date=date("d-m-Y", strtotime($cat->entry_date));
                $employee_name = $cat->first_name." ".$cat->alias_name;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . str_replace('_',' ',$cat->job_position) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->maincategory . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->checkpoint . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $date . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->br_code.":".$cat->br_name. '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->rating. '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_check_list_report.pdf');
        }
    }
}
