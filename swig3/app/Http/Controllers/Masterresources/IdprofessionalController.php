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

class IdprofessionalController extends Controller {

    public function index(Request $request) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $paginate = Config::get('app.PAGINATE');
        $idprof = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'ID_PROFESSION')
                ->where('status', '!=', 2)->where('company_id', '=', $company_id)
                ->orderby('master_resources.name','ASC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $sortordname = Input::get('sortordname');
           
            
            $sortOrdDefault='';
            if($sortordname==''){
                $sortOrdDefault='ASC';
            }
            
            $idprof = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'ID_PROFESSION')
                    ->where('status', '!=', 2)->where('company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                   ->paginate($paginate);

            return view('masterresources/id_professionals/idprofessional_result', array('idprof' => $idprof));
        }
        
        return view('masterresources/id_professionals/index', array('idprof' => $idprof));
    }

    public function add() {
        return view('masterresources/id_professionals/add');
    }

    public function checkidprof() {

        $name = Input::get('job_code');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.job_code')
                ->where('job_code', '=', $name)
                ->where('resource_type', '=', 'ID_PROFESSION')
               // ->where('status', '!=', 2)
                ->where('id', '!=', $id)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }
    
    public function checkname() {

        $name = Input::get('name');
        
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.job_code')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'ID_PROFESSION')
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
      //  $masterresourcemodel->id_professional_code = Input::get('job_code');
        $masterresourcemodel->name = $position_name;
        $masterresourcemodel->alias_name = Input::get('alias_name');
        $masterresourcemodel->resource_type = 'ID_PROFESSION';
        $masterresourcemodel->company_id = $company_id;
        $masterresourcemodel->status = 1;
        $masterresourcemodel->save();
        Toastr::success('ID Professional Successfully Added!', $title = null, $options = []);
        return Redirect::to('masterresources/id_professionals');
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $idprof = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();
       
        return view('masterresources/id_professionals/edit', array('idprof' => $idprof));
    }

    public function update() {
        try {
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
           // $job_code = Input::get('job_code');
            $alias_name = Input::get('alias_name');
            $position_name = Input::get('name');
            $position_name = str_replace(' ', '_', $position_name);
            $spots = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $position_name, 
                              'alias_name' => $alias_name]);
            Toastr::success('Id Profession Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals/edit/' . $dn);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('ID Professional Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('ID Professional Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('ID Professional Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/id_professionals');
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
        

        $sortOrdDefault='';
        if($sortordname==''){
            $sortOrdDefault='ASC';
        }

        $jposs = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'ID_PROFESSION')
                ->where('status', '!=', 2)->where('company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
               ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
               
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('IDProfessionals', function($excel) use($jposs){
                 // Set the title
                $excel->setTitle('ID Professionals');
                
                $excel->sheet('ID Professionals', function($sheet) use($jposs){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'ID Professionals');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'ID Profession','Alias'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:C5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($jposs);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, str_replace('_',' ',$jposs[$i]->name));
                       $sheet->setCellValue('C'.$chrRow, $jposs[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':C'.$chrRow, function($cells) {
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
                <title>Morrocon Taste</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>ID Professionals</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Id Professional</td>
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
            return $pdf->download('mtg_id_profession_list.pdf');
        }
    }

    
}
