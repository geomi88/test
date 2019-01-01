<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Customhelper;
use DB;
use App;
use PDF;
use Excel;

class MinimumsalesplanController extends Controller {

    public function index(Request $request) {
        $login_id = Session::get('login_id');
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $current_year = date('Y');
        
        $sales = DB::table('master_resources as m')
                ->select('m.branch_code as code','m.name as branch_name',
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=1 AND target_year=$current_year) as q1target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=2 AND target_year=$current_year) as q2target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=3 AND target_year=$current_year) as q3target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=4 AND target_year=$current_year) as q4target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_year=$current_year) as total"))
                ->where('m.resource_type', '=', 'BRANCH')
                ->where('m.status', '=', '1')
                ->groupby('m.id')
                ->orderby('branch_name', 'ASC')
                ->paginate($paginate);
        
                $totq1target=$sales->sum("q1target");
                $totq2target=$sales->sum("q2target");
                $totq3target=$sales->sum("q3target");
                $totq4target=$sales->sum("q4target");
                $total=$sales->sum("total");
               
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
        
        $branches = DB::table('master_resources as branch_details')
                ->select('branch_details.id as id', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'branch_details.alias_name as branch_alias')->distinct()
                ->where('branch_details.resource_type', '=', 'BRANCH')
                ->where('branch_details.status', '=', 1)
                ->get();
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $region_id = Input::get('region_id');
            $branch_id = Input::get('branch_id');
            $branch_code = Input::get('branch_code');
            $year = Input::get('year');
            $current_year=$year;

            $sales = DB::table('master_resources as m')
                    ->select('m.branch_code as code','m.name as branch_name',
                            DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=1 AND target_year=$current_year) as q1target"),
                            DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=2 AND target_year=$current_year) as q2target"),
                            DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=3 AND target_year=$current_year) as q3target"),
                            DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=4 AND target_year=$current_year) as q4target"),
                            DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_year=$current_year) as total"))
                    ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($branch_id, function ($query) use ($branch_id) {
                        return $query->where('m.id', '=', $branch_id);
                    })
                    ->when($branch_code, function ($query) use ($branch_code) {
                        return $query->whereRaw("m.branch_code like '$branch_code%'");
                    })
                    ->where('m.resource_type', '=', 'BRANCH')
                    ->where('m.status', '=', '1')
                    ->groupby('m.id')
                    ->orderby('branch_name', 'ASC')
                    ->paginate($paginate);
                    
                    $totq1target=$sales->sum("q1target");
                    $totq2target=$sales->sum("q2target");
                    $totq3target=$sales->sum("q3target");
                    $totq4target=$sales->sum("q4target");
                    $total=$sales->sum("total");
                    
            return view('finance/minimum_sales_plan/sales_result', array('sales' => $sales,'totq1target'=>$totq1target,'totq2target'=>$totq2target,'totq3target'=>$totq3target,'totq4target'=>$totq4target,'tottotal'=>$total));
        }
        

        return view('finance/minimum_sales_plan/index', array('sales' => $sales, "regions" => $regions,'currentYear'=>$current_year,'branches'=>$branches,'totq1target'=>$totq1target,'totq2target'=>$totq2target,'totq3target'=>$totq3target,'totq4target'=>$totq4target,'tottotal'=>$total));
    }

    public function exportdata() {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $region_id = Input::get('region_id');
        $branch_id = Input::get('branch_id');
        $branch_code = Input::get('branch_code');
        $year = Input::get('cmbyear');
        $current_year=$year;

        $sales = DB::table('master_resources as m')
                ->select('m.branch_code as code','m.name as branch_name',
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=1 AND target_year=$current_year) as q1target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=2 AND target_year=$current_year) as q2target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=3 AND target_year=$current_year) as q3target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_quarter=4 AND target_year=$current_year) as q4target"),
                        DB::raw("(select COALESCE(sum(target_amount),0) from sales_target where branch_id=m.id AND target_year=$current_year) as total"))
                ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    return $query->where('m.id', '=', $branch_id);
                })
                ->when($branch_code, function ($query) use ($branch_code) {
                    return $query->whereRaw("m.branch_code like '$branch_code%'");
                })
                ->where('m.resource_type', '=', 'BRANCH')
                ->where('m.status', '=', '1')
                ->groupby('m.id')
                ->orderby('branch_name', 'ASC')
               ->get();

                
                $totq1target=$sales->sum("q1target");
                $totq2target=$sales->sum("q2target");
                $totq3target=$sales->sum("q3target");
                $totq4target=$sales->sum("q4target");
                $total=$sales->sum("total");

        if ($excelorpdf == "EXCEL") {

            Excel::create('MinimumSalesPlan', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Minimum Sales Plan');

                $excel->sheet('Minimum Sales Plan', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('D3', 'Minimum Sales Plan');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Branch Code', 'Branch Name', 'Quarter 1 Target', 'Quarter 2 Target', 'Quarter 3 Target','Quarter 4 Target','Total'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    
                    $totq1target=$sales->sum("q1target");
                    $totq2target=$sales->sum("q2target");
                    $totq3target=$sales->sum("q3target");
                    $totq4target=$sales->sum("q4target");
                    $total=$sales->sum("total");
                
                    for ($i = 0; $i < count($sales); $i++) {
                        
                        $sheet->setCellValue('A' . $chrRow, $sales[$i]->code);
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->branch_name);
                        $sheet->setCellValue('C' . $chrRow, round($sales[$i]->q1target,2));
                        $sheet->setCellValue('D' . $chrRow, round($sales[$i]->q2target,2));
                        $sheet->setCellValue('E' . $chrRow, round($sales[$i]->q3target,2));
                        $sheet->setCellValue('F' . $chrRow, round($sales[$i]->q4target,2));
                        $sheet->setCellValue('G' . $chrRow, round($sales[$i]->total,2));


                        $sheet->cells('A' . $chrRow . ':G' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                    
                    $sheet->row($chrRow+1, array('', 'Total', round($totq1target,2), round($totq2target,2), round($totq3target,2),round($totq4target,2),round($total,2)));
                });
            })->export('xls');
        } else {
            
                    
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
                <div style="text-align:center;"><h1>Minimum Sales Plan</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -25px" box-sizing: border-box; cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Branch Code</td>
						<td style="padding:10px 5px;color:#fff;"> Branch Name </td>
						<td style="padding:10px 5px;color:#fff;"> Quarter 1 Target </td>
						<td style="padding:10px 5px;color:#fff;"> Quarter 2 Target </td>
                                                <td style="padding:10px 5px;color:#fff;"> Quarter 3 Target </td>
                                                <td style="padding:10px 5px;color:#fff;"> Quarter 4 Target </td>
                                                <td style="padding:10px 5px;color:#fff;"> Total </td>
                                               
					</tr>
				</thead>
				
				<tbody class="pos" id="pos" >';
            foreach ($sales as $sale) {
                
                
                $html_table .='<tr>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;">' . $sale->code . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . $sale->branch_name . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . Customhelper::numberformatter($sale->q1target) . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($sale->q2target). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($sale->q3target). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($sale->q4target). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($sale->total). '</td>
                                        </tr>';
                                    }
                                    
                                     $html_table .='<tr>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;"></td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">Total</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . Customhelper::numberformatter($totq1target) . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($totq2target). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($totq3target). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($totq4target). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($total). '</td>
                                        </tr>';
                                     
                                    
                                    $html_table .='</tbody>
                                                </table>
                                        </section>
                                </body>
                        </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('minsalesplan_report.pdf');
        }
    }
    

}
