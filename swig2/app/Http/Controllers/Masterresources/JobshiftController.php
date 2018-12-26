<?php

namespace App\Http\Controllers\Masterresources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Company;
use DB;
use App;
use PDF;
use Excel;

class JobshiftController extends Controller {

    public function index(Request $request) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $paginate = Config::get('app.PAGINATE');
        
        $shifts = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'JOB_SHIFT')
                ->where('status', '!=', 2)
                ->where('company_id', '=', $company_id)
                ->orderby('master_resources.name','ASC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $sortordname = Input::get('sortordname');
            if($sortordname==''){
                $sortordname=1;
            }
            
            $shifts = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'JOB_SHIFT')
                    ->where('status', '!=', 2)
                    ->where('company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        if($sortordname!=1){
                            return $query->orderby('master_resources.name', $sortordname);
                        }else{
                            return $query->orderby('master_resources.name', 'ASC');
                        }
                    })
                    ->paginate($paginate);

            return view('masterresources/job_shifts/shift_result', array('shifts' => $shifts));
        }
        
        return view('masterresources/job_shifts/index', array('shifts' => $shifts));
    }

    public function add() {
        return view('masterresources/job_shifts/add');
    }

    public function checkshifts() {

        $name = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'JOB_SHIFT')
                ->where('id', '!=', $id)
                ->where('status', '!=', 2)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
        
        $dn = \Crypt::decrypt($id);

        $jobshift = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();

        return view('masterresources/job_shifts/edit', array('jobshift' => $jobshift));
    }

    public function store() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $masterresourcemodel = new Masterresources;
        $masterresourcemodel->name = Input::get('name');
        $masterresourcemodel->alias_name = Input::get('alias_name');
        $masterresourcemodel->resource_type = 'JOB_SHIFT';
        $masterresourcemodel->company_id = $company_id;
        $masterresourcemodel->start_time = Input::get('tin');
        $masterresourcemodel->end_time = Input::get('tout');
        $masterresourcemodel->status = 1;
        $masterresourcemodel->save();
        Toastr::success('Shift Successfully Added!', $title = null, $options = []);
        return Redirect::to('masterresources/job_shifts');
    }

    public function update() {
        try {

            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $id = Input::get('cid');
            $dn=\Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');
            $company_id = $company_id;
            $start_time = Input::get('tin');
            $end_time = Input::get('tout');

            $shifts = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $name, 'alias_name' => $alias_name, 'start_time' => $start_time, 'end_time' => $end_time]);
            Toastr::success('Shift Successfully Updated!', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts/edit/'.$dn);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Job Shift Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Job Shift Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Job Shift Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_shifts');
        }
    }

        // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $sortordname = Input::get('sortordname');
        if($sortordname==''){
            $sortordname=1;
        }

        $shifts = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'JOB_SHIFT')
                ->where('status', '!=', 2)
                ->where('company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    if($sortordname!=1){
                        return $query->orderby('master_resources.name', $sortordname);
                    }else{
                        return $query->orderby('master_resources.name', 'ASC');
                    }
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('JobShiftsList', function($excel) use($shifts){
                 // Set the title
                $excel->setTitle('JobShifts List');
                
                $excel->sheet('Job Shifts List', function($sheet) use($shifts){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Job Shifts List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Shifts Name',"Alias",'Start Time','End Time'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($shifts);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $shifts[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $shifts[$i]->alias_name);
                        $sheet->setCellValue('D'.$chrRow, $shifts[$i]->start_time);
                        $sheet->setCellValue('E'.$chrRow, $shifts[$i]->end_time);
                            
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
                <div style="text-align:center;"><h1>Job Shifts List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Shift Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                                <td style="padding:10px 5px;color:#fff;"> Start Time </td>
                                <td style="padding:10px 5px;color:#fff;"> End Time </td>
                            </tr>
                        </thead>
                        <tbody class="shiftbody" id="shiftbody" >';
            $slno=0;
            foreach ($shifts as $shift) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $shift->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $shift->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $shift->start_time . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $shift->end_time . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_shift_list.pdf');
        }
    }
    
}
