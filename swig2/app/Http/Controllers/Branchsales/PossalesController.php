<?php

namespace App\Http\Controllers\Branchsales;

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
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Pos_sale;
use DB;
use Customhelper;

class PossalesController extends Controller {

    public function index() {

        $branches = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'BRANCH'])
                        ->where('status', '=', 1)->get();
        $job_shifts = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_SHIFT'])->where('status', '=', 1)->get();
        $pos_reasons = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'POS_REASON'])->where('status', '=', 1)->get();
        $employees = DB::table('employees')
                ->select('employees.*')->where('status', '=', 1)
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('name', '=', 'Supervisor')->where('resource_type', '=', 'JOB_POSITION')
                ->get();

        return view('branchsales/pos_sales/add', array('branches' => $branches, 'job_shifts' => $job_shifts, 'pos_reasons' => $pos_reasons, 'employees' => $employees));
    }

    public function add() {

        $login_id = Session::get('login_id');
//        Toastr::error('Site under maintenance', $title = null, $options = []);
//        return Redirect::to('branchsales');
        try {
            $branches = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'BRANCH'])
                            ->where('status', '=', 1)->get();
            $job_shifts = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'JOB_SHIFT'])->where('status', '=', 1)->get();
            $employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->join('country', 'country.id', '=', 'employees.nationality')
                    ->whereRaw("employees.id=$login_id")
                    ->first();
            if ($employee_details->admin_status != 1) {
                $loggedin_employee_details = DB::table('employees')
                        ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                        ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                        ->join('country', 'country.id', '=', 'employees.nationality')
                        ->whereRaw("employees.id=$login_id")
                        ->first();
            } else {
                $loggedin_employee_details = $employee_details;
            }
            $supervisor_branches = array();
            $branch_details = array();
            $supervisor_details = array();
            if ($loggedin_employee_details->name == 'Cashier') {
                $branch_details = DB::table('resource_allocation')
                        ->select('resource_allocation.*', 'branch_details.id as branch_id', 'branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund', 'branch_details.opening_fund_editable as opening_fund_editable', 'shift_details.name as shift_name', 'shift_details.id as job_shift_id', 'branch_details.branch_code as branch_code')
                        ->join('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                        ->join('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                        ->whereRaw("employee_id=$login_id and active=1")
                        ->first();
                $branch_id = $branch_details->branch_id;
                $supervisor_details = DB::table('resource_allocation')
                                ->select('resource_allocation.*', 'supervisor_details.id as supervisor_id', 'supervisor_details.first_name as supervisor_first_name', 'supervisor_details.alias_name as supervisor_alias_name', 'supervisor_details.profilepic as profilepic', 'country.name as country_name', 'country.flag_128 as flag_name')
                                ->join('employees as supervisor_details', 'supervisor_details.id', '=', 'resource_allocation.employee_id')
                                ->join('country', 'country.id', '=', 'supervisor_details.nationality')
                                ->whereRaw("branch_id= $branch_id and active = 1 and resource_type = 'SUPERVISOR'")->first();
            } else if ($loggedin_employee_details->name == 'Supervisor') {
                $supervisor_branches = DB::table('resource_allocation')
                        ->select('resource_allocation.*', 'branch_details.id as branch_id', 'branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund', 'branch_details.branch_code as branch_code')
                        ->join('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                        ->whereRaw("employee_id = $login_id and active=1 and branch_details.status=1")
                        ->get();
            }
            $pos_reasons = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'POS_REASON'])->where('status', '=', 1)->get();
            $employees = DB::table('employees')
                    ->select('employees.*')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')->where('employees.status', '=', 1)
                    ->get();
            //print_r($employees);
            $cashieremployees = DB::table('employees')
                    ->select('employees.*')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('master_resources.name', '=', 'Cashier')->where('master_resources.resource_type', '=', 'JOB_POSITION')->where('employees.status', '=', 1)
                    ->get();


            //return view('branchsales/pos_sales/add', array('branches' => $branches, 'job_shifts' => $job_shifts, 'pos_reasons' => $pos_reasons, 'employees' => $employees, 'cashieremployees' => $cashieremployees, 'loggedin_employee_details' => $loggedin_employee_details));
            return view('branchsales/pos_sales/add', array('branch_details' => $branch_details, 'pos_reasons' => $pos_reasons, 'employees' => $employees, 'cashieremployees' => $cashieremployees, 'loggedin_employee_details' => $loggedin_employee_details, 'supervisor_details' => $supervisor_details, 'supervisor_branches' => $supervisor_branches));
        } catch (\Exception $e) {
            Toastr::error('No branches allocated!', $title = null, $options = []);
            return Redirect::to('branchsales');
        }
    }

    public function store() {
        try {
            $current_date = date('Y-m-d');
            $posmodel = new Pos_sale;
            $posmodel->company_id = Session::get('company');
            $posmodel->branch_id = Input::get('branch_id');
            $posmodel->job_shift_id = Input::get('job_shift');

            $branch_id = Input::get('branch_id');
            $job_shift = Input::get('job_shift');
            $pos_date = Input::get('pos_date');

            //////////////////////////insertion by Supervisor///////////////////////////
            if (Input::get('added_by_user_type') == 'Supervisor') {
                $pos_date = explode('-', $pos_date);
                $year = $pos_date[2];
                $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];
                $pos_details = DB::table('pos_sales')
                        ->select('pos_sales.*')
                        ->whereRaw("branch_id=$branch_id and job_shift_id=$job_shift and pos_date like '$pos_date%' and added_by_user_type='Supervisor' and pos_sales.status=1")
                        ->first();
                
                if (count($pos_details) > 0) {
                    Toastr::error("Pos Sale already added this date", $title = null, $options = []);
                    return Redirect::to('branchsales/pos_sales/add');
                }
                
                $cash_sale=0;
                if(Input::get('cash_sale')!=''){
                    $cash_sale=Input::get('cash_sale');
                }
                
                $bank_sale=0;
                if(Input::get('bank_sale')!=''){
                    $bank_sale=Input::get('bank_sale');
                }
                
                $credit_sale=0;
                if(Input::get('credit_sale')!=''){
                    $credit_sale=Input::get('credit_sale');
                }
                
                $totalsale=Input::get('total_sale');
                $tot_branch_sale=Input::get('tot_branch_sale');
                if(($cash_sale+$bank_sale+$credit_sale)!=$totalsale){
                    Toastr::error("Total branch sales does not matches the amount you have entered,(Cash sales + Bank sales + Credit sales) should be equal to total sales!", $title = null, $options = []);
                    return Redirect::to('branchsales/pos_sales/add');
                }
                
                if($tot_branch_sale!=$totalsale){
                    Toastr::error("Total branch sales does not matches the amount you have entered,(Cash sales + Bank sales + Credit sales) should be equal to total sales!", $title = null, $options = []);
                    return Redirect::to('branchsales/pos_sales/add');
                }
                        
                $posmodel->cash_collection = Input::get('cash_collection');
                $posmodel->employee_id = Session::get('login_id');
                $posmodel->cashier_id = Input::get('cash_employee_id');
                $posmodel->credit_sale = Input::get('credit_sale');
                $posmodel->bank_sale = Input::get('bank_sale');
                $posmodel->difference = Input::get('diff');
                $posmodel->meal_consumption = Input::get('meals');

                $posmodel->cash_sale = Input::get('cash_sale');
                $posmodel->bank_collection = Input::get('bank_collection');
                $posmodel->total_sale = Input::get('total_sale');
                $tax_amount = Input::get('tax_amount');
                $posmodel->tax_in_mis = str_replace(',', '', $tax_amount);
                $tax_in_pos = Input::get('tax_in_pos');
                
                if ($tax_in_pos == "") {
                    $posmodel->tax_in_pos = 5;
                } else {
                    $posmodel->tax_in_pos = str_replace(',', '', $tax_in_pos);
                }

                if (Input::get('pos_reason') != 'other') {
                    $pos_reason = Input::get('pos_reason');
                    if ($pos_reason == '') {
                        $pos_reason = NULL;
                    }
                    $posmodel->reason_id = $pos_reason;
                }
                
                $posmodel->reason_details = Input::get('reason_details');
                $posmodel->created_at = date('Y-m-d H:i:s');
                $posmodel->pos_date = $pos_date;
                
            } else {
                $posmodel->employee_id = Input::get('employee_id');
            }
            
            
            //////////////////////////insertion by cashier///////////////////////////
            if (Input::get('added_by_user_type') == 'Cashier') {
                $posmodel->tips_collected = Input::get('tips_collected');
                $posmodel->employee_id = Input::get('employee_id');
                $posmodel->pos_date = date('Y-m-d H:i:s');
                $sales_amount = Input::get('sales_amount');
                $posmodel->bank_collection = Input::get('bank_card_collection');
                $posmodel->cash_collection = Input::get('cash_collection');

                $date_time = DB::table('master_resources')
                        ->select('start_time', 'end_time')
                        ->whereRaw("id=$job_shift and status=1")
                        ->first();
                
                if (count($date_time) > 0) {
                    $newPeriodArray = explode(' ', $date_time->start_time);
                    $newPeriod = trim($newPeriodArray[1]);
                    $current_submit_time = date('a');

                    if ($newPeriod == 'pm' && $current_submit_time == 'am') {

                        $posmodel->pos_date = date('Y-m-d H:i:s', strtotime($posmodel->pos_date . ' -1 day'));
                    }
                }


                $pos_details = DB::table('pos_sales')
                        ->select('pos_sales.*')
                        ->whereRaw("branch_id=$branch_id and job_shift_id=$job_shift and pos_date like '$current_date%' and added_by_user_type='Cashier' and pos_sales.status=1")
                        ->first();
                
                if (count($pos_details) > 0) {
                    Toastr::error("Pos Sale already added", $title = null, $options = []);
                    return Redirect::to('branchsales/pos_sales/add');
                }
                
                $posmodel->cashier_id = Session::get('login_id');
            } else {
                $posmodel->cashier_id = Input::get('cash_employee_id');
            }
            
            $posmodel->opening_amount = Input::get('open_amount');
            $posmodel->added_by_user_type = Input::get('added_by_user_type');

            if ($posmodel->cashier_id == "") {
                Toastr::error('No Cashier Exist!', $title = null, $options = []);
                return Redirect::to('branchsales/pos_sales/add');
            } else if ($posmodel->employee_id == "") {
                Toastr::error('No Supervisor Exist!', $title = null, $options = []);
                return Redirect::to('branchsales/pos_sales/add');
            } else {
                $posmodel->save();
                Toastr::success('Successfully Added!', $title = null, $options = []);
                return Redirect::to('branchsales/pos_sales/add');
            }
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('branchsales/pos_sales/add');
        }
    }

    public function getbranchname() {
        $branch_id = Input::get('branch_id');

        $branch_details = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('id', '=', $branch_id)->where('status', '=', 1)
                ->first();
        echo $branch_details->name . '</br>Opening Change Fund:' . $branch_details->opening_fund;
    }

    public function getsupervisordetails() {
        $employee_id = Input::get('employee_id');

        $employee_details = DB::table('employees')
                ->select('employees.*')
                ->where('id', '=', $employee_id)->where('status', '=', 1)
                ->first();
        $pic_url = $employee_details->profilepic;
        $employee_name = $employee_details->first_name . " " . $employee_details->middle_name . " " . $employee_details->last_name;
        echo '<label>' . $employee_name . '</label>
              <figure style="background-image: url(' . $pic_url . ');"></figure>';
    }

    public function getcashierdetails() {
        $employee_id = Input::get('employee_id');

        $employee_details = DB::table('employees')
                ->select('employees.*')
                ->where('id', '=', $employee_id)->where('status', '=', 1)
                ->first();
        $pic_url = $employee_details->profilepic;
        $employee_name = $employee_details->first_name . " " . $employee_details->middle_name . " " . $employee_details->last_name;
        echo '<label>' . $employee_name . '</label>
              <figure style="background-image: url(' . $pic_url . ');"></figure>';
    }

    public function getjobtimings() {
        $job_shift = Input::get('job_shift');

        $shift_details = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('id', '=', $job_shift)->where('status', '=', 1)
                ->first();
        echo $shift_details->start_time . ' To ' . $shift_details->end_time;
    }

    public function getopenamount() {
        $branch_id = Input::get('branch_id');
        $job_shift = Input::get('job_shift');
        $pos_date = Input::get('pos_date');
        $pos_date = explode('-', $pos_date);
        $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];
        $pos_details = DB::table('pos_sales')
                ->select('pos_sales.*')
                ->whereRaw("branch_id=$branch_id and job_shift_id=$job_shift and pos_date like '$pos_date%' and pos_sales.status=1")
                ->first();
        if (count($pos_details) > 0) {
            $branch_details = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('id', '=', $branch_id)->where('status', '=', 1)
                    ->first();
            $opening_fund_editable = $branch_details->opening_fund_editable;
            $opening_amount = $pos_details->opening_amount;
            $cash_collected = $pos_details->cash_collection;
            $tip_collected = $pos_details->tips_collected;
            $cashier_entry = true;
            // echo $branch_details->opening_fund_editable;
            // echo $result;


            return \Response::json(array('opening_fund_editable' => $opening_fund_editable, 'opening_amount' => $opening_amount, 'cash_collected' => $cash_collected, 'tip_collected' => $tip_collected, 'cashier_entry' => $cashier_entry));
        } else {
            $branch_details = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('id', '=', $branch_id)->where('status', '=', 1)
                    ->first();
            //   echo $branch_details->opening_fund;
            //  echo $branch_details->opening_fund_editable;

            $opening_fund_editable = $branch_details->opening_fund_editable;
            $opening_amount = $branch_details->opening_fund;
            $cash_collected = 0;
            $tip_collected = 0;
            $cashier_entry = false;
            //  echo $result;
            return \Response::json(array('opening_fund_editable' => $opening_fund_editable, 'opening_amount' => $opening_amount, 'cash_collected' => $cash_collected, 'tip_collected' => $tip_collected, 'cashier_entry' => $cashier_entry));
        }
    }

    public function branch_shifts() {
        $branch_id = Input::get('branch_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $shifts = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id', 'master_resources.shift_id')
                ->where(['resource_type' => 'BRANCH', 'id' => $branch_id, 'company_id' => $company_id, 'status' => 1])
                ->first();
        $job_shift_array = explode(',', $shifts->shift_id);
        $n = 0;
        echo '<option value="">Select Shift</option>';
        foreach ($job_shift_array as $job_shift_id) {
            $n++;
            $shift_details = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.status', '=', 1)
                    ->where('master_resources.id', '=', $job_shift_id)
                    ->first();
            echo '<option value="' . $shift_details->id . '">' . $shift_details->name . '</option>';
        }
    }

    public function shift_cashier() {
        $branch_id = Input::get('branch_id');
        $shift_id = Input::get('shift_id');
        $pos_date = Input::get('pos_date');


        $pos_date = explode('-', $pos_date);
        $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];


        $pos_details = DB::table('pos_sales')
                ->select('pos_sales.*')
                ->whereRaw("branch_id=$branch_id and job_shift_id=$shift_id and pos_date like '$pos_date%' and added_by_user_type='Cashier' and pos_sales.status=1")
                ->first();
        if (count($pos_details) > 0) {


            $cashier_id = $pos_details->cashier_id;

            $employee_details = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'employees.*', 'employees.id as cashier_id', 'country.name as country_name', 'country.flag_128 as flag_name')
                            ->join('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                            ->join('country', 'country.id', '=', 'employees.nationality')
                            ->where([ 'employees.id' => $cashier_id, 'resource_type' => 'CASHIER'])->first();
        } else {


            $employee_details = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'employees.*', 'employees.id as cashier_id', 'country.name as country_name', 'country.flag_128 as flag_name')
                            ->join('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                            ->join('country', 'country.id', '=', 'employees.nationality')
                            ->where(['resource_allocation.branch_id' => $branch_id, 'resource_allocation.shift_id' => $shift_id, 'active' => 1, 'resource_type' => 'CASHIER'])->first();
        }
        if (count($employee_details) < 1) {
            echo '<span class="noCashier">No Cashier Found</span>';
        } else {
            $pic_url = $employee_details->profilepic;
            $employee_name = $employee_details->first_name . " " . $employee_details->middle_name . " " . $employee_details->last_name;
            $country_name = $employee_details->country_name;
            $flag_name = $employee_details->flag_name;
            $cashier_id = $employee_details->cashier_id;
            /* echo '<label>' . $employee_name . '</label>
              <figure style="background-image: url(' . $pic_url . ');"></figure>'; */
            echo '<input type="hidden" value="' . $cashier_id . '" name="cash_employee_id" id="cash_employee_id"><figure class="imgHolder">
                            <img src="' . $pic_url . '" alt="Profile">
                        </figure>
                        <div class="details">
                            <b>' . $employee_name . '</b>
                            <p>Designation : <span>Cashier</span></p>
                            <figure class="flagHolder">
                                <img src="../../images/flags/' . $flag_name . '" alt="Flag">
                                <figcaption>' . $country_name . '</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>';
        }
    }

    public function gettaxamount() {
        $cash_collection = Input::get('cash_collection');
        $pos_date = Input::get('pos_date');
        $pos_date = explode('-', $pos_date);
        $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];



        $tax_details = DB::table('master_resources')
                        ->select('tax_percent', 'tax_applicable_from', 'name')
                        ->where('resource_type', '=', "TAX")->where('status', '=', 1)
                        ->orderBy('id', 'desc')->first();

        if (count($tax_details) > 0) {

            $taxpercent = $tax_details->tax_percent;
            $taxdate = $tax_details->tax_applicable_from;
            if (strtotime($pos_date) >= strtotime($taxdate)) {

                if ($taxpercent == "") {
                    $taxpercent = 5;
                }
                $cash_percent = ($cash_collection * $taxpercent) / (100 * 1.05);
                $sales_percent = $cash_collection - $cash_percent;


                // bfre changng with static vat 1.05
                //      $cash_percent=($cash_collection*$taxpercent)/100 ;
                //  $sales_percent=$cash_collection-$cash_percent;

                $sales_amount = number_format($sales_percent, 2);
                $tax_amount = number_format($cash_percent, 2);
                return \Response::json(array('sales_amount' => $sales_amount, 'tax_amount' => $tax_amount));
            } else {

                $sales_amount = number_format($cash_collection, 2);
                return \Response::json(array('sales_amount' => $sales_amount, 'tax_amount' => 0));
            }
        } else {

            return \Response::json(array('sales_amount' => 0, 'tax_amount' => 0));
        }
    }

    public function dateCompare() {

        $pos_date = Input::get('pos_date');
        $pos_date = explode('-', $pos_date);
        $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];



        $tax_details = DB::table('master_resources')
                        ->select('tax_percent', 'tax_applicable_from', 'name')
                        ->where('resource_type', '=', "TAX")->where('status', '=', 1)
                        ->orderBy('id', 'desc')->first();

        if (count($tax_details) > 0) {


            $taxdate = $tax_details->tax_applicable_from;
            if (strtotime($pos_date) >= strtotime($taxdate)) {

                $taxapplicable = true;
            } else {
                $taxapplicable = false;
            }

            // echo $branch_details->opening_fund_editable;
            // echo $result;


            return \Response::json(array('taxapplicable' => $taxapplicable));
        } else {

            return \Response::json(array('taxapplicable' => false));
        }
    }

}
