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

class DivisionController extends Controller {

    public function index(Request $request) {
        $paginate=Config::get('app.PAGINATE');
        $company_id=session('company');

        $companies = Company::all();
        $divisions = DB::table('master_resources')
                    ->select('master_resources.*','dept.name as dept_name')
                    ->leftjoin('master_resources as dept', 'master_resources.department_id', '=', 'dept.id')
                    ->where('master_resources.resource_type', '=', 'DIVISION')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)              
                    ->orderby('master_resources.name','ASC')
                    ->paginate($paginate);

        $departments = DB::table('master_resources')
                    ->select('master_resources.name', 'master_resources.id')
                    ->where(['resource_type' => 'DEPARTMENT', 'company_id' => $company_id])
                    ->where('status', '!=', 2)
                    ->get();
      
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbydivision = Input::get('searchbydivision');
            $sortordname = Input::get('sortordname');
            $sortorderdivision = Input::get('sortorderdivision');
            $sortOrdDefault='';
            if($sortordname=='' && $sortorderdivision==''){
                $sortOrdDefault='ASC';
            }
            
            $divisions = DB::table('master_resources')
                    ->select('master_resources.*','dept.name as dept_name')
                    ->leftjoin('master_resources as dept', 'master_resources.department_id', '=', 'dept.id')
                    ->where('master_resources.resource_type', '=', 'DIVISION')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)      
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($searchbydivision, function ($query) use ($searchbydivision) {
                        return $query->whereRaw("(master_resources.department_id=$searchbydivision)");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortorderdivision, function ($query) use ($sortorderdivision) {
                        return $query->orderby('dept.name', $sortorderdivision);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/divisions/division_result', array('divisions' => $divisions));
        }
        
        return view('masterresources/divisions/index', array('departments' => $departments, 'divisions' => $divisions));
    }

    public function add()
    {  
        if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $companies = Company::all();
         
        $departments = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'DEPARTMENT', 'company_id' => $company_id])
                        ->where('status', '!=', 2)
                        ->get();
       
         return view('masterresources/divisions/add', array('companies' => $companies, 'departments' => $departments));
            
    }
    public function store() {
        
            if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $masterresourcemodel = new Masterresources();
        $masterresourcemodel->resource_type = 'DIVISION';
        $masterresourcemodel->name = Input::get('name');
        $masterresourcemodel->alias_name = Input::get('alias_name');
        $masterresourcemodel->company_id = $company_id;
        $masterresourcemodel->department_id = Input::get('department');
        
//        $masterresourcemodel->address = Input::get('address');
//        $masterresourcemodel->latitude = Input::get('latitude');
//        $masterresourcemodel->longitude = Input::get('longitude');
//        $masterresourcemodel->branch_code = Input::get('branch_code');
//        $masterresourcemodel->opening_fund = Input::get('fund');
        $masterresourcemodel->status = 1;
        $masterresourcemodel->save();
                Toastr::success('Division Successfully Created!', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
        }

     public function checkdivisionname() 
       {
        
        $department = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('name', '=', $department)
                ->where('resource_type', '=', 'DIVISION')
                ->where('id', '!=', $id)
                ->where('status', '!=', 2)
                ->get();
      //print_r(count($data));die('dsd');
        if(count($data)==0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));

       }
    
     public function edit($id)
    {
          if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
                $dn=\Crypt::decrypt($id);  
                $companies = Company::all();
                $departments = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'DEPARTMENT', 'company_id' => $company_id])
                        ->where('status', '!=', 2)
                        ->get();
                $division = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->first();      
           // print_r('<pre>');print_r($areas);die('df');
                return view('masterresources/divisions/edit', array('departments' => $departments , 'division'=>$division ,'company_id'=>$company_id));
           
    }
     
    public function update() {
         
        try
        {
            if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
            $id = Input::get('cid');
            $dn=\Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');
            $department_id = Input::get('department');
            $divisions = DB::table('master_resources')
                                ->where(['id' => $id])                            
                                ->update(['name' => $name,'alias_name'=>$alias_name,'department_id'=>$department_id]);
                 Toastr::success('Division Successfully Updated!', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
        }
     catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/divisions/edit/'.$dn);
            }
    }
                                  
       public function Disable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 0]);
                Toastr::success('Division Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
            
           }
        } 
       public function Enable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 1]);
                Toastr::success('Division Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
            
           }
        }
    
    public function delete($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 2]);
                Toastr::success('Division Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/divisions');
            
           }
        }

    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbydivision = Input::get('searchbydivision');
        $sortordname = Input::get('sortordname');
        $sortorderdivision = Input::get('sortorderdivision');
        $sortOrdDefault='';
        if($sortordname=='' && $sortorderdivision==''){
            $sortOrdDefault='ASC';
        }

        $divisions = DB::table('master_resources')
                ->select('master_resources.*','dept.name as dept_name')
                ->leftjoin('master_resources as dept', 'master_resources.department_id', '=', 'dept.id')
                ->where('master_resources.resource_type', '=', 'DIVISION')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)      
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbydivision, function ($query) use ($searchbydivision) {
                    return $query->whereRaw("(master_resources.department_id=$searchbydivision)");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortorderdivision, function ($query) use ($sortorderdivision) {
                    return $query->orderby('dept.name', $sortorderdivision);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('DivisionsList', function($excel) use($divisions){
                 // Set the title
                $excel->setTitle('Divisions List');
                
                $excel->sheet('Divisions List', function($sheet) use($divisions){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Divisions List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Division Name',"Department","Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($divisions);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $divisions[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $divisions[$i]->dept_name);
                        $sheet->setCellValue('D'.$chrRow, $divisions[$i]->alias_name);
                            
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
                <div style="text-align:center;"><h1>Divisions List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Divisions Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Department</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="areabody" id="areabody" >';
            $slno=0;
            foreach ($divisions as $division) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $division->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $division->dept_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $division->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_spot_list.pdf');
        }
    }     
}
