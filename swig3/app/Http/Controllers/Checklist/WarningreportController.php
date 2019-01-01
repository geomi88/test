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
use App\Models\Warnings;
use DB;
use App;
use PDF;
use Excel;

class WarningreportController extends Controller {

     public function index(Request $request,$id='') {
        $paginate = Config::get('app.PAGINATE');
        
        $category_id='';
        if($id){
            $category_id = \Crypt::decrypt($id);
        }
        
        $allbranches = DB::table('master_resources')
            ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
            ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
            ->get();
                
        $job_positions = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        $warning_types = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'WARNING_TYPE', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
               
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $warnings = DB::table('warnings')
                ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code',
                        'employees.first_name as first_name','employees.alias_name as alias_name','employees.username as emp_code',
                        'jobpos.name as job_position','country.name as nationality','r.first_name as reportedby','type.name as warning_name')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('employees as r', 'warnings.submitted_by', '=', 'r.id')
                ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
                ->leftjoin('master_resources as jobpos', 'employees.job_position', '=', 'jobpos.id')
                ->leftjoin('country', 'employees.nationality', '=', 'country.id')
                ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
                ->where('warnings.status', '=', 1)
                ->when($category_id, function ($query) use ($category_id) {
                    return $query->where('warnings.warning_type', '=', $category_id);
                })
                ->orderby('warnings.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbytitle = Input::get('searchbytitle');
            $searchbybranch = Input::get('searchbybranch');
            $searchbyemployee = Input::get('searchbyemployee');
            $job_position = Input::get('job_position');
            $searchbycode = Input::get('searchbycode');
            $createdatfrom = Input::get('created_at_from');
            $createdatto = Input::get('created_at_to');
            $searchbytype = Input::get('searchbytype');
            $searchbycountry = Input::get('searchbycountry');
            $searchbyreportedby = Input::get('searchbyreportedby');
            
            $sortordtitle = Input::get('sortordtitle');
            $sortordbranch = Input::get('sortordbranch');
            $sortordemployee = Input::get('sortordemployee');
            $sortordjob = Input::get('sortordjob');
            $sortordcode = Input::get('sortordcode');
            $sortordercreated = Input::get('sortordercreated');
            $sortordercountry = Input::get('sortordercountry');
            $sortordtype = Input::get('sortordtype');
            
            if ($createdatfrom != '') {
                $createdatfrom = explode('-', $createdatfrom);
                $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
            }
            if ($createdatto != '') {
                $createdatto = explode('-', $createdatto);
                $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
            }
            
            $sortOrdDefault='';
            if($sortordtitle=='' && $sortordbranch=='' && $sortordemployee=='' && $sortordjob=='' && $sortordcode=='' && $sortordercreated=='' && $sortordercountry=='' && $sortordtype==''){
                $sortOrdDefault='DESC';
            }

            $warnings = DB::table('warnings')
                ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code',
                        'employees.first_name as first_name','employees.alias_name as alias_name','employees.username as emp_code',
                        'jobpos.name as job_position','country.name as nationality','r.first_name as reportedby','type.name as warning_name')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('employees as r', 'warnings.submitted_by', '=', 'r.id')
                ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
                ->leftjoin('master_resources as jobpos', 'employees.job_position', '=', 'jobpos.id')
                ->leftjoin('country', 'employees.nationality', '=', 'country.id')
                ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
                ->where('warnings.status', '=', 1)
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("employees.username like '$searchbycode%'");
                })
                ->when($searchbytitle, function ($query) use ($searchbytitle) {
                    return $query->whereRaw("warnings.title like '$searchbytitle%'");
                })
                ->when($searchbyemployee, function ($query) use ($searchbyemployee) {
                    return $query->whereRaw("employees.first_name like '$searchbyemployee%'");
                })
                ->when($searchbyreportedby, function ($query) use ($searchbyreportedby) {
                    return $query->whereRaw("r.first_name like '$searchbyreportedby%'");
                })
                ->when($job_position, function ($query) use ($job_position) {
                    return $query->where('employees.job_position', '=', $job_position);
                })
                ->when($searchbycountry, function ($query) use ($searchbycountry) {
                    return $query->where('employees.nationality', '=', $searchbycountry);
                })
                ->when($searchbytype, function ($query) use ($searchbytype) {
                    return $query->where('warnings.warning_type', '=', $searchbytype);
                })
                ->when($searchbybranch, function ($query) use ($searchbybranch) {
                    return $query->whereRaw("warnings.branch_id=$searchbybranch");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(warnings.created_at)>= '$createdatfrom' ");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('employees.username', $sortordcode);
                })
                ->when($sortordtitle, function ($query) use ($sortordtitle) {
                    return $query->orderby('warnings.title', $sortordtitle);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordemployee, function ($query) use ($sortordemployee) {
                    return $query->orderby('employees.first_name', $sortordemployee);
                })
                ->when($sortordjob, function ($query) use ($sortordjob) {
                    return $query->orderby('jobpos.name', $sortordjob);
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(warnings.created_at)<= '$createdatto' ");
                })
                ->when($sortordercreated, function ($query) use ($sortordercreated) {
                    return $query->orderby('warnings.created_at', $sortordercreated);
                })
                ->when($sortordercountry, function ($query) use ($sortordercountry) {
                    return $query->orderby('country.name', $sortordercountry);
                })
                ->when($sortordercountry, function ($query) use ($sortordercountry) {
                    return $query->orderby('country.name', $sortordercountry);
                })
                ->when($sortordtype, function ($query) use ($sortordtype) {
                    return $query->orderby('type.name', $sortordtype);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('warnings.created_at', $sortOrdDefault);
                })
                ->paginate($paginate);

            return view('checklist/warning_report/result', array('warnings' => $warnings));
        }

        return view('checklist/warning_report/index', array('warnings' => $warnings,'allbranches'=>$allbranches,'job_positions'=>$job_positions,'countries'=>$countries,'warning_types'=>$warning_types,'category_id'=>$category_id));
    }



        // Generate PDF funcion
    public function exportdata() {
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbytitle = Input::get('searchbytitle');
        $searchbybranch = Input::get('searchbybranch');
        $searchbyemployee = Input::get('searchbyemployee');
        $job_position = Input::get('job_position');
        $searchbycode = Input::get('searchbycode');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $searchbytype = Input::get('searchbytype');
        $searchbycountry = Input::get('searchbycountry');
        $searchbyreportedby = Input::get('searchbyreportedby');

        $sortordtitle = Input::get('sortordtitle');
        $sortordbranch = Input::get('sortordbranch');
        $sortordemployee = Input::get('sortordemployee');
        $sortordjob = Input::get('sortordjob');
        $sortordcode = Input::get('sortordcode');
        $sortordercreated = Input::get('sortordercreated');
        $sortordercountry = Input::get('sortordercountry');
        $sortordtype = Input::get('sortordtype');

        if ($createdatfrom != '') {
            $createdatfrom = explode('-', $createdatfrom);
            $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
        }
        if ($createdatto != '') {
            $createdatto = explode('-', $createdatto);
            $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
        }

        $sortOrdDefault='';
        if($sortordtitle=='' && $sortordbranch=='' && $sortordemployee=='' && $sortordjob=='' && $sortordcode=='' && $sortordercreated=='' && $sortordercountry=='' && $sortordtype==''){
            $sortOrdDefault='DESC';
        }

        $warnings = DB::table('warnings')
            ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code',
                    'employees.first_name as first_name','employees.alias_name as alias_name','employees.username as emp_code',
                    'jobpos.name as job_position','country.name as nationality','r.first_name as reportedby','type.name as warning_name')
            ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
            ->leftjoin('employees as r', 'warnings.submitted_by', '=', 'r.id')
            ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
            ->leftjoin('master_resources as jobpos', 'employees.job_position', '=', 'jobpos.id')
            ->leftjoin('country', 'employees.nationality', '=', 'country.id')
            ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
            ->where('warnings.status', '=', 1)
            ->when($searchbycode, function ($query) use ($searchbycode) {
                return $query->whereRaw("employees.username like '$searchbycode%'");
            })
            ->when($searchbytitle, function ($query) use ($searchbytitle) {
                return $query->whereRaw("warnings.title like '$searchbytitle%'");
            })
             ->when($searchbyreportedby, function ($query) use ($searchbyreportedby) {
                    return $query->whereRaw("r.first_name like '$searchbyreportedby%'");
                })
            ->when($searchbyemployee, function ($query) use ($searchbyemployee) {
                return $query->whereRaw("employees.first_name like '$searchbyemployee%'");
            })
            ->when($job_position, function ($query) use ($job_position) {
                return $query->where('employees.job_position', '=', $job_position);
            })
            ->when($searchbycountry, function ($query) use ($searchbycountry) {
                return $query->where('employees.nationality', '=', $searchbycountry);
            })
            ->when($searchbytype, function ($query) use ($searchbytype) {
                return $query->where('warnings.warning_type', '=', $searchbytype);
            })
            ->when($searchbybranch, function ($query) use ($searchbybranch) {
                return $query->whereRaw("warnings.branch_id=$searchbybranch");
            })
            ->when($createdatfrom, function ($query) use ($createdatfrom) {
                return $query->whereRaw("date(warnings.created_at)>= '$createdatfrom' ");
            })
            ->when($sortordcode, function ($query) use ($sortordcode) {
                return $query->orderby('employees.username', $sortordcode);
            })
            ->when($sortordtitle, function ($query) use ($sortordtitle) {
                return $query->orderby('warnings.title', $sortordtitle);
            })
            ->when($sortordbranch, function ($query) use ($sortordbranch) {
                return $query->orderby('branch.name', $sortordbranch);
            })
            ->when($sortordemployee, function ($query) use ($sortordemployee) {
                return $query->orderby('employees.first_name', $sortordemployee);
            })
            ->when($sortordjob, function ($query) use ($sortordjob) {
                return $query->orderby('jobpos.name', $sortordjob);
            })
            ->when($createdatto, function ($query) use ($createdatto) {
                return $query->whereRaw("date(warnings.created_at)<= '$createdatto' ");
            })
            ->when($sortordercreated, function ($query) use ($sortordercreated) {
                return $query->orderby('warnings.created_at', $sortordercreated);
            })
            ->when($sortordercountry, function ($query) use ($sortordercountry) {
                return $query->orderby('country.name', $sortordercountry);
            })
            ->when($sortordercountry, function ($query) use ($sortordercountry) {
                return $query->orderby('country.name', $sortordercountry);
            })
            ->when($sortordtype, function ($query) use ($sortordtype) {
                return $query->orderby('type.name', $sortordtype);
            })
            ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                return $query->orderby('warnings.created_at', $sortOrdDefault);
            })
            ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('Warnings', function($excel) use($warnings){
                 // Set the title
                $excel->setTitle('Warnings');
                
                $excel->sheet('Warnings', function($sheet) use($warnings){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('D3', 'Warnings');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:J3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Code', 'Employee Name','Nationality','Job Position','Warning Type','Title','Date','Branch','Reported By','Description'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:J5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($warnings);$i++){
                        $sheet->setCellValue('A'.$chrRow, $warnings[$i]->emp_code);
                        $sheet->setCellValue('B'.$chrRow, $warnings[$i]->first_name);
                        $sheet->setCellValue('C'.$chrRow, $warnings[$i]->nationality);
                        $sheet->setCellValue('D'.$chrRow, str_replace("_", " ", $warnings[$i]->job_position));
                        $sheet->setCellValue('E'.$chrRow, $warnings[$i]->warning_name);
                        $sheet->setCellValue('F'.$chrRow, $warnings[$i]->title);
                        $sheet->setCellValue('G'.$chrRow, date("d-m-Y", strtotime($warnings[$i]->created_at)));
                        $sheet->setCellValue('H'.$chrRow, $warnings[$i]->br_code.' : '.$warnings[$i]->br_name);
                        $sheet->setCellValue('I'.$chrRow, $warnings[$i]->reportedby);
                        $sheet->setCellValue('J'.$chrRow, $warnings[$i]->description);
                            
                        $sheet->cells('A'.$chrRow.':J'.$chrRow, function($cells) {
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
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h1>Warnings</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Code </td>
                                <td style="padding:10px 10px;color:#fff;"> Employee Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Nationality </td>
                                <td style="padding:10px 5px;color:#fff;"> Job Position </td>
                                <td style="padding:10px 5px;color:#fff;"> Warning Type </td>
                                <td style="padding:10px 5px;color:#fff;"> Title </td>
                                <td style="padding:10px 5px;color:#fff;"> Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Branch </td>
                                <td style="padding:10px 5px;color:#fff;"> Reported By </td>
                                <td style="padding:10px 5px;color:#fff;"> Description</td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($warnings as $cat) {
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->emp_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->first_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->nationality . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . str_replace("_", " ", $cat->job_position) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->warning_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($cat->created_at)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->br_code." : ".$cat->br_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->reportedby . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->description . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_warnings_report.pdf');
        }
    }
}
