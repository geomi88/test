<?php

namespace App\Http\Controllers\Operation;

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
use App\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;
use Mail;
use App\Models\Resource_allocation;

class ResourceallocationController extends Controller {

    public function index() {
//        $shift_names = DB::table('pos_sales')
//                ->select('pos_sales.*')->select('jobshift_details.name as jobshift_name', 'jobshift_details.id as jobshift_id')->distinct()
//                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
//                ->where('jobshift_details.status', '=', 1)
//                ->get();
//      print_r('<pre>');
//        print_r($supervisors);

        $shift_names = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                ->where('master_resources.status', '=', 1)
                ->get();
        return view('operation/resource_allocation/index', array('shift_names' => $shift_names));
    }

    public function show_shifts() {
        $branch_id = Input::get('branch_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $shifts = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id', 'master_resources.shift_id')
                ->where(['resource_type' => 'BRANCH', 'id' => $branch_id, 'company_id' => $company_id, 'status' => 1])
                ->first();
        // print_r($shifts->shift_id);print_r('ss');
        $arrays = explode(',', $shifts->shift_id);
        $n = 0;
        echo '<label>Select Job Shift</label>
              <select id="shiftid">';
        echo '<option value="-1">Select Shift</option>';
        foreach ($arrays as $array) {
            $n++;
            $shift_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.status', '=', 1)
                    ->where('master_resources.id', '=', $array)
                    ->first();
            if(count($shift_names)!=0){
                echo '<option value="'.$array.'">'.$shift_names->name.'</option>';
            }
            //echo '<li class="shift_name" id="' . $shift_names->name . '" value="' . $array . '"><a href="javascript:void(0)" rel="#tabS' . $n . '"  >' . $shift_names->name . '</a></li>';
        
            
        }
    }

    public function showbranch() {
        $rtype = Input::get('rtype');
        $bar = ucfirst($rtype);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $resourcetypes = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => $rtype, 'company_id' => $company_id, 'status' => 1])->get();
        echo '<label>Choose ' . $bar . '</label><select class="resourcedrop" id=' . $rtype . '>';
        echo '<option value="-1">Select ' . $rtype . '</option>';
        foreach ($resourcetypes as $resourcetype) {
            echo "<option value='$resourcetype->id'>$resourcetype->name</option>";
        }
    }

    public function show_resource_details() {
        $rtype = Input::get('data');
        $bar = ucfirst($rtype);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $resourcetypes = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => $rtype, 'company_id' => $company_id, 'status' => 1])->get();
        echo '<label>Choose ' . $bar . '</label><select class="resourcedrop" id=' . $rtype . '>';
        foreach ($resourcetypes as $resourcetype) {
            echo "<option value='$resourcetype->id'>$resourcetype->name</option>";
        }
    }

    public function show_supervisors() {
        $rtype = Input::get('data');
        //$bar = ucfirst($rtype);
        $br = Input::get('branch_id');
        $employees = DB::table('employees')
                ->where('status', '=', 1)
                ->get();
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        // $company_id = Input::get('company_id');
        $supervisors = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->where('employees.status', '=', 1)
                //->where('resource_allocation.branch_id', '!=',$br)
                ->whereRaw(" employees.id NOT
                        IN (

                        SELECT employee_id
                        FROM resource_allocation
                        WHERE branch_id =$br and active=1 and 
                        ((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))
                        ) 
                        ")
                ->groupby(DB::raw("employees.id"))
                ->get();

//      print_r('<pre>');
//        print_r($supervisors);
        foreach ($supervisors as $supervisor) {
            $leaves = DB::table('leaves')
                            ->select('leaves.*')
                            ->where('leaves.employee_id', '=', $supervisor->id)->get();
            $branches = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.region_id')
                            ->where('resource_allocation.employee_id', '=', $supervisor->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_branches = count($branches);
            $no_of_leaves = count($leaves);
            
            if ($supervisor->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '<div class="sup_emp empList ' . $status . ' " id="' . $supervisor->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="supervisor_allocations_modal(' . $supervisor->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $supervisor->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $supervisor->first_name . ' ' . $supervisor->alias_name . '</b>
              <p>Designation : <span>Supervisor</span></p>';
            if ($supervisor->bames != NULL) {
                echo' <p>Branch : <span>' . $supervisor->bames . '</span></p>';
            }
            echo '</div>
              <div class="customClear"></div>
              <figure class="flagHolder">
                <img src="../images/flags/' . $supervisor->flag_pic . '" alt="Flag">
                 <figcaption>' . $supervisor->flag_code . '</figcaption>
                </figure>';
            if ($no_of_branches > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_branches . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($branches as $branch) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($branch->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($branch->to_date)) . '</span>
                            <b>' . $branch->branch_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
            if ($no_of_leaves > 0) {
                echo'<div class="allocationBtnHolder last">
                        <a href="javascript:void(0)" class="btns bgPink">' . $no_of_leaves . ' Leaves</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPink">Leaves <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($leaves as $leave) {
                    echo '<div class="list">
                        <span>' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '</span>
                         </div>';
                }
                echo'</div></div></div>';
            }
            echo'  <div class = "customClear"></div></div>';
        }
    }

    public function show_area_managers() {

        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        //$bar = ucfirst($rtype); 
        $br = Input::get('area_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $area_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, area_details.name, NULL )  ) AS bames , employees.* , resource_allocation.area_id , area_details.name AS area_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->where('employees.status', '=', 1)
                ->whereRaw(" employees.id NOT
                        IN (

                        SELECT employee_id
                        FROM resource_allocation
                        WHERE area_id =$br and active=1 and 
                        ((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))) 
                        ")
                ->groupby(DB::raw("employees.id"))
                ->get();

        foreach ($area_managers as $area_manager) {
            $leaves = DB::table('leaves')
                            ->select('leaves.*')
                            ->where('leaves.employee_id', '=', $area_manager->id)->get();
            $areas = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'area_details.name as area_name')
                            ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                            ->where('resource_allocation.employee_id', '=', $area_manager->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_areas = count($areas);
            $no_of_leaves = count($leaves);
            if ($area_manager->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="area_emp empList ' . $status . ' " id="' . $area_manager->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="area_manager_allocations_modal(' . $area_manager->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $area_manager->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $area_manager->first_name . ' ' . $area_manager->alias_name . '</b>
              <p>Designation : <span>Area Manager</span></p>';
            if ($area_manager->bames != NULL) {
                echo' <p>Area : <span>' . $area_manager->bames . '</span></p>';
            }
            echo '</div>
              <div class="customClear"></div>
              <figure class="flagHolder">
                <img src="../images/flags/' . $area_manager->flag_pic . '" alt="Flag">
                 <figcaption>' . $area_manager->flag_code . '</figcaption>
                </figure></figure>';
            if ($no_of_areas > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_areas . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($areas as $area) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($area->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($area->to_date)) . '</span>
                            <b>' . $area->area_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
            if ($no_of_leaves > 0) {
                echo'<div class="allocationBtnHolder last">
                        <a href="javascript:void(0)" class="btns bgPink">' . $no_of_leaves . ' Leaves</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPink">Leaves <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($leaves as $leave) {
                    echo '<div class="list">
                        <span>' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '</span>
                         </div>';
                }
                echo'</div></div></div>';
            }
            echo'  <div class = "customClear"></div></div>';
        }
    }

    public function show_regional_managers() {
        $rtype = Input::get('data');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        $br = Input::get('region_id');
        $employees = DB::table('employees')
                ->where('status', '=', 1)
                ->get();
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $regional_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, region_details.name, NULL )  ) AS bames , employees.* , resource_allocation.region_id , region_details.name AS region_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->where('employees.status', '=', 1)
                ->whereRaw(" employees.id NOT
                        IN (

                        SELECT employee_id
                        FROM resource_allocation
                        WHERE region_id =$br and active=1 and
                        ((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))    
                        ) 
                        ")
                ->groupby(DB::raw("employees.id"))
                ->get();

//      print_r('<pre>');
//        print_r($regional_managers);
        foreach ($regional_managers as $regional_manager) {
            $leaves = DB::table('leaves')
                            ->select('leaves.*')
                            ->where('leaves.employee_id', '=', $regional_manager->id)->get();
            $regions = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'region_details.name as region_name')
                            ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                            ->where('resource_allocation.employee_id', '=', $regional_manager->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_regions = count($regions);
            $no_of_leaves = count($leaves);
            if ($regional_manager->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="search_emp empList ' . $status . ' " id="' . $regional_manager->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="regional_manager_allocations_modal(' . $regional_manager->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $regional_manager->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $regional_manager->first_name . ' ' . $regional_manager->alias_name . '</b>
              <p>Designation : <span>Regional Manager</span></p>';
            if ($regional_manager->bames != NULL) {
                echo' <p>Region : <span>' . $regional_manager->bames . '</span></p>';
            }
            echo '</div>
              <div class="customClear"></div>
              <figure class="flagHolder">
                <img src="../images/flags/' . $regional_manager->flag_pic . '" alt="Flag">
                 <figcaption>' . $regional_manager->flag_code . '</figcaption>
                </figure>';
            if ($no_of_regions > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_regions . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($regions as $region) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($region->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($region->to_date)) . '</span>
                            <b>' . $region->region_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
            if ($no_of_leaves > 0) {
                echo'<div class="allocationBtnHolder last">
                        <a href="javascript:void(0)" class="btns bgPink">' . $no_of_leaves . ' Leaves</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPink">Leaves <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($leaves as $leave) {
                    echo '<div class="list">
                        <span>' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '</span>
                         </div>';
                }
                echo'</div></div></div>';
            }
            echo'  <div class = "customClear"></div></div>';
        }
    }

    public function show_allocated_supervisors() {
        $rtype = Input::get('data');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        if ($from_date != '') {
            $from_date = explode('-', $from_date);
            $from_date = $from_date[2] . '-' . $from_date[1] . '-' . $from_date[0];
        }
        if ($to_date != '') {
            $to_date = explode('-', $to_date);
            $to_date = $to_date[2] . '-' . $to_date[1] . '-' . $to_date[0];
        }
        //$bar = ucfirst($rtype); 
        $br = Input::get('branch_id');
        $employees = DB::table('employees')
                ->where('status', '=', 1)
                ->get();
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        //////////////////////allocated supervisors///////////////////
        $allocated_supervisorss = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name ,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code,resource_allocation.from_date,resource_allocation.to_date'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$from_date' AND '$to_date') OR (resource_allocation.to_date BETWEEN '$from_date' AND '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
//              print_r('<pre>');
//         print_r($allocated_supervisors);
        foreach ($allocated_supervisorss as $allocated_supervisors) {
            $leaves = DB::table('leaves')
                            ->select('leaves.*')
                            ->where('leaves.employee_id', '=', $allocated_supervisors->id)->get();
            $supervisor_branches = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.region_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_supervisors->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_branches = count($supervisor_branches);
            $no_of_leaves = count($leaves);
            
            $branches = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name '))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->where('employees.id', '=', $allocated_supervisors->id)
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            foreach ($branches as $branche) {

                $assigned_branches = $branche->bames;
            }
            if ($allocated_supervisors->branch_id != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="empList nodragorsort ' . $status . '" id="' . $allocated_supervisors->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="supervisor_allocations_modal(' . $allocated_supervisors->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $allocated_supervisors->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $allocated_supervisors->first_name . ' ' . $allocated_supervisors->alias_name . '</b>
              <p>Designation : <span>Supervisor</span></p>
              <p>Branch : <span>' . $assigned_branches . '</span></p>
              </div>
              <div class="customClear"></div>
              <figure class="flagHolder">
              <img src="../images/flags/' . $allocated_supervisors->flag_pic . '" alt="Flag">
                 <figcaption>' . $allocated_supervisors->flag_code . '</figcaption>
                </figure>';
            if ($no_of_branches > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_branches . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($supervisor_branches as $branch) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($branch->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($branch->to_date)) . '</span>
                            <b>' . $branch->branch_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
            if ($no_of_leaves > 0) {
                echo'<div class="allocationBtnHolder last">
                        <a href="javascript:void(0)" class="btns bgPink">' . $no_of_leaves . ' Leaves</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPink">Leaves <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($leaves as $leave) {
                    echo '<div class="list">
                        <span>' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '</span>
                         </div>';
                }
                echo'</div></div></div>';
            }
            echo'  <div class = "customClear"></div></div>';
        }
    }

    public function show_allocated_regional_managers() {
        //$bar = ucfirst($rtype); 
        $br = Input::get('region_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
//print_r($br);print_r($created);print_r($ended);
        //////////////////////allocated_region_managers///////////////////
        $allocated_region_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, region_details.name, NULL )  ) AS bames , employees.* , resource_allocation.region_id , region_details.name AS region_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code,resource_allocation.from_date,resource_allocation.to_date'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.region_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
//              print_r('<pre>');
//         print_r($allocated_region_managers);
        foreach ($allocated_region_managers as $allocated_region_manager) {
            $leaves = DB::table('leaves')
                            ->select('leaves.*')
                            ->where('leaves.employee_id', '=', $allocated_region_manager->id)->get();
            $regions = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'region_details.name as region_name')
                            ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_region_manager->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_regions = count($regions);
            $no_of_leaves = count($leaves);
            $branches = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, region_details.name, NULL )  ) AS bames , employees.* , resource_allocation.region_id , region_details.name AS region_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                    ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->where('employees.id', '=', $allocated_region_manager->id)
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            foreach ($branches as $branche) {
                $assigned_branches = $branche->bames;
            }
            if ($allocated_region_manager->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="empList nodragorsort ' . $status . '" id="' . $allocated_region_manager->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="regional_manager_allocations_modal(' . $allocated_region_manager->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $allocated_region_manager->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $allocated_region_manager->first_name . ' ' . $allocated_region_manager->alias_name . '</b>
              <p>Designation : <span>Regional Manager</span></p>
              <p>Region : <span>' . $assigned_branches . '</span></p>
              </div>
              <div class="customClear"></div>
              <figure class="flagHolder">
              <img src="../images/flags/' . $allocated_region_manager->flag_pic . '" alt="Flag">
                 <figcaption>' . $allocated_region_manager->flag_code . '</figcaption>
                </figure>';
            if ($no_of_regions > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_regions . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($regions as $region) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($region->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($region->to_date)) . '</span>
                            <b>' . $region->region_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
            if ($no_of_leaves > 0) {
                echo'<div class="allocationBtnHolder last">
                        <a href="javascript:void(0)" class="btns bgPink">' . $no_of_leaves . ' Leaves</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPink">Leaves <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($leaves as $leave) {
                    echo '<div class="list">
                        <span>' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '</span>
                         </div>';
                }
                echo'</div></div></div>';
            }
            echo'  <div class = "customClear"></div></div>';
        }
    }

    public function show_allocated_cashiers() {
        $rtype = Input::get('data');
        //$bar = ucfirst($rtype); 
        $br = Input::get('branch_id');
        $sh = Input::get('shift_id');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        if ($from_date != '') {
            $from_date = explode('-', $from_date);
            $from_date = $from_date[2] . '-' . $from_date[1] . '-' . $from_date[0];
        }
        if ($to_date != '') {
            $to_date = explode('-', $to_date);
            $to_date = $to_date[2] . '-' . $to_date[1] . '-' . $to_date[0];
        }
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        //////////////////////allocated supervisors///////////////////
        $allocated_cashiers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'CASHIER')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $br and resource_allocation.active = 1 and resource_allocation.shift_id = $sh and ((resource_allocation.from_date BETWEEN '$from_date' AND '$to_date') OR (resource_allocation.to_date BETWEEN '$from_date' AND '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();

        foreach ($allocated_cashiers as $allocated_cashier) {
            $branches = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames ,GROUP_CONCAT(if( resource_allocation.active =1, shift_details.name, NULL )  ) AS sames, employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                    ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                    ->where('master_resources.name', '=', 'CASHIER')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->where('employees.id', '=', $allocated_cashier->id)
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            foreach ($branches as $branche) {
                $assigned_branches = $branche->bames;
                $assigned_shifts = $branche->sames;
            }
            if ($allocated_cashier->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="empList nodragorsort ' . $status . '" id="' . $allocated_cashier->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="cashier_allocations_modal(' . $allocated_cashier->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $allocated_cashier->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $allocated_cashier->first_name . ' ' . $allocated_cashier->alias_name . '</b>
              <p>Designation : <span>Cashier</span></p>
              <p>Branch : <span>' . $assigned_branches . '</span></p>
              <p>shifts : <span>' . $assigned_shifts . '</span></p>
              </div>
              <div class="customClear"></div>
              <figure class="flagHolder nationality" id="' . $allocated_cashier->nationality . '">
              <img src="../images/flags/' . $allocated_cashier->flag_pic . '" alt="Flag">
                 <figcaption>' . $allocated_cashier->flag_code . '</figcaption>
              </figure></div>';
        }
    }

    public function show_allocated_baristas() {
        $rtype = Input::get('data');
        //$bar = ucfirst($rtype); 
        $br = Input::get('branch_id');
        $sh = Input::get('shift_id');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        if ($from_date != '') {
            $from_date = explode('-', $from_date);
            $from_date = $from_date[2] . '-' . $from_date[1] . '-' . $from_date[0];
        }
        if ($to_date != '') {
            $to_date = explode('-', $to_date);
            $to_date = $to_date[2] . '-' . $to_date[1] . '-' . $to_date[0];
        }
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        //////////////////////allocated supervisors///////////////////
        $allocated_baristas = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'BARISTA')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $br and resource_allocation.active = 1 and resource_allocation.shift_id = $sh and ((resource_allocation.from_date BETWEEN '$from_date' AND '$to_date') OR (resource_allocation.to_date BETWEEN '$from_date' AND '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
        foreach ($allocated_baristas as $allocated_barista) {
            if ($allocated_barista->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            $branches = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , GROUP_CONCAT(if( resource_allocation.active =1, shift_details.name, NULL )  ) AS sames,employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                    ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                    ->where('master_resources.name', '=', 'BARISTA')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->where('employees.id', '=', $allocated_barista->id)
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            foreach ($branches as $branche) {
                $assigned_branches = $branche->bames;
                $assigned_shifts = $branche->sames;
            }

            echo '  <div class="empList nodragorsort ' . $status . '" id="' . $allocated_barista->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="barista_allocations_modal(' . $allocated_barista->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $allocated_barista->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $allocated_barista->first_name . ' ' . $allocated_barista->alias_name . '</b>
              <p>Designation : <span>Barista</span></p>
              <p>Branch : <span>' . $assigned_branches . '</span></p>
              <p>Shift : <span>' . $assigned_shifts . '</span></p>
              </div>
              <div class="customClear"></div>
              <figure class="flagHolder nationality" id="' . $allocated_barista->nationality . '">
              <img src="../images/flags/' . $allocated_barista->flag_pic . '" alt="Flag">
                 <figcaption>' . $allocated_barista->flag_code . '</figcaption>
                </figure></div>';
        }
    }

    public function save_supervisors() {
        try {
            $br = Input::get('branch_id');
            $emp = Input::get('employee_id');
            $dt = new \DateTime();
            /*$supervisors = DB::table('resource_allocation')
                    ->where('branch_id', '=', $br)->where('resource_type', '=', 'SUPERVISOR')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            */
            
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
            }
            
            $allocated_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*')
                    ->whereRaw("resource_allocation.resource_type = 'SUPERVISOR' and resource_allocation.branch_id = $br and resource_allocation.active = 1 and ((resource_allocation.from_date BETWEEN '$from_date' and '$to_date' ) or (resource_allocation.to_date BETWEEN '$from_date' and '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->first();
            
            if(count($allocated_details) > 0)
            {
                return -1;
            }
            $resourcemodel = new Resource_allocation;
            $resourcemodel->resource_type = "SUPERVISOR";
            $resourcemodel->branch_id = Input::get('branch_id');
            $resourcemodel->employee_id = Input::get('employee_id');
            $resourcemodel->from_date = $from_date;
            $resourcemodel->to_date = $to_date;
            $resourcemodel->active = 1;
            $resourcemodel->mail_status = 0;
            $resourcemodel->save();
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'BRANCH')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            echo $employees->first_name . ' Has Been Allocated To ' . $branch_names->name;
        } catch (\Exception $e) {
            echo 'Sorry There Is Some Problem';
        }
    }

    public function save_regional_managers() {

        $dt = new \DateTime();
        $br = Input::get('region_id');
        $emp = Input::get('employee_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        $check_region_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, region_details.name, NULL )  ) AS bames , employees.* , resource_allocation.region_id , region_details.name AS region_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.region_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended'))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
        if (count($check_region_managers) > 0) {
            return -1;
        } else {
            $resourcemodel = new Resource_allocation;
            $resourcemodel->resource_type = "REGIONAL_MANAGER";
            $resourcemodel->region_id = Input::get('region_id');
            $resourcemodel->employee_id = Input::get('employee_id');
            $resourcemodel->from_date = $created;
            $resourcemodel->to_date = $ended;
            $resourcemodel->active = 1;
            $resourcemodel->save();
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $area_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'REGION')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            echo $employees->first_name . ' Has Been Allocated To ' . $area_names->name;
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

    public function show_cashiers() {
        $rtype = Input::get('data');
        //$bar = ucfirst($rtype); 
        $br = Input::get('branch_id');
        $shift_id = Input::get('shift_id');

        $employees = DB::table('employees')
                ->where('status', '=', 1)
                ->get();
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $cashiers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames ,GROUP_CONCAT(if( resource_allocation.active =1, shift_details.name, NULL )  ) AS sames, employees.* , resource_allocation.branch_id , branch_details.name AS branch_name ,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code , shift_details.name AS shift_name'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Cashier')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->where('employees.status', '=', 1)
                /*->whereRaw(" employees.id NOT
                        IN (

                        SELECT employee_id
                        FROM resource_allocation
                        WHERE branch_id =$br and active=1 and  shift_id =$shift_id
                        ) 
                        ")*/
                ->whereRaw(" employees.id NOT
                        IN (

                        SELECT employee_id
                        FROM resource_allocation
                        WHERE active=1
                        ) 
                        ")
                ->groupby(DB::raw("employees.id"))
                ->get();
        foreach ($cashiers as $cashier) {

            if ($cashier->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="cash_emp empList ' . $status . ' " id="' . $cashier->id . '" ><figure class="imgHolder">
              <img style="width: 77px;" src="' . $cashier->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $cashier->first_name . ' ' . $cashier->alias_name . '</b>
              <p>Designation : <span>Cashier</span></p>';
            if ($cashier->bames != NULL) {
                echo' <p>Branch : <span>' . $cashier->bames . '</span></p>';
            }
            if ($cashier->sames != NULL) {
                echo' <p>Shift : <span>' . $cashier->sames . '</span></p>';
            }
            echo '</div>
              <div class="customClear"></div>
              <figure class="flagHolder nationality" id="' . $cashier->nationality . '">
               <img src="../images/flags/' . $cashier->flag_pic . '" alt="Flag">
                 <figcaption>' . $cashier->flag_code . '</figcaption>
                </figure></div>';
        }
    }

    public function save_cashiers_shift1() {
        try {
            $dt = new \DateTime();
            $emp = Input::get('employee_id');
            $br = Input::get('branch_id');
            $shift_id = Input::get('shift_id');
            /*$cashier = DB::table('resource_allocation')
                    ->where('shift_id', '=', $shift_id)
                    ->where('branch_id', '=', $br)
                    ->where('resource_type', '=', 'CASHIER')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);

            $cashier = DB::table('resource_allocation')
                    ->where('employee_id', '=', $emp)
                    ->where('resource_type', '=', 'CASHIER')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);

            $cashier = DB::table('resource_allocation')
                    ->where('shift_id', '=', Input::get('shift_id'))->where('branch_id', '=', Input::get('branch_id'))->where('resource_type', '=', 'CASHIER')
                    ->where('employee_id', '=', $emp)
                    ->update(['active' => 0, 'deallocated_date' => '', 'mail_status' => 0]);
            */        
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
            }
            
            $allocated_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*')
                    ->whereRaw("resource_allocation.resource_type = 'CASHIER' and resource_allocation.branch_id = $br and resource_allocation.shift_id = $shift_id and resource_allocation.active = 1 and ((resource_allocation.from_date BETWEEN '$from_date' and '$to_date' ) or (resource_allocation.to_date BETWEEN '$from_date' and '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->first();
            
            if(count($allocated_details) > 0)
            {
                return -1;
            }
            
            $resourcemodel = new Resource_allocation;
            $resourcemodel->resource_type = "CASHIER";
            $resourcemodel->branch_id = Input::get('branch_id');
            $resourcemodel->shift_id = Input::get('shift_id');
            $resourcemodel->employee_id = $emp;
            $resourcemodel->from_date = $from_date;
            $resourcemodel->to_date = $to_date;
            $resourcemodel->active = 1;
            $resourcemodel->mail_status = 0;
            $resourcemodel->save();
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'BRANCH')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            $shift_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.id', '=', $shift_id)
                    ->first();
            echo $employees->first_name . ' Has Been Allocated To ' . $branch_names->name . ' And ' . $shift_names->name;
        } catch (\Exception $e) {
            
        }
    }

    public function deallocated_cashiers() {
        try {
            $dt = new \DateTime();
            $emp = Input::get('employee_id');
            $br = Input::get('branch_id');
            $shift_id = Input::get('shift_id');
            $cashier = DB::table('resource_allocation')
                    ->where('employee_id', '=', $emp)
                    ->where('branch_id', '=', $br)
                    ->where('shift_id', '=', $shift_id)
                    ->where('resource_type', '=', 'CASHIER')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'BRANCH')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            $shift_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.id', '=', $shift_id)
                    ->first();
            if ($cashier) {
                echo $employees->first_name . ' Has Been Dellocated From ' . $branch_names->name . ' And ' . $shift_names->name;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            
        }
    }

    public function deallocate_supervisors() {
        try {
            $dt = new \DateTime();
            $emp = Input::get('employee_id');
            $br = Input::get('branch_id');
            $supervisor = DB::table('resource_allocation')
                    ->where('employee_id', '=', $emp)
                    ->where('branch_id', '=', $br)
                    ->where('resource_type', '=', 'SUPERVISOR')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'BRANCH')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            if ($supervisor) {
                echo $employees->first_name . ' Has Been Dellocated From ' . $branch_names->name;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            
        }
    }

    public function deallocated_baristas() {
        try {
            $dt = new \DateTime();
            $emp = Input::get('employee_id');
            $br = Input::get('branch_id');
            $shift_id = Input::get('shift_id');
            $baristas = DB::table('resource_allocation')
                    ->where('employee_id', '=', $emp)
                    ->where('branch_id', '=', $br)
                    ->where('shift_id', '=', $shift_id)
                    ->where('resource_type', '=', 'BARISTA')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'BRANCH')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            $shift_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.id', '=', $shift_id)
                    ->first();
            if ($baristas) {
                echo $employees->first_name . ' Has Been Dellocated From ' . $branch_names->name . ' And ' . $shift_names->name;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            
        }
    }

    public function deallocate_regional_managers() {
        try {
            $dt = new \DateTime();
            $emp = Input::get('employee_id');
            $br = Input::get('region_id');
            $regional_managers = DB::table('resource_allocation')
                    ->where('employee_id', '=', $emp)
                    ->where('region_id', '=', $br)
                    ->where('resource_type', '=', 'REGIONAL_MANAGER')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'REGION')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            if ($regional_managers) {
                echo $employees->first_name . ' Has Been Dellocated From ' . $branch_names->name;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            
        }
    }

    public function deallocate_area_managers() {
        try {
            $dt = new \DateTime();
            $emp = Input::get('employee_id');
            $br = Input::get('area_id');
            $area_managers = DB::table('resource_allocation')
                    ->where('employee_id', '=', $emp)
                    ->where('area_id', '=', $br)
                    ->where('resource_type', '=', 'AREA_MANAGER')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'AREA')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            if ($area_managers) {
                echo $employees->first_name . ' Has Been Dellocated From ' . $branch_names->name;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            
        }
    }

    public function show_baristas() {
        $rtype = Input::get('data');
        $shift_id = Input::get('shift_id');
        $br = Input::get('branch_id');
        $baristas = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames ,GROUP_CONCAT(if( resource_allocation.active =1, shift_details.name, NULL )  ) AS sames, employees.* , resource_allocation.branch_id , branch_details.name AS branch_name ,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code , shift_details.name AS shift_name'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'BARISTA')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->where('employees.status', '=', 1)
                /*->whereRaw(" employees.id NOT
                        IN (

                        SELECT employee_id
                        FROM resource_allocation
                        WHERE branch_id =$br and active=1 and  shift_id =$shift_id
                        ) 
                        ")*/
                ->whereRaw(" employees.id NOT
                        IN (

                        SELECT employee_id
                        FROM resource_allocation
                        WHERE active=1
                        ) 
                        ")
                ->groupby(DB::raw("employees.id"))
                ->get();

        foreach ($baristas as $barista) {

            if ($barista->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="bar_emp empList ' . $status . ' " id="' . $barista->id . '"><figure class="imgHolder">
              <img style="width: 77px;" src="' . $barista->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $barista->first_name . ' ' . $barista->alias_name . '</b>
              <p>Designation : <span>Barista</span></p>';
            if ($barista->bames != NULL) {
                echo' <p>Branch : <span>' . $barista->bames . '</span></p>';
            }
            if ($barista->sames != NULL) {
                echo' <p>Shift : <span>' . $barista->sames . '</span></p>';
            }
            echo '</div>
              <div class="customClear"></div>
              <figure class="flagHolder nationality" id="' . $barista->nationality . '">
               <img src="../images/flags/' . $barista->flag_pic . '" alt="Flag">
                 <figcaption>' . $barista->flag_code . '</figcaption>
                </figure></div>';
        }
    }

    public function save_barista() {
        try {
            $dt = new \DateTime();
            $emp = Input::get('employee_ids');
            $br = Input::get('branch_id');
            $shift_id = Input::get('shift_id');

            /*$barista = DB::table('resource_allocation')
                    ->where('branch_id', '=', $br)
                    ->where('employee_id', '=', $emp)
                    ->where('resource_type', '=', 'BARISTA')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $barista = DB::table('resource_allocation')
                    ->where('employee_id', '=', $emp)
                    ->where('resource_type', '=', 'BARISTA')
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);

            $alreadycashier = DB::table('resource_allocation')
                    ->select('resource_allocation.*')->where('shift_id', '=', Input::get('shift_id'))->where('branch_id', '=', Input::get('branch_id'))->where('resource_type', '=', 'BARISTA')->where('employee_id', '=', $emp)
                    ->get();
            $cashier = DB::table('resource_allocation')
                    ->where('shift_id', '=', Input::get('shift_id'))->where('branch_id', '=', Input::get('branch_id'))->where('resource_type', '=', 'BARISTA')
                    ->where('employee_id', '=', $emp)
                    ->update(['active' => 0, 'deallocated_date' => '', 'mail_status' => 0]);
            */
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
            }
//            $allocated_details = DB::table('resource_allocation')
//                    ->select('resource_allocation.*')
//                    ->whereRaw("resource_allocation.resource_type = 'BARISTA' and resource_allocation.branch_id = $br and resource_allocation.shift_id = $shift_id and resource_allocation.active = 1 and ((resource_allocation.from_date BETWEEN '$from_date' and '$to_date' ) or (resource_allocation.to_date BETWEEN '$from_date' and '$to_date'))")
//                    ->first();
//            
//            if(count($allocated_details) > 0)
//            {
//                return -1;
//            }
            $resourcemodel = new Resource_allocation;
            $resourcemodel->resource_type = "BARISTA";
            $resourcemodel->branch_id = Input::get('branch_id');
            $resourcemodel->shift_id = Input::get('shift_id');
            $resourcemodel->employee_id = $emp;
            $resourcemodel->from_date = $from_date;
            $resourcemodel->to_date = $to_date;
            $resourcemodel->active = 1;
            $resourcemodel->mail_status = 0;
            $resourcemodel->save();
            $employees = DB::table('employees')
                    ->select('employees.id', 'employees.first_name')
                    ->where('employees.id', '=', $emp)
                    ->first();
            $branch_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'BRANCH')
                    ->where('master_resources.id', '=', $br)
                    ->first();
            $shift_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.id', '=', $shift_id)
                    ->first();
            echo $employees->first_name . ' Has Been Allocated To ' . $branch_names->name . ' And ' . $shift_names->name;
        } catch (\Exception $e) {
            
        }
    }

    public function check_nationality() {
        $emp = Input::get('employee_id');
        $br = Input::get('branch_id');
        $shift_id = Input::get('shift_id');
        $country = DB::table('employees')
                ->select('employees.nationality', 'employees.id')
                ->where('employees.id', '=', $emp)
                ->first();
        // print_r($country);
        $nationality = $country->nationality;
//        $employees = DB::table('employees')
//                ->select('employees.*')
//                ->join('resource_allocation', 'resource_allocation.employee_id', '=', 'employees.id')
//                ->where('employees.nationality', '=', $nationality)
//                ->where('resource_allocation.branch_id', '=', $br)
//                ->where('resource_allocation.shift_id', '=', $shift_id)
//                ->where('resource_allocation.active', '=', 1)
//                ->where('resource_allocation.employee_id', '!=', $emp)
//                ->get();
        $employees = DB::table('resource_allocation')
                ->select('resource_allocation.id')
                ->leftjoin('employees', 'resource_allocation.employee_id', '=', 'employees.id')
                ->where('employees.nationality', '=', $nationality)
                ->where('resource_allocation.branch_id', '=', $br)
                ->where('resource_allocation.shift_id', '=', $shift_id)
                ->where('resource_allocation.active', '=', 1)
                ->where('resource_allocation.employee_id', '!=', $emp)
                ->get();
        
        if (count($employees) > 0) {
            return 1;
        } else{
             return -1;
        }
    }

    public function save_area_managers() {
//        try {
        $dt = new \DateTime();
        $br = Input::get('area_id');
        $emp = Input::get('employee_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        if ($emp) {
            $check_area_managers = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, area_details.name, NULL )  ) AS bames , employees.* , resource_allocation.area_id,resource_allocation.from_date ,resource_allocation.to_date, area_details.name AS area_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code,leave_details.start_date AS leave_from,leave_details.end_date AS leave_to'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                    ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                    ->leftjoin('leaves as leave_details', 'leave_details.employee_id', '=', 'employees.id')
                    ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->whereRaw("resource_allocation.area_id = $br and resource_allocation.active = 1 and "
                            . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            if (count($check_area_managers) > 0) {
                return -1;
            } else {
                $resourcemodel = new Resource_allocation;
                $resourcemodel->resource_type = "AREA_MANAGER";
                $resourcemodel->area_id = Input::get('area_id');
                $resourcemodel->employee_id = Input::get('employee_id');
                $resourcemodel->from_date = $created;
                $resourcemodel->to_date = $ended;
                $resourcemodel->active = 1;
                $resourcemodel->save();
                $employees = DB::table('employees')
                        ->select('employees.id', 'employees.first_name')
                        ->where('employees.id', '=', $emp)
                        ->first();
                $area_names = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where('master_resources.resource_type', '=', 'AREA')
                        ->where('master_resources.id', '=', $br)
                        ->first();
                echo $employees->first_name . ' Has Been Allocated To ' . $area_names->name;
            }
        }
    }

//////////////////////////////////edit_area_managers//////////////////
    public function edit_area_managers() {
//        try {

        $br = Input::get('area_id');
        $resource_allocation_id = Input::get('ra_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        if ($resource_allocation_id) {
            $check_area_managers = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, area_details.name, NULL )  ) AS bames , employees.* , resource_allocation.area_id,resource_allocation.from_date ,resource_allocation.to_date, area_details.name AS area_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code,leave_details.start_date AS leave_from,leave_details.end_date AS leave_to'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                    ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                    ->leftjoin('leaves as leave_details', 'leave_details.employee_id', '=', 'employees.id')
                    ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->whereRaw("resource_allocation.area_id = $br and resource_allocation.id != $resource_allocation_id and resource_allocation.active = 1 and "
                            . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();

            if (count($check_area_managers) > 0) {

                return -1;
            } else {

                $new_allocation = DB::table('resource_allocation')
                        ->where('id', '=', $resource_allocation_id)
                        ->update(['from_date' => $created, 'to_date' => $ended, 'mail_status' => 0]);
                $resource_details = DB::table('resource_allocation')
                        ->select('resource_allocation.*', 'employees.*', 'area_details.name as area_name')
                        ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                        ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                        ->where('resource_allocation.id', '=', $resource_allocation_id)
                        ->first();
                $employee = $resource_details->first_name . ' ' . $resource_details->alias_name;
                return \Response::json(array('employee' => $employee, 'from' => Input::get('from_date'), 'to' => Input::get('to_date')));
            }
        }
    }

    /////////////////////////free employee///////////////////////////
    public function free_area_employee() {
        try {
            $resource_allocation_id = Input::get('id');
            $dt = new \DateTime();
            $free = DB::table('resource_allocation')
                    ->where('id', '=', $resource_allocation_id)
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*', 'employees.*', 'area_details.name as area_name')
                    ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            echo $resource_details->first_name . ' Has Been Deallocated From ' . $resource_details->area_name;
//           
        } catch (\Exception $e) {
            
        }
    }

    public function free_region_employee() {
        try {
            $resource_allocation_id = Input::get('id');
            $dt = new \DateTime();
            $free = DB::table('resource_allocation')
                    ->where('id', '=', $resource_allocation_id)
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*', 'employees.*', 'region_details.name as region_name')
                    ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            echo $resource_details->first_name . ' Has Been Deallocated From ' . $resource_details->region_name;
//           
        } catch (\Exception $e) {
            
        }
    }

    ///////////////////////////////////////////show_regional_modal//////////////////
    public function show_region_modal() {
        $emp = Input::get('employee_id');
        $br = Input::get('region_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        /*$allocated_region_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, region_details.name, NULL )  ) AS bames , employees.* , resource_allocation.* , region_details.name AS region_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.region_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended'))")
                ->where('employees.status', '=', 1)
                //->groupby(DB::raw("employees.id"))
                ->get();*/
        $allocated_region_managers = DB::table('resource_allocation')
                ->select('employees.*' , 'resource_allocation.*' , 'region_details.name AS region_name','flag_details.flag_32 AS flag_pic','flag_details.name AS flag_code')
                ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                
                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.region_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                //->groupby(DB::raw("employees.id"))
                ->get();
        if (count($allocated_region_managers) > 0) {
            foreach ($allocated_region_managers as $allocated_region_manager) {
                $leaves = DB::table('leaves')
                                ->select('leaves.*')
                                ->where('leaves.employee_id', '=', $allocated_region_manager->id)->get();
                $regions = DB::table('resource_allocation')
                                ->select('resource_allocation.*', 'region_details.name as region_name')
                                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                                ->where('resource_allocation.employee_id', '=', $allocated_region_manager->id)
                                ->where('resource_allocation.active', '=', 1)->get();

                echo'<h3>Allocations Of ' . $allocated_region_manager->first_name . ' ' . $allocated_region_manager->alias_name . '</h3><tr>';
                $region_from_date = "region_from_date_".$allocated_region_manager->id;
                $region_to_date = "region_to_date_".$allocated_region_manager->id;
                //foreach ($regions as $region) {
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocated_region_manager->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocated_region_manager->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$region_from_date.'" value="' . date("d-m-Y", strtotime($allocated_region_manager->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$region_to_date.'" value="' . date("d-m-Y", strtotime($allocated_region_manager->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocated_region_manager->region_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_region_managers(' . $allocated_region_manager->id . ')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_region_employee(' . $allocated_region_manager->id . ')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                //}
                if (count($leaves) > 0) {
                    echo'<h3>Leaves Of ' . $allocated_region_manager->first_name . ' ' . $allocated_region_manager->alias_name . '</h3><tr>';
                    foreach ($leaves as $leave) {
                        echo'<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($leave->start_date)) . ' To ' . date("d-m-Y", strtotime($leave->end_date)) . '
                   
                    </td>
                </tr>
                </table>';
                    }
                }
            }
        }
    }

//////////////////  area_modal//////////////////////////////////////////////////////////////////////////////////////
    public function show_area_modal() {
        $emp = Input::get('employee_id');
        $br = Input::get('area_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        /*$check_area_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, area_details.name, NULL )  ) AS bames , employees.* , resource_allocation.*, area_details.name AS area_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.area_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended'))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();*/
        $check_area_managers = DB::table('resource_allocation')
                ->select('employees.*' , 'resource_allocation.*' , 'area_details.name AS area_name','flag_details.flag_32 AS flag_pic','flag_details.name AS flag_code')
                ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                
                ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.area_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                //->groupby(DB::raw("employees.id"))
                ->get();

        if (count($check_area_managers) > 0) {
            foreach ($check_area_managers as $check_area_manager) {
                $areas = DB::table('resource_allocation')
                                ->select('resource_allocation.*', 'area_details.name as area_name')
                                ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                                ->where('resource_allocation.employee_id', '=', $check_area_manager->employee_id)
                                ->where('resource_allocation.active', '=', 1)->get();
                $leaves = DB::table('leaves')
                                ->select('leaves.*')
                                ->where('leaves.employee_id', '=', $check_area_manager->employee_id)->get();
                echo'<h3>Allocations Of ' . $check_area_manager->first_name . ' ' . $check_area_manager->alias_name . '</h3><tr>';

                //foreach ($areas as $area) {
                    $area_from_date = "area_from_date_".$check_area_manager->id;
                    $area_to_date = "area_to_date_".$check_area_manager->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($check_area_manager->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($check_area_manager->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$area_from_date.'" value="' . date("d-m-Y", strtotime($check_area_manager->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$area_to_date.'" value="' . date("d-m-Y", strtotime($check_area_manager->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $check_area_manager->area_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_area_managers(' . $check_area_manager->id . ')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_area_employee(' . $check_area_manager->id . ')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                //}
                if (count($leaves) > 0) {
                    echo'<h3>Leaves Of ' . $check_area_manager->first_name . ' ' . $check_area_manager->alias_name . '</h3><tr>';
                    foreach ($leaves as $leave) {
                        echo'<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '
                   
                    </td>
                </tr>
                </table>';
                    }
                }
            }
        }
    }

    ///////////////////////////////////////////show_branch_supervisor_modal//////////////////
    public function show_branch_supervisor_modal() {
        $emp = Input::get('employee_id');
        $branch_id = Input::get('branch_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        /*$allocated_supervisors = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.* , branch_details.name AS branch_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $branch_id and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended'))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();*/
        
        $allocated_supervisors = DB::table('resource_allocation')
                ->select('employees.*' , 'resource_allocation.*' , 'branch_details.name AS branch_name','flag_details.flag_32 AS flag_pic','flag_details.name AS flag_code')
                ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $branch_id and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                //->groupby(DB::raw("employees.id"))
                ->get();

        if (count($allocated_supervisors) > 0) {
            foreach ($allocated_supervisors as $allocated_supervisor) {
                $leaves = DB::table('leaves')
                                ->select('leaves.*')
                                ->where('leaves.employee_id', '=', $allocated_supervisor->id)->get();
                $branches = DB::table('resource_allocation')
                                ->select('resource_allocation.*', 'branch_details.name as branch_name')
                                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                                ->where('resource_allocation.employee_id', '=', $allocated_supervisor->id)
                                ->where('resource_allocation.active', '=', 1)->get();

                echo'<h3>Allocations Of ' . $allocated_supervisor->first_name . ' ' . $allocated_supervisor->alias_name . '</h3><tr>';

                //foreach ($branches as $branch) {
                    $branch_from_date = "branch_from_date_".$allocated_supervisor->id;
                    $branch_to_date = "branch_to_date_".$allocated_supervisor->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocated_supervisor->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocated_supervisor->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$branch_from_date.'" value="' . date("d-m-Y", strtotime($allocated_supervisor->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$branch_to_date.'" value="' . date("d-m-Y", strtotime($allocated_supervisor->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocated_supervisor->branch_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_branch_supervisor(' . $allocated_supervisor->id . ')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_branch_supervisor(' . $allocated_supervisor->id . ')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                //}
                if (count($leaves) > 0) {
                    echo'<h3>Leaves Of ' . $allocated_supervisor->first_name . ' ' . $allocated_supervisor->alias_name . '</h3><tr>';
                    foreach ($leaves as $leave) {
                        echo'<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '
                   
                    </td>
                </tr>
                </table>';
                    }
                }
            }
        }
    }
    public function show_allocated_area_managers() {
        //$bar = ucfirst($rtype); 
        $br = Input::get('area_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        //print_r($br);print_r($from_date);print_r($to_date);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        //////////////////////allocated area manager///////////////////
        $allocated_area_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, area_details.name, NULL )  ) AS bames , employees.* , resource_allocation.area_id,resource_allocation.from_date ,resource_allocation.to_date, area_details.name AS area_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code,leave_details.start_date AS leave_from,leave_details.end_date AS leave_to'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->leftjoin('leaves as leave_details', 'leave_details.employee_id', '=', 'employees.id')
                ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.area_id = $br and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
//
        foreach ($allocated_area_managers as $allocated_area_manager) {
            $areas = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'area_details.name as area_name')
                            ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_area_manager->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_areas = count($areas);
//////////////////////////NO Of Leaves//////////////////
            $leaves = DB::table('leaves')
                            ->select('leaves.*')
                            ->where('leaves.employee_id', '=', $allocated_area_manager->id)->get();
            $no_of_leaves = count($leaves);
            /////////////////////////////
            $branches = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, area_details.name, NULL )  ) AS bames , employees.* , resource_allocation.area_id , area_details.name AS area_name'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                    ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->where('employees.id', '=', $allocated_area_manager->id)
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            foreach ($branches as $branche) {
                $assigned_branches = $branche->bames;
            }
            if ($allocated_area_manager->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="empList nodragorsort ' . $status . '" id="' . $allocated_area_manager->id . '"><a href="javascript:void(0)" class="btnEditAssign btnAction edit bgBlue" onclick="area_manager_allocations_modal(' . $allocated_area_manager->id . ')">Edit</a><figure class="imgHolder">
              <img style="width: 77px;" src="' . $allocated_area_manager->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>' . $allocated_area_manager->first_name . ' ' . $allocated_area_manager->alias_name . '</b>
              <p>Designation : <span>Area Manager</span></p>
             <p>Area : <span>' . $assigned_branches . '</span></p>
              </div>
              <div class="customClear"></div>
              <figure class="flagHolder">
              <img src="../images/flags/' . $allocated_area_manager->flag_pic . '" alt="Flag">
                 <figcaption>' . $allocated_area_manager->flag_code . '</figcaption>
                </figure>';
            if ($no_of_areas > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_areas . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($areas as $area) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($area->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($area->to_date)) . '</span>
                           <b>' . $area->area_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
            if ($no_of_leaves > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPink">' . $no_of_leaves . ' Leaves</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPink">Leaves <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($leaves as $leave) {
                    echo '<div class="list">
                        <span>' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '</span>
                         </div>';
                }
                echo'</div></div></div>';
            }
            echo'  <div class = "customClear"></div></div>';
        }
    }

    public function send_region_manager_mail() {
        try {
            $br = Input::get('region_id');
            $web_url = $_ENV['WEB_URL'];
            if (Session::get('login_id')) {
                $assigned_by = Session::get('login_id');
                $assigned_name = DB::table('employees')
                        ->select('employees.first_name')
                        ->where('employees.id', '=', $assigned_by)
                        ->first();
            }
           
            $allocated_region_managers = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active = 1, region_details.name, NULL ) ) AS bames, employees.*, resource_allocation.region_id, resource_allocation.allocated_date, region_details.name AS region_name'))
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                    ->whereRaw("resource_allocation.region_id = $br")->whereRaw("resource_allocation.active = 1")->whereRaw("resource_allocation.mail_status = 0")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->first();
            if (count($allocated_region_managers) > 0) {
                $to_mail = $allocated_region_managers->email;
                if (!$to_mail) {
                    $to_mail = $allocated_region_managers->contact_email;
                }
                
                
                Mail::send('emailtemplates.region_allocation', ['first_name' => $allocated_region_managers->first_name, 'region_name' => $allocated_region_managers->region_name, 'allocated_date' => $allocated_region_managers->allocated_date, 'assigned_by' => $assigned_name->first_name,'web_url'=>$web_url], function($message)use ($to_mail) {
                    $message->to($to_mail)->subject(' Employee allocation ');
                });
                $rms = DB::table('resource_allocation')
                        ->where('employee_id', '=', $allocated_region_managers->id)
                        ->where('region_id', '=', $br)
                        ->where('resource_type', '=', 'REGIONAL_MANAGER')
                        ->update(['mail_status' => 1]);
                return 1;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function send_area_manager_mail() {
        try {
            $web_url = $_ENV['WEB_URL'];
            $br = Input::get('area_id');
            if (Session::get('login_id')) {
                $assigned_by = Session::get('login_id');
                $assigned_name = DB::table('employees')
                        ->select('employees.first_name')
                        ->where('employees.id', '=', $assigned_by)
                        ->first();
            }
            // print_r($assigned_name->first_name);
            $allocated_area_managers = DB::table('employees')
                    ->select(DB::raw('employees.*, resource_allocation.area_id, area_details.name AS area_name, resource_allocation.allocated_date '))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                    ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->whereRaw("resource_allocation.area_id = $br")->whereRaw("resource_allocation.active = 1")
                    ->whereRaw("resource_allocation.mail_status = 0")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->first();
            if (count($allocated_area_managers) > 0) {
                $to_mail = $allocated_area_managers->email;
                if (!$to_mail) {
                    $to_mail = $allocated_area_managers->contact_email;
                }
                Mail::send('emailtemplates.area_allocation', ['first_name' => $allocated_area_managers->first_name, 'area_name' => $allocated_area_managers->area_name, 'allocated_date' => $allocated_area_managers->allocated_date, 'assigned_by' => $assigned_name->first_name,'web_url'=>$web_url], function($message)use ($to_mail) {
                    $message->to($to_mail)->subject(' Employee allocation ');
                });
                $rms = DB::table('resource_allocation')
                        ->where('employee_id', '=', $allocated_area_managers->id)
                        ->where('area_id', '=', $br)
                        ->where('resource_type', '=', 'AREA_MANAGER')
                        ->update(['mail_status' => 1]);
                return 1;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function send_branch_manager_mail() {
        try {
            $web_url = $_ENV['WEB_URL'];
            $br = Input::get('branch_id');
            if (Session::get('login_id')) {
                $assigned_by = Session::get('login_id');
                $assigned_name = DB::table('employees')
                        ->select('employees.first_name')
                        ->where('employees.id', '=', $assigned_by)
                        ->first();
            }
            // print_r($assigned_name->first_name);
            $allocated_supervisorss = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active = 1, branch_details.name, NULL ) ) AS bames, employees.*, resource_allocation.branch_id, branch_details.name AS branch_name, resource_allocation.allocated_date '))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->whereRaw("resource_allocation.branch_id = $br")->whereRaw("resource_allocation.active = 1")
                    ->whereRaw("resource_allocation.mail_status = 0")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->first();
            if (count($allocated_supervisorss) > 0) {
                $to_mail = $allocated_supervisorss->email;
                if (!$to_mail) {
                    $to_mail = $allocated_supervisorss->contact_email;
                }
                Mail::send('emailtemplates.branch_allocation', ['first_name' => $allocated_supervisorss->first_name, 'branch_name' => $allocated_supervisorss->branch_name, 'allocated_date' => $allocated_supervisorss->allocated_date, 'assigned_by' => $assigned_name->first_name,'web_url'=>$web_url], function($message)use ($to_mail) {
                    $message->to($to_mail)->subject(' Employee allocation ');
                });
                $rms = DB::table('resource_allocation')
                        ->where('employee_id', '=', $allocated_supervisorss->id)
                        ->where('branch_id', '=', $br)
                        ->where('resource_type', '=', 'SUPERVISOR')
                        ->update(['mail_status' => 1]);


                return 1;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function send_cashier_mail() {
////////////////////////////////cashier mail//////////////////////////////////////////// 
        try {
            $web_url = $_ENV['WEB_URL'];
            $br = Input::get('branch_id');
            if (Session::get('login_id')) {
                $assigned_by = Session::get('login_id');
                $assigned_name = DB::table('employees')
                        ->select('employees.first_name')
                        ->where('employees.id', '=', $assigned_by)
                        ->first();
            }
            $allocated_cashiers = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active = 1, branch_details.name, NULL ) ) AS bames, employees.*, resource_allocation.branch_id, resource_allocation.allocated_date, branch_details.name AS branch_name, shift_details.name AS shift_name '))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                    ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                    ->where('master_resources.name', '=', 'CASHIER')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->whereRaw("resource_allocation.branch_id = $br")->whereRaw("resource_allocation.active = 1")
                    ->whereRaw("resource_allocation.mail_status = 0")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->first();
            if (count($allocated_cashiers) > 0) {
                $to_mail2 = $allocated_cashiers->email;
                if (!$to_mail2) {
                    $to_mail2 = $allocated_cashiers->contact_email;
                }
                Mail::send('emailtemplates.cashier_allocation', ['first_name' => $allocated_cashiers->first_name, 'branch_name' => $allocated_cashiers->branch_name, 'allocated_date' => $allocated_cashiers->allocated_date, 'assigned_by' => $assigned_name->first_name, 'shift_name' => $allocated_cashiers->shift_name,'web_url'=>$web_url], function($message)use ($to_mail2) {
                    $message->to($to_mail2)->subject(' Employee allocation ');
                });
                $rms = DB::table('resource_allocation')
                        ->where('employee_id', '=', $allocated_cashiers->id)
                        ->where('branch_id', '=', $br)
                        ->where('resource_type', '=', 'CASHIER')
                        ->update(['mail_status' => 1]);

                return 1;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function send_barista_mail() {
        try {//////////////////////////////////////////////////////////////barista mail///////////////////////////
            $web_url = $_ENV['WEB_URL'];
            $br = Input::get('branch_id');
            if (Session::get('login_id')) {
                $assigned_by = Session::get('login_id');
                $assigned_name = DB::table('employees')
                        ->select('employees.first_name')
                        ->where('employees.id', '=', $assigned_by)
                        ->first();
            }
            $allocated_barista = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active = 1, branch_details.name, NULL ) ) AS bames, employees.*, resource_allocation.branch_id, resource_allocation.allocated_date, branch_details.name AS branch_name, shift_details.name AS shift_name '))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                    ->where('master_resources.name', '=', 'BARISTA')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->whereRaw("resource_allocation.branch_id = $br")->whereRaw("resource_allocation.active = 1")
                    ->whereRaw("resource_allocation.mail_status = 0")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            if (count($allocated_barista) > 0) {
                foreach ($allocated_barista as $allocated_baristas) {
                    $to_mail3 = $allocated_baristas->email;
                    if (!$to_mail3) {
                        $to_mail3 = $allocated_baristas->contact_email;
                    }
                    Mail::send('emailtemplates.barista_allocation', ['first_name' => $allocated_baristas->first_name, 'branch_name' => $allocated_baristas->branch_name, 'allocated_date' => $allocated_baristas->allocated_date, 'assigned_by' => $assigned_name->first_name, 'shift_name' => $allocated_baristas->shift_name,'web_url'=>$web_url], function($message)use ($to_mail3) {
                        $message->to($to_mail3)->subject(' Employee allocation ');
                    });
                    $rms = DB::table('resource_allocation')
                            ->where('employee_id', '=', $allocated_baristas->id)
                            ->where('branch_id', '=', $br)
                            ->where('resource_type', '=', 'BARISTA')
                            ->update(['mail_status' => 1]);
                }
                return 1;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            return 0;
        }
//            }
//         catch (\Exception $e) {
//            return 0;
//        }
    }
    
    public function edit_region_managers() {
        try {
            $resource_allocation_id = Input::get('ra_id');
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
            }
            $region_details = DB::table('resource_allocation')
                    ->select('resource_allocation.region_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            $region_id = $region_details->region_id;
            /*$allocated_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*')
                    ->whereRaw("resource_allocation.id != $resource_allocation_id and resource_allocation.region_id = $region_id and resource_allocation.active = 1 and ((resource_allocation.from_date BETWEEN '$from_date' and '$to_date' ) or (resource_allocation.to_date BETWEEN '$from_date' and '$to_date'))")
                    ->first();*/
            
            
            $allocated_details = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, region_details.name, NULL )  ) AS bames , employees.* , resource_allocation.region_id,resource_allocation.from_date ,resource_allocation.to_date, region_details.name AS region_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code,leave_details.start_date AS leave_from,leave_details.end_date AS leave_to'))
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                    ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                    ->leftjoin('leaves as leave_details', 'leave_details.employee_id', '=', 'employees.id')
                    ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                    ->whereRaw("resource_allocation.region_id = $region_id and resource_allocation.id != $resource_allocation_id and resource_allocation.active = 1 and "
                            . "((resource_allocation.from_date BETWEEN '$from_date' AND '$to_date') OR (resource_allocation.to_date BETWEEN '$from_date' AND '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->where('employees.status', '=', 1)
                    ->groupby(DB::raw("employees.id"))
                    ->get();
            
            
            if(count($allocated_details) > 0)
            {
                return -1;
            }
            else
            {
            
            DB::table('resource_allocation')
                    ->where('id', '=', $resource_allocation_id)
                    ->update(['from_date' => $from_date, 'to_date' => $to_date]);
            }

           
        } catch (\Exception $e) {
            
        }
    }
    
    public function edit_branch_supervisor() {
        try {
            $br = Input::get('branch_id');
            $resource_allocation_id = Input::get('ra_id');
            $dt = new \DateTime();
            
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
            }
            $assigned_supervisor_details = DB::table('resource_allocation')
                    ->select('resource_allocation.employee_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            $employee_id = $assigned_supervisor_details->employee_id;
            $allocated_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*')
                    ->whereRaw("resource_allocation.branch_id = $br and resource_allocation.id != $resource_allocation_id and resource_allocation.active = 1 and ((resource_allocation.from_date BETWEEN '$from_date' and '$to_date' ) or (resource_allocation.to_date BETWEEN '$from_date' and '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->first();
            
            if(count($allocated_details) > 0)
            {
                return -1;
            }
            $new_allocation = DB::table('resource_allocation')
                        ->where('id', '=', $resource_allocation_id)
                        ->update(['from_date' => $from_date, 'to_date' => $to_date, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                        ->select('resource_allocation.*', 'employees.*', 'branch_details.name as branch_name')
                        ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                        ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                        ->where('resource_allocation.id', '=', $resource_allocation_id)
                        ->first();
            $employee = $resource_details->first_name . ' ' . $resource_details->alias_name;
            return \Response::json(array('employee' => $employee, 'from' => Input::get('from_date'), 'to' => Input::get('to_date')));
        } catch (\Exception $e) {
            echo 'Sorry There Is Some Problem';
        }
    }
    
    ///////////////////////////////////////////show_branch_barista_modal//////////////////
    public function show_branch_barista_modal() {
        $emp = Input::get('employee_id');
        $branch_id = Input::get('branch_id');
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        /*$allocated_baristas = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.* , branch_details.name AS branch_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Barista')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $branch_id and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended'))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();*/
        $allocated_baristas = DB::table('resource_allocation')
                ->select('employees.*' , 'resource_allocation.*' , 'branch_details.name AS branch_name','flag_details.flag_32 AS flag_pic','flag_details.name AS flag_code')
                ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Barista')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $branch_id and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                //->groupby(DB::raw("employees.id"))
                ->get();

        if (count($allocated_baristas) > 0) {
            foreach ($allocated_baristas as $allocated_barista) {
                $leaves = DB::table('leaves')
                                ->select('leaves.*')
                                ->where('leaves.employee_id', '=', $allocated_barista->id)->get();
                $branches = DB::table('resource_allocation')
                                ->select('resource_allocation.*', 'branch_details.name as branch_name')
                                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                                ->where('resource_allocation.employee_id', '=', $allocated_barista->id)
                                ->where('resource_allocation.active', '=', 1)->get();

                echo'<h3>Allocations Of ' . $allocated_barista->first_name . ' ' . $allocated_barista->alias_name . '</h3><tr>';

                //foreach ($branches as $branch) {
                    $branch_from_date = "branch_from_date_".$allocated_barista->id;
                    $branch_to_date = "branch_to_date_".$allocated_barista->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocated_barista->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocated_barista->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$branch_from_date.'" value="' . date("d-m-Y", strtotime($allocated_barista->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$branch_to_date.'" value="' . date("d-m-Y", strtotime($allocated_barista->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocated_barista->branch_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_branch_barista(' . $allocated_barista->id . ')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_branch_barista(' . $allocated_barista->id . ')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                //}
                if (count($leaves) > 0) {
                    echo'<h3>Leaves Of ' . $allocated_barista->first_name . ' ' . $allocated_barista->alias_name . '</h3><tr>';
                    foreach ($leaves as $leave) {
                        echo'<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '
                   
                    </td>
                </tr>
                </table>';
                    }
                }
            }
        }
    }
    
    public function edit_branch_barista() {
        try {
            $br = Input::get('branch_id');
            $shift_id = Input::get('shift_id');
            $resource_allocation_id = Input::get('ra_id');
            $dt = new \DateTime();
            
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
            }
            $assigned_barista_details = DB::table('resource_allocation')
                    ->select('resource_allocation.employee_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            $employee_id = $assigned_barista_details->employee_id;
            $allocated_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*')
                    ->whereRaw("resource_allocation.resource_type = 'BARISTA' and resource_allocation.branch_id = $br and resource_allocation.shift_id = $shift_id and resource_allocation.id != $resource_allocation_id and resource_allocation.active = 1 and ((resource_allocation.from_date BETWEEN '$from_date' and '$to_date' ) or (resource_allocation.to_date BETWEEN '$from_date' and '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->first();
            
            if(count($allocated_details) > 0)
            {
                return -1;
            }
            $new_allocation = DB::table('resource_allocation')
                        ->where('id', '=', $resource_allocation_id)
                        ->update(['from_date' => $from_date, 'to_date' => $to_date, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                        ->select('resource_allocation.*', 'employees.*', 'branch_details.name as branch_name')
                        ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                        ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                        ->where('resource_allocation.id', '=', $resource_allocation_id)
                        ->first();
            $employee = $resource_details->first_name . ' ' . $resource_details->alias_name;
            return \Response::json(array('employee' => $employee, 'from' => Input::get('from_date'), 'to' => Input::get('to_date')));
        } catch (\Exception $e) {
            echo 'Sorry There Is Some Problem';
        }
    }
    
    public function show_branch_cashier_modal() {
        $emp = Input::get('employee_id');
        $branch_id = Input::get('branch_id');
        $shift_id = Input::get('shift_id');
       
        $created = Input::get('from_date');
        $ended = Input::get('to_date');
        if ($created != '') {
            $created = explode('-', $created);
            $created = $created[2] . '-' . $created[1] . '-' . $created[0];
        }
        if ($ended != '') {
            $ended = explode('-', $ended);
            $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
        }
        /*$allocated_cashiers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.* , branch_details.name AS branch_name,flag_details.flag_32 AS flag_pic,flag_details.name AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Cashier')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $branch_id and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended'))")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();*/
        
        $allocated_cashiers = DB::table('resource_allocation')
                ->select('employees.*' , 'resource_allocation.*' , 'branch_details.name AS branch_name','flag_details.flag_32 AS flag_pic','flag_details.name AS flag_code')
                ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Cashier')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $branch_id AND resource_allocation.shift_id=$shift_id and resource_allocation.active = 1 and "
                        . "((resource_allocation.from_date BETWEEN '$created' AND '$ended') OR (resource_allocation.to_date BETWEEN '$created' AND '$ended') OR ('$created' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$ended' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                ->where('employees.status', '=', 1)
                //->groupby(DB::raw("employees.id"))
                ->get();

        if (count($allocated_cashiers) > 0) {
            foreach ($allocated_cashiers as $allocated_cashier) {
                $leaves = DB::table('leaves')
                                ->select('leaves.*')
                                ->where('leaves.employee_id', '=', $allocated_cashier->id)->get();
                $branches = DB::table('resource_allocation')
                                ->select('resource_allocation.*', 'branch_details.name as branch_name')
                                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                                ->where('resource_allocation.employee_id', '=', $allocated_cashier->id)
                                ->where('resource_allocation.active', '=', 1)->get();

                echo'<h3>Allocations Of ' . $allocated_cashier->first_name . ' ' . $allocated_cashier->alias_name . '</h3><tr>';

                //foreach ($branches as $branch) {
                    $branch_from_date = "branch_from_date_".$allocated_cashier->id;
                    $branch_to_date = "branch_to_date_".$allocated_cashier->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocated_cashier->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocated_cashier->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$branch_from_date.'" value="' . date("d-m-Y", strtotime($allocated_cashier->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$branch_to_date.'" value="' . date("d-m-Y", strtotime($allocated_cashier->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocated_cashier->branch_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_branch_cashier(' . $allocated_cashier->id . ')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_branch_cashier(' . $allocated_cashier->id . ')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                //}
                if (count($leaves) > 0) {
                    echo'<h3>Leaves Of ' . $allocated_cashier->first_name . ' ' . $allocated_cashier->alias_name . '</h3><tr>';
                    foreach ($leaves as $leave) {
                        echo'<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($leave->start_date)) . '<b> To </b>' . date("d-m-Y", strtotime($leave->end_date)) . '
                   
                    </td>
                </tr>
                </table>';
                    }
                }
            }
        }
    }
    
    public function edit_branch_cashier() {
        try {
            $br = Input::get('branch_id');
            $shift_id = Input::get('shift_id');
            $resource_allocation_id = Input::get('ra_id');
            $dt = new \DateTime();
            
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
            }
            $assigned_cashier_details = DB::table('resource_allocation')
                    ->select('resource_allocation.employee_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            $employee_id = $assigned_cashier_details->employee_id;
            $allocated_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*')
                    ->whereRaw("resource_allocation.resource_type = 'CASHIER' and resource_allocation.branch_id = $br and resource_allocation.shift_id = $shift_id and resource_allocation.id != $resource_allocation_id and resource_allocation.active = 1 and ((resource_allocation.from_date BETWEEN '$from_date' and '$to_date' ) or (resource_allocation.to_date BETWEEN '$from_date' and '$to_date') OR ('$from_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date) OR ('$to_date' BETWEEN  resource_allocation.from_date AND resource_allocation.to_date))")
                    ->first();
            
            if(count($allocated_details) > 0)
            {
                return -1;
            }
            $new_allocation = DB::table('resource_allocation')
                        ->where('id', '=', $resource_allocation_id)
                        ->update(['from_date' => $from_date, 'to_date' => $to_date, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                        ->select('resource_allocation.*', 'employees.*', 'branch_details.name as branch_name')
                        ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                        ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                        ->where('resource_allocation.id', '=', $resource_allocation_id)
                        ->first();
            $employee = $resource_details->first_name . ' ' . $resource_details->alias_name;
            return \Response::json(array('employee' => $employee, 'from' => Input::get('from_date'), 'to' => Input::get('to_date')));
        } catch (\Exception $e) {
            echo 'Sorry There Is Some Problem';
        }
    }
    
    public function free_branch_supervisor() {
        try {
            $resource_allocation_id = Input::get('id');
            $dt = new \DateTime();
            $free = DB::table('resource_allocation')
                    ->where('id', '=', $resource_allocation_id)
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*', 'employees.*', 'branch_details.name as branch_name')
                    ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            echo $resource_details->first_name . ' Has Been Deallocated From ' . $resource_details->branch_name;
           
        } catch (\Exception $e) {
            
        }
    }
    
    public function free_branch_barista() {
        try {
            $resource_allocation_id = Input::get('id');
            $dt = new \DateTime();
            $free = DB::table('resource_allocation')
                    ->where('id', '=', $resource_allocation_id)
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*', 'employees.*', 'branch_details.name as branch_name')
                    ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            echo $resource_details->first_name . ' Has Been Deallocated From ' . $resource_details->branch_name;
           
        } catch (\Exception $e) {
            
        }
    }
    
    public function free_branch_cashier() {
        try {
            $resource_allocation_id = Input::get('id');
            $dt = new \DateTime();
            $free = DB::table('resource_allocation')
                    ->where('id', '=', $resource_allocation_id)
                    ->update(['active' => 0, 'deallocated_date' => $dt, 'mail_status' => 0]);
            $resource_details = DB::table('resource_allocation')
                    ->select('resource_allocation.*', 'employees.*', 'branch_details.name as branch_name')
                    ->leftjoin('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->where('resource_allocation.id', '=', $resource_allocation_id)
                    ->first();
            echo $resource_details->first_name . ' Has Been Deallocated From ' . $resource_details->branch_name;
           
        } catch (\Exception $e) {
            
        }
    }
    public function regional_manager_allocations_modal() {
        $employee_id = Input::get('employee_id');
        
        $allocations = DB::table('resource_allocation')
                                ->select('resource_allocation.*', 'region_details.name as region_name')
                                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                                ->where('resource_allocation.employee_id', '=', $employee_id)
                                ->where('resource_allocation.active', '=', 1)->get();
        $employee_details = Employee::where('id', $employee_id)->first();
                echo'<h3>Allocations Of ' . $employee_details->first_name . ' ' . $employee_details->alias_name . '</h3><tr>';
                
                foreach ($allocations as $allocation) {
                echo '<table cellspacing="0" class="empAllocations">';    
                echo '<tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocation->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocation->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" value="' . date("d-m-Y", strtotime($allocation->from_date)) . '">
                            <b> To </b>
                            <input type="text" class="edit_to_date" value="' . date("d-m-Y", strtotime($allocation->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocation->region_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_region_managers_allocations(' . $allocation->id . ','.$employee_id.')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_region_manager(' . $allocation->id .  ','.$employee_id.')">Free</a>
                        </div>
                    </td>
                </tr>';
                echo '</table>';
                }
                
    }
    
    public function area_manager_allocations_modal() {
        $employee_id = Input::get('employee_id');
        
        $allocations =  DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'area_details.name as area_name')
                            ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                                ->where('resource_allocation.employee_id', '=', $employee_id)
                                ->where('resource_allocation.active', '=', 1)->get();
        $employee_details = Employee::where('id', $employee_id)->first();
                echo'<h3>Allocations Of ' . $employee_details->first_name . ' ' . $employee_details->alias_name . '</h3><tr>';

                foreach ($allocations as $allocation) {
                    $area_from_date = "area_from_date_".$allocation->id;
                    $area_to_date = "area_to_date_".$allocation->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocation->from_date)) . ' To ' . date("d-m-Y", strtotime($allocation->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$area_from_date.'" value="' . date("d-m-Y", strtotime($allocation->from_date)) . '">
                            <input type="text" class="edit_to_date" id="'.$area_to_date.'" value="' . date("d-m-Y", strtotime($allocation->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocation->area_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_area_managers_allocations(' . $allocation->id . ','.$employee_id.')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_area_manger(' . $allocation->id . ','.$employee_id.')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                }
                
    }
    
    public function supervisor_allocations_modal() {
        $employee_id = Input::get('employee_id');
        
        $allocations =  DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->where('resource_allocation.employee_id', '=', $employee_id)
                             ->where('resource_allocation.active', '=', 1)->get();
        $employee_details = Employee::where('id', $employee_id)->first();
                echo'<h3>Allocations Of ' . $employee_details->first_name . ' ' . $employee_details->alias_name . '</h3><tr>';

                foreach ($allocations as $allocation) {
                    $branch_from_date = "branch_from_date_".$allocation->id;
                    $branch_to_date = "branch_to_date_".$allocation->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocation->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocation->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$branch_from_date.'" value="' . date("d-m-Y", strtotime($allocation->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$branch_to_date.'" value="' . date("d-m-Y", strtotime($allocation->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocation->branch_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_supervisor_allocations(' . $allocation->id . ','.$employee_id.')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_supervisor(' . $allocation->id .  ','.$employee_id.')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                }
                
    }
    
    public function barista_allocations_modal() {
        $employee_id = Input::get('employee_id');
        
        $allocations =  DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->where('resource_allocation.employee_id', '=', $employee_id)
                             ->where('resource_allocation.active', '=', 1)->get();
        $employee_details = Employee::where('id', $employee_id)->first();
                echo'<h3>Allocations Of ' . $employee_details->first_name . ' ' . $employee_details->alias_name . '</h3><tr>';

                foreach ($allocations as $allocation) {
                    $branch_from_date = "branch_from_date_".$allocation->id;
                    $branch_to_date = "branch_to_date_".$allocation->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocation->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocation->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$branch_from_date.'" value="' . date("d-m-Y", strtotime($allocation->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$branch_to_date.'" value="' . date("d-m-Y", strtotime($allocation->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocation->branch_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_barista_allocations(' . $allocation->id . ','.$employee_id.')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_barista(' . $allocation->id .  ','.$employee_id.')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                }
                
    }
    
    public function cashier_allocations_modal() {
        $employee_id = Input::get('employee_id');
        
        $allocations =  DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->where('resource_allocation.employee_id', '=', $employee_id)
                             ->where('resource_allocation.active', '=', 1)->get();
        $employee_details = Employee::where('id', $employee_id)->first();
                echo'<h3>Allocations Of ' . $employee_details->first_name . ' ' . $employee_details->alias_name . '</h3><tr>';

                foreach ($allocations as $allocation) {
                    $branch_from_date = "branch_from_date_".$allocation->id;
                    $branch_to_date = "branch_to_date_".$allocation->id;
                    echo '<table cellspacing="0" class="empAllocations">
                <tr>
                    <td>
                        ' . date("d-m-Y", strtotime($allocation->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($allocation->to_date)) . '
                        <div class="dataUpdate">
                            <input type="text"  class="edit_from_date" id="'.$branch_from_date.'" value="' . date("d-m-Y", strtotime($allocation->from_date)) . '">
                            <b> To </b><input type="text" class="edit_to_date" id="'.$branch_to_date.'" value="' . date("d-m-Y", strtotime($allocation->to_date)) . '">
                        </div>
                    </td>
                    <td>' . $allocation->branch_name . '</td>
                    <td>
                        <div class="btnHolderV1">
                            <a href="javascript:void(0)" class="btnAction bgBlue actionEdit">Edit</a>
                            <a href="javascript:void(0)" class="btnAction bgBlue actionOk" onclick="edit_cashier_allocations(' . $allocation->id . ','.$employee_id.')">Ok</a>
                            <a href="javascript:void(0)" class="btnAction bgGreen modalEdit" onclick="free_cashier(' . $allocation->id .  ','.$employee_id.')">Free</a>
                        </div>
                    </td>
                </tr>
                </table>';
                }
                
                
    }
}
