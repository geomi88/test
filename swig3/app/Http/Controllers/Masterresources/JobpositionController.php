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

class JobpositionController extends Controller {

    public function index(Request $request) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $paginate = Config::get('app.PAGINATE');
        $jposs = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'JOB_POSITION')
                ->where('status', '!=', 2)->where('company_id', '=', $company_id)
                ->orderby('master_resources.name','ASC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordercode = Input::get('sortordercode');
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordercode==''){
                $sortOrdDefault='ASC';
            }
            
            $jposs = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'JOB_POSITION')
                    ->where('status', '!=', 2)->where('company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(master_resources.job_code like '$searchbycode%')");
                    })
                   
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortordercode, function ($query) use ($sortordercode) {
                        return $query->orderby('master_resources.job_code', $sortordercode);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/job_positions/jobpos_result', array('jposs' => $jposs));
        }
        
        return view('masterresources/job_positions/index', array('jposs' => $jposs));
    }

    public function add() {
        return view('masterresources/job_positions/add');
    }

    public function checkjobpos() {

        $name = Input::get('job_code');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.job_code')
                ->where('job_code', '=', $name)
                ->where('resource_type', '=', 'JOB_POSITION')
                ->where('status', '!=', 2)
                ->where('id', '!=', $id)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function store() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $position_name = Input::get('name');
        $position_name = str_replace(' ', '_', $position_name);
        $masterresourcemodel = new Masterresources;
        $masterresourcemodel->job_code = Input::get('job_code');
        $masterresourcemodel->name = $position_name;
        $masterresourcemodel->alias_name = Input::get('alias_name');
        $masterresourcemodel->resource_type = 'JOB_POSITION';
        $masterresourcemodel->company_id = $company_id;
        $masterresourcemodel->status = 1;
        $masterresourcemodel->save();
        Toastr::success('Job Position Successfully Added!', $title = null, $options = []);
        return Redirect::to('masterresources/job_positions');
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $jobpos = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();
       
        return view('masterresources/job_positions/edit', array('jobpos' => $jobpos));
    }

    public function update() {
        try {
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $job_code = Input::get('job_code');
            $alias_name = Input::get('alias_name');
            $position_name = Input::get('name');
            $position_name = str_replace(' ', '_', $position_name);
            $spots = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $position_name, 
                              'alias_name' => $alias_name, 
                              'job_code' => $job_code]);
            Toastr::success('Job Position Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions/edit/' . $dn);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Job Position Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Job Position Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Job Position Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/job_positions');
        }
    }

       // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordercode = Input::get('sortordercode');

        $sortOrdDefault='';
        if($sortordname=='' && $sortordercode==''){
            $sortOrdDefault='ASC';
        }

        $jposs = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'JOB_POSITION')
                ->where('status', '!=', 2)->where('company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(master_resources.job_code like '$searchbycode%')");
                })

                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortordercode, function ($query) use ($sortordercode) {
                    return $query->orderby('master_resources.job_code', $sortordercode);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('JobPositions', function($excel) use($jposs){
                 // Set the title
                $excel->setTitle('Job Positions');
                
                $excel->sheet('Job Positions', function($sheet) use($jposs){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Job Positions');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Job Position','Job Code','Alias'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($jposs);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, str_replace('_',' ',$jposs[$i]->name));
                        $sheet->setCellValue('C'.$chrRow, $jposs[$i]->job_code);
                        $sheet->setCellValue('D'.$chrRow, $jposs[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':D'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
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
                <div style="text-align:center;"><h1>Job Positions</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Job Position</td>
                                <td style="padding:10px 5px;color:#fff;"> Job Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="jobbody" id="jobbody" >';
            $slno=0;
            foreach ($jposs as $jpos) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . str_replace('_',' ',$jpos->name) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $jpos->job_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $jpos->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_job_pos_list.pdf');
        }
    }

    
}
