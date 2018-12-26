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
use App\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;
use App;
use PDF;
use Excel;
use Customhelper;

class TaxreportController extends Controller {

    public function index(Request $request) {
        $login_id = Session::get('login_id');
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = Input::get('region_id');
        if ($region_id < 1) {
            $region_id = "";
        }
        $first_day = Input::get('start_date');
        $last_day = Input::get('end_date');
        if ($first_day == '') {
            $first_day = date('Y-m-01');
        } else {

            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day == '') {
            $last_day = date('Y-m-t');
        } else {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $tax_details = DB::table('master_resources')
                        ->select('tax_percent', 'tax_applicable_from', 'name')
                        ->where('resource_type', '=', "TAX")->where('status', '=', 1)
                        ->orderBy('id', 'desc')->first();
  
        $taxpercent = $tax_details->tax_percent;
        
                if ($taxpercent == "") {
                    $taxpercent = 5;
                }  

        $sales = DB::table('master_resources as m')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,m.name as branch_name,m.branch_code as code,sum(pos_sales.tax_in_mis) as tax_amount'))
                ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                    $join->on('m.id', '=', 'pos_sales.branch_id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                })
                //->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->where('m.resource_type', '=', 'BRANCH')
                ->where('m.status', '=', '1')
                ->groupby('m.id')
                ->orderby('total_sale', 'DESC')
                ->paginate($paginate);
                
               
                
                $sum_total_sales=$sales->sum("total_sale");
                $sum_total_tax=$sales->sum("tax_amount");
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
            $first_day = Input::get('start_date');
            $last_day = Input::get('end_date');

            if ($first_day != '') {
                $first_day = explode('-', $first_day);
                $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
            }
            if ($last_day != '') {
                $last_day = explode('-', $last_day);
                $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
            }

            $region_id = Input::get('region_id');
            $branch_id = Input::get('branch_id');
            $branch_code = Input::get('branch_code');
            if ($region_id < 1) {
                $region_id = "";
            }
            $sales = DB::table('master_resources as m')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,m.name as branch_name,m.branch_code as code,sum(pos_sales.tax_in_mis) as tax_amount'))
                    ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                        $join->on('m.id', '=', 'pos_sales.branch_id')
                        ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
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
                        return $query->where('m.id', '=', $branch_code);
                    })
                    ->where('m.resource_type', '=', 'BRANCH')
                    ->where('m.status', '=', '1')
                    ->groupby('m.id')
                    ->orderby('total_sale', 'DESC')
                    ->paginate($paginate);
            //     ->toSql();die('asd');
                    $sum_total_sales=$sales->sum("total_sale");
                    $sum_total_tax=$sales->sum("tax_amount");
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
            return view('finance/tax_report/sales_result', array('sales' => $sales, 'periodStartDate' => $first_day, 'periodEndDate' => $last_day, "regions" => $regions, "region_id" => $region_id, "branches" => $branches,"sum_total_sales" => $sum_total_sales,"sum_total_tax" => $sum_total_tax,'taxpercent'=>$taxpercent));
        }
        $first_day = date('d-m-Y', strtotime($first_day));
        $last_day = date('d-m-Y', strtotime($last_day));
        return view('finance/tax_report/index', array('sales' => $sales, 'periodStartDate' => $first_day, 'periodEndDate' => $last_day, "regions" => $regions, "region_id" => $region_id, "branches" => $branches,"sum_total_sales" => $sum_total_sales,"sum_total_tax" => $sum_total_tax,'taxpercent'=>$taxpercent));
    }

    public function exporttaxreport() {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $excelorpdf = Input::get('excelorpdf');

        $first_day = Input::get('start_date');
        $last_day = Input::get('end_date');
        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }
        $region_id = Input::get('region_id');
        $branch_id = Input::get('branch_id');
        $branch_code = Input::get('branch_code');
        if ($region_id < 1) {
            $region_id = "";
        }
        $sales = DB::table('master_resources as m')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,m.name as branch_name,m.branch_code as code,sum(pos_sales.tax_in_mis) as tax_amount'))
                ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                    $join->on('m.id', '=', 'pos_sales.branch_id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
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
                    return $query->where('m.id', '=', $branch_code);
                })
                ->where('m.resource_type', '=', 'BRANCH')
                ->where('m.status', '=', '1')
                ->groupby('m.id')
                ->orderby('total_sale', 'DESC')
                ->get();
                
                $sum=$sales->sum("total_sale");
                
           
            


                   

        if ($excelorpdf == "EXCEL") {

            Excel::create('TaxReport', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Tax Report');

                $excel->sheet('Tax Report', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('D3', 'Tax Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Branch Code', 'Branch Name', 'Total Sale', 'Net Sale', 'Tax Amount'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                
      
                       $netSale=0;
                       $totaltax=0;
                       $saleAmount=0;
                       $cash_percent=0;
                       $tax_percent=0;
                       
     $taxDetails = DB::table('master_resources')
                        ->select('tax_percent', 'tax_applicable_from', 'name')
                        ->where('resource_type', '=', "TAX")->where('status', '=', 1)
                        ->orderBy('id', 'desc')->first();
  
                 
                        $taxpercent = $taxDetails->tax_percent;
        
                          if ($taxpercent == "") {
                         $taxpercent = 5;
                             }  
                
                       
                       
                    for ($i = 0; $i < count($sales); $i++) {

                    
                      $saleAmount= $sales[$i]->total_sale;
                   if($sales[$i]->total_sale==""){
                       $saleAmount=0;
                   } 
            $cash_percent=Customhelper::calculate_vat($taxpercent,$saleAmount);
                 
                 
                       $tax_percent=$saleAmount-$cash_percent;
                       $netSale+=$cash_percent;
                       $totaltax+=$tax_percent;  
                       
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->code);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->branch_name);
                        $sheet->setCellValue('D' . $chrRow, Customhelper::numberformatter($saleAmount));
                        $sheet->setCellValue('E' . $chrRow, Customhelper::numberformatter($cash_percent));
                        $sheet->setCellValue('F' . $chrRow, Customhelper::numberformatter($tax_percent));

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
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
                <div style="text-align:center;"><h1>Tax Report</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -25px" box-sizing: border-box; cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Branch Code</td>
						<td style="padding:10px 5px;color:#fff;"> Branch Name </td>
						<td style="padding:10px 5px;color:#fff;"> Total Sale </td>
						<td style="padding:10px 5px;color:#fff;"> Net Sale </td>
                                                <td style="padding:10px 5px;color:#fff;"> Tax Amount  </td>
                                               
					</tr>
				</thead>
				
				<tbody class="pos" id="pos" >';
                       $netSale=0;
                       $totaltax=0;
                       $saleAmount=0;
                       $cash_percent=0;
                       $tax_percent=0;
                       
            foreach ($sales as $sale) {
                
                 
                      $saleAmount= $sale->total_sale;
                   if($sale->total_sale==""){
                       $saleAmount=0;
                   } 
                  
                   $cash_percent=Customhelper::calculate_vat($taxpercent,$saleAmount);
                 
                       $tax_percent=$saleAmount-$cash_percent;
                       $netSale+=$cash_percent;
                       $totaltax+=$tax_percent;  
                
               
                $html_table .='<tr>
                <td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;">' . $sale->code . '</td>
		<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . $sale->branch_name . '</td>
		<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . Customhelper::numberformatter($saleAmount) . '</td>
		<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($cash_percent). '</td>
		<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . Customhelper::numberformatter($tax_percent) . '</td>
		</tr>';
            }
            $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('tax_report.pdf');
        }
    }

}
