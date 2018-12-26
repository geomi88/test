@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions/payment_advice')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Payment Advice</h1>
    </header>
    <div class="custRow reqCodeDateHolder payreqCodeDateHolder">
        <div class="custCol-6">
            <label>Date : <span><?php echo date('d-m-Y'); ?></span></label>
        </div>
        <div class="custCol-6 alignRight">
            <label>Payment Code : <span>{{$paymentcode}}</span></label>
        </div>
    </div>
    <form action="{{ action('Requisitions\PaymentadviceController@store') }}" id="frmsavepaymentadvice" method="post" enctype="multipart/form-data">
        <div class="inputAreaWrapper">
            <div class="blockWrapper">
                <div class="custRow">
                    <input type="hidden"  name="payment_code" value="{{$paymentcode}}" id="payment_code" >
                    <input type="hidden"  name="requisition_id" value="{{$requisitiondata->id}}" id="requisition_id" >
                    <input type="hidden"  name="requisition_code" value="{{$requisitiondata->requisition_code}}" id="requisition_code" >
                    <input type="hidden"  name="outstanding_amount" value="{{$requisitiondata->outstanding_amount}}" id="outstanding_amount" >
                    <input type="hidden"  name="supplier_id" id="supplier_id" value="{{$requisitiondata->party_id}}" >
                    <input type="hidden"  name="to_ledger_type" id="to_ledger_type" value="{{$requisitiondata->party_type}}" >
                    <input type="hidden"  name="requsition_name" id="requsition_name" value="{{$requisitiondata->req_name}}" >
                    <div class="custCol-12 ">
                        <div class="inputView totAmntHandle">
                            <span>Requisition Total Amount : <strong><?php echo Customhelper::numberformatter($requisitiondata->total_price) ?></strong></span>
                        </div>
                    </div>
                    <div class="custCol-12">
                        <b>Mode of Payment</b>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input id="cash" value="2" name="payment_type" type="radio" checked class="clsmodeofpay" >
                                <span></span> <em>Cash</em> </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input id="check" value="1" name="payment_type" type="radio" class="clsmodeofpay">
                                <span></span> <em>Cheque</em> </label>
                        </div>

                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input id="online" value="3" name="payment_type" type="radio" class="clsmodeofpay" >
                                <span></span> <em>Online</em> </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="statusMessage">
                    <span class="errorMsg" >Payment Exceeds Balance Amount</span>
            </div>
            <div class="blockWrapper">
                <strong>From</strong>
                <div class="paymentDetailWrapper">
                    <div class="custRow clsparentdiv">
                        <div class="custCol-4">
                            <input type="hidden"  name="from_ledger_type" id="from_ledger_type" value="General Ledger" >
                            <div class="inputHolder">
                                <label>Account</label>
                                <?php  if($requisitiondata->req_name=='Advance Payment Requisition' || $requisitiondata->req_name=='Owner Drawings Requisition'){ ?>
                                <input type="hidden" name="account_id" value="{{$requisitiondata->general_ledger}}">
                                <input type="hidden" name="emp_id" value="{{$requisitiondata->party_id}}">
                                <?php } ?>
                                <select id="account_id" name="account_id" class="account_id <?php  if($requisitiondata->req_name!='Advance Payment Requisition' || $requisitiondata->req_name!='Owner Drawings Requisition'){  echo "chosen-select";}?>"  <?php  if($requisitiondata->req_name=='Advance Payment Requisition' || $requisitiondata->req_name=='Owner Drawings Requisition'){  echo "disabled";}?>>
                                        <option value=''>Select Ledger</option>
                                        @foreach ($gledgers as $gledger)
                                        <option value="{{ $gledger->accountid }}" role="{{$gledger->type}}" <?php  if(($requisitiondata->req_name=='Advance Payment Requisition' || $requisitiondata->req_name=='Owner Drawings Requisition') &&  $gledger->accountid==$requisitiondata->general_ledger){ echo "selected " ; } ?> >{{ $gledger->first_name}} ({{$gledger->code}})</option>
                                        @endforeach
                                    </select>

                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Responsible Employee</label>
                                <select id="emp_id" name="emp_id" class="emp_id chosen-select">
                                        <option value=''>Choose Employee</option>
                                        @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->first_name}} ({{$employee->code}})</option>
                                        @endforeach
                                    </select>

                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4 ">
                            <div class="inputHolder  ">
                                <label>Amount Pay</label>
                                <input type="text" name="payamount" id="payamount" <?php  if($requisitiondata->req_name=='Advance Payment Requisition' || $requisitiondata->req_name=='Owner Drawings Requisition'){ echo "readonly value='$requisitiondata->total_price'"; } ?> onpaste="return false;" placeholder="Enter Amount Pay" autocomplete="off" class="numberwithdot" maxlength="15">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    <div class="customClear"></div>
                    <div class="custRow clscheckinfo" style="display: none;">
                        <div class="custCol-4 ">

                            <div class="inputHolder  ">
                                <label>Cheque Number</label>
                                <input type="text" name="checknumber" id="checknumber" autocomplete="off" placeholder="Enter Cheque Number" maxlength="100">
                                <span class="commonError"></span>

                            </div>
                        </div>
                        <div class="custCol-4 ">
                            <div class="inputHolder">
                                <label>Upload Copy</label>
                                <input type="file" name="checkimage" class="reqdocument" id="checkimage" accept=".img, .png,.jpeg,.jpg">
                                <span class="commonError"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="blockWrapper">
                <strong>To</strong>
                 <?php  if($requisitiondata->req_name=='Advance Payment Requisition' || $requisitiondata->req_name=='Owner Drawings Requisition'){?>
                  
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
                            <span>Supplier Code<strong> {{$requisitiondata->code}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Supplier Name<strong> {{$requisitiondata->first_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Account Number<strong> {{$requisitiondata->bank_account_number}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Swift Code<strong> {{$requisitiondata->bank_swift_code}}</strong></span>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="inputView inputViewHandle">
                            <span>Beneficiary Name<strong> {{$requisitiondata->bank_beneficiary_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Bank Name<strong> {{$requisitiondata->bank_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Country<strong> {{$requisitiondata->bankcountry}}</strong></span>
                        </div>
                    </div>
                    
                </div>
                <?php } ?>
            </div>

            <div class="accordionWrapper">

                <div class="accordion">
                    <div class="accdnTitle">
                        <a href="{{ URL::to($requisitiondata->strrequesturl, ['id' => Crypt::encrypt($requisitiondata->id)]) }}" target="_blank"><i></i>Requisition Code : {{$requisitiondata->requisition_code}}</a>

                        <div class="iconInfoWrapper">
                            <a href="javascript:gettransactionhistory('{{$requisitiondata->requisition_code}}')" class="btnTransaction  btnTooltip"><img src="{{ URL::asset('images/iconTrancation.png')}}" alt="Transaction"></a>
                            <div class="tooltipInfo">
                                <a href="javascript:void(0);" class="infoClose"></a>
                                <a href="javascript:void(0);" class="btnAction print bgGreen printtransaction">Print</a>
                                <div class="tbleListWrapper ">
                                    <div class="inputView totAmntHandle">
                                        <span>Requisition : <strong id="reqcode_{{$requisitiondata->id}}"></strong></span>
                                        <span class="right">Total Amount : <strong id="reqamount_{{$requisitiondata->id}}"></strong></span>
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
                                        <tbody id="history_{{$requisitiondata->id}}">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-6 ">
                    <div class="inputHolder  ">
                        <label>Description</label>
                        <textarea placeholder="Enter Description" id="description" name="description"></textarea>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
        </div>
        
        <input type="submit" id="btnSavePayment" class="btnIcon btnSaveV3  lightGreenV3 " value="Submit">
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
    
</div>
    <div class="commonLoaderV1"></div>
<script>
    $(function () {
        $(".statusMessage").hide();
        $(".commonLoaderV1").hide();
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
                account_id: {required: true},
                emp_id: {required: true},
                payamount: {required: true,number:true},
                checknumber: {
                                required: {depends: function () {if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}}
                             },
            },
            messages: {
                account_id: "Select Ledger",
                emp_id: "Select Employee",
                payamount: {
                 required: "Enter Amount",
                 number: "Enter a valid amount"
                 },
                checknumber: "Enter Cheque Number",
            }
        });
        
        
        $(".clsmodeofpay").click(function () {
            if ($("#check").prop("checked")) {
                $(".clscheckinfo").show();
            } else {
                $(".clscheckinfo").hide();
            }
        });
        
        $('#frmsavepaymentadvice').submit(function(e) {
            if(!$('#frmsavepaymentadvice').valid()){
                return false;
            }
            if(parseFloat($("#payamount").val())>parseFloat($("#outstanding_amount").val())){
                $(".statusMessage").show();
                return false;
            }else{
                $(".statusMessage").hide();
            }
            $('#btnSavePayment').attr('disabled', 'disabled');
            $(".commonLoaderV1").show();
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