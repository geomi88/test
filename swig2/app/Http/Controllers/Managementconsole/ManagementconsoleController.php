<?php

namespace App\Http\Controllers\Managementconsole;

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

class ManagementconsoleController extends Controller {

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
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
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
        if (isset($result) != 0) {
            $noOfDaysInQuaarter = $result->DaysInQuarter + 1;
        }

        $target = $target / $noOfDaysInQuaarter * date('t');

        $yearsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as year_sale'))
                ->whereraw("EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
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
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy('pos_sales.pos_date')
                ->orderby('pos_sales.pos_date')
                ->get();

        $graph_area = array();
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
        // Area graph ends   
        // monthly sale data for pi graph
        $region_sales = DB::table('pos_sales')
                ->select("region.id as region_id", "region.name as region", db::raw('sum(pos_sales.total_sale) as region_sale'))
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy(['region.id', 'region.name'])
                ->get();


        $graph_pi = array();
        $full_row = array();
        $full_pi_graph_data = array();

        foreach ($region_sales as $sale) {

            $percent = ($sale->region_sale / $actualsale) * 100;
            $percent = round($percent, 2);
            $row = array("name" => "$sale->region", "y" => $percent, "id" => $sale->region_id);
            array_push($graph_pi, $row);
        }

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);
        //  Pi graph ends

        $arrSalesData = array(
            "PeriodStartDate" => date('01-m-Y'),
            "PeriodEndDate" => date('t-m-Y'),
            "actualsale" => round($actualsale, 2),
            "target" => round($target, 2),
            "difference" => round($difference, 2),
            "yearsale" => round($yearsale, 2),
            "yeartarget" => round($yearTarget, 2),
        );

        return view('managementconsole/managementconsole', array("full_area_graph_data" => $full_area_graph_data,
            "full_pi_graph_data" => $full_pi_graph_data,
            "arrSalesData" => $arrSalesData,
            "allBranches" => $allBranches,
            "allEmployees" => $allEmployees,
            "totEmployees" => $totEmployees));
    }

    public function getmonthlysale() {

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
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
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
                ->whereraw("EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
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
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy('pos_sales.pos_date')
                ->orderby('pos_sales.pos_date')
                ->get();

        $graph_area = array();
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
        // Area graph ends   
        // monthly sale data for pi graph
        $region_sales = DB::table('pos_sales')
                ->select("region.id as region_id", "region.name as region", db::raw('sum(pos_sales.total_sale) as region_sale'))
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("(pos_sales.pos_date BETWEEN '$first_day' and '$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy(['region.id', 'region.name'])
                ->get();


        $graph_pi = array();
        $full_row = array();
        $full_pi_graph_data = array();

        foreach ($region_sales as $sale) {

            $percent = ($sale->region_sale / $actualsale) * 100;
            $percent = round($percent, 2);
            $row = array("name" => "$sale->region", "y" => $percent, "id" => $sale->region_id);
            array_push($graph_pi, $row);
        }

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);
        //  Pi graph ends

        $arrSalesData = array(
            "PeriodStartDate" => $period_start_date,
            "PeriodEndDate" => $period_end_date,
            "actualsale" => round($actualsale, 2),
            "target" => round($target, 2),
            "difference" => round($difference, 2),
            "yearsale" => round($yearsale, 2),
            "yeartarget" => round($yearTarget, 2),
        );

        return view('managementconsole/managementconsole', array("full_area_graph_data" => $full_area_graph_data,
            "full_pi_graph_data" => $full_pi_graph_data,
            "arrSalesData" => $arrSalesData,
            "allBranches" => $allBranches,
            "allEmployees" => $allEmployees,
            "totEmployees" => $totEmployees));
    }

    public function getbranches() {
        try {
            $region_id = Input::get('region_id');
            $region_name = Input::get('region_name');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');

            if ($first_day != '') {
                $first_day = explode('-', $first_day);
                $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
            }
            if ($last_day != '') {
                $last_day = explode('-', $last_day);
                $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
            }

            $branches = DB::table('master_resources as branch')
                    ->select("branch.id as branchid", "branch.name as br_name")
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereRaw("area.region_id=$region_id AND branch.status=1")
                    ->orderby("br_name", "ASC")
                    ->get();

            $arrAllBranch = array();
            if (count($branches) != 0) {
                $arrAllBranch = $branches->pluck("branchid")->toArray();
            }

            $branch_sales = DB::table('pos_sales')
                    ->select("pos_sales.branch_id", "branch.name as br_name", db::raw('sum(pos_sales.total_sale) as total'))
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->whereRaw("(date(pos_sales.pos_date)>='$first_day' and date(pos_sales.pos_date)<='$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and branch.status=1")
                    ->whereIn('pos_sales.branch_id', $arrAllBranch)
                    ->groupBy(['pos_sales.branch_id', 'branch.name'])
                    ->orderby("total", "DESC")
                    ->get();

            $regionTotalSale = 0;
            if (count($branch_sales) > 0) {
                $regionTotalSale = $branch_sales->sum('total');
            }

            $branchOrderOfSale = array();
            foreach ($branch_sales as $branch) {
                $branchOrderOfSale[$branch->branch_id] = array("branch_id" => $branch->branch_id, "br_name" => $branch->br_name, "totSale" => $branch->total, "targetSale" => '');
            }

            //************************* target for branches ****************************     
            $quarter = DB::table('sales_target')
                    ->select('target_quarter', 'target_year', 'start_date', 'end_date', db::raw('concat(target_year,"-",target_quarter) as quarter'), db::raw('DATEDIFF(end_date,start_date) as DaysInQuarter'))
                    ->whereraw("date(sales_target.end_date)>='$first_day' AND date(sales_target.start_date)<='$last_day' AND sales_target.status=1")
                    ->distinct('quarter')
                    ->orderby('quarter', 'ASC')
                    ->get();

            $start_date = date_create($first_day);
            $end_date = date_create($last_day);

            foreach ($branchOrderOfSale as $br) {
                $periodTargetAmt = 0;
                for ($i = 0; $i < count($quarter); $i++) {
                    $targetamount = DB::table('sales_target')
                            ->select(db::raw('COALESCE(SUM(target_amount),0) as targetamount'))
                            ->whereraw("sales_target.target_quarter=" . $quarter[$i]->target_quarter . " AND sales_target.target_year=" . $quarter[$i]->target_year . "")
                            ->whereraw("sales_target.branch_id=" . $br['branch_id'])
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
                $br['targetSale'] = $periodTargetAmt;
            }
            //******************************* ends *************************************     

            foreach ($branches as $branch) {
                if (!key_exists($branch->branchid, $branchOrderOfSale)) {
                    $branchOrderOfSale[$branch->branchid] = array("branch_id" => $branch->branchid, "br_name" => $branch->br_name, "totSale" => 0, "targetSale" => 0);
                }
            }

            $public_path = url('/');
            if (count($branchOrderOfSale) != 0) {

                $strHtml = '<h2>' . $region_name . '<input type="text" placeholder="search" name="search" style="margin-left:10px;width: 160px;" id="search" onkeyup="filterbranch()">'
                        . '<input type="hidden" name="regionid" id="regionid" value=' . $region_id . '>'
                        . '<input type="hidden" name="regionname" id="regionname" value=' . $region_name . '>'
                        . '<span style="float: right;padding-right:28px;">' . number_format($regionTotalSale, 2) . '</span></h2><div id="branchpop"><ul class="listTypeV2">';

                foreach ($branchOrderOfSale as $branch) {
                    if ($branch['targetSale'] != 0 && $branch['totSale'] >= $branch['targetSale']) {
                        $clsArrow = "plus";
                    } else {
                        $clsArrow = "down";
                    }

                    $strHtml .= '<li class="' . $clsArrow . '"> <a href="' . $public_path . '/' . 'kpi/branch/view' . '/' . \Crypt::encrypt($branch['branch_id']) . '">' . $branch['br_name'] . '<span style="float: right;padding-right:10px;">' . number_format($branch['totSale'], 2) . '</span></a></li>';
                }
                $reg_enc_id = \Crypt::encrypt($region_id);
                $strHtml .= '</ul></div><input type="hidden" id="reg_enc_id" value="' . $reg_enc_id . '">';

                echo $strHtml;
            } else {
                echo "No Branches";
            }
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function filterbranches() {
        try {
            $region_id = Input::get('region_id');
            $region_name = Input::get('region_name');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $search = Input::get('search');

            if ($first_day != '') {
                $first_day = explode('-', $first_day);
                $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
            }
            if ($last_day != '') {
                $last_day = explode('-', $last_day);
                $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
            }

            $branches = DB::table('master_resources as branch')
                    ->select("branch.id as branchid", "branch.name as br_name")
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereRaw("area.region_id=$region_id")
                    ->when($search, function($query) use($search) {
                        return $query->whereRaw("branch.name like '%$search%'");
                    })
                    ->orderby("br_name", "ASC")
                    ->get();

            $arrAllBranch = array();
            if (count($branches) != 0) {
                $arrAllBranch = $branches->pluck("branchid")->toArray();
            }

            $branch_sales = DB::table('pos_sales')
                    ->select("pos_sales.branch_id", "branch.name as br_name", db::raw('sum(pos_sales.total_sale) as total'))
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->whereRaw("(date(pos_sales.pos_date)>='$first_day' and date(pos_sales.pos_date)<='$last_day') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->whereIn('pos_sales.branch_id', $arrAllBranch)
                    ->when($search, function($query) use($search) {
                        return $query->whereRaw("branch.name like '%$search%'");
                    })
                    ->groupBy(['pos_sales.branch_id', 'branch.name'])
                    ->orderby("total", "DESC")
                    ->get();

            $regionTotalSale = 0;
            if (count($branch_sales) > 0) {
                $regionTotalSale = $branch_sales->sum('total');
            }

            $branchOrderOfSale = array();
            foreach ($branch_sales as $branch) {
                $branchOrderOfSale[$branch->branch_id] = array("branch_id" => $branch->branch_id, "br_name" => $branch->br_name, "totSale" => $branch->total, "targetSale" => '');
            }

            //************************* target for branches ****************************     
            $quarter = DB::table('sales_target')
                    ->select('target_quarter', 'target_year', 'start_date', 'end_date', db::raw('concat(target_year,"-",target_quarter) as quarter'), db::raw('DATEDIFF(end_date,start_date) as DaysInQuarter'))
                    ->whereraw("date(sales_target.end_date)>='$first_day' AND date(sales_target.start_date)<='$last_day' AND sales_target.status=1")
                    ->distinct('quarter')
                    ->orderby('quarter', 'ASC')
                    ->get();

            $start_date = date_create($first_day);
            $end_date = date_create($last_day);

            foreach ($branchOrderOfSale as $br) {
                $periodTargetAmt = 0;
                for ($i = 0; $i < count($quarter); $i++) {
                    $targetamount = DB::table('sales_target')
                            ->select(db::raw('COALESCE(SUM(target_amount),0) as targetamount'))
                            ->whereraw("sales_target.target_quarter=" . $quarter[$i]->target_quarter . " AND sales_target.target_year=" . $quarter[$i]->target_year . "")
                            ->whereraw("sales_target.branch_id=" . $br['branch_id'])
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
                $br['targetSale'] = $periodTargetAmt;
            }
            //******************************* ends *************************************     

            foreach ($branches as $branch) {
                if (!key_exists($branch->branchid, $branchOrderOfSale)) {
                    $branchOrderOfSale[$branch->branchid] = array("branch_id" => $branch->branchid, "br_name" => $branch->br_name, "totSale" => 0, "targetSale" => 0);
                }
            }

            $public_path = url('/');
            if (count($branchOrderOfSale) != 0) {

                $strHtml = '<div id="branchpop"><ul class="listTypeV2">';

                foreach ($branchOrderOfSale as $branch) {
                    if ($branch['targetSale'] != 0 && $branch['totSale'] >= $branch['targetSale']) {
                        $clsArrow = "plus";
                    } else {
                        $clsArrow = "down";
                    }

                    $strHtml .= '<li class="' . $clsArrow . '"> <a href="' . $public_path . '/' . 'kpi/branch/view' . '/' . \Crypt::encrypt($branch['branch_id']) . '">' . $branch['br_name'] . '<span style="float: right;padding-right:10px;">' . number_format($branch['totSale'], 2) . '</span></a></li>';
                }

                $strHtml .= '</ul></div>';

                echo $strHtml;
            } else {
                echo "No Branches";
            }
        } catch (\Exception $e) {
            return -1;
        }
    }

}
