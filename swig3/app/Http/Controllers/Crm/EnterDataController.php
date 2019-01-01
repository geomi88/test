<?php

namespace App\Http\Controllers\Crm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use App\Masterresources;
use App\Models\Crm_customers;
use DB;

class EnterDataController extends Controller {
    public function index(Request $request){
        try{
            $login_id = Session::get('login_id'); 
            $job_position = DB::table('employees')
                    ->select('master_resources.name')
                    ->leftjoin('master_resources','employees.job_position','=','master_resources.id')
                    ->where('employees.id','=',$login_id)
                    ->where('master_resources.name','=','Cashier')
                    ->first();
//                    dd("hai");
            if(isset($job_position)){
                if($request->ajax()){
                    $paginate = Input::get("pagelimit");
                    if(empty($paginate)){
                        $paginate = Config::get("app.PAGINATE");
                    }
                    $searchbymobile = Input::get("searchbymobile");
                    $crm_customers = DB::table('crm_customers as a')
                        ->select('a.*',
                                DB::raw("count(a.id) as repeat_count"),
                                DB::raw("(select b.name FROM crm_customers b where b.mobile_number = a.mobile_number ORDER BY b.id DESC LIMIT 1) as cus_name"))
                        ->where('a.status','!=',2)
                        ->where('a.created_by','=',$login_id)
                        ->when($searchbymobile,function($qry) use ($searchbymobile){
                            return $qry->whereraw("(a.mobile_number LIKE '%$searchbymobile%')");
                        })
                        ->groupBy("a.mobile_number","a.branch_id")
                        ->orderby("a.id"," DESC ")
                        ->paginate($paginate);
                    return view('crm/enter_data/result',array('crm_customers' => $crm_customers));
                }
                else{
                    $paginate = Config::get("app.PAGINATE");
                    $crm_customers = DB::table('crm_customers as a')
                        ->select('*',
                                DB::raw("count(a.id) as repeat_count"),
                                DB::raw("(select b.name FROM crm_customers b where b.mobile_number = a.mobile_number ORDER BY b.id DESC LIMIT 1) as cus_name"))
                        ->where('a.status','!=',2)
                        ->where('a.created_by','=',$login_id)
                        ->orderby("a.id"," DESC ")
                        ->groupBy("a.mobile_number","a.branch_id")
                        ->paginate($paginate);
                    return view('crm/enter_data/index',array('crm_customers' => $crm_customers));
                }
            }
            else{
                Toastr::error("Sorry You don't have permission!", $title = null, $options = []);
                return Redirect::to('crm');
            }
        } catch (\Exception $e) { 
//            dd($e);
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('crm');

            }
        }
        
        public function add(){
            try{
                return view('crm/enter_data/add');
            } catch (\Exception $e) { //echo $e->getMessage(); die();
                Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
                return Redirect::to('crm/enter_data');
            }
        }
        
        public function store(){
            try{
                $data = Input::all();
                $login_id = Session::get('login_id'); 
                $current_date = date("Y-m-d");
                $creater_branch_id = DB::table('resource_allocation')
                        ->select('resource_allocation.branch_id')
                        ->leftjoin('employees','resource_allocation.employee_id','=','employees.id')
                        ->whereRaw("((resource_allocation.from_date < '$current_date' or resource_allocation.from_date = '$current_date') and (resource_allocation.to_date > '$current_date' or resource_allocation.to_date = '$current_date'))")
                        ->where('employees.id','=',$login_id)
                        ->first();
                if(!empty($creater_branch_id->branch_id)){
                    $branchId = $creater_branch_id->branch_id;

                    $crm_obj = new Crm_customers;
                    $crm_obj->name = $data['customer_name'];
                    $crm_obj->mobile_number = $data['ph_no'];
                    $crm_obj->branch_id = $branchId;
                    $crm_obj->created_by = $login_id;
                    $crm_obj->status = 1;
                    $save = $crm_obj->save();
                    if($save){
                        Toastr::success('CRM Customer Added Successfully', $title = null, $options = []);
                    }else{
                        Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
                    } 
                }
                else{
                    Toastr::error("Sorry You don't have permission!", $title = null, $options = []);
                    return Redirect::to('crm');
                }
                return Redirect::to('crm/enter_data');
            } catch (\Exception $e) {
                Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
                return Redirect::to('crm/enter_data');
            }
        }
        
        public function check_phone_number() {
            $ph_no = Input::get('p_code');
            $customer_data = DB::table('crm_customers')
                    ->select('*')
                    ->where('mobile_number', '=', $ph_no)
                    ->where('status', '!=', 2)
                    ->get();

            if (count($customer_data) == 0) {
                return \Response::json(array('msg' => 'false'));
            } else {
                return \Response::json(array('msg' => 'true'));
            }
    }
}