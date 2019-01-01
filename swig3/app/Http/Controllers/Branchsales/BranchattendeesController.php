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
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Branch_attendees;
use App\Models\Employee;
use App\Models\Pos_sale;
use DB;

class BranchattendeesController extends Controller {

    public function index() {
        $loggin_id = Session::get('login_id');
        $paginate = Config::get('app.PAGINATE');
        
        $login_type = DB::table('employees')
                    ->select('employees.id', DB::raw('upper(master_resources.name) as name'))
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where(['employees.id' => $loggin_id])
                    ->first();
          
        $branch_attendees = DB::table('branch_attendees')
                        ->select('branch_attendees.*', 'branch_details.name as branch_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname')
                        ->join('employees as employee_details', 'employee_details.id', '=', 'branch_attendees.employee_id')
                        ->join('master_resources as branch_details', 'branch_details.id', '=', 'branch_attendees.branch_id')
                        ->orderby('branch_attendees.created_at', 'DESC')->paginate($paginate);

        return view('branchsales/branch_attendees/index', array('branch_attendees' => $branch_attendees,'login_type' => $login_type->name));
    }

    public function add() {
        try {

            $loggin_id = Session::get('login_id');

            $cashier_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                    ->where(['employees.id' => $loggin_id,
                        'master_resources.name' => 'Cashier',
                        'master_resources.resource_type' => 'JOB_POSITION'])
                    ->first();

            $cashier_allocation_det = DB::table('resource_allocation')
                    ->select('resource_allocation.*', 'branch.name as branch_name', 'shift.name as shift_name', 'shift.start_time as start_time', 'shift.end_time as end_time')
                    ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                    ->where(['resource_allocation.employee_id' => $loggin_id, 'resource_allocation.active' => 1])
                    ->first();

            $employees = DB::table('resource_allocation')
                    ->select('resource_allocation.id', 'shift.start_time as start_time', 'shift.end_time as end_time', 'employees.id as emp_id', 'employees.username as emp_code')
                    ->leftjoin('employees', 'resource_allocation.employee_id', '=', 'employees.id')
                    ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                    ->where(['resource_allocation.branch_id' => $cashier_allocation_det->branch_id,
                        'resource_allocation.shift_id' => $cashier_allocation_det->shift_id,
                        'resource_allocation.resource_type' => 'BARISTA',
                        'resource_allocation.active' => 1])
                    ->get();

            return view('branchsales/branch_attendees/add', array('cashier_details' => $cashier_details, 'cashier_allocation_det' => $cashier_allocation_det, 'employees' => $employees));
        } catch (\Exception $e) {
            Toastr::error('No branches allocated!', $title = null, $options = []);
            return Redirect::to('branchsales/branch_attendees');
        }
    }

    public function getcashier() {


        $loggin_id = Session::get('login_id');

        $cashier_details = DB::table('employees')
                ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where(['employees.id' => $loggin_id,
                    'master_resources.name' => 'Cashier',
                    'master_resources.resource_type' => 'JOB_POSITION'])
                ->first();

        $cashier_allocation_det = DB::table('resource_allocation')
                ->select('resource_allocation.*', 'branch.name as branch_name', 'shift.name as shift_name', 'shift.start_time as start_time', 'shift.end_time as end_time')
                ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                ->where(['resource_allocation.employee_id' => $loggin_id, 'resource_allocation.active' => 1])
                ->first();

        $public_path = url('/');

        echo '<div class="listContainerV1">
                    <div class="empList">
                    <input type="hidden" name="cashier_id" value=' . $cashier_details->id . ' >
                        <figure class="imgHolder">
                            <img src=' . $cashier_details->profilepic . ' alt="">
                        </figure>
                        <div class="details">
                            <b>' . $cashier_details->first_name . " " . $cashier_details->alias_name . '</b>
                            <p>Designation : <span>Cashier</span></p>
                            <figure class="flagHolder">
                                <img src="' . $public_path . '/images/flags/' . $cashier_details->flag_name . '" alt="Flag">
                                <figcaption>' . $cashier_details->country_name . '</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>

                </div>';
        if (count($cashier_allocation_det) > 0) {
            echo '<input type="hidden" name="shift_start" id="shift_start" value="' . $cashier_allocation_det->start_time . '" class="shift_start">
                <input type="hidden" name="shift_end" id="shift_end" value="' . $cashier_allocation_det->end_time . '" class="shift_end">
                <input type="hidden" name="shift_min" id="shift_min"  class="shift_min">
                <input type="hidden" name="shift_br" id="shift_br"  class="shift_br">
                <div class="custRow">
                    <div class="custCol-4">
                        <input type="hidden" name="branch_id" value=' . $cashier_allocation_det->branch_id . '>
                        <div class="inputHolder bgSelect">
                            <label>Branch Name</label>
                            <b>' . $cashier_allocation_det->branch_name . '</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <input type="hidden" name="job_shift" value=' . $cashier_allocation_det->shift_id . '>
                        <div class="inputHolder bgSelect">
                            <label>Shift Name</label>
                            <b>' . $cashier_allocation_det->shift_name . '</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder bgSelect">
                            <label>Date</label>
                            <b>' . date("d-m-Y") . '</b>
                            <span class="commonError"></span>
                        </div>
                    </div>

                </div>';
        }
    }

    public function getbarista() {


        $employee_id = Input::get('employee_id');

        $barista_details = DB::table('employees')
                ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where(['employees.id' => $employee_id])
                ->first();

        $barista_allocation_det = DB::table('resource_allocation')
                ->select('resource_allocation.*', 'branch.name as branch_name', 'shift.name as shift_name', 'shift.start_time as start_time', 'shift.end_time as end_time')
                ->leftjoin('master_resources as branch', 'resource_allocation.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as shift', 'resource_allocation.shift_id', '=', 'shift.id')
                ->where(['resource_allocation.employee_id' => $employee_id, 'resource_allocation.active' => 1])
                ->first();
        $public_path = url('/');

        echo '<div class="listContainerV1">
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src=' . $barista_details->profilepic . ' alt="">
                        </figure>
                        <div class="details">
                            <b>' . $barista_details->first_name . " " . $barista_details->alias_name . '</b>
                            <p>Designation : <span>Barista</span></p>
                            <figure class="flagHolder">
                                <img src="' . $public_path . '/images/flags/' . $barista_details->flag_name . '" alt="Flag">
                                <figcaption>' . $barista_details->country_name . '</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>

                </div>';
        if (count($barista_allocation_det) > 0) {
            echo '<input type="hidden" name="shift_start" id="shift_start" value="' . $barista_allocation_det->start_time . '" class="shift_start">
                <input type="hidden" name="shift_end" id="shift_end" value="' . $barista_allocation_det->end_time . '" class="shift_end">
                <input type="hidden" name="shift_min" id="shift_min"  class="shift_min">
                <input type="hidden" name="shift_br" id="shift_br"  class="shift_br">
                <div class="custRow">
                    <div class="custCol-4">
                        <input type="hidden" name="branch_id" value=' . $barista_allocation_det->branch_id . '>
                        <div class="inputHolder bgSelect">
                            <label>Branch Name</label>
                            <b>' . $barista_allocation_det->branch_name . '</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <input type="hidden" name="job_shift" value=' . $barista_allocation_det->shift_id . '>
                        <div class="inputHolder bgSelect">
                            <label>Shift Name</label>
                            <b>' . $barista_allocation_det->shift_name . '</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder bgSelect">
                            <label>Date</label>
                            <b>' . date("d-m-Y") . '</b>
                            <span class="commonError"></span>
                        </div>
                    </div>

                </div>';
        }
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Input::get('branchhead') == "cashier") {
                $empid = Input::get('cashier_id');
            } else {
                $empid = Input::get('employee_id');
            }

            $branchattendeesmodel = new Branch_attendees;
            $branchattendeesmodel->company_id = $company_id;
            $branchattendeesmodel->branch_id = Input::get('branch_id');
            $branchattendeesmodel->job_shift_id = Input::get('job_shift');
            $branchattendeesmodel->employee_id = $empid;
            $branchattendeesmodel->time_in = Input::get('tin');
            $branchattendeesmodel->time_out = Input::get('tout');
            $branchattendeesmodel->over_time = Input::get('ot');
            $branchattendeesmodel->total_time = Input::get('tt');
            $branchattendeesmodel->save();
            Toastr::success('Shift Successfully Added!', $title = null, $options = []);
            return Redirect::to('branchsales/branch_attendees');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('branchsales/branch_attendees/add');
        }
    }

    public function getcode() {
        $branchhead = Input::get('branchhead');

        $employees = DB::table('employees')
                ->select('employees.*')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('name', '=', $branchhead)->where('resource_type', '=', 'JOB_POSITION')
                ->get();
        echo '<label>Choose Code</label><select class="commoSelect" name="employee_id" id="employee_id">';
        echo '<option value="">Select Code</option>';
        foreach ($employees as $employee) {
            echo "<option value='$employee->id'>$employee->username</option>";
        }
    }

    public function getcashierdetails() {
        $employee_id = Input::get('employee_id');

        $employee_details = DB::table('employees')
                ->select('employees.*')
                ->where('id', '=', $employee_id)
                ->first();
        $pic_url = $employee_details->profilepic;
        $employee_name = $employee_details->first_name . " " . $employee_details->middle_name . " " . $employee_details->last_name;
        echo '<label>Name :</label><label>' . $employee_name . '</label>
              <figure style="background-image: url(' . $pic_url . ');"></figure>';
    }

    public function show($id) {
        $id = \Crypt::decrypt($id);

        $branch_attendees_data = DB::table('branch_attendees')
                ->select('branch_attendees.*', 'branch_details.name as branch_name', 'shift_details.name as shift_name', 'employees.*')
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'branch_attendees.branch_id')
                ->join('master_resources as shift_details', 'shift_details.id', '=', 'branch_attendees.job_shift_id')
                ->join('employees', 'employees.id', '=', 'branch_attendees.employee_id')
                ->where('branch_attendees.id', '=', $id)
                ->first();
        //print_r($branch_attendees_data);die('sd');
        return view('branchsales/branch_attendees/show', array('branch_attendees_data' => $branch_attendees_data));
    }

    public function shiftdetails() {
        $shiftid = Input::get('shiftid');

        $job_shifts = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_SHIFT'])->where('id', '=', $shiftid)->first();
        // print_r('<pre>');print_r($job_shifts);
        $start = $job_shifts->start_time;
        $end = $job_shifts->end_time;
        echo json_encode(array($start, $end));
    }

}
