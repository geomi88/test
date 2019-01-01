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
use App\Models\Accounts;
use DB;
use Mail;
use App;
use PDF;
use Excel;


class PayrollController extends Controller {

    public function index(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');

        $strUrl = url()->current();
        $job_id = '';
        $country_id = '';
        if ($id) {
            if (strpos($strUrl, 'employeewithcountry') !== false) {
                $country_id = \Crypt::decrypt($id);
            } else {
                $job_id = \Crypt::decrypt($id);
            }
        }


        $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name','idprofessional.name as id_professional_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as idprofessional', 'idprofessional.id', '=', 'employees.id_professional')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0)
                ->when($job_id, function ($query) use ($job_id) {
                    return $query->where('employees.job_position', '=', $job_id);
                })
                ->when($country_id, function ($query) use ($country_id) {
                    return $query->where('employees.nationality', '=', $country_id);
                })
                ->orderby('employees.created_at', 'DESC')
               ->paginate($paginate);
              
                
        $job_positions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();

        $divisions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'DIVISION', 'status' => 1])
                ->orderby('name', 'ASC')
                ->get();
        
         $id_professional = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'ID_PROFESSION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
            $searchbyemail = Input::get('searchbyemail');
            $job_position = Input::get('job_position');
            $division = Input::get('division');
            $country = Input::get('country');
            $status = Input::get('status');

            $employees = DB::table('employees')
                    ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name','idprofessional.name as id_professional_name')
                    ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                    ->leftjoin('master_resources as idprofessional', 'idprofessional.id', '=', 'employees.id_professional')
                    ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                    ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                     ->where('employees.status', '!=', 2)
                    ->where('employees.admin_status', '=', 0)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
                    })
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->where('employees.nationality', '=', $country);
                    })
                    ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("(employees.email like '$searchbyemail%')");
                    })
                    ->when($searchbyph, function ($query) use ($searchbyph) {
                        if ($searchbyph == ';') {
                            $searchbyph = 0;
                        }
                        return $query->whereRaw("(employees.mobile_number like '$searchbyph%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(employees.username like '$searchbycode%')");
                    })
                    ->when($division, function ($query) use ($division) {
                        return $query->where('employees.division', '=', $division);
                    })
                    ->when($status, function ($query) use ($status) {
                        return $query->where('employees.status', '=', $status);
                    })
                    ->orderby('employees.created_at', 'DESC')
                          
                   ->paginate($paginate);
           return view('hr/payroll/searchresults', array('employees' => $employees));
        }
        return view('hr/payroll/index', array('employees' => $employees, 'job_positions' => $job_positions, 'countries' => $countries, "job_id" => $job_id, "divisions" => $divisions, 'country_id' => $country_id,'id_professional'=>$id_professional));
    }

    public function add() {
        if (Session::get('company')) {
            $companies = Session::get('company');
        }

        Session::forget('employee_id');
        $religions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'RELIGION', 'status' => 1])->get();
        $gender_list = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'GENDER', 'status' => 1])->get();
        // $companies = Company::all();
        $job_positions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        $divisions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'DIVISION', 'status' => 1])->get();
        $modules = DB::table('modules')
                        ->select('modules.*')
                        ->where(['parent_id' => 0])->get();
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        $employees = DB::table('employees')
                ->select('employees.*')
                ->where('employees.status', '=', 1)
                ->get();
        
         $id_professional = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'ID_PROFESSION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();

        $modules_full_array = array();
        foreach ($modules as $module) {
            $sub_modules_array = array();
            $modules_array['id'] = $module->id;
            $modules_array['name'] = $module->name;
            $sub_modules = DB::table('modules')
                            ->select('modules.*')
                            ->where(['parent_id' => $module->id])->get();
            foreach ($sub_modules as $sub_module) {
                $sub_modules_row['id'] = $sub_module->id;
                $sub_modules_row['name'] = $sub_module->name;
                array_push($sub_modules_array, $sub_modules_row);
            }
            $modules_array['sub_modules'] = $sub_modules_array;
            array_push($modules_full_array, $modules_array);
        }
        $modules = $modules_full_array;
        return view('employee/add', array('title' => 'Add Employee', 'description' => '', 'page' => '', 'religions' => $religions,
            'gender_list' => $gender_list, 'job_positions' => $job_positions, 'companies' => $companies, 'divisions' => $divisions, 'modules' => $modules, 'countries' => $countries, 'employees' => $employees,'id_professional'=>$id_professional));
    }

    public function store() {


        $employee_data = Input::all();
      
         $top_manager_id= $employee_data['top_manager'];
       unset($employee_data['top_manager']);
         $employee_data['top_manager_id']=$top_manager_id;
        $employee_id = Session::get('employee_id');
        $employee_code = Input::get('employee_code');
        $login_id=  Session::get('login_id');
          unset($employee_data['val_professional']);
       
        $employee_dup_data = DB::table('employees')
                            ->select('employees.username')
                            ->where('username', '=', $employee_code)
                            ->where('status', '!=', 2)
                            ->where('id', '!=', $employee_id)->get();
         
        if(count($employee_dup_data)>0){
            return 1;
        } 
      
        $subfolder = Input::get('employee_code');
        $s3 = \Storage::disk('s3');
        $world = Config::get('app.WORLD');
        if (Input::hasFile('profilepic')) {
            $profilepic = Input::file('profilepic');
            $extension = time() . '.' . $profilepic->getClientOriginalExtension();
            $filePath = config('filePath.s3.bucket') . $world . '/profile_pics/';
            $filePath = $filePath . $subfolder . '/' . $extension;
            $s3filepath = $s3->put($filePath, file_get_contents($profilepic), 'public');
            $pic_url = Storage::disk('s3')->url($filePath);
        } else {
            $employee_pic = array();
            if (Session::get('employee_id')) {
                $employee_id = Session::get('employee_id');
                $employee_pic = DB::table('employees')
                        ->select('employees.profilepic')
                        ->where('id', $employee_id)
                        ->first();
            }
            if (count($employee_pic) > 0) {
                $pic_url = $employee_pic->profilepic;
            } else {
                $pic_url = \URL::asset('/images/imgAvatar.jpg');
            }
        }
        
        $employee_data['profilepic'] = $pic_url;
        $password = Input::get('password');
        
       
        if($password!=""){
             
           Session::set('loginkey', $password);
        $employee_data['password'] = Hash::make($password);
        }else{
            unset($employee_data['password']);
        }
        $employee_data['username'] = Input::get('employee_code');
        $employee_data['company'] = Session::get('company');
        unset($employee_data['employee_code']);
        unset($employee_data['email_radio']);
        if (Session::get('employee_id')) {
            unset($employee_data['curPosition']);
            $employee_id = Session::get('employee_id');
            $employee = DB::table('employees')
                        ->select('employees.job_position')
                        ->where('id', $employee_id)
                        ->first();
            
         
            
            if ($employee_data['job_position'] != $employee->job_position) {
                DB::table('resource_allocation')
                    ->where('employee_id', $employee_id)
                    ->update(['active' => 0]);
            }
        
            DB::table('employees')
                    ->where('id', $employee_id)
                    ->update($employee_data);
            
            DB::table('ac_accounts')
                        ->where('party_id', $employee_id)
                        ->update(['code'=>$employee_data['username'],'first_name'=>$employee_data['first_name'],'last_name'=>$employee_data['last_name'],'alias_name'=>$employee_data['alias_name']]);
            
        } else {
           $employee_data['created_by']=$login_id;
            $result = Employee::create($employee_data);

            /* $username = Input::get('username');
              $password = Input::get('mobile_number');
              Mail::send('emailtemplates.employee_reg', ['username' => $username,'password' => $password], function($message) {
              $message->to(Input::get('email'))->subject('Employee Registration');
              }); */
            
            $modules = DB::table('modules')
                ->select('modules.id as id')
                ->whereRaw("modules.is_removable=0")
                ->get();
        
            foreach ($modules as $module) {
                $model = new Usermodule();
                $model->module_id= $module->id;
                $model->employee_id= $result->id;
                $model->save();
            }

            Session::set('employee_id', $result->id);
            
            $model= new Accounts();
            $model->party_id = $result->id;
            $model->code = $employee_data['username'];
            $model->first_name = $employee_data['first_name'];
            $model->last_name = $employee_data['last_name'];
            $model->alias_name = $employee_data['alias_name'];
            $model->type = "Employee";
            $model->save();
        }
    }

    public function checkemployeecode() {
        if (Session::get('employee_id')) {
            $employee_id = Session::get('employee_id');
            $employee_code = Input::get('employee_code');
            $employee_data = DB::table('employees')
                            ->select('employees.username')
                            ->where('username', '=', $employee_code)
                            //->where('status', '!=', 2)
                            ->where('id', '!=', $employee_id)->get();
            //->where(['username' => $employee_code])->get();
        } else {
            $employee_code = Input::get('employee_code');
            $employee_data = DB::table('employees')
                            ->select('employees.username')
                           // ->where('status', '!=', 2)
                            ->where(['username' => $employee_code])
                            ->get();
        }
        if (count($employee_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function checkemail() {
        $email = Input::get('email');
        if (Session::get('employee_id')) {
            $employee_id = Session::get('employee_id');
            $employee_data = DB::table('employees')
                            ->select('employees.email')
                            ->where('email', '=', $email)
                            ->where('status', '!=', 2)
                            ->where('id', '!=', $employee_id)
                            ->get();
           
        } else {
            $employee_code = Input::get('employee_code');
            $employee_data = DB::table('employees')
                            ->select('employees.email')
                            ->where('status', '!=', 2)
                            ->where(['email' => $email])
                            ->get();
        }
        if (count($employee_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }
    
    public function checkphoneno() {
        $mob_no = Input::get('mobile_number');
        if (Session::get('employee_id')) {
            $employee_id = Session::get('employee_id');
            $employee_data = DB::table('employees')
                            ->select('employees.mobile_number')
                            ->where('mobile_number', '=', $mob_no)
                            ->where('status', '!=', 2)
                            ->where('id', '!=', $employee_id)
                            ->get();
           
        } else {
            $employee_code = Input::get('employee_code');
            $employee_data = DB::table('employees')
                            ->select('employees.mobile_number')
                            ->where('status', '!=', 2)
                            ->where(['mobile_number' => $mob_no])
                            ->get();
        }
        if (count($employee_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }
    
    public function checkpassportno() {
        $pp_no = Input::get('passport_number');
        if (Session::get('employee_id')) {
            $employee_id = Session::get('employee_id');
            $employee_data = DB::table('employees')
                            ->select('employees.passport_number')
                            ->where('passport_number', '=', $pp_no)
                            ->where('status', '!=', 2)
                            ->where('id', '!=', $employee_id)
                            ->get();
           
        } else {
            $employee_code = Input::get('employee_code');
            $employee_data = DB::table('employees')
                            ->select('employees.passport_number')
                            ->where('status', '!=', 2)
                            ->where(['passport_number' => $pp_no])
                            ->get();
        }
        if (count($employee_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }
    
    public function checkresidentsid() {
        $res_id = Input::get('residence_id_number');
        if (Session::get('employee_id')) {
            $employee_id = Session::get('employee_id');
            $employee_data = DB::table('employees')
                            ->select('employees.residence_id_number')
                            ->where('residence_id_number', '=', $res_id)
                            ->where('status', '!=', 2)
                            ->where('id', '!=', $employee_id)
                            ->get();
           
        } else {
            $employee_code = Input::get('employee_code');
            $employee_data = DB::table('employees')
                            ->select('employees.residence_id_number')
                            ->where('status', '!=', 2)
                            ->where(['residence_id_number' => $res_id])
                            ->get();
        }
        if (count($employee_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('employees')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Employee Successfully Disabled', $title = null, $options = []);
            return Redirect::to('employee');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('employee');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('employees')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            
            $companies = DB::table('ac_accounts')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Employee Successfully Enabled', $title = null, $options = []);
            return Redirect::to('employee');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('employee');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('employees')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            
            $companies = DB::table('ac_accounts')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
                        
            Toastr::success('Employee Successfully Deleted', $title = null, $options = []);
            return Redirect::to('employee/delete');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('employee/delete');
        }
    }

    public function edit($id) {
        $id = \Crypt::decrypt($id);
         $div_id = Session::get('div_id');
        
         if($div_id == ""){
             $div_id="step1";
         }else{
             $div_id="step4";
         }
        $employee_data = DB::table('employees')
                        ->select('employees.*')
                        ->where(['id' => $id])->first();
        $religions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'RELIGION', 'status' => 1])->get();
        $gender_list = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'GENDER', 'status' => 1])->get();
        $companies = Company::all();
        $job_positions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])->get();
        $divisions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'DIVISION', 'status' => 1])->get();
        $modules = DB::table('modules')
                        ->select('modules.*')
                        ->where(['parent_id' => 0])->get();
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        $employees = DB::table('employees')
                ->select('employees.*')
                ->where('employees.status', '=', 1)
                ->get();
        
        $id_professional = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'ID_PROFESSION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        ///////////////////////////// id pic//////////////////
        $id_card_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'ID_CARD')
                ->first();
        if ($id_card_pic == '') {
            $id_pic = '';
        } else {
            $id_pic = $id_card_pic->file_url;
        }
        //////////////////////// job decription pic////////////////////
        $job_descr = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'JOB_DESCRIPTION')
                ->first();
        if ($job_descr == '') {
            $job_pic = '';
        } else {
            $job_pic = $job_descr->file_url;
        }
        ///////////////////////contract_pic///////////////////////
        $con_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'CONTRACT')
                ->first();
        if ($con_pic == '') {
            $contract_pic = '';
        } else {
            $contract_pic = $con_pic->file_url;
        }
        ////////////////// start_from///////////////////////
        $start_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'START_FROM')
                ->first();
        if ($start_pic == '') {
            $st_pic = '';
        } else {
            $st_pic = $start_pic->file_url;
        }
        ///////////////////////personal prfile////////////////////  
        $per_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'PERSONAL_PROFILE')
                ->first();
        if ($per_pic == '') {
            $pp_pic = '';
        } else {
            $pp_pic = $per_pic->file_url;
        }
        /////////////////////qualifiaction//////////////////
        $qu_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'QUALIFICATION')
                ->first();
        if ($qu_pic == '') {
            $q_pic = '';
        } else {
            $q_pic = $qu_pic->file_url;
        }
        //////////////////////////////////cv/////////////////////
        $cv_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'CV')
                ->first();
        if ($cv_pic == '') {
            $c_pic = '';
        } else {
            $c_pic = $cv_pic->file_url;
        }
        //////////////////////////////////Arabic cv/////////////////////
        $arabiccv_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'ARABIC_CV')
                ->first();
        if ($arabiccv_pic == '') {
            $acv_pic = '';
        } else {
            $acv_pic = $arabiccv_pic->file_url;
        }
        //////////////////////////job offer////////////////
        $jb_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'JOB_OFFER')
                ->first();
        if ($jb_pic == '') {
            $j_pic = '';
        } else {
            $j_pic = $jb_pic->file_url;
        }
        ///////////////////passport////////////////////
        $pass_pic = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'PASSPORT')
                ->first();
        if ($pass_pic == '') {
            $pa_pic = '';
        } else {
            $pa_pic = $pass_pic->file_url;
        }
        $modules_full_array = array();
        foreach ($modules as $module) {
            $sub_modules_array = array();
            $modules_array['id'] = $module->id;
            $modules_array['name'] = $module->name;
            $sub_modules = DB::table('modules')
                            ->select('modules.*')
                            ->where(['parent_id' => $module->id])->get();
            foreach ($sub_modules as $sub_module) {
                $sub_modules_row['id'] = $sub_module->id;
                $sub_modules_row['name'] = $sub_module->name;
                array_push($sub_modules_array, $sub_modules_row);
            }
            $modules_array['sub_modules'] = $sub_modules_array;
            array_push($modules_full_array, $modules_array);
        }
        $modules = $modules_full_array;
        $user_modules = DB::table('user_modules')
                        ->select('user_modules.module_id')
                        ->where(['employee_id' => $id])->get();
        $user_modules_array = array();
        foreach ($user_modules as $user_module) {
            array_push($user_modules_array, $user_module->module_id);
        }
        Session::set('employee_id', $id);
         Session::set('div_id', "");
        return view('employee/edit', array('title' => 'Add Employee', 'description' => '', 'page' => '', 'religions' => $religions,
            'gender_list' => $gender_list, 'job_positions' => $job_positions, 'companies' => $companies, 'divisions' => $divisions, 'modules' => $modules,
            'employee_data' => $employee_data, 'user_modules' => $user_modules_array, 'countries' => $countries, 'employees' => $employees, 'id_card_pic' => $id_card_pic, 'job_pic' => $job_pic, 'contract_pic' => $contract_pic, 'st_pic' => $st_pic, 'pp_pic' => $pp_pic, 'q_pic' => $q_pic, 'c_pic' => $c_pic, 'acv_pic' => $acv_pic,'j_pic' => $j_pic, 'pa_pic' => $pa_pic, 'id_pic' => $id_pic,'div_id'=>$div_id,'id_professional'=>$id_professional));
    }

    public function forbidden() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
         return view('dashboard/forbidden');
    }

    public function logout() {
        Auth::logout();
        return Redirect::to('/');
    }

    public function searchemployee() {
        
    }

    public function fetch_notification_count() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $notify_counts = DB::table('notifications')
                        ->where('data->to', $login_id)->where('read_at', NULL)->count();
        return $notify_counts;
    }

    public function fetch_notification() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $notification = DB::table('notifications')
                        ->select('notifications.*')
                        ->where('data->to', $login_id)->orderBy('created_at', 'DESC')->get();
        $noti = array();
        //print_r($notification);
        foreach ($notification as $notifications) {
            $details = json_decode($notifications->data);
            $url='';
            if ($notifications->read_at != NULL) {
                $old = 'old';
            } else {
                $old = "";
            }
            if($details->category=='Leave Requisition')
            {
                $url='leave_requisition';
            }
            if($details->category=='General Requisition')
            {
                $url='general_requisition';
            }
            if($details->category=='Maintenance Requisition')
            {
                $url='maintenance_requisition';
            }
            
            
            $vid = $notifications->id;
            
          $public_path= url('/');
          
          if ($details->category == 'Inventory Request') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/' . $details->type . '/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else if ($details->category == 'Analyst Discussion') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/' . $details->type . '/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else if ($details->category == 'To do Notification') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/' . $details->type . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else if ($details->category == 'Assigend Task') {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/dashboard/assign_task/edit/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            else {
                echo '<a  class="not_id ' . $old . ' " id=' . $notifications->id . ' value="' . $notifications->id . ' "href="' . $public_path . '/requisition/' . $url . '/' . $details->type . '/' . \Crypt::encrypt($details->notifiable_id) . '">
                                    <span>' . $details->category . '</span>
                                    <em>' . date("d-m-Y", strtotime($notifications->created_at)) . '</em>
                                    <b>' . $details->message . '</b>
                                </a>';
            }
            
        }
    }

    public function mark_notification() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $id = Input::get('id');
        $notification = DB::table('notifications')
                ->where('id', $id)
                ->update(['read_at' => date("Y-m-d")]);
    }

    public function upload_documents() {
        try {
            if (Session::get('employee_id')) {
                $employee_id = Session::get('employee_id');
                $employee_code = DB::table('employees')
                        ->select('employees.username')
                        ->where('id', $employee_id)
                        ->first();
            }
            $subfolder = $employee_code->username;
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            $web_url = $_ENV['WEB_URL'];
            
            /////////////////////profile_pic//////////////////////
            if (Input::hasFile('profilepic')) {
                $profilepic = Input::file('profilepic');
                $extension = time() . '.' . $profilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/profile_pics/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($profilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
            } else {
                $pic_url = \URL::asset('/images/imgAvatar.jpg');
                }
            $employee_data['profilepic'] = $pic_url;
            if (Session::get('employee_id')) {
                $employee_id = Session::get('employee_id');
                DB::table('employees')
                        ->where('id', $employee_id)
                        ->update($employee_data);
            }

            //////////////////////// identification card//////////////////
            if (Input::hasFile('indentpic')) {
                $indentpic = Input::file('indentpic');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ID_CARD';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ID_CARD';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }

            ////////////////////// JOB PROFILE//////////////////////////


            if (Input::hasFile('jobprofilepic')) {
                $jobprofilepic = Input::file('jobprofilepic');
                $extension = time() . '.' . $jobprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/JOB_DESCRIPTION';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($jobprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'JOB_DESCRIPTION';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }

            /////////////////////////contract////////////////////////////////

            if (Input::hasFile('conprofilepic')) {
                $conprofilepic = Input::file('conprofilepic');
                $extension = time() . '.' . $conprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/CONTRACT';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($conprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'CONTRACT';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }

            /////////////////////////////////////////start_form////////////////////
            if (Input::hasFile('stprofilepic')) {
                $stprofilepic = Input::file('stprofilepic');
                $extension = time() . '.' . $stprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/START_FROM';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($stprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'START_FROM';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            //////////////////////////////////PERSONAL_PROFILE////////////////
            if (Input::hasFile('perprofilepic')) {
                $perprofilepic = Input::file('perprofilepic');
                $extension = time() . '.' . $perprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/PERSONAL_PROFILE';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($perprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'PERSONAL_PROFILE';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            ///////////////////////////////QUALIFIACTION////////////////////////

            if (Input::hasFile('qprofilepic')) {
                $qprofilepic = Input::file('qprofilepic');
                $extension = time() . '.' . $qprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/QUALIFICATION';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($qprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'QUALIFICATION';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            /////////////////////////cv////////////////////////
            if (Input::hasFile('cvprofilepic')) {
                $cvprofilepic = Input::file('cvprofilepic');
                $extension = time() . '.' . $cvprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/CV';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($cvprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'CV';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            /////////////////////////Arabic CV////////////////////////
            if (Input::hasFile('cvarabic')) {
                $cvarabic = Input::file('cvarabic');
                $extension = time() . '.' . $cvarabic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ARABIC_CV';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($cvarabic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ARABIC_CV';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            /////////////////////////////JOB_OFFER/////////////////
            if (Input::hasFile('joboffprofilepic')) {
                $joboffprofilepic = Input::file('joboffprofilepic');
                $extension = time() . '.' . $joboffprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/JOB_OFFER';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($joboffprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'JOB_OFFER';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            ///////////////////////////PASSPORT//////////////////////////
            if (Input::hasFile('passprofilepic')) {
                $passprofilepic = Input::file('passprofilepic');
                $extension = time() . '.' . $passprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/PASSPORT';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($passprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'PASSPORT';
                $documentmodel->user_id = $employee_id;
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            /////////////////send welcome email//////////////////////

            $employee_details = DB::table('employees')
                            ->select('employees.*')
                            ->where(['id' => Session::get('employee_id')])->first();
            $username = $employee_details->username;
          $password= Session::get('loginkey');
          //  $password = $employee_details->mobile_number;
            $email = $employee_details->email;
            if ($email == NULL || $email == '') {
                $email = $employee_details->contact_email;
            }
            $employee_name = $employee_details->first_name . " " . $employee_details->middle_name . " " . $employee_details->last_name;
            Mail::send('emailtemplates.employee_reg', ['email' => $email, 'username' => $username, 'password' => $password, 'employee_name' => $employee_name,'web_url'=>$web_url], function($message)use ($email) {
                $message->to($email)->subject('Employee Registration');
            });
             Session::set('loginkey', "");
            Toastr::success('Employee Successfully Created!', $title = null, $options = []);
            return Redirect::to('employee/add');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('employee/add');
            
        }
    }

    public function update_upload_documents() {
        try {
            if (Session::get('employee_id')) {
                $employee_id = Session::get('employee_id');
                $employee_code = DB::table('employees')
                        ->select('employees.username','employees.profilepic')
                        ->where('id', $employee_id)
                        ->first();
            }
            $subfolder = $employee_code->username;
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            /////////////////////profile_pic//////////////////////
            if (Input::hasFile('profilepic')) {
                $profilepic = Input::file('profilepic');
                $extension = time() . '.' . $profilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/profile_pics/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($profilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
           
            } else {
                if ($employee_code->profilepic != "" && $employee_code->profilepic != null) {
                    $pic_url = $employee_code->profilepic;
                    
                } else {
                    $pic_url = \URL::asset('/images/imgAvatar.jpg');
                   
                }
            }
            $employee_data['profilepic'] = $pic_url;
            if (Session::get('employee_id')) {
                $employee_id = Session::get('employee_id');
                DB::table('employees')
                        ->where('id', $employee_id)
                        ->update($employee_data);
            }

            //////////////////////// identification card//////////////////
            if (Input::hasFile('indentpic')) {
                $indentpic = Input::file('indentpic');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ID_CARD';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'ID_CARD')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ID_CARD';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }

            ////////////////////// JOB PROFILE//////////////////////////


            if (Input::hasFile('jobprofilepic')) {
                $jobprofilepic = Input::file('jobprofilepic');
                $extension = time() . '.' . $jobprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/JOB_DESCRIPTION';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($jobprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'JOB_DESCRIPTION')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'JOB_DESCRIPTION';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }


            /////////////////////////contract////////////////////////////////

            if (Input::hasFile('conprofilepic')) {
                $conprofilepic = Input::file('conprofilepic');
                $extension = time() . '.' . $conprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/CONTRACT';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($conprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'CONTRACT')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'CONTRACT';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }

            /////////////////////////////////////////start_form////////////////////
            if (Input::hasFile('stprofilepic')) {
                $stprofilepic = Input::file('stprofilepic');
                $extension = time() . '.' . $stprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/START_FROM';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($stprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'START_FROM')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'START_FROM';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            //////////////////////////////////PERSONAL_PROFILE////////////////
            if (Input::hasFile('perprofilepic')) {
                $perprofilepic = Input::file('perprofilepic');
                $extension = time() . '.' . $perprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/PERSONAL_PROFILE';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($perprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'PERSONAL_PROFILE')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'PERSONAL_PROFILE';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            ///////////////////////////////QUALIFIACTION////////////////////////

            if (Input::hasFile('qprofilepic')) {
                $qprofilepic = Input::file('qprofilepic');
                $extension = time() . '.' . $qprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/QUALIFICATION';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($qprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'QUALIFICATION')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'QUALIFICATION';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            /////////////////////////cv////////////////////////
            if (Input::hasFile('cvprofilepic')) {
                $cvprofilepic = Input::file('cvprofilepic');
                $extension = time() . '.' . $cvprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/CV';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($cvprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'CV')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'CV';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            /////////////////////////Arabic cv////////////////////////
            if (Input::hasFile('cvarabic')) {
                $cvarabic = Input::file('cvarabic');
                $extension = time() . '.' . $cvarabic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ARABIC_CV';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($cvarabic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'ARABIC_CV')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ARABIC_CV';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            /////////////////////////////JOB_OFFER/////////////////
            if (Input::hasFile('joboffprofilepic')) {
                $joboffprofilepic = Input::file('joboffprofilepic');
                $extension = time() . '.' . $joboffprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/JOB_OFFER';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($joboffprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'JOB_OFFER')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'JOB_OFFER';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            ///////////////////////////PASSPORT//////////////////////////
            if (Input::hasFile('passprofilepic')) {
                $passprofilepic = Input::file('passprofilepic');
                $extension = time() . '.' . $passprofilepic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/PASSPORT';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($passprofilepic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                $id = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $employee_id)->where('document_type', 'PASSPORT')
                        ->first();
                if (count($id) > 0) {

                    DB::table('documents')
                            ->where('id', $id->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'PASSPORT';
                    $documentmodel->user_id = $employee_id;
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
          $empID=\Crypt::encrypt( $employee_id);
          
            /////////////////send welcome email//////////////////////
//        $employee_details = DB::table('employees')
//                        ->select('employees.*')
//                        ->where(['id' => Session::get('employee_id')])->first();
//        $username = $employee_details->username;
//        $password = $employee_details->mobile_number;
//        $email = $employee_details->email;
//        if ($email == NULL || $email == '') {
//            $email = $employee_details->contact_email;
//        }
//        $employee_name = $employee_details->first_name . " " . $employee_details->middle_name . " " . $employee_details->last_name;
//        Mail::send('emailtemplates.employee_reg', ['email' => $email, 'username' => $username, 'password' => $password, 'employee_name' => $employee_name], function($message)use ($email) {
//            $message->to($email)->subject('Employee Registration');
//        });
          
          Session::set('div_id', "step4");
            Toastr::success('Employee Successfully Updated!', $title = null, $options = []);
            return Redirect::to('employee/edit/'.$empID);
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('employee');
        }
    }

    public function editlist(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        
        $strUrl=url()->current();
        $job_id='';
        $country_id='';
        if($id){
            if (strpos($strUrl, 'employeewithcountry') !== false) {
                $country_id = \Crypt::decrypt($id);
            }else{
                $job_id = \Crypt::decrypt($id);
            }
        }
        
        
        $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0)
                ->when($job_id, function ($query) use ($job_id) {
                    return $query->where('employees.job_position', '=', $job_id);
                })
                ->when($country_id, function ($query) use ($country_id) {
                    return $query->where('employees.nationality', '=', $country_id);
                })
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
        $job_positions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $divisions = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where(['resource_type' => 'DIVISION', 'status' => 1])
                    ->orderby('name', 'ASC')
                    ->get();
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
            $searchbyemail = Input::get('searchbyemail');
            $job_position = Input::get('job_position');
            $division = Input::get('division');
            $country = Input::get('country');
            $status = Input::get('status');
            
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
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->where('employees.nationality', '=', $country);
                    })
                     ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("(employees.email like '$searchbyemail%')");
                    })
                     ->when($searchbyph, function ($query) use ($searchbyph) {
                         if($searchbyph==';'){ $searchbyph=0;}
                        return $query->whereRaw("(employees.mobile_number like '$searchbyph%')");
                    })
                     ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(employees.username like '$searchbycode%')");
                    })
                     ->when($division, function ($query) use ($division) {
                        return $query->where('employees.division', '=', $division);
                    }) 
                     ->when($status, function ($query) use ($status) {
                        return $query->where('employees.status', '=', $status);
                    })
                    ->orderby('employees.created_at', 'DESC')
                    ->paginate($paginate);
            return view('employee/editlistresults', array('employees' => $employees));
        }
        return view('employee/editlist', array('employees' => $employees, 'job_positions' => $job_positions, 'countries' => $countries,"job_id"=>$job_id,"divisions"=>$divisions,'country_id'=>$country_id));
    }

    
    public function profile() {
        $id =Session::get('login_id') ;
         
         $profileDetails = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name','country.flag_32 AS flag_pic','country.name AS flag_code','top_managers.first_name AS topmanager_first_name','top_managers.middle_name AS topmanager_middle_name','top_managers.last_name AS topmanager_last_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('employees as top_managers', 'top_managers.id', '=', 'employees.top_manager_id')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.id', '=', $id)
                ->first();
      // echo $profileDetails;
        return view('employee/profileview', array('profile_details' => $profileDetails));
   
      }
      
      public function updatepassword() {
        $id =Session::get('login_id') ;
         $password = Input::get('password');
        if($password!=""){
        $employee_data['password'] = Hash::make($password);
        }

        
          DB::table('employees')
                    ->where('id', $id)
                    ->update($employee_data);
        
   Toastr::success('Password Successfully Updated', $title = null, $options = []);
            return Redirect::to('employee/profile');
      }

                // Generate PDF funcion
    public function exportdata() {
        
         ini_set('memory_limit', '-1');
         ini_set('max_execution_time', 3000);
        
         $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
            $searchbyemail = Input::get('searchbyemail');
            $job_position = Input::get('job_position');
            $division = Input::get('division');
            $country = Input::get('country');
            $status = Input::get('status');
            $excelorpdf = Input::get('excelorpdf');
            
            
              $employees = DB::table('employees')
                    ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name','idprofessional.name as id_professional_name')
                    ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                    ->leftjoin('master_resources as idprofessional', 'idprofessional.id', '=', 'employees.id_professional')
                    ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                    ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                     ->where('employees.status', '!=', 2)
                    ->where('employees.admin_status', '=', 0)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
                    })
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->where('employees.nationality', '=', $country);
                    })
                    ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("(employees.email like '$searchbyemail%')");
                    })
                    ->when($searchbyph, function ($query) use ($searchbyph) {
                        if ($searchbyph == ';') {
                            $searchbyph = 0;
                        }
                        return $query->whereRaw("(employees.mobile_number like '$searchbyph%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(employees.username like '$searchbycode%')");
                    })
                    ->when($division, function ($query) use ($division) {
                        return $query->where('employees.division', '=', $division);
                    })
                    ->when($status, function ($query) use ($status) {
                        return $query->where('employees.status', '=', $status);
                    })
                    ->orderby('employees.created_at', 'DESC')
                    ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('Payroll', function($excel) use($employees){
                 // Set the title
                $excel->setTitle('Payroll List');
                
                $excel->sheet('Payroll List', function($sheet) use($employees){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('D3', 'Payroll List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:P3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Employee Code','Employee Name','Gossi Number','System Position',"ID Profession" ,"Country","Email Id","Phone No","Division","Basic Salary","Housing  Allowance","Transportation Allowance","Food Allowance","Other Expense","Status"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:P5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $employee_name="";
                    for($i=0;$i<count($employees);$i++){
                        $employee_name =  $employees[$i]->first_name." ". $employees[$i]->alias_name;
                        if( $employees[$i]->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $employees[$i]->username);
                        $sheet->setCellValue('C'.$chrRow, $employee_name);  
                        $sheet->setCellValue('D'.$chrRow, $employees[$i]->gossi_number);
                        $sheet->setCellValue('E'.$chrRow, str_replace('_',' ',$employees[$i]->job_position_name));
                        $sheet->setCellValue('F'.$chrRow, str_replace('_',' ',$employees[$i]->id_professional_name));
                        $sheet->setCellValue('G'.$chrRow, $employees[$i]->country_name);
                        $sheet->setCellValue('H'.$chrRow, $employees[$i]->email);
                        $sheet->setCellValue('I'.$chrRow, $employees[$i]->mobile_number);
                        $sheet->setCellValue('J'.$chrRow, $employees[$i]->division_name);
                        $sheet->setCellValue('K'.$chrRow, $employees[$i]->basic_salary);
                        $sheet->setCellValue('L'.$chrRow, $employees[$i]->housing_allowance);
                        $sheet->setCellValue('M'.$chrRow, $employees[$i]->transportation_allowance);
                        $sheet->setCellValue('N'.$chrRow, $employees[$i]->food_allowance);
                        $sheet->setCellValue('O'.$chrRow, $employees[$i]->other_expense);
                        $sheet->setCellValue('P'.$chrRow, $status);
                            
                        $sheet->cells('A'.$chrRow.':P'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        
                        $chrRow++;
                    }

                });
                
            })->export('xls');
            
        } else{

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Payroll List</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Gossi Number</td>
                                <td style="padding:10px 5px;color:#fff;"> System Position</td>
                                <td style="padding:10px 5px;color:#fff;"> ID Profession</td>
                                <td style="padding:10px 5px;color:#fff;"> Country </td>
                                <td style="padding:10px 5px;color:#fff;"> Email Id </td>
                                <td style="padding:10px 5px;color:#fff;"> Phone No </td>
                                <td style="padding:10px 5px;color:#fff;"> Division </td>
                                <td style="padding:10px 5px;color:#fff;"> Basic Salary </td>
                                <td style="padding:10px 5px;color:#fff;"> Housing Allowance </td>
                                <td style="padding:10px 5px;color:#fff;"> Transportation Allowance </td>
                                <td style="padding:10px 5px;color:#fff;"> Food Allowance </td>
                                <td style="padding:10px 5px;color:#fff;"> Other Expense </td>
                                <td style="padding:10px 5px;color:#fff;"> Division </td>
                                
                                <td style="padding:10px 5px;color:#fff;"> Status </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno=0;
            foreach ($employees as $employee) {
                 $employee_name = $employee->first_name." ". $employee->alias_name;
                        if($employee->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->username . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' .  $employee->gossi_number  . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . str_replace('_',' ',$employee->job_position_name) . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . str_replace('_',' ',$employee->id_professional_name) . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->country_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->email . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->mobile_number . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->division_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->basic_salary . '</td>
                                   <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->housing_allowance . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->transportation_allowance . '</td>
                                   <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->food_allowance . '</td>
                                   <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->other_expense . '</td>
                                   <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $status . '</td>
                               </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('payroll_list.pdf');
        }
    }   
       public function get_topmanager() {

        $term = Input::get('term');
        $data = array();
        if($term == ''){
            $data = array();
        }else{
            $result = DB::table('employees')
                    ->select('*')
                    ->where('status', '=', '1')
                    ->where('username', 'LIKE', $term . '%')
                    ->take(10)
                    ->get();

            foreach ($result as $result) {
                $data[] = array('name' => $result->first_name . ' (' . $result->username.')', 'id' => $result->id);
            }
//            if (!count($data)) {
//                $data[] = array('name' => 'No Result Found', 'id' => '');
//            }
        }
        return json_encode($data);
    }
    
    public function deletelist(Request $request, $id = ''){
             $paginate = Config::get('app.PAGINATE');

        $strUrl = url()->current();
        $job_id = '';
        $country_id = '';
        if ($id) {
            if (strpos($strUrl, 'employeewithcountry') !== false) {
                $country_id = \Crypt::decrypt($id);
            } else {
                $job_id = \Crypt::decrypt($id);
            }
        }


        $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0)
                ->when($job_id, function ($query) use ($job_id) {
                    return $query->where('employees.job_position', '=', $job_id);
                })
                ->when($country_id, function ($query) use ($country_id) {
                    return $query->where('employees.nationality', '=', $country_id);
                })
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
        $job_positions = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                        ->orderby('name', 'ASC')->get();
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();

        $divisions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'DIVISION', 'status' => 1])
                ->orderby('name', 'ASC')
                ->get();

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
            $searchbyemail = Input::get('searchbyemail');
            $job_position = Input::get('job_position');
            $division = Input::get('division');
            $country = Input::get('country');
            $status = Input::get('status');

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
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->where('employees.nationality', '=', $country);
                    })
                    ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("(employees.email like '$searchbyemail%')");
                    })
                    ->when($searchbyph, function ($query) use ($searchbyph) {
                        if ($searchbyph == ';') {
                            $searchbyph = 0;
                        }
                        return $query->whereRaw("(employees.mobile_number like '$searchbyph%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(employees.username like '$searchbycode%')");
                    })
                    ->when($division, function ($query) use ($division) {
                        return $query->where('employees.division', '=', $division);
                    })
                    ->when($status, function ($query) use ($status) {
                        return $query->where('employees.status', '=', $status);
                    })
                    ->orderby('employees.created_at', 'DESC')
                    ->paginate($paginate);
            return view('employee/deleteresults', array('employees' => $employees));
        }
        return view('employee/deletelist', array('employees' => $employees, 'job_positions' => $job_positions, 'countries' => $countries, "job_id" => $job_id, "divisions" => $divisions, 'country_id' => $country_id));
    }
   
    
     public function checkgossino() {
        $gs_no = Input::get('gossi_number');
        if (Session::get('employee_id')) {
            $employee_id = Session::get('employee_id');
            $employee_data = DB::table('employees')
                            ->select('employees.gossi_number')
                            ->where('gossi_number', '=', $gs_no)
                          //  ->where('status', '!=', 2)
                            ->where('id', '!=', $employee_id)
                            ->get();
           
        } else {
            $employee_code = Input::get('employee_code');
            $employee_data = DB::table('employees')
                            ->select('employees.gossi_number')
                            //->where('status', '!=', 2)
                            ->where(['gossi_number' => $gs_no])
                            ->get();
        }
        if (count($employee_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }
    
    
      public function get_idprofessional() {

        $term = Input::get('term');
        $data = array();
        if($term == ''){
            $data = array();
        }else{
            $result = DB::table('master_resources')
                    ->select('id','name','alias_name')
                    ->where('status', '=', '1')
                    ->where('name', 'LIKE', $term . '%')
                    ->where('resource_type', '=', 'ID_PROFESSION')
                    ->get();

            foreach ($result as $result) {
                $data[] = array('name' => $result->name.' '.$result->alias_name, 'id' => $result->id);
            }
//            if (!count($data)) {
//                $data[] = array('name' => 'No Result Found', 'id' => '');
//            }
        }
        return json_encode($data);
    }
    
}
