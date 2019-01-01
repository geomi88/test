@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions/payment_approval/inbox')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Payment Advice</h1>
    </header>
    <div class="custRow reqCodeDateHolder payreqCodeDateHolder">
        <div class="custCol-6">
            <label>Date : <span><?php echo date('d-m-Y',  strtotime($paymentdata->created_at)); ?></span></label>
        </div>
        <div class="custCol-6 alignRight">
            <label>Payment Code : <span>{{$paymentdata->payment_code}}</span></label>
        </div>
    </div>
    <form id="frmsavepaymentadvice" method="post" enctype="multipart/form-data">
        <div class="inputAreaWrapper">
            <div class="blockWrapper">
                <div class="custRow">
                    <input type="hidden"  name="payment_id" id="payment_id" value="{{$paymentdata->id}}">
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
                                <label>Cheque Number</label>
                                <input type="text" name="checknumber" id="checknumber" disabled value="{{$paymentdata->cheque_number}}" placeholder="Enter Cheque Number" maxlength="100">
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
                    
                 <?php } else {?>
                
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
                <strong>Description</strong>
                <div class="inputViewWrapper">
                    <div class="inputView inputViewHandle">
                        <span>{{$paymentdata->description}}</span>
                    </div>
                </div>
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
                                <a href="javascript:void(0);" class="btnAction print bgGreen printtransaction">Print</a>
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
       
    </form>
    
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
    
    <?php if($isactiontaken!="Yes"){?>
    <div class="inputAreaWrapper">
        <div class="custRow">
            <div class="custCol-6">

                <form id="frmreject">
                    <div class="inputHolder  ">
                        <label>Comments</label>
                        <textarea placeholder="Enter Comments" id="comments" name="comments"></textarea>
                        <span class="commonError"></span>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="bottomBtnsHolder">
        <input type="button" id="btnapprove" class="btnIcon bgGreen" value="Approve">
        <input type="button" id="btnreject" class="btnIcon bgRed" value="Reject">
         
        <div class="customClear "></div>
    </div>
    <?php } ?>
    <div class="commonModalHolder">
        <div class="modalContent printModal">
            <a href="javascript:void(0);" class="btnAction print bgGreen btnImgPrint" style="display: none;">Print</a>
            <a href="javascript:void(0);" class="btnModalClose">Close(X)</a>
            <div id="printHolder">
                <iframe id="frame" style="width:100%;height:100%;"></iframe>
            </div>
        </div>
    </div> 
</div>
    <div class="commonLoaderV1"></div>
<script>
    $(function () {
        $('.commonLoaderV1').hide();
        $('#btnreject').removeAttr('disabled');
        $('#btnapprove').removeAttr('disabled');
        $("#frmsavepaymentadvice").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                account: {required: true},
                employee: {required: true},
                payamount: {required: true},
                checknumber: {required: true},
            },
            messages: {
                account: "Select Ledger",
                employee: "Select Employee",
                payamount: "Enter Amount",
                checknumber: "Enter Cheque Number",
            }
        });
        
        $("#frmreject").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                comments: {required: true},
            },
            messages: {
                comments: "Enter Reason",
            }
        });

        $("#btnreject").click(function () {
            if (!$("#frmreject").valid()) {
                return;
            }
            
             var blnConfirm = confirm("Are you sure to reject payment ?");
            if (!blnConfirm) {
                return;
            }


            $('.commonLoaderV1').show();
            $('#btnreject').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../reject_payment',
                data: '&payment_id=' + $('#payment_id').val() + '&comments=' + $("#comments").val(),
                success: function (return_data) {
                    $('.commonLoaderV1').hide();
                    $('#btnreject').removeAttr('disabled');
                    window.location.href = '{{url("requisitions/payment_approval/inbox")}}';
                }
            });
            
        });
        
        $("#btnapprove").click(function () {
            var blnConfirm = confirm("Are you sure to approve payment ?");
            if(!blnConfirm){
               return; 
            }
            
            $('.commonLoaderV1').show();
            $('#btnapprove').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../approve_payment',
                data: '&payment_id=' + $('#payment_id').val() + '&comments=' + $("#comments").val(),
                success: function (return_data) {
                    $('.commonLoaderV1').hide();
                    $('#btnapprove').removeAttr('disabled');
                    window.location.href = '{{url("requisitions/payment_approval/inbox")}}';
                }
            });
            
        });
        
        $('.btnModalClose').on('click', function () {
            $('.commonModalHolder').hide()
        });
        
        $('.printtransaction').click(function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('.transactionprint').html());
            win.document.close();
            win.print();
            win.close();
            return false;
        });
        
         $('body').on('click', '.btnImgPrint', function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('#printHolder').html());
            win.document.close();
            win.print();
            win.close();
            return false;
        });
        
    });
        
    function gettransactionhistory(code) {
         $.ajax({
            type: 'POST',
            url: '../../gettransactionhistory',
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