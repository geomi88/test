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
use App\Models\Company;
use App\Models\Masterresources;
use DB;
use App;
use PDF;
use Excel;

class WarehouseController extends Controller {

    public function index(Request $request) {
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $paginate=Config::get('app.PAGINATE');
        $warehouses = DB::table('master_resources')
                ->select('master_resources.*','region.name as region_name','emp.first_name as manager')
                ->leftjoin('master_resources as region', 'master_resources.region_id', '=', 'region.id')
                ->leftjoin('employees as emp', 'master_resources.warehouse_manager', '=', 'emp.id')
                ->where('master_resources.resource_type', '=', 'WAREHOUSE')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name','ASC')
                ->paginate($paginate);
        
        $regions = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id')
                ->where(['resource_type' => 'REGION', 'company_id' => $company_id])
                ->where('status', '=', 1)
                ->get();
               
        $warehousemanagers = DB::table('employees')
                ->select('employees.first_name','employees.alias_name', 'employees.id')
                ->leftjoin('master_resources as m', 'employees.job_position', '=', 'm.id')
                ->where(['m.name' => 'Warehouse_Manager', 'm.company_id' => $company_id])
                ->where('employees.status', '=', 1)
                ->get();
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbyregion = Input::get('searchbyregion');
            $searchbymgr = Input::get('searchbymgr');
            $sortordname = Input::get('sortordname');
            $sortorderregion = Input::get('sortorderregion');
            $sortordermgr = Input::get('sortordermgr');
            $sortOrdDefault='';
            if($sortordname=='' && $sortorderregion=='' && $sortordermgr==''){
                $sortOrdDefault='ASC';
            }
            
            $warehouses = DB::table('master_resources')
                    ->select('master_resources.*','region.name as region_name','emp.first_name as manager')
                    ->leftjoin('master_resources as region', 'master_resources.region_id', '=', 'region.id')
                    ->leftjoin('employees as emp', 'master_resources.warehouse_manager', '=', 'emp.id')
                    ->where('master_resources.resource_type', '=', 'WAREHOUSE')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($searchbyregion, function ($query) use ($searchbyregion) {
                        return $query->whereRaw("(master_resources.region_id=$searchbyregion)");
                    })
                    ->when($searchbymgr, function ($query) use ($searchbymgr) {
                        return $query->whereRaw("(master_resources.warehouse_manager=$searchbymgr)");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortorderregion, function ($query) use ($sortorderregion) {
                        return $query->orderby('region.name', $sortorderregion);
                    })
                    ->when($sortordermgr, function ($query) use ($sortordermgr) {
                        return $query->orderby('emp.first_name', $sortordermgr);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/warehouse/warehouse_result', array('warehouses' => $warehouses));
        }
        
        return view('masterresources/warehouse/index', array('warehouses' => $warehouses,'regions'=>$regions,'warehousemanagers'=>$warehousemanagers));
    }

    public function add() {
        if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
         $regions = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'REGION', 'company_id' => $company_id])->where('status', '=', 1)->get();
         
         $warehousemanagers = DB::table('employees')
                        ->select('employees.first_name','employees.alias_name', 'employees.id')
                        ->leftjoin('master_resources as m', 'employees.job_position', '=', 'm.id')
                        ->where(['m.name' => 'Warehouse_Manager', 'm.company_id' => $company_id])
                        ->where('employees.status', '=', 1)
                        ->get();
               
        return view('masterresources/warehouse/add' ,array('regions' => $regions,'warehousemanagers' => $warehousemanagers));
    }

    public function store() {
        try {
             if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('warehouse_name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->region_id = Input::get('regionselect');
            $masterresourcemodel->warehouse_manager = Input::get('warehousemgr');
            $masterresourcemodel->resource_type = 'WAREHOUSE';
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            Toastr::success('Warehouse Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/warehouse');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/warehouse/add');
        }
    }

    public function checkwarehouse() {
        $name = Input::get('warehouse_name');
        $id = Input::get('cid');        
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)->where('resource_type', '=', 'WAREHOUSE')->where('id', '!=', $id)
                ->get();
      // print_r($name);print_r($id);die('fsdfsf');
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
        $warehouses = DB::table('master_resources')
                 ->where(['id' => $dn])                            
                                ->get();
       $regions = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'REGION', 'company_id' => $company_id])
                        ->get(); 
       
       $warehousemanagers = DB::table('employees')
                        ->select('employees.first_name','employees.alias_name', 'employees.id')
                        ->leftjoin('master_resources as m', 'employees.job_position', '=', 'm.id')
                        ->where(['m.name' => 'Warehouse_Manager', 'm.company_id' => $company_id])
                        ->where('employees.status', '=', 1)
                        ->get();
        return view('masterresources/warehouse/edit', array('warehouses' => $warehouses,'regions' => $regions,'warehousemanagers' => $warehousemanagers));
    
        }
        
        
       public function update()
        {
           try
           {
                $id = Input::get('cid');
                $dn=\Crypt::encrypt($id);
                $name = Input::get('warehouse_name');
                $alias_name = Input::get('alias_name');
                $region = Input::get('region');
                $warehousemgr = Input::get('warehousemgr');
                $warehouses = DB::table('master_resources')
                                ->where(['id' => $id])                            
                                ->update(['name' => $name,'alias_name'=>$alias_name,'region_id'=>$region,'warehouse_manager'=>$warehousemgr]);
                Toastr::success('Warehouse Successfully Updated', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse');
            } 
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse/edit/'.$dn);            
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
                Toastr::success('Warehouse Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse');
            
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
                Toastr::success('Warehouse Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse');
            
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
                Toastr::success('Warehouse Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/warehouse');
            
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
        $searchbymgr = Input::get('searchbymgr');
        $sortordname = Input::get('sortordname');
        $sortorderregion = Input::get('sortorderregion');
        $sortordermgr = Input::get('sortordermgr');
        $sortOrdDefault='';
        if($sortordname=='' && $sortorderregion=='' && $sortordermgr==''){
            $sortOrdDefault='ASC';
        }

        $warehouses = DB::table('master_resources')
                ->select('master_resources.*','region.name as region_name','emp.first_name as manager')
                ->leftjoin('master_resources as region', 'master_resources.region_id', '=', 'region.id')
                ->leftjoin('employees as emp', 'master_resources.warehouse_manager', '=', 'emp.id')
                ->where('master_resources.resource_type', '=', 'WAREHOUSE')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbyregion, function ($query) use ($searchbyregion) {
                    return $query->whereRaw("(master_resources.region_id=$searchbyregion)");
                })
                ->when($searchbymgr, function ($query) use ($searchbymgr) {
                    return $query->whereRaw("(master_resources.warehouse_manager=$searchbymgr)");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortorderregion, function ($query) use ($sortorderregion) {
                    return $query->orderby('region.name', $sortorderregion);
                })
                ->when($sortordermgr, function ($query) use ($sortordermgr) {
                    return $query->orderby('emp.first_name', $sortordermgr);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('WarehouseList', function($excel) use($warehouses){
                 // Set the title
                $excel->setTitle('Warehouse List');
                
                $excel->sheet('Warehouse List', function($sheet) use($warehouses){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Warehouse List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Warehouse Name',"Region",'Manager',"Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($warehouses);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $warehouses[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $warehouses[$i]->region_name);
                        $sheet->setCellValue('D'.$chrRow, $warehouses[$i]->manager);
                        $sheet->setCellValue('E'.$chrRow, $warehouses[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':E'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Warehouse List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Warehouse Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Region</td>
                                <td style="padding:10px 5px;color:#fff;"> Manager</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="warehousebody" id="warehousebody" >';
            $slno=0;
            foreach ($warehouses as $warehouse) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $warehouse->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $warehouse->region_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $warehouse->manager . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $warehouse->alias_name . '</td>
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
