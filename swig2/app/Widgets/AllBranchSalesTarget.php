<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Session;
use DB;

class AllBranchSalesTarget extends AbstractWidget {

    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run() {
        //
        $employee_id = Session::get('login_id');
        $employee_data = DB::table('employees')
                        ->select('employees.*')
                        ->where(['id' => $employee_id])->first();
        $current_year = date('Y');
        $current_quarter = ceil(date('n') / 3);
        $selected_year = $this->config['selected_year'];
        $selected_quarter = $this->config['selected_quarter'];
        if ($selected_year != '') {
            $current_year = $selected_year;
        }
        if ($selected_quarter == '') {
            $selected_quarter = $current_quarter;
        }
        $next_year = $current_year + 1;
        if ($employee_data-> privilege_status == 1) {
            $branches = DB::table('master_resources')
                    ->select('master_resources.id as branch_id', 'master_resources.name as branch_name')
                    ->whereRaw("master_resources.resource_type = 'BRANCH' and master_resources.status = 1")
                    ->orderby('master_resources.name')
                    ->get();
        } else {
            $branches = DB::table('branches_to_analyst')
                    ->select('branches_to_analyst.branch_id', 'master_resources.name as branch_name')
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'branches_to_analyst.branch_id')
                    ->whereRaw("branches_to_analyst.analyst_id = $employee_id and branches_to_analyst.status = 1")
                    ->orderby('master_resources.name')
                    ->get();
        }
        $full_result = array();
        foreach ($branches as $branch) {
            $row['branch_name'] = $branch->branch_name;
            $branch_id = $branch->branch_id;

            if ($selected_quarter == 1) {
                $quarter_start_date = "$current_year-01-01";
                $quarter_end_date = "$current_year-03-31";

                $first_month_start_date = "$current_year-01-01";
                $first_month_end_date = "$current_year-01-31";

                $second_month_start_date = "$current_year-02-01";
                $second_month_end_date = "$current_year-02-29";

                $third_month_start_date = "$current_year-03-01";
                $third_month_end_date = "$current_year-03-31";
            }
            if ($selected_quarter == 2) {
                $quarter_start_date = "$current_year-04-01";
                $quarter_end_date = "$current_year-06-30";

                $first_month_start_date = "$current_year-04-01";
                $first_month_end_date = "$current_year-04-30";

                $second_month_start_date = "$current_year-05-01";
                $second_month_end_date = "$current_year-05-31";

                $third_month_start_date = "$current_year-06-01";
                $third_month_end_date = "$current_year-06-30";
            }
            if ($selected_quarter == 3) {
                $quarter_start_date = "$current_year-07-01";
                $quarter_end_date = "$current_year-09-30";

                $first_month_start_date = "$current_year-07-01";
                $first_month_end_date = "$current_year-07-31";

                $second_month_start_date = "$current_year-08-01";
                $second_month_end_date = "$current_year-08-31";

                $third_month_start_date = "$current_year-09-01";
                $third_month_end_date = "$current_year-09-30";
            }
            if ($selected_quarter == 4) {
                $quarter_start_date = "$current_year-10-01";
                $quarter_end_date = "$current_year-12-31";

                $first_month_start_date = "$current_year-10-01";
                $first_month_end_date = "$current_year-10-31";

                $second_month_start_date = "$current_year-11-01";
                $second_month_end_date = "$current_year-11-30";

                $third_month_start_date = "$current_year-12-01";
                $third_month_end_date = "$current_year-12-31";
            }
            $branch_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("(date(pos_sales.pos_date) BETWEEN '$quarter_start_date' and '$quarter_end_date') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.branch_id = $branch_id")
                    ->first();
            $row['total_sales'] = $branch_sales->total;

            $first_month_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("(date(pos_sales.pos_date) BETWEEN '$first_month_start_date' and '$first_month_end_date') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.branch_id = $branch_id")
                    ->first();

            $second_month_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("(date(pos_sales.pos_date) BETWEEN '$second_month_start_date' and '$second_month_end_date') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.branch_id = $branch_id")
                    ->first();
            $third_month_sales = DB::table('pos_sales')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->whereRaw("(date(pos_sales.pos_date) BETWEEN '$third_month_start_date' and '$third_month_end_date') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.branch_id = $branch_id")
                    ->first();
            $row['quarter_sales'] = $first_month_sales->total . ", " . $second_month_sales->total . ", " . $third_month_sales->total . " ";


            $branch_total_target = DB::table('sales_target')
                    ->select(DB::raw('sum(sales_target.target_amount) as target_amount'))
                    ->whereRaw("sales_target.start_date >= '$quarter_start_date' and sales_target.end_date <= '$quarter_end_date' and sales_target.branch_id = $branch_id")
                    ->first();
            $first_month_target = number_format((float) ($branch_total_target->target_amount) / 3, 2, '.', '');
            $second_month_target = number_format((float) ($branch_total_target->target_amount) / 3, 2, '.', '');
            $third_month_target = number_format((float) ($branch_total_target->target_amount) / 3, 2, '.', '');


            $row['target_amount'] = $branch_total_target->target_amount;
            $row['quarter_target'] = $first_month_target . ", " . $second_month_target . ", " . $third_month_target . " ";


            /* $branch_sales = DB::table('pos_sales')
              ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
              ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
              ->whereRaw("(pos_sales.created_at BETWEEN '$current_year-01-01' and '$current_year-12-31') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id")
              ->first();
              $row['total_sales'] = $branch_sales->total;

              $first_quarter_sales = DB::table('pos_sales')
              ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
              ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
              ->whereRaw("(pos_sales.created_at BETWEEN '$current_year-01-01' and '$current_year-03-31') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id")
              ->first();

              $second_quarter_sales = DB::table('pos_sales')
              ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
              ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
              ->whereRaw("(pos_sales.created_at BETWEEN '$current_year-04-01' and '$current_year-06-30') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id")
              ->first();
              $third_quarter_sales = DB::table('pos_sales')
              ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
              ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
              ->whereRaw("(pos_sales.created_at BETWEEN '$current_year-07-01' and '$current_year-09-30') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id")
              ->first();
              $fourth_quarter_sales = DB::table('pos_sales')
              ->select(DB::raw('sum(pos_sales.total_sale) as total,sum(pos_sales.cash_collection) as cash_collection,sum(pos_sales.credit_sale) as credit_sale,sum(pos_sales.bank_sale) as bank_sale,pos_sales.branch_id,master_resources.name as branch_name'))
              ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
              ->whereRaw("(pos_sales.created_at BETWEEN '$current_year-10-01' and '$current_year-12-31') and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id")
              ->first();
              $row['quarter_sales'] = $first_quarter_sales->total.", ".$second_quarter_sales->total.", ".$third_quarter_sales->total.", ".$fourth_quarter_sales->total." ";
              $branch_total_target = DB::table('sales_target')
              ->select(DB::raw('sum(sales_target.target_amount) as target_amount'))
              ->whereRaw("sales_target.start_date >= '$current_year-01-01' and sales_target.end_date <= '$current_year-12-31' and sales_target.branch_id = $branch_id")
              ->first();
              $first_quarter_target = DB::table('sales_target')
              ->select(DB::raw('sum(sales_target.target_amount) as target_amount'))
              ->whereRaw("sales_target.start_date >= '$current_year-01-01' and sales_target.end_date <= '$current_year-03-31' and sales_target.branch_id = $branch_id")
              ->first();
              $second_quarter_target = DB::table('sales_target')
              ->select(DB::raw('sum(sales_target.target_amount) as target_amount'))
              ->whereRaw("sales_target.start_date >= '$current_year-04-01' and sales_target.end_date <= '$current_year-06-30' and sales_target.branch_id = $branch_id")
              ->first();
              $third_quarter_target = DB::table('sales_target')
              ->select(DB::raw('sum(sales_target.target_amount) as target_amount'))
              ->whereRaw("sales_target.start_date >= '$current_year-07-01' and sales_target.end_date <= '$current_year-09-30' and sales_target.branch_id = $branch_id")
              ->first();

              $fourth_quarter_target = DB::table('sales_target')
              ->select(DB::raw('sum(sales_target.target_amount) as target_amount'))
              ->whereRaw("sales_target.start_date >= '$current_year-10-01' and sales_target.end_date <= '$current_year-12-31' and sales_target.branch_id = $branch_id")
              ->first();
              $row['target_amount'] = $branch_total_target->target_amount;
              $row['quarter_target'] = $first_quarter_target->target_amount.", ".$second_quarter_target->target_amount.", ".$third_quarter_target->target_amount.", ".$fourth_quarter_target->target_amount." "; */
            array_push($full_result, $row);
        }
        return view("widgets.all_branch_sales_target", [
            'config' => $this->config, 'full_result' => $full_result
        ]);
    }

}
