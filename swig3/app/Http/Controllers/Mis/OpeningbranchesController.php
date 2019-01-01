<?php

namespace App\Http\Controllers\Mis;

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
use DB;
use App;
use PDF;
use Excel;

class OpeningbranchesController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $currentDate=date("Y-m-d");
        
        $branches = DB::table('master_resources')
                ->select('master_resources.*','area.name as area','region.name as region')
                ->leftjoin('master_resources as area', 'master_resources.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("date(master_resources.branch_start_date)>='$currentDate' AND master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->orderby('master_resources.name', 'ASC')
                ->paginate($paginate);
        
        $areas = DB::table('master_resources as area')
                ->select('area.id as id', 'area.name as area_name')->distinct()
                ->where('area.resource_type', '=', 'AREA')
                ->where('area.status', '=', 1)
                ->get();
        
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
                
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbycode = Input::get('searchbycode');
            $search_key = Input::get('search_key');
            $searchbyarea = Input::get('searchbyarea');
            $searchbyregion = Input::get('searchbyregion');
            $startfrom = Input::get('start_date');
            $endfrom = Input::get('end_date');
            
            $sortordname = Input::get('sortordname');
            $sortordarea = Input::get('sortordarea');
            $sortordcode = Input::get('sortordcode');
            $sortordregion = Input::get('sortordregion');
            $sortorddate = Input::get('sortorddate');
            
            $sortOrdDefault='';
            if($sortorddate=='' && $sortordregion=='' && $sortordarea=='' ){
                $sortOrdDefault='ASC';
            }
            
            if ($startfrom != '') {
                $startfrom = explode('-', $startfrom);
                $startfrom = $startfrom[2] . '-' . $startfrom[1] . '-' . $startfrom[0];
            }
            if ($endfrom != '') {
                $endfrom = explode('-', $endfrom);
                $endfrom = $endfrom[2] . '-' . $endfrom[1] . '-' . $endfrom[0];
            }
                
            $branches = DB::table('master_resources')
                        ->select('master_resources.*','area.name as area','region.name as region')
                        ->leftjoin('master_resources as area', 'master_resources.area_id', '=', 'area.id')
                        ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                        ->where('master_resources.company_id', '=', $company_id)
                        ->whereRaw("date(master_resources.branch_start_date)>='$currentDate' AND master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("master_resources.branch_code like '$searchbycode%'");
                        })
                        ->when($search_key, function ($query) use ($search_key) {
                            return $query->whereRaw("master_resources.name like '$search_key%'");
                        })
                        ->when($searchbyarea, function ($query) use ($searchbyarea) {
                            return $query->where('master_resources.area_id', '=', $searchbyarea);
                        })
                        ->when($searchbyregion, function ($query) use ($searchbyregion) {
                            return $query->whereRaw("area.region_id=$searchbyregion");
                        })
                        ->when($startfrom, function ($query) use ($startfrom) {
                            return $query->whereRaw("date(master_resources.branch_start_date) >= '$startfrom' ");
                        })
                        ->when($endfrom, function ($query) use ($endfrom) {
                            return $query->whereRaw("date(master_resources.branch_start_date)<= '$endfrom' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('master_resources.branch_code', $sortordcode);
                        })
                        ->when($sortordname, function ($query) use ($sortordname) {
                            return $query->orderby('master_resources.name', $sortordname);
                        })
                        ->when($sortordarea, function ($query) use ($sortordarea) {
                            return $query->orderby('area.name', $sortordarea);
                        })
                        ->when($sortordregion, function ($query) use ($sortordregion) {
                            return $query->orderby('region.name', $sortordregion);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('master_resources.branch_start_date', $sortorddate);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('master_resources.name', $sortOrdDefault);

                        })
                        ->paginate($paginate);

            return view('mis/opening_branches/result', array('branches'=>$branches));
        }

        return view('mis/opening_branches/index', array('branches'=>$branches,'areas'=>$areas,'regions'=>$regions));
    }



        // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $search_key = Input::get('search_key');
        $searchbyarea = Input::get('searchbyarea');
        $searchbyregion = Input::get('searchbyregion');
        $startfrom = Input::get('start_date');
        $endfrom = Input::get('end_date');

        $sortordname = Input::get('sortordname');
        $sortordarea = Input::get('sortordarea');
        $sortordcode = Input::get('sortordcode');
        $sortordregion = Input::get('sortordregion');
        $sortorddate = Input::get('sortorddate');
        
        $currentDate=date("Y-m-d");

        $sortOrdDefault='';
        if($sortorddate=='' && $sortordregion=='' && $sortordarea=='' ){
            $sortOrdDefault='ASC';
        }

        if ($startfrom != '') {
            $startfrom = explode('-', $startfrom);
            $startfrom = $startfrom[2] . '-' . $startfrom[1] . '-' . $startfrom[0];
        }
        if ($endfrom != '') {
            $endfrom = explode('-', $endfrom);
            $endfrom = $endfrom[2] . '-' . $endfrom[1] . '-' . $endfrom[0];
        }

        $branches = DB::table('master_resources')
                ->select('master_resources.*','area.name as area','region.name as region')
                ->leftjoin('master_resources as area', 'master_resources.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("date(master_resources.branch_start_date)>='$currentDate' AND master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("master_resources.branch_code like '$searchbycode%'");
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("master_resources.name like '$search_key%'");
                })
                ->when($searchbyarea, function ($query) use ($searchbyarea) {
                    return $query->where('master_resources.area_id', '=', $searchbyarea);
                })
                ->when($searchbyregion, function ($query) use ($searchbyregion) {
                    return $query->whereRaw("area.region_id=$searchbyregion");
                })
                ->when($startfrom, function ($query) use ($startfrom) {
                    return $query->whereRaw("date(master_resources.branch_start_date) >= '$startfrom' ");
                })
                ->when($endfrom, function ($query) use ($endfrom) {
                    return $query->whereRaw("date(master_resources.branch_start_date)<= '$endfrom' ");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('master_resources.branch_code', $sortordcode);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortordarea, function ($query) use ($sortordarea) {
                    return $query->orderby('area.name', $sortordarea);
                })
                ->when($sortordregion, function ($query) use ($sortordregion) {
                    return $query->orderby('region.name', $sortordregion);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('master_resources.branch_start_date', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);

                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('NewlyOpeningBranches', function($excel) use($branches){
                 // Set the title
                $excel->setTitle('Newly Opening Branches');
                
                $excel->sheet('Newly Opening Branches', function($sheet) use($branches){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Newly Opening Branches');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:H3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array("Code",'Branch Name','Start Date','Area','Region','Alias Name','Address'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($branches);$i++){
                        $date1=date("d-m-Y", strtotime($branches[$i]->branch_start_date));
                        
                        $sheet->setCellValue('A'.$chrRow, $branches[$i]->branch_code);
                        $sheet->setCellValue('B'.$chrRow, $branches[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $date1);
                        $sheet->setCellValue('D'.$chrRow, $branches[$i]->area);
                        $sheet->setCellValue('E'.$chrRow, $branches[$i]->region);
                        $sheet->setCellValue('F'.$chrRow, $branches[$i]->alias_name);
                        $sheet->setCellValue('G'.$chrRow, $branches[$i]->address);
                            
                        $sheet->cells('A'.$chrRow.':G'.$chrRow, function($cells) {
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
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h1>Newly Opening Branches</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 5px;color:#fff;"> Code </td>
                                <td style="padding:10px 5px;color:#fff;"> Branch Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Start Date</td>
                                <td style="padding:10px 5px;color:#fff;"> Area</td>
                                <td style="padding:10px 5px;color:#fff;"> Region </td>
                                <td style="padding:10px 5px;color:#fff;"> Alias Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Address </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($branches as $cat) {
                
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->branch_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($cat->branch_start_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->area . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->region . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->address . '</td>
                                  
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_opening_branches.pdf');
        }
    }
}
