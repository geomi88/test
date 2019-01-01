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

class BankdepositsaleController extends Controller {

    public function index(Request $request) {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
        $currentQuarter = $arrMonthQuarter["$currentMonth"];

        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $target = DB::table('sales_target')
                ->select(DB::raw('COALESCE(sum(sales_target.target_amount),0) as target'))
                ->whereraw("sales_target.target_quarter=$currentQuarter AND sales_target.target_year=$currentYear AND status=1")
                ->value('target');

        $result = DB::table('sales_target')
                ->select(db::raw('DATEDIFF(end_date,start_date) as DaysInQuarter'))
                ->whereraw("sales_target.target_quarter=$currentQuarter AND sales_target.target_year=$currentYear and status=1")
                ->first();

        $noOfDaysInQuaarter = 90;
        if (count($result) != 0) {
            $noOfDaysInQuaarter = $result->DaysInQuarter + 1;
        }

        $target = $target / $noOfDaysInQuaarter * date('t');

        $yearsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as year_sale'))
                ->whereraw("EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->where('company_id', '=', $company_id)
                ->value('year_sale');

        $difference = $actualsale - $target;

        $yearTarget = DB::table('sales_target')
                ->select(db::raw('COALESCE(SUM(target_amount),0) as yeartarget'))
                ->whereraw("target_year=$currentYear AND sales_target.status=1")
                ->value('yeartarget');

        // All branches
        $allBranches = DB::table('master_resources as branch')
                ->select("branch.id", "branch.name as br_name")
                ->whereRaw("branch.status=1 AND branch.resource_type='BRANCH'")
                ->where('branch.company_id', '=', $company_id)
                ->orderby('branch.name', 'ASC')
                ->get();

        // All employees
        $allEmployees = DB::table('employees')
                ->select("job_pos.name as job_name", "job_pos.id as job_id", db::raw("count(*) as emp_count"))
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND employees.job_position IS NOT NULL")
                ->groupBy(['job_pos.name', 'job_pos.id'])
                ->orderby('job_pos.name', 'ASC')
                ->get();

        $totEmployees = $allEmployees->sum('emp_count');

        // monthly sale data for area graph  
        $month_sales = DB::table('pos_sales')
                ->select("pos_sales.pos_date", db::raw('sum(pos_sales.total_sale) as total_sale'))
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->groupBy('pos_sales.pos_date')
                ->orderby('pos_sales.pos_date')
                ->get();

        /* $graph_area = array();
          $full_area_graph_data = array();

          foreach ($month_sales as $sale) {
          $sale_created_date = $sale->pos_date;
          $sale_created_date = explode(" ", $sale_created_date);
          $sale_created_date = $sale_created_date[0];

          $row['x'] = ((int) strtotime($sale_created_date)) * 1000;
          $row['y'] = (int) $sale->total_sale;

          array_push($graph_area, $row);
          }

          $full_row['name'] = "Sale";
          $full_row['data'] = $graph_area;
          $full_row['color'] = '#02838e';


          array_push($full_area_graph_data, $full_row);

          $full_area_graph_data = array_values($full_area_graph_data);
          $full_area_graph_data = json_encode($full_area_graph_data);
          // Area graph ends */

        $graph_area = array();
        $full_row = array();
        $full_area_graph_data = array();

        foreach ($month_sales as $sale) {

            $sale_created_date = $sale->pos_date;
            $sale_created_date = explode(" ", $sale_created_date);
            $sale_created_date = $sale_created_date[0];
            $row = array("x" => ((int) strtotime($sale_created_date)) * 1000, "y" => (int) $sale->total_sale, "name" => "sale", "sale_date" => $sale_created_date);
            array_push($graph_area, $row);
        }

        $full_area_graph_data = array_values($graph_area);
        $full_area_graph_data = json_encode($full_area_graph_data);

        $arrSalesData = array(
            "PeriodStartDate" => date('01-m-Y'),
            "PeriodEndDate" => date('t-m-Y'),
            "actualsale" => round($actualsale, 2),
            "target" => round($target, 2),
            "difference" => round($difference, 2),
            "yearsale" => round($yearsale, 2),
            "yeartarget" => round($yearTarget, 2),
        );

        return view('finance/bankdepositsale', array("full_area_graph_data" => $full_area_graph_data,
            "arrSalesData" => $arrSalesData,
            "allBranches" => $allBranches,
            "allEmployees" => $allEmployees,
            "totEmployees" => $totEmployees));
    }

    public function getdatewisesale() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

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

        $currentYear = date('Y');
        $currentMonth = date('m');

        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $quarter = DB::table('sales_target')
                ->select('target_quarter', 'target_year', 'start_date', 'end_date', db::raw('concat(target_year,"-",target_quarter) as quarter'), db::raw('DATEDIFF(end_date,start_date) as DaysInQuarter'))
                ->whereraw("date(sales_target.end_date)>='$first_day' AND date(sales_target.start_date)<='$last_day' AND sales_target.status=1")
                ->distinct('quarter')
                ->orderby('quarter', 'ASC')
                ->get();

        $periodTargetAmt = 0;
        $start_date = date_create($first_day);
        $end_date = date_create($last_day);

        for ($i = 0; $i < count($quarter); $i++) {
            $targetamount = DB::table('sales_target')
                    ->select(db::raw('COALESCE(sum(target_amount),0) as targetamount'))
                    ->whereraw("sales_target.target_quarter=" . $quarter[$i]->target_quarter . " AND sales_target.target_year=" . $quarter[$i]->target_year . "")
                    ->value('targetamount');

            $TargetPerDay = $targetamount / ($quarter[$i]->DaysInQuarter + 1);

            if (count($quarter) == 1) {
                $interval = date_diff($start_date, $end_date)->days + 1;
                $periodTargetAmt = $periodTargetAmt + ($interval * $TargetPerDay);
            } else {
                if ($i == 0) {
                    $interval = date_diff($start_date, date_create($quarter[$i]->end_date))->days + 1;
                    $periodTargetAmt = $periodTargetAmt + ($interval * $TargetPerDay);
                } else if ($i == (count($quarter) - 1)) {
                    $interval = date_diff(date_create($quarter[$i]->start_date), $end_date)->days + 1;
                    $periodTargetAmt = $periodTargetAmt + ($interval * $TargetPerDay);
                } else {
                    $periodTargetAmt = $periodTargetAmt + ($TargetPerDay * $quarter[$i]->DaysInQuarter);
                }
            }
        }

        $target = $periodTargetAmt;
        $difference = $actualsale - $target;

        $yearsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(SUM(pos_sales.total_sale),0) as year_sale'))
                ->whereraw("EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->where('company_id', '=', $company_id)
                ->value('year_sale');

        $yearTarget = DB::table('sales_target')
                ->select(db::raw('COALESCE(SUM(target_amount),0) as yeartarget'))
                ->whereraw("target_year=$currentYear AND sales_target.status=1")
                ->value('yeartarget');

        // All branches
        $allBranches = DB::table('master_resources as branch')
                ->select("branch.id", "branch.name as br_name")
                ->whereRaw("branch.status=1 AND branch.resource_type='BRANCH'")
                ->where('branch.company_id', '=', $company_id)
                ->orderby('branch.name', 'ASC')
                ->get();

        // All employees
        $allEmployees = DB::table('employees')
                ->select("job_pos.name as job_name", "job_pos.id as job_id", db::raw("count(*) as emp_count"))
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND employees.job_position IS NOT NULL")
                ->groupBy(['job_pos.name', 'job_pos.id'])
                ->orderby('job_pos.name', 'ASC')
                ->get();

        $totEmployees = $allEmployees->sum('emp_count');

        // monthly sale data for area graph  
        $month_sales = DB::table('pos_sales')
                ->select("pos_sales.pos_date", db::raw('sum(pos_sales.total_sale) as total_sale'))
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->groupBy('pos_sales.pos_date')
                ->orderby('pos_sales.pos_date')
                ->get();


        $graph_area = array();
        $full_row = array();
        $full_area_graph_data = array();

        foreach ($month_sales as $sale) {

            $sale_created_date = $sale->pos_date;
            $sale_created_date = explode(" ", $sale_created_date);
            $sale_created_date = $sale_created_date[0];
            $row = array("x" => ((int) strtotime($sale_created_date)) * 1000, "y" => (int) $sale->total_sale, "name" => "sale", "sale_date" => $sale_created_date);
            array_push($graph_area, $row);
        }

        $full_area_graph_data = array_values($graph_area);
        $full_area_graph_data = json_encode($full_area_graph_data);


        $arrSalesData = array(
            "PeriodStartDate" => $period_start_date,
            "PeriodEndDate" => $period_end_date,
            "actualsale" => round($actualsale, 2),
            "target" => round($target, 2),
            "difference" => round($difference, 2),
            "yearsale" => round($yearsale, 2),
            "yeartarget" => round($yearTarget, 2),
        );

        return view('finance/bankdepositsale', array("full_area_graph_data" => $full_area_graph_data,
            "arrSalesData" => $arrSalesData,
            "allBranches" => $allBranches,
            "allEmployees" => $allEmployees,
            "totEmployees" => $totEmployees));
    }

    public function getsupervisorsale() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $sale_date = Input::get('sale_date');



        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->whereraw("date(pos_sales.pos_date)>='$sale_date' AND date(pos_sales.pos_date)<='$sale_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->where('company_id', '=', $company_id)
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();



        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$sale_date' AND date(pos_sales.pos_date)<='$sale_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.cash_collection_final_status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        //$arrsupervisors = '';
        //$arrsales = '';
        $arrsales = array();
        $arrsupervisors = array();
        $public_path= url('/');
        foreach ($sales as $data) {
            //$arrsupervisors = $arrsupervisors . "" . $data->first_name . ",";
            //$arrsales = $arrsales . (int)$data->total_sale . ",";
            $arrsupervisors[] =  $data->first_name;
            //$arrsales[] = (int) $data->total_sale;
            $emp_id = (int) $data->employee_id;
            
            $arrsales[]=array('y'=>(int) $data->total_sale,'emp_id'=> (int) $data->employee_id,'sale_date' => $sale_date,'url' => "$public_path/finance/supervisordaydeposits/$emp_id/$sale_date");

        }

        //$arrsupervisors = rtrim($arrsupervisors, ",");
        $arrsales = array_values($arrsales);
        $arrsupervisors = array_values($arrsupervisors);
        //$supervisorcount = 480;


        if(count($arrsupervisors)>8){
            $supervisorcount=count($arrsupervisors);
            $supervisorcount=$supervisorcount*60;
        }else{
            $supervisorcount=480;
        }

        return \Response::json(array('arrsupervisors' => $arrsupervisors,
                    'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
                    "actualsale" => round($actualsale, 2)));
    }
    
    public function supervisordaydeposits($emp_id,$sale_date) {
                
        $from_date = $sale_date;
        $to_date = $sale_date;
        
        $pos_sales = array();
        if ($from_date != '' && $to_date != '') {
            
            $pos_sales = DB::table('pos_sales')
                    ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id' )
                    ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                    ->whereRaw("pos_sales.employee_id=$emp_id and (date(pos_sales.pos_date) BETWEEN '$from_date' and '$to_date') and pos_sales.cash_collection_final_status=1 AND pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->get();
        }
       
        $employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->join('country', 'country.id', '=', 'employees.nationality')
                    ->where('employees.id', '=', $emp_id)
                    ->first();
    
        
      
        return view('finance/supervisordaydeposits', array('employee_details' => $employee_details, 'pos_sales' => $pos_sales));
    }

}
