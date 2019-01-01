<?php

namespace App\Http\Controllers\Checklist;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use App\Models\Company;
use App\Models\Masterresources;
use DB;
use App;
use PDF;
use Excel;

class ChecklistcategoryController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $checklist_categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'CHECK_LIST_CATEGORY')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name', 'ASC')
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

            $checklist_categories = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'CHECK_LIST_CATEGORY')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)
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

            return view('checklist/check_list_category/result', array('checklist_categories' => $checklist_categories));
        }

        return view('checklist/check_list_category/index', array('checklist_categories' => $checklist_categories));
    }

    public function graphindex() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
          $region_id="";
        
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        
        $warnings = DB::table('warnings')
                ->select(DB::raw('count(*) as catcount,warning_type,wrg.name as categoryname,wrg.alias_name as alias'))
                ->leftjoin('master_resources as wrg', 'warnings.warning_type', '=', 'wrg.id')
                ->whereraw("date(warnings.created_at)>='$first_day' AND date(warnings.created_at)<='$last_day' and warnings.status=1")
                ->groupby('warning_type')
                ->orderby('catcount','DESC')
                ->get();
       
        $totalcount=$warnings->sum("catcount");
        
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id','region_details.name as region_name','region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
         
        $public_path= url('/');
                
        $arrwarnings = array();
        $arrcount = array();
        
        
        foreach ($warnings as $data) {
            $arrwarnings[]=$data->categoryname.' : '.$data->alias;
            $Id=\Crypt::encrypt($data->warning_type);
            $arrcount[]=array('y'=>$data->catcount,'url'=>$public_path.'/warningwithcategory/'.$Id);
        }
        
        if(count($arrwarnings)>8){
            $supervisorcount=count($arrwarnings);
            $supervisorcount=$supervisorcount*60;
        }else{
            $supervisorcount=480;
        }
        
        $arrwarnings = array_values($arrwarnings);
        $arrwarnings = json_encode($arrwarnings);
        
        $arrcount = array_values($arrcount);
        $arrcount = json_encode($arrcount);
        
        return view('checklist/warning_graph/index', array(
            'arrwarnings' => $arrwarnings,'arrcount'=>$arrcount,
            'supervisorcount'=>$supervisorcount,'totalcount'=>$totalcount,
            'periodStartDate'=>date('01-m-Y'),'periodEndDate'=>date('t-m-Y'),"regions"=>$regions,"region_id"=>$region_id));
    }
    
    public function getcategorygraph() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        
        $period_start_date = $first_day;
        $period_end_date = $last_day;
        
        $region_id=Input::get('region_id');
        
        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }
        
        $warnings = DB::table('warnings')
                ->select(DB::raw('count(*) as catcount,warning_type,wrg.name as categoryname,wrg.alias_name as alias'))
                ->leftjoin('master_resources as wrg', 'warnings.warning_type', '=', 'wrg.id')
                ->leftjoin('master_resources as branch', 'warnings.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(warnings.created_at)>='$first_day' AND date(warnings.created_at)<='$last_day' and warnings.status=1")
                ->when($region_id, function ($query) use ($region_id) {
                       return $query->where('area.region_id', '=', $region_id);
                    }) 
                ->groupby('warning_type')
                ->orderby('catcount','DESC')
                ->get();
       
                
        $totalcount=$warnings->sum("catcount");
        
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id','region_details.name as region_name','region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
         
        $public_path= url('/');
                
        $arrwarnings = array();
        $arrcount = array();
        
        foreach ($warnings as $data) {
            $arrwarnings[]=$data->categoryname.' : '.$data->alias;
            $Id=\Crypt::encrypt($data->warning_type);
            $arrcount[]=array('y'=>$data->catcount,'url'=>$public_path.'/warningwithcategory/'.$Id);
        }
        
        if(count($arrwarnings)>8){
            $supervisorcount=count($arrwarnings);
            $supervisorcount=$supervisorcount*60;
        }else{
            $supervisorcount=480;
        }
        
        $arrwarnings = array_values($arrwarnings);
        $arrwarnings = json_encode($arrwarnings);
        
        $arrcount = array_values($arrcount);
        $arrcount = json_encode($arrcount);
        
        return view('checklist/warning_graph/index', array(
                'arrwarnings' => $arrwarnings,'arrcount'=>$arrcount,
                'supervisorcount'=>$supervisorcount,'totalcount'=>$totalcount,
                'periodStartDate'=>$period_start_date,'periodEndDate'=>$period_end_date,"regions"=>$regions,"region_id"=>$region_id));
    }
    
    
    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        return view('checklist/check_list_category/add', array());
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->resource_type = 'CHECK_LIST_CATEGORY';
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            
            Toastr::success('Check List Category Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category/add');
        }
    }

    public function checkcategories() {
        $name = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'CHECK_LIST_CATEGORY')
                ->where('id', '!=', $id)
                ->where('status', '!=', 2)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $categorie_data = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['master_resources.id' => $dn])
                            ->first();
        
        return view('checklist/check_list_category/edit', array('categorie_datas' => $categorie_data));
    }

    public function update() {
        try {
            $masterresourcemodel = new Masterresources;
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');

            $categories = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $name,
                            'alias_name' => $alias_name,
                            ]);

            Toastr::success('Check List Category Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category/edit/' . $dn);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Check List Category Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Check List Category Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Check List Category Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/check_list_category');
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

        $checklist_categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'CHECK_LIST_CATEGORY')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
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
            
            Excel::create('CheckListCategory', function($excel) use($checklist_categories){
                 // Set the title
                $excel->setTitle('Check List Category');
                
                $excel->sheet('Check List Category', function($sheet) use($checklist_categories){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Check List Category');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:C3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Category Name',"Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:C5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($checklist_categories);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $checklist_categories[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $checklist_categories[$i]->alias_name);
                            
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
                <div style="text-align:center;"><h1>Check List Category</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;">Category Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($checklist_categories as $cat) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_check_list.pdf');
        }
    }
}
