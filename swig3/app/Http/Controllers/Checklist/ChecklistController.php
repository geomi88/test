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
use App\Models\Check_list;
use DB;
use App;
use PDF;
use Excel;

class ChecklistController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $checklist = DB::table('check_list')
                ->select('check_list.*','job_position.name as jobposition','category.name as categoryname')
                ->leftjoin('master_resources as job_position', 'check_list.job_position', '=', 'job_position.id')
                ->leftjoin('master_resources as category', 'check_list.category_id', '=', 'category.id')
                ->where('check_list.status', '!=', 2)
                ->orderby('category.name', 'ASC')
                ->paginate($paginate);
        
        $job_positions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        
        $categories = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'CHECK_LIST_CATEGORY', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyquery = Input::get('searchbyquery');
            $searchbyjob = Input::get('searchbyjob');
            $searchbycategory = Input::get('searchbycategory');
            $sortordquery = Input::get('sortordquery');
            $sortordjob = Input::get('sortordjob');
            $sortordcategory = Input::get('sortordcategory');
            
            $sortOrdDefault='';
            if($sortordquery=='' && $sortordjob=='' && $sortordcategory==''){
                $sortOrdDefault='ASC';
            }

            $checklist = DB::table('check_list')
                    ->select('check_list.*','job_position.name as jobposition','category.name as categoryname')
                    ->leftjoin('master_resources as job_position', 'check_list.job_position', '=', 'job_position.id')
                    ->leftjoin('master_resources as category', 'check_list.category_id', '=', 'category.id')
                    ->where('check_list.status', '!=', 2)
                    ->when($searchbyquery, function ($query) use ($searchbyquery) {
                        return $query->whereRaw("(check_list.checkpoint like '$searchbyquery%')");
                    })
                    ->when($searchbyjob, function ($query) use ($searchbyjob) {
                        return $query->whereRaw("(check_list.job_position=$searchbyjob)");
                    })
                    ->when($searchbycategory, function ($query) use ($searchbycategory) {
                        return $query->whereRaw("(check_list.category_id=$searchbycategory)");
                    })
                    ->when($sortordquery, function ($query) use ($sortordquery) {
                        return $query->orderby('check_list.checkpoint', $sortordquery);
                    })
                    ->when($sortordjob, function ($query) use ($sortordjob) {
                        return $query->orderby('job_position.name', $sortordjob);
                    })
                    ->when($sortordcategory, function ($query) use ($sortordcategory) {
                        return $query->orderby('category.name', $sortordcategory);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('category.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('checklist/check_list/result', array('checklist' => $checklist));
        }

        return view('checklist/check_list/index', array('checklist' => $checklist,'job_positions'=>$job_positions,'categories'=>$categories));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'CHECK_LIST_CATEGORY', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        return view('checklist/check_list/add', array('job_positions'=>$job_positions,'categories'=>$categories));
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            $arrPointList = Input::get('arrPointList');
            $arrPointList = json_decode($arrPointList);
            
            foreach ($arrPointList as $query) {
                $daystring='';
                if($query->sunday=="Yes"){
                    $daystring=$daystring."1,";
                }
                if($query->monday=="Yes"){
                    $daystring=$daystring."2,";
                }
                if($query->tuesday=="Yes"){
                    $daystring=$daystring."3,";
                }
                if($query->wednesday=="Yes"){
                    $daystring=$daystring."4,";
                }
                if($query->thursday=="Yes"){
                    $daystring=$daystring."5,";
                }
                if($query->friday=="Yes"){
                    $daystring=$daystring."6,";
                }
                if($query->satday=="Yes"){
                    $daystring=$daystring."7,";
                }
                $daystring=rtrim($daystring, ",");
                
                $allday=0;
                if($query->allday=="Yes"){
                    $allday=1;
                }
                
                $checkmodel = new Check_list();
                $checkmodel->category_id = $arraData->category_id;
                $checkmodel->job_position = $arraData->job_id;
                $checkmodel->checkpoint = $query->checkpoint;
                $checkmodel->alias = $query->alias;
                $checkmodel->daystring = $daystring;
                $checkmodel->all_day = $allday;
                $checkmodel->save();
            }

            Toastr::success("Check List Saved Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'CHECK_LIST_CATEGORY', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $check_point = DB::table('check_list')
                            ->select('check_list.*')
                            ->where(['check_list.id' => $dn])
                            ->first();
        
         $arrDays=explode(",", $check_point->daystring);
         
        
        return view('checklist/check_list/edit', array('check_point' => $check_point,'job_positions'=>$job_positions,'categories'=>$categories,'arrDays'=>$arrDays));
    }

    public function update() {
        try {

            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            
            $daystring='';
            if($arraData->sunday=="Yes"){
                $daystring=$daystring."1,";
            }
            if($arraData->monday=="Yes"){
                $daystring=$daystring."2,";
            }
            if($arraData->tuesday=="Yes"){
                $daystring=$daystring."3,";
            }
            if($arraData->wednesday=="Yes"){
                $daystring=$daystring."4,";
            }
            if($arraData->thursday=="Yes"){
                $daystring=$daystring."5,";
            }
            if($arraData->friday=="Yes"){
                $daystring=$daystring."6,";
            }
            if($arraData->satday=="Yes"){
                $daystring=$daystring."7,";
            }
            $daystring=rtrim($daystring, ",");

            $allday=0;
            if($arraData->allday=="Yes"){
                $allday=1;
            }
            $categories = DB::table('check_list')
                    ->where(['id' => $arraData->point_id])
                    ->update([
                            'category_id' => $arraData->category_id,
                            'job_position' => $arraData->job_id,
                            'checkpoint' => $arraData->checkpoint,
                            'alias' => $arraData->alias,
                            'daystring' => $daystring,
                            'all_day' => $allday,
                            ]);

            Toastr::success('Check Point Successfully Updated', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('check_list')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Check Point Successfully Disabled', $title = null, $options = []);
            return Redirect::to('checklist/check_list');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('checklist/check_list');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('check_list')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Check Point Successfully Enabled', $title = null, $options = []);
            return Redirect::to('checklist/check_list');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('checklist/check_list');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('check_list')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Check List Category Successfully Deleted', $title = null, $options = []);
            return Redirect::to('checklist/check_list');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('checklist/check_list');
        }
    }

        // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyquery = Input::get('searchbyquery');
        $searchbyjob = Input::get('searchbyjob');
        $searchbycategory = Input::get('searchbycategory');
        $sortordquery = Input::get('sortordquery');
        $sortordjob = Input::get('sortordjob');
        $sortordcategory = Input::get('sortordcategory');

        $sortOrdDefault='';
        if($sortordquery=='' && $sortordjob=='' && $sortordcategory==''){
            $sortOrdDefault='ASC';
        }

        $checklist = DB::table('check_list')
                ->select('check_list.*','job_position.name as jobposition','category.name as categoryname')
                ->leftjoin('master_resources as job_position', 'check_list.job_position', '=', 'job_position.id')
                ->leftjoin('master_resources as category', 'check_list.category_id', '=', 'category.id')
                ->where('check_list.status', '!=', 2)
                ->when($searchbyquery, function ($query) use ($searchbyquery) {
                    return $query->whereRaw("(check_list.checkpoint like '$searchbyquery%')");
                })
                ->when($searchbyjob, function ($query) use ($searchbyjob) {
                    return $query->whereRaw("(check_list.job_position=$searchbyjob)");
                })
                ->when($searchbycategory, function ($query) use ($searchbycategory) {
                    return $query->whereRaw("(check_list.category_id=$searchbycategory)");
                })
                ->when($sortordquery, function ($query) use ($sortordquery) {
                    return $query->orderby('check_list.checkpoint', $sortordquery);
                })
                ->when($sortordjob, function ($query) use ($sortordjob) {
                    return $query->orderby('job_position.name', $sortordjob);
                })
                ->when($sortordcategory, function ($query) use ($sortordcategory) {
                    return $query->orderby('category.name', $sortordcategory);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('category.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('CheckList', function($excel) use($checklist){
                 // Set the title
                $excel->setTitle('Check List');
                
                $excel->sheet('Check List', function($sheet) use($checklist){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Check List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Check Point', 'Category Name',"Job Position",'Days','Alias'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($checklist);$i++){
                        
                        if($checklist[$i]->all_day==1){
                            $daystring="All Days";
                        }else{
                            $daystring=$checklist[$i]->daystring;
                            $daystring=str_replace("1", "Sun", $daystring);
                            $daystring=str_replace("2", "Mon", $daystring);
                            $daystring=str_replace("3", "Tue", $daystring);
                            $daystring=str_replace("4", "Wed", $daystring);
                            $daystring=str_replace("5", "Thu", $daystring);
                            $daystring=str_replace("6", "Fri", $daystring);
                            $daystring=str_replace("7", "Sat", $daystring);
                        }
                        $sheet->setCellValue('A'.$chrRow, ($checklist[$i]->checkpoint));
                        $sheet->setCellValue('B'.$chrRow, $checklist[$i]->categoryname);
                        $sheet->setCellValue('C'.$chrRow, $checklist[$i]->jobposition);
                        $sheet->setCellValue('D'.$chrRow, $daystring);
                        $sheet->setCellValue('E'.$chrRow, $checklist[$i]->alias);
                            
                        $sheet->cells('A'.$chrRow.':E'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Check List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Check Point</td>
                                <td style="padding:10px 5px;color:#fff;">Category Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Job Position </td>
                                <td style="padding:10px 5px;color:#fff;"> Days </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            
            foreach ($checklist as $point) {
                if($point->all_day==1){
                    $daystring="All Days";
                }else{
                    $daystring=$point->daystring;
                    $daystring=str_replace("1", "Sun", $daystring);
                    $daystring=str_replace("2", "Mon", $daystring);
                    $daystring=str_replace("3", "Tue", $daystring);
                    $daystring=str_replace("4", "Wed", $daystring);
                    $daystring=str_replace("5", "Thu", $daystring);
                    $daystring=str_replace("6", "Fri", $daystring);
                    $daystring=str_replace("7", "Sat", $daystring);
                }
                
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $point->checkpoint . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $point->categoryname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $point->jobposition . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $daystring . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $point->alias . '</td>
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