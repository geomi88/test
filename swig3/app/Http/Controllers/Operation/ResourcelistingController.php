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
use App\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;

class ResourcelistingController extends Controller {

    public function index() {
       
        $regions = array();
        $all_regions = DB::table('master_resources')
                ->where('master_resources.resource_type', '=', 'REGION')
                ->where('master_resources.status', '=', 1)
                ->get();

        foreach ($all_regions as $region) {
            $region_details['areas'] = array();
            
            $region_details['id'] = $region->id;
            $region_details['name'] = $region->name;

            $region_areas = DB::table('master_resources')
                    ->where('master_resources.resource_type', '=', 'AREA')
                    ->where('master_resources.region_id', '=', $region->id)
                    ->where('master_resources.status', '=', 1)
                    ->get();
	    $areas = array();
            
            foreach ($region_areas as $area) {
                //$areas['branches'] = array();
                $branches = array();
                $full_branches = array();
                $region_details['areas']['branches'] = array();
                $area_details['id'] = $area->id;
                $area_details['name'] = $area->name;
                
                $region_branches = DB::table('master_resources')
                        ->where('master_resources.resource_type', '=', 'BRANCH')
                        ->where('master_resources.area_id', '=', $area->id)
                        ->where('master_resources.status', '=', 1)
                        ->get();
                foreach ($region_branches as $branch) {
                    $branch_details['id'] = $branch->id;
                    $branch_details['name'] = $branch->name;
                    //array_push($region_details['branches'], $branch_details);
                    array_push($branches, $branch_details);
                }
                $area_details['branches'] = $branches;
                //array_push($region_details['areas']['branches'], $full_branches);
                //print_r(json_encode($region_details));die('asd');
                array_push($areas, $area_details);
            }
		$region_details['areas'] = $areas;

            

            array_push($regions, $region_details);
             
        }
      //print_r(json_encode($regions));
        //print_r('<pre>');
      
        
           $shift_names = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                ->where('master_resources.status', '=', 1)
                ->get();
        return view('operation/resource_listing/index', array('regions' => $regions,'shift_names' => $shift_names));
    }
    
    public function get_region_manager() {
        //$bar = ucfirst($rtype); 
        $br = Input::get('region_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        //////////////////////allocated supervisors///////////////////
        $allocated_region_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, region_details.name, NULL )  ) AS bames , employees.* , resource_allocation.region_id , region_details.name AS region_name,flag_details.flag_32 AS flag_pic,flag_details.code3l AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'regional_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.region_id = $br")->whereRaw("resource_allocation.active = 1")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
//              print_r('<pre>');
//         print_r($allocated_supervisors);
        foreach ($allocated_region_managers as $allocated_region_manager) {
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
            $regions = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'region_details.name as region_name')
                            ->leftjoin('master_resources as region_details', 'region_details.id', '=', 'resource_allocation.region_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_region_manager->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_regions = count($regions);
            foreach ($branches as $branche) {
                $assigned_branches = $branche->bames;
            }
            if ($allocated_region_manager->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="empList ' . $status . '" id="' . $allocated_region_manager->id . '"><figure class="imgHolder">
              <img  src="' . $allocated_region_manager->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>'.$allocated_region_manager->first_name.' '.$allocated_region_manager->alias_name.'</b>
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
             echo'</div>';
        }
    }
    
     public function get_area_manager() {
        //$bar = ucfirst($rtype); 
        $br = Input::get('area_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        //////////////////////allocated supervisors///////////////////
        $allocated_area_managers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, area_details.name, NULL )  ) AS bames , employees.* , resource_allocation.area_id , area_details.name AS area_name,flag_details.flag_32 AS flag_pic,flag_details.code3l AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'area_manager')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.area_id = $br")->whereRaw("resource_allocation.active = 1")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
//              print_r('<pre>');
//         print_r($allocated_supervisors);
        foreach ($allocated_area_managers as $allocated_area_manager) {
            $areas = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'area_details.name as area_name')
                            ->leftjoin('master_resources as area_details', 'area_details.id', '=', 'resource_allocation.area_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_area_manager->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_areas = count($areas);
            
            if ($allocated_area_manager->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="empList ' . $status . '" id="' . $allocated_area_manager->id . '"><figure class="imgHolder">
              <img  src="' . $allocated_area_manager->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>'.$allocated_area_manager->first_name.' '.$allocated_area_manager->alias_name.'</b>
              <p>Designation : <span>Area Manager</span></p>
              <p>Area : <span>' . $allocated_area_manager->bames . '</span></p>
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
            echo'</div>';
        }
    }

    public function get_supervisor() {
        $br = Input::get('branch_id');
        $employees = DB::table('employees')
                ->where('status', '=', 1)
                ->get();
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        //////////////////////allocated supervisors///////////////////
        $allocated_supervisorss = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name ,flag_details.flag_32 AS flag_pic,flag_details.code3l AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'Supervisor')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $br")->whereRaw("resource_allocation.active = 1")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();
 
        foreach ($allocated_supervisorss as $allocated_supervisors) {
           
            $supervisor_branches = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_supervisors->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_branches = count($supervisor_branches);
           
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
            
          echo '  <div class="empList ' . $status . '" id="' . $allocated_supervisors->id . '"><figure class="imgHolder">
              <img  src="' . $allocated_supervisors->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>'.$allocated_supervisors->first_name.' '.$allocated_supervisors->alias_name.'</b>
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
            echo'</div>';
            
        }
    }
    
    public function get_cashier() {
        $br = Input::get('branch_id');
        $sh = Input::get('shift_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        //////////////////////allocated supervisors///////////////////
        $allocated_cashiers = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.code3l AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'CASHIER')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $br")->whereRaw("resource_allocation.active = 1")->whereRaw("resource_allocation.shift_id = $sh")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();

        foreach ($allocated_cashiers as $allocated_cashier) {
            $cashier_allocation = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_cashier->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_allocations = count($cashier_allocation);
            
            $branches = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.code3l AS flag_code'))
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
            }
            if ($allocated_cashier->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            echo '  <div class="empList ' . $status . '" id="' . $allocated_cashier->id . '"><figure class="imgHolder">
              <img style="width: 77px;" src="' . $allocated_cashier->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>'.$allocated_cashier->first_name.' '.$allocated_cashier->alias_name.'</b>
              <p>Designation : <span>Cashier</span></p>
              <p>Branch : <span>' . $assigned_branches . '</span></p>
              </div>
              <div class="customClear"></div>
              <figure class="flagHolder nationality" id="' . $allocated_cashier->nationality . '">
              <img src="../images/flags/' . $allocated_cashier->flag_pic . '" alt="Flag">
                 <figcaption>' . $allocated_cashier->flag_code . '</figcaption>
                </figure>';
             if ($no_of_allocations > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_allocations . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($cashier_allocation as $branch) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($branch->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($branch->to_date)) . '</span>
                            <b>' . $branch->branch_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
              echo  '<div class="customClear"></div></div>';
        }
    }

    public function get_barista() {
        $br = Input::get('branch_id');
        $sh = Input::get('shift_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        //////////////////////allocated supervisors///////////////////
        $allocated_baristas = DB::table('employees')
                ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.code3l AS flag_code'))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->leftjoin('resource_allocation', 'employees.id', '=', 'resource_allocation.employee_id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                ->leftjoin('country as flag_details', 'flag_details.id', '=', 'employees.nationality')
                ->where('master_resources.name', '=', 'BARISTA')->where('master_resources.resource_type', '=', 'JOB_POSITION')
                ->whereRaw("resource_allocation.branch_id = $br")->whereRaw("resource_allocation.active = 1")->whereRaw("resource_allocation.shift_id = $sh")
                ->where('employees.status', '=', 1)
                ->groupby(DB::raw("employees.id"))
                ->get();

        foreach ($allocated_baristas as $allocated_barista) {
            if ($allocated_barista->bames != NULL) {
                $status = 'assigned';
            } else {
                $status = '';
            }
            
            $barista_allocation = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.name as branch_name')
                            ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->where('resource_allocation.employee_id', '=', $allocated_barista->id)
                            ->where('resource_allocation.active', '=', 1)->get();
            $no_of_allocations = count($barista_allocation);
            
            $branches = DB::table('employees')
                    ->select(DB::raw(' GROUP_CONCAT(if( resource_allocation.active =1, branch_details.name, NULL )  ) AS bames , employees.* , resource_allocation.branch_id , branch_details.name AS branch_name, shift_details.name AS shift_name ,flag_details.flag_32 AS flag_pic,flag_details.code3l AS flag_code'))
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
            }

            echo '  <div class="empList ' . $status . '" id="' . $allocated_barista->id . '"><figure class="imgHolder">
              <img style="width: 77px;" src="' . $allocated_barista->profilepic . '" alt="Profile">
              </figure>
              <div class="details">
              <b>'.$allocated_barista->first_name.' '.$allocated_barista->alias_name.'</b>
              <p>Designation : <span>Barista</span></p>
              <p>Branch : <span>' . $assigned_branches . '</span></p>
              </div>
              <div class="customClear"></div>
              <figure class="flagHolder nationality" id="' . $allocated_barista->nationality . '">
              <img src="../images/flags/' . $allocated_barista->flag_pic . '" alt="Flag">
                 <figcaption>' . $allocated_barista->flag_code . '</figcaption>
                                </figure>';
             if ($no_of_allocations > 0) {
                echo'<div class="allocationBtnHolder">
                        <a href="javascript:void(0)" class="btns bgPurple">' . $no_of_allocations . ' Allocations</a>
                        <div class="toolTipV1">
                        <h3 class="bgLightPurple">Allocations <a href="javascript:void(0)">Close</a></h3>
                        <div class="listHolder">';
                foreach ($barista_allocation as $branch) {
                    echo '<div class="list">
                            <span>' . date("d-m-Y", strtotime($branch->from_date)) . '<b> To </b>' . date("d-m-Y", strtotime($branch->to_date)) . '</span>
                            <b>' . $branch->branch_name . '</b>
                          </div>';
                }
                echo'</div></div></div>';
            }
              echo  '<div class="customClear"></div></div>';
        }
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
        
        $arrays = explode(',', $shifts->shift_id);
        $n = 0;
        foreach ($arrays as $array) {
            $n++;
            $shift_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.status', '=', 1)
                    ->where('master_resources.id', '=', $array)
                    ->first();
            
            echo '<li class="shift_name" onclick="get_cashier('.$array.')"><a href="javascript:void(0)">'.$shift_names->name.'</a></li>';
        }
    }

    
    
}
