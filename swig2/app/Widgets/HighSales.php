<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Session;
use DB;

class HighSales extends AbstractWidget {

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
        $employee_id = Session::get('login_id');
        $employee_data = DB::table('employees')
                        ->select('employees.*')
                        ->where(['id' => $employee_id])->first();
        $search_key = $this->config['search_key'];
        if ($search_key == '') {
            $search_key = "Month";
        }
        $current_date = date('Y-m-d');
        $search_date = date('Y-m-d', strtotime($current_date) - (30 * 24 * 3600));

        if ($search_key == 'Week') {
            $search_date = date('Y-m-d', strtotime($current_date) - (7 * 24 * 3600));
        }
        if ($search_key == 'Quarter') {
            $search_date = date('Y-m-d', strtotime($current_date) - (90 * 24 * 3600));
        }
        if ($employee_data->privilege_status == 1) {
            $top_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->groupby(DB::raw("pos_sales.branch_id"))
                    ->orderby('total', 'DESC')
                    ->whereRaw("date(pos_sales.pos_date) > '$search_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->limit(5)
                    ->get();
        } else {
            $top_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->groupby(DB::raw("pos_sales.branch_id"))
                    ->orderby('total', 'DESC')
                    ->whereRaw("date(pos_sales.pos_date) > '$search_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.branch_id in (select branch_id from branches_to_analyst where analyst_id = $employee_id and status = 1)")
                    ->limit(5)
                    ->get();
        }
        $branches = '';
        $total_sale = '';
        foreach ($top_sales as $top_sale) {
            $branches = $branches . "'" . $top_sale->branch_name . "',";
            $total_sale = $total_sale . $top_sale->total . ",";
        }
        $branches = trim($branches, ',');
        $total_sale = trim($total_sale, ',');
        return view("widgets.high_sales", [
            'config' => $this->config, 'branches' => $branches, 'total_sale' => $total_sale, 'search_key' => $search_key
        ]);
    }

}
