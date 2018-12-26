@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function(){
       $('#printpayment').click(function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write( $('.printDiv').html());
            win.document.close();
            win.print();
            win.close();
            return false;
        }); 
    });
</script>
<?php if($paymentdata->payment_type==2){$mode = "Cash";}
if($paymentdata->payment_type==1){$mode = "Cheque";}
if($paymentdata->payment_type==3){$mode = "Online";}?>
<div style="display: none" class="printDiv">
    <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;color:#000;border:3px solid #760000;">
        <tr>
            <td>
                <?php if($blnshobackbutton==0){?>
                <span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;">Remittance Report</span>
                <?php } elseif($blnshobackbutton==1) { ?>
                <span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;">Payment Advice</span>
                </header>
                <?php } ?>
                
                <p style="padding-bottom:5px;color:#000;"><strong style=padding-right:5px;>Date :</strong><span><?php echo date('d-m-Y',  strtotime($paymentdata->created_at)); ?></span></p>
                <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">Payment Code : </strong><span>{{$paymentdata->payment_code}}</span></p>
            </td>
            <td style="text-align:right;vertical-align:top;">
                <img src="{{Url('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:150px;">
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Requisition Details</th>
                    </tr>
                    <tr>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Requisition Total Amount : </span>
                                        <strong style="display:inline-block;color:#000;"><?php echo Customhelper::numberformatter($paymentdata->requisitiontotal) ?></strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Mode of Payment :</span>
                                        <strong style="display:inline-block;color:#000;">{{$mode}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;"> </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">From</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Account : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->fromledgername}} {{'('.$paymentdata->fromledgercode.')'}}</strong>
                                    </td>
                                </tr>
                                @if($paymentdata->payment_type==1)
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Cheque Number : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->cheque_number}}</strong>
                                    </td>
                                </tr>
                                @endif

                            </table>
                        </td>
                        
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Responsible Employee</span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->respname." (".$paymentdata->respcode.")"}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Amount Pay</span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->total_amount}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">To</strong>
                        </td>
                    </tr>
                     <?php  if($paymentrequests[0]->req_name=='Advance Payment Requisition' || $paymentrequests[0]->req_name=="Owner Drawings Requisition"){ ?>
                  
                    <tr>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code : </span>
                                        <strong style="display:inline-block;color:#000;">{{$employee_data->code}}</strong>
                                    </td>
                                </tr>
                                
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name : </span>
                                        <strong style="display:inline-block;color:#000;">{{$employee_data->first_name}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                 <?php } else { ?>
                    <tr>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Supplier Code : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->suppliercode}}</strong>
                                    </td>
                                </tr>
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Swift Code : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->supplier_swiftcode}}</strong>
                                    </td>
                                </tr>
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Country : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->bankcountry}}</strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">

                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Supplier Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->suppliername}}</strong>
                                    </td>
                                </tr>
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Beneficiary Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->bank_beneficiary_name}}</strong>
                                    </td>
                                </tr>
                               
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;"></span>
                                        <strong style="display:inline-block;color:#000;"></strong>
                                    </td>
                                </tr>
                               
                            </table>

                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Account Number : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->supplier_acno}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Bank Name : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->bank_name}}</strong>
                                    </td>
                                </tr>
                                <tr  style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;"></span>
                                        <strong style="display:inline-block;color:#000;"></strong>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Remittance Details</th>
                    </tr>
                    <tr>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Remittance No. / Cheque No. : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->remittance_number}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Remittance Image : </span>
                                        <strong style="display:inline-block;color:#000;"> <?php if($paymentdata->remittance_image){ echo "File Attached";} ?></strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                         <td width="33.33%" style="vertical-align:top; ">
                             <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Remitted Date : </span>
                                        <strong style="display:inline-block;color:#000;"><?php if($paymentdata->remitted_date) { echo date("d-m-Y", strtotime($paymentdata->remitted_date)); } ?></strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="33.33%" style="vertical-align:top; ">
                             <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Beneficiary Name : </span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->beneficiary_name}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td  colspan="2" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Description :</span>
                                        <strong style="display:inline-block;color:#000;">{{$paymentdata->remittance_desc}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        
                    </tr>
                </table>
            </td>
        </tr>
        @foreach ($paymentrequests as $request)
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th width="50%">
                            <span style="padding-right:5px;display:inline-block;color:#fff;">Requisition Code :</span>
                            <strong style="display:inline-block;color:#fff;">{{$request->requisition_code}}</strong>
                        </th>
                        <th width="50%" style="text-align:right;">
                            <span style="padding-right:5px;display:inline-block;color:#fff;">Payment Amount :</span>
                            <strong style="display:inline-block;color:#fff;"><?php echo Customhelper::numberformatter($request->pay_amount) ?></strong>
                        </th>
                    </tr>
                </table>
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3" style="color:#760000;font-size:14px;font-weight:bold;">
                Approved By
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none;">
                    <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr>
                            <th>Action Taker</th>
                            <th width="80px">Date</th>
                            <th>Comments </th>
                            <th width="80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <tr>
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y', strtotime($actor->created_at)); ?></td>
                            <td>{{$actor->comments}}</td>
                            <td>{{$actor->action}}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom:2px solid #760000; height:10px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:10px;text-align:center;">
                <img src="{{Url('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:5px 0 10px; color:#760000;text-align:center;">
                P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
            </td>
        </tr>
    </table>
</div>
<div class="innerContent">
    <?php if($blnshobackbutton==0){?>
    <header class="pageTitleV3">
        <h1>Remittance Report</h1>
        <a class="btnAction print bgGreen" id="printpayment" href="#">Print</a>
    </header>
    <?php } elseif($blnshobackbutton==1) { ?>
    <a class="btnBack" href="{{ URL::to('finance/received_payments')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Payment Advice</h1>
        <a class="btnAction print bgGreen" id="printpayment" href="#">Print</a>
    </header>
    <?php } ?>
    
    
    <div class="custRow reqCodeDateHolder payreqCodeDateHolder">
        <div class="custCol-6">
            <label>Date : <span><?php echo date('d-m-Y',  strtotime($paymentdata->created_at)); ?></span></label>
        </div>
        <div class="custCol-6 alignRight">
            <label>Payment Code : <span>{{$paymentdata->payment_code}}</span></label>
        </div>
    </div>
   
        <div class="inputAreaWrapper">
            <div class="blockWrapper">
                <div class="custRow">
                    
                    <div class="custCol-12 ">
                        <div class="inputView totAmntHandle">
                            <span>Requisition Total Amount : <strong><?php echo Customhelper::numberformatter($paymentdata->requisitiontotal) ?></strong></span>
                        </div>
                    </div>
                    <div class="custCol-12">
                        <b>Mode of Payment</b>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input id="cash" value="2" name="payment_type" type="radio" <?php if($paymentdata->payment_type==2){echo "checked";}?> disabled class="clsmodeofpay" >
                                <span></span> <em>Cash</em> </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input id="check" value="1" name="payment_type" type="radio" <?php if($paymentdata->payment_type==1){echo "checked";}?> disabled class="clsmodeofpay">
                                <span></span> <em>Cheque</em> </label>
                        </div>

                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input id="online" value="3" name="payment_type" type="radio" <?php if($paymentdata->payment_type==3){echo "checked";}?> disabled class="clsmodeofpay" >
                                <span></span> <em>Online</em> </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="blockWrapper">
                <strong>From</strong>
                <div class="paymentDetailWrapper">
                    <div class="custRow ">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Account</label>
                                <div class="bgSelect">                                                          
                                    <input type="text" name="account" id="account" autocomplete="off" disabled value="{{$paymentdata->fromledgername}} ({{$paymentdata->fromledgercode}})" placeholder="Enter Code or Name">
                                    <ul class="account_list classscroll">
                                    </ul>

                                    <input type="hidden"  name="account_id" id="account_id" >
                                    <input type="hidden"  name="account_code" id="account_code" >
                                    <input type="hidden"  name="account_name" id="account_name" >
                                </div>
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Responsible Employee</label>
                                <div class="bgSelect">                                                          
                                    <input type="text" name="employee" id="employee" autocomplete="off" disabled value="<?php if($paymentdata->respname){echo $paymentdata->respname." (".$paymentdata->respcode.")";}?>" placeholder="Enter Code or Name">
                                    <ul class="employee_list classscroll">
                                    </ul>

                                    <input type="hidden"  name="emp_id" id="emp_id" >
                                    <input type="hidden"  name="emp_code" id="emp_code" >
                                    <input type="hidden"  name="emp_fname" id="emp_fname" >
                                </div>
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4 ">
                            <div class="inputHolder  ">
                                <label>Amount Pay</label>
                                <input type="text" name="payamount" id="payamount" placeholder="Enter Amount Pay" disabled value="{{$paymentdata->total_amount}}" class="numberwithdot" maxlength="15">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    <div class="customClear"></div>
                    <div class="custRow clscheckinfo" style="<?php if($paymentdata->payment_type!=1){echo "display: none;";}?>">
                        <div class="custCol-4 ">

                            <div class="inputHolder  ">
                                <label>Check Number</label>
                                <input type="text" name="checknumber" id="checknumber" disabled value="{{$paymentdata->cheque_number}}" placeholder="Enter Check Number" maxlength="100">
                                <span class="commonError"></span>

                            </div>
                        </div>
                        <div class="custCol-4 ">
                            <div class="inputHolder ">
                                <label>Cheque Image</label>
                                <span class="commonError"></span>
                                <a class="btnAction edit bgBlue viewreqdocument" href="{{$paymentdata->cheque_image}}">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="blockWrapper">
                <strong>To</strong>
                <?php  if($paymentrequests[0]->req_name=='Advance Payment Requisition' || $paymentrequests[0]->req_name=='Owner Drawings Requisition'){?>
                  
                    <div class="inputViewWrapper">

                        <div class="inputView inputViewHandle">
                            <span>Employee Code<strong> {{$employee_data->code}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Employee Name<strong> {{$employee_data->first_name}}</strong></span>
                        </div>

                    </div>

                <?php }else{ ?>
                <div class="inputViewWrapper">
                    <div class="custRow">
                        <div class="inputView inputViewHandle">
                            <span>Supplier Code<strong> {{$paymentdata->suppliercode}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Supplier Name<strong> {{$paymentdata->suppliername}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Account Number<strong> {{$paymentdata->supplier_acno}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Swift Code<strong> {{$paymentdata->supplier_swiftcode}}</strong></span>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        <div class="inputView inputViewHandle">
                            <span>Beneficiary Name<strong> {{$paymentdata->bank_beneficiary_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Bank Name<strong> {{$paymentdata->bank_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Country<strong> {{$paymentdata->bankcountry}}</strong></span>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="blockWrapper">
                <form action="{{ action('Finance\ReceivedpaymentsController@update') }}" id="frmsavepaymentadvice" method="post" enctype="multipart/form-data">
                    <div class="custRow clscheckinfo" >
                        <div class="custCol-4 ">
                            <div class="inputHolder  ">
                                <label>Remittance Number / Cheque Number</label>
                                <input type="text" name="remittancenumber" id="remittancenumber" value="{{$paymentdata->remittance_number}}"  placeholder="Enter Remittance Number" autocomplete="off" maxlength="100" <?php if($paymentdata->remittance_number || $blnshobackbutton!=1){echo "disabled";}?>>
                                <input type="hidden"  name="payment_id" id="payment_id" value="{{$paymentdata->id}}">
                                <input type="hidden"  name="remittance_url" id="remittance_url" value="{{$paymentdata->remittance_image}}">
                                <span class="commonError"></span>

                            </div>
                        </div>
                        <?php if(!$paymentdata->remittance_number  && $blnshobackbutton!=0){?>
                        <div class="custCol-4 ">
                            <div class="inputHolder">
                                <label>Upload Remittance Copy</label>
                                <input type="file" name="remitanceimage" id="remitanceimage" class="reqdocument" accept=".img, .png,.jpeg,.jpg">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <?php } if($paymentdata->remittance_image){?>
                        <div class="custCol-4 ">
                            <div class="inputHolder ">
                                <label>Remittance Image</label>
                                <span class="commonError"></span>
                                <a class="btnAction edit bgBlue viewreqdocument" href="{{$paymentdata->remittance_image}}">View</a>
                            </div>
                        </div>
                        <?php } if($paymentdata->remittance_number) { ?>
                        <div class="custCol-2">
                            <div class="inputHolder ">
                                <label ><span>Remitted date : </span><strong><?php if($paymentdata->remitted_date) { echo date("d-m-Y", strtotime($paymentdata->remitted_date)); } ?></strong></label>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-4 ">
                            <div class="inputHolder ">
                                <label>Beneficiary Name</label>
                                <input type="text" name="beneficiary_name" id="beneficiary_name" value="{{$paymentdata->beneficiary_name}}"  placeholder="Enter Beneficiary Name" autocomplete="off" <?php if($paymentdata->remittance_number || $blnshobackbutton!=1){echo "disabled";}?> maxlength="200">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Description</label>
                                <textarea id="description" name="description" placeholder="Enter Description" <?php if($paymentdata->remittance_number || $blnshobackbutton!=1){echo "disabled";}?>>{{$paymentdata->remittance_desc}}</textarea>
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(!$paymentdata->remittance_number && $blnshobackbutton!=0){?>
                        <input type="submit" id="btnSavePayment" class="btnIcon btnSaveV3  lightGreenV3 " value="Submit">
                    <?php } ?>
                </form>
            </div>
            @foreach ($paymentrequests as $request)
            <div class="accordionWrapper">

                <div class="accordion">
                    <div class="accdnTitle">
                        
                        <a href="{{ URL::to($request->strrequesturl, ['id' => Crypt::encrypt($request->requisition_id)]) }}" target="_blank"><i></i>Requisition Code : {{$request->requisition_code}}</a> <em>Payment Amount : <strong><?php echo Customhelper::numberformatter($request->pay_amount) ?></strong></em>

                        <div class="iconInfoWrapper">
                            <a href="javascript:gettransactionhistory('{{$request->requisition_code}}')" class="btnTransaction  btnTooltip"><img src="{{ URL::asset('images/iconTrancation.png')}}" alt="Transaction"></a>
                            <div class="tooltipInfo">
                                <a href="javascript:void(0);" class="infoClose"></a>
                                <a href="javascript:void(0);" class="btnAction print bgGreen printtransaction" >Print</a>
                                <div class="tbleListWrapper ">
                                    <div class="inputView totAmntHandle">
                                        <span>Requisition : <strong id="reqcode_{{$request->requisition_id}}"></strong></span>
                                        <span class="right">Total Amount : <strong id="reqamount_{{$request->requisition_id}}"></strong></span>
                                    </div>
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <thead class="headingHolder">
                                            <tr>
                                                <th>Date</th>
                                                <th>Payment Code</th>
                                                <th>Mode Of Payment</th>
                                                <th>Paid By</th>
                                                <th class="amountAlign">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="history_{{$request->requisition_id}}">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
       
        <div class="transactionprint" style="display: none;">
        <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;color:#000;border:3px solid #760000;">
            <tr>
                <td>
                    <span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;">Transaction History</span>
                    <p style="padding-bottom:5px;color:#000;"><strong style=padding-right:5px;>Requisition Code :</strong><span id="printcode">17418</span></p>
                    <p style="margin: 0;"><strong style="padding-right:5px;color:#000;"> Total Amount :</strong><span id="printamount">12,34,500</span></p>
                </td>
                <td style="text-align:right;vertical-align:top;">
                    <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:150px;">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none;">
                        <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                            <tr>
                                <th width="50px;">Date </th>
                                <th width="70px;">Payment Code</th>
                                <th width="60px;">Mode of Payment</th>
                                <th width="250px;">Paid By </th>
                                <th width="100px;" style="text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="printbody">

                        </tbody>

                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:2px solid #760000; height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;text-align:center;">
                    <img src="{{Url('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding:5px 0 10px; color:#760000;text-align:center;">
                    P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
                </td>
            </tr>
        </table>
    </div>
    
    <div class="approverDetailsWrapper">
        <div class="tbleListWrapper">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead class="headingHolder ">
                    <tr>
                        <th>Action Taker</th>
                        <th>Date</th>
                        <th>Comments</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($action_takers as $actor)
                    <?php if($actor->action=="Rejected"){$class="bgRed";}else{$class="bgGreen";}?>
                    <tr class="<?php echo $class;?>">
                        <td>{{$actor->action_taker}}</td>
                        <td><?php echo date('d-m-Y',  strtotime($actor->created_at));?></td>
                        <td>{{$actor->comments}}</td>
                        <td>{{$actor->action}}</td>

                    </tr>
                    @endforeach

                    
                </tbody>
            </table>
        </div>

    </div> 
    <div class="commonModalHolder">
        <div class="modalContent printModal">
            <a href="javascript:void(0);" class="btnAction print bgGreen btnImgPrint" style="display: none;">Print</a>
            <a href="javascript:void(0);" class="btnModalClose">Close(X)</a>
            <div id="printHolder">
                <iframe id="frame" style="width:100%;height:100%;"></iframe>
            </div>
        </div>
    </div>
    <div class="commonLoaderV1"></div>
</div>
<script>
    $(function(){
        
        $("#frmsavepaymentadvice").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id=$(element).attr("id")+"_chosen";
                if($(element).hasClass('valErrorV1')){ 
                  $("#"+id).find('.chosen-single').addClass('chosen_error');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                remittancenumber:{
                                   required: {depends: function () {if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}}
                                },
                beneficiary_name:{
                                   required: {depends: function () {if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}}
                                },
                description:{
                             required: {depends: function () {if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}}
                            },
            },
            messages: {
                remittancenumber: "Enter Remittance Number",
                beneficiary_name: "Enter Beneficiary Name",
                description: "Enter Description",
            }
        });
    
        $('.btnModalClose').on('click', function () {
            $('.commonModalHolder').hide()
        });
        
        $('body').on('click', '.btnImgPrint', function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('#printHolder').html());
            win.document.close();
            win.print();
            win.close();
        });
    
        $('#frmsavepaymentadvice').submit(function(e) {
            if(document.getElementById("remitanceimage").files.length==0){
                alert("Remittance copy not selected");
                return false;
                e.preventDefault();
            }
           
        });
        
        $('.printtransaction').click(function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('.transactionprint').html());
            win.document.close();
            win.print();
            win.close();
            return false;
        });
        
    });

    function gettransactionhistory(code) {
         $.ajax({
            type: 'POST',
            url: '../../../requisitions/gettransactionhistory',
            data: '&requisition_code=' + code,
            success: function (return_data) {
                var strHtml='';
                var requisition=return_data.requisition;
                $("#reqcode_"+requisition.req_id).html(code);
                $("#reqamount_"+requisition.req_id).html(requisition.totalformated);
                $("#history_"+requisition.req_id).html(return_data.history);
                $("#printcode").html(code);
                $("#printamount").html(requisition.totalformated);
                $("#printbody").html(return_data.printhtml);
            }
        });
    }
</script>
@endsection