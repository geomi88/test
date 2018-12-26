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

class OutstandingpaymentsController extends Controller {
    
    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $suppliers = DB::table('ac_party')
                ->select('ac_party.first_name','ac_party.code','ac_party.id','ac_party.alias_name')
                ->where(['ac_party.party_type' => 'Supplier', 'ac_party.status' => 1])
                ->orderby('ac_party.first_name', 'ASC')
                ->get();

            if ($request->ajax()) {
                $supplier = Input::get('supplier');
            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                
                $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name as req_name')
                        ->leftjoin('requisition_types','requisition.requisition_type', '=','requisition_types.id' )
                        ->whereRaw("requisition.status=4 AND payment_approved=1 AND is_settled=2")
                        ->orderby('requisition.updated_at', 'DESC')
                        ->when($supplier, function ($query) use ($supplier) {
                            return $query->whereRaw("requisition.party_id=$supplier AND party_type='Supplier'");
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/outstanding_payments/results', array('requisitions' => $requisitions));
            }
            return view('requisitions/outstanding_payments/index', array('requisitions' => array(),'suppliers'=>$suppliers));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function add() {
        try {
            
            $arrdata = Input::get('selectedrequests');
            $requisitiondata=  json_decode($arrdata);
            $supplier_id = Input::get('supplier_id');
            $totalpayamount = Input::get('totalpayamount');
            $requisitiontotal = Input::get('requisitiontotal');
            
            $supplierdata = DB::table('ac_party')
                ->select('ac_party.first_name','ac_party.code','ac_party.id','ac_party.bank_swift_code','ac_party.bank_account_number','ac_party.party_type','ac_party.bank_beneficiary_name','ac_party.bank_name','country.name as bankcountry')
                ->leftjoin('country', 'ac_party.bank_country','=','country.id')
                ->where(['ac_party.id' => $supplier_id])
                ->first();
                        
            $common = new Commonfunctions();
            $paymentcode = $common->generatePaymentAdviceCode();
            foreach ($requisitiondata as $request) {
                $request->strrequesturl=$common->getRequestViewUrlFromPaymentAdvice($request->req_name);
            }
            
            $employees = DB::table('employees')
                    ->select('employees.id','username as code','employees.first_name','employees.alias_name')
                    ->whereRaw("employees.status=1 AND id!=1")
                    ->orderby('employees.first_name','ASC')
                    ->get();
            
            $gledgers = DB::table('ac_accounts')
                    ->select('ac_accounts.type_id as accountid', 'code', 'ac_accounts.first_name', 'ac_accounts.type')
                    ->whereRaw("ac_accounts.status=1 AND type='General Ledger'")
                    ->orderby('ac_accounts.first_name', 'ASC')
                    ->get();
            
            return view('requisitions/outstanding_payments/add', array('paymentcode'=>$paymentcode,'requisitiondata'=>$requisitiondata,'supplierdata'=>$supplierdata,'totalpayamount'=>$totalpayamount,'requisitiontotal'=>$requisitiontotal,'arrdata'=>$arrdata,'employees'=>$employees,'gledgers'=>$gledgers));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
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
            $arraDetails=json_decode($arraData['arrdetails']);

            $payment = new Paymentadvice();
            $createpurchaseorder=2;
            $result = $payment->generatePaymentAdvice($arraData,$arraDetails,$createpurchaseorder);
            
          
            /*------------Send notification----------------*/
        
            Toastr::success('Payment Advice Saved Successfully !', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/payment_advice');
        }
    }
    
}