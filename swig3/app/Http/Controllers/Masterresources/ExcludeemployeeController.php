<?php

namespace App\Http\Controllers\Masterresources;

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
use App\Models\Exclude_employee;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Company;
use DB;
use App;
use PDF;
use Excel;

class ExcludeemployeeController extends Controller {

    public function index(Request $request,$id='') {
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
         $exclude_emp = DB::table('excepted_employees_list')
                ->select('employee_id') 
                 ->whereRaw('status=1')
                ->orderby('created_at', 'DESC')
                ->get();
         $excepted_id="";
         $excepted_ids=array();
        foreach($exclude_emp as $excluded_ids) {
            $excepted_ids[]=$excluded_ids->employee_id;
        }
        //if(count($excepted_ids)>0){
     $excepted_id=implode(",",$excepted_ids);
       // }
        
        $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '!=', 1)
                ->when($excepted_id, function ($query) use ($excepted_id) {
                    return $query->whereRaw("employees.id NOT IN ($excepted_id)");
                }) 
                 ->when($job_id, function ($query) use ($job_id) {
                    return $query->where('employees.job_position', '=', $job_id);
                })
                ->when($country_id, function ($query) use ($country_id) {
                    return $query->where('employees.nationality', '=', $country_id);
                })
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
                    //    ->toSql();
               // echo $employees;
    // die();
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
            $employees = DB::table('employees')
                     ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '!=', 1)
                 ->when($excepted_id, function ($query) use ($excepted_id) {
                    return $query->whereRaw("employees.id NOT IN ($excepted_id)");
                }) 
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
                    ->orderby('employees.created_at', 'DESC')
                    ->paginate($paginate);
            return view('masterresources/exclude_employees/searchresults', array('employees' => $employees));
        }
        return view('masterresources/exclude_employees/index', array('employees' => $employees, 'job_positions' => $job_positions, 'countries' => $countries,"job_id"=>$job_id,"divisions"=>$divisions,'country_id'=>$country_id));
    }
    
  
    public function exclude_employees() {
        $selected_pos = Input::get('selected_pos');
        $idarray= Input::get('selected_pos');//idarray is string
       $selected_ids= json_decode($selected_pos, true);
      
       
       
       $selectedidarray= Input::get('selected_ids');
       $selected_ids_final= json_decode($selectedidarray, true);
       $result = array_diff($selected_ids, $selected_ids_final);
      $resultString=implode(",",$result);
     
     //  $login_id = Session::get('login_id');
     //   $created_at = date("Y-m-d H:i:s");
     //  $updated_at =  date("Y-m-d H:i:s");;
     //  $status = Input::get('status');
        
       
      /*   foreach ($selected_ids as $sel_id) {
           
        $excepetedmodel = new Exclude_employee();
        $excepetedmodel->employee_id = $sel_id;
        $excepetedmodel->report_type = "kpi_report";
        $excepetedmodel->status = $status;
        $excepetedmodel->created_at = $created_at;
        $excepetedmodel->updated_at = $updated_at;
        $excepetedmodel->save();
     Toastr::success('Successfully Excluded !', $title = null, $options = []);
     //  return Redirect::to('masterresources/exclude_employees/index');
        }*/
        $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
           $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0) 
                 ->whereRaw("employees.id IN ($resultString)")
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
        
     
      
        echo '<div class="listHolderType1"><div class="listerType1">
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                             <td>Employee Code</td>
                                <td>Employee Name</td>
                                <td>Job Position</td>
                                <td>Division</td>
                                <td>Action</td>
                                
                            </tr>
                        </thead>
                        <div>
                        <tbody>';
        $total_cash = 0;
        foreach ($employees as $emp_details) {
           
           $employee_name = $emp_details->first_name." ".$emp_details->alias_name;
            echo '<td>' . $emp_details->username . '</td>';
            echo '<td>' . $employee_name . '</td>';
            echo '<td>' . str_replace('_',' ',$emp_details->job_position_name). '</td>';
            echo '<td>' . $emp_details->division_name . '</td>';
           
            echo '<td><a class="remove_emp_id" id="remove_emp_id" href="javascript:void(0);" value="'.$emp_details->id.'">Remove</a>
                  </td>';
            echo'</tr> <input type="hidden" name="emp_ids[]" value="'.$emp_details->id.'">';
        }
        echo '</tbody></table><div class="commonLoaderV1"></div></div>					
            </div>';
      //  echo $str2;
    }
    
     public function remove_employees() {
        $selected_pos = Input::get('selected_pos');
        $idarray= Input::get('selected_pos');//idarray is string
       $selected_ids= json_decode($selected_pos, true);
      
       
       
       $selectedidarray= Input::get('selected_ids');
      
       $selected_ids_final= json_decode($selectedidarray, true);
       
      $result1 = array_diff($selected_ids, $selected_ids_final);
      $result2 = array_diff($selected_ids_final,$selected_ids);
       $result = array_merge($result1,$result2);
  
      $resultString=implode(",",$result);
      if($resultString!=""){
     
        $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
           $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0) 
                   ->when($resultString, function ($query) use ($resultString) {
                    return $query->whereRaw("employees.id IN ($resultString)");
                })
              //   ->whereRaw("employees.id IN ($resultString)")
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
        
     
      
        echo '<div class="listHolderType1"><div class="listerType1">
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                             <td>Employee Code</td>
                                <td>Employee Name</td>
                                <td>Job Position</td>
                                <td>Division</td>
                                <td>Action</td>
                                
                            </tr>
                        </thead>
                        <div>
                        <tbody>';
        $total_cash = 0;
        foreach ($employees as $emp_details) {
           
           $employee_name = $emp_details->first_name." ".$emp_details->alias_name;
            echo '<td>' . $emp_details->username . '</td>';
            echo '<td>' . $employee_name . '</td>';
            echo '<td>' . str_replace('_',' ',$emp_details->job_position_name). '</td>';
            echo '<td>' . $emp_details->division_name . '</td>';
           
            echo '<td><a class="remove_emp_id" id="remove_emp_id" href="javascript:void(0);" value="'.$emp_details->id.'">Remove</a>
                  </td>';
            echo'</tr> <input type="hidden" name="emp_ids[]" value="'.$emp_details->id.'">';
        }
        echo '</tbody></table><div class="commonLoaderV1"></div></div>					
            </div>';
      //  echo $str2;
      }else{
          
      }
    }

     public function notexcludedids() {
        $selected_pos = Input::get('selected_pos');
        $idarray= Input::get('selected_pos');
       $selected_ids= json_decode($selected_pos, true);
      
     $str1 = str_replace("[", "",$idarray); 
      $str2 = str_replace("]", "",$str1); 
        $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
           $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0) 
                 ->whereRaw("employees.id NOT IN ($str2)")
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
        
      // return view('masterresources/exclude_employees/searchresults', array('employee' => $employees));
  
           $n = $employees->perPage() * ($employees->currentPage()-1);  
      
                    foreach ($employees as $employee){
                   $n++; 
                     echo '<tr> ';
                    $employee_name = $employee->first_name." ".$employee->alias_name;
                    

              
                   echo  '<td class="alignCenter"><input type="checkbox" class="alltSelected" name="selected_pos[]" value="'.$employee->id;
                   echo '" id="selected_pos[]"> </td> ';
                   echo '<td>'.$employee->username;
                   echo '</td>';
                   echo '<td>'.$employee_name;
                   echo '</td>';
                   echo '<td>'.str_replace('_',' ',$employee->job_position_name); 
                   echo '</td>';
                   echo '<td>'.$employee->division_name;
                   echo '</td>';
                   echo '</tr>';
                   
                     }
                  
   echo  '<tr class="paginationHolder"><th><div> '.$employees->render() ;
   echo ' </div> </th></tr>';
                   
       
           
           
            }
    
    
            
             public function notexcludedlist() {
                 
        $selected_pos = Input::get('selected_pos');
        $idarray= Input::get('selected_pos');//idarray is string
       $selected_ids= json_decode($selected_pos, true);
      
       
       
       $selectedidarray= Input::get('selected_ids');
      
       $selected_ids_final= json_decode($selectedidarray, true);
       
      $result1 = array_diff($selected_ids, $selected_ids_final);
      $result2 = array_diff($selected_ids_final,$selected_ids);
       $result = array_merge($result1,$result2);
  
      $resultString=implode(",",$result);
     
        $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
           $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->where('employees.admin_status', '=', 0) 
                 ->whereRaw("employees.id NOT IN ($resultString)")
                ->orderby('employees.created_at', 'DESC')
                ->paginate($paginate);
        
      // return view('masterresources/exclude_employees/searchresults', array('employee' => $employees));
  
           $n = $employees->perPage() * ($employees->currentPage()-1);  
      
                    foreach ($employees as $employee){
                   $n++; 
                     echo '<tr> ';
                    $employee_name = $employee->first_name." ".$employee->alias_name;
                    

              
                   echo  '<td class="alignCenter"><input type="checkbox" class="alltSelected" name="selected_pos[]" value="'.$employee->id;
                   echo '" id="selected_pos[]"> </td> ';
                   echo '<td>'.$employee->username;
                   echo '</td>';
                   echo '<td>'.$employee_name;
                   echo '</td>';
                   echo '<td>'.str_replace('_',' ',$employee->job_position_name); 
                   echo '</td>';
                   echo '<td>'.$employee->division_name;
                   echo '</td>';
                   echo '</tr>';
                   
                     }
                  
   echo  '<tr class="paginationHolder"><th><div> '.$employees->render() ;
   echo ' </div> </th></tr>';
                   
       
           
           
            }
    
    
    
    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $companies = Company::all();
        $banks = DB::table('master_resources')
                        ->join('companies', 'master_resources.company_id', '=', 'companies.id')
                        ->select('master_resources.*', 'companies.name as company_name')
                        ->where(['resource_type' => 'BANK'])->where('master_resources.status', '=', 1)->get();


        return view('masterresources/bank/add', array('companies' => $companies, 'banks' => $banks));
    }

    public function store() {

        $data = Input::all();
         $created_at = date("Y-m-d H:i:s");
       $updated_at =  date("Y-m-d H:i:s");;
       $status = 1;
        if (isset($data['emp_ids'])) {
            
            foreach ($data['emp_ids'] as $sel_id) {
           
        $excepetedmodel = new Exclude_employee();
        $excepetedmodel->employee_id = $sel_id;
        $excepetedmodel->report_type = "kpi_report";
        $excepetedmodel->status = $status;
        $excepetedmodel->created_at = $created_at;
        $excepetedmodel->updated_at = $updated_at;
        $excepetedmodel->save();
       }
            
         Toastr::success('Successfully Excluded !', $title = null, $options = []);
     return Redirect::to('masterresources/excepted');
         
            }
    }

    public function checkbankname() {

        $name = Input::get('name');
        $bank_id = Input::get('bank_id');
        $data = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('name', '=', $name)->where('resource_type', '=', 'BANK')->where('id', '!=', $bank_id)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $dn = \Crypt::decrypt($id);
        $companies = Company::all();
        $bank = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();
        return view('masterresources/bank/edit', array('bank' => $bank, 'company_id' => $company_id));
    }

    public function update() {

        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $bank_id = Input::get('bank_id');
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');

            $banks = DB::table('master_resources')
                    ->where(['id' => $bank_id])
                    ->update(['name' => $name, 'alias_name' => $alias_name]);
            Toastr::success('Bank Successfully Updated!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank/edit/' . $bank_id);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Bank Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Bank Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Bank Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/bank');
        }
    }

       // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $companyid = Session::get('company');  
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $sortordname = Input::get('sortordname');
        if($sortordname==''){
            $sortordname=1;
        }

        $banks = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'BANK')
                ->where('status', '!=', 2)
                ->where('company_id', '=', $companyid)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    if($sortordname!=1){
                        return $query->orderby('master_resources.name', $sortordname);
                    }else{
                        return $query->orderby('master_resources.name', 'ASC');
                    }
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('BankList', function($excel) use($banks){
                 // Set the title
                $excel->setTitle('Bank List');
                
                $excel->sheet('Banks List', function($sheet) use($banks){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Banks List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:C3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Bank Name',"Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:C5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($banks);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $banks[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $banks[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':C'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
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
                <title>Project Name</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:Arial;">
                <section id="container">
                <div style="text-align:center;"><h1>Banks List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Bank Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="regionbody" id="regionbody" >';
            $slno=0;
            foreach ($banks as $bank) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $bank->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $bank->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_bank_list.pdf');
        }
    }
}
