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
use Excel;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Pos_sale;
use DB;
use Customhelper;

class PossupervisorController extends Controller {

    public function index(Request $request) {
        $created = $custom_frm_date = (Input::get("from_date") ? date("Y-m-d", strtotime(Input::get("from_date"))) : '');
        $ended = $custom_to_date = (Input::get("to_date") ? date("Y-m-d", strtotime(Input::get("to_date"))) : '');
        if ($request->ajax()) {
            $custom_branch = '';
            $branch = '';
        } else {
            $custom_branch = (Input::get("branch_name") ? (\Crypt::decrypt(Input::get("branch_name"))) : '');
            $branch = $custom_branch;
        }
        $paginate = Config::get('app.PAGINATE');
        $pos_sales = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as cashier_fname', 'employee_details1.alias_name as cashier_aname', 'employee_details2.first_name as editedby_name', 'employee_details2.alias_name as editedby_aname', DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
                ->whereRaw("pos_sales.status=1")
                ->when($branch, function ($query) use ($branch) {
                    return $query->whereRaw("branch_details.id=$branch");
                })
                ->when($created, function ($query) use ($created, $ended) {
                    return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                })
                ->paginate($paginate);
        $t_pos_sales = DB::table('pos_sales')
                ->selectraw('SUM(opening_amount) as t_opening_amount,'
                            . 'SUM(total_sale) as t_total_sale,'
                            . 'SUM(cash_collection) as t_cash_collection,'
                            . 'SUM(credit_sale) as t_credit_sale,'
                            . 'SUM(bank_sale) as t_bank_sale,'
                            . '(SUM(cash_sale) - SUM(cash_collection)) as t_cashdifference,'
                            . '(SUM(bank_sale) - SUM(bank_collection)) as t_bankdifference, '
                            . 'SUM(difference) as t_difference,SUM(meal_consumption) as t_meal_consumption')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
                ->whereRaw("pos_sales.status=1")
                ->when($branch, function ($query) use ($branch) {
                    return $query->whereRaw("branch_details.id=$branch");
                })
                ->when($created, function ($query) use ($created, $ended) {
                    return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                })
                ->first();

        $branch_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'branch_details.id as branch_id')->distinct()
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->when($branch, function ($query) use ($branch) {
                    return $query->whereRaw("branch_details.id=$branch");
                })
                ->get();
        $shift_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('jobshift_details.name as jobshift_name', 'jobshift_details.id as jobshift_id')->distinct()
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->get();

        if ($request->ajax()) {

            $branch_names = DB::table('pos_sales')
                    ->select('pos_sales.*')->select('branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'branch_details.id as branch_id')->distinct()
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->get();

            $sort = Input::get('sorting');
            $cashsort = Input::get('cashsort');

            $edit_searchkey = Input::get('edit_searchkey');
            $editedsort = Input::get('editedsort');


            $opensort = Input::get('opensort');
            $totsort = Input::get('totsort');
            $creditsort = Input::get('creditsort');
            $banksort = Input::get('banksort');
            $diffsort = Input::get('diffsort');
            $cashdiffsort = Input::get('cashdiffsort');
            $bankdiffsort = Input::get('bankdiffsort');

            $cashiersort = Input::get('cashiersort');
            $mealsort = Input::get('mealsort');


            $searchkey = Input::get('searchkey');
            if ($custom_branch != '') {
                $branch = $custom_branch;
            } else {
                $$custom_branch = $branch = Input::get('branch');
            }
            $sup_searchkey = Input::get('sup_searchkey');
            $corder = Input::get('corder');
            $oorder = Input::get('oorder');
            $torder = Input::get('torder');
            $crorder = Input::get('crorder');
            $border = Input::get('border');
            $dorder = Input::get('dorder');
            $morder = Input::get('morder');
            $cdorder = Input::get('cdorder');
            $bdorder = Input::get('bdorder');



            $camount = Input::get('camount');
            $oamount = Input::get('oamount');
            $tamount = Input::get('tamount');
            $cramount = Input::get('cramount');
            $tamount = Input::get('tamount');
            $bamount = Input::get('bamount');
            $damount = Input::get('damount');
            $mamount = Input::get('mamount');
            $cdamount = Input::get('cdamount');
            $bdamount = Input::get('bdamount');



            $shift = Input::get('shift');
            if ($custom_frm_date != '') {
                $created = $custom_frm_date;
            } else {
                $created = Input::get('startdate');
                if ($created != '') {
                    $created = explode('-', $created);
                    $created = $created[2] . '-' . $created[1] . '-' . $created[0];
                }
            }
            if ($custom_to_date != '') {
                $ended = $custom_to_date;
            } else {
                $ended = Input::get('enddate');
                if ($ended != '') {
                    $ended = explode('-', $ended);
                    $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
                }
            }
            
            if (empty($ended) && empty($custom_to_date)) {
                $custom_to_date = $ended = DATE('Y-m-d');
            }
            $paginate = Input::get('pagelimit');

            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            //$cashdifforder="pos_sales.cash_sale-pos_sales.cash_collection";
            // $bankdifforder="pos_sales.bank_sale-pos_sales.bank_collection";

            /* $pos_sales = DB::table('pos_sales')
              ->select('pos_sales.*', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as cashier_fname', 'employee_details1.alias_name as cashier_aname',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
              ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
              ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
              ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
              ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
              ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=','reason.id')
              ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
              ->whereRaw('pos_sales.status=1') */
            $pos_sales = DB::table('pos_sales')
                    ->select('pos_sales.*', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as cashier_fname', 'employee_details1.alias_name as cashier_aname', 'employee_details2.first_name as editedby_name', 'employee_details2.alias_name as editedby_aname', DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                    ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                    ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                    ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                    ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
                    ->whereRaw('pos_sales.status=1')
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                    })
                    ->when($sup_searchkey, function ($query) use ($sup_searchkey) {
                        return $query->whereRaw("(employee_details1.first_name like '$sup_searchkey%' or concat(employee_details1.alias_name) like '$sup_searchkey%')");
                    })
                    ->when($edit_searchkey, function ($query) use ($edit_searchkey) {
                        return $query->whereRaw("(employee_details2.first_name like '$edit_searchkey%' or concat(employee_details2.alias_name) like '$edit_searchkey%')");
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->whereRaw("branch_details.id=$branch");
                    })
                    ->when($shift, function ($query) use ($shift) {
                        return $query->whereRaw("jobshift_details.id=$shift");
                    })
                    ->when($created, function ($query) use ($created, $ended) {
                        return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                    })
                    ->when($camount, function ($query) use ($camount, $corder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.cash_collection $corder $camount ");
                    })
                    ->when($oamount, function ($query) use ($oamount, $oorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.opening_amount $oorder $oamount ");
                    })
                    ->when($tamount, function ($query) use ($tamount, $torder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.total_sale $torder $tamount ");
                    })
                    ->when($cramount, function ($query) use ($cramount, $crorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.credit_sale $crorder $cramount ");
                    })
                    ->when($bamount, function ($query) use ($bamount, $border) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.bank_sale $border $bamount ");
                    })
                    ->when($damount, function ($query) use ($damount, $dorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.difference $dorder $damount ");
                    })
                    ->when($mamount, function ($query) use ($mamount, $morder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.meal_consumption $morder $mamount ");
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('pos_sales.cash_collection', $cashsort);
                    })
                    ->when($opensort, function ($query) use ($opensort) {
                        return $query->orderby('pos_sales.opening_amount', $opensort);
                    })
                    ->when($totsort, function ($query) use ($totsort) {
                        return $query->orderby('pos_sales.total_sale', $totsort);
                    })
                    ->when($creditsort, function ($query) use ($creditsort) {
                        return $query->orderby('pos_sales.credit_sale', $creditsort);
                    })
                    ->when($banksort, function ($query) use ($banksort) {
                        return $query->orderby('pos_sales.bank_sale', $banksort);
                    })
                    ->when($diffsort, function ($query) use ($diffsort) {
                        return $query->orderby('pos_sales.difference', $diffsort);
                    })
                    ->when($mealsort, function ($query) use ($mealsort) {
                        return $query->orderby('pos_sales.meal_consumption', $mealsort);
                    })
                    ->when($editedsort, function ($query) use ($editedsort) {
                        return $query->orderby('employee_details2.first_name', $editedsort);
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('employee_details.first_name', $sort);
                    })
                    ->when($cashiersort, function ($query) use ($cashiersort) {
                        return $query->orderby('employee_details1.first_name', $cashiersort);
                    })
                    ->when($cdamount, function ($query) use ($cdamount, $cdorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("(pos_sales.cash_sale-pos_sales.cash_collection) $cdorder $cdamount ");
                    })
                    ->when($bdamount, function ($query) use ($bdamount, $bdorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("(pos_sales.bank_sale-pos_sales.bank_collection) $bdorder $bdamount ");
                    })
                    ->when($cashdiffsort, function ($query) use ($cashdiffsort) {
                        return $query->orderByRaw("(pos_sales.cash_sale-pos_sales.cash_collection) $cashdiffsort");
                    })
                    ->when($bankdiffsort, function ($query) use ($bankdiffsort) {
                        return $query->orderByRaw("(pos_sales.bank_sale-pos_sales.bank_collection) $bankdiffsort");
                    })
                    ->paginate($paginate);
                    
            $t_pos_sales = DB::table('pos_sales')
                    ->selectraw('SUM(opening_amount) as t_opening_amount,'
                            . 'SUM(total_sale) as t_total_sale,'
                            . 'SUM(cash_collection) as t_cash_collection,'
                            . 'SUM(credit_sale) as t_credit_sale,'
                            . 'SUM(bank_sale) as t_bank_sale,'
                            . '(SUM(cash_sale) - SUM(cash_collection)) as t_cashdifference,'
                            . '(SUM(bank_sale) - SUM(bank_collection)) as t_bankdifference, '
                            . 'SUM(difference) as t_difference,SUM(meal_consumption) as t_meal_consumption')
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                    ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                    ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                    ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                    ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
                    ->whereRaw('pos_sales.status=1')
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                    })
                    ->when($sup_searchkey, function ($query) use ($sup_searchkey) {
                        return $query->whereRaw("(employee_details1.first_name like '$sup_searchkey%' or concat(employee_details1.alias_name) like '$sup_searchkey%')");
                    })
                    ->when($edit_searchkey, function ($query) use ($edit_searchkey) {
                        return $query->whereRaw("(employee_details2.first_name like '$edit_searchkey%' or concat(employee_details2.alias_name) like '$edit_searchkey%')");
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->whereRaw("branch_details.id=$branch");
                    })
                    ->when($shift, function ($query) use ($shift) {
                        return $query->whereRaw("jobshift_details.id=$shift");
                    })
                    ->when($created, function ($query) use ($created, $ended) {
                        return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                    })
                    ->when($camount, function ($query) use ($camount, $corder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.cash_collection $corder $camount ");
                    })
                    ->when($oamount, function ($query) use ($oamount, $oorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.opening_amount $oorder $oamount ");
                    })
                    ->when($tamount, function ($query) use ($tamount, $torder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.total_sale $torder $tamount ");
                    })
                    ->when($cramount, function ($query) use ($cramount, $crorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.credit_sale $crorder $cramount ");
                    })
                    ->when($bamount, function ($query) use ($bamount, $border) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.bank_sale $border $bamount ");
                    })
                    ->when($damount, function ($query) use ($damount, $dorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.difference $dorder $damount ");
                    })
                    ->when($mamount, function ($query) use ($mamount, $morder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("pos_sales.meal_consumption $morder $mamount ");
                    })
                    ->when($cdamount, function ($query) use ($cdamount, $cdorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("(pos_sales.cash_sale-pos_sales.cash_collection) $cdorder $cdamount ");
                    })
                    ->when($bdamount, function ($query) use ($bdamount, $bdorder) {
                        // echo "pos_sales.cash_collection $cashorder $cashamount ";
                        return $query->whereRaw("(pos_sales.bank_sale-pos_sales.bank_collection) $bdorder $bdamount ");
                    })
                    ->first();





            return view('mis/pos_sales/supervisor_report_result', array('pos_sales' => $pos_sales, 'branch_names' => $branch_names,"total_list"=>$t_pos_sales));
        }
        return view('mis/pos_sales/supervisorreports', array('pos_sales' => $pos_sales, 'branch_names' => $branch_names, 'shift_names' => $shift_names, 'custom_frm_date' => $custom_frm_date, "custom_to_date" => $custom_to_date, "custom_branch" => $custom_branch,"total_list"=>$t_pos_sales));
    }

    public function show($id) {
        $id1 = \Crypt::decrypt($id);
        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as cashier_fname', 'employee_details1.alias_name as cashier_aname', 'employee_details2.first_name as editedby_name', 'employee_details2.alias_name as editedby_aname', DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason', 'branch_details.branch_code as branch_code'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                ->where('pos_sales.added_by_user_type', '=', 'Supervisor')
                ->where('pos_sales.id', '=', $id1)
                ->first();
        return view('mis/pos_sales/supervisor_show', array('sale_details_data' => $sale_details_data, "enc_id" => $id));
    }

    public function exporttopdf() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        $sort = Input::get('sorting');
        $cashsort = Input::get('cashsort');

        $edit_searchkey = Input::get('edit_searchkey');
        $editedsort = Input::get('editedsort');


        $opensort = Input::get('opensort');
        $totsort = Input::get('totsort');
        $creditsort = Input::get('creditsort');
        $banksort = Input::get('banksort');
        $diffsort = Input::get('diffsort');

        $cashiersort = Input::get('cashiersort');
        $mealsort = Input::get('mealsort');


        $searchkey = Input::get('searchkey');
        $branch = Input::get('hidden_br_id');
        $sup_searchkey = Input::get('sup_searchkey');
        $corder = Input::get('corder');
        $oorder = Input::get('oorder');
        $torder = Input::get('torder');
        $crorder = Input::get('crorder');
        $border = Input::get('border');
        $dorder = Input::get('dorder');
        $morder = Input::get('morder');

        $camount = Input::get('camount');
        $oamount = Input::get('oamount');
        $tamount = Input::get('tamount');
        $cramount = Input::get('cramount');
        $bamount = Input::get('bamount');
        $damount = Input::get('damount');
        $mamount = Input::get('mamount');



        $shift = Input::get('shift');
        $created = date("Y-m-d", strtotime(Input::get('from_date_hidden')));
        $ended = date("Y-m-d", strtotime(Input::get('to_date_hidden')));
        $paginate = Input::get('pagelimit');

        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        /* $pos_sales = DB::table('pos_sales')
          ->select('pos_sales.*', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as cashier_fname', 'employee_details1.alias_name as cashier_aname',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
          ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
          ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
          ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
          ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
          ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=','reason.id')
          ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
          ->whereRaw('pos_sales.status=1') */
        $pos_sales = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as cashier_fname', 'employee_details1.alias_name as cashier_aname', 'employee_details2.first_name as editedby_name', 'employee_details2.alias_name as editedby_aname', DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
                ->whereRaw('pos_sales.status=1')
                ->when($searchkey, function ($query) use ($searchkey) {
                    return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                })
                ->when($sup_searchkey, function ($query) use ($sup_searchkey) {
                    return $query->whereRaw("(employee_details1.first_name like '$sup_searchkey%' or concat(employee_details1.alias_name) like '$sup_searchkey%')");
                })
                ->when($edit_searchkey, function ($query) use ($edit_searchkey) {
                    return $query->whereRaw("(employee_details2.first_name like '$edit_searchkey%' or concat(employee_details2.alias_name) like '$edit_searchkey%')");
                })
                ->when($branch, function ($query) use ($branch) {
                    return $query->whereRaw("branch_details.id=$branch");
                })
                ->when($shift, function ($query) use ($shift) {
                    return $query->whereRaw("jobshift_details.id=$shift");
                })
                ->when($created, function ($query) use ($created, $ended) {
                    return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                })
                ->when($camount, function ($query) use ($camount, $corder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.cash_collection $corder $camount ");
                })
                ->when($oamount, function ($query) use ($oamount, $oorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.opening_amount $oorder $oamount ");
                })
                ->when($tamount, function ($query) use ($tamount, $torder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.total_sale $torder $tamount ");
                })
                ->when($cramount, function ($query) use ($cramount, $crorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.opening_amount $crorder $cramount ");
                })
                ->when($bamount, function ($query) use ($bamount, $border) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.bank_sale $border $bamount ");
                })
                ->when($damount, function ($query) use ($damount, $dorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.difference $dorder $damount ");
                })
                ->when($mamount, function ($query) use ($mamount, $morder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.meal_consumption $morder $mamount ");
                })
                ->when($cashsort, function ($query) use ($cashsort) {
                    return $query->orderby('pos_sales.cash_collection', $cashsort);
                })
                ->when($opensort, function ($query) use ($opensort) {
                    return $query->orderby('pos_sales.opening_amount', $opensort);
                })
                ->when($totsort, function ($query) use ($totsort) {
                    return $query->orderby('pos_sales.total_sale', $totsort);
                })
                ->when($creditsort, function ($query) use ($creditsort) {
                    return $query->orderby('pos_sales.credit_sale', $creditsort);
                })
                ->when($banksort, function ($query) use ($banksort) {
                    return $query->orderby('pos_sales.bank_sale', $banksort);
                })
                ->when($diffsort, function ($query) use ($diffsort) {
                    return $query->orderby('pos_sales.difference', $diffsort);
                })
                ->when($mealsort, function ($query) use ($mealsort) {
                    return $query->orderby('pos_sales.meal_consumption', $mealsort);
                })
                ->when($editedsort, function ($query) use ($editedsort) {
                    return $query->orderby('employee_details2.first_name', $editedsort);
                })
                ->when($sort, function ($query) use ($sort) {
                    return $query->orderby('employee_details.first_name', $sort);
                })
                ->when($cashiersort, function ($query) use ($cashiersort) {
                    return $query->orderby('employee_details1.first_name', $cashiersort);
                })
                ->get();
                
        $t_pos_sales = DB::table('pos_sales')
                ->selectraw('SUM(opening_amount) as t_opening_amount,'
                        . 'SUM(total_sale) as t_total_sale,'
                        . 'SUM(cash_collection) as t_cash_collection,'
                        . 'SUM(credit_sale) as t_credit_sale,'
                        . 'SUM(bank_sale) as t_bank_sale,'
                        . '(SUM(cash_sale) - SUM(cash_collection)) as t_cashdifference,'
                        . '(SUM(bank_sale) - SUM(bank_collection)) as t_bankdifference, '
                        . 'SUM(difference) as t_difference,SUM(meal_consumption) as t_meal_consumption')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                ->whereRaw("pos_sales.added_by_user_type='Supervisor'")
                ->whereRaw('pos_sales.status=1')
                ->when($searchkey, function ($query) use ($searchkey) {
                    return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                })
                ->when($sup_searchkey, function ($query) use ($sup_searchkey) {
                    return $query->whereRaw("(employee_details1.first_name like '$sup_searchkey%' or concat(employee_details1.alias_name) like '$sup_searchkey%')");
                })
                ->when($edit_searchkey, function ($query) use ($edit_searchkey) {
                    return $query->whereRaw("(employee_details2.first_name like '$edit_searchkey%' or concat(employee_details2.alias_name) like '$edit_searchkey%')");
                })
                ->when($branch, function ($query) use ($branch) {
                    return $query->whereRaw("branch_details.id=$branch");
                })
                ->when($shift, function ($query) use ($shift) {
                    return $query->whereRaw("jobshift_details.id=$shift");
                })
                ->when($created, function ($query) use ($created, $ended) {
                    return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                })
                ->when($camount, function ($query) use ($camount, $corder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.cash_collection $corder $camount ");
                })
                ->when($oamount, function ($query) use ($oamount, $oorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.opening_amount $oorder $oamount ");
                })
                ->when($tamount, function ($query) use ($tamount, $torder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.total_sale $torder $tamount ");
                })
                ->when($cramount, function ($query) use ($cramount, $crorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.opening_amount $crorder $cramount ");
                })
                ->when($bamount, function ($query) use ($bamount, $border) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.bank_sale $border $bamount ");
                })
                ->when($damount, function ($query) use ($damount, $dorder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.difference $dorder $damount ");
                })
                ->when($mamount, function ($query) use ($mamount, $morder) {
                    // echo "pos_sales.cash_collection $cashorder $cashamount ";
                    return $query->whereRaw("pos_sales.meal_consumption $morder $mamount ");
                })
                ->when($cashsort, function ($query) use ($cashsort) {
                    return $query->orderby('pos_sales.cash_collection', $cashsort);
                })
                ->when($opensort, function ($query) use ($opensort) {
                    return $query->orderby('pos_sales.opening_amount', $opensort);
                })
                ->when($totsort, function ($query) use ($totsort) {
                    return $query->orderby('pos_sales.total_sale', $totsort);
                })
                ->when($creditsort, function ($query) use ($creditsort) {
                    return $query->orderby('pos_sales.credit_sale', $creditsort);
                })
                ->when($banksort, function ($query) use ($banksort) {
                    return $query->orderby('pos_sales.bank_sale', $banksort);
                })
                ->when($diffsort, function ($query) use ($diffsort) {
                    return $query->orderby('pos_sales.difference', $diffsort);
                })
                ->when($mealsort, function ($query) use ($mealsort) {
                    return $query->orderby('pos_sales.meal_consumption', $mealsort);
                })
                ->when($editedsort, function ($query) use ($editedsort) {
                    return $query->orderby('employee_details2.first_name', $editedsort);
                })
                ->when($sort, function ($query) use ($sort) {
                    return $query->orderby('employee_details.first_name', $sort);
                })
                ->when($cashiersort, function ($query) use ($cashiersort) {
                    return $query->orderby('employee_details1.first_name', $cashiersort);
                })
                ->first();


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
	<body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
		<section id="container">
                <div style="text-align:center; font-size: 14px;"><h1>POS Supervisor Sales Report</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -31px;" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Sl no</td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Date</td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Branch Name </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px; "> Supervisor Name </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;">  Cashier</td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Opening Amount </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Cash Collection </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Total Sale </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Credit Sale </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Bank Sale </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Difference </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Meals </td>
						<td style="padding:10px 5px;color:#fff; font-size: 12px;"> Reason </td>
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
                                                <td class="filterFields"></td>
						<td></td>
					</tr>
				</thead>
				<tbody class="pos" id="pos" >';
        $i = '1';
        foreach ($pos_sales as $pos_sale) {
            $pos_sale_date = date("d-m-Y", strtotime($pos_sale->pos_date));
            $html_table .= '<tr>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:40px;">' . $i++ . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:40px;">' . $pos_sale_date . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . $pos_sale->branch_code . "-" . $pos_sale->branch_name . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . $pos_sale->employee_fname . " " . $pos_sale->employee_aname . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px;width:40px;">' . $pos_sale->cashier_fname . ' ' . $pos_sale->cashier_aname . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . ($pos_sale->opening_amount ? round($pos_sale->opening_amount,2) : '0.00') . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . ($pos_sale->cash_collection ? round($pos_sale->cash_collection,2) : '0.00') . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . ($pos_sale->total_sale ? round($pos_sale->total_sale,2) : '0.00') . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . ($pos_sale->credit_sale ? round($pos_sale->credit_sale,2): '0.00') . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . ($pos_sale->bank_sale ? round($pos_sale->bank_sale,2) : '0.00') . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:30px;">' . ($pos_sale->difference ? round($pos_sale->difference,2) : '0.00') . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">' . ($pos_sale->meal_consumption ? round($pos_sale->meal_consumption,2) : '0.00') . '</td>
						<td style="color: #535352; font-size: 12px;padding: 10px 5px; width:30px;">' . $pos_sale->reason . '</td>
					</tr>';
        }
                $html_table .= '<tr>
                                    <td colspan="4"></td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">Total</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">'. ($t_pos_sales->t_opening_amount ? $t_pos_sales->t_opening_amount : '0.00') .'</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">'. ($t_pos_sales->t_cash_collection ? $t_pos_sales->t_cash_collection : '0.00') .'</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">'. ($t_pos_sales->t_total_sale ? $t_pos_sales->t_total_sale : '0.00') .'</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">'. ($t_pos_sales->t_credit_sale ? $t_pos_sales->t_credit_sale : '0.00') .'</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">'. ($t_pos_sales->t_bank_sale ? $t_pos_sales->t_bank_sale : '0.00') .'</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:30px;">'. ($t_pos_sales->t_difference ? $t_pos_sales->t_difference : '0.00') .'</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px; width:40px;">'. ($t_pos_sales->t_meal_consumption ? $t_pos_sales->t_meal_consumption : '0.00') .'</td>
                                    <td></td>
                                </tr>
                            </tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_superisor_sales_pdf_report.pdf');
    }

    public function exportviewtopdf() {

        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 3000);
            $id = Input::get("view_details");
            $type = Input::get("excelorpdf");
            $ids = \Crypt::decrypt($id);
            $paginate = Input::get('pagelimit');

            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $sale_details_data = DB::table('pos_sales')
                    ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname', 'employee_details1.first_name as cashier_fname', 'employee_details1.alias_name as cashier_aname', 'employee_details2.first_name as editedby_name', 'employee_details2.alias_name as editedby_aname', DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason', 'branch_details.branch_code as branch_code'))
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                    ->leftjoin('employees as employee_details1', 'employee_details1.id', '=', 'pos_sales.cashier_id')
                    ->leftjoin('employees as employee_details2', 'employee_details2.id', '=', 'pos_sales.edited_by')
                    ->leftjoin('master_resources as reason', 'pos_sales.reason_id', '=', 'reason.id')
                    ->where('pos_sales.added_by_user_type', '=', 'Supervisor')
                    ->where('pos_sales.id', '=', $ids)
                    ->first();
            if (isset($sale_details_data)) {
                if ($type == 'PDF') {
                    $html_table = '<!DOCTYPE html>
                                        <html>
                                            <head>
                                                <title>Branch sale report</title>
                                                <meta charset="UTF-8">
                                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                            </head>
                                            <body>
                                                <div class="innerContent" style="background: #fff; padding: 32px; position: relative; font-family: "open_sansregular";
                                                     font-size: 12px;">
                                                    <header class="pageTitle" style="padding-top: 0px; margin-bottom: 28px; color: #554d4f; padding: 10px 0 10px; padding-top: 10px; border-bottom: 1px solid #e4dee0;">
                                                        <h1 style="font-family: "open_sanssemibold"; padding: 0 0 10px 0; line-height: 32px;">Branch Sale Report <span></span></h1>
                                                    </header>	
                                                    <div class="reportV1" style="border: 4px solid #e7e7e7;">
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-1 alignCenter" style="font-size: 15px; display: table-cell; padding: 12px; width: 20.33%;">
                                                                <b style="display: block; padding-bottom: 6px;">Date</b>
                                                                ' . (date("d-m-Y", strtotime($sale_details_data->pos_date))) . '</li>

                                                            <li class="custCol-3 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 16.66%;">
                                                                <b style="display: block; padding-bottom: 6px;">Shift</b>
                                                                ' . ($sale_details_data->jobshift_name ? $sale_details_data->jobshift_name : '') . '
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Branch</b>
                                                                ' . ($sale_details_data->branch_name ? $sale_details_data->branch_name : '') . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Opening Amount</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->opening_amount) ? $sale_details_data->opening_amount : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Sale</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? $sale_details_data->total_sale : 0))) . '
                                                            </li>

                                                            
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;" >
                                                            
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Cash Collection</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_collection) ? $sale_details_data->cash_collection : 0))) . '</span>
                                                            </li>
                                                            
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Credit Sale</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->credit_sale) ? $sale_details_data->credit_sale : 0))) . '</span>
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Bank Sale</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? $sale_details_data->bank_sale : 0))) . '</span>
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Difference</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->difference) ? $sale_details_data->difference : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Meal Consumption</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->meal_consumption) ? $sale_details_data->meal_consumption : 0))) . '</span>
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-4" style="font-size: 15px;  display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Supervisor Name</b>
                                                                ' . ($sale_details_data->employee_fname ? $sale_details_data->employee_fname : "") . ($sale_details_data->employee_aname ? $sale_details_data->employee_aname : "") . '
                                                            </li>
                                                            <li class="custCol-4" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Cashier Name</b>
                                                                ' . ($sale_details_data->cashier_fname ? $sale_details_data->cashier_fname : "") . ($sale_details_data->cashier_aname ? $sale_details_data->cashier_aname : "") . '
                                                            </li>
                                                            <li class="custCol-4 " style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Edited By</b>
                                                                ' . ($sale_details_data->editedby_name ? $sale_details_data->editedby_name : "") . ($sale_details_data->editedby_aname ? $sale_details_data->editedby_aname : "") . '
                                                            </li>
                                                        </ul>

                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">

                                                            <li class="custCol-8 " style="font-size: 15px;  display: table-cell; padding: 12px;">
                                                                <b style="display: block; padding-bottom: 6px;">Reason</b>
                                                                ' . ($sale_details_data->reason ? $sale_details_data->reason : "") . '
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </body>
                                        </html>';
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($html_table);
                    return $pdf->download('branch_sales_report.pdf');
                } else if ($type == 'Excel') {
                    Excel::create('BranchSale', function($excel) use($sale_details_data) {
                        // Set the title
                        $excel->setTitle('Branch Sale');

                        $excel->sheet('Branch Sale', function($sheet) use($sale_details_data) {
                            // Sheet manipulation

                            $sheet->setCellValue('E3', 'Branch Sale');
                            $sheet->setHeight(3, 20);
                            $sheet->cells('A3:N3', function($cells) {
                                $cells->setBackground('#00CED1');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(14);
                            });

                            $chrRow = 6;

                            $sheet->row(5, array('Date', 'Shift', 'Branch', 'Opening Amount', "Total Sale", 'Cash Collection', "Credit Sale", "Bank Sale", "Difference", "Meal Consumption" ,"Supervisor Name", "Cashier Name", "Edited By", "Reason"));
                            $sheet->setHeight(5, 15);
                            $sheet->cells('A5:N5', function($cells) {
                                $cells->setBackground('#6495ED');
                                $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $employee_name = "";
                            $sheet->setCellValue('A' . $chrRow, (date("d-m-Y", strtotime($sale_details_data->pos_date))));
                            $sheet->setCellValue('B' . $chrRow, $sale_details_data->jobshift_name);
                            $sheet->setCellValue('C' . $chrRow, $sale_details_data->branch_name);
                            $sheet->setCellValue('D' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->opening_amount) ? $sale_details_data->opening_amount : 0)));
                            $sheet->setCellValue('E' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? $sale_details_data->total_sale : 0)));
                            $sheet->setCellValue('F' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->cash_collection) ? $sale_details_data->cash_collection : 0)));
                            $sheet->setCellValue('G' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->credit_sale) ? $sale_details_data->credit_sale : 0)));
                            $sheet->setCellValue('H' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? $sale_details_data->bank_sale : 0)));
                            $sheet->setCellValue('I' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->difference) ? $sale_details_data->difference : 0)));
                            $sheet->setCellValue('J' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->meal_consumption) ? $sale_details_data->meal_consumption : 0)));
                            $sheet->setCellValue('K' . $chrRow, ($sale_details_data->employee_fname . $sale_details_data->employee_aname));
                            $sheet->setCellValue('L' . $chrRow, ($sale_details_data->cashier_fname . $sale_details_data->cashier_aname));
                            $sheet->setCellValue('M' . $chrRow, ($sale_details_data->editedby_name . $sale_details_data->editedby_aname));
                            $sheet->setCellValue('N' . $chrRow, ($sale_details_data->reason));

                            $sheet->cells('A' . $chrRow . ':N' . $chrRow, function($cells) {
                                $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $chrRow++;
                        });
                    })->export('xls');
                } else if ($type == 'PRINT') {
                    $html_table = '<!DOCTYPE html>
                                        <html>
                                            <head>
                                                <title>Branch sale report</title>
                                                <meta charset="UTF-8">
                                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                            </head>
                                            <body>
                                                <div class="innerContent" style="background: #fff; padding: 32px; position: relative; font-family: "open_sansregular";
                                                     font-size: 12px;">
                                                    <header class="pageTitle" style="padding-top: 0px; margin-bottom: 28px; color: #554d4f; padding: 10px 0 10px; padding-top: 10px; border-bottom: 1px solid #e4dee0;">
                                                        <h1 style="font-family: "open_sanssemibold"; padding: 0 0 10px 0; line-height: 32px;">Branch Sale Report <span></span></h1>
                                                    </header>	
                                                    <div class="reportV1" style="border: 4px solid #e7e7e7;">
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-1 alignCenter" style="font-size: 15px; display: table-cell; padding: 12px; width: 20.33%;">
                                                                <b style="display: block; padding-bottom: 6px;">Date</b>
                                                                ' . (date("d-m-Y", strtotime($sale_details_data->pos_date))) . '</li>

                                                            <li class="custCol-3 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 16.66%;">
                                                                <b style="display: block; padding-bottom: 6px;">Shift</b>
                                                                ' . ($sale_details_data->jobshift_name ? $sale_details_data->jobshift_name : '') . '
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Branch</b>
                                                                ' . ($sale_details_data->branch_name ? $sale_details_data->branch_name : '') . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Opening Amount</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->opening_amount) ? $sale_details_data->opening_amount : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Sale</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? $sale_details_data->total_sale : 0))) . '
                                                            </li>

                                                            
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;" >
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Cash Collection</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_collection) ? $sale_details_data->cash_collection : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Credit Sale</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->credit_sale) ? $sale_details_data->credit_sale : 0))) . '</span>
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Bank Sale</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? $sale_details_data->bank_sale : 0))) . '</span>
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Difference</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->difference) ? $sale_details_data->difference : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Meal Consumption</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->meal_consumption) ? $sale_details_data->meal_consumption : 0))) . '</span>
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-4" style="font-size: 15px;  display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Supervisor Name</b>
                                                                ' . ($sale_details_data->employee_fname ? $sale_details_data->employee_fname : "") . ($sale_details_data->employee_aname ? $sale_details_data->employee_aname : "") . '
                                                            </li>
                                                            <li class="custCol-4" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Cashier Name</b>
                                                                ' . ($sale_details_data->cashier_fname ? $sale_details_data->cashier_fname : "") . ($sale_details_data->cashier_aname ? $sale_details_data->cashier_aname : "") . '
                                                            </li>
                                                            <li class="custCol-4 " style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Edited By</b>
                                                                ' . ($sale_details_data->editedby_name ? $sale_details_data->editedby_name : "") . ($sale_details_data->editedby_aname ? $sale_details_data->editedby_aname : "") . '
                                                            </li>
                                                        </ul>

                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">

                                                            <li class="custCol-8 " style="font-size: 15px;  display: table-cell; padding: 12px;">
                                                                <b style="display: block; padding-bottom: 6px;">Reason</b>
                                                                ' . ($sale_details_data->reason ? $sale_details_data->reason : "") . '
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </body>
                                        </html>';
                    $ret = array("data" => $html_table, "has_data" => '1');
                    echo json_encode($ret);
                }
            } else {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            }
        } Catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return redirect()->back();
        }
    }

}
