<?php

namespace App\Http\Controllers\Requisitions;

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
use App\Services\Commonfunctions;
use App\Services\Paymentadvice;
use Customhelper;
use Exception;
use DB;
use PDF;
use App;
use Excel;
use Mail;

class PurchaseorderlistController extends Controller {

    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            $purchaseorders = DB::table('ac_purchase_order as ord')
                    ->select('ord.*', 'pa.payment_code', 'requisition.total_price', 'requisition.requisition_code')
                    ->leftjoin('ac_payment_advice as pa', 'ord.payment_advice_id', '=', 'pa.id')
                    ->leftjoin('requisition', 'ord.requisition_id', '=', 'requisition.id')
                    ->where('ord.status', '=', 1)
                    ->where('ord.order_status', '=', 1)
                    ->where('ord.order_type', '=', 2)
                    ->orderby('ord.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbyordcode = Input::get('searchbyordcode');
                $searchbyrqcode = Input::get('searchbyrqcode');
                $searchbypacode = Input::get('searchbypacode');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordpocode = Input::get('sortordpocode');
                $sortordpacode = Input::get('sortordpacode');
                $sortorddate = Input::get('sortorddate');

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
                if ($sortordpocode == '' && $sortorddate == '' && $sortordpacode == '') {
                    $sortOrdDefault = 'DESC';
                }

                $purchaseorders = DB::table('ac_purchase_order as ord')
                        ->select('ord.*', 'pa.payment_code', 'requisition.total_price', 'requisition.requisition_code')
                        ->leftjoin('ac_payment_advice as pa', 'ord.payment_advice_id', '=', 'pa.id')
                        ->leftjoin('requisition', 'ord.requisition_id', '=', 'requisition.id')
                        ->where('ord.status', '=', 1)
                        ->where('ord.order_type', '=', 2)
                        ->where('ord.order_status', '=', 1)
                        ->when($searchbyordcode, function ($query) use ($searchbyordcode) {
                            return $query->whereRaw("ord.order_code like '%$searchbyordcode%' ");
                        })
                        ->when($searchbyrqcode, function ($query) use ($searchbyrqcode) {
                            return $query->whereRaw("requisition.requisition_code like '%$searchbyrqcode%' ");
                        })
                        ->when($searchbypacode, function ($query) use ($searchbypacode) {
                            return $query->whereRaw("pa.payment_code like '%$searchbypacode%' ");
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
                        ->when($sortordpacode, function ($query) use ($sortordpacode) {
                            return $query->orderby('pa.id', $sortordpacode);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('ord.created_at', $sortorddate);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ord.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);

                return view('requisitions/purchase_order_list/results', array('purchaseorders' => $purchaseorders));
            }
            return view('requisitions/purchase_order_list/index', array('purchaseorders' => $purchaseorders));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/purchase_order_list');
        }
    }

    public function view($id) {
        try {
            $id = \Crypt::decrypt($id);

            $orderdata = DB::table('ac_purchase_order as order')
                            ->select('order.*', 'supplier.first_name as supplierfname', 'supplier.alias_name as supplieraname', 'supplier.code as suppliercode', 'supplier.address as supplier_address', 'supplier_pin', 'country.name as supnation', 'supplier.bank_beneficiary_name', 'supplier.bank_iban_no', 'supplier.bank_branch_name', 'supplier.bank_account_number', 'supplier.bank_city', 'supplier.bank_swift_code', 'supplier.contact_person', 'supplier.contact_number', 'bank_nation.name as banknation', 'supplier.company_name', 'supplier.bank_name', 'supplier.cr_number', 'requisition.total_vat', 'employees.username as empcode', 'employees.first_name as empname', 'employees.alias_name as empaname', 'master_resources.name as jobpos', db::raw("COALESCE(supplier.email,supplier.contact_email,'') as supemail"), db::raw("COALESCE(supplier.mobile_number,'') as supmob")
                                    ,DB::raw("(select if((count(*) > 0) , 'true','false') FROM st_po_actions spa where spa.purchase_order_id = order.id AND spa.actions = 2) as warehouse_status"))
                            ->leftjoin('ac_party as supplier', function($join) {
                                $join->on('order.to_ledger_id', '=', 'supplier.id');
                                $join->on('order.to_ledger_type', '=', 'supplier.party_type');
                            })
                            ->leftjoin('country', 'supplier.nationality', '=', 'country.id')
                            ->leftjoin('country as bank_nation', 'supplier.bank_country', '=', 'bank_nation.id')
                            ->leftjoin('requisition', 'order.requisition_id', '=', 'requisition.id')
                            ->leftjoin('employees', 'requisition.created_by', '=', 'employees.id')
                            ->leftjoin('master_resources', 'employees.job_position', '=', 'master_resources.id')
                            ->where(['order.id' => $id])->first();

            $requisitionitems = DB::table('requisition_items')
                    ->select('requisition_items.*', 'inventory.product_code', 'inventory.name as productname', 'units.name as unit')
                    ->leftjoin('inventory', 'requisition_items.requisition_item_id', '=', 'inventory.id')
                    ->leftjoin('units', 'requisition_items.alternate_unit_id', '=', 'units.id')
                    ->where('requisition_id', '=', $orderdata->requisition_id)
                    ->get();

            $action_takers = DB::table('requisition_activity as req_a')
                            ->select('req_a.comments', 'empl.username as code', 'master_resources.name as jobpos', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"))
                            ->leftjoin('employees as empl', 'req_a.actor_id', '=', 'empl.id')
                            ->leftjoin('master_resources', 'empl.job_position', '=', 'master_resources.id')
                            ->where('req_a.requisition_id', '=', $orderdata->requisition_id)->where('req_a.status', '=', 1)->get();

            $orderdata->ordertotal = $requisitionitems->sum('total_price');

            return view('requisitions/purchase_order_list/view', array('orderdata' => $orderdata, 'requisitionitems' => $requisitionitems, 'action_takers' => $action_takers));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/purchase_order_list');
        }
    }

    public function mailto_supplier() {
        try {

            $id = Input::get('po_id');
            $log_id = Session::get("login_id");
//            $orderdata = DB::table('ac_purchase_order as order')
//                            ->select('order.*', 'supplier.first_name as supplierfname', 'supplier.alias_name as supplieraname', 'supplier.code as suppliercode', 'supplier.address as supplier_address', 'supplier_pin', 'country.name as supnation', 'supplier.bank_beneficiary_name', 'supplier.bank_iban_no', 'supplier.bank_branch_name', 'supplier.contact_email as sendmail', 'supplier.bank_account_number', 'supplier.bank_city', 'supplier.bank_swift_code', 'supplier.contact_person', 'supplier.contact_number', 'bank_nation.name as banknation', 'supplier.company_name', 'supplier.bank_name', 'supplier.cr_number', 'requisition.total_vat', 'employees.username as empcode', 'employees.first_name as empname', 'employees.alias_name as empaname', 'master_resources.name as jobpos', db::raw("COALESCE(supplier.email,supplier.contact_email,'') as supemail"), db::raw("COALESCE(supplier.mobile_number,'') as supmob"))
//                            ->leftjoin('ac_party as supplier', function($join) {
//                                $join->on('order.to_ledger_id', '=', 'supplier.id');
//                                $join->on('order.to_ledger_type', '=', 'supplier.party_type');
//                            })
//                            ->leftjoin('country', 'supplier.nationality', '=', 'country.id')
//                            ->leftjoin('country as bank_nation', 'supplier.bank_country', '=', 'bank_nation.id')
//                            ->leftjoin('requisition', 'order.requisition_id', '=', 'requisition.id')
//                            ->leftjoin('employees', 'requisition.created_by', '=', 'employees.id')
//                            ->leftjoin('master_resources', 'employees.job_position', '=', 'master_resources.id')
//                            ->where(['order.id' => $id])->first();
//
//            $requisitionitems = DB::table('requisition_items')
//                    ->select('requisition_items.*', 'inventory.product_code', 'inventory.name as productname', 'units.name as unit')
//                    ->leftjoin('inventory', 'requisition_items.requisition_item_id', '=', 'inventory.id')
//                    ->leftjoin('units', 'requisition_items.alternate_unit_id', '=', 'units.id')
//                    ->where('requisition_id', '=', $orderdata->requisition_id)
//                    ->get();
//
//            $action_takers = DB::table('requisition_activity as req_a')
//                            ->select('req_a.comments', 'empl.username as code', 'master_resources.name as jobpos', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"))
//                            ->leftjoin('employees as empl', 'req_a.actor_id', '=', 'empl.id')
//                            ->leftjoin('master_resources', 'empl.job_position', '=', 'master_resources.id')
//                            ->where('req_a.requisition_id', '=', $orderdata->requisition_id)->where('req_a.status', '=', 1)->get();

//            if ($orderdata->sendmail) {

//                Mail::send('emailtemplates.plain', array('strmessage' => ''), function($message) use($orderdata, $requisitionitems, $action_takers) {
//                    $pdf = PDF::loadView('requisitions.purchase_order_list.print', array('orderdata' => $orderdata, 'requisitionitems' => $requisitionitems, 'action_takers' => $action_takers));
//                    $message->to($orderdata->sendmail)->subject($orderdata->order_code . " / " . date('d-m-Y', strtotime($orderdata->created_at)));
//                    $message->attachData($pdf->output(), 'purchaseorder.pdf');
//                });
//
//                if (Mail::failures()) {
//                    return -1;
//                }

                DB::table('ac_purchase_order')
                        ->where(['id' => $id])
                        ->update(['mailed_status' => 1]);
                
                DB::table('st_po_actions')
                        ->insert(['purchase_order_id'=>$id,"actions"=>'1',"updated_by"=>$log_id]);

                return 1;
//            } else {
//                return -1;
//            }
        } catch (\Exception $e) {

            return -1;
        }
    }
    
        public function download_pfd(){
        $id = Input::get('po_id');
            $log_id = Session::get("login_id");
            $orderdata = DB::table('ac_purchase_order as order')
                            ->select('order.*', 'supplier.first_name as supplierfname', 'supplier.alias_name as supplieraname', 'supplier.code as suppliercode', 'supplier.address as supplier_address', 'supplier_pin', 'country.name as supnation', 'supplier.bank_beneficiary_name', 'supplier.bank_iban_no', 'supplier.bank_branch_name', 'supplier.contact_email as sendmail', 'supplier.bank_account_number', 'supplier.bank_city', 'supplier.bank_swift_code', 'supplier.contact_person', 'supplier.contact_number', 'bank_nation.name as banknation', 'supplier.company_name', 'supplier.bank_name', 'supplier.cr_number', 'requisition.total_vat', 'employees.username as empcode', 'employees.first_name as empname', 'employees.alias_name as empaname', 'master_resources.name as jobpos', db::raw("COALESCE(supplier.email,supplier.contact_email,'') as supemail"), db::raw("COALESCE(supplier.mobile_number,'') as supmob"))
                            ->leftjoin('ac_party as supplier', function($join) {
                                $join->on('order.to_ledger_id', '=', 'supplier.id');
                                $join->on('order.to_ledger_type', '=', 'supplier.party_type');
                            })
                            ->leftjoin('country', 'supplier.nationality', '=', 'country.id')
                            ->leftjoin('country as bank_nation', 'supplier.bank_country', '=', 'bank_nation.id')
                            ->leftjoin('requisition', 'order.requisition_id', '=', 'requisition.id')
                            ->leftjoin('employees', 'requisition.created_by', '=', 'employees.id')
                            ->leftjoin('master_resources', 'employees.job_position', '=', 'master_resources.id')
                            ->where(['order.id' => $id])->first();

            $requisitionitems = DB::table('requisition_items')
                    ->select('requisition_items.*', 'inventory.product_code', 'inventory.name as productname', 'units.name as unit')
                    ->leftjoin('inventory', 'requisition_items.requisition_item_id', '=', 'inventory.id')
                    ->leftjoin('units', 'requisition_items.alternate_unit_id', '=', 'units.id')
                    ->where('requisition_id', '=', $orderdata->requisition_id)
                    ->get();

            $action_takers = DB::table('requisition_activity as req_a')
                            ->select('req_a.comments', 'empl.username as code', 'master_resources.name as jobpos', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"))
                            ->leftjoin('employees as empl', 'req_a.actor_id', '=', 'empl.id')
                            ->leftjoin('master_resources', 'empl.job_position', '=', 'master_resources.id')
                            ->where('req_a.requisition_id', '=', $orderdata->requisition_id)->where('req_a.status', '=', 1)->get();

        $print = view('requisitions.purchase_order_list.print', array('orderdata' => $orderdata, 'requisitionitems' => $requisitionitems, 'action_takers' => $action_takers));
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($print);
//        return $pdf->stream();;
        return $pdf->download("purchase_order.pdf");
    }

    // Generate PDF funcion
    public function exportdata() {

        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbyordcode = Input::get('searchbyordcode');
        $searchbyrqcode = Input::get('searchbyrqcode');
        $searchbypacode = Input::get('searchbypacode');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $sortordpocode = Input::get('sortordpocode');
        $sortordpacode = Input::get('sortordpacode');
        $sortorddate = Input::get('sortorddate');

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
        if ($sortordpocode == '' && $sortorddate == '' && $sortordpacode == '') {
            $sortOrdDefault = 'DESC';
        }

        $purchaseorders = DB::table('ac_purchase_order as ord')
                ->select('ord.*', 'pa.payment_code', 'requisition.total_price', 'requisition.requisition_code')
                ->leftjoin('ac_payment_advice as pa', 'ord.payment_advice_id', '=', 'pa.id')
                ->leftjoin('requisition', 'ord.requisition_id', '=', 'requisition.id')
                ->where('ord.status', '=', 1)
                ->where('ord.order_status', '=', 1)
                ->where('ord.order_type', '=', 2)
                ->when($searchbyordcode, function ($query) use ($searchbyordcode) {
                    return $query->whereRaw("ord.order_code like '%$searchbyordcode%' ");
                })
                ->when($searchbyrqcode, function ($query) use ($searchbyrqcode) {
                    return $query->whereRaw("requisition.requisition_code like '%$searchbyrqcode%' ");
                })
                ->when($searchbypacode, function ($query) use ($searchbypacode) {
                    return $query->whereRaw("pa.payment_code like '%$searchbypacode%' ");
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
                ->when($sortordpacode, function ($query) use ($sortordpacode) {
                    return $query->orderby('pa.id', $sortordpacode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('ord.created_at', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('ord.created_at', $sortOrdDefault);
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Local PO Action', function($excel) use($purchaseorders) {
                // Set the title
                $excel->setTitle('Local PO Action');

                $excel->sheet('Local PO Action', function($sheet) use($purchaseorders) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Local PO Action');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Order Code', 'Requisition Code', 'Payment Code', 'Date', 'Advance Amount', 'Total Amount '));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($purchaseorders); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $purchaseorders[$i]->order_code);
                        $sheet->setCellValue('B' . $chrRow, $purchaseorders[$i]->requisition_code);
                        $sheet->setCellValue('C' . $chrRow, $purchaseorders[$i]->payment_code);
                        $sheet->setCellValue('D' . $chrRow, date("d-m-Y", strtotime($purchaseorders[$i]->created_at)));
                        $sheet->setCellValue('E' . $chrRow, $purchaseorders[$i]->amount);
                        $sheet->setCellValue('F' . $chrRow, $purchaseorders[$i]->total_price);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }
    
    public function send_to_warehouse(){
        try{
            $id = Input::get("po_id");
            $log_id = Session::get("login_id");
            DB::table("st_po_actions")
                    ->insert(['purchase_order_id'=>$id,"actions"=>'2',"updated_by"=>$log_id]);
            DB::table("ac_purchase_order")
                    ->where("id",$id)
                    ->update(["send_warehouse_status"=>1]);
            return '1';
        }
        catch(\Exception $e){
            return '-1';
        }
    }

}
