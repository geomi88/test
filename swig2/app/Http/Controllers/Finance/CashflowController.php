<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use Illuminate\Support\Facades\Config;
use App\Models\Company;
use App\Models\Module;
use App\Models\Usermodule;
use App;
use DB;
use Mail;
use Customhelper;

class CashflowController extends Controller {

    public function index(Request $request) {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $tot_pending = '0';
        $total_count = '0';
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        
        $full_graph_data = array();
        
        // monthly sale data
        $month_sales = DB::table('pos_sales')
                ->select("pos_sales.pos_date", db::raw('sum(pos_sales.total_sale) as total_sale'))
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy('pos_sales.pos_date')
                ->orderby('pos_sales.pos_date')
                ->get();
        $Pending_amount = DB::table('ac_payment_advice')
                ->select(db::raw('sum(ac_payment_advice.total_amount) as total_cost,count(ac_payment_advice.total_amount) as tot_count'))
                ->whereRaw("(status=2 AND (remittance_number = '' OR remittance_number IS NULL))")
                ->get();
        $tot_pending=$Pending_amount->sum('total_cost');
        $total_count = $Pending_amount->sum('tot_count') ? $Pending_amount->sum('tot_count') : '';
        
        
        $totalincome=$month_sales->sum('total_sale');
        
        $graph_area = array();
        foreach ($month_sales as $sale) {
            $sale_created_date = $sale->pos_date;
            $sale_created_date = explode(" ", $sale_created_date);
            $sale_created_date = $sale_created_date[0];

            $row['x'] = ((int) strtotime($sale_created_date)) * 1000;
            $row['y'] = (int) $sale->total_sale;
            $row['date'] = '';
            array_push($graph_area, $row);
        }

        $full_row['name'] = "Income";
        $full_row['data'] = $graph_area;
        $full_row['color'] = '#02838e';


        array_push($full_graph_data, $full_row);
        // sale data ends
        
        // monthly expense data
        $expenditures = DB::table('ac_payment_advice')
                ->select("ac_payment_advice.remitted_date", db::raw('sum(ac_payment_advice.total_amount) as total_cost'))
                ->whereRaw("(ac_payment_advice.remitted_date BETWEEN '$first_day' and '$last_day') AND status=2 AND remittance_number!='' AND remittance_number IS NOT NULL")
                ->groupBy('ac_payment_advice.remitted_date')
                ->orderby('ac_payment_advice.remitted_date')
                ->get();
        
        $totalexpense=$expenditures->sum('total_cost');
		$difference = ($totalincome - $totalexpense);
        
        $graph_area = array();
        foreach ($expenditures as $expense) {
            $exp_created_date = $expense->remitted_date;
            $exp_created_date = explode(" ", $exp_created_date);
            $exp_created_date = $exp_created_date[0];

            $row['x'] = ((int) strtotime($exp_created_date)) * 1000;
            $row['y'] = (int) $expense->total_cost;
            $row['date'] = explode(" ", $expense->remitted_date)[0];
           
            array_push($graph_area, $row);
        }

        $full_row['name'] = "Expense";
        $full_row['data'] = $graph_area;
        $full_row['color'] = '#bd0c04';

        array_push($full_graph_data, $full_row);
        // sale expense ends
        
        $full_graph_data = array_values($full_graph_data);
        $full_graph_data = json_encode($full_graph_data);
        
        
        // Area graph ends   
      
        $arrSalesData = array(
                        "PeriodStartDate"=>date('01-m-Y'),
                        "PeriodEndDate"=>date('t-m-Y'),
                        );
        
        return view('finance/cash_flow/cash_flow',
                array(  "graph_data" => $full_graph_data,
                        "arrSalesData" => $arrSalesData,
                        "totalincome" => $totalincome,
                        "totalexpense" => $totalexpense,
                        "tot_pending" => $tot_pending,
                        "difference" => $difference,
                        "total_count" => $total_count,
                    ));
    }
    
    
    public function getcustomcashflow() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $tot_pending ='';
        $total_count ='';
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        
        $period_start_date = $first_day;
        $period_end_date = $last_day;
        
        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }
        
        $full_graph_data = array();
        
        // monthly sale data
        $month_sales = DB::table('pos_sales')
                ->select("pos_sales.pos_date", db::raw('sum(pos_sales.total_sale) as total_sale'))
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy('pos_sales.pos_date')
                ->orderby('pos_sales.pos_date')
                ->get();
        
        $totalincome=$month_sales->sum('total_sale');
        
        $graph_area = array();
        foreach ($month_sales as $sale) {
            $sale_created_date = $sale->pos_date;
            $sale_created_date = explode(" ", $sale_created_date);
            $sale_created_date = $sale_created_date[0];

            $row['x'] = ((int) strtotime($sale_created_date)) * 1000;
            $row['y'] = (int) $sale->total_sale;
            $row['date'] = '';
           
            array_push($graph_area, $row);
        }

        $full_row['name'] = "Income";
        $full_row['data'] = $graph_area;
        $full_row['color'] = '#02838e';


        array_push($full_graph_data, $full_row);
        // sale data ends
        
        // monthly expense data
        $expenditures = DB::table('ac_payment_advice')
                ->select("ac_payment_advice.remitted_date", db::raw('sum(ac_payment_advice.total_amount) as total_cost'))
                ->whereRaw("(ac_payment_advice.remitted_date BETWEEN '$first_day' and '$last_day') AND status=2 AND remittance_number!='' AND remittance_number IS NOT NULL")
                ->groupBy('ac_payment_advice.remitted_date')
                ->orderby('ac_payment_advice.remitted_date')
                ->get();
        
        
        // Pending data
        $Pending_amount = DB::table('ac_payment_advice')
                ->select(db::raw('sum(ac_payment_advice.total_amount) as total_cost,count(ac_payment_advice.total_amount) as tot_count'))
                ->whereRaw("(status=2 AND (remittance_number = '' OR remittance_number IS NULL))")
                ->get();
        $totalexpense=$expenditures->sum('total_cost');
        $tot_pending=$Pending_amount->sum('total_cost');
        $total_count = $Pending_amount->sum('tot_count') ? $Pending_amount->sum('tot_count') : '';
        
        $difference = $totalincome - $totalexpense;
        
        $graph_area = array();
        foreach ($expenditures as $expense) {
            $exp_created_date = $expense->remitted_date;
            $exp_created_date = explode(" ", $exp_created_date);
            $exp_created_date = $exp_created_date[0];

            $row['x'] = ((int) strtotime($exp_created_date)) * 1000;
            $row['y'] = (int) $expense->total_cost;
            $row['date'] = explode(" ", $expense->remitted_date)[0];
           
            array_push($graph_area, $row);
        }

        $full_row['name'] = "Expense";
        $full_row['data'] = $graph_area;
        $full_row['color'] = '#bd0c04';

        array_push($full_graph_data, $full_row);
        // sale expense ends
        
        $full_graph_data = array_values($full_graph_data);
        $full_graph_data = json_encode($full_graph_data);
      
        // Area graph ends   
      
        $arrSalesData = array(
                        "PeriodStartDate"=>$period_start_date,
                        "PeriodEndDate"=>$period_end_date,
                        );
        
        return view('finance/cash_flow/cash_flow',
                array(  "graph_data" => $full_graph_data,
                        "arrSalesData" => $arrSalesData,
                        "totalincome" => $totalincome,
                        "totalexpense" => $totalexpense,
                        "tot_pending" => $tot_pending,
                        "difference" => $difference,
                        "total_count" => $total_count,
                    ));
    }


}
