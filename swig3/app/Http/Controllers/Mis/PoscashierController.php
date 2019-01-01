<?php

namespace App\Http\Controllers\Mis;

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
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Mis\URL;
use Kamaln7\Toastr\Facades\Toastr;
use App;
use PDF;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Pos_sale;
use DB;

class PoscashierController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $pos_sales = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as supervisor_fname', 'employee_details1.alias_name as supervisor_aname','employee_details2.first_name as editedby_name','employee_details2.alias_name as editedby_aname','branch_details.branch_code as branch_code')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                ->whereRaw("pos_sales.added_by_user_type= 'Cashier'")
                ->whereRaw("pos_sales.status=1")
                ->paginate($paginate);
        $branch_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('branch_details.name as branch_name','branch_details.branch_code as branch_code', 'branch_details.id as branch_id')->distinct()
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->get();
        $shift_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('jobshift_details.name as jobshift_name', 'jobshift_details.id as jobshift_id')->distinct()
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->get();

        if ($request->ajax()) {
            $searchkey = Input::get('searchkey');
            $sup_searchkey = Input::get('sup_searchkey');
            $edit_searchkey = Input::get('edit_searchkey');
             $branch = Input::get('branch');
            $sort = Input::get('sorting');
          $editedsort = Input::get('editedsort');
          
            
            $cashsort = Input::get('cashsort');
            $tipssort = Input::get('tipssorting');
            $bsort = Input::get('bsort');
            $ssort = Input::get('ssort');
            $osort = Input::get('osort');
            $cashorder = Input::get('corder');
            $cashamount = Input::get('camount');
            $aorder = Input::get('aorder');
            $aamount = Input::get('aamount');
            $tipamount = Input::get('tamount');
            $tiporder = Input::get('torder');
            $shift = Input::get('shift');
            $created = Input::get('startdate');
            $ended = Input::get('enddate');
            if ($created != '') {
                $created = explode('-', $created);
                $created = $created[2] . '-' . $created[1] . '-' . $created[0];
            }
            if ($ended != '') {
                $ended = explode('-', $ended);
                $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
            }
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $pos_sales = DB::table('pos_sales')
                    ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as supervisor_fname', 'employee_details1.alias_name as supervisor_aname','employee_details2.first_name as editedby_name','employee_details2.alias_name as editedby_aname','branch_details.branch_code as branch_code')
                 ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.cashier_id')
                    ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.employee_id')
                    ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                    ->where('pos_sales.added_by_user_type', '=', 'Cashier')
                    ->where('pos_sales.status', '=', '1')
                    ->when($created, function ($query) use ($created, $ended) {
                        return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                    })
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                    })
                    ->when($sup_searchkey, function ($query) use ($sup_searchkey) {
                        return $query->whereRaw("(employee_details1.first_name like '$sup_searchkey%' or concat(employee_details1.alias_name) like '$sup_searchkey%')");
                    }) 
                    ->when($edit_searchkey, function ($query) use ($edit_searchkey) {
                        return $query->whereRaw("(employee_details2.first_name like '$edit_searchkey%' or concat(employee_details2.alias_name) like '$edit_searchkey%')");
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('employee_details.first_name', $sort);
                    }) 
                    ->when($editedsort, function ($query) use ($editedsort) {
                        return $query->orderby('employee_details2.first_name', $editedsort);
                    })
                    ->when($ssort, function ($query) use ($ssort) {
                        return $query->orderby('employee_details1.first_name', $ssort);
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->where('branch_details.id', '=', $branch);
                    })
                    ->when($shift, function ($query) use ($shift) {
                        return $query->where('jobshift_details.id', '=', $shift);
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.cash_collection $cashorder $cashamount ");
                    })
                    ->when($tipamount, function ($query) use ($tipamount, $tiporder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.tips_collected $tiporder $tipamount");
                    })
                    ->when($aamount, function ($query) use ($aamount, $aorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.opening_amount $aorder $aamount ");
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('pos_sales.cash_collection', $cashsort);
                    })
                    ->when($tipssort, function ($query) use ($tipssort) {
                        return $query->orderby('pos_sales.tips_collected', $tipssort);
                    })
                    ->when($osort, function ($query) use ($osort) {
                        return $query->orderby('pos_sales.opening_amount', $osort);
                    })
                    ->when($bsort, function ($query) use ($bsort) {
                        return $query->orderby('branch_name', $bsort);
                    })
                    ->paginate($paginate);
            return view('mis/pos_sales/cashier_report_result', array('pos_sales' => $pos_sales));
        }

        return view('mis/pos_sales/cashierreports', array('pos_sales' => $pos_sales, 'branch_names' => $branch_names, 'shift_names' => $shift_names));
    }

    public function show($id) {
        $id = \Crypt::decrypt($id);
        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as supervisor_fname', 'employee_details1.alias_name as supervisor_aname','employee_details2.first_name as editedby_name','employee_details2.alias_name as editedby_aname')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                ->where('pos_sales.added_by_user_type', '=', 'Cashier')
                ->where('pos_sales.id', '=', $id)
                ->first();
        return view('mis/pos_sales/cashier_show', array('sale_details_data' => $sale_details_data));
    }

    public function exporttopdf() {

     /*   $searchkey = Input::get('searchkey');
        $sup_searchkey = Input::get('sup_searchkey');
        $branch = Input::get('branch');
        $sort = Input::get('sortsimp');
        $cashsort = Input::get('cashsort');
        $bsort = Input::get('bsort');
        $tsort = Input::get('tipsort');
        $ssort = Input::get('ssort');
        $osort = Input::get('osort');
        $cashorder = Input::get('corder');
         $cashamount = Input::get('camount');
        $tipsamount = Input::get('tamount');
        $tipsorder = Input::get('torder');
       
        $aorder = Input::get('aorder');
        $aamount = Input::get('aamount');
        $shift = Input::get('shift');
        $created = Input::get('start_date');
        $ended = Input::get('end_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }
        $pos_sales = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as supervisor_fname', 'employee_details1.alias_name as supervisor_aname','branch_details.branch_code as branch_code')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.employee_id')
                ->where('pos_sales.added_by_user_type', '=', 'Cashier')
                ->where('pos_sales.status', '=', '1')
                ->when($created, function ($query) use ($created, $ended) {
                    return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                })
                ->when($searchkey, function ($query) use ($searchkey) {
                    return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                })
                ->when($sup_searchkey, function ($query) use ($sup_searchkey) {
                        return $query->whereRaw("(employee_details1.first_name like '$sup_searchkey%' or concat(employee_details1.alias_name) like '$sup_searchkey%')");
                    })
                ->when($sort, function ($query) use ($sort) {
                    return $query->orderby('employee_details.first_name', $sort);
                })
                ->when($ssort, function ($query) use ($ssort) {
                    return $query->orderby('employee_details1.first_name', $ssort);
                })
                ->when($branch, function ($query) use ($branch) {
                    return $query->where('branch_details.id', '=', $branch);
                })
                ->when($shift, function ($query) use ($shift) {
                    return $query->where('jobshift_details.id', '=', $shift);
                })
                ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.cash_collection $cashorder $cashamount ");
                }) 
                ->when($tipsamount, function ($query) use ($tipsamount, $tipsorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.tips_collected $tipsorder $tipsamount ");
                })
                ->when($aamount, function ($query) use ($aamount, $aorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.opening_amount $aorder $aamount ");
                })
                ->when($cashsort, function ($query) use ($cashsort) {
                    return $query->orderby('pos_sales.cash_collection', $cashsort);
                })
                ->when($osort, function ($query) use ($osort) {
                    return $query->orderby('pos_sales.opening_amount', $osort);
                })
               ->when($tsort, function ($query) use ($tsort) {
                    return $query->orderby('pos_sales.tips_collected', $tsort);
                })
                ->when($bsort, function ($query) use ($bsort) {
                    return $query->orderby('branch_name', $bsort);
                })
                ->get(); */
        
         $searchkey = Input::get('searchkey');
            $sup_searchkey = Input::get('sup_searchkey');
            $edit_searchkey = Input::get('edit_searchkey');
             $branch = Input::get('branch');
            $sort = Input::get('sorting');
          $editedsort = Input::get('editedsort');
          
            
            $cashsort = Input::get('cashsort');
            $tipssort = Input::get('tipssorting');
            $bsort = Input::get('bsort');
            $ssort = Input::get('ssort');
            $osort = Input::get('osort');
            $cashorder = Input::get('corder');
            $cashamount = Input::get('camount');
            $aorder = Input::get('aorder');
            $aamount = Input::get('aamount');
            $tipamount = Input::get('tamount');
            $tiporder = Input::get('torder');
            $shift = Input::get('shift');
            $created = Input::get('startdate');
            $ended = Input::get('enddate');
            if ($created != '') {
                $created = explode('-', $created);
                $created = $created[2] . '-' . $created[1] . '-' . $created[0];
            }
            if ($ended != '') {
                $ended = explode('-', $ended);
                $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
            }
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $pos_sales = DB::table('pos_sales')
                    ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as supervisor_fname', 'employee_details1.alias_name as supervisor_aname','employee_details2.first_name as editedby_name','employee_details2.alias_name as editedby_aname','branch_details.branch_code as branch_code')
                 ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.cashier_id')
                    ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.employee_id')
                    ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                    ->where('pos_sales.added_by_user_type', '=', 'Cashier')
                    ->where('pos_sales.status', '=', '1')
                    ->when($created, function ($query) use ($created, $ended) {
                        return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                    })
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                    })
                    ->when($sup_searchkey, function ($query) use ($sup_searchkey) {
                        return $query->whereRaw("(employee_details1.first_name like '$sup_searchkey%' or concat(employee_details1.alias_name) like '$sup_searchkey%')");
                    }) 
                    ->when($edit_searchkey, function ($query) use ($edit_searchkey) {
                        return $query->whereRaw("(employee_details2.first_name like '$edit_searchkey%' or concat(employee_details2.alias_name) like '$edit_searchkey%')");
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('employee_details.first_name', $sort);
                    }) 
                    ->when($editedsort, function ($query) use ($editedsort) {
                        return $query->orderby('employee_details2.first_name', $editedsort);
                    })
                    ->when($ssort, function ($query) use ($ssort) {
                        return $query->orderby('employee_details1.first_name', $ssort);
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->where('branch_details.id', '=', $branch);
                    })
                    ->when($shift, function ($query) use ($shift) {
                        return $query->where('jobshift_details.id', '=', $shift);
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.cash_collection $cashorder $cashamount ");
                    })
                    ->when($tipamount, function ($query) use ($tipamount, $tiporder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.tips_collected $tiporder $tipamount");
                    })
                    ->when($aamount, function ($query) use ($aamount, $aorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.opening_amount $aorder $aamount ");
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('pos_sales.cash_collection', $cashsort);
                    })
                    ->when($tipssort, function ($query) use ($tipssort) {
                        return $query->orderby('pos_sales.tips_collected', $tipssort);
                    })
                    ->when($osort, function ($query) use ($osort) {
                        return $query->orderby('pos_sales.opening_amount', $osort);
                    })
                    ->when($bsort, function ($query) use ($bsort) {
                        return $query->orderby('branch_name', $bsort);
                    })
                    ->get();

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
                <div style="text-align:center; font-size: 14px;"><h1>POS Cashier Sales Report</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -25px" box-sizing: border-box; cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Date</td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px; word-break:break-word;"> Cashier Name </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Branch Name </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Shift Name </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Cash Collection </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Tips Collection </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px; word-break:break-word;"> Opening Amount </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px; word-break:break-word;"> Supervisor Name </td>
                                                <td style="padding:10px 5px;color:#fff; font-size: 12px;"> Edited By </td>
					</tr>
				</thead>
				<thead class="listHeaderBottom">
					<tr class="headingHolder">
						<td class="filterFields"></td>
						<td></td>
						<td class=""></td>
						<td class="filterFields"></td>
						<td class="filterFields"></td>
						<td class="filterFields"></td>
                                                <td class="filterFields"></td>
                                                <td class="filterFields"></td>
						<td></td>
					</tr>
				</thead>
				<tbody class="pos" id="pos" >';
        foreach ($pos_sales as $pos_sale) {
            $pos_sale_date = date("d-m-Y", strtotime($pos_sale->pos_date));
            $html_table .='<tr>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px;">' . $pos_sale_date . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px;  word-break: break-word;">' . $pos_sale->employee_fname ." ". $pos_sale->employee_aname . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px;">' .$pos_sale->branch_code ."-".  $pos_sale->branch_name . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:60px; word-break: break-word;">' . $pos_sale->jobshift_name . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px;">' . $pos_sale->cash_collection . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px;">' . $pos_sale->tips_collected . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px;">' . $pos_sale->opening_amount . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px; word-break: break-word;">' . $pos_sale->supervisor_fname ." ". $pos_sale->supervisor_aname . '</td>
                                                <td style="color: #535352; font-size: 12px;padding: 10px 5px;width:80px; word-break: break-word;">' . $pos_sale->editedby_name ." ". $pos_sale->editedby_aname . '</td>
					</tr>';
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_cashierreport_pdf_report.pdf');
    }

}
