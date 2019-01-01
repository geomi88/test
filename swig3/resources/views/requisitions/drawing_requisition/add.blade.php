@extends('layouts.main')
@section('content')
<div class="innerContent">

    <div class="statusMessage">
        <span class="errorMsg" ></span>
<!--<span class="successMsg" ></span>-->
    </div>
    <div class="customClear"></div>
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Create Drawing Requisition</h1>
    </header>
    <div class=" inputAreaWrapper">


        <form id="frmaddrequisition" method="post" enctype="multipart/form-data">
            <div class="custRow reqCodeDateHolder">
                <div class="custCol-6">
                    <label>Date : <span><?php echo date('d-m-Y'); ?></span></label>

                </div>
                <div class="custCol-6 alignRight">
                    <label>Requisition Code : <span>{{$requisitioncode}}</span></label>
                </div>
            </div>
            <div class="custRow ">
                <input type="hidden"  name="pending" id="pending" >  
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Requisition Title</label>
                        <input type="text" id="title" name="title" autocomplete="off" placeholder="Enter Title" maxlength="250">
                        <input type="hidden" value="{{$requisitioncode}}" id="requisition_code" name="requisition_code">
                        <span class="commonError"></span>
                    </div>


                </div>

                <div class="customClear"></div>
                <div class="custCol-4 leaderInfo">

                    <div class="inputHolder  clsparentdiv">
                        <label>Choose Ledger</label>
                        <select id="supplier_id" name="supplier_id" class="supplier_id chosen-select" onchange="budgetInfo()">
                            <option value=''>Choose Ledger</option>
                            @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" >{{ $supplier->code}}:{{$supplier->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                    <div class="iconInfoWrapper">

                        <a href="javascript:void(0);" class="tbleInfo btnTooltip" title="information"></a>
                        <div class="tooltipInfo">
                            <a href="javascript:void(0);" class="infoClose"></a>
                            <strong >Budget Details</strong>
                            <span>Intial Budget Amount:<strong class="initialBgt"></strong> </span>
                            <span>Used Budget Amount:<strong class="usedBgt"></strong> </span>
                            <span>Balance Budget Amount:<strong class="balanceBgt"></strong> </span>

                        </div>
                    </div>
                    <!--                                </div>-->
                </div>
                <div class="custCol-4 amtInputField">
                    <div class="inputHolder  ">
                        <label>Amount </label>
                        <input type="text" placeholder="Enter Amount" onpaste="return false;" class="numberwithdot" name="total_price"  id="total_price" value="" maxlength="20">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="customClear"></div>
                
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea id="description" name="description" placeholder="Enter Description"></textarea>
                        <span class="commonError"></span>
                    </div>
                </div>
                
            </div>
            <input type="hidden" value="General Ledger" id="party_type" name="party_type"> 

        </form>
        
        <form id="frmdocs" name="frmdocs" method="post" enctype="multipart/form-data">
            <div class="custRow" >
                <div class="custCol-4 ">
                    <div class="inputHolder">
                        <label>Upload Document</label>
                        <input type="file" name="req_doc" class="reqdocument" id="req_doc" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="custCol-12">
            <div class="inputBtnHolder">
                <input type="button" id="btnSaveRequisition" class="btnIcon  lightGreenV3" value="Submit">
            </div>
        </div>
    </div>



</div>
<div class="commonLoaderV1"></div>


<div class="customClear "></div>

<script>
    $('.statusMessage').hide();
    function budgetInfo() {

        if ($("#supplier_id").val() != '') {
            $.ajax({
                type: 'POST',
                url: '../getgeneralledgerdata',
                data: '&supplier_id=' + $("#supplier_id").val(),
                success: function (return_data) {

                    if (return_data != -1) {

                        var budgetdata = return_data.budgetdata;
                        var useddata = return_data.useddata;
                        if (budgetdata == null) {
                            $('.initialBgt').text("0");
                            $('.usedBgt').text("0");
                            $('.balanceBgt').text("0");
                            $("#pending").val("0");

                        } else {
                            $('.initialBgt').text(budgetdata.price);
                            $('.usedBgt').text(useddata);
                            $('.balanceBgt').text(budgetdata.price - useddata);
                            $("#pending").val(budgetdata.price - useddata);

                        }
                    } else {
                        $('.initialBgt').text("0");
                        $('.usedBgt').text("0");
                        $('.balanceBgt').text("0");
                        $("#pending").val("0");
                    }
                }
            });
        } else {
            $(".tblbasic").html('<tr><td>No Records Found<td></tr>');
            $(".tblbudget").html('<tr><td>No Records Found<td></tr>');
            $(".tblbank").html('<tr><td>No Records Found<td></tr>');
        }

    }

    $('#amount').bind('keyup', function () {
        $('.statusMessage').hide();
    });


    $("#btnSaveRequisition").click(function () {
        $('.statusMessage').hide();
        if (!$("#frmaddrequisition").valid()) {
            return;
        }

        if(parseFloat($("#total_price").val())==0){
            $('.errorMsg').text("Amount should not be zero"); 
            $('.statusMessage').show();
            return;
        }else{
            $('.statusMessage').hide();
        }

        var requisitionTotalPrice = $("#total_price").val();
        var pending = $("#pending").val();



        if (isNaN(pending)) {
            pending = 0;
        }

        if (parseFloat(requisitionTotalPrice) > parseFloat(pending)) {

            $('.errorMsg').text("Total price exceeds your user budget");
            $('.statusMessage').show();
            return;
        }
        var blnConfirm = confirm("Are you sure to submit");
        if (!blnConfirm) {
            return;
        }



        var arraData = {
            requisition_code: $('#requisition_code').val(),
            title: $('#title').val(),
            description: $('#description').val(),
            supplier_id: $("#supplier_id").val(),
            party_type: $("#party_type").val(),
            isallproducts: "",
            total_price: amountformat($("#total_price").val()),
        }

        var arrData = JSON.stringify(arraData);
        
        var documents = new FormData($('#frmdocs')[0]);
        documents.append('arrData', arrData);
        
        $('.commonLoaderV1').show();
        $('#btnSaveRequisition').attr('disabled', 'disabled');
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
                 total_price: {
                    required: true,
                    number: true,
                },
            },
            messages: {
                title: "Enter Title",
                supplier_id: "Choose Ledger",
                total_price: {
                 required: "Enter the Amount",
                 number: "Enter a valid amount"
                 }
            },
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id = $(element).attr("id") + "_chosen";
                if ($(element).hasClass('valErrorV1')) {
                    $("#" + id).find('.chosen-single').addClass('chosen_error');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });
        
    });


</script>
@endsection