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

class SpotController extends Controller {

    public function index(Request $request) {
        
        if (Session::get('company')) { 
        $company_id = Session::get('company');  
        }
        $paginate=Config::get('app.PAGINATE');
        $spots = DB::table('master_resources')
                ->select('master_resources.*','w.name as warehouse_name')
                ->leftjoin('master_resources as w', 'master_resources.warehouse_id', '=', 'w.id')
                ->where('master_resources.resource_type', '=', 'SPOT')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name','ASC')
                ->paginate($paginate);
        
        $warehouse = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id')
                ->where(['resource_type' => 'WAREHOUSE', 'company_id' => $company_id])
                ->where('status', '=', 1)
                ->get();    
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbyware = Input::get('searchbyware');
            $sortordname = Input::get('sortordname');
            $sortorderware = Input::get('sortorderware');
            $sortOrdDefault='';
            if($sortordname=='' && $sortorderware==''){
                $sortOrdDefault='ASC';
            }
            
            $spots = DB::table('master_resources')
                    ->select('master_resources.*','w.name as warehouse_name')
                    ->leftjoin('master_resources as w', 'master_resources.warehouse_id', '=', 'w.id')
                    ->where('master_resources.resource_type', '=', 'SPOT')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($searchbyware, function ($query) use ($searchbyware) {
                        return $query->whereRaw("(master_resources.warehouse_id=$searchbyware)");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortorderware, function ($query) use ($sortorderware) {
                        return $query->orderby('w.name', $sortorderware);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/spots/spot_result', array('spots' => $spots));
        }
        
        return view('masterresources/spots/index', array('spots' => $spots,'warehouses'=>$warehouse));
    }

    public function add() {
       
         if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $warehouses = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'WAREHOUSE'])->where('status', '!=', 2)->where('company_id', '=', $company_id)->orderby('name','ASC')->get();
        return view('masterresources/spots/add',array('warehouses' => $warehouses));
    }

    public function store() {
        try {
             if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('spot_name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->resource_type = 'SPOT';
            $masterresourcemodel->warehouse_id = Input::get('warehouse_id');
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            Toastr::success('Spot Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/spot');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/spot/add');
        }
    }

    public function checkspots() {
        $name = Input::get('spot_name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)->where('resource_type', '=', 'SPOT')->where('id', '!=', $id)
                ->get();
        if(count($data)==0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
        $dn=\Crypt::decrypt($id);
        if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $spots = DB::table('master_resources')
                 ->where(['id' => $dn])                            
                                ->get();
           
        $warehouses = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'WAREHOUSE'])->where('status', '!=', 2)->where('company_id', '=', $company_id)->orderby('name','ASC')->get();
       
        return view('masterresources/spots/edit', array('spots' => $spots , 'warehouses' => $warehouses));
   
    }

    public function update() {
        try
           {
                $id = Input::get('cid');
                $dn=\Crypt::encrypt($id);
                $name = Input::get('spot_name');
                $alias_name = Input::get('alias_name');
                $warehouse_id = Input::get('warehouse_id');
                $spots = DB::table('master_resources')
                        ->where(['id' => $id])                            
                        ->update(['name' => $name,'alias_name'=>$alias_name ,'warehouse_id'=>$warehouse_id]);
                Toastr::success('Spot Successfully Updated', $title = null, $options = []);
                return Redirect::to('masterresources/spot');
            } 
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/spot/edit/'.$dn);
            
            }
    }

       public function Disable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies= DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 0]);
                Toastr::success('Spot Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/spot');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/spot');
            
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
                Toastr::success('Spot Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/spot');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/spot');
            
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
                Toastr::success('Spot Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/spot');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/spot');
            
           }
        }
     
    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbyware = Input::get('searchbyware');
        $sortordname = Input::get('sortordname');
        $sortorderware = Input::get('sortorderware');
        $sortOrdDefault='';
        if($sortordname=='' && $sortorderware==''){
            $sortOrdDefault='ASC';
        }

        $spots = DB::table('master_resources')
                ->select('master_resources.*','w.name as warehouse_name')
                ->leftjoin('master_resources as w', 'master_resources.warehouse_id', '=', 'w.id')
                ->where('master_resources.resource_type', '=', 'SPOT')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbyware, function ($query) use ($searchbyware) {
                    return $query->whereRaw("(master_resources.warehouse_id=$searchbyware)");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortorderware, function ($query) use ($sortorderware) {
                    return $query->orderby('w.name', $sortorderware);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('SpotList', function($excel) use($spots){
                 // Set the title
                $excel->setTitle('Spot List');
                
                $excel->sheet('Spot List', function($sheet) use($spots){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Spots List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Spot Name',"Warehouse","Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($spots);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $spots[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $spots[$i]->warehouse_name);
                        $sheet->setCellValue('D'.$chrRow, $spots[$i]->alias_name);
                            
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
                <div style="text-align:center;"><h1>Spots List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Spot Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Warehouse</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="areabody" id="areabody" >';
            $slno=0;
            foreach ($spots as $spot) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $spot->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $spot->warehouse_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $spot->alias_name . '</td>
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
