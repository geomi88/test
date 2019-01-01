<?php

namespace App\Http\Controllers\Supervisors;

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
use App\Notifications\InventoryNotifications;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Item_request;
use App\Models\Item_request_details;
use App\Models\Item_request_tracking;
use App\Models\Usermodule;
use DB;
use PDF;
use App;
use Exception;

class InventoryrequestController extends Controller {

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

            $loggintype = $employee_details->name;

            if ($loggintype != 'Supervisor' && $loggintype != 'Cashier' && $employee_details->admin_status != 1) {
                throw new Exception("No_permission");
            }

//            if($loggintype == 'Cashier'){
//                $item_requests = DB::table('item_request')
//                    ->select('item_request.*', 'warehouse.name as warehouse')
//                    ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
//                    ->whereRaw("item_request.request_status IN('In_Transit','Completed')")
//                    ->orderby('id', 'DESC')
//                    ->paginate($paginate);
//            }else{
                $item_requests = DB::table('item_request')
                    ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name','branch.branch_code as branch_code')
                    ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                    ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                    ->orderby('id', 'DESC')
                    ->paginate($paginate);
//            }
            

            if ($request->ajax()) {

                $searchkey = Input::get('searchkey');
                $sort = Input::get('sorting');
                if (!$sort) {
                    $sort = 'DESC';
                }
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                
//                if($loggintype == 'Cashier'){
//                    $item_requests = DB::table('item_request')
//                        ->select('item_request.*', 'warehouse.name as warehouse')
//                        ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
//                        ->whereRaw("item_request.request_status IN('In_Transit','Completed')")
//                        ->when($searchkey, function ($query) use ($searchkey) {
//                            return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
//                        })
//                        ->when($sort, function ($query) use ($sort) {
//                            return $query->orderby('item_request.id', $sort);
//                        })
//                        ->paginate($paginate);
//                }else{
                    $item_requests = DB::table('item_request')
                        ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name','branch.branch_code as branch_code')
                        ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                        ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                        ->when($searchkey, function ($query) use ($searchkey) {
                            return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
                        })
                        ->when($sort, function ($query) use ($sort) {
                            return $query->orderby('item_request.id', $sort);
                        })
                        ->paginate($paginate);
//                }

                return view('supervisors/inventory_request/results', array('item_requests' => $item_requests,'usertype'=>$loggintype));
            }
            return view('supervisors/inventory_request/index', array('item_requests' => $item_requests,'usertype'=>$loggintype));
        } catch (\Exception $e) {

            if ($e->getMessage() == "No_permission") {
                Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
                return Redirect::to('supervisors');
            }

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('supervisors');
        }
    }

    public function add() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            
            $empid = Session::get('login_id');
            $curDate = date('Y-m-d');
            
            $supervisor_branches = DB::table('resource_allocation')
                            ->select('resource_allocation.*', 'branch_details.id as branch_id', 'branch_details.name as branch_name', 'branch_details.opening_fund as opening_fund', 'branch_details.branch_code as branch_code')
                            ->join('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                            ->whereRaw("employee_id=$empid AND resource_allocation.from_date <= '$curDate' AND resource_allocation.to_date >= '$curDate' AND resource_allocation.active=1")
                            ->get();
 
            $warehouses = DB::table('master_resources')
                            ->select('master_resources.id', 'master_resources.name')
                            ->where(['resource_type' => 'WAREHOUSE'])
                            ->where('status', '=', 1)
                            ->where('company_id', '=', $company_id)
                            ->orderby('name', 'ASC')->get();

            $products = DB::table('inventory')
                            ->select('inventory.id', 'inventory.product_code', 'inventory.name')
                            ->where('status', '=', 1)
                            ->where('company_id', '=', $company_id)
                            ->orderby('product_code', 'ASC')->get();

//            $units = DB::table('units')
//                    ->select('units.*')->where('company_id', '=', $company_id)
//                    ->where('status', '=', 1)
//                    ->where('unit_type', '=', 'SIMPLE')
//                    ->orderby('name', 'ASC')
//                    ->get();

            return view('supervisors/inventory_request/add', array('supervisor_branches' => $supervisor_branches, 'warehouses' => $warehouses, 'products' => $products));
            
        } catch (\Exception $e) {

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('supervisors/inventory_request');
        }
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            $arrProductList = Input::get('arrProductList');
            $arrProductList = json_decode($arrProductList);
            
            $nextid = DB::table('item_request')->max('id') + 1;
            $nextid = $nextid + 1000;
            $nextrequestid = 'IR-' . $nextid;
            
            $requestmodel = new Item_request();
            $requestmodel->request_id = $nextrequestid;
            $requestmodel->branch_id = $arraData->branch_id;
            $requestmodel->ordered_by = $login_id;
            $requestmodel->request_status = 'In_Progress';
            $requestmodel->warehouse_id = $arraData->warehouse_id;

            $requestmodel->save();
            $latest_id = $requestmodel->id;

            foreach ($arrProductList as $product) {
                $itemdetails = new Item_request_details();
                $itemdetails->item_request_id = $latest_id;
                $itemdetails->inventory_id = $product->product_id;
                $itemdetails->request_quantity = $product->quantity;
                $itemdetails->unit = $product->unit_id;
                $itemdetails->in_final_order = 1;
                $itemdetails->save();
            }

            $trackingmodel = new Item_request_tracking();
            $trackingmodel->inventory_request_id = $latest_id;
            $trackingmodel->status = 'In_Progress';
            $trackingmodel->save();
            
            //Notification to warehouse manager
            
            $loggedinemp = DB::table('employees')
                    ->select('employees.first_name')
                    ->where('employees.id', '=', $login_id)
                    ->first();
            
            $warehousemanager = DB::table('master_resources')
                    ->select('master_resources.id','emp.id as emp_id')
                    ->leftjoin('employees as emp', 'master_resources.warehouse_manager', '=', 'emp.id')
                    ->where('master_resources.id', '=', $arraData->warehouse_id)
                    ->first();
            
            $from=$login_id;
            $to=$warehousemanager->emp_id;
            $message = 'Inventory Request ' . $nextrequestid . ' From ' . $loggedinemp->first_name . ' Please review & update the status for the request.';
            $category="Inventory Request";
            $type = "warehouse/received_inventory_request/showdetails";
            Auth::user()->notify(new InventoryNotifications($from, $to, $message, $category, $latest_id, $type));

            Toastr::success("Inventory Request $nextrequestid Saved Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function showdetails($id) {
        $id = \Crypt::decrypt($id);
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $employee_details = DB::table('employees')
                ->select('employees.*', 'master_resources.name')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', $login_id)
                ->first();

        $loggintype = $employee_details->name;
        
        $item_request_details = DB::table('item_request')
                ->select('item_request.*', 'warehouse.name as warehouse', 'branch.name as branch_name','branch.branch_code as branch_code','inventory.product_code as product_code',
                        'units.name as units', 'inventory.name as product_name',
                        'det.id as det_id',
                        'det.request_quantity as request_quantity',
                        'det.approved_quantity as approved_quantity',
                        'det.received_quantity as received_quantity',
                        db::raw("(CASE WHEN det.in_final_order=0 THEN 'Cancelled' ELSE 'Active' END) as item_status"))
                ->leftjoin('item_request_details as det', 'item_request.id', '=', 'det.item_request_id')
                ->leftjoin('inventory', 'det.inventory_id', '=', 'inventory.id')
                ->leftjoin('units', 'det.unit', '=', 'units.id')
                ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                ->where('item_request.id', '=', $id)
                ->get();
        $item_request = $item_request_details[0];
        return view('supervisors/inventory_request/showdetails', array('item_request_details' => $item_request_details, 'item_request' => $item_request,'usertype'=>$loggintype));
    }

    public function checkmaximumqty() {
        $product_id = Input::get('product_id');
        $branch_id = Input::get('branch_id');

        $phyStock = DB::table('branch_physical_stock')
                ->select("branch_physical_stock.*")
                ->whereRaw("branch_id=$branch_id AND created_at = (select max(created_at) from branch_physical_stock)")
                ->first();
        
        if(count($phyStock)>0){
            $phyStock = json_decode($phyStock->physical_stock, true);
        }else{
            $phyStock = array();
        }

        $maxStock = DB::table('inventory')
                ->select("max_branch_stock")
                ->whereRaw("id=$product_id")
                ->value('max_branch_stock');
        if (key_exists($product_id, $phyStock)) {
            return \Response::json(array('maxValue' => $maxStock,'presentStock'=>$phyStock[$product_id]));
        } else {
            return \Response::json(array('maxValue' => $maxStock,'presentStock'=>0));
        }
    }
    
    public function getunits() {
        $product_id = Input::get('product_id');
        
        $primaryUnit = DB::table('inventory')
                ->select("primary_unit")
                ->whereRaw("id=$product_id")
                ->value('primary_unit');

        $selectedUnits = DB::table('inventory_alternate_units')
                ->select('inventory_alternate_units.unit_id')
                ->where(['inventory_item_id' => $product_id])
                ->pluck('unit_id')
                ->toArray();
        
        $arrUnits = array();
//        if(count($selectedUnits)>0){
//            $arrUnits = $selectedUnits;
//        }
        
        $arrUnits[]=$primaryUnit;
        
        $unitsData = DB::table('units')
            ->select("units.*")
            ->whereIn('id',$arrUnits)
            ->get();
        
//        $strHtml="<option value=''>Select Unit</option>";
//        $strHtml="";
//        foreach ($unitsData as $unit) {
//            $strHtml.="<option value='".$unit->id."'>".$unit->name."</option>";
//        }
        
//        echo $strHtml;
        
        return \Response::json(array('unitid' => $unitsData[0]->id,'name' => $unitsData[0]->name));
        
    }
    
    public function completerequest() {
        try {
            
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $requestid = Input::get('requestid');
            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            
            $trackingmodel = new Item_request_tracking();
            $trackingmodel->inventory_request_id = $requestid;
            $trackingmodel->status = 'Completed';
            $trackingmodel->save();
            
            $itemrequest = DB::table('item_request')
                ->where(['id' => $requestid])
                ->update(['request_status' => 'Completed']);
            
            foreach ($arraData as $objData) {
                $itemdetails = DB::table('item_request_details')
                   ->where(['id' => $objData->itemdet_id,'item_request_id' => $requestid])
                   ->whereRaw("in_final_order!=0")
                   ->update(['received_quantity' => $objData->quantity]);
            }
            
            //Notification to warehouse manager
            
            $loggedinemp = DB::table('employees')
                    ->select('employees.first_name')
                    ->where('employees.id', '=', $login_id)
                    ->first();
            
            $request = DB::table('item_request')
                    ->select('item_request.ordered_by','item_request.request_id','item_request.warehouse_id')
                    ->where(['id' => $requestid])->first();
            
            $warehousemanager = DB::table('master_resources')
                    ->select('master_resources.id','emp.id as emp_id')
                    ->leftjoin('employees as emp', 'master_resources.warehouse_manager', '=', 'emp.id')
                    ->where('master_resources.id', '=', $request->warehouse_id)
                    ->first();
            
            $from=$login_id;
            $to=$warehousemanager->emp_id;
            $latest_id = $requestid;
            $message = 'Inventory Request ' . $request->request_id . ' from ' . $loggedinemp->first_name . 'has been completed';
            $category="Inventory Request";
            $type = "warehouse/received_inventory_request/showdetails";
            Auth::user()->notify(new InventoryNotifications($from, $to, $message, $category, $latest_id, $type));
            
            Toastr::success('Inventory Request Completed Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    // Generate PDF funcion
    public function exporttopdf() {
        $searchkey = Input::get('searchkey');
            $sort = Input::get('sorting');
            if (!$sort) {
                $sort = 'DESC';
            }
            
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

//            $employee_details = DB::table('employees')
//                    ->select('employees.*', 'master_resources.name')
//                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
//                    ->where('employees.id', '=', $login_id)
//                    ->first();
//
//            $loggintype = $employee_details->name;
            
//            if($loggintype == 'Cashier'){
//                $item_requests = DB::table('item_request')
//                    ->select('item_request.*', 'warehouse.name as warehouse')
//                    ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
//                    ->whereRaw("item_request.request_status IN('In_Transit','Completed')")
//                    ->when($searchkey, function ($query) use ($searchkey) {
//                        return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
//                    })
//                    ->when($sort, function ($query) use ($sort) {
//                        return $query->orderby('item_request.id', $sort);
//                    })
//                    ->get();
//            }else{
                $item_requests = DB::table('item_request')
                    ->select('item_request.*', 'warehouse.name as warehouse','branch.name as branch_name')
                    ->leftjoin('master_resources as warehouse', 'item_request.warehouse_id', '=', 'warehouse.id')
                    ->leftjoin('master_resources as branch', 'item_request.branch_id', '=', 'branch.id')
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->where('item_request.request_id', 'like', '%' . $searchkey . '%');
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('item_request.id', $sort);
                    })
                    ->get();
//            }
            
                    
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
                <div style="text-align:center;"><h1>Inventory Item Requests</h1></div>
			<table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Request Id</td>
						<td style="padding:10px 5px;color:#fff;"> Status</td>
						<td style="padding:10px 5px;color:#fff;"> Branch </td>
						<td style="padding:10px 5px;color:#fff;"> Warehouse </td>
						<td style="padding:10px 5px;color:#fff;"> Requested Date</td>
						
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
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $item_request->request_id . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $status . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $item_request->branch_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $item_request->warehouse . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $created_date . '</td>

                            </tr>';
        }
        $html_table .='</tbody>
			</table>
                    </section>
                </body>
        </html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_supervisor_item_requests.pdf');
    }
}
