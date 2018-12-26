<?php

namespace App\Http\Controllers\Finance;

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
use App\Models\Payment_advice;
use App\Models\Payment_advice_details;
use App\Models\Payment_advice_activity;
use Customhelper;
use DB;
use PDF;
use Excel;

class ReceivedpaymentsController extends Controller {
    

    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $payments = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*',db::raw("(case when ac_payment_advice.payment_type=1 then 'Cheque' when ac_payment_advice.payment_type=2 then 'Cash' else 'Online' end) as paymode"))
                    ->whereRaw("responsible_emp_id=$login_id AND ac_payment_advice.status=2 and remittance_number is null")
                    ->orderby('ac_payment_advice.created_at', 'DESC')
                    ->paginate($paginate);
            
            $pendingPayments = DB::table('ac_payment_advice')
                    ->whereRaw("remittance_number IS NULL AND status=2 AND responsible_emp_id=$login_id")->count();
            
            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbymode = Input::get('searchbymode');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordcode = Input::get('sortordcode');
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
                if ($sortordcode=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $payments = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*',db::raw("(case when ac_payment_advice.payment_type=1 then 'Cheque' when ac_payment_advice.payment_type=2 then 'Cash' else 'Online' end) as paymode"))
                        ->whereRaw("responsible_emp_id=$login_id AND ac_payment_advice.status=2 and remittance_number is null")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
                        })
                        ->when($searchbymode, function ($query) use ($searchbymode) {
                            return $query->whereRaw("ac_payment_advice.payment_type=$searchbymode");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(ac_payment_advice.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(ac_payment_advice.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('ac_payment_advice.id', $sortordcode);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('ac_payment_advice.created_at', $sortorddate);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('finance/received_payments/results', array('payments' => $payments));
            }
            return view('finance/received_payments/index', array('payments' => $payments,'pendingPayments'=>$pendingPayments));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice/outbox');
        }
    }
    
    public function view($id) {
        try{
            $id = \Crypt::decrypt($id);
            $common = new Commonfunctions();
            $paymentdata = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*',
                            'employees.first_name as respname','employees.username as respcode',
                            'supplier.first_name as suppliername','supplier.code as suppliercode',
                            'supplier.bank_account_number as supplier_acno','supplier.bank_swift_code as supplier_swiftcode',
                            'supplier.bank_beneficiary_name','supplier.bank_name','country.name as bankcountry',
                            'fromledger.first_name as fromledgername','fromledger.code as fromledgercode')
                    ->leftjoin('ac_party as supplier', function($join){
                        $join->on('ac_payment_advice.payment_to_id','=','supplier.id')
                        ->whereRaw("supplier.party_type='Supplier'");
                     })
                     ->leftjoin('country', 'supplier.bank_country','=','country.id')
                    ->leftjoin('ac_accounts as fromledger', function($join){
                        $join->on('ac_payment_advice.payment_from_id','=','fromledger.type_id')
                        ->where('fromledger.type', '=', 'General Ledger');
                     })
                    ->leftjoin('employees', 'ac_payment_advice.responsible_emp_id','=','employees.id')
                    ->where(['ac_payment_advice.id' => $id])->first();
                         
            $paymentrequests = DB::table('ac_payment_advice_details as pad')
                        ->select('pad.*','requisition_types.name as req_name','requisition.party_id as employee_id')
                        ->leftjoin('requisition','pad.requisition_id', '=', 'requisition.id')
                        ->leftjoin('requisition_types','requisition.requisition_type', '=','requisition_types.id' )
                        ->where(['pad.payment_advice_id' => $id])->get();
            
            $employee_data=array();
            if (count($paymentrequests) == 1) {
                if ($paymentrequests[0]->req_name == 'Advance Payment Requisition' || $paymentrequests[0]->req_name == 'Owner Drawings Requisition') {
                    $employee_id = $paymentrequests[0]->employee_id;
                    $employee_data = DB::table('employees')
                            ->select('employees.id', 'username as code', 'employees.first_name', 'employees.alias_name')
                            ->whereRaw("employees.id=$employee_id")
                            ->first();
                }
            }
            
            foreach ($paymentrequests as $request) {
                $request->strrequesturl=$common->getRequestViewUrlFromPaymentAdvice($request->req_name);
            }
            
            $arrrequisitionids = $paymentrequests->pluck("requisition_id")->toArray();
            
            $requisitiontotal = DB::table('requisition')
                    ->select('requisition.total_price')
                    ->whereIn('id',$arrrequisitionids)
                    ->sum("total_price");
            
            $paymentdata->requisitiontotal=$requisitiontotal;
            
            $action_takers = DB::table('ac_payment_advice_activity as pa_act')
                    ->select('pa_act.created_at','pa_act.action','pa_act.comments', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"),DB::raw("case when action=1 then 'Approved' when action=2 then 'Rejected' end as action"))
                    ->leftjoin('employees as empl','pa_act.action_taker_id', '=', 'empl.id')
                    ->where('pa_act.payment_advice_id', '=', $id)->get();
            
           
            $strUrl = url()->current();
            if (strpos($strUrl, 'remittance_report')) {
                $blnshobackbutton = 0;
            }else {
                $blnshobackbutton =1;
            }
            
            return view('finance/received_payments/view', array('paymentdata'=>$paymentdata,'paymentrequests'=>$paymentrequests,'action_takers'=>$action_takers,'blnshobackbutton'=>$blnshobackbutton,'employee_data'=>$employee_data));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('finance/received_payments');
        }
    }



    public function update() {
        try {
            $payment_id = Input::get('payment_id');
            $remittancenumber = Input::get('remittancenumber');
            $description = Input::get('description');
            $beneficiaryname = Input::get('beneficiary_name');
            $remittance_url = Input::get('remittance_url');
            $subfolder = "remittanceimages";
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            
            $pic_url = '';
            if (Input::hasFile('remitanceimage')) {
                $checkimage = Input::file('remitanceimage');
                $extension = time() . '.' . $checkimage->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/pics/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($checkimage), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
            }
            
            if($pic_url==''){
                $pic_url=$remittance_url;
            }
            
            DB::table('ac_payment_advice')
                ->where(['id' => $payment_id])
                ->update(['remittance_number' => $remittancenumber,'remittance_image'=>$pic_url,'remittance_desc'=>$description,'remitted_date'=>date('Y-m-d'),'beneficiary_name'=>$beneficiaryname]);
            
            Toastr::success('Remittance Details Saved Successfully !', $title = null, $options = []);
            return Redirect::to('finance/received_payments');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
           return Redirect::to('finance/received_payments');
        }
    }
    
    //Generatr PDF, EXCEL
    public function exportdata() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbymode = Input::get('searchbymode');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $sortordcode = Input::get('sortordcode');
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
        if ($sortordcode=='') {
            $sortOrdDefault = 'DESC';
        }

        $payments = DB::table('ac_payment_advice')
                ->select('ac_payment_advice.*',db::raw("(case when ac_payment_advice.payment_type=1 then 'Cheque' when ac_payment_advice.payment_type=2 then 'Cash' else 'Online' end) as paymode"))
                ->whereRaw("responsible_emp_id=$login_id AND ac_payment_advice.status=2 and remittance_number is null")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
                })
                ->when($searchbymode, function ($query) use ($searchbymode) {
                    return $query->whereRaw("ac_payment_advice.payment_type=$searchbymode ");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(ac_payment_advice.created_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(ac_payment_advice.created_at)<= '$createdatto' ");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('ac_payment_advice.id', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('ac_payment_advice.created_at', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Received_Payments', function($excel) use($payments) {
                // Set the title
                $excel->setTitle('Received Payments');

                $excel->sheet('Received Payments', function($sheet) use($payments) {
                    // Sheet manipulation

                    $sheet->setCellValue('B3', 'Received Payments (Inbox)');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Payment Code', 'Date', 'Payment Mode', 'Amount'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($payments); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $payments[$i]->payment_code);
                        $sheet->setCellValue('B' . $chrRow, date("d-m-Y", strtotime($payments[$i]->created_at)));
                        $sheet->setCellValue('C' . $chrRow, $payments[$i]->paymode);
                        $sheet->setCellValue('D' . $chrRow, round($payments[$i]->total_amount,2));
                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }

}