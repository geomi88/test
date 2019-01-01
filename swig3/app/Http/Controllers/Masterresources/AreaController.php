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

class AreaController extends Controller {

    public function index(Request $request) {
        $paginate=Config::get('app.PAGINATE');
        $companyid=session('company');

        $companies = Company::all();
        $areas = DB::table('master_resources')
                    ->select('master_resources.*','region.name as region_name')
                    ->leftjoin('master_resources as region', 'master_resources.region_id', '=', 'region.id')
                    ->where('master_resources.resource_type', '=', 'AREA')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $companyid)                        
                    ->orderby('master_resources.name', 'ASC')
                    ->paginate($paginate);
        
        $regions = DB::table('master_resources')
                    ->select('master_resources.name', 'master_resources.id')
                    ->where(['resource_type' => 'REGION', 'company_id' => $companyid])
                    ->where('status', '=', 1)
                    ->get();
      
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbyregion = Input::get('searchbyregion');
            $sortordname = Input::get('sortordname');
            $sortorderregion = Input::get('sortorderregion');
            $sortOrdDefault='';
            if($sortordname=='' && $sortorderregion==''){
                $sortOrdDefault='ASC';
            }
            
            $areas = DB::table('master_resources')
                    ->select('master_resources.*','region.name as region_name')
                    ->leftjoin('master_resources as region', 'master_resources.region_id', '=', 'region.id')
                    ->where('master_resources.resource_type', '=', 'AREA')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $companyid)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbyregion, function ($query) use ($searchbyregion) {
                    return $query->whereRaw("(master_resources.region_id=$searchbyregion)");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortorderregion, function ($query) use ($sortorderregion) {
                    return $query->orderby('region.name', $sortorderregion);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->paginate($paginate);

            return view('masterresources/areas/area_result', array('areas' => $areas));
        }
        
        return view('masterresources/areas/index', array('companies' => $companies, 'areas' => $areas,'regions'=>$regions));
    }

    public function add()
    {  
        if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $companies = Company::all();
        $areas = DB::table('master_resources')
                        ->join('companies', 'master_resources.company_id', '=', 'companies.id')
                        ->select('master_resources.*', 'companies.name as company_name')
                        ->where(['resource_type' => 'AREA'])->where('master_resources.status', '=', 1)->get();
        
        $regions = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'REGION', 'company_id' => $company_id])->where('status', '=', 1)->get();
       
         return view('masterresources/areas/add', array('companies' => $companies, 'areas' => $areas, 'regions' => $regions));
            
    }
    public function store() {
        
            if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $masterresourcemodel = new Masterresources();
        $masterresourcemodel->resource_type = 'AREA';
        $masterresourcemodel->name = Input::get('name');
        $masterresourcemodel->alias_name = Input::get('alias_name');
        $masterresourcemodel->company_id = $company_id;
        $masterresourcemodel->region_id = Input::get('region');
        if(Input::get('region')=='')
        {
          $masterresourcemodel->region_id = NULL;  
        }
//        $masterresourcemodel->address = Input::get('address');
//        $masterresourcemodel->latitude = Input::get('latitude');
//        $masterresourcemodel->longitude = Input::get('longitude');
//        $masterresourcemodel->branch_code = Input::get('branch_code');
//        $masterresourcemodel->opening_fund = Input::get('fund');
        $masterresourcemodel->status = 1;
        $masterresourcemodel->save();
                Toastr::success('Area Successfully Created!', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
        }

     public function checkareaname() 
       {
        
        $area = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('name', '=', $area)->where('resource_type', '=', 'AREA')
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
                $regions = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'REGION', 'company_id' => $company_id])->get();
                $areas = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->first();      
           // print_r('<pre>');print_r($areas);die('df');
                return view('masterresources/areas/edit', array('areas' => $areas , 'regions'=>$regions ,'company_id'=>$company_id));
           
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
            $region_id = Input::get('region');
            if(Input::get('region')=='')
            {
              $region_id = NULL;  
            }
           
            $areas = DB::table('master_resources')
                                ->where(['id' => $id])                            
                                ->update(['name' => $name,'alias_name'=>$alias_name,'region_id'=>$region_id]);
                 Toastr::success('Area Successfully Updated!', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
        }
     catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/areas/edit/'.$dn);
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
                Toastr::success('Area Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
            
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
                Toastr::success('Area Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
            
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
                Toastr::success('Area Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/areas');
            
           }
        }

    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbyregion = Input::get('searchbyregion');
        $sortordname = Input::get('sortordname');
        $sortorderregion = Input::get('sortorderregion');

        $sortOrdDefault='';
        if($sortordname=='' && $sortorderregion==''){
            $sortOrdDefault='ASC';
        }

        $areas = DB::table('master_resources')
                ->select('master_resources.*','region.name as region_name')
                ->leftjoin('master_resources as region', 'master_resources.region_id', '=', 'region.id')
                ->where('master_resources.resource_type', '=', 'AREA')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbyregion, function ($query) use ($searchbyregion) {
                    return $query->whereRaw("(master_resources.region_id=$searchbyregion)");
                })
                ->when($sortorderregion, function ($query) use ($sortorderregion) {
                    return $query->orderby('region.name', $sortorderregion);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('AreaList', function($excel) use($areas){
                 // Set the title
                $excel->setTitle('Area List');
                
                $excel->sheet('Area List', function($sheet) use($areas){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Area List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Area Name',"Region","Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($areas);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $areas[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $areas[$i]->region_name);
                        $sheet->setCellValue('D'.$chrRow, $areas[$i]->alias_name);
                            
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
                <div style="text-align:center;"><h1>Area List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Area Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Region</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="areabody" id="areabody" >';
            $slno=0;
            foreach ($areas as $area) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $area->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $area->region_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $area->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_area_list.pdf');
        }
    }

}
