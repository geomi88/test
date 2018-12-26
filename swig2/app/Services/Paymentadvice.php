<?php

namespace App\Services;

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
use App\Notifications\RequisitionNotification;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Payment_advice;
use App\Models\Payment_advice_history;
use App\Models\Payment_advice_details;
use App\Models\Payment_advice_activity;
use App\Models\Transaction;
use App\Models\Purchaseorder;
use App\Services\Commonfunctions;
use Customhelper;
use DB;
use PDF;

class Paymentadvice {

    public function generatePaymentAdvice($arraData,$arraDetails,$createpurchaseorder=2) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
            
        $reqtype = DB::table('requisition_types as rq')
                ->select('rq.id as type_id')
                ->whereRaw("name='Payment Advice'")->first();

        $hierarchy_det = DB::table('requisition_hierarchy')
                    ->select('requisition_hierarchy.approver_id')
                    ->where(['requisition_type_id' => $reqtype->type_id, 'level' => 2 ,'status' => 1])->first();
        
        $paymentcode=$arraData['payment_code'];
            
        $duplicatecounts = DB::table('ac_payment_advice')->whereRaw("payment_code='$paymentcode'")->count();
        if($duplicatecounts>0){
            $common = new Commonfunctions();
            $paymentcode = $common->generatePaymentAdviceCode();
        }
            
        ///////////////////// insert in to payment advice ///////////////////////
        $paymodel = new Payment_advice();
        $paymodel->payment_code = $paymentcode;
        $paymodel->payment_from_id = $arraData['account_id'];
        $paymodel->from_ledger_type = $arraData['from_ledger_type'];
        $paymodel->payment_to_id = $arraData['supplier_id'];
        $paymodel->to_ledger_type = $arraData['to_ledger_type'];
        $paymodel->payment_type = $arraData['payment_type'];
        $paymodel->cheque_number = $arraData['checknumber'];
        $paymodel->cheque_image = $arraData['checkimage'];
        $paymodel->total_amount = $arraData['payamount'];
        $paymodel->level = 2;
        $paymodel->next_approver_id =$hierarchy_det->approver_id;
        $paymodel->responsible_emp_id =$arraData['emp_id'];
        $paymodel->description =$arraData['description'];
        $paymodel->generate_purchase_order=$createpurchaseorder;
        $paymodel->is_owner_drawing_requsition=$arraData['is_drawing'];
        $paymodel->created_by = $login_id;
        $paymodel->status = 1;
        $paymodel->save();

        $payment_id = $paymodel->id;
        
        
         ///////////////////// insert in to payment advice history ///////////////////////
        $payhismodel = new Payment_advice_history();
        $payhismodel->payment_advice_id =$payment_id;
        $payhismodel->payment_code = $paymentcode;
        $payhismodel->payment_from_id = $arraData['account_id'];
        $payhismodel->from_ledger_type = $arraData['from_ledger_type'];
        $payhismodel->payment_to_id = $arraData['supplier_id'];
        $payhismodel->to_ledger_type = $arraData['to_ledger_type'];
        $payhismodel->payment_type = $arraData['payment_type'];
        $payhismodel->cheque_number = $arraData['checknumber'];
        $payhismodel->cheque_image = $arraData['checkimage'];
        $payhismodel->total_amount = $arraData['payamount'];
        $payhismodel->level = 2;
        $payhismodel->next_approver_id =$hierarchy_det->approver_id;
        $payhismodel->responsible_emp_id =$arraData['emp_id'];
        $payhismodel->generate_purchase_order=$createpurchaseorder;
        $payhismodel->created_by = $login_id;
        $payhismodel->status = 1;
        $payhismodel->save();

        
        
        foreach ($arraDetails as $details) {
            ///////////////////// insert in to payment advice details ///////////////////////
            $detailsmodel = new Payment_advice_details();
            $detailsmodel->payment_advice_id = $payment_id;
            $detailsmodel->requisition_id = $details->requisition_id;
            $detailsmodel->requisition_code = $details->requisition_code;
            $detailsmodel->pay_amount = $details->pay_amount;
            $detailsmodel->save();
            
            DB::table('requisition')
                ->where(['id' => $details->requisition_id])
                ->update(['payment_generated' => 1]);
            
        }
        
        ///////////////////// insert in to payment advice activity ///////////////////////
//        $activitymodel = new Payment_advice_activity();
//        $activitymodel->payment_advice_id = $payment_id;
//        $activitymodel->action = 1;
//        $activitymodel->action_taker_id = $login_id;
//        $activitymodel->status = 1;
//        $activitymodel->save();
        
        /* ------------Send notification---------------- */
        $code = $paymentcode;
        $to = $hierarchy_det->approver_id;
        $common = new Commonfunctions();
        $result = $common->notifyNextActionTakerPaymentAdvice("Payment Advice", $code, $to, $payment_id, 1);

        return 1;
    }
    
    public function settleRequisitionWithPayment($payment_id) {
        $paymentrequests = DB::table('ac_payment_advice_details as ac_pad')
                ->select('ac_pad.*','requisition.outstanding_amount')
                ->leftjoin('requisition', 'ac_pad.requisition_id','=','requisition.id')
                ->where(['ac_pad.payment_advice_id' => $payment_id])->get();

        foreach ($paymentrequests as $request) {
            if ($request->outstanding_amount <= $request->pay_amount) {
                $outstanding_amount = 0;
                $isSettled = 1;
            } else {
                $outstanding_amount = $request->outstanding_amount - $request->pay_amount;
                if ($outstanding_amount < 0) {
                    $outstanding_amount = 0;
                }
                $isSettled = 2;
            }

            DB::table('requisition')
                    ->where(['id' => $request->requisition_id])
                    ->update(['outstanding_amount' => $outstanding_amount, 'is_settled' => $isSettled, 'payment_approved' => 1]);
        }

        return 1;
    }
    
    public function generatePaymentTransaction($payment_id) {
        $paymentdet=array();
        $paymentdet = DB::table('ac_payment_advice')
                ->select('ac_payment_advice.*')
                ->where(['ac_payment_advice.id' => $payment_id])->first();
        
        $paymentrequests = DB::table('ac_payment_advice_details')
                ->select('ac_payment_advice_details.pay_amount')
                ->where(['ac_payment_advice_details.payment_advice_id' => $payment_id])->get();
        
        $amount=$paymentrequests->sum('pay_amount');
        
        if(count($paymentdet)>0){
            $transModel = new Transaction();
            $transModel->payment_advice_id = $paymentdet->id;
            $transModel->payment_code = $paymentdet->payment_code;
            $transModel->payment_from_account = $paymentdet->payment_from_id;
            $transModel->from_ledger_type = $paymentdet->from_ledger_type;
            $transModel->payment_to_account = $paymentdet->payment_to_id;
            $transModel->to_ledger_type = $paymentdet->to_ledger_type;
            $transModel->payment_mode = $paymentdet->payment_type;
            $transModel->cheque_number = $paymentdet->cheque_number;
            $transModel->debit_amount = $amount;
            $transModel->save();
        }
        
        return 1;
    }
    
    public function generatePurchaseOrder($requisition_id,$order_type) {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $curTime=time();
        $timeStr=substr($curTime,-6);
        $maxid = DB::table('ac_purchase_order')->max('id')+1;
        if($order_type==3){
            $order_code="IPO-".$timeStr."-".$maxid;
        }else{
            $order_code="LPO-".$timeStr."-".$maxid;
        }
        
        
        $requestdet = DB::table('requisition')
                ->select('requisition.id','party_id','party_type','payment_mode','creditdays','delivery_place','delivery_date','payment_terms','requisition_types.make_purchase_order')
                ->leftjoin('requisition_types','requisition.requisition_type','=','requisition_types.id')
                ->whereRaw("requisition.id=$requisition_id")
                ->first();    

        if(isset($requestdet) && $requestdet->make_purchase_order==1){
            $orderModel = new Purchaseorder();
            $orderModel->order_code = $order_code;
            $orderModel->order_type = $order_type;
            $orderModel->payment_advice_id = NULL;
            $orderModel->company_id = $company_id;
            $orderModel->requisition_id = $requisition_id;
            $orderModel->from_ledger_id = NULL;
            $orderModel->from_ledger_type = NULL;
            $orderModel->to_ledger_id = $requestdet->party_id;
            $orderModel->to_ledger_type = $requestdet->party_type;
            $orderModel->amount = 0;
            $orderModel->delivery_destination = $requestdet->delivery_place;
            $orderModel->delivery_date = $requestdet->delivery_date;
            $orderModel->payment_term = $requestdet->payment_terms;
            $orderModel->payment_mode = $requestdet->payment_mode;
            $orderModel->credit_days = $requestdet->creditdays;
            $orderModel->save();
        }
        
        return 1;
    }
    
     public function updatePurchaseOrder($payment_id) {

        $paymentdet = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*', 'det.requisition_id', 'det.pay_amount')
                        ->join('ac_payment_advice_details as det', 'ac_payment_advice.id', '=', 'det.payment_advice_id')
                        ->where(['ac_payment_advice.id' => $payment_id])->first();

        if (isset($paymentdet) && $paymentdet->generate_purchase_order == 1) {

            DB::table('ac_purchase_order')
                    ->whereRaw("requisition_id=$paymentdet->requisition_id")
                    ->update([
                        'payment_advice_id' => $payment_id,
                        'from_ledger_id' => $paymentdet->payment_from_id,
                        'from_ledger_type' => $paymentdet->from_ledger_type,
                        'amount' => $paymentdet->pay_amount
                    ]);
        }

        return 1;
    }

}
