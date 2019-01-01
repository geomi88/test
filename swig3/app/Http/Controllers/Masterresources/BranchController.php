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

class BranchController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        $companyid = session('company');

        $companies = Company::all();
        $branches = DB::table('master_resources')
                ->select('master_resources.*','area.name as area_name')
                ->leftjoin('master_resources as area', 'master_resources.area_id', '=', 'area.id')
                ->where('master_resources.resource_type', '=', 'BRANCH')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $companyid)
                ->orderby("master_resources.name","ASC")
                ->paginate($paginate);
        
        $areas = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id', 'master_resources.region_id')
                ->where(['resource_type' => 'AREA', 'company_id' => $companyid, 'status' => 1])->get();
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbyarea = Input::get('searchbyarea');
            $searchbycode = Input::get('searchbycode');
            
            $sortordname = Input::get('sortordname');
            $sortorderarea = Input::get('sortorderarea');
            $sortordercode = Input::get('sortordercode');
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortorderarea=='' && $sortordercode==''){
                $sortOrdDefault='ASC';
            }
            
            $branches = DB::table('master_resources')
                    ->select('master_resources.*','area.name as area_name')
                    ->leftjoin('master_resources as area', 'master_resources.area_id', '=', 'area.id')
                    ->where('master_resources.resource_type', '=', 'BRANCH')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $companyid)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($searchbyarea, function ($query) use ($searchbyarea) {
                        return $query->whereRaw("(master_resources.area_id=$searchbyarea)");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(master_resources.branch_code like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortorderarea, function ($query) use ($sortorderarea) {
                        return $query->orderby('area.name', $sortorderarea);
                    })
                    ->when($sortordercode, function ($query) use ($sortordercode) {
                        return $query->orderby('master_resources.branch_code', $sortordercode);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/branch/branch_result', array('branches' => $branches));
        }
        
        return view('masterresources/branch/index', array('title' => 'Branch Listing', 'description' => '', 'page' => 'branches', 'companies' => $companies, 'branches' => $branches,'areas'=>$areas));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $companies = Company::all();

        $job_shifts = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_SHIFT'])->where('status', '=', 1)->get();

        $branches = DB::table('master_resources')
                        ->join('companies', 'master_resources.company_id', '=', 'companies.id')
                        ->select('master_resources.*', 'companies.name as company_name')
                        ->where(['resource_type' => 'BRANCH'])->get();

        $areas = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id', 'master_resources.region_id')
                        ->where(['resource_type' => 'AREA', 'company_id' => $company_id, 'status' => 1])->get();
        return view('masterresources/branch/add', array('title' => 'Branch Listing', 'description' => '', 'page' => 'branches', 'companies' => $companies, 'branches' => $branches, 'areas' => $areas, 'job_shifts' => $job_shifts));
    }

    public function getjobtimings() {
        $job_shift = Input::get('job_shift');

        $shift_details = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('id', '=', $job_shift)->where('status', '=', 1)
                ->first();
        echo $shift_details->start_time . ' To ' . $shift_details->end_time;
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            
            $start_date = Input::get('start_date');
            $start_date = explode('-', $start_date);
            $start_date = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
            
            $shift_id = Input::get('job_shifts');
            $shifts=implode( ",",$shift_id );            
            $masterresourcemodel = new Masterresources();
            $masterresourcemodel->resource_type = 'BRANCH';
            $masterresourcemodel->name = Input::get('name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->area_id = Input::get('area');
            if (Input::get('area') == '') {
                $masterresourcemodel->area_id = NULL;
            }
            $masterresourcemodel->shift_id = $shifts;
            $masterresourcemodel->address = Input::get('address');
            $masterresourcemodel->latitude = Input::get('latitude');
            $masterresourcemodel->longitude = Input::get('longitude');
            $masterresourcemodel->branch_code = Input::get('branch_code');
            $masterresourcemodel->branch_phone = Input::get('branch_phone');
            $masterresourcemodel->branch_start_date = $start_date;
            $masterresourcemodel->opening_fund = Input::get('fund');
            
            $masterresourcemodel->status = 1;
            
            $fund_editable = Input::has('fund_editable');
           if($fund_editable==""){
                $masterresourcemodel->opening_fund_editable = 0;
           }else{
                 $masterresourcemodel->opening_fund_editable = 1;
           }
           // $masterresourcemodel->bottom_sale_line = Input::get('bottom_sale_line');
            $masterresourcemodel->bottom_sale_line = Input::get('bottom_sale_line_month');
            $masterresourcemodel->save();
            Toastr::success('Branch Successfully Created!', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/branch/add');
        }
    }

    public function getbranchregions() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $regions = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'REGION', 'company_id' => $company_id])->get();
        echo '<label>Choose Region</label><select class="commoSelect" name="region" id="regionselect">';
        echo '<option value="-1">Select Region</option>';
        foreach ($regions as $region) {
            echo "<option value='$region->id'>$region->name</option>";
        }
    }

    public function checkbranchcode() {

        $branch_code = Input::get('branch_code');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('branch_code', '=', $branch_code)
                ->where('resource_type', '=', 'BRANCH')
                ->where('id', '!=', $id)
                ->where('status', '!=', 2)
                ->get();
        //print_r(count($data));die('dsd');
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
         $job_shifts = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_SHIFT'])->where('status', '=', 1)->get();

       
        $areas = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id')
                ->where(['resource_type' => 'AREA', 'company_id' => $company_id])
                ->where(['status' => '1'])
                ->get();
     
        $branch = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();
        $branchmaps = DB::table('master_resources')->get();

        return view('masterresources/branch/edit', array('branch' => $branch, 'branchmaps' => $branchmaps, 'areas' => $areas, 'company_id' => $company_id, 'job_shifts' => $job_shifts));
    }

    public function update() {

        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');
            $shift_id = Input::get('job_shifts');
            $shifts=implode( ", ",$shift_id );   
            $area_id = Input::get('area');
            if (Input::get('region') == -1) {
                $region_id = NULL;
            }
            $address = Input::get('address');
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            $branch_code = Input::get('branch_code');
            $branch_phone = Input::get('branch_phone');
            $opening_fund = Input::get('fund');
            $fund_editable = Input::has('fund_editable');
            
            $start_date = Input::get('start_date');
            $start_date = explode('-', $start_date);
            $start_date = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];
            
            
           if($fund_editable == ""){
                $opening_fund_editable = 0;
           }else{
                 $opening_fund_editable = 1;
           }
            
           
            
            $bottom_sale_line = Input::get('bottom_sale_line_month');
            $branches = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $name, 'alias_name' => $alias_name, 'area_id' => $area_id, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'branch_code' => $branch_code, 'shift_id' => $shifts, 'opening_fund' => $opening_fund, 'bottom_sale_line' => $bottom_sale_line,'branch_start_date'=>$start_date,'branch_phone'=>$branch_phone]);
            Toastr::success('Branch Successfully Updated!', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/branch/edit/' . $dn);
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/branch/edit/' . $dn);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Branch Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Department Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Branch Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/branches');
        }
    }

              // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbyarea = Input::get('searchbyarea');
        $searchbycode = Input::get('searchbycode');

        $sortordname = Input::get('sortordname');
        $sortorderarea = Input::get('sortorderarea');
        $sortordercode = Input::get('sortordercode');

        $sortOrdDefault='';
        if($sortordname=='' && $sortorderarea=='' && $sortordercode==''){
            $sortOrdDefault='ASC';
        }

        $branches = DB::table('master_resources')
                ->select('master_resources.*','area.name as area_name')
                ->leftjoin('master_resources as area', 'master_resources.area_id', '=', 'area.id')
                ->where('master_resources.resource_type', '=', 'BRANCH')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbyarea, function ($query) use ($searchbyarea) {
                    return $query->whereRaw("(master_resources.area_id=$searchbyarea)");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(master_resources.branch_code like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortorderarea, function ($query) use ($sortorderarea) {
                    return $query->orderby('area.name', $sortorderarea);
                })
                ->when($sortordercode, function ($query) use ($sortordercode) {
                    return $query->orderby('master_resources.branch_code', $sortordercode);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('BranchList', function($excel) use($branches){
                 // Set the title
                $excel->setTitle('Branch List');
                
                $excel->sheet('Branch List', function($sheet) use($branches){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Branch List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Branch Name','Area Name','Branch Code',"Alias","Branch Phone"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($branches);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $branches[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $branches[$i]->area_name);
                        $sheet->setCellValue('D'.$chrRow, $branches[$i]->branch_code);
                        $sheet->setCellValue('E'.$chrRow, $branches[$i]->alias_name);
                        $sheet->setCellValue('F'.$chrRow, $branches[$i]->branch_phone);
                            
                        $sheet->cells('A'.$chrRow.':F'.$chrRow, function($cells) {
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
            <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Branch List</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Area Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                                <td style="padding:10px 5px;color:#fff;"> Branch Phone </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno=0;
            foreach ($branches as $branch) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $branch->name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $branch->area_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $branch->branch_code . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $branch->alias_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $branch->branch_phone . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_branch_list.pdf');
        }
    }
}
