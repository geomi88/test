<?php

namespace App\Http\Controllers\Supervisors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;
use App\Models\Pos_sale;
use App\Models\Masterresources;
use DB;
use DateTime;

class BranchController extends Controller {

    public function view($id) {
        try {
            $search_key = Input::get('search_key');
            $selected_quarter = Input::get('selected_quarter');
            $branch_id = \Crypt::decrypt($id);
            $current_year = date('Y');
            $current_quarter = ceil(date('n') / 3);
            $start_date = "$current_year-01-01";
            $end_date = "$current_year-03-31";
            if ($search_key == '') {
                $search_key = "Month";
            }
            if ($selected_quarter == '') {
                $selected_quarter = $current_quarter;
            }
            $current_date = date('Y-m-d');
            /* $search_date = date('Y-m-d', strtotime($current_date) - (30 * 24 * 3600));
              $previous_start_date = date('Y-m-d', strtotime($current_date) - (60 * 24 * 3600));
              $previous_end_date = date('Y-m-d', strtotime($current_date) - (31 * 24 * 3600));

              if ($search_key == 'Week') {
              $search_date = date('Y-m-d', strtotime($current_date) - (7 * 24 * 3600));
              $previous_start_date = date('Y-m-d', strtotime($current_date) - (14 * 24 * 3600));
              $previous_end_date = date('Y-m-d', strtotime($current_date) - (8 * 24 * 3600));
              }
              if ($search_key == 'Quarter') {
              $search_date = date('Y-m-d', strtotime($current_date) - (90 * 24 * 3600));
              $previous_start_date = date('Y-m-d', strtotime($current_date) - (180 * 24 * 3600));
              $previous_end_date = date('Y-m-d', strtotime($current_date) - (91 * 24 * 3600));
              } */

            $search_date = date('Y-m-01', strtotime($current_date));
            $previous_start_date = date('Y-m-01', strtotime('-1 month', strtotime($search_date)));
            $previous_end_date = date('Y-m-31', strtotime('-1 month', strtotime($search_date)));
            if ($search_key == 'Week') {
                $search_date = date('Y-m-d', strtotime($current_date) - (7 * 24 * 3600));
                $previous_start_date = date('Y-m-d', strtotime($current_date) - (14 * 24 * 3600));
                $previous_end_date = date('Y-m-d', strtotime($current_date) - (8 * 24 * 3600));
            }
            if ($search_key == 'Quarter') {

                if ($current_quarter == 1) {
                    $search_date = "$current_year-01-01";
                }
                if ($current_quarter == 2) {
                    $search_date = "$current_year-04-01";
                }
                if ($current_quarter == 3) {
                    $search_date = "$current_year-07-01";
                }
                if ($current_quarter == 4) {
                    $search_date = "$current_year-10-01";
                }
                $previous_start_date = date('Y-m-d', strtotime($search_date) - (180 * 24 * 3600));
                $previous_end_date = date('Y-m-d', strtotime($search_date) - (91 * 24 * 3600));
            }

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

            $branch_details = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where(['id' => $branch_id])
                    ->first();

            $branch_workers = DB::table('resource_allocation')
                    ->select('employees.first_name', 'employees.alias_name', 'employees.profilepic', 'country.flag_32 AS flag_pic', 'country.name AS country_name', 'resource_allocation.resource_type')
                    ->leftjoin('employees', 'resource_allocation.employee_id', '=', 'employees.id')
                    ->leftjoin('country', 'employees.nationality', '=', 'country.id')
                    ->whereRaw("(resource_allocation.branch_id = $branch_id and resource_allocation.active = 1) and (resource_allocation.resource_type = 'SUPERVISOR' or resource_allocation.resource_type = 'CASHIER')")
                    ->get();

            $branch_contact_persons = DB::table('resource_allocation')
                    ->select('employees.id AS emp_id', 'employees.first_name', 'employees.alias_name', 'employees.profilepic', 'country.flag_32 AS flag_pic', 'country.name AS country_name', 'resource_allocation.resource_type')
                    ->leftjoin('employees', 'resource_allocation.employee_id', '=', 'employees.id')
                    ->leftjoin('country', 'employees.nationality', '=', 'country.id')
                    ->whereRaw("(resource_allocation.branch_id = $branch_id and resource_allocation.active = 1) and (resource_allocation.resource_type = 'SUPERVISOR' or resource_allocation.resource_type = 'CASHIER')")
                    ->get();


            $branch_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("date(pos_sales.pos_date) >= '$search_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id and pos_sales.status=1")
                    ->first();
            $previous_total_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id'))
                    ->whereRaw("date(pos_sales.pos_date) >= '$previous_start_date' and date(pos_sales.pos_date) <= '$previous_end_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id and pos_sales.status=1")
                    ->first();
            if ($branch_sales->total > $previous_total_sales->total) {
                $profit_data['profit'] = "plus";
            } else {
                $profit_data['profit'] = "down";
            }


            $full_graph_data = array();
            $branch_shifts = $branch_details->shift_id;
            $branch_shifts = explode(',', $branch_shifts);
            $color_array = ["#0786D7", "#851E32", "#f01111", "#604d06"];
            $i = 0;
            foreach ($branch_shifts as $shift_id) {
                $shift_details = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['id' => $shift_id])
                        ->first();
                $quarter_sales = DB::table('pos_sales')
                        ->select('pos_sales.*', 'master_resources.name as job_shift_name')
                        ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.job_shift_id')
                        ->whereRaw("(date(pos_sales.pos_date) BETWEEN '$start_date' and '$end_date') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.branch_id = $branch_id and pos_sales.job_shift_id = $shift_id")
                        ->orderby('pos_sales.pos_date')
                        ->get();
                $graph_data = array();
                foreach ($quarter_sales as $quarter_sale) {
                    $sale_created_date = $quarter_sale->pos_date;
                    $sale_created_date = explode(" ", $sale_created_date);
                    $sale_created_date = $sale_created_date[0];
//                $date = new DateTime("$sale_created_date");
//                $date->format("U");
//                $row['x'] = $date->getTimestamp()*1000;
                    $row['x'] = ((int) strtotime($sale_created_date)) * 1000;
                    $row['y'] = $quarter_sale->total_sale;
                    $row['z'] = $quarter_sale->job_shift_name;

                    array_push($graph_data, $row);
                }

                $full_row['name'] = $shift_details->name;
                $full_row['data'] = $graph_data;
                $full_row['color'] = $color_array[$i];
                $full_row['visible'] = false;
                $i++;
                array_push($full_graph_data, $full_row);
            }

            // Sales target graph

            $branch_target = DB::table('sales_target')
                    ->select("sales_target.target_amount")
                    ->whereRaw("branch_id = $branch_id AND target_year=$current_year AND target_quarter=$selected_quarter AND status=1")
                    ->first();

            if (count($branch_target) > 0) {

                $graph_data = array();
                $date1 = date_create($start_date);
                $date2 = date_create($end_date);
                $interval = date_diff($date1, $date2)->days;
                $interval = $interval + 1;

                $amount = ((int) $branch_target->target_amount) / $interval;
                $row['x'] = ((int) strtotime($start_date)) * 1000;
                $row['y'] = $amount;
                $row['z'] = "Target";

                array_push($graph_data, $row);

                $row['x'] = ((int) strtotime($end_date)) * 1000;
                $row['y'] = $amount;
                $row['z'] = "Target";

                array_push($graph_data, $row);


                $full_row['name'] = "Target";
                $full_row['data'] = $graph_data;
                $full_row['color'] = '#bd0c04';
                $full_row['visible'] = true;

                array_push($full_graph_data, $full_row);
            }

            // sales target graph endes

            /* -------For taking values for all shifts------- */

            $quarter_sales1 = DB::table('pos_sales')
                    ->select("pos_sales.pos_date", db::raw('sum(pos_sales.total_sale) as total_sale'))
                    ->whereRaw("(date(pos_sales.pos_date) BETWEEN '$start_date' and '$end_date') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.branch_id = $branch_id")
                    ->groupBy('pos_sales.pos_date')
                    ->orderby('pos_sales.pos_date')
                    ->get();

            $graph_data1 = array();
            foreach ($quarter_sales1 as $quarter_sale) {
                $sale_created_date = $quarter_sale->pos_date;
                $sale_created_date = explode(" ", $sale_created_date);
                $sale_created_date = $sale_created_date[0];

                $row['x'] = ((int) strtotime($sale_created_date)) * 1000;
                $row['y'] = (int) $quarter_sale->total_sale;
                $row['z'] = "All Shift";

                array_push($graph_data1, $row);
            }

            $full_row['name'] = "All Shift";
            $full_row['data'] = $graph_data1;
            $full_row['color'] = '#02838e';
            $full_row['visible'] = true;

            array_push($full_graph_data, $full_row);

            /* --------------------------------------------- */
            /*Bottom Sale Line Target Graph*/
            $bottom_line_graph_data = array();
            $amount = ((int) ($branch_details->bottom_sale_line/30));
            $row['x'] = ((int) strtotime($start_date)) * 1000;
            $row['y'] = $amount;
            $row['z'] = "Bottom Sale Line Target";
            array_push($bottom_line_graph_data, $row);

            $row['x'] = ((int) strtotime($end_date)) * 1000;
            $row['y'] = $amount;
            $row['z'] = "Bottom Sale Line Target";
            array_push($bottom_line_graph_data, $row);

            $full_row['name'] = "Bottom Sale Line Target";
            $full_row['data'] = $bottom_line_graph_data;
            $full_row['color'] = 'black';
            $full_row['visible'] = true;
            array_push($full_graph_data, $full_row);
            /*-----*/
            
            $full_graph_data = array_values($full_graph_data);
            $full_graph_data = json_encode($full_graph_data);



            if ($current_quarter == 1) {
                $start_date = "$current_year-01-01";
                $end_date = "$current_year-03-31";
            }
            if ($current_quarter == 2) {
                $start_date = "$current_year-04-01";
                $end_date = "$current_year-06-30";
            }
            if ($current_quarter == 3) {
                $start_date = "$current_year-07-01";
                $end_date = "$current_year-09-30";
            }
            if ($current_quarter == 4) {
                $start_date = "$current_year-10-01";
                $end_date = "$current_year-12-31";
            }
            $quarter_branch_target = DB::table('sales_target')
                    ->select("sales_target.target_amount")
                    ->whereRaw("branch_id = $branch_id AND target_year=$current_year AND target_quarter=$current_quarter AND status=1")
                    ->first();
            $current_quarter_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("date(pos_sales.pos_date) >= '$start_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id and pos_sales.status=1")
                    ->first();

            $current_quarter_morning_shift_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("date(pos_sales.pos_date) >= '$start_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id and pos_sales.job_shift_id in (select id from master_resources where name='Morning Shift') and pos_sales.status=1")
                    ->first();
            $current_quarter_evening_shift_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("date(pos_sales.pos_date) >= '$start_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id and pos_sales.job_shift_id in (select id from master_resources where name='Evening Shift ') and pos_sales.status=1")
                    ->first();

            $current_sales_details = array();
            $current_sales_details['quarter_target_amount'] = $quarter_branch_target->target_amount;
            $current_sales_details['quarter_achieved_sales'] = $current_quarter_sales->total;
            $current_sales_details['quarter_achieved_sales_percentage'] = ($current_quarter_sales->total / $quarter_branch_target->target_amount) * 100;
            $current_sales_details['quarter_sales_variance_percentage'] = 100 - (($current_quarter_sales->total / $quarter_branch_target->target_amount) * 100);

            $number_of_days = strtotime($end_date) - strtotime($start_date);
            $number_of_days = floor($number_of_days / (60 * 60 * 24)) + 1;
            $current_sales_details['daily_target_amount'] = $quarter_branch_target->target_amount / $number_of_days;
            $current_sales_details['daily_achieved_sales'] = $current_quarter_sales->total / $number_of_days;
            $current_sales_details['daily_achieved_sales_percentage'] = ($current_sales_details['daily_achieved_sales'] / $current_sales_details['daily_target_amount']) * 100;
            $current_sales_details['daily_sales_variance_percentage'] = 100 - (($current_sales_details['daily_achieved_sales'] / $current_sales_details['daily_target_amount']) * 100);

            $current_no_of_days = time() - strtotime($start_date);
            $current_no_of_days = floor($current_no_of_days / (60 * 60 * 24));
            $current_sales_details['current_quarter_morning_shift_sales'] = $current_quarter_morning_shift_sales->total;
            $current_sales_details['morning_shift_achieved_sales_percentage'] = ($current_quarter_morning_shift_sales->total / $current_quarter_sales->total) * 100;
            $current_sales_details['morning_shift_average_sales'] = $current_quarter_morning_shift_sales->total / $current_no_of_days;
            $current_sales_details['current_quarter_evening_shift_sales'] = $current_quarter_evening_shift_sales->total;
            $current_sales_details['evening_shift_achieved_sales_percentage'] = ($current_quarter_evening_shift_sales->total / $current_quarter_sales->total) * 100;
            $current_sales_details['evening_shift_average_sales'] = $current_quarter_evening_shift_sales->total / $current_no_of_days;
            
            $branch_start_date=date("Y-m-d",strtotime($branch_details->branch_start_date));
            $currentDate=date("Y-m-d");
            $d1 = date_create("$branch_start_date");
            $d2 = date_create("$currentDate");
            $interval = date_diff($d1, $d2)->days;
            
            $branchdate=date("d-m-Y",strtotime($branch_details->branch_start_date));
            $workingdays = $interval + 1;
            $current_sales_details['branch_start_date'] = $branchdate;
            $current_sales_details['workingdays'] = $workingdays;
            
            return view('supervisors/branch/view', array('search_key' => $search_key, 'branch_sales' => $branch_sales, 'branch_details' => $branch_details, 'branch_workers' => $branch_workers, 'profit_data' => $profit_data, 'previous_total_sales' => $previous_total_sales, 'branch_contact_persons' => $branch_contact_persons, 'graph_data' => $full_graph_data, 'selected_quarter' => $selected_quarter, 'current_sales_details' => $current_sales_details));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('supervisors');
        }
    }

}
