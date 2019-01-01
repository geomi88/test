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
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Masterresources;
use App\Models\Company;
use DB;
use App;
use PDF;
use Excel;

class DepartmentController extends Controller {

  public function index(Request $request) {
        $paginate=Config::get('app.PAGINATE');
        $companyid=session('company');

        $companies = Company::all();
        $departments = DB::table('master_resources')
                  ->select('master_resources.*')
                  ->where('resource_type', '=', 'DEPARTMENT')
                  ->where('status', '!=', 2)->where('company_id', '=', $companyid)       
                  ->orderby('master_resources.name', 'ASC')
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
            
            $departments = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'DEPARTMENT')
                    ->where('status', '!=', 2)->where('company_id', '=', $companyid) 
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

            return view('masterresources/department/department_result', array('departments' => $departments));
        }
        
      return view('masterresources/department/index', array('departments' => $departments,'companies' => $companies));
    }

    public function add()
    {  
        try{
            if (Session::get('company'))
            { 
            $companyid = Session::get('company');  
            $companies = DB::table('companies')
                        ->where('id', '=', $companyid)->get(); 
             
        return view('masterresources/department/add', array('companies' => $companies));
            }
             else
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department');
            
            }
    }
     catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department');
            }
            
    }
    public function store()
    {   
       try{
            if (Session::get('company'))
            {
            $companyid = Session::get('company');
            $masterresourcemodel = new Masterresources();
            $masterresourcemodel->resource_type = 'DEPARTMENT';
            $masterresourcemodel->name = Input::get('name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->company_id = $companyid;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            Toastr::success('Department Successfully Created', $title = null, $options = []);
            return Redirect::to('masterresources/department');
             }
        
            else
                {
                    Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                    return Redirect::to('masterresources/department/add');

                }
            }
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department/add');
            }
    }
     public function edit($id)
    {
    try{
            if (Session::get('company'))
            { 
                $dn=\Crypt::decrypt($id);
                $departments = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->get();

            return view('masterresources/department/edit', array('departments' => $departments));
                }
                 else
                {
                    Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                    return Redirect::to('masterresources/department');

                }
        }
     catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department/edit/'.$dn);
            }
            
    }
     
       public function update()
        {
           try{ 
                $companyid = Session::get('company');
                $masterresourcemodel = new Masterresources;
                $id = Input::get('cid');
                $dn=\Crypt::encrypt($id);
                $name = Input::get('name');
                $alias_name = Input::get('alias_name');
                $department = DB::table('master_resources')
                                ->where(['id' => $id])                            
                                ->update(['name' => $name,'alias_name'=>$alias_name]);
                $department = DB::table('master_resources');
                Toastr::success('Department Successfully Updated', $title = null, $options = []);
                return Redirect::to('masterresources/department');
               
           
           }
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department/edit/'.$dn);
            
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
                Toastr::success('Department Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/department');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department');
            
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
                Toastr::success('Department Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/department');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department');
            
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
                Toastr::success('Department Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/department');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/department');
            
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

        $departments = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'DEPARTMENT')
                ->where('status', '!=', 2)->where('company_id', '=', $company_id) 
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
            
            Excel::create('DepartmentList', function($excel) use($departments){
                 // Set the title
                $excel->setTitle('Department List');
                
                $excel->sheet('Department List', function($sheet) use($departments){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Department List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:C3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Department Name',"Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:C5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($departments);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $departments[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $departments[$i]->alias_name);
                            
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
                <div style="text-align:center;"><h1>Department List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Department Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="departmentbody" id="departmentbody" >';
            $slno=0;
            foreach ($departments as $department) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $department->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $department->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_department_list.pdf');
        }
    }
}
