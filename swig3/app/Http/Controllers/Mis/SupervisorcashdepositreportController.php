<?php

namespace App\Http\Controllers\Mis;

use Illuminate\Support\Facades\Config;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests;
use Kamaln7\Toastr\Facades\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Document;
use App;
use PDF;
use DB;

class SupervisorcashdepositreportController extends Controller {

    public function index(Request $request) {
        $login_id = Session::get('login_id');
        $paginate = Config::get('app.PAGINATE');

//      DB::raw('CASE WHEN cash_collection.submitted_to = "TOP_CASHIER" THEN concat(cashier.first_name," ",cashier.alias_name) ELSE bank_details.name END as deposited_to')

       
        $cash_collections = DB::table('cash_collection')
                ->select('cash_collection.*',
                        DB::raw('CASE WHEN cash_collection.submitted_to = "TOP_CASHIER" THEN "" ELSE bank_details.name END as bank_name'),
                        DB::raw('CASE WHEN cash_collection.submitted_to = "TOP_CASHIER" THEN "TOP CASHIER" ELSE "BANK" END as cashier_or_bank'), 
                        DB::raw('concat(depositer.first_name," ",depositer.alias_name) as deposited_by'),
                        DB::raw('concat(cashier.first_name," ",cashier.alias_name) as deposited_to '))
                ->join('employees as depositer', 'depositer.id', '=', 'cash_collection.supervisor_id')
                ->leftjoin('employees as cashier', 'cashier.id', '=', 'cash_collection.cashier_id')
                ->leftjoin('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                ->paginate($paginate);
        
        $bank_names = DB::table('cash_collection')
                ->select('cash_collection.bank_id')->select('bank_details.name as bank_name', 'bank_details.id as id')->distinct()
                ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                ->get();
       
        if ($request->ajax()) {
            $depositbysort = Input::get('depositbysort');
            $deposittosort = Input::get('deposittosort');
            $cashsorting = Input::get('cashsorting');
            $bsort = Input::get('bsort');

            $cashorder = Input::get('corder');
            $bankid = Input::get('bank');
            $cashamount = Input::get('camount');
            $depositedbysearch = Input::get('depositedbysearch');
            $depositedtosearch = Input::get('depositedtosearch');
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

            $cash_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 
                            DB::raw('CASE WHEN cash_collection.submitted_to = "TOP_CASHIER" THEN "" ELSE bank_details.name END as bank_name'),
                            DB::raw('CASE WHEN cash_collection.submitted_to = "TOP_CASHIER" THEN "TOP CASHIER" ELSE "BANK" END as cashier_or_bank'), 
                            DB::raw('concat(depositer.first_name," ",depositer.alias_name) as deposited_by'), 
                            DB::raw('concat(cashier.first_name," ",cashier.alias_name) as deposited_to '))
                    ->join('employees as depositer', 'depositer.id', '=', 'cash_collection.supervisor_id')
                    ->leftjoin('employees as cashier', 'cashier.id', '=', 'cash_collection.cashier_id')
                    ->leftjoin('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                    ->when($created, function ($query) use ($created) {
                        return $query->whereRaw("date(cash_collection.created_at) >= '$created'");
                    })
                    ->when($ended, function ($query) use ($ended) {
                        return $query->whereRaw("date(cash_collection.created_at) <= '$ended'");
                    })
                    ->when($depositedbysearch, function ($query) use ($depositedbysearch) {
                        return $query->whereRaw("(depositer.first_name like '$depositedbysearch%')");
                    })
                    ->when($depositedtosearch, function ($query) use ($depositedtosearch) {
                        return $query->whereRaw("(cashier.first_name like '$depositedtosearch%')");
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        return $query->whereRaw("cash_collection.amount $cashorder $cashamount ");
                    })
                    ->when($bankid, function ($query) use ($bankid) {
                        return $query->whereRaw("bank_details.id = $bankid AND cash_collection.cashier_id IS NULL");
                    })
                    ->when($depositbysort, function ($query) use ($depositbysort) {
                        return $query->orderby('deposited_by', $depositbysort);
                    })
                    ->when($cashsorting, function ($query) use ($cashsorting) {
                        return $query->orderby('cash_collection.amount', $cashsorting);
                    })
                    ->when($deposittosort, function ($query) use ($deposittosort) {
                        return $query->orderby('deposited_to', $deposittosort);
                    })
                    ->when($bsort, function ($query) use ($bsort) {
                        return $query->orderby('bank_details.name', $bsort);
                    })
                    ->paginate($paginate);

            return view('mis/supervisor_cash_deposit_report/supervisor_cash_deposit_result', array('cash_collections' => $cash_collections));
        }

        return view('mis/supervisor_cash_deposit_report/index', array('cash_collections' => $cash_collections, 'bank_names' => $bank_names));
    }

    public function showdepositdetails($id) {
        $id = \Crypt::decrypt($id);

        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name', DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->whereRaw("pos_sales.id IN ($id) and pos_sales.status=1")
                ->get();

        return view('mis/supervisor_cash_deposit_report/supervisor_cash_deposit_report', array('sale_details_data' => $sale_details_data));
    }

    // Generate PDF funcion
    public function exporttopdf() {
        $depositbysort = Input::get('depositbysort');
        $deposittosort = Input::get('deposittosort');
        $cashsorting = Input::get('cashsorting');
        $bsort = Input::get('bsort');

        $cashorder = Input::get('corder');
        $bankid = Input::get('bank');
        $cashamount = Input::get('camount');
        $depositedbysearch = Input::get('depositedbysearch');
        $depositedtosearch = Input::get('depositedtosearch');
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

        $cash_collections = DB::table('cash_collection')
                ->select('cash_collection.*', 'bank_details.name as bank_name', DB::raw('CASE WHEN cash_collection.submitted_to = "TOP_CASHIER" THEN "TOP CASHIER" ELSE "BANK" END as cashier_or_bank'), DB::raw('concat(depositer.first_name," ",depositer.alias_name) as deposited_by'), DB::raw('concat(cashier.first_name," ",cashier.alias_name) as deposited_to '))
                ->join('employees as depositer', 'depositer.id', '=', 'cash_collection.supervisor_id')
                ->leftjoin('employees as cashier', 'cashier.id', '=', 'cash_collection.cashier_id')
                ->leftjoin('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                ->when($created, function ($query) use ($created) {
                    return $query->whereRaw("date(cash_collection.created_at) >= '$created'");
                })
                ->when($ended, function ($query) use ($ended) {
                    return $query->whereRaw("date(cash_collection.created_at) <= '$ended'");
                })
                ->when($depositedbysearch, function ($query) use ($depositedbysearch) {
                    return $query->whereRaw("(depositer.first_name like '$depositedbysearch%')");
                })
                ->when($depositedtosearch, function ($query) use ($depositedtosearch) {
                    return $query->whereRaw("(cashier.first_name like '$depositedtosearch%')");
                })
                ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                    return $query->whereRaw("cash_collection.amount $cashorder $cashamount ");
                })
                ->when($bankid, function ($query) use ($bankid) {
                    return $query->whereRaw("bank_details.id = $bankid");
                })
                ->when($depositbysort, function ($query) use ($depositbysort) {
                    return $query->orderby('deposited_by', $depositbysort);
                })
                ->when($cashsorting, function ($query) use ($cashsorting) {
                    return $query->orderby('cash_collection.amount', $cashsorting);
                })
                ->when($deposittosort, function ($query) use ($deposittosort) {
                    return $query->orderby('deposited_to', $deposittosort);
                })
                ->when($bsort, function ($query) use ($bsort) {
                    return $query->orderby('bank_details.name', $bsort);
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
	<body style="margin: 0; padding: 0;font-family:Arial;">
		<section id="container">
                <div style="text-align:center;"><h1>Supervisor Cash Deposit Report</h1></div>
			<table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 0;color:#fff;"> Date</td>
						<td style="padding:10px 0;color:#fff;"> Deposited By </td>
						<td style="padding:10px 0;color:#fff;"> Amount </td>
						<td style="padding:10px 0;color:#fff;"> Bank Name</td>
						<td style="padding:10px 0;color:#fff;"> Top Cashier</td>
						<td style="padding:10px 0;color:#fff;"> Top Cashier/Bank </td>
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
						<td></td>
					</tr>
				</thead>
				<tbody class="pos" id="pos" >';
        foreach ($cash_collections as $cash_collection) {
            $created_date = date("d-m-Y", strtotime($cash_collection->created_at));
            $html_table .='<tr>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $created_date . '</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $cash_collection->deposited_by . '</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $cash_collection->amount . '</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $cash_collection->bank_name . '</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $cash_collection->deposited_to . '</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $cash_collection->cashier_or_bank . '</td>
					</tr>';
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_supervisor_cash_deposit_pdf_report.pdf');
    }

}
