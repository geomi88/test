<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Usermodule;
use App\Models\Country;
use App\Models\Document;
use DB;
use Mail;

class UserpermissionController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0)
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
        $job_positions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
             $search_code_key = Input::get('search_code_key');
            $job_position = Input::get('job_position');
            $country = Input::get('country');
            $employees = DB::table('employees')
                     ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                 ->where('employees.status', '!=', 2)
                     ->where('employees.admin_status', '=', 0)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
                    })
                     ->when($search_code_key, function ($query) use ($search_code_key) {
                        return $query->whereRaw("(employees.username like '$search_code_key%')");
                    })
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->where('employees.nationality', '=', $country);
                    })
                    ->orderby('employees.created_at', 'DESC')
                    ->paginate($paginate);
            return view('hr/userpermissions/searchresults', array('employees' => $employees));
        }
        return view('hr/userpermissions/index', array('employees' => $employees, 'job_positions' => $job_positions, 'countries' => $countries));
    }

 



    public function showdetails($id) {
        $id = \Crypt::decrypt($id);
        
        $modules = DB::table('modules')
                        ->select('modules.*')
                        ->where(['parent_id' => 0])->get();
      
        $modules_full_array = array();
        foreach ($modules as $module) {
            $sub_modules_array = array();
            $modules_array['id'] = $module->id;
            $modules_array['name'] = $module->name;
            $modules_array['class_name'] = $module->class_name;
            $sub_modules = DB::table('modules')
                            ->select('modules.*')
                            ->where(['parent_id' => $module->id])->get();
            foreach ($sub_modules as $sub_module) {
                $sub_modules_row['id'] = $sub_module->id;
                $sub_modules_row['name'] = $sub_module->name;
                $sub_modules_row['class_name'] = $sub_module->class_name;
                array_push($sub_modules_array, $sub_modules_row);
            }
            $modules_array['sub_modules'] = $sub_modules_array;
            array_push($modules_full_array, $modules_array);
        }
        
        $modules = $modules_full_array;
        $user_modules = DB::table('user_modules')
                        ->select('user_modules.module_id','user_modules.filter_by_job_position')
                        ->where(['employee_id' => $id])->get();
        
        $user_modules_array = array();
        foreach ($user_modules as $user_module) {
//            array_push($user_modules_array, $user_module->module_id);
            $user_modules_array[$user_module->module_id]=array('id'=>$user_module->module_id,'filter_by_job_position'=>$user_module->filter_by_job_position);
        }
        
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $employees = DB::table('employees')
                    ->select('employees.first_name as first_name','employees.alias_name as alias_name')
                    ->where(['id' => $id])
                    ->first();
        
        Session::set('employee_id', $id);
        return view('hr/userpermissions/edit', array('modules' => $modules,'empid' => $id, 'user_modules' => $user_modules_array,'job_positions'=>$job_positions,'employees'=>$employees));
    }

     public function updatemodules() {
        try {
        
            $previlege_data = Input::all();
            $employeid = $previlege_data['empid'];
            
            DB::table('user_modules')->where('employee_id', $employeid)->delete();
            $usermodulemodel = new Usermodule;
        
            if (isset($previlege_data['modules'])) {
                foreach ($previlege_data['modules'] as $module) {
                    $module_previlege['module_id'] = $module;
                    $module_previlege['employee_id'] = $employeid;
                    $result = Usermodule::create($module_previlege);
                }
            }
            
            $arrSettings=array();
            if (isset($previlege_data['jobpositions'])) {
                $arrSettings=$previlege_data['jobpositions'];
            }
            
           $previlege_data['sub_modules'][]=DB::table('modules')->select('modules.id')
                        ->whereRaw("modules.name='To Do'")->value('id');
           $previlege_data['sub_modules'][]=DB::table('modules')->select('modules.id')
                        ->whereRaw("modules.name='View Plan'")->value('id');
           $previlege_data['sub_modules'][]=DB::table('modules')->select('modules.id')
                        ->whereRaw("modules.name='Create Plan'")->value('id');
           $previlege_data['sub_modules'][]=DB::table('modules')->select('modules.id')
                        ->whereRaw("modules.name='History'")->value('id');
           $previlege_data['sub_modules'][]=DB::table('modules')->select('modules.id')
                        ->whereRaw("modules.name='Assign Task List'")->value('id');
           $previlege_data['sub_modules'][]=DB::table('modules')->select('modules.id')
                        ->whereRaw("modules.name='Track Task'")->value('id');
           $previlege_data['sub_modules'][]=DB::table('modules')->select('modules.id')
                        ->whereRaw("modules.name='Add Suggestion'")->value('id');
           

            if (isset($previlege_data['sub_modules'])) {
                foreach ($previlege_data['sub_modules'] as $sub_module) {
                    
                    $module_details = DB::table('modules')
                            ->select('modules.*')
                            ->where(['id' => $sub_module])->first();
                    $module_id = $module_details->parent_id;
                    
                    $module_permission_status = DB::table('user_modules')
                            ->select('user_modules.*')
                            ->where(['module_id' => $module_id,'employee_id' => $employeid])->first();
                    if(count($module_permission_status)<1)
                    {
                      $sub_module_previlege['module_id'] = $module_id;
                      $sub_module_previlege['employee_id'] = $employeid;
                      $result = Usermodule::create($sub_module_previlege);  
                    }
                    
                   
                    $sub_module_previlege['module_id'] = $sub_module;
                    $sub_module_previlege['employee_id'] = $employeid;
                    if(key_exists($sub_module, $arrSettings) && $arrSettings[$sub_module]!=''){
                        $sub_module_previlege['filter_by_job_position'] = rtrim($arrSettings[$sub_module],",");
                    }else{
                        $sub_module_previlege['filter_by_job_position'] = NULL;
                    }
                    $result = Usermodule::create($sub_module_previlege);
                    
                    $create_module_details = DB::table('modules')
                            ->select('modules.*')
                            ->where(['parent_id' => $sub_module])->first();
                    if(count($create_module_details)>0)
                    {
                    $create_module_id = $create_module_details->id;
                    $sub_module_previlege['module_id'] = $create_module_id;
                    $sub_module_previlege['employee_id'] = $employeid;
                    $result = Usermodule::create($sub_module_previlege);
                    }
                }
            }

            
            Toastr::success('Employee Permissions Saved Successfully!', $title = null, $options = []);
            return 1;
        } 
        catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

}
