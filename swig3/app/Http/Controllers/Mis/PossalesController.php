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
use Customhelper;

class PossalesController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $pos_sales = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','branch_details.branch_code as branch_code')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->paginate($paginate);
        $branch_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('branch_details.name as branch_name', 'branch_details.branch_code as branch_code','branch_details.id as branch_id')->distinct()
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->get();
        $shift_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('jobshift_details.name as jobshift_name', 'jobshift_details.id as jobshift_id')->distinct()
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->get();
        //print_r('<pre>');print_r($pos_sales);die('sd');
        
        if ($request->ajax()) {
            $searchkey = Input::get('searchkey');
            $branch = Input::get('branch');
            $sort = Input::get('sorting');
            $cashsort = Input::get('cashsorting');
            $diffsort = Input::get('diffsorting');
            $bsort = Input::get('bsorting');
            $order = Input::get('order');
            $cashorder = Input::get('cashorder');
            $difforder = Input::get('difforder');
            $amount = Input::get('amount');
            $cashamount = Input::get('cashamount');
            $diffamount = Input::get('diffamount');
            $tot = Input::get('totsorting');
            $shift = Input::get('shift');
            $created = Input::get('startdate');
            $ended = Input::get('enddate');
            if($created!='')
            {
                $created = explode('-',$created);
                $created = $created[2].'-'.$created[1].'-'.$created[0];
            }
            if($ended!='')
            {
                $ended = explode('-',$ended);
                $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
            }
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $pos_sales = DB::table('pos_sales')
                    ->select('employees.*','employees.first_name as employee_fname', 'pos_sales.*', 'jobshift_details.name as jobshift_name', 'branch_details.name as branch_name', 'employees.alias_name as employee_aname')
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                    ->join('employees', 'employees.id', '=', 'pos_sales.employee_id')
                    ->whereRaw("pos_sales.status=1")
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employees.first_name like '$searchkey%' or concat(employees.alias_name) like '$searchkey%')");
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->where('branch_details.id', '=', $branch);
                    })
                    ->when($shift, function ($query) use ($shift) {
                        return $query->where('jobshift_details.id', '=', $shift);
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('date(pos_sales.pos_date)', $sort);
                    })
                    ->when($amount, function ($query) use ($amount, $order) {
                        return $query->whereRaw("pos_sales.total_sale $order $amount ");
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.cash_collection $cashorder $cashamount ");
                    })
                    ->when($diffamount, function ($query) use ($diffamount, $difforder) {
                        //echo "pos_sales.cash_collection $difforder $diffamount ";
                        return $query->whereRaw("(pos_sales.total_sale-pos_sales.cash_collection) $difforder $diffamount ");
                    })
                    ->when($diffsort, function ($query) use ($diffsort) {
                        // echo '(pos_sales.cash_collection-pos_sales.total_sale) '.$diffsort;
                        return $query->orderByRaw("(pos_sales.cash_collection-pos_sales.total_sale) $diffsort");
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        // echo 'pos_sales.cash_collection', $cashsort;
                        return $query->orderby('pos_sales.cash_collection', $cashsort);
                    })
                    ->when($bsort, function ($query) use ($bsort) {
                        return $query->orderby('branch_name', $bsort);
                    })
//                 start >= '2013-07-22' AND end <= '2013-06-13'
//                ->when($diffsort, function ($query) use ($diffsort) {
//                   return $query->orderby('branch_name', $diffsort);
//                })
                    ->when($tot, function ($query) use ($tot) {
                        return $query->orderby('pos_sales.total_sale', $tot);
                    })
                    ->when($created, function ($query) use ($created, $ended) {
                        //echo "pos_sales.created_at >= '$created' AND pos_sales.created_at<= '$ended' ";
                        return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                    })
                    ->paginate($paginate);
            // $pdf = PDF::loadView('mis/pos_sales/pdfview', array('pos_sales' => $pos_sales, 'branch_names' => $branch_names, 'shift_names' => $shift_names));
            //print_r('<pre>');print_r($pdf);
            return view('mis/pos_sales/searchresults', array('pos_sales' => $pos_sales));
        }

        return view('mis/pos_sales/index', array('pos_sales' => $pos_sales, 'branch_names' => $branch_names, 'shift_names' => $shift_names));
    }

    public function show($id) {
        $id = \Crypt::decrypt($id);

        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'shift_details.name as shift_name', 'reason_details.name as reason', 'employees.*')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as shift_details', 'shift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('master_resources as reason_details', 'reason_details.id', '=', 'pos_sales.reason_id')
                ->join('employees', 'employees.id', '=', 'pos_sales.employee_id')
                ->where('pos_sales.id', '=', $id)
                ->first();
        return view('mis/pos_sales/show', array('sale_details_data' => $sale_details_data));
    }   
    
    public function exporttopdf() {
        
            $searchkey = Input::get('searchkey');
            $branch = Input::get('branch');
            $sort = Input::get('sortsimp');
            $cashsort = Input::get('cashsort');
            $diffsort = Input::get('diffsort');
            $bsort = Input::get('bsort');
            $order = Input::get('order');
            $cashorder = Input::get('cashorder');
            $difforder = Input::get('difforder');
            $amount = Input::get('amount');
            $cashamount = Input::get('cashamount');
            $diffamount = Input::get('diffamount');
            $tot = Input::get('tot');
            $shift = Input::get('shift');
            $created = Input::get('startdate');
            $ended = Input::get('enddate');
            if($created!='')
            {
                $created = explode('-',$created);
                $created = $created[2].'-'.$created[1].'-'.$created[0];
            }
            if($ended!='')
            {
                $ended = explode('-',$ended);
                $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
            }
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $pos_sales = DB::table('pos_sales')
                    ->select('employees.first_name as employee_fname','employees.alias_name as employee_aname', 'pos_sales.*', 'jobshift_details.name as jobshift_name', 'branch_details.name as branch_name')
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                    ->join('employees', 'employees.id', '=', 'pos_sales.employee_id')
                    ->whereRaw("pos_sales.status=1")
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->where('employees.first_name', 'like', '%' . $searchkey . '%');
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->where('branch_details.id', '=', $branch);
                    })
                    ->when($shift, function ($query) use ($shift) {
                        return $query->where('jobshift_details.id', '=', $shift);
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('date(pos_sales.pos_date)', $sort);
                    })
                    ->when($amount, function ($query) use ($amount, $order) {
                        return $query->whereRaw("pos_sales.total_sale $order $amount ");
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        return $query->whereRaw("pos_sales.cash_collection $cashorder $cashamount ");
                    })
                    ->when($diffamount, function ($query) use ($diffamount, $difforder) {
                        return $query->whereRaw("pos_sales.difference $difforder $diffamount ");
                    })
                    ->when($diffsort, function ($query) use ($diffsort) {
                        return $query->orderByRaw("pos_sales.difference $diffsort");
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('pos_sales.cash_collection', $cashsort);
                    })
                    ->when($bsort, function ($query) use ($bsort) {
                        return $query->orderby('branch_name', $bsort);
                    })
                 
                    ->when($tot, function ($query) use ($tot) {
                        return $query->orderby('pos_sales.total_sale', $tot);
                    })
                    ->when($created, function ($query) use ($created, $ended) {
                        return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                    })
                    ->get();

            /*$html_table = '
            <table border="1" cellspacing="0" cellpadding="2">
                <tr><th> Supervisor Name </th><th> Branch Name </th><th> Shift Name </th>
                        <th> Total Sale </th><th> Cash Collection </th><th> Difference </th><th> Date </th>';
            foreach ($pos_sales as $pos_sale) {
                
                $html_table .= "<tr><td>$pos_sale->employee_name</td>"
                        . "<td>$pos_sale->branch_name</td>"
                        . "<td>$pos_sale->jobshift_name</td>"
                        . "<td>$pos_sale->total_sale</td>"
                        . "<td>$pos_sale->cash_collection</td>"
                        . "<td>$pos_sale->difference</td>"
                        . "<td>$pos_sale->created_at</td>";
            }
            $html_table .='
                </tr></table>';*/
            
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
			<table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 0;color:#fff;"> Supervisor Name </td>
						<td style="padding:10px 0;color:#fff;"> Branch Name </td>
						<td style="padding:10px 0;color:#fff;"> Shift Name </td>
						<td style="padding:10px 0;color:#fff;"> Total Sale </td>
						<td style="padding:10px 0;color:#fff;"> Cash Collection </td>
						<td style="padding:10px 0;color:#fff;"> Difference </td>
						<td style="padding:10px 0;color:#fff;"> Date </td>
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
                                        foreach ($pos_sales as $pos_sale) {
                                        $pos_sale_date = explode(' ', $pos_sale->pos_date)[0];
					$html_table .='<tr>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">'.$pos_sale->employee_fname.' '.$pos_sale->employee_aname.'</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">'.$pos_sale->branch_name.'</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">'.$pos_sale->jobshift_name.'</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">'.$pos_sale->total_sale.'</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">'.$pos_sale->cash_collection.'</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">'.$pos_sale->difference.'</td>
						<td style="color: #535352; font-size: 14px;padding: 15px 5px;">'.$pos_sale_date.'</td>
					</tr>';
                                        }
				$html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';
            
            
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_pdf_report.pdf');
        }
        
}
