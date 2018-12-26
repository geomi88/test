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
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Company;
use DB;
use App;
use PDF;
use Excel;

class BankController extends Controller {

    public function index(Request $request) {
        Session::set('current_url', 'master/bank');
        $paginate = Config::get('app.PAGINATE');
        $companyid = session('company');

        $companies = Company::all();
        $banks = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'BANK')
                ->where('status', '!=', 2)
                ->where('company_id', '=', $companyid)
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
            
            $banks = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'BANK')
                    ->where('status', '!=', 2)
                    ->where('company_id', '=', $companyid)
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

            return view('masterresources/bank/bank_result', array('banks' => $banks));
        }
        
        return view('masterresources/bank/index', array('companies' => $companies, 'banks' => $banks));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $companies = Company::all();
        $banks = DB::table('master_resources')
                        ->join('companies', 'master_resources.company_id', '=', 'companies.id')
                        ->select('master_resources.*', 'companies.name as company_name')
                        ->where(['resource_type' => 'BANK'])->where('master_resources.status', '=', 1)->get();


        return view('masterresources/bank/add', array('companies' => $companies, 'banks' => $banks));
    }

    public function store() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $masterresourcemodel = new Masterresources();
        $masterresourcemodel->resource_type = 'BANK';
        $masterresourcemodel->name = Input::get('name');
        $masterresourcemodel->alias_name = Input::get('alias_name');
        $masterresourcemodel->company_id = $company_id;
        if (Input::get('region') == '') {
            $masterresourcemodel->region_id = NULL;
        }

        $masterresourcemodel->status = 1;
        $masterresourcemodel->save();
        Toastr::success('Bank Successfully Created!', $title = null, $options = []);
        return Redirect::to('masterresources/bank');
    }

    public function checkbankname() {

        $name = Input::get('name');
        $bank_id = Input::get('bank_id');
        $data = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('name', '=', $name)->where('resource_type', '=', 'BANK')->where('id', '!=', $bank_id)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $dn = \Crypt::decrypt($id);
        $companies = Company::all();
        $bank = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();
        return view('masterresources/bank/edit', array('bank' => $bank, 'company_id' => $company_id));
    }

    public function update() {

        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $bank_id = Input::get('bank_id');
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');

            $banks = DB::table('master_resources')
                    ->where(['id' => $bank_id])
                    ->update(['name' => $name, 'alias_name' => $alias_name]);
            Toastr::success('Bank Successfully Updated!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank/edit/' . $bank_id);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Bank Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Bank Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Bank Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        }
    }

       // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $companyid = Session::get('company');  
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $sortordname = Input::get('sortordname');
        if($sortordname==''){
            $sortordname=1;
        }

        $banks = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'BANK')
                ->where('status', '!=', 2)
                ->where('company_id', '=', $companyid)
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
            
            Excel::create('BankList', function($excel) use($banks){
                 // Set the title
                $excel->setTitle('Bank List');
                
                $excel->sheet('Banks List', function($sheet) use($banks){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Banks List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:C3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Bank Name',"Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:C5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($banks);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $banks[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $banks[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':C'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Banks List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Bank Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="regionbody" id="regionbody" >';
            $slno=0;
            foreach ($banks as $bank) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $bank->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $bank->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_bank_list.pdf');
        }
    }
}
