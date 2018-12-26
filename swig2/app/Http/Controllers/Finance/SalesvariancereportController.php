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

class SalesvariancereportController extends Controller {

    public function index(Request $request) {
        $login_id = Session::get('login_id');
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $currentMonth = date('m');
        $current_year = date('Y');
        $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
        $currentQuarter = $arrMonthQuarter["$currentMonth"];
        
        
        $selected_quarter = $currentQuarter;
        

        if ($selected_quarter == 1) {
            $start_date = "$current_year-01-01";
            $end_date = "$current_year-03-31";
        }
        if ($selected_quarter == 2) {
            $start_date = "$current_year-04-01";
            $end_date = "$current_year-06-30";
        }
        if ($selected_quarter == 3) {
            $start_date = "$current_year-07-01";
            $end_date = "$current_year-09-30";
        }
        if ($selected_quarter == 4) {
            $start_date = "$current_year-10-01";
            $end_date = "$current_year-12-31";
        }
        
        $sales = DB::table('master_resources as m')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as quarter_sale,m.name as branch_name,m.branch_code as code,COALESCE(max(target.target_amount),0) as target_amount,COALESCE(max(target.target_amount)-sum(pos_sales.total_sale),0) as variance'))
                ->leftJoin('pos_sales', function($join) use ($start_date, $end_date, $company_id) {
                    $join->on('m.id', '=', 'pos_sales.branch_id')
                    ->whereraw("date(pos_sales.pos_date)>='$start_date' AND date(pos_sales.pos_date)<='$end_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                })
                ->leftjoin('sales_target as target', function($join) use ($current_year, $selected_quarter) {
                    $join->on('m.id', '=', 'target.branch_id')
                    ->whereraw("target.target_quarter=$selected_quarter AND target.target_year=$current_year");
                })
                ->where('m.resource_type', '=', 'BRANCH')
                ->where('m.status', '=', '1')
                ->groupby('m.id')
                ->orderby('quarter_sale', 'DESC')->orderby('branch_name', 'ASC')
                ->paginate($paginate);
        
                $totQuarterSale=$sales->sum("quarter_sale");
                $totTarget=$sales->sum("target_amount");
                $totVariance=$sales->sum("variance");
                
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
            $quarter = Input::get('quarter');
            $year = Input::get('year');
            $selected_quarter=$quarter;
            $current_year=$year;
            
            if ($selected_quarter == 1) {
                $start_date = "$current_year-01-01";
                $end_date = "$current_year-03-31";
            }
            if ($selected_quarter == 2) {
                $start_date = "$current_year-04-01";
                $end_date = "$current_year-06-30";
            }
            if ($selected_quarter == 3) {
                $start_date = "$current_year-07-01";
                $end_date = "$current_year-09-30";
            }
            if ($selected_quarter == 4) {
                $start_date = "$current_year-10-01";
                $end_date = "$current_year-12-31";
            }

            $sales = DB::table('master_resources as m')
                    ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as quarter_sale,m.name as branch_name,m.branch_code as code,COALESCE(max(target.target_amount),0) as target_amount,COALESCE(max(target.target_amount)-sum(pos_sales.total_sale),0) as variance'))
                    ->leftJoin('pos_sales', function($join) use ($start_date, $end_date, $company_id) {
                        $join->on('m.id', '=', 'pos_sales.branch_id')
                        ->whereraw("date(pos_sales.pos_date)>='$start_date' AND date(pos_sales.pos_date)<='$end_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                    })
                    ->leftjoin('sales_target as target', function($join) use ($current_year, $selected_quarter) {
                        $join->on('m.id', '=', 'target.branch_id')
                        ->whereraw("target.target_quarter=$selected_quarter AND target.target_year=$current_year");
                    })
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
                    ->orderby('quarter_sale', 'DESC')->orderby('branch_name', 'ASC')
                  ->paginate($paginate);
                   
                    $totQuarterSale=$sales->sum("quarter_sale");
                    $totTarget=$sales->sum("target_amount");
                    $totVariance=$sales->sum("variance");
                    
            return view('finance/salesvariance_report/sales_result', array('sales' => $sales, 'currentQuarter'=>$currentQuarter,'currentYear'=>$current_year,'totQuarterSale'=>$totQuarterSale,'totTarget'=>$totTarget,'totVariance'=>$totVariance));
        }
        

        return view('finance/salesvariance_report/index', array('sales' => $sales, "regions" => $regions,'currentQuarter'=>$currentQuarter,'currentYear'=>$current_year,'branches'=>$branches,'totQuarterSale'=>$totQuarterSale,'totTarget'=>$totTarget,'totVariance'=>$totVariance));
    }

    public function exporttaxreport() {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $excelorpdf = Input::get('excelorpdf');
        $region_id = Input::get('region_id');
        $branch_id = Input::get('branch_id');
        $branch_code = Input::get('searchbycode');
        $quarter = Input::get('cmbquarter');
        $year = Input::get('cmbyear');
        $selected_quarter=$quarter;
        $current_year=$year;

        if ($selected_quarter == 1) {
            $start_date = "$current_year-01-01";
            $end_date = "$current_year-03-31";
        }
        if ($selected_quarter == 2) {
            $start_date = "$current_year-04-01";
            $end_date = "$current_year-06-30";
        }
        if ($selected_quarter == 3) {
            $start_date = "$current_year-07-01";
            $end_date = "$current_year-09-30";
        }
        if ($selected_quarter == 4) {
            $start_date = "$current_year-10-01";
            $end_date = "$current_year-12-31";
        }

        $sales = DB::table('master_resources as m')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as quarter_sale,m.name as branch_name,m.branch_code as code,COALESCE(max(target.target_amount),0) as target_amount,COALESCE(max(target.target_amount)-sum(pos_sales.total_sale),0) as variance'))
                ->leftJoin('pos_sales', function($join) use ($start_date, $end_date, $company_id) {
                    $join->on('m.id', '=', 'pos_sales.branch_id')
                    ->whereraw("date(pos_sales.pos_date)>='$start_date' AND date(pos_sales.pos_date)<='$end_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                })
                ->leftjoin('sales_target as target', function($join) use ($current_year, $selected_quarter) {
                    $join->on('m.id', '=', 'target.branch_id')
                    ->whereraw("target.target_quarter=$selected_quarter AND target.target_year=$current_year");
                })
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
                ->orderby('quarter_sale', 'DESC')
                ->get();

               

        if ($excelorpdf == "EXCEL") {

            Excel::create('VarianceReport', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Sales Variance Report');

                $excel->sheet('Sales Variance Report', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('D3', 'Sales Variance Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Branch Code', 'Branch Name', 'Quarter Sale', 'Quarter Target', 'Sales %','Variance','Variance %'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    
                    $totQuarterSale=$sales->sum("quarter_sale");
                    $totTarget=$sales->sum("target_amount");
                    $totVariance=$sales->sum("variance");
                
                    for ($i = 0; $i < count($sales); $i++) {
                        
                        $salesper=0;
                        $varianceper=100;
                        if($sales[$i]->target_amount!=0){
                            $salesper=$sales[$i]->quarter_sale/$sales[$i]->target_amount*100;
                            $varianceper=100-$salesper;
                            
                        }
                        $salespercent=round($salesper,2);
                        $variancepercent=round($varianceper,2);
                        
                        $sheet->setCellValue('A' . $chrRow, $sales[$i]->code);
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->branch_name);
                        $sheet->setCellValue('C' . $chrRow, round($sales[$i]->quarter_sale,2));
                        $sheet->setCellValue('D' . $chrRow, round($sales[$i]->target_amount,2));
                        $sheet->setCellValue('E' . $chrRow, $salespercent);
                        $sheet->setCellValue('F' . $chrRow, round($sales[$i]->variance,2));
                        $sheet->setCellValue('G' . $chrRow, $variancepercent);

                        $sheet->cells('A' . $chrRow . ':G' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                    
                    $sheet->row($chrRow+1, array('', 'Total', round($totQuarterSale,2), round($totTarget,2), '',round($totVariance,2),''));
                });
            })->export('xls');
        } else {
            $totQuarterSale=$sales->sum("quarter_sale");
            $totTarget=$sales->sum("target_amount");
            $totVariance=$sales->sum("variance");
                    
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
                <div style="text-align:center;"><h1>Sales Variance Report</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -25px" box-sizing: border-box; cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Branch Code</td>
						<td style="padding:10px 5px;color:#fff;"> Branch Name </td>
						<td style="padding:10px 5px;color:#fff;"> Quarter Sale </td>
						<td style="padding:10px 5px;color:#fff;"> Quarter Target </td>
                                                <td style="padding:10px 5px;color:#fff;"> Sales % </td>
                                                <td style="padding:10px 5px;color:#fff;"> Variance </td>
                                                <td style="padding:10px 5px;color:#fff;"> Variance % </td>
                                               
					</tr>
				</thead>
				
				<tbody class="pos" id="pos" >';
            foreach ($sales as $sale) {
                
                $salesper=0;
                $varianceper=100;
                if($sale->target_amount!=0){
                    $salesper=$sale->quarter_sale/$sale->target_amount*100;
                    $varianceper=100-$salesper;
                }

                $salespercent=Customhelper::numberformatter($salesper);
                $variancepercent=Customhelper::numberformatter($varianceper);

                
                $html_table .='<tr>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;">' . $sale->code . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . $sale->branch_name . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . Customhelper::numberformatter($sale->quarter_sale) . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($sale->target_amount). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . $salespercent . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($sale->variance) . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . $variancepercent . '</td>
                                        </tr>';
                                    }
                                    
                                     $html_table .='<tr>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;"></td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">Total</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . Customhelper::numberformatter($totQuarterSale) . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($totTarget). '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;"></td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($totVariance) . '</td>
                                        <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;"></td>
                                        </tr>';
                                     
                                    
                                    $html_table .='</tbody>
                                                </table>
                                        </section>
                                </body>
                        </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('variance_report.pdf');
        }
    }
    

}
