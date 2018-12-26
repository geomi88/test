@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions/outstanding_payments')}}">Back</a>
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
    <form action="{{ action('Requisitions\OutstandingpaymentsController@store') }}" id="frmsavepaymentadvice" method="post" enctype="multipart/form-data">
        <div class="inputAreaWrapper">
            <div class="blockWrapper">
                <div class="custRow">
                    <input type="hidden"  name="payment_code" value="{{$paymentcode}}" id="payment_code" >
                    <input type="hidden"  name="supplier_id" id="supplier_id" value="{{$supplierdata->id}}" >
                    <input type="hidden"  name="to_ledger_type" id="to_ledger_type" value="{{$supplierdata->party_type}}" >
                    <input type="hidden"  name="arrdetails" id="arrdetails" value="{{$arrdata}}" >
                    <div class="custCol-12 ">
                        <div class="inputView totAmntHandle">
                            <span>Requisition Total Amount : <strong><?php echo Customhelper::numberformatter($requisitiontotal) ?></strong></span>
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
            <div class="blockWrapper">
                <strong>From</strong>
                <div class="paymentDetailWrapper">
                    <div class="custRow clsparentdiv">
                        <div class="custCol-4">
                            <input type="hidden"  name="from_ledger_type" id="from_ledger_type" value="General Ledger" >
                            <div class="inputHolder">
                                <label>Account</label>
                                <select id="account_id" name="account_id" class="account_id chosen-select">
                                        <option value=''>Select Ledger</option>
                                        @foreach ($gledgers as $gledger)
                                        <option value="{{ $gledger->accountid }}" role="{{$gledger->type}}">{{ $gledger->first_name}} ({{$gledger->code}})</option>
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
                                        <option value="{{ $employee->id }}" >{{ $employee->first_name}} ({{$employee->code}})</option>
                                        @endforeach
                                    </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4 ">
                            <div class="inputHolder  ">
                                <label>Amount Pay</label>
                                <input type="text" name="payamount" id="payamount" placeholder="Enter Amount Pay" class="numberwithdot" value="{{$totalpayamount}}" readonly maxlength="15">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    <div class="customClear"></div>
                    <div class="custRow clscheckinfo" style="display: none;">
                        <div class="custCol-4 ">

                            <div class="inputHolder  ">
                                <label>Cheque Number</label>
                                <input type="text" name="checknumber" id="checknumber" placeholder="Enter Cheque Number" maxlength="100">
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
                <div class="inputViewWrapper">
                    <div class="custRow">
                        <div class="inputView inputViewHandle">
                            <span>Supplier Code<strong> {{$supplierdata->code}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Supplier Name<strong> {{$supplierdata->first_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Account Number<strong> {{$supplierdata->bank_account_number}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Swift Code<strong> {{$supplierdata->bank_swift_code}}</strong></span>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="inputView inputViewHandle">
                            <span>Beneficiary Name<strong> {{$supplierdata->bank_beneficiary_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Bank Name<strong> {{$supplierdata->bank_name}}</strong></span>
                        </div>
                        <div class="inputView inputViewHandle">
                            <span>Country<strong> {{$supplierdata->bankcountry}}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordionWrapper">
                @foreach ($requisitiondata as $requisition)
                <div class="accordion">
                    <div class="accdnTitle">
                        <a href="{{ URL::to($requisition->strrequesturl, ['id' => Crypt::encrypt($requisition->requisition_id)]) }}" target="_blank"><i></i>Requisition Code : {{$requisition->requisition_code}}</a> <em>Payment Amount : <strong><?php echo Customhelper::numberformatter($requisition->pay_amount) ?></strong></em>

                        <div class="iconInfoWrapper">
                            <a href="javascript:gettransactionhistory('{{$requisition->requisition_code}}')" class="btnTransaction  btnTooltip"><img src="{{ URL::asset('images/iconTrancation.png')}}" alt="Transaction"></a>
                            <div class="tooltipInfo">
                                <a href="javascript:void(0);" class="infoClose"></a>
                                <a href="javascript:void(0);" class="btnAction print bgGreen printtransaction">Print</a>
                                <div class="tbleListWrapper ">
                                    <div class="inputView totAmntHandle">
                                        <span>Requisition : <strong id="reqcode_{{$requisition->requisition_id}}"></strong></span>
                                        <span class="right">Total Amount : <strong id="reqamount_{{$requisition->requisition_id}}"></strong></span>
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
                                        <tbody id="history_{{$requisition->requisition_id}}">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
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
                checknumber: {required: true},
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
        
        $('#account').keyup(function () {
            $(".account_list").html('');
            $('#account_id').val('');
            $('#account_code').val('');
            $('#account_name').val('');
            $('#from_ledger_type').val('');

            var searchkey = $(this).val();

            jQuery.ajax({
                url: "../autocompletegeneralledgers",
                type: 'POST',
                data: {searchkey: searchkey},
                success: function (result) {
                    var total = result.length;
                    if (total == 0) {
                        resetaccount();
                    }
                    var liname = '';
                    $.each(result, function (i, value) {
                        liname += '<li id=' + value['accountid'] +
                                ' attrcode=' + value['code'] +
                                ' attrtype="' + value['type'] +
                                '" attrname="' + value['first_name'] + '">' + value['first_name'] + " (" + value['code'] + ")" + '</li>';
                    });

                    $(".account_list").html(liname);

                    var $selectText = $('.bgSelect input');
                    var $selectLi = $('.bgSelect li');

                    var selectval;
                    var Drop = 0;

                    $('body').click(function () {
                        if (Drop == 1) {
                            $('.bgSelect ul').hide();
                            Drop = 0;
                        }
                    });

                    $selectText.click(function () {
                        $('.bgSelect ul').hide();
                        Drop = 0;
                        if (Drop == 0) {
                            $(this).parent().find('ul').slideDown();
                        }
                        setTimeout(function () {
                            Drop = 1;
                        }, 50);

                    });

                    $selectLi.click(function () {
                        Drop = 1;
                        selectval = $(this).text();

                        $('#account').val(selectval);

                        $(this).parent().parent().find('.commonError').hide();
                    });

                    $('.bgSelect li').click(function () {
                        $('#account_id').val($(this).attr('id'));
                        $('#account_code').val($(this).attr('attrcode'));
                        $('#account_name').val($(this).attr('attrname'));
                        $('#from_ledger_type').val($(this).attr('attrtype'));
                    });
                },
            });
        });
        
        $('#frmsavepaymentadvice').submit(function(e) {
            if($('#account_id').val()==''){
                $('#account').val('');
                $('#frmsavepaymentadvice').valid();
                return false;
            }
            
            if(!$('#frmsavepaymentadvice').valid()){
                return false;
            }
            
            if(parseFloat($("#payamount").val())==0){
                alert("Amount should not be zero"); 
                return false;
            }
            
            var blnConfirm = confirm("Are you sure to submit");
            if (!blnConfirm) {
                return false;
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

    
    function resetaccount() {
        $('#account_id').val('');
        $('#account').val('');
        $('#account_code').val('');
        $('#account_name').val('');
        $('#from_ledger_type').val('');
    }
    
    function gettransactionhistory(code) {
         $.ajax({
            type: 'POST',
            url: '../gettransactionhistory',
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