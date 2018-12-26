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

class WarningsController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        $allbranches = DB::table('master_resources')
            ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
            ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
            ->get();
        
         $warning_types = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'WARNING_TYPE', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
         
        $warnings = DB::table('warnings')
                ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code','employees.first_name as first_name','employees.alias_name as alias_name','type.name as warning_name')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
                ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
                ->where('warnings.status', '=', 1)
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
            $searchbytype = Input::get('searchbytype');
            $sortordtitle = Input::get('sortordtitle');
            $sortordbranch = Input::get('sortordbranch');
            $sortordemployee = Input::get('sortordemployee');
            $sortordtype = Input::get('sortordtype');
            
            $sortOrdDefault='';
            if($sortordtitle=='' && $sortordbranch=='' && $sortordemployee=='' && $sortordtype==''){
                $sortOrdDefault='DESC';
            }

            $warnings = DB::table('warnings')
                ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code','employees.first_name as first_name','employees.alias_name as alias_name','type.name as warning_name')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
                ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
                ->where('warnings.status', '=', 1)
                ->when($searchbytitle, function ($query) use ($searchbytitle) {
                    return $query->whereRaw("warnings.title like '$searchbytitle%'");
                })
                ->when($searchbyemployee, function ($query) use ($searchbyemployee) {
                    return $query->whereRaw("employees.first_name like '$searchbyemployee%'");
                })
                ->when($searchbytype, function ($query) use ($searchbytype) {
                    return $query->whereRaw("warnings.warning_type=$searchbytype");
                })
                ->when($searchbybranch, function ($query) use ($searchbybranch) {
                    return $query->whereRaw("warnings.branch_id=$searchbybranch");
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
                ->when($sortordtype, function ($query) use ($sortordtype) {
                    return $query->orderby('type.name', $sortordtype);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('warnings.created_at', $sortOrdDefault);
                })
                ->paginate($paginate);

            return view('checklist/warnings/result', array('warnings' => $warnings));
        }

        return view('checklist/warnings/index', array('warnings' => $warnings,'allbranches'=>$allbranches,'warning_types'=>$warning_types));
    }
    
    public function editindex(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        $allbranches = DB::table('master_resources')
            ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
            ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
            ->get();
        
         $warning_types = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'WARNING_TYPE', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
         
        $warnings = DB::table('warnings')
                ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code','employees.first_name as first_name','employees.alias_name as alias_name','type.name as warning_name')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
                ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
                ->where('warnings.status', '=', 1)
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
            $searchbytype = Input::get('searchbytype');
            $sortordtitle = Input::get('sortordtitle');
            $sortordbranch = Input::get('sortordbranch');
            $sortordemployee = Input::get('sortordemployee');
            $sortordtype = Input::get('sortordtype');
            
            $sortOrdDefault='';
            if($sortordtitle=='' && $sortordbranch=='' && $sortordemployee=='' && $sortordtype==''){
                $sortOrdDefault='DESC';
            }

            $warnings = DB::table('warnings')
                ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code','employees.first_name as first_name','employees.alias_name as alias_name','type.name as warning_name')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
                ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
                ->where('warnings.status', '=', 1)
                ->when($searchbytitle, function ($query) use ($searchbytitle) {
                    return $query->whereRaw("warnings.title like '$searchbytitle%'");
                })
                ->when($searchbyemployee, function ($query) use ($searchbyemployee) {
                    return $query->whereRaw("employees.first_name like '$searchbyemployee%'");
                })
                ->when($searchbytype, function ($query) use ($searchbytype) {
                    return $query->whereRaw("warnings.warning_type=$searchbytype");
                })
                ->when($searchbybranch, function ($query) use ($searchbybranch) {
                    return $query->whereRaw("warnings.branch_id=$searchbybranch");
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
                ->when($sortordtype, function ($query) use ($sortordtype) {
                    return $query->orderby('type.name', $sortordtype);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('warnings.created_at', $sortOrdDefault);
                })
                ->paginate($paginate);

            return view('checklist/edit_warning/result', array('warnings' => $warnings));
        }

        return view('checklist/edit_warning/index', array('warnings' => $warnings,'allbranches'=>$allbranches,'warning_types'=>$warning_types));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $allbranches = DB::table('master_resources')
            ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
            ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
            ->get();
        
        $warning_types = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'WARNING_TYPE', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        return view('checklist/warnings/add', array('allbranches'=>$allbranches,'warning_types'=>$warning_types));
    }

    public function store() {
        try {
            if (Session::get('login_id')) {
                $login_id=Session::get('login_id');
            }
            $model = new Warnings;
            $model->branch_id = Input::get('cmbbranch');
            $model->employee_id = Input::get('cmbemployee');
            $model->warning_type = Input::get('cmbwarning');
            $model->submitted_by = $login_id;
            $model->title = Input::get('title');
            $model->description = Input::get('description');
            $model->status = 1;
            $model->save();
            
            Toastr::success('Warning Successfully Added!', $title = null, $options = []);
            return Redirect::to('checklist/warnings');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('checklist/warnings');
        }
    }

    public function getbranchemployees() {
        $branch_id = Input::get('branch_id');
        if($branch_id!=''){
            $employees = DB::table('resource_allocation')
            ->select('resource_allocation.resource_type as type','resource_allocation.employee_id as emp_id', 'employees.first_name as first_name')
                ->leftjoin('employees', 'resource_allocation.employee_id', '=', 'employees.id')
                ->whereRaw("resource_allocation.branch_id=$branch_id and active=1 AND resource_allocation.resource_type IN('CASHIER','BARISTA','SUPERVISOR')")
                ->get();

            echo '<option value="">Select Employee</option>';
            foreach ($employees as $employee) {
                echo '<option value="' . $employee->emp_id . '">' . $employee->type.' :: '.$employee->first_name . '</option>';
            }
        }else{
            echo '<option value="">Select Employee</option>';
        }
        
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
       
        $allbranches = DB::table('master_resources')
                    ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
                    ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                    ->get();
        
        $warnings_data = DB::table('warnings')
                ->select('warnings.*')
                ->where(['warnings.id' => $dn])
                ->first();
        
        $warning_types = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'WARNING_TYPE', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        $branch_id=$warnings_data->branch_id;
        $employees = DB::table('resource_allocation')
            ->select('resource_allocation.resource_type as type','resource_allocation.employee_id as emp_id', 'employees.first_name as first_name')
                ->leftjoin('employees', 'resource_allocation.employee_id', '=', 'employees.id')
                ->whereRaw("resource_allocation.branch_id=$branch_id and active=1 AND resource_allocation.resource_type IN('CASHIER','BARISTA','SUPERVISOR')")
                ->get();

        return view('checklist/edit_warning/edit', array('warnings_data' => $warnings_data,'allbranches'=>$allbranches,'employees'=>$employees,'warning_types'=>$warning_types));
    }

    public function update() {
        try {
            
            if (Session::get('login_id')) {
                $login_id=Session::get('login_id');
            }
            
            $model = new Warnings;
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            
            $warnings = DB::table('warnings')
                    ->where(['id' => $id])
                    ->update(['branch_id' => Input::get('cmbbranch'),
                            'employee_id' =>Input::get('cmbemployee'),
                            'submitted_by' =>$login_id,
                            'warning_type' =>Input::get('cmbwarning'),
                            'title' =>Input::get('title'),
                            'description' =>Input::get('description'),
                            ]);

            Toastr::success('Warning Successfully Updated', $title = null, $options = []);
            return Redirect::to('checklist/editwarnings');
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('checklist/warnings/edit/' . $dn);
        }
    }

   

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('warnings')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Warning Successfully Deleted', $title = null, $options = []);
            return Redirect::to('checklist/editwarnings');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('checklist/editwarnings');
        }
    }

        // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
            $searchbytitle = Input::get('searchbytitle');
            $searchbybranch = Input::get('searchbybranch');
            $searchbyemployee = Input::get('searchbyemployee');
            $searchbytype = Input::get('searchbytype');
            $sortordtitle = Input::get('sortordtitle');
            $sortordbranch = Input::get('sortordbranch');
            $sortordemployee = Input::get('sortordemployee');
            $sortordtype = Input::get('sortordtype');
            
            $sortOrdDefault='';
            if($sortordtitle=='' && $sortordbranch=='' && $sortordemployee=='' && $sortordtype==''){
                $sortOrdDefault='DESC';
            }

            $warnings = DB::table('warnings')
                ->select('warnings.*','branch.name as br_name','branch.branch_code as br_code','employees.first_name as first_name','employees.alias_name as alias_name','type.name as warning_name')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as type', 'warnings.warning_type', '=', 'type.id')
                ->leftjoin('employees', 'warnings.employee_id', '=', 'employees.id')
                ->where('warnings.status', '=', 1)
                ->when($searchbytitle, function ($query) use ($searchbytitle) {
                    return $query->whereRaw("warnings.title like '$searchbytitle%'");
                })
                ->when($searchbyemployee, function ($query) use ($searchbyemployee) {
                    return $query->whereRaw("employees.first_name like '$searchbyemployee%'");
                })
                ->when($searchbytype, function ($query) use ($searchbytype) {
                    return $query->whereRaw("warnings.warning_type=$searchbytype");
                })
                ->when($searchbybranch, function ($query) use ($searchbybranch) {
                    return $query->whereRaw("warnings.branch_id=$searchbybranch");
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
                    
                    $sheet->setCellValue('B3', 'Warnings');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Title', 'Employee','Warning Type','Branch Name','Description',));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($warnings);$i++){
                        $sheet->setCellValue('A'.$chrRow, $warnings[$i]->title);
                        $sheet->setCellValue('B'.$chrRow, $warnings[$i]->first_name);
                        $sheet->setCellValue('C'.$chrRow, $warnings[$i]->warning_name);
                        $sheet->setCellValue('D'.$chrRow, $warnings[$i]->br_code.' : '.$warnings[$i]->br_name);
                        $sheet->setCellValue('E'.$chrRow, $warnings[$i]->description);
                            
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
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h1>Warnings</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Title </td>
                                <td style="padding:10px 5px;color:#fff;"> Employee </td>
                                <td style="padding:10px 5px;color:#fff;"> Warning Type </td>
                                <td style="padding:10px 5px;color:#fff;"> Branch Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Description</td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($warnings as $cat) {
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->first_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->warning_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->br_code." : ".$cat->br_name . '</td>
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
            return $pdf->download('mtg_warnings.pdf');
        }
    }
}
