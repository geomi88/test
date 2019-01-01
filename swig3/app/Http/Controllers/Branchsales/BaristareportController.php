<?php

namespace App\Http\Controllers\Branchsales;

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

class BaristareportController extends Controller {

    public function morning(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();
        
        $allregions = DB::table('master_resources')
                ->select('master_resources.id as region_id','master_resources.name as region_name')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='REGION'")
                ->get();
        
        $baristas = DB::table('resource_allocation')
                ->select('branch.name as br_name','branch.branch_code as br_code',
                        'region.name as region_name','emp.first_name as first_name',
                        'emp.alias_name as alias_name','emp.username as emp_code')
                ->leftjoin('employees as emp', 'resource_allocation.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("resource_allocation.active=1 AND resource_allocation.resource_type='BARISTA'")
                ->whereRaw("shift.name='Morning Shift'")
                ->orderby('emp.first_name', 'ASC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbycode = Input::get('searchbycode');
            $searchbybranch = Input::get('searchbybranch');
            $searchbyregion = Input::get('searchbyregion');
            
            $sortordcode = Input::get('sortordcode');
            $sortordname = Input::get('sortordname');
            $sortordbranch = Input::get('sortordbranch');
            $sortordregion = Input::get('sortordregion');
            
            $sortOrdDefault='';
            if($sortordbranch=='' && $sortordname=='' && $sortordcode=='' && $sortordregion==''){
                $sortOrdDefault='ASC';
            }
            
            $baristas = DB::table('resource_allocation')
                    ->select('branch.name as br_name','branch.branch_code as br_code',
                            'region.name as region_name','emp.first_name as first_name',
                            'emp.alias_name as alias_name','emp.username as emp_code')
                    ->leftjoin('employees as emp', 'resource_allocation.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                    ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereRaw("resource_allocation.active=1 AND resource_allocation.resource_type='BARISTA'")
                    ->whereRaw("shift.name='Morning Shift'")
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("emp.username like '$searchbycode%'");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("emp.first_name like '$searchbyname%'");
                    })
                    ->when($searchbyregion, function ($query) use ($searchbyregion) {
                        return $query->whereRaw("area.region_id=$searchbyregion");
                    })
                    ->when($searchbybranch, function ($query) use ($searchbybranch) {
                        return $query->whereRaw("resource_allocation.branch_id=$searchbybranch");
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordregion, function ($query) use ($sortordregion) {
                        return $query->orderby('region.name', $sortordregion);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('emp.first_name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/barista_reports/morning_result', array('baristas'=>$baristas));
        }

        return view('branchsales/barista_reports/morning', array('allregions' => $allregions,'allbranches'=>$allbranches,'baristas'=>$baristas));
    }

  

        // Generate PDF funcion
    public function exportdatamorning() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbycode = Input::get('searchbycode');
        $searchbybranch = Input::get('searchbybranch');
        $searchbyregion = Input::get('searchbyregion');

        $sortordcode = Input::get('sortordcode');
        $sortordname = Input::get('sortordname');
        $sortordbranch = Input::get('sortordbranch');
        $sortordregion = Input::get('sortordregion');

        $sortOrdDefault='';
        if($sortordbranch=='' && $sortordname=='' && $sortordcode=='' && $sortordregion==''){
            $sortOrdDefault='ASC';
        }
            
        $baristas = DB::table('resource_allocation')
                ->select('branch.name as br_name','branch.branch_code as br_code',
                        'region.name as region_name','emp.first_name as first_name',
                        'emp.alias_name as alias_name','emp.username as emp_code')
                ->leftjoin('employees as emp', 'resource_allocation.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("resource_allocation.active=1 AND resource_allocation.resource_type='BARISTA'")
                ->whereRaw("shift.name='Morning Shift'")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("emp.username like '$searchbycode%'");
                })
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("emp.first_name like '$searchbyname%'");
                })
                ->when($searchbyregion, function ($query) use ($searchbyregion) {
                    return $query->whereRaw("area.region_id=$searchbyregion");
                })
                ->when($searchbybranch, function ($query) use ($searchbybranch) {
                    return $query->whereRaw("resource_allocation.branch_id=$searchbybranch");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordregion, function ($query) use ($sortordregion) {
                    return $query->orderby('region.name', $sortordregion);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('emp.first_name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('BaristaInDutyMorningShift', function($excel) use($baristas){
                 // Set the title
                $excel->setTitle('Barista In Duty Morning Shift');
                
                $excel->sheet('Barista In Duty Morning Shift', function($sheet) use($baristas){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Barista In Duty Morning Shift');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Employee Code','Employee Name','Branch','Region'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($baristas);$i++){
                        $sheet->setCellValue('A'.$chrRow, $baristas[$i]->emp_code);
                        $sheet->setCellValue('B'.$chrRow, $baristas[$i]->first_name.' '.$baristas[$i]->alias_name);
                        $sheet->setCellValue('C'.$chrRow, $baristas[$i]->br_code.' : '.$baristas[$i]->br_name);
                        $sheet->setCellValue('D'.$chrRow, $baristas[$i]->region_name);
                        $sheet->cells('A'.$chrRow.':D'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Barista In Duty Morning Shift</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 5px;color:#fff;"> Employee code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Branch </td>
                                <td style="padding:10px 5px;color:#fff;"> Region </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($baristas as $cat) {
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->emp_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->first_name.' '.$cat->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->br_code.' '.$cat->br_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->region_name. '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_barista_morining.pdf');
        }
    }
    
    public function evening(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();
        
        $allregions = DB::table('master_resources')
                ->select('master_resources.id as region_id','master_resources.name as region_name')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='REGION'")
                ->get();
        
        $baristas = DB::table('resource_allocation')
                ->select('branch.name as br_name','branch.branch_code as br_code',
                        'region.name as region_name','emp.first_name as first_name',
                        'emp.alias_name as alias_name','emp.username as emp_code')
                ->leftjoin('employees as emp', 'resource_allocation.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("resource_allocation.active=1 AND resource_allocation.resource_type='BARISTA'")
                ->whereRaw("shift.name='Evening Shift'")
                ->orderby('emp.first_name', 'ASC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbycode = Input::get('searchbycode');
            $searchbybranch = Input::get('searchbybranch');
            $searchbyregion = Input::get('searchbyregion');
            
            $sortordcode = Input::get('sortordcode');
            $sortordname = Input::get('sortordname');
            $sortordbranch = Input::get('sortordbranch');
            $sortordregion = Input::get('sortordregion');
            
            $sortOrdDefault='';
            if($sortordbranch=='' && $sortordname=='' && $sortordcode=='' && $sortordregion==''){
                $sortOrdDefault='ASC';
            }
            
            $baristas = DB::table('resource_allocation')
                    ->select('branch.name as br_name','branch.branch_code as br_code',
                            'region.name as region_name','emp.first_name as first_name',
                            'emp.alias_name as alias_name','emp.username as emp_code')
                    ->leftjoin('employees as emp', 'resource_allocation.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                    ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereRaw("resource_allocation.active=1 AND resource_allocation.resource_type='BARISTA'")
                    ->whereRaw("shift.name='Evening Shift'")
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("emp.username like '$searchbycode%'");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("emp.first_name like '$searchbyname%'");
                    })
                    ->when($searchbyregion, function ($query) use ($searchbyregion) {
                        return $query->whereRaw("area.region_id=$searchbyregion");
                    })
                    ->when($searchbybranch, function ($query) use ($searchbybranch) {
                        return $query->whereRaw("resource_allocation.branch_id=$searchbybranch");
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordregion, function ($query) use ($sortordregion) {
                        return $query->orderby('region.name', $sortordregion);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('emp.first_name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/barista_reports/evening_result', array('baristas'=>$baristas));
        }

        return view('branchsales/barista_reports/evening', array('allregions' => $allregions,'allbranches'=>$allbranches,'baristas'=>$baristas));
    }

  

        // Generate PDF funcion
    public function exportdataevening() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbycode = Input::get('searchbycode');
        $searchbybranch = Input::get('searchbybranch');
        $searchbyregion = Input::get('searchbyregion');

        $sortordcode = Input::get('sortordcode');
        $sortordname = Input::get('sortordname');
        $sortordbranch = Input::get('sortordbranch');
        $sortordregion = Input::get('sortordregion');

        $sortOrdDefault='';
        if($sortordbranch=='' && $sortordname=='' && $sortordcode=='' && $sortordregion==''){
            $sortOrdDefault='ASC';
        }
            
        $baristas = DB::table('resource_allocation')
                ->select('branch.name as br_name','branch.branch_code as br_code',
                        'region.name as region_name','emp.first_name as first_name',
                        'emp.alias_name as alias_name','emp.username as emp_code')
                ->leftjoin('employees as emp', 'resource_allocation.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("resource_allocation.active=1 AND resource_allocation.resource_type='BARISTA'")
                ->whereRaw("shift.name='Evening Shift'")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("emp.username like '$searchbycode%'");
                })
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("emp.first_name like '$searchbyname%'");
                })
                ->when($searchbyregion, function ($query) use ($searchbyregion) {
                    return $query->whereRaw("area.region_id=$searchbyregion");
                })
                ->when($searchbybranch, function ($query) use ($searchbybranch) {
                    return $query->whereRaw("resource_allocation.branch_id=$searchbybranch");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordregion, function ($query) use ($sortordregion) {
                    return $query->orderby('region.name', $sortordregion);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('emp.first_name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('BaristaInDutyEveningShift', function($excel) use($baristas){
                 // Set the title
                $excel->setTitle('Barista In Duty Evening Shift');
                
                $excel->sheet('Barista In Duty Evening Shift', function($sheet) use($baristas){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Barista In Duty Evening Shift');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Employee Code','Employee Name','Branch','Region'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($baristas);$i++){
                        $sheet->setCellValue('A'.$chrRow, $baristas[$i]->emp_code);
                        $sheet->setCellValue('B'.$chrRow, $baristas[$i]->first_name.' '.$baristas[$i]->alias_name);
                        $sheet->setCellValue('C'.$chrRow, $baristas[$i]->br_code.' : '.$baristas[$i]->br_name);
                        $sheet->setCellValue('D'.$chrRow, $baristas[$i]->region_name);
                        $sheet->cells('A'.$chrRow.':D'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Barista In Duty Evening Shift</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 5px;color:#fff;"> Employee code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Branch </td>
                                <td style="padding:10px 5px;color:#fff;"> Region </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($baristas as $cat) {
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->emp_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->first_name.' '.$cat->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->br_code.' '.$cat->br_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->region_name. '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_barista_morining.pdf');
        }
    }
}