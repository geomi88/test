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
use DB;
use App;
use PDF;
use Excel;

class StaffhouseController extends Controller {

    public function index(Request $request) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
         $regions = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id')
                ->where(['resource_type' => 'REGION', 'company_id' => $company_id])
                ->where('status', '=', 1)
                ->get();
         
        $paginate = Config::get('app.PAGINATE');
        $staffhouse = DB::table('master_resources')
                ->select('master_resources.*','region.name as region_name')
                ->leftjoin('master_resources as region', 'master_resources.staffhouseregion', '=', 'region.id')
                ->where('master_resources.resource_type', '=', 'STAFF_HOUSE')
                ->where('master_resources.status', '!=', 2)->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name','ASC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbyregion = Input::get('searchbyregion');
            $sortordname = Input::get('sortordname');
           
            
            $sortOrdDefault='';
            if($sortordname==''){
                $sortOrdDefault='ASC';
            }
            
            $staffhouse = DB::table('master_resources')
                    ->select('master_resources.*','region.name as region_name')
                    ->leftjoin('master_resources as region', 'master_resources.staffhouseregion', '=', 'region.id')
                    ->where('master_resources.resource_type', '=', 'STAFF_HOUSE')
                    ->where('master_resources.status', '!=', 2)->where('master_resources.company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($searchbyregion, function ($query) use ($searchbyregion) {
                        return $query->whereRaw("(master_resources.staffhouseregion=$searchbyregion)");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                   ->paginate($paginate);

            return view('masterresources/staff_house/result', array('staffhouse' => $staffhouse));
        }
        
        return view('masterresources/staff_house/index', array('staffhouse' => $staffhouse,'regions'=>$regions));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $regions = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id')
                ->where(['resource_type' => 'REGION', 'company_id' => $company_id])
                ->where('status', '=', 1)
                ->get();
                
        return view('masterresources/staff_house/add',array('regions'=>$regions));
    }

    
    public function checkname() {

        $name = Input::get('name');
        
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.job_code')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'STAFF_HOUSE')
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
        
        $masterresourcemodel = new Masterresources;
        $masterresourcemodel->name = Input::get('name');
        $masterresourcemodel->alias_name = Input::get('alias_name');
        $masterresourcemodel->resource_type = 'STAFF_HOUSE';
        $masterresourcemodel->staffhouseregion=Input::get('staffhouseregion');
        $masterresourcemodel->company_id = $company_id;
        $masterresourcemodel->status = 1;
        $masterresourcemodel->save();
        
        Toastr::success('Staff House Successfully Added!', $title = null, $options = []);
        return Redirect::to('masterresources/staff_house');
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
         if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $staffhouse = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();
        
        $regions = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id')
                ->where(['resource_type' => 'REGION', 'company_id' => $company_id])
                ->where('status', '=', 1)
                ->get();
               
        return view('masterresources/staff_house/edit', array('staffhouse' => $staffhouse,'regions'=>$regions));
    }

    public function update() {
        try {
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $alias_name = Input::get('alias_name');
            $position_name = Input::get('name');
            
            DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $position_name,'staffhouseregion' =>Input::get('staffhouseregion'),'alias_name' => $alias_name]);
            
            Toastr::success('Staff House Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/staff_house');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/staff_house');
        }
    }


    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Staff House Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/staff_house');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/staff_house');
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

        $sortOrdDefault='';
        if($sortordname==''){
            $sortOrdDefault='ASC';
        }

        $staffhouse = DB::table('master_resources')
                ->select('master_resources.*','region.name as region_name')
                ->leftjoin('master_resources as region', 'master_resources.staffhouseregion', '=', 'region.id')
                ->where('master_resources.resource_type', '=', 'STAFF_HOUSE')
                ->where('master_resources.status', '!=', 2)->where('master_resources.company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbyregion, function ($query) use ($searchbyregion) {
                    return $query->whereRaw("(master_resources.staffhouseregion=$searchbyregion)");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('Staff House List', function($excel) use($staffhouse){
                 // Set the title
                $excel->setTitle('Staff House List');
                
                $excel->sheet('Staff House List', function($sheet) use($staffhouse){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Staff House List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Staff House','Region','Alias'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($staffhouse);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $staffhouse[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $staffhouse[$i]->region_name);
                       $sheet->setCellValue('D'.$chrRow, $staffhouse[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':D'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });
                        
                        $chrRow++;
                    }

                });
                
            })->export('xls');
            
        } 
    }

    
}
