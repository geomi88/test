<?php

namespace App\Http\Controllers\Purchase;

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
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use App\Helpers\CategoryHierarchy;
use App\Models\Batch_master;
use App\Models\Stock_info;
use App\Services\Stockfunctions;
use Customhelper;
use Exception;
use DB;
use PDF;
use App;
use Mail;

class ReceivedgrnController extends Controller
{
    public function index(Request $request){
         try {
            $paginate = Config::get('app.PAGINATE');
            $purchaseorders = DB::table('ac_purchase_order as ord')
                    ->select('ord.*', 'pa.payment_code', 'requisition.total_price', 'requisition.requisition_code', 'ac_party.first_name as supname', 'ac_party.code as supcode')
                    ->leftjoin('ac_payment_advice as pa', 'ord.payment_advice_id', '=', 'pa.id')
                    ->leftjoin('requisition', 'ord.requisition_id', '=', 'requisition.id')
                    ->leftjoin('ac_party', 'ord.to_ledger_id', '=', 'ac_party.id')
                    ->where('ord.status', '=', 1)
                    ->where('ord.order_status', '=', 1)
                    ->where('ord.mailed_status', '=', 1)
                    ->where('ord.send_warehouse_status', '=', 1)
                    ->where('ord.stock_entered', '=', 1)
                    ->where('ord.po_close_status', '!=', 1)
                    ->orderby('ord.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbyordcode = Input::get('searchbyordcode');
                $searchbyrqcode = Input::get('searchbyrqcode');
                $searchbysupplier = Input::get('searchbysupplier');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordpocode = Input::get('sortordpocode');

                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }

                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortordpocode == '') {
                    $sortOrdDefault = 'DESC';
                }

                $purchaseorders = DB::table('ac_purchase_order as ord')
                        ->select('ord.*', 'pa.payment_code', 'requisition.total_price', 'requisition.requisition_code', 'ac_party.first_name as supname', 'ac_party.code as supcode')
                        ->leftjoin('ac_payment_advice as pa', 'ord.payment_advice_id', '=', 'pa.id')
                        ->leftjoin('requisition', 'ord.requisition_id', '=', 'requisition.id')
                        ->leftjoin('ac_party', 'ord.to_ledger_id', '=', 'ac_party.id')
                        ->where('ord.status', '=', 1)
                        ->where('ord.mailed_status', '=', 1)
                        ->where('ord.order_status', '=', 1)
                        ->where('ord.send_warehouse_status', '=', 1)
                        ->where('ord.stock_entered', '=', 1)
                        ->when($searchbyordcode, function ($query) use ($searchbyordcode) {
                            return $query->whereRaw("ord.order_code like '%$searchbyordcode%' ");
                        })
                        ->when($searchbyrqcode, function ($query) use ($searchbyrqcode) {
                            return $query->whereRaw("requisition.requisition_code like '%$searchbyrqcode%' ");
                        })
                        ->when($searchbysupplier, function ($query) use ($searchbysupplier) {
                            return $query->whereRaw("(CONCAT(ac_party.code,' : ',ac_party.first_name) like '%$searchbysupplier%')");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(ord.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(ord.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordpocode, function ($query) use ($sortordpocode) {
                            return $query->orderby('ord.id', $sortordpocode);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ord.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);

                return view('purchase/received_grn/results', array('purchaseorders' => $purchaseorders));
            }
            return view('purchase/received_grn/index', array('purchaseorders' => $purchaseorders));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('purchase');
        }
    }
    
    public function view($id){
        try {
            $id = \Crypt::decrypt($id);

            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            $orderdata = DB::table('ac_purchase_order as order')
                            ->select('order.*', 'supplier.first_name as supplierfname', 'supplier.code as suppliercode')
                            ->leftjoin('ac_party as supplier', function($join) {
                                $join->on('order.to_ledger_id', '=', 'supplier.id');
                                $join->on('order.to_ledger_type', '=', 'supplier.party_type');
                            })
                            ->where(['order.id' => $id])->first();

            $orderitems = DB::table('requisition_items')
                    ->select('requisition_items.*', 'inventory.product_code', 'inventory.name as productname', 'units.name as unit', 'track_manufacturing', 'track_expiry','inventory.company_id as itemcompany')
                    ->leftjoin('inventory', 'requisition_items.requisition_item_id', '=', 'inventory.id')
                    ->leftjoin('units', 'requisition_items.alternate_unit_id', '=', 'units.id')
                    ->where('requisition_id', '=', $orderdata->requisition_id)
                    ->get();
            
            $trackmfd = 0;
            $trackexp = 0;
            foreach ($orderitems as $item) {
                if ($item->track_manufacturing == 1) {
                    $trackmfd = 1;
                }

                if ($item->track_expiry == 1) {
                    $trackexp = 1;
                }
                
                $item->entered_stock = DB::table('st_stock_info as st')
                            ->select('st.purchase_quantity')
                            ->where('purchase_order_id','=',$id)
                            ->where('item_id','=',$item->requisition_item_id)
                            ->where('status','=',1)->sum('st.purchase_quantity');
                    
            }

            $warehouses = DB::table('master_resources')
                            ->select('master_resources.id', 'master_resources.name')
                            ->where(['resource_type' => 'WAREHOUSE'])
                            ->where('status', '=', 1)
                            ->where('company_id', '=', $company_id)
                            ->orderby('name', 'ASC')->get();
            
            $prevgrns = DB::table('st_stock_info as st')
                    ->select('st.batch_code','m.name')
                    ->leftjoin('master_resources as m', 'st.stock_area_id', '=', 'm.id')
                    ->where('st.purchase_order_id', '=', $id)
                    ->groupBy('st.batch_code')
                    ->orderBy('st.created_at','DESC')
                    ->get();
            
            foreach ($prevgrns as $grn) {
                $grn->items=DB::table('st_stock_info as st')
                        ->select('st.id','st.mfg_date','st.exp_date','st.purchase_quantity', 'inventory.product_code', 'inventory.name as productname', 'units.name as unit')
                        ->leftjoin('inventory', 'st.item_id', '=', 'inventory.id')
                        ->leftjoin('units', 'st.purchase_unit', '=', 'units.id')
                        ->where('st.batch_code', '=', $grn->batch_code)
                        ->get();
            }

            $stokfuns = new Stockfunctions();
            $batchcode = $stokfuns->generateGrnCode(1);
            return view('purchase/received_grn/view', array('orderdata' => $orderdata, 'orderitems' => $orderitems,
                            'warehouses' => $warehouses, 'batchcode' => $batchcode, 'trackmfd' => $trackmfd, 
                            'trackexp' => $trackexp,'prevgrns'=>$prevgrns));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('purchase/received_grn');
        }
    }
}
