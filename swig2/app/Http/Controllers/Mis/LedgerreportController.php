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

class LedgerreportController extends Controller
{
    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        $companyid = session('company');

        $companies = Company::all();
        $ledger_reports = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'LEDGER')->where('status', '!=', 2)->where('company_id', '=', $companyid)
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $searchkey = Input::get('searchkey');
            $sort = Input::get('sorting');
            $cashsort = Input::get('cashsorting');
            $cashorder = Input::get('corder');
            $cashamount = Input::get('camount');
            $createdfrom = Input::get('startdatefrom');
            $endedfrom = Input::get('enddatefrom');
              $ldgordname=Input::get('ldgordname');
            
            if ($createdfrom != '') {
                $createdfrom = explode('-', $createdfrom);
                $createdfrom = $createdfrom[2] . '-' . $createdfrom[1] . '-' . $createdfrom[0];
            }
            if ($endedfrom != '') {
                $endedfrom = explode('-', $endedfrom);
                $endedfrom = $endedfrom[2] . '-' . $endedfrom[1] . '-' . $endedfrom[0];
            }
            
            
            $startdateto = Input::get('startdateto');
            $endedateto = Input::get('enddateto');
            if ($startdateto != '') {
                $startdateto = explode('-', $startdateto);
                $startdateto = $startdateto[2] . '-' . $startdateto[1] . '-' . $startdateto[0];
            }
            if ($endedateto != '') {
                $endedateto = explode('-', $endedateto);
                $endedateto = $endedateto[2] . '-' . $endedateto[1] . '-' . $endedateto[0];
            }
            
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
           $ledger_reports = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'LEDGER')->where('status', '!=', 2)->where('company_id', '=', $companyid)
                    ->when($createdfrom, function ($query) use ($createdfrom) {
                        return $query->whereRaw("master_resources.start_time >= '$createdfrom' ");
                    })
                    ->when($endedfrom, function ($query) use ($endedfrom) {
                        return $query->whereRaw("master_resources.start_time<= '$endedfrom' ");
                    })
                    ->when($startdateto, function ($query) use ($startdateto) {
                        return $query->whereRaw("master_resources.end_time >= '$startdateto' ");
                    })
                    ->when($endedateto, function ($query) use ($endedateto) {
                        return $query->whereRaw("master_resources.end_time<= '$endedateto' ");
                    })
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(master_resources.name like '$searchkey%')");
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        return $query->whereRaw("master_resources.amount $cashorder $cashamount ");
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('master_resources.name', $sort);
                    })
                     ->when($ldgordname, function ($query) use ($ldgordname) {
                        return $query->orderby('master_resources.ledger_code', $ldgordname);
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('master_resources.amount', $cashsort);
                    })
                    ->paginate($paginate);
                   
            return view('mis/ledger_report/ledger_report_result', array('ledger_reports' => $ledger_reports));
        }
        
        return view('mis/ledger_report/index', array('ledger_reports' => $ledger_reports));
    }
    
     public function showdetails($id) {
        $id = \Crypt::decrypt($id);
        
//        $ledger_details = DB::table('requisition')
//                ->select('requisition.id', 'ledger.name as ledger_name',DB::raw('ledger.amount - sum(requisition.amount) as remaing_amount'))
//                ->leftjoin('master_resources as ledger', 'ledger.id', '=', 'requisition.ledger_id')
//                ->whereRaw("requisition.ledger_id = $id AND requisition.status=1")
//                ->first();
        
        $ledger_details = DB::table('master_resources')
                ->select('master_resources.id', 'master_resources.name as ledger_name',DB::raw('master_resources.amount - COALESCE((select sum(amount) from requisition where requisition.status=4 AND requisition.ledger_id = '.$id.'),0) as remaing_amount'))
                ->leftjoin('requisition as requisition', 'master_resources.id', '=', 'requisition.ledger_id')
                ->whereRaw("master_resources.id = $id")
                ->first();
        
        $requisition_details = DB::table('requisition')
                ->select('requisition.*', 'branch_details.name as branch_name', 'employee_details.first_name as requested_by')
                ->leftjoin('master_resources as branch_details', 'requisition.branch_id', '=', 'branch_details.id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'requisition.created_by')
                ->whereRaw("requisition.ledger_id = $id")
                ->orderby("requisition.created_at","DESC")
                ->get();

        return view('mis/ledger_report/showdetails', array('requisition_details' => $requisition_details,'ledger_details' => $ledger_details));
    }
    
   // Generate PDF funcion
    public function exporttopdf() {
         $companyid = session('company');
            $searchkey = Input::get('searchkey');
            $sort = Input::get('sortsimp');
            $cashsort = Input::get('cashsorting');
            $cashorder = Input::get('corder');
            $cashamount = Input::get('camount');
            $createdfrom = Input::get('startdatefrom');
            $endedfrom = Input::get('enddatefrom');
            $ldgordname = Input::get('ldgordname');
            
            
            if ($createdfrom != '') {
                $createdfrom = explode('-', $createdfrom);
                $createdfrom = $createdfrom[2] . '-' . $createdfrom[1] . '-' . $createdfrom[0];
            }
            if ($endedfrom != '') {
                $endedfrom = explode('-', $endedfrom);
                $endedfrom = $endedfrom[2] . '-' . $endedfrom[1] . '-' . $endedfrom[0];
            }
            
            
            $startdateto = Input::get('startdateto');
            $endedateto = Input::get('enddateto');
            if ($startdateto != '') {
                $startdateto = explode('-', $startdateto);
                $startdateto = $startdateto[2] . '-' . $startdateto[1] . '-' . $startdateto[0];
            }
            if ($endedateto != '') {
                $endedateto = explode('-', $endedateto);
                $endedateto = $endedateto[2] . '-' . $endedateto[1] . '-' . $endedateto[0];
            }
            
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
           $ledger_reports = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'LEDGER')->where('status', '!=', 2)->where('company_id', '=', $companyid)
                    ->when($createdfrom, function ($query) use ($createdfrom) {
                        return $query->whereRaw("master_resources.start_time >= '$createdfrom' ");
                    })
                    ->when($endedfrom, function ($query) use ($endedfrom) {
                        return $query->whereRaw("master_resources.start_time<= '$endedfrom' ");
                    })
                    ->when($startdateto, function ($query) use ($startdateto) {
                        return $query->whereRaw("master_resources.end_time >= '$startdateto' ");
                    })
                    ->when($endedateto, function ($query) use ($endedateto) {
                        return $query->whereRaw("master_resources.end_time<= '$endedateto' ");
                    })
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(master_resources.name like '$searchkey%')");
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        return $query->whereRaw("master_resources.amount $cashorder $cashamount ");
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('master_resources.name', $sort);
                    })
                    ->when($ldgordname, function ($query) use ($ldgordname) {
                        return $query->orderby('master_resources.ledger_code', $ldgordname);
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('master_resources.amount', $cashsort);
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
                <div style="text-align:center;"><h1>Ledger Report</h1></div>
			<table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Sl.No.</td>
						<td style="padding:10px 5px;color:#fff;"> Ledger Code </td>
						<td style="padding:10px 5px;color:#fff;"> Ledger Name </td>
						<td style="padding:10px 5px;color:#fff;"> Budget Start Date</td>
						<td style="padding:10px 5px;color:#fff;"> Budget End Date</td>
						<td style="padding:10px 5px;color:#fff;"> Budget Amount </td>
					</tr>
				</thead>
				
				<tbody class="pos" id="pos" >';
        $i = 0;
        foreach ($ledger_reports as $ledger_report) {
            $start_date = date("d-m-Y", strtotime($ledger_report->start_time));
            $end_date = date("d-m-Y", strtotime($ledger_report->end_time));
            $html_table .='<tr>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:50px;">' . ($i+1) . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:100px;">' . $ledger_report->ledger_code . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:200px;">' . $ledger_report->name . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;">' . $start_date . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;">' . $end_date . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:100px;">' . $ledger_report->amount . '</td>
					</tr>';
            $i++;
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_ledger_pdf_report.pdf');
    }
}
