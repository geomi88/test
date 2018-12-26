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
use Illuminate\Support\Facades\Config;
use Illuminate\Encryption\EncryptionServiceProvider;
use App\Models\Company;
use DB;
use App;
use PDF;
use Excel;

class CompaniesController extends Controller {
    
    public function index(Request $request) 
        {
         $paginate=Config::get('app.PAGINATE');
       
        $companies = DB::table('companies')
                    ->where('status', '!=', 2)
                    ->orderby('companies.name','ASC')
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
            
            $companies = DB::table('companies')
                ->where('status', '!=', 2)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(companies.name like '$searchbyname%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    if($sortordname!=1){
                        return $query->orderby('companies.name', $sortordname);
                    }else{
                        return $query->orderby('companies.name', 'ASC');
                    }
                })
                ->paginate($paginate);

            return view('masterresources/companies/companies_result', array('companies' => $companies));
        }
        
            return view('masterresources/companies/index', array('title' => 'Company Listing','description' => '','page' => 'companies', 'companies' => $companies));
        }
    
        
    public function add() 
        {
            return view('masterresources/companies/add');
        }
        
        
    public function store() {
            try
            {
                $companymodel = new Company;
                $companymodel->name = Input::get('name');
                $companymodel->alias_name = Input::get('alias_name');
                $companymodel->status = 1;
                $companymodel->save();
                Toastr::success('Company Successfully Added!', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
            }
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/companies/add');
            }

    }
    
    
    public function checkcompanies() 
       {
        
        $name = Input::get('name');
        $id = Input::get('cid'); 
        $company_data = DB::table('companies')
                            ->select('companies.name')
                            ->where('name', '=', $name)
                            ->where('status', '!=', 2)
                            ->where('id', '!=', $id)
                            ->get();
        if(count($company_data)==0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));

        }
    public function edit($id)
        {
        
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('companies')
                                ->where(['id' => $dn])                            
                                ->get();
                return view('masterresources/companies/edit', array('companies' => $companies));
        }
       public function update()
        {
           try
           {
                $companymodel = new Company;
                $id = Input::get('cid');
                $dn=\Crypt::encrypt($id);
                $name = Input::get('name');
                $alias_name = Input::get('alias_name');
                $companies = DB::table('companies')
                                ->where(['id' => $id])                            
                                ->update(['name' => $name,'alias_name'=>$alias_name]);
                $companies = DB::table('companies');
                Toastr::success('Company Successfully Updated', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
            } 
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/companies/edit/'.$dn);
            
            }
        }
            
       public function Disable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('companies')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 0]);
                Toastr::success('Company Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
            
           }
        } 
       public function Enable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('companies')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 1]);
                Toastr::success('Company Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
            
           }
        }
    
    public function delete($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('companies')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 2]);
                Toastr::success('Company Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/companies');
            
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

        $companies = DB::table('companies')
            ->where('status', '!=', 2)
            ->when($searchbyname, function ($query) use ($searchbyname) {
                return $query->whereRaw("(companies.name like '$searchbyname%')");
            })
            ->when($sortordname, function ($query) use ($sortordname) {
                if($sortordname!=1){
                    return $query->orderby('companies.name', $sortordname);
                }else{
                    return $query->orderby('companies.name', 'ASC');
                }
            })
            ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('CompaniesList', function($excel) use($companies){
                 // Set the title
                $excel->setTitle('Companies List');
                
                $excel->sheet('Companies List', function($sheet) use($companies){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Companies List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:C3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Company Name',"Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:C5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($companies);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $companies[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $companies[$i]->alias_name);
                            
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
                <title>Project Name</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:Arial;">
                <section id="container">
                <div style="text-align:center;"><h1>Companies List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Company Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="regionbody" id="regionbody" >';
            $slno=0;
            foreach ($companies as $company) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $company->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $company->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_company_list.pdf');
        }
    }
}