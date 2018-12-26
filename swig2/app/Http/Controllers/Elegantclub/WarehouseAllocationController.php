<?php

namespace App\Http\Controllers\Elegantclub;
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
use App\Models\EmployeeWarehoseAllocation;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;

class WarehouseAllocationController extends Controller {
    public function warehose_allocation(Request $request){
        try{
            $paginate = Config::get('app.PAGINATE');
            $empwarehouseData = DB::table('employee_warehouse_allocation as e_w_a')
                        ->select('e_w_a.id','e_w_a.status','master_resources.name','employees.username','employees.first_name')
                        ->leftjoin('master_resources','master_resources.id','=','e_w_a.warehouse_id')
                        ->leftjoin('employees','employees.id','=','e_w_a.employee_id')
                        ->where('e_w_a.status','!=', 2)
                        ->paginate($paginate);
            $warehouses = DB::table('master_resources')
                        ->select('master_resources.id', 'master_resources.name')
                        ->where(['resource_type' => 'WAREHOUSE'])
                        ->where('status', '=', 1)
                        ->orderby('name', 'ASC')->get(); 
            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $searchbyemployee = Input::get('searchbyemployee');
                $searchbywarehouse = Input::get('searchbywarehouse'); 
                $searchbystatus = Input::get('searchbystatus');                
                
               /* $sortordcode = Input::get('sortordcode');
                $sortordname = Input::get('sortordname');
                $sortordbranch = Input::get('sortordbranch');
                $sortordregion = Input::get('sortordregion');

                $sortOrdDefault='';
                if($sortordbranch=='' && $sortordname=='' && $sortordcode=='' && $sortordregion==''){
                    $sortOrdDefault='ASC';
                }*/

                $empwarehouseData = DB::table('employee_warehouse_allocation as e_w_a')
                        ->select('e_w_a.id','e_w_a.status','master_resources.name','employees.username','employees.first_name')
                        ->leftjoin('master_resources','master_resources.id','=','e_w_a.warehouse_id')
                        ->leftjoin('employees','employees.id','=','e_w_a.employee_id')
                        ->where('e_w_a.status','!=', 2)
                        ->when($searchbyemployee, function ($query) use ($searchbyemployee) {
                            return $query->whereRaw("employees.username like '$searchbyemployee%' or employees.first_name like '$searchbyemployee%'");
                        })
                        ->when($searchbywarehouse, function ($query) use ($searchbywarehouse) {
                            return $query->whereRaw("(e_w_a.warehouse_id=$searchbywarehouse)");
                        })
                        ->when($searchbystatus, function ($query) use ($searchbystatus) {
                            if($searchbystatus==-1){
                                $searchbystatus=0;
                            }
                            return $query->where('e_w_a.status', '=', $searchbystatus);
                        })
                        /*->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('emp.username', $sortordcode);
                        })
                        ->when($sortordname, function ($query) use ($sortordname) {
                            return $query->orderby('emp.first_name', $sortordname);
                        })
                        ->when($sortordbranch, function ($query) use ($sortordbranch) {
                            return $query->orderby('branch.name', $sortordbranch);
                        })
                        ->when($sortordregion, function ($query) use ($sortordregion) {
                            return $query->orderby('region.name', $sortordregion);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('emp.first_name', $sortOrdDefault);
                        })*/
                        ->paginate($paginate);

            return view('elegantclub/warehouse_allocation/result', array('empwarehouseData'=>$empwarehouseData));
        }
            return view('elegantclub/warehouse_allocation/index',array('empwarehouseData' => $empwarehouseData,'warehouses' => $warehouses));

        } catch (\Exception $e) { //echo $e->getMessage(); die();
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function add(){
        try{
            $employees = DB::table('employees')
                        ->select('id','username','first_name')
                        ->orderby('first_name', 'ASC')->get();        
            $warehouses = DB::table('master_resources')
                        ->select('master_resources.id', 'master_resources.name')
                        ->where(['resource_type' => 'WAREHOUSE'])
                        ->where('status', '=', 1)
                        ->orderby('name', 'ASC')->get(); 
            return view('elegantclub/warehouse_allocation/add',array('employees' => $employees,'warehouses' => $warehouses));

        } catch (\Exception $e) { //echo $e->getMessage(); die();
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/warehose_allocation');
            
        }
    }
    
    public function store(){
        try{ 
            if(Session::get('company')){
                $company_id = Session::get('company');
            }
            $data = Input::all();
            $ew_id = $data['ew_id'];
            $emp_id = $data['employee_id'];
            $warehouse_id = $data['warehouse_id'];
            $searchExists = DB::table('employee_warehouse_allocation')
                    ->select('id')
                    ->where('employee_id','=',$emp_id)
                    ->where('warehouse_id','=',$warehouse_id)
                    ->where('status','!=',2)
                    ->first();
            if($searchExists){
                Toastr::error('Employee is already allocated!', $title = null, $options = []);
            }else{
                $emp_ware_alloc = new EmployeeWarehoseAllocation;
                if($ew_id != ''){
                    $emp_ware_alloc->exists = true;
                    $emp_ware_alloc->id = $ew_id;
                }
                $emp_ware_alloc->employee_id = $emp_id;
                $emp_ware_alloc->warehouse_id = $warehouse_id;
                $emp_ware_alloc->company_id = $company_id;
                $emp_ware_alloc->status = 1;
                $save = $emp_ware_alloc->save();
                if($save){
                    if($ew_id != ''){
                        Toastr::success('Employee Warehouse Updated', $title = null, $options = []);
                    }else{
                        Toastr::success('Employee Warehouse Allocated', $title = null, $options = []);
                    }
                }else{
                    Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
                }
            }
            return Redirect::to('elegantclub/warehose_allocation');
        } catch (\Exception $e) { //echo $e->getMessage(); die();
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/warehose_allocation/add');
            
        }
    }
    
    public function edit($id){
        try{
            $ew_id = \Crypt::decrypt($id);
            $ew_data = DB::table('employee_warehouse_allocation as e_w_a')
                ->select('e_w_a.*')
                ->leftjoin('employees', 'employees.id', '=', 'e_w_a.employee_id')
                ->leftjoin('master_resources', 'master_resources.id', '=', 'e_w_a.warehouse_id')
                ->where('e_w_a.id','=',$ew_id)
                ->first();
            $employees = DB::table('employees')
                        ->select('id','username','first_name')
                        ->orderby('first_name', 'ASC')->get();        
            $warehouses = DB::table('master_resources')
                        ->select('master_resources.id', 'master_resources.name')
                        ->where(['resource_type' => 'WAREHOUSE'])
                        ->where('status', '=', 1)
                        ->orderby('name', 'ASC')->get(); 
            return view('elegantclub/warehouse_allocation/edit',array('employees' => $employees,'warehouses' => $warehouses,'ew_data' => $ew_data));
            
        } catch (\Exception $e) { //echo $e->getMessage(); die();
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/warehose_allocation');
            
        }
    }
    
    public function enable($id){
        try{
            $ew_id = \Crypt::decrypt($id);
            $emp_ware_alloc = new EmployeeWarehoseAllocation;
            $emp_ware_alloc->exists = true;
            $emp_ware_alloc->id = $ew_id;
            $emp_ware_alloc->status = 1;
            $save = $emp_ware_alloc->save();
            if($save){
                Toastr::success('Employee Warehouse Enabled', $title = null, $options = []);
            }else{
                Toastr::error('Sorry There is Some Problem', $title = null, $options = []);
            }
            return Redirect::to('elegantclub/warehose_allocation');
            
        } catch (\Exception $e) { //echo $e->getMessage(); die();
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/warehose_allocation');
            
        }
    }
    
    public function disable($id){
        try{
            $ew_id = \Crypt::decrypt($id);
            $emp_ware_alloc = new EmployeeWarehoseAllocation;
            $emp_ware_alloc->exists = true;
            $emp_ware_alloc->id = $ew_id;
            $emp_ware_alloc->status = 0;
            $save = $emp_ware_alloc->save();
            if($save){
                Toastr::success('Employee Warehouse Disabled', $title = null, $options = []);
            }else{
                Toastr::error('Sorry There is Some Problem', $title = null, $options = []);
            }
            return Redirect::to('elegantclub/warehose_allocation');
            
        } catch (\Exception $e) { //echo $e->getMessage(); die();
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/warehose_allocation');
            
        }
    }
    
    public function delete($id){
        try{
            $ew_id = \Crypt::decrypt($id);
            $emp_ware_alloc = new EmployeeWarehoseAllocation;
            $emp_ware_alloc->exists = true;
            $emp_ware_alloc->id = $ew_id;
            $emp_ware_alloc->status = 2;
            $save = $emp_ware_alloc->save();
            if($save){
                Toastr::success('Employee Warehouse Deleted', $title = null, $options = []);
            }else{
                Toastr::error('Sorry There is Some Problem', $title = null, $options = []);
            }
            return Redirect::to('elegantclub/warehose_allocation');
            
        } catch (\Exception $e) { //echo $e->getMessage(); die();
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/warehose_allocation');
            
        }
    }
}