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
use App\Models\Payment_advice;
use App\Models\Payment_advice_details;
use App\Models\Payment_advice_activity;
use Customhelper;
use DB;
use PDF;
use Excel;

class PaymentadviceController extends Controller {
    
    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $requisitions = DB::table('requisition')
                    ->select('requisition.*', 'requisition_types.name')
                    ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                    ->where(['next_approver_id' => $login_id])
                    ->whereRaw("requisition.status=4 AND convert_to_payment=1 AND payment_generated=2")
                    ->orderby('requisition.updated_at', 'DESC')
                    ->paginate($paginate);
            
            $requisitionTypes = DB::table('requisition_types')
                    ->select('requisition_types.*')
                    ->where('requisition_types.status','=',1)
                    ->get();

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbyname = Input::get('searchbyname');
                $searchbytype = Input::get('searchbytype');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordtype = Input::get('sortordtype');
                $sortordcode = Input::get('sortordcode');

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
                if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('requisition')
                        ->select('requisition.*', 'requisition_types.name')
                        ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                        ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status=4 AND convert_to_payment=1 AND payment_generated=2")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                        })
                        ->when($searchbyname, function ($query) use ($searchbyname) {
                            return $query->whereRaw("requisition.title like '$searchbyname%' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.updated_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.updated_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortordtype, function ($query) use ($sortordtype) {
                            return $query->orderby('requisition_types.name', $sortordtype);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.updated_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/payment_advice/results', array('requisitions' => $requisitions));
            }
            return view('requisitions/payment_advice/index', array('requisitions' => $requisitions,'requisitionTypes'=>$requisitionTypes));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function inbox(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $payments = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*','employees.first_name')
                    ->leftjoin('employees', 'ac_payment_advice.created_by','=','employees.id')
                    ->where(['next_approver_id' => $login_id])
                    ->whereRaw("ac_payment_advice.status=1")
                    ->orderby('ac_payment_advice.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbyname = Input::get('searchbyname');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordcode = Input::get('sortordcode');

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
                if ($sortordtitle == '' && $sortordcode=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $payments = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*','employees.first_name')
                        ->leftjoin('employees', 'ac_payment_advice.created_by','=','employees.id')
                        ->where(['next_approver_id' => $login_id])
                        ->whereRaw("ac_payment_advice.status=1")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
                        })
                        ->when($searchbyname, function ($query) use ($searchbyname) {
                            return $query->whereRaw("requisition.title like '$searchbyname%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(ac_payment_advice.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(ac_payment_advice.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('ac_payment_advice.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/payment_approval/inbox_results', array('payments' => $payments));
            }
            return view('requisitions/payment_approval/inbox', array('payments' => $payments));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_approval/inbox');
        }
    }
    
    public function outbox(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $payments = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*',DB::raw("case when status=1 then 'Pending' when status=2 then 'Approved' when status=3 then 'Rejected' end as status"))
                    ->where(['created_by' => $login_id])
                    ->orderby('ac_payment_advice.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
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
                        ->select('ac_payment_advice.*',DB::raw("case when status=1 then 'Pending' when status=2 then 'Approved' when status=3 then 'Rejected' end as status"))
                        ->where(['created_by' => $login_id])
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
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
                        
                return view('requisitions/payment_advice/outbox_results', array('payments' => $payments));
            }
            return view('requisitions/payment_advice/outbox', array('payments' => $payments));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice/outbox');
        }
    }
    
    public function userwisepayments(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $payments = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*','employees.first_name','activity.created_at as createdat',db::raw("(case when activity.action=1 then 'Approved' when activity.action=2 then 'Rejected' else '' end) as actionstatus"))
                    ->join('ac_payment_advice_activity as activity', function($join) {
                        $join->on('ac_payment_advice.id','=','activity.payment_advice_id')
                        ->whereRaw("activity.status=1");
                    })
                    ->leftjoin('employees', 'ac_payment_advice.created_by','=','employees.id')
                    ->whereRaw("activity.action_taker_id=$login_id")
                    ->groupBy('activity.payment_advice_id','activity.action_taker_id','activity.action')
                    ->orderby('activity.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
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
                if ($sortorddate == '' && $sortordcode=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $payments = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*','employees.first_name','activity.created_at as createdat',db::raw("(case when activity.action=1 then 'Approved' when activity.action=2 then 'Rejected' else '' end) as actionstatus"))
                    ->join('ac_payment_advice_activity as activity', function($join) {
                        $join->on('ac_payment_advice.id','=','activity.payment_advice_id')
                        ->whereRaw("activity.status=1");
                    })
                    ->leftjoin('employees', 'ac_payment_advice.created_by','=','employees.id')
                    ->whereRaw("activity.action_taker_id=$login_id")
                    ->groupBy('activity.payment_advice_id','activity.action_taker_id','activity.action')
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
                    })
                    ->when($createdatfrom, function ($query) use ($createdatfrom) {
                        return $query->whereRaw("date(activity.created_at)>= '$createdatfrom' ");
                    })
                    ->when($createdatto, function ($query) use ($createdatto) {
                        return $query->whereRaw("date(activity.created_at)<= '$createdatto' ");
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('activity.created_at', $sortorddate);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('activity.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);

                return view('requisitions/userwise_payments/results', array('payments' => $payments));
            }
            return view('requisitions/userwise_payments/index', array('payments' => $payments));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function add($id) {
        try {
            $id = \Crypt::decrypt($id);
            $common = new Commonfunctions();
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $requisitiondata = DB::table('requisition')
                    ->select('requisition.*','p.first_name','p.code','p.bank_swift_code','p.bank_account_number','requisition_types.name as req_name','p.bank_beneficiary_name','p.bank_name','country.name as bankcountry')
                    ->leftjoin('requisition_types','requisition_types.id', '=', 'requisition.requisition_type')
                    ->leftjoin('ac_party as p', function($join) {
                            $join->on('requisition.party_id', '=', 'p.id')->whereRaw("requisition.party_type='Supplier'");
                    })
                    ->leftjoin('country', 'p.bank_country','=','country.id')
                    ->where(['requisition.id' => $id])->first();
            
            $strrequesturl = $common->getRequestViewUrlFromPaymentAdvice($requisitiondata->req_name);
            $requisitiondata->strrequesturl=$strrequesturl;
            
            $gledgers = DB::table('ac_accounts')
                    ->select('ac_accounts.type_id as accountid', 'code', 'ac_accounts.first_name', 'ac_accounts.type')
                    ->whereRaw("ac_accounts.status=1 AND type='General Ledger'")
                    ->orderby('ac_accounts.first_name', 'ASC')
                    ->get();
            
            $paymentcode = $common->generatePaymentAdviceCode();
            
            $employees = DB::table('employees')
                    ->select('employees.id','username as code','employees.first_name','employees.alias_name')
                    ->whereRaw("employees.status=1 AND id!=1")
                    ->orderby('employees.first_name','ASC')
                    ->get();
            
            $employee_data= DB::table('employees')
                    ->select('employees.id','username as code','employees.first_name','employees.alias_name')
                    ->whereRaw("employees.id=$requisitiondata->party_id")
                     ->orderby('employees.first_name','ASC')
                    ->first();
            
            $common = new Commonfunctions();
            $levelstatus=$common->checkLevelAuthenticationForPayment(1);
            if($levelstatus==-1){
                Toastr::error('You are not authorised to do this action !', $title = null, $options = []);
                return Redirect::to('requisitions/payment_advice');
            }
            
            $submitteddata = DB::table('ac_payment_advice_details')
                    ->select('ac_payment_advice_details.*')
                    ->leftjoin('ac_payment_advice','payment_advice_id', '=', 'ac_payment_advice.id')
                    ->whereRaw("requisition_id=$id AND created_by=$login_id AND ac_payment_advice.status!=3")
                    ->get();
            
            if(count($submitteddata)>0){
                Toastr::success('You have already generated the payment advice !', $title = null, $options = []);
                return Redirect::to('requisitions/payment_advice');
            }
            
            return view('requisitions/payment_advice/add', array('requisitiondata'=>$requisitiondata,'paymentcode'=>$paymentcode,'employees'=>$employees,'gledgers'=>$gledgers,'employee_data'=>$employee_data));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice');
        }
    }

    public function store() {
        try {
            $arraData = Input::all();
            $subfolder = $arraData['payment_code'];
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            
            $pic_url = '';
            if (Input::hasFile('checkimage')) {
                $checkimage = Input::file('checkimage');
                $extension = time() . '.' . $checkimage->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/pics/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($checkimage), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
            }
            
            $arraData['checkimage']=$pic_url;
            if($arraData['requsition_name'] == 'Owner Drawings Requisition'){
                $arraData['is_drawing'] = 1;
            }else{
                $arraData['is_drawing'] = 2;
            }
            
            $arraDetails[]=(object) array("requisition_id"=>$arraData['requisition_id'],"requisition_code"=>$arraData['requisition_code'],"outstanding_amount"=>$arraData['outstanding_amount'],"pay_amount"=>$arraData['payamount']);
           
            $req_id=$arraData['requisition_id'];
            $paymentcount = DB::table('ac_payment_advice_details')
                    ->leftjoin('ac_payment_advice','payment_advice_id', '=', 'ac_payment_advice.id')
                    ->whereRaw("ac_payment_advice_details.requisition_id=$req_id AND ac_payment_advice.status!=3")->count();
                      
            if($paymentcount>0){
                Toastr::error('Payment advice already generated !', $title = null, $options = []);
                return Redirect::to('requisitions/payment_advice');
            }
            
            $payment = new Paymentadvice();
            $createpurchaseorder=1;
            $result = $payment->generatePaymentAdvice($arraData,$arraDetails,$createpurchaseorder);

            Toastr::success('Payment Advice Saved Successfully !', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice');
        } catch (\Exception $e) {
          
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice');
        }
    }
    
    public function view($id) {
        try{
            $id = \Crypt::decrypt($id);
            $common = new Commonfunctions();
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
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
            
            $action_taker_count = DB::table('ac_payment_advice_activity as act')
                    ->whereRaw("act.payment_advice_id=$id AND action_taker_id=$login_id AND status=1")->count();
            
            $isactiontaken="No";
            if($action_taker_count>0 || $paymentdata->status!=1){
                $isactiontaken="Yes";
            }
            
            $levelstatus=$common->checkLevelAuthenticationForPayment($paymentdata->level);
            if($levelstatus==-1){
                Toastr::error('You are not authorised to do this action !', $title = null, $options = []);
                return Redirect::to('requisitions/payment_approval/inbox');
            }
            
            return view('requisitions/payment_approval/view', array('paymentdata'=>$paymentdata,'paymentrequests'=>$paymentrequests,'isactiontaken'=>$isactiontaken,'employee_data'=>$employee_data));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_approval/inbox');
        }
    }
    
    public function listview($id) {
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
                    ->where('pa_act.payment_advice_id', '=', $id)->whereRaw("pa_act.status=1")->get();
            
            $next_action_takers_list = array();
            if($paymentdata->status!=3){
                $pending_actions = array();
                if($paymentdata->level != NULL){
                $pending_actions = DB::table('requisition_hierarchy as rh')
                        ->select('rh.approver_type', 'rh.approver_id', 'rh.level')
                        ->leftjoin('requisition_types','requisition_types.id', '=', 'rh.requisition_type_id')
                        ->where('rh.level','>=',$paymentdata->level)
                        ->where('rh.status','=', 1)
                        ->where('requisition_types.name','=', 'Payment Advice')
                        ->orderby('rh.level','ASC')
                        ->get();
                }

                foreach($pending_actions as $pending_action){  
                    $emp_data = DB::table('employees as empl')
                            ->select(DB::raw("concat(empl.first_name, ' ', empl.alias_name) as name"),'empl.id as empl_id')
                            ->where('empl.id', '=',$pending_action->approver_id)->first();

                    $pending_actions_takers['name'] = $emp_data->name;
                    $pending_actions_takers['id'] = $emp_data->empl_id;
                    $pending_actions_takers['action'] = "Waiting";

                    array_push($next_action_takers_list, $pending_actions_takers);
                }
            }
            
            return view('requisitions/payment_advice/view', array('paymentdata'=>$paymentdata,'paymentrequests'=>$paymentrequests,'action_takers'=>$action_takers,'next_action_takers_list'=>$next_action_takers_list,'employee_data'=>$employee_data));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice/outbox');
        }
    }

    public function approve_payment() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $common = new Commonfunctions();
            $payment_id = Input::get('payment_id');
            $comments = Input::get('comments');
            
            $validatemsg=$common->checkpaymentvalidation($payment_id);
            
            if($validatemsg!="success"){
                Toastr::error($validatemsg, $title = null, $options = []);
                return 1;
            }
            
            $payment_data = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*')
                    ->where(['id' => $payment_id])->first();
            
            $next_level = $payment_data->level + 1;
            $arrApprover = $common->getNextLevelApproverForPayment("Payment Advice",$next_level,$payment_data->created_by);
            
            if($arrApprover['status']==2){
                $payment = new Paymentadvice();
                $result1 = $payment->settleRequisitionWithPayment($payment_id);
                $result2 = $payment->generatePaymentTransaction($payment_id);
                $result3 = $payment->updatePurchaseOrder($payment_id);

                DB::table('ac_payment_advice')
                    ->where(['id' => $payment_id])
                    ->update(['status' => 2,'level'=>null,'next_approver_id'=>null]);

                if($result1==1){
                    $activitymodel = new Payment_advice_activity();
                    $activitymodel->payment_advice_id = $payment_id;
                    $activitymodel->action = 1;
                    $activitymodel->comments = $comments;
                    $activitymodel->action_taker_id = $login_id;
                    $activitymodel->status = 1;
                    $activitymodel->save();
                }

            } else{
                DB::table('ac_payment_advice')
                        ->where(['id' => $payment_id])
                        ->update(['next_approver_id' => $arrApprover['approver_id'],'level'=>$arrApprover['next_level']]);
                
                $activitymodel = new Payment_advice_activity();
                $activitymodel->payment_advice_id = $payment_id;
                $activitymodel->action = 1;
                $activitymodel->comments = $comments;
                $activitymodel->action_taker_id = $login_id;
                $activitymodel->status = 1;
                $activitymodel->save();
            }

            /*------------Send notification----------------*/   
            $to1 = $payment_data->created_by;
            $code = $payment_data->payment_code;
            $result=$common->notifyCreatedByPaymentAdvice("Payment Advice.",$code,$to1,$payment_id,1);
            if($arrApprover['approver_id']!=null){
                $result = $common->notifyNextActionTakerPaymentAdvice("Payment Advice", $code, $arrApprover['approver_id'], $payment_id, 1);
            }

            Toastr::success('Payment Approved Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) { 
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function reject_payment() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $payment_id = Input::get('payment_id');
            $comments = Input::get('comments');
            
            $common = new Commonfunctions();
            $validatemsg=$common->checkpaymentvalidation($payment_id);
            
            if($validatemsg!="success"){
                Toastr::error($validatemsg, $title = null, $options = []);
                return 1;
            }
            
            DB::table('ac_payment_advice')
                ->where(['id' => $payment_id])
                ->update(['status' => 3]);
            
            ///////////////////// insert in to payment advice activity ///////////////////////
            $activitymodel = new Payment_advice_activity();
            $activitymodel->payment_advice_id = $payment_id;
            $activitymodel->action = 2;
            $activitymodel->comments = $comments;
            $activitymodel->action_taker_id = $login_id;
            $activitymodel->status = 1;
            $activitymodel->save();
            
            $details = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.next_approver_id','ac_payment_advice.created_by','ac_payment_advice.payment_code','generate_purchase_order')
                    ->where('id','=',$payment_id)
                    ->first();
            
            $to1 = $details->created_by;
            $code = $details->payment_code;
            
            if($details->generate_purchase_order==1){
                $requestid = DB::table('ac_payment_advice_details')
                    ->select('ac_payment_advice_details.requisition_id')
                    ->where('payment_advice_id','=',$payment_id)
                    ->first();
                
                if(isset($requestid)){
                    $result=$common->convertToPaymentAdvice($requestid->requisition_id);
                    DB::table('requisition')
                        ->where(['id' => $requestid->requisition_id])
                        ->update(['payment_generated' => 2]);
                }
                
            }
            
            /*------------Send notification----------------*/   
            
            $result=$common->notifyCreatedByPaymentAdvice("Payment Advice.",$code,$to1,$payment_id,2);
            
            Toastr::success('Payment Rejected Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    //Generatr PDF, EXCEL
    public function exportdata() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbyname = Input::get('searchbyname');
        $searchbytype = Input::get('searchbytype');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $sortordtitle = Input::get('sortordtitle');
        $sortordtype = Input::get('sortordtype');
        $sortordcode = Input::get('sortordcode');

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
        if ($sortordtitle == '' && $sortordcode == '' && $sortordtype == '') {
            $sortOrdDefault = 'DESC';
        }

        $requisitions = DB::table('requisition')
                ->select('requisition.*', 'requisition_types.name')
                ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                ->where(['next_approver_id' => $login_id])
                ->whereRaw("requisition.status=4 AND convert_to_payment=1 AND payment_generated=2")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                })
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("requisition.title like '$searchbyname%' ");
                })
                ->when($searchbytype, function ($query) use ($searchbytype) {
                    return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(requisition.updated_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(requisition.updated_at)<= '$createdatto' ");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('requisition.requisition_code', $sortordcode);
                })
                ->when($sortordtitle, function ($query) use ($sortordtitle) {
                    return $query->orderby('requisition.title', $sortordtitle);
                })
                ->when($sortordtype, function ($query) use ($sortordtype) {
                    return $query->orderby('requisition_types.name', $sortordtype);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('requisition.updated_at', $sortOrdDefault);
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Requisitions For Payment Advice', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('Requisitions For Payment Advice');

                $excel->sheet('Requisitions For Payment Advice', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('B3', 'Requisitions For Payment Advice');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requsition Code', 'Title', 'Type', 'Date'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, $requisitions[$i]->name);
                        $sheet->setCellValue('D' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->updated_at)));
                        
                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } 
    }
    
    public function exportdata_approval(){
       if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
                $searchbyname = Input::get('searchbyname');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordcode = Input::get('sortordcode');

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
                if ($sortordtitle == '' && $sortordcode=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $payments = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*','employees.first_name')
                        ->leftjoin('employees', 'ac_payment_advice.created_by','=','employees.id')
                        ->where(['next_approver_id' => $login_id])
                        ->whereRaw("ac_payment_advice.status=1")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
                        })
                        ->when($searchbyname, function ($query) use ($searchbyname) {
                            return $query->whereRaw("requisition.title like '$searchbyname%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(ac_payment_advice.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(ac_payment_advice.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('ac_payment_advice.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                        })
                        ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Payment_Advices_For_Approval', function($excel) use($payments) {
                // Set the title
                $excel->setTitle('Payment Advices For Approval');

                $excel->sheet('Payment Advices For Approval', function($sheet) use($payments) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Payment Advices For Approval');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Payment Code', 'Date', 'Amount', 'Created By'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($payments); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $payments[$i]->payment_code);
                        $sheet->setCellValue('B' . $chrRow, date("d-m-Y", strtotime($payments[$i]->created_at)));
                        $sheet->setCellValue('C' . $chrRow, $payments[$i]->total_amount);
                        $sheet->setCellValue('D' . $chrRow, $payments[$i]->first_name);

                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } 
    }
    
    public function exportuserwispaymentsdata(){
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
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
        if ($sortorddate == '' && $sortordcode=='') {
            $sortOrdDefault = 'DESC';
        }

        $payments = DB::table('ac_payment_advice')
            ->select('ac_payment_advice.*','employees.first_name','activity.created_at as createdat',db::raw("(case when activity.action=1 then 'Approved' when activity.action=2 then 'Rejected' else '' end) as actionstatus"))
            ->join('ac_payment_advice_activity as activity', function($join) {
                $join->on('ac_payment_advice.id','=','activity.payment_advice_id')
                ->whereRaw("activity.status=1");
            })
            ->leftjoin('employees', 'ac_payment_advice.created_by','=','employees.id')
            ->whereRaw("activity.action_taker_id=$login_id")
            ->groupBy('activity.payment_advice_id','activity.action_taker_id','activity.action')
            ->when($searchbycode, function ($query) use ($searchbycode) {
                return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
            })
            ->when($createdatfrom, function ($query) use ($createdatfrom) {
                return $query->whereRaw("date(activity.created_at)>= '$createdatfrom' ");
            })
            ->when($createdatto, function ($query) use ($createdatto) {
                return $query->whereRaw("date(activity.created_at)<= '$createdatto' ");
            })
            ->when($sortordcode, function ($query) use ($sortordcode) {
                return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
            })
            ->when($sortorddate, function ($query) use ($sortorddate) {
                return $query->orderby('activity.created_at', $sortorddate);
            })
            ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                return $query->orderby('activity.created_at', $sortOrdDefault);
            })
            ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Payment_Approval_List', function($excel) use($payments) {
                // Set the title
                $excel->setTitle('Payment Approval List');

                $excel->sheet('Payment Approval List', function($sheet) use($payments) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Payment Approval List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Payment Code', 'Date', 'Action Taken', 'Created By'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($payments); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $payments[$i]->payment_code);
                        $sheet->setCellValue('B' . $chrRow, date("d-m-Y", strtotime($payments[$i]->createdat)));
                        $sheet->setCellValue('C' . $chrRow, $payments[$i]->actionstatus);
                        $sheet->setCellValue('D' . $chrRow, $payments[$i]->first_name);

                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } 
    }
    
    public function exportdata_paymentadvicelist(){
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
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
                        ->select('ac_payment_advice.*',DB::raw("case when status=1 then 'Pending' when status=2 then 'Approved' when status=3 then 'Rejected' end as status"))
                        ->where(['created_by' => $login_id])
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '$searchbycode%' ");
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

            Excel::create('Payment Advices List', function($excel) use($payments) {
                // Set the title
                $excel->setTitle('Payment Advices List');

                $excel->sheet('Payment Advices List', function($sheet) use($payments) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Payment Advices List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Payment Code', 'Date', 'Amount', 'Status'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($payments); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $payments[$i]->payment_code);
                        $sheet->setCellValue('B' . $chrRow, date("d-m-Y", strtotime($payments[$i]->created_at)));
                        $sheet->setCellValue('C' . $chrRow, $payments[$i]->total_amount);
                        $sheet->setCellValue('D' . $chrRow, $payments[$i]->status);

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