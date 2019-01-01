<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Session;
use DB;

class BranchesSales extends AbstractWidget {

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
        $current_year = date('Y');
        $current_quarter = ceil(date('n') / 3);
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
        if ($employee_data->privilege_status == 1) {
            $total_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->groupby(DB::raw("pos_sales.branch_id"))
                    ->orderby('total', 'DESC')
                    ->whereRaw("date(pos_sales.pos_date) > '$search_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
//                    ->when($search_branch, function ($query) use ($search_branch) {
//                        return $query->whereRaw("master_resources.name like '$search_branch%'");
//                    })
                    ->get();

        } else {
            $total_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id,master_resources.name as branch_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'pos_sales.branch_id')
                    ->groupby(DB::raw("pos_sales.branch_id"))
                    ->orderby('total', 'DESC')
                    ->whereRaw("date(pos_sales.pos_date) > '$search_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and  pos_sales.branch_id in (select branch_id from branches_to_analyst where analyst_id = $employee_id and status = 1)")
//                   ->when($search_branch, function ($query) use ($search_branch) {
//                        return $query->whereRaw("master_resources.name like '$search_branch%'");
//                    })
                    ->get();
        }
        $branches_sales = array();
        foreach ($total_sales as $total_sale) {
            $branch_id = $total_sale->branch_id;
            $previous_total_sales = DB::table('pos_sales')
                    ->select(DB::raw(' sum(pos_sales.total_sale) as total,pos_sales.branch_id'))
                    ->whereRaw("date(pos_sales.pos_date) > '$previous_start_date' and date(pos_sales.pos_date) < '$previous_end_date' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.branch_id = $branch_id and pos_sales.status=1")
                    ->first();
            if ($total_sale->total > $previous_total_sales->total) {
                $row['profit'] = "plus";
            } else {
                $row['profit'] = "down";
            }
            $row['branch_id'] = $total_sale->branch_id;
            $row['total'] = $total_sale->total;
            $row['branch_name'] = $total_sale->branch_name;
            array_push($branches_sales, $row);
        }

        return view("widgets.branches_sales", [
            'config' => $this->config, 'branches_sales' => $branches_sales
        ]);
        
         
        
    }
    

}
