@extends('layouts.main')
@section('content')
<div class="contentArea">

    <div class="innerContent">
        <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
        <header class="pageTitleV3">
            <h1>Service Requisitions</h1>
        </header>
        <div class=" inputAreaWrapper">
            <div class="statusMessage">
		<span class="errorMsg" ></span>
            </div>
            
            <form id="frmaddrequisition" method="post" enctype="multipart/form-data">
            <div class="custRow reqCodeDateHolder">
                <div class="custCol-6">
                    <label>Requisition Date : <span><?php echo date('d-m-Y'); ?></span></label>

                </div>
                <div class="custCol-6 alignRight">
                    <label>Requisition Code : <span>{{$requisitioncode}}</span></label>  
                </div>
            </div>
            <div class="custRow ">
                <div class="custCol-4">
                    <div class="inputHolder  ">
                        <label>Requisition Title</label>
                        <input type="text" placeholder="Enter Title" id="title" name="title" autocomplete="off" maxlength="250">
                        <input type="hidden" value="{{$requisitioncode}}" id="requisition_code" name="requisition_code">
                        <span class="commonError"></span>
                    </div>
                    <div class="inputHolder clsparentdiv">
                        <label>Select Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="supplier_id chosen-select">
                            <option value=''>Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" >{{$supplier->first_name}} ({{ $supplier->code}})</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
               
                    <div class="inputHolder  ">
                        <label>Amount </label>
                        <input type="text" placeholder="Enter Amount" id="amount" name="amount" autocomplete="off" class="numberwithdot" maxlength="15" value="">
                        <span class="commonError"></span>
                    </div>
               
                </div>
                <div class="custCol-8 suplierView">
                    <div class="tabWrapper">
                        <ul>
                            <li rel="basic">Basic</li>
                            <li rel="budget">Budget</li>
                            <li rel="bank">Bank</li>
                        </ul>
                        <div class="tabDtls">
                            <div class="btnTab">Basic</div>
                            <div id="basic" class="tabContent">
                                <div class="tbleListWrapper ">
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody class="tblbasic">
                                            <tr><td>No Records Found<td></tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="btnTab">Budget</div>
                            <div id="budget" class="tabContent">
                                <div class="tbleListWrapper ">
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody class="tblbudget">
                                            <tr><td>No Records Found<td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="btnTab">Bank</div>
                            <div id="bank" class="tabContent">
                                <div class="tbleListWrapper ">
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody class="tblbank">
                                            <tr><td>No Records Found<td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>  
            
            <form id="frmdocs" name="frmdocs" method="post" enctype="multipart/form-data">
                <div class="custRow" >
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Upload Document</label>
                            <input type="file" name="req_doc" class="reqdocument" id="req_doc" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                </div>
            </form> 
            <div class="custRow ">
                <div class="custCol-6">
                    <div class="inputHolder  ">
                        <label>Description</label>
                        <textarea id="description" name="description" placeholder="Enter Description"></textarea>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-6 reqBtnHolder">
                    <input type="button" id="btnSaveRequisition" class="btnIcon lightGreenV3" value="Make Request">

                </div>
            </div>
           
        </div>
    </div>
    <div class="commonLoaderV1"></div>
</div>
<input type="hidden" name="pending" id="pending">
<script>
    $('.statusMessage').hide();
    $('.commonLoaderV1').hide();
    $('#btnSaveRequisition').removeAttr('disabled');
    
    $(function () {
        var v = jQuery("#frmaddrequisition").validate({
            rules: {
                title: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                supplier_id: {
                    required: true,
                },
                amount: {
                    required: true,
                },
            },
            messages: {
                title: "Enter Title",
                supplier_id: "Select Supplier",
                amount: "Enter Amount",
            },
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
        });

  $('#amount').bind('keyup', function () {
              $('.statusMessage').hide();
    });
 
 
        $('body').on('change', '.supplier_id', function () {
            if ($("#supplier_id").val() != '') {
                $.ajax({
                    type: 'POST',
                    url: '../getsupplierdata',
                    data: '&supplier_id=' + $("#supplier_id").val(),
                    success: function (return_data) {
                        if (return_data != -1) {
                            var supplierdata = return_data.supplierdata; 
                            var budgetdata = return_data.budgetdata;  
                            
                            
                            var strRegistred = "Registered";
                            if (supplierdata.registration_type == 0) {
                                strRegistred = "Not Registered";
                            }
                            var strHtmlBasic = '<tr><td>' + supplierdata.code + '</td><td>' + supplierdata.first_name + ' ' + supplierdata.alias_name + '</td><td>' + strRegistred + '</td></tr><tr><td>' + supplierdata.nationality + '</td><td>' + supplierdata.mobile_number + '</td><td>' + supplierdata.email + '</td></tr><tr><td>Contact Info :</td><td>' + supplierdata.contact_number + '</td><td>' + supplierdata.contact_email + '</td></tr>';
                            var strHtmlBank = '<tr><td>Swift Code : ' + supplierdata.bank_swift_code + '</td><td> Name : ' + supplierdata.bank_branch_name + '</td></tr><tr><td>Beneficiary : ' + supplierdata.bank_beneficiary_name + '</td><td>Ac No : ' + supplierdata.bank_account_number + '</td></tr><tr><td>Country : ' + supplierdata.bankcountry + '</td><td>Credit Limit : ' + supplierdata.creditlimitformated + '</td></tr>';
                            strHtmlBank = strHtmlBank.replace(/null/g, '');
                             $("#pending").val(budgetdata.price - budgetdata.usedData);
                            $(".tblbasic").html(strHtmlBasic);
                            var strHtmlBudget = '<tr><td>No Records Found<td></tr>'
                            if(Object.keys(budgetdata).length>0){
                                var strHtmlBudget = '<tr><td>Total : ' + budgetdata.format_initial + '</td></tr><tr><td>Used : ' + budgetdata.format_used + '</td></tr><tr><td>Pending : '+ budgetdata.format_balance +'</td></tr>';
                            }
                            $(".tblbudget").html(strHtmlBudget);
                            $(".tblbank").html(strHtmlBank);
                        } else {
                            $(".tblbasic").html('<tr><td>No Records Found<td></tr>');
                            $(".tblbudget").html('<tr><td>No Records Found<td></tr>');
                            $(".tblbank").html('<tr><td>No Records Found<td></tr>');
                        }
                    }
                });
            } else {
                $(".tblbasic").html('<tr><td>No Records Found<td></tr>');
                $(".tblbudget").html('<tr><td>No Records Found<td></tr>');
                $(".tblbank").html('<tr><td>No Records Found<td></tr>');
            }
        });
        
        $("#btnSaveRequisition").click(function () {
            if (!$("#frmaddrequisition").valid()) {
                return;
            }
            
               var pending=  $("#pending").val();
               var requisitionTotalPrice=  $("#amount").val();
            
                 if(isNaN(pending) || pending==""){
                   pending=0;
                 }
                 
            if(parseFloat($("#amount").val())==0){
                $('.errorMsg').text("Amount should not be zero"); 
                $('.statusMessage').show();
                return;
            }else{
                $('.statusMessage').hide();
            }
            
            if(parseFloat(requisitionTotalPrice)>parseFloat(pending)){
                
                  $('.errorMsg').text("Total price exceeds your user budget"); 
                  $('.statusMessage').show();
                return;
            }
            
            var blnConfirm = confirm("Are you sure to submit");
            if(!blnConfirm){
               return; 
            }
            
            var arraData = {
                requisition_code: $('#requisition_code').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                supplier_id: $("#supplier_id").val(),
                price: amountformat($("#amount").val()),
            }

            var arrData = JSON.stringify(arraData);
            
            var documents = new FormData($('#frmdocs')[0]);
            documents.append('arrData', arrData);
            
            $('.commonLoaderV1').show();
            $('#btnSaveRequisition').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: 'store',
                data:documents, 
                contentType: false,  
                processData: false,
                success: function (return_data) {
                    $('.commonLoaderV1').hide();
                    window.location.href = '{{url("requisitions/outbox")}}';
                }
            });
            
            $('#btnSaveRequisition').removeAttr('disabled');
            
        });
        
    });
</script>
@endsection