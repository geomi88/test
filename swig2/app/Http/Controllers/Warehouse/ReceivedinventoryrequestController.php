<?php

namespace App\Http\Controllers\Warehouse;

use App\Events\RequisitionSubmitted;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Item_request;
use App\Models\Item_request_details;
use App\Models\Item_request_tracking;
use App\Notifications\InventoryNotifications;
use App\Models\Usermodule;
use DB;
use PDF;
use App;
use Exception;

class ReceivedinventoryrequestController extends Controller {

    public function index(Request $request) {
        try {
            
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('employees.id', '=', $login_id)
                    ->first();
            
            $branches = DB::table('master_resources')
                        ->select('master_resources.name', 'master_resources.id')
                        ->where(['resource_type' => 'BRANCH', 'status' => 1])->get();
           

            $warehouse = DB::table('master_resources')
                ->select('master_resources.name', 'master_resources.id')
                ->where('status', '=', 1)
                ->where('resource_type', '=', 'WAREHOUSE')
                ->orderby('name', 'ASC')
                ->get();
           
            $loggintype = $employee_details->name;
            
            if ($loggintype != 'Cashier' && $loggintype != 'Warehouse_Manager' && $employee_details->admin_status != 1) {
                throw new Exception("No_permission");
            }

            if($loggintype == 'Cashier'){
                
                 $status = DB::table('item_request')
                        ->select('item_request.request_status')
                        ->whereRaw("item_request.request_status IN('In_Transit','Completed')")
                        ->distinct()->get();

                $item_requests = DB::table('item_request')
                    ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name')
                    ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                    ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                    ->whereRaw("item_request.request_status IN('In_Transit','Completed')")
                    ->orderby('id', 'DESC')
                    ->paginate($paginate);
            }else{
                
                 $status = DB::table('item_request')
                        ->select('item_request.request_status')
                        ->distinct()->get();
                
                $item_requests = DB::table('item_request')
                    ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name')
                    ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                    ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                    ->where(['warehouse.warehouse_manager' => $login_id])
                    ->orderby('id', 'DESC')
                    ->paginate($paginate);
            }
            

            if ($request->ajax()) {

                $searchkey = Input::get('searchkey');
                $sort = Input::get('sorting');
                $status = Input::get('status');
                $branch = Input::get('branch');
                $warehouse = Input::get('warehouse');
                $createdfrom = Input::get('startdatefrom');
                $endedfrom = Input::get('enddatefrom');
                
                
                $sortorderrequesteddate = Input::get('sortorderrequesteddate');
                $sortorderbranch = Input::get('sortorderbranch');
                $sortorderwarehouse = Input::get('sortorderwarehouse');
                
                
                if ($createdfrom != '') {
                $createdfrom = explode('-', $createdfrom);
                $createdfrom = $createdfrom[2] . '-' . $createdfrom[1] . '-' . $createdfrom[0];
                }
                 if ($endedfrom != '') {
                $endedfrom = explode('-', $endedfrom);
                $endedfrom = $endedfrom[2] . '-' . $endedfrom[1] . '-' . $endedfrom[0];
                }
                
              //  if (!$sort) {
              //      $sort = 'DESC';
             //   }
                $sortDefault="";
                 if($sort=='' && $sortorderrequesteddate=='' && $sortorderbranch=='' && $sortorderwarehouse==''){
                   $sortDefault = 'DESC';
                }
                
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                
                if($loggintype == 'Cashier'){
                    
                    $item_requests = DB::table('item_request')
                        ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name')
                        ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                        ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                        ->whereRaw("item_request.request_status IN('In_Transit','Completed')")
                        ->when($searchkey, function ($query) use ($searchkey) {
                            return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
                        })
                        ->when($status, function ($query) use ($status) {
                            return $query->where('item_request.request_status', '=', $status);
                      
                            })
                        ->when($branch, function ($query) use ($branch) {
                            return $query->where('item_request.branch_id', '=', $branch);
                      
                        })
                        ->when($warehouse, function ($query) use ($warehouse) {
                            return $query->where('item_request.warehouse_id', '=', $warehouse);
                      
                        })
                        ->when($createdfrom, function ($query) use ($createdfrom) {
                        return $query->whereRaw("date(item_request.created_at) >= '$createdfrom' ");
                        })
                        ->when($endedfrom, function ($query) use ($endedfrom) {
                        return $query->whereRaw("date(item_request.created_at)<= '$endedfrom' ");
                        })
                        ->when($sort, function ($query) use ($sort) {
                            return $query->orderby('item_request.id', $sort);
                        })
                        ->when($sortorderrequesteddate, function ($query) use ($sortorderrequesteddate) {
                        return $query->orderby('item_request.created_at', $sortorderrequesteddate);
                        })
                        ->when($sortorderbranch, function ($query) use ($sortorderbranch) {
                        return $query->orderby('branch.name', $sortorderbranch);
                        })
                        ->when($sortorderwarehouse, function ($query) use ($sortorderwarehouse) {
                        return $query->orderby('warehouse.name', $sortorderwarehouse);
                        })
                        ->when($sortDefault, function ($query) use ($sortDefault) {
                        return $query->orderby('item_request.id', $sortDefault);
                        })
                        ->paginate($paginate);
                    
                }else{
                    
                    $item_requests = DB::table('item_request')
                        ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name')
                        ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                        ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                        ->where(['warehouse.warehouse_manager' => Session::get('login_id')])
                        ->when($searchkey, function ($query) use ($searchkey) {
                            return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
                        })
                        ->when($status, function ($query) use ($status) {
                            return $query->where('item_request.request_status', '=', $status);
                      
                            })
                        ->when($branch, function ($query) use ($branch) {
                            return $query->where('item_request.branch_id', '=', $branch);
                      
                        })
                        ->when($warehouse, function ($query) use ($warehouse) {
                            return $query->where('item_request.warehouse_id', '=', $warehouse);
                      
                        })
                        ->when($createdfrom, function ($query) use ($createdfrom) {
                        return $query->whereRaw("date(item_request.created_at) >= '$createdfrom' ");
                        })
                         ->when($endedfrom, function ($query) use ($endedfrom) {
                        return $query->whereRaw("date(item_request.created_at)<= '$endedfrom' ");
                        })
                        ->when($sort, function ($query) use ($sort) {
                            return $query->orderby('item_request.id', $sort);
                        })
                         ->when($sortorderrequesteddate, function ($query) use ($sortorderrequesteddate) {
                        return $query->orderby('item_request.created_at', $sortorderrequesteddate);
                        })
                       ->when($sortorderbranch, function ($query) use ($sortorderbranch) {
                        return $query->orderby('branch.name', $sortorderbranch);
                        })
                        ->when($sortorderwarehouse, function ($query) use ($sortorderwarehouse) {
                        return $query->orderby('warehouse.name', $sortorderwarehouse);
                        })
                        ->when($sortDefault, function ($query) use ($sortDefault) {
                        return $query->orderby('item_request.id', $sortDefault);
                        })
                        ->paginate($paginate);
                }
                
                return view('warehouse/received_inventory_request/results', array('item_requests' => $item_requests));
            }
            return view('warehouse/received_inventory_request/index', array('item_requests' => $item_requests,'branches' => $branches,'status' => $status,'warehouses' => $warehouse));
        } catch (\Exception $e) {

            if ($e->getMessage() == "No_permission") {
                Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
                return Redirect::to('warehouse');
            }

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('warehouse');
        }
    }

    public function approverequest() {
        try {
            
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $requestid = Input::get('requestid');
            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            
            $trackingmodel = new Item_request_tracking();
            $trackingmodel->inventory_request_id = $requestid;
            $trackingmodel->status = 'In_Transit';
            $trackingmodel->save();

            $itemrequest = DB::table('item_request')
                ->where(['id' => $requestid])
                ->update(['request_status' => 'In_Transit']);
            
            foreach ($arraData as $objData) {
                
                $itemdetails = DB::table('item_request_details')
                   ->where(['id' => $objData->itemdet_id,'item_request_id' => $requestid])
                    ->whereRaw("in_final_order!=0")
                   ->update(['approved_quantity' => $objData->quantity]);
            }
            
            //Notification back to supervisor
            $loggedinemp = DB::table('employees')
                    ->select('employees.first_name')
                    ->where('employees.id', '=', $login_id)
                    ->first();
            
            $request = DB::table('item_request')
                    ->select('item_request.ordered_by','item_request.request_id')
                    ->where(['id' => $requestid])->first();
            
            $from=$login_id;
            $to=$request->ordered_by;
            $latest_id = $requestid;
            $message = 'Your request ' . $request->request_id . ' was approved by ' . $loggedinemp->first_name;
            $category="Inventory Request";
            $type = "supervisors/inventory_request/showdetails";
            Auth::user()->notify(new InventoryNotifications($from, $to, $message, $category, $latest_id, $type));
            
            Toastr::success('Inventory Request Has Been Approved!', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function holdrequest() {
        try {
            
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $requestid = Input::get('requestid');
            
            $trackingmodel = new Item_request_tracking();
            $trackingmodel->inventory_request_id = $requestid;
            $trackingmodel->status = 'Holded';
            $trackingmodel->save();
            
            $itemrequest = DB::table('item_request')
                   ->where(['id' => $requestid])
                   ->update(['request_status' => 'Holded']);
            
            //Notification back to supervisor
            $loggedinemp = DB::table('employees')
                    ->select('employees.first_name')
                    ->where('employees.id', '=', $login_id)
                    ->first();
            
            $request = DB::table('item_request')
                    ->select('item_request.ordered_by','item_request.request_id')
                    ->where(['id' => $requestid])->first();
            
            $from=$login_id;
            $to=$request->ordered_by;
            $latest_id = $requestid;
            $message = 'Your request ' . $request->request_id . ' was holded by ' . $loggedinemp->first_name . '. Please revise & request again.';
            $category="Inventory Request";
            $type = "supervisors/inventory_request/showdetails";
            Auth::user()->notify(new InventoryNotifications($from, $to, $message, $category, $latest_id, $type));
            
            Toastr::success('Inventory Request Holded Successfully!', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    
    public function rejectrequest() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $requestid = Input::get('requestid');
            
            $trackingmodel = new Item_request_tracking();
            $trackingmodel->inventory_request_id = $requestid;
            $trackingmodel->status = 'Rejected';
            $trackingmodel->save();
            
            $itemrequest = DB::table('item_request')
                   ->where(['id' => $requestid])
                   ->update(['request_status' => 'Rejected']);
            
            $itemdet = DB::table('item_request_details')
                   ->where(['item_request_id' => $requestid])
                   ->update(['in_final_order' => 0]);
            
            //Notification back to supervisor
            $loggedinemp = DB::table('employees')
                    ->select('employees.first_name')
                    ->where('employees.id', '=', $login_id)
                    ->first();
            
            $request = DB::table('item_request')
                    ->select('item_request.ordered_by','item_request.request_id')
                    ->where(['id' => $requestid])->first();
            
            $from=$login_id;
            $to=$request->ordered_by;
            $latest_id = $requestid;
            $message = 'Your request ' . $request->request_id . ' was rejected by ' . $loggedinemp->first_name . '. Please revise & request again.';
            $category="Inventory Request";
            $type = "supervisors/inventory_request/showdetails";
            Auth::user()->notify(new InventoryNotifications($from, $to, $message, $category, $latest_id, $type));
            
            Toastr::success('Inventory Request Rejected !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function cancelitem() {
        try {
            
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $requestid = Input::get('requestid');
            $item_detid = Input::get('pid');
            
            $itemdet = DB::table('item_request_details')
                   ->where(['id' => $item_detid])
                   ->update(['in_final_order' => 0]);
            
            $acitveItemcounts = DB::table('item_request_details')
                        ->where(['item_request_id' => $requestid,'in_final_order' => 1])
                        ->count();
            
            // if all items are cancelled then request is rejected
            if($acitveItemcounts == 0){
                $itemrequest = DB::table('item_request')
                   ->where(['id' => $requestid])
                   ->update(['request_status' => 'Rejected']);
                
                //Notification back to supervisor
                $loggedinemp = DB::table('employees')
                        ->select('employees.first_name')
                        ->where('employees.id', '=', $login_id)
                        ->first();

                $request = DB::table('item_request')
                        ->select('item_request.ordered_by','item_request.request_id')
                        ->where(['id' => $requestid])->first();

                $from=$login_id;
                $to=$request->ordered_by;
                $latest_id = $requestid;
                $message = 'Your request ' . $request->request_id . ' was rejected by ' . $loggedinemp->first_name . '. Please revise & request again.';
                $category="Inventory Request";
                $type = "supervisors/inventory_request/showdetails";
                Auth::user()->notify(new InventoryNotifications($from, $to, $message, $category, $latest_id, $type));
            
            }
            
            Toastr::success('Inventory Item Cancelled Successfully!', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function showdetails($id) {
        $id = \Crypt::decrypt($id);

        $item_request_details = DB::table('item_request')
                ->select('item_request.*', 'warehouse.name as warehouse', 'branch.name as branch_name','inventory.product_code as product_code',
                        'inventory.name as product_name','units.name as units',
                        'det.id as itemdet_id',
                        'det.request_quantity as request_quantity',
                        'det.received_quantity as received_quantity',
                        db::raw("CASE WHEN det.in_final_order=0 THEN '' ELSE COALESCE(det.approved_quantity,det.request_quantity) END as approved_quantity"),
                        db::raw("(CASE WHEN det.in_final_order=0 THEN 'Cancelled' ELSE 'Active' END) as item_status"))
                ->leftjoin('item_request_details as det', 'item_request.id', '=', 'det.item_request_id')
                ->leftjoin('inventory', 'det.inventory_id', '=', 'inventory.id')
                ->leftjoin('units', 'det.unit', '=', 'units.id')
                ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                ->where('item_request.id', '=', $id)
                ->get();
        $item_request = $item_request_details[0];
        return view('warehouse/received_inventory_request/showdetails', array('item_request_details' => $item_request_details,'item_request'=>$item_request));
    }
    
 // Generate PDF funcion
    public function exporttopdf() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $employee_details = DB::table('employees')
                ->select('employees.*', 'master_resources.name')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', $login_id)
                ->first();

        $loggintype = $employee_details->name;
            
        $searchkey = Input::get('searchkey');
        $sort = Input::get('sortsimp');
      //  if (!$sort) {
      //      $sort = 'DESC';
     //   }
        
        
        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }

        
                $status = Input::get('status');
                $branch = Input::get('branch');
                $warehouse = Input::get('warehouse');
                $createdfrom = Input::get('startdatefrom');
                $endedfrom = Input::get('enddatefrom');
                
                
                $sortorderrequesteddate = Input::get('sortorderrequesteddate');
                $sortorderbranch = Input::get('sortorderbranch');
                $sortorderwarehouse = Input::get('sortorderwarehouse');
                 
                if ($createdfrom != '') {
                $createdfrom = explode('-', $createdfrom);
                $createdfrom = $createdfrom[2] . '-' . $createdfrom[1] . '-' . $createdfrom[0];
                }
                 if ($endedfrom != '') {
                $endedfrom = explode('-', $endedfrom);
                $endedfrom = $endedfrom[2] . '-' . $endedfrom[1] . '-' . $endedfrom[0];
                }
                
            $sortDefault="";
                 if($sort=='' && $sortorderrequesteddate=='' && $sortorderbranch=='' && $sortorderwarehouse==''){
                   $sortDefault = 'DESC';
                }
                     
                
        if($loggintype == 'Cashier'){

            $item_requests = DB::table('item_request')
                ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name')
                ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                ->whereRaw("item_request.request_status IN('In_Transit','Completed')")
                ->when($searchkey, function ($query) use ($searchkey) {
                    return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
                })
                 ->when($status, function ($query) use ($status) {
                            return $query->where('item_request.request_status', '=', $status);
                      
                            })
                        ->when($branch, function ($query) use ($branch) {
                            return $query->where('item_request.branch_id', '=', $branch);
                      
                        })
                        ->when($warehouse, function ($query) use ($warehouse) {
                            return $query->where('item_request.warehouse_id', '=', $warehouse);
                      
                        })
                        ->when($createdfrom, function ($query) use ($createdfrom) {
                        return $query->whereRaw("date(item_request.created_at) >= '$createdfrom' ");
                        })
                        ->when($endedfrom, function ($query) use ($endedfrom) {
                        return $query->whereRaw("date(item_request.created_at)<= '$endedfrom' ");
                        })
                        ->when($sort, function ($query) use ($sort) {
                         return $query->orderby('item_request.id', $sort);
                        })
                        ->when($sortorderrequesteddate, function ($query) use ($sortorderrequesteddate) {
                        return $query->orderby('item_request.created_at', $sortorderrequesteddate);
                        })
                        ->when($sortorderbranch, function ($query) use ($sortorderbranch) {
                        return $query->orderby('branch.name', $sortorderbranch);
                        })
                        ->when($sortorderwarehouse, function ($query) use ($sortorderwarehouse) {
                        return $query->orderby('warehouse.name', $sortorderwarehouse);
                        })
                        ->when($sortDefault, function ($query) use ($sortDefault) {
                        return $query->orderby('item_request.id', $sortDefault);
                        })
                        ->get();

        }else{

            $item_requests = DB::table('item_request')
                ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name')
                ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                ->where(['warehouse.warehouse_manager' => Session::get('login_id')])
                ->when($searchkey, function ($query) use ($searchkey) {
                    return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
                })
                 ->when($status, function ($query) use ($status) {
                            return $query->where('item_request.request_status', '=', $status);
                      
                            })
                        ->when($branch, function ($query) use ($branch) {
                            return $query->where('item_request.branch_id', '=', $branch);
                      
                        })
                        ->when($warehouse, function ($query) use ($warehouse) {
                            return $query->where('item_request.warehouse_id', '=', $warehouse);
                      
                        })
                        ->when($createdfrom, function ($query) use ($createdfrom) {
                        return $query->whereRaw("date(item_request.created_at) >= '$createdfrom' ");
                        })
                        ->when($endedfrom, function ($query) use ($endedfrom) {
                        return $query->whereRaw("date(item_request.created_at)<= '$endedfrom' ");
                        })
                        ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('item_request.id', $sort);
                        })
                        ->when($sortorderrequesteddate, function ($query) use ($sortorderrequesteddate) {
                        return $query->orderby('item_request.created_at', $sortorderrequesteddate);
                        })
                        ->when($sortorderbranch, function ($query) use ($sortorderbranch) {
                        return $query->orderby('branch.name', $sortorderbranch);
                        })
                        ->when($sortorderwarehouse, function ($query) use ($sortorderwarehouse) {
                        return $query->orderby('warehouse.name', $sortorderwarehouse);
                        })
                        ->when($sortDefault, function ($query) use ($sortDefault) {
                        return $query->orderby('item_request.id', $sortDefault);
                        })
                        ->get();
        }
                    
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
                <div style="text-align:center;"><h1>Received Inventory Item Requests</h1></div>
			<table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
                                                <td style="padding:10px 0;color:#fff;"> Requested Date</td>
						<td style="padding:10px 0;color:#fff;"> Request Id</td>
						<td style="padding:10px 0;color:#fff;"> Status</td>
						<td style="padding:10px 0;color:#fff;"> Warehouse </td>
						
					</tr>
				</thead>
				<thead class="listHeaderBottom">
					<tr class="headingHolder">
						<td class="filterFields"></td>
						<td class=""></td>
						<td class="filterFields"></td>
						<td class="filterFields"></td>
					</tr>
				</thead>
				<tbody class="pos" id="pos" >';
        foreach ($item_requests as $item_request) {
            $created_date = date("d-m-Y", strtotime($item_request->created_at));
            $status = str_replace('_',' ',$item_request->request_status);
            $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $created_date . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $item_request->request_id . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $status . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 15px 5px;">' . $item_request->warehouse . '</td>
                                   
                            </tr>';
        }
        $html_table .='</tbody>
			</table>
                    </section>
                </body>
        </html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_received_supervisor_item_requests.pdf');
    }
}
