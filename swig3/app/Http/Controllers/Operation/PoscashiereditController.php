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
use App\Models\Usermodule;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Pos_sale;
use DB;
use Mail;
use App\Models\Resource_allocation;

class PoscashiereditController extends Controller {

    public function index() {

        $branches = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'BRANCH'])
                        ->where('status', '=', 1)->get();
        $job_shifts = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'JOB_SHIFT'])->where('status', '=', 1)->get();


        $shift_names = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                ->where('master_resources.status', '=', 1)
                ->get();
        return view('operation/pos_cashier_edit/index', array('shift_names' => $shift_names, 'branches' => $branches));
    }

    public function show_shifts() {
        $branch_id = Input::get('branch_id');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        // $company_id = Input::get('company_id');
        $shifts = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id', 'master_resources.shift_id')
                ->where(['resource_type' => 'BRANCH', 'id' => $branch_id, 'status' => 1])
                ->first();
        // print_r($shifts->shift_id);print_r('ss');
        $arrays = explode(',', $shifts->shift_id);
        $n = 0;
        // echo '<label>Select Job Shift</label>
        //        <select id="shiftid">';
        echo '<option value="">Select Shift</option>';
        foreach ($arrays as $array) {
            $n++;
            $shift_names = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'JOB_SHIFT')
                    ->where('master_resources.status', '=', 1)
                    ->where('master_resources.id', '=', $array)
                    ->first();
            if (count($shift_names) != 0) {
                echo '<option value="' . $array . '">' . $shift_names->name . '</option>';
            }
            //echo '<li class="shift_name" id="' . $shift_names->name . '" value="' . $array . '"><a href="javascript:void(0)" rel="#tabS' . $n . '"  >' . $shift_names->name . '</a></li>';
        }
    }

    
    
    

 public function store() {
        try {



            $current_date = date('Y-m-d');
            $posmodel = new Pos_sale;
            $posmodel->company_id = Session::get('company');
            $posmodel->branch_id = Input::get('branch_editid');
            $posmodel->job_shift_id = Input::get('job_shiftid');

            //   $posmodel->cash_collection = Input::get('cash_collection');
            $branch_id = Input::get('branch_editid');
            $job_shift = Input::get('job_shiftid');
            $pos_date = Input::get('edit_date');
            $get_pos_date = Input::get('edit_date');

            $cashier_id = Input::get('cashier_id');
            $cashier = Input::get('cashier');
            $employee_id = Input::get('supervisor_id');
            $employer = Input::get('supervisor');


 
            if ($cashier_id == '' && $cashier == '' ) {



                /*  $pos_date = explode('-',$pos_date);
                  $pos_date = $pos_date[2].'-'.$pos_date[1].'-'.$pos_date[0];
                  $posmodel->pos_date = $pos_date ;
                  $pos_details = DB::table('pos_sales')
                  ->select('pos_sales.*')
                  ->whereRaw("branch_id=$branch_id and job_shift_id=$job_shift and pos_date like '$pos_date%' and added_by_user_type='Cashier' and status=1")
                  ->first();


                  if (count($pos_details) > 0) {

                  $edit_details = DB::table('pos_sales')
                  ->select('pos_sales.*','employees.id as emp_id','employees.first_name','employees.alias_name','employees.last_name','employees.profilepic', 'branch_details.id as branch_id','branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund', 'shift_details.name as shift_name','shift_details.id as job_shift_id','country.name as country_name', 'country.flag_128 as flag_name')
                  ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                  ->join('master_resources as shift_details', 'shift_details.id', '=', 'pos_sales.job_shift_id')
                  ->join('employees', 'employees.id', '=', 'pos_sales.cashier_id')
                  ->join('country', 'country.id', '=', 'employees.nationality')
                  ->whereRaw("pos_sales.branch_id = $branch_id and pos_sales.job_shift_id=$job_shift and  pos_date like '$pos_date%' and added_by_user_type='Cashier' and pos_sales.status=1")
                  ->first();

                  if (count($edit_details) > 0) {

                  $cash_collection=$edit_details->cash_collection;
                  $parent_id=$edit_details->id;

                  Toastr::error("Pos Sale already added", $title = null, $options = []);
                  return view('operation/pos_cashier_edit/add',array('branch_details'=>$edit_details,'pos_date'=>$get_pos_date,'cash_collection'=>$cash_collection,'parent_id'=>$parent_id));

                  }else{


                  Toastr::error('No Allocation Exist !', $title = null, $options = []);
                  return Redirect::to('operation/pos_cashier_edit');

                  }

                  }else{
                  Toastr::error('No Allocation Exist !', $title = null, $options = []);
                  return Redirect::to('operation/pos_cashier_edit');

                  } */
                Toastr::error('No cashier is allocated !', $title = null, $options = []);
                return Redirect::to('operation/pos_cashier_edit');
            }
            
               else if ($cashier_id != '' || $cashier != '') {
                   
                  if( $employee_id!="" || $employer!=""){
                      
                      
                      
                if ($cashier_id != "") {
                    $cashier = $cashier_id;
                }

                 if ($employee_id != "") {
                    $employer = $employee_id;
                }
                
                $pos_date = explode('-', $pos_date);
                $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];
                $posmodel->pos_date = $pos_date;
                $pos_details = DB::table('pos_sales')
                        ->select('pos_sales.*')
                        ->whereRaw("branch_id=$branch_id and job_shift_id=$job_shift and pos_date like '$pos_date%' and added_by_user_type='Cashier' and status=1 and pos_sales.cashier_id=$cashier")
                        ->first();
     
                if (count($pos_details) > 0) {

                    $edit_details = DB::table('pos_sales')
                            ->select('pos_sales.*', 'employees.id as emp_id', 'employees.first_name', 'employees.alias_name', 'employees.last_name', 'employees.profilepic', 'branch_details.id as branch_id', 'branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund','branch_details.opening_fund_editable as opening_fund_editable', 'shift_details.name as shift_name', 'shift_details.id as job_shift_id', 'country.name as country_name', 'country.flag_128 as flag_name')
                            ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                            ->join('master_resources as shift_details', 'shift_details.id', '=', 'pos_sales.job_shift_id')
                            ->join('employees', 'employees.id', '=', 'pos_sales.cashier_id')
                            ->join('country', 'country.id', '=', 'employees.nationality')
                            ->whereRaw("pos_sales.branch_id = $branch_id and pos_sales.job_shift_id=$job_shift and  pos_date like '$pos_date%' and added_by_user_type='Cashier' and pos_sales.status=1 and pos_sales.cashier_id=$cashier")
                            ->first();

                    if (count($edit_details) > 0) {
                        
                        if(count($pos_details) > 0){
                            $supervisor_id=$pos_details->employee_id;
                            
                             $cash_collection = $edit_details->cash_collection;
                        $tip_collection = $edit_details->tips_collected;
                        $parent_id = $edit_details->id;
                        $sales_amount=$edit_details->cash_collection;
                        $tax_amount=$edit_details->tax_in_mis;
                        $bankCard=$edit_details->bank_sale;
                        $bankCollection=$edit_details->bank_collection;

                        Toastr::error("Pos Sale already added", $title = null, $options = []);
                        return view('operation/pos_cashier_edit/add', array('branch_details' => $edit_details, 'pos_date' => $get_pos_date, 'cash_collection' => $cash_collection, 'parent_id' => $parent_id, 'tip_collection' => $tip_collection,'supervisor'=>$supervisor_id,'tax_amount'=>$tax_amount,'sales_amount'=>$sales_amount,'bankCard'=>$bankCard,'bankCollection'=>$bankCollection));
                    
                        }else{
                            
                            Toastr::error('No Allocation Exist !', $title = null, $options = []);
                        return Redirect::to('operation/pos_cashier_edit');
                  }

                       } else {


                        Toastr::error('No Allocation Exist !', $title = null, $options = []);
                        return Redirect::to('operation/pos_cashier_edit');
                    }
                } else {

                    $branch_details = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'employees.id as emp_id', 'employees.first_name', 'employees.alias_name', 'employees.last_name', 'employees.profilepic', 'branch_details.id as branch_id', 'branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund','branch_details.opening_fund_editable as opening_fund_editable', 'shift_details.name as shift_name', 'shift_details.id as job_shift_id', 'country.name as country_name', 'country.flag_128 as flag_name')
                            ->join('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->join('master_resources as shift_details', 'shift_details.id', '=', 'resource_allocation.shift_id')
                            ->join('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                            ->join('country', 'country.id', '=', 'employees.nationality')
                            ->whereRaw("resource_allocation.resource_type = 'CASHIER' and resource_allocation.branch_id = $branch_id and resource_allocation.shift_id=$job_shift  and  '$pos_date' BETWEEN from_date and to_date and resource_allocation.employee_id=$cashier")
                            ->first();


                     $supervisor = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'employees.id as supervisor_id', 'employees.first_name', 'employees.alias_name', 'employees.last_name', 'employees.profilepic', 'branch_details.id as branch_id', 'branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund', 'country.name as country_name', 'country.flag_128 as flag_name')
                            ->join('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                           ->join('employees', 'employees.id', '=', 'resource_allocation.employee_id')
                            ->join('country', 'country.id', '=', 'employees.nationality')
                            ->whereRaw("resource_allocation.resource_type = 'SUPERVISOR' and resource_allocation.branch_id = $branch_id   and  '$pos_date' BETWEEN from_date and to_date and resource_allocation.employee_id=$employer")
                            ->first();

                    
                    if (count($branch_details) > 0) {
                        
                        if(count($supervisor)>0){
                           
                            $supervisor_id=$supervisor->supervisor_id;
                        Toastr::error('No Entry Found.Please add a new entry!', $title = null, $options = []);
                        return view('operation/pos_cashier_edit/add', array('branch_details' => $branch_details, 'pos_date' => $get_pos_date, 'cash_collection' => '', 'parent_id' => '', 'tip_collection' => '','supervisor'=>$supervisor_id,'tax_amount'=>'','sales_amount'=>'','bankCard'=>'','bankCollection'=>''));
                   
                        }else{

                      //  Toastr::error('No entry Found.Please add a new entry!', $title = null, $options = []);
                     //   return view('operation/pos_cashier_edit/add', array('branch_details' => $branch_details, 'pos_date' => $get_pos_date, 'cash_collection' => '', 'parent_id' => '', 'tip_collection' => '',));
                      
                            
                          Toastr::error('No Allocation Exist !', $title = null, $options = []);
                        return Redirect::to('operation/pos_cashier_edit');
                  
                            
                            
                        }  
                        
                        } else {

                        Toastr::error('No Allocation Exist !', $title = null, $options = []);
                        return Redirect::to('operation/pos_cashier_edit');
                    }
                }
                      
                      
                  }
                   
                    else {
                Toastr::error('No supervisor is allocated for the branch !', $title = null, $options = []);
                return Redirect::to('operation/pos_cashier_edit');
                  }
                   
            }
            
            
          
        } catch (\Exception $e) {
          //  die($e);
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('operation/pos_cashier_edit');
        }
    }

    public function save() {
        try {
            $current_date = date('Y-m-d');
            $posmodel = new Pos_sale;
            $posmodel->company_id = Session::get('company');
            $posmodel->branch_id = Input::get('branch_id');
            $posmodel->job_shift_id = Input::get('job_shift');


            
            $posmodel->tips_collected = Input::get('tips_collection');
            $branch_id = Input::get('branch_id');
            $job_shift = Input::get('job_shift');
            $pos_date = Input::get('pos_date');
            $pos_date = explode('-', $pos_date);
            $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];
            $posmodel->pos_date = $pos_date;
          //  $posmodel->employee_id = Session::get('login_id');
            $posmodel->parent_id = Input::get('parent_id');
           
          //  $posmodel->cashier_id = Input::get('employee_id');
          $posmodel->employee_id = Input::get('employee_id');
            $posmodel->cashier_id = Input::get('cashier_id');

           // $sales_amount=Input::get('sales_amount');
           // $posmodel->cash_collection = str_replace(",","",$sales_amount);
           $posmodel->cash_collection = Input::get('cash_collection');
           
            
            $tax_amount=Input::get('tax_amount');
            $posmodel->tax_in_mis = str_replace(",","",$tax_amount);
            
            $posmodel->added_by_user_type = 'Cashier';
            //$posmodel->pos_date = $pos_date ;
            $posmodel->edited_by = Session::get('login_id');



            $posmodel->opening_amount = Input::get('open_amount');
            //$posmodel->added_by_user_type = Input::get('added_by_user_type');
            $posmodel->bank_sale = Input::get('bank_card_submission');

                $posmodel->bank_collection = Input::get('bank_card_collection');

 if ($posmodel->parent_id != "") {
                    $pos_data['status'] = 0;
                    DB::table('pos_sales')
                            ->where('parent_id', $posmodel->parent_id)
                            ->update($pos_data);
                     $saved = $posmodel->save();
                }else{
                    $saved = $posmodel->save(); 
                }

           
            if ($saved) {
                if ($posmodel->parent_id != "") {
                    $pos_data['status'] = 0;
                    DB::table('pos_sales')
                            ->where('id', $posmodel->parent_id)
                            ->update($pos_data);
                }
            }
            Toastr::success('Successfully Added!', $title = null, $options = []);
            return Redirect::to('operation/pos_cashier_edit');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('operation/pos_cashier_edit');
        }
    }

    public function show_cashiers() {

        $branch_id = Input::get('branch_id');
        $job_shift = Input::get('shift_id');
        $pos_date = Input::get('edit_date');

        $pos_date = explode('-', $pos_date);
        $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];

        $pos_details = DB::table('pos_sales')
                ->select('pos_sales.*')
                ->whereRaw("branch_id=$branch_id and job_shift_id=$job_shift and pos_date like '$pos_date%' and added_by_user_type='Cashier' and status=1")
                ->first();

        if (count($pos_details) > 0) {


            $cashier_id = $pos_details->cashier_id;
            $select_status = 0;
            $options = 0;


            return \Response::json(array('cashier_id' => $cashier_id, 'select_status' => $select_status, 'options' => $options));
        } else {

            $cashiers = DB::table('employees')
                    ->select('employees.id as emp_id', 'employees.first_name', 'employees.alias_name', 'employees.last_name')
                    ->join('resource_allocation', 'resource_allocation.employee_id', '=', 'employees.id')
                    ->whereRaw("resource_allocation.resource_type = 'CASHIER' and resource_allocation.branch_id = $branch_id and resource_allocation.shift_id=$job_shift  and  '$pos_date' BETWEEN from_date and to_date")
                    ->groupBy('employees.id')
                    ->get();
            if (count($cashiers) > 0) {

                if (count($cashiers) == 1) {
                    foreach ($cashiers as $cashier) {
                        $cashier_id = $cashier->emp_id;
                        $select_status = 0;
                        $options = 0;

                        return \Response::json(array('cashier_id' => $cashier_id, 'select_status' => $select_status, 'options' => $options));
                    }
                } else {
                    $select_status = 1;
                    $options = '<option value="">Choose Cashier</option>';
                    foreach ($cashiers as $cashier) {

                        $options.='<option value="' . $cashier->emp_id . '">' . $cashier->first_name . ' ' . $cashier->alias_name . '</option>';
                    }
                    return \Response::json(array('cashier_id' => "", 'select_status' => $select_status, 'options' => $options));
                }
            } else {
                return \Response::json(array('cashier_id' => 0, 'select_status' => 0, 'options' => 0));
            }
        }
    }
    
    
 public function show_supervisors() {

        $branch_id = Input::get('branch_id');
        $job_shift = Input::get('shift_id');
        $pos_date = Input::get('edit_date');


        $pos_date = explode('-', $pos_date);
        $pos_date = $pos_date[2] . '-' . $pos_date[1] . '-' . $pos_date[0];


        $pos_details = DB::table('pos_sales')
                ->select('pos_sales.*')
                ->whereRaw("branch_id=$branch_id and job_shift_id=$job_shift and pos_date like '$pos_date%' and added_by_user_type='cashier' and status=1")
                ->first();
        if (count($pos_details) > 0) {


            $cashier_id = $pos_details->employee_id;
            $select_status = 0;
            $options = 0;

            return \Response::json(array('cashier_id' => $cashier_id, 'select_status' => $select_status, 'options' => $options));
        } else {
            
           

            $supervisors = DB::table('employees')
                    ->select('employees.id as emp_id', 'employees.first_name', 'employees.alias_name', 'employees.last_name')
                    ->join('resource_allocation', 'resource_allocation.employee_id', '=', 'employees.id')
                    ->whereRaw("resource_allocation.resource_type = 'SUPERVISOR' and resource_allocation.branch_id = $branch_id   and  '$pos_date' BETWEEN from_date and to_date")
                    ->groupBy('employees.id')
                    ->get();
            if (count($supervisors) > 0) {

                if (count($supervisors) == 1) {
                    foreach ($supervisors as $supervisor) {
                        $cashier_id = $supervisor->emp_id;
                        $select_status = 0;
                        $options = 0;

                        return \Response::json(array('cashier_id' => $cashier_id, 'select_status' => $select_status, 'options' => $options));
                    }
                } else {
                    $select_status = 1;
                    $options = '<option value="">Choose Supervisor</option>';
                    foreach ($supervisors as $supervisor) {

                        $options.='<option value="' . $supervisor->emp_id . '">' . $supervisor->first_name . ' ' . $supervisor->alias_name . '</option>';
                    }
                    return \Response::json(array('cashier_id' => "", 'select_status' => $select_status, 'options' => $options));
                }
            } else {
                return \Response::json(array('cashier_id' => 0, 'select_status' => 0, 'options' => 0));
            }
            
            
        }
    }
    
    
    public function gettaxamount() {
        $cash_collection = Input::get('cash_collection');
        $pos_date = Input::get('pos_date');
        $pos_date = explode('-',$pos_date);
        $pos_date = $pos_date[2].'-'.$pos_date[1].'-'.$pos_date[0];
         
       
        
        $tax_details = DB::table('master_resources')
                    ->select('tax_percent','tax_applicable_from','name')
                    ->where('resource_type', '=', "TAX")->where('status', '=', 1)
                     ->orderBy('id', 'desc')->first();
        
        if (count($tax_details) > 0) {
            
            $taxpercent= $tax_details->tax_percent;
            $taxdate=$tax_details->tax_applicable_from;
            if(strtotime($pos_date)>=strtotime($taxdate)){
                
                  if($taxpercent==""){
                    $taxpercent=5;
                }
                
           $cash_percent=($cash_collection*$taxpercent)/(100*1.05);
           $sales_percent=$cash_collection-$cash_percent;
           
           $sales_amount=number_format($sales_percent,2);
           $tax_amount=number_format($cash_percent,2);
            return \Response::json(array('sales_amount' => $sales_amount, 'tax_amount'=> $tax_amount));
         }else{
             
              $sales_amount=number_format($cash_collection,2);
             return \Response::json(array('sales_amount' =>$sales_amount, 'tax_amount'=> 0));
    
         }
           
          
        } else {
            
             return \Response::json(array('sales_amount' => 0, 'tax_amount'=> 0));
    
            
            }
    }
    

}
