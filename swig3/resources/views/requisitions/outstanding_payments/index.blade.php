@extends('layouts.main')
@section('content')
<script>
    $(window).on('hashchange', function () {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');
            if (page == Number.NaN || page <= 0) {
                return false;
            } else {
                getData(page);
            }
        }
    });

    $(document).ready(function ()
    {
        $(document).on('click', '.pagination a', function (event)
        {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });
        
    });

        
    function getData(page) {
        var supplier = $('#supplier').val();
        var pagelimit = $('#page-limit').val();
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: {supplier:supplier, pagelimit: pagelimit},

                })
            .done(function (data)
            {
                console.log(data);

                $(".outstanding_list").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                alert('No response from server');
            });
    }


</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <div class="innerContent">
        <header class="pageTitleV3">
            <h1>Outstanding Requisition</h1>
        </header>
        <form action="{{ url('requisitions/outstanding_payments/add') }}" id="frmrequests" method="post" enctype="multipart/form-data">
            <div class="inputAreaWrapper">
                <div class="custRow ">
                    <div class="custCol-4 ">
                        <div class="inputHolder" clsparentdiv>
                            <label>Select Supplier</label>
                            <select id="supplier" name="supplier" class="supplier chosen-select">
                                <option value=''>Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" >{{ $supplier->code}}:{{$supplier->first_name}}</option>
                                @endforeach
                            </select>
                            <span class="commonError"></span>
                        </div>
                    </div>
                </div>
                <div class="statusMessage">
                        <span class="errorMsg" ></span>
                </div>
                <div class="tbleListWrapper ">

                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder">
                            <tr>
                                <th>Requisition Code</th>
                                <th>Title</th>
                                <th class="amountAlign">Total Amount</th>
                                <th class="amountAlign">Balance Amount </th>
                                <th>Current Payment</th>
                                <th>Full Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tbody class="outstanding_list" id='outstanding_list'>                  
                                @include('requisitions/outstanding_payments/results')
                            </tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div> 
        
            <input type="hidden" id="selectedrequests" name="selectedrequests">
            <input type="hidden" id="supplier_id" name="supplier_id">
            <input type="hidden" id="totalpayamount" name="totalpayamount">
            <input type="hidden" id="requisitiontotal" name="requisitiontotal">
            
        </form>
        <div class="pagesShow">
            <span>Showing 10 of 20</span>
            <select id="page-limit">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
         <div class="bottomBtnsHolder">
             <input type="button" class="btnIcon btnSaveV3  lightGreenV3" id="btnSavePayment" value="Make Payment">
            <div class="customClear"></div>
        </div>
</div>
<script>
    var arrRequisitions=[];
    $(".statusMessage").hide();
    $(function () {
        
         $("#frmrequests").validate({
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
                supplier: {required: true},
                
            },
            messages: {
                supplier: "Select Supplier",
            }
        });
        
        $('body').on('click', '.chkfullpayment', function () {
            var id=$(this).attr('id');
            if ($(this).prop('checked')) {
                var balance=$("#balance_"+id).val();
                $('#amount_'+id).val(balance);
                $('#amount_'+id).prop("disabled",true);
            }else{
                $('#amount_'+id).val("");
                $('#amount_'+id).prop("disabled",false);
            }

            calculatetotal();
        });

        $('body').on('keyup', '.txtamount', function () {
            calculatetotal();
            var txtid=$(this).attr('attrid');
            $("#amount_"+txtid).removeClass("txtamounterror");
        });
        
        $("#btnSavePayment").click(function () {
            
            if(!$("#frmrequests").valid()){
                return;
            }
            
            var paymetexceeds=0;
            var nonzerovalue=1;
            $(".txtamount").each(function() {
                var txtid=$(this).attr('attrid');
                if($("#amount_"+txtid).val()=="."){
                    $("#amount_"+txtid).val('');
                }
                
                if(parseFloat($("#amount_"+txtid).val())==0){
                    nonzerovalue=0;
                }

                if($(this).val()!=''){
                    if(parseFloat($("#amount_"+txtid).val())>parseFloat($("#balance_"+txtid).val())){
                        $("#amount_"+txtid).addClass("txtamounterror");
                        paymetexceeds=1;
                    }else{
                        $("#amount_"+txtid).removeClass("txtamounterror");
                    }
                }
            });
            
            if(nonzerovalue==0){
                $(".errorMsg").html("Amount should not be zero");
                $(".statusMessage").show();
                return;
            }else{
                $(".statusMessage").hide();
            }
            
            if(paymetexceeds==1){
                $(".errorMsg").html("Payment Exceeds Balance Amount");
                $(".statusMessage").show();
                return;
            }else{
                $(".statusMessage").hide();
            }
            
            var arrids=new Array();
            $(".txtamount").each(function() {
                if($(this).val()!=''){
                    arrids.push($(this).attr('attrid'));
                }
            });
            
            var intpendingpaymentexist=-1;
            if(arrids.length>0){
                var arrreqids=JSON.stringify(arrids);
                $.ajax({
                    type: 'POST',
                    url: 'checkpendinpaymentexist',
                    data: '&arrids=' + arrreqids,
                    async: false,
                    success: function (data) {
                        if(data!=-1){
                            if(data.intpendingpaymentexist==1){
                                intpendingpaymentexist=1;
                                $(".errorMsg").html("Pending payment advices exist for requisitions "+data.strcodes);
                                $(".statusMessage").show();
                                var result=data.reqdata;
                                for(var i=0;i<result.length;i++){
                                    $("#amount_"+result[i].requisition_id).addClass("txtamounterror");
                                }
                            } 
                        }
                    }
                });
            }
            
            if(intpendingpaymentexist==1){
                return;
            }else{
                $(".statusMessage").hide();
            }
            
            $(".txtamount").each(function() {
                if($(this).val()!=''){
                    addtolist($(this).attr('attrid'));
                }
            });
        
            if (arrRequisitions.length == 0) {
                alert("Please select atleast one requisition");
                return;
            }
            
            var arrreq=JSON.stringify(arrRequisitions);
            $("#selectedrequests").val(arrreq);
            $("#supplier_id").val($("#supplier").val());
            
            $('#btnSavePayment').attr('disabled','disabled');
            $("#frmrequests").submit();
        });
        
        
        $('body').on('change', '.supplier', function () {
            if ($("#supplier").val() != '') {
                search();
            } else {
                $(".outstanding_list").html('<tr><td>No Records Found<td></tr>');
            }
        });
        
        $('#page-limit').on("change", function () {
            search();
        });

    });


    function calculatetotal() {
        var price=0;
        var totalprice=0;
        $(".txtamount").each(function() {
            if($(this).val()!=''){
                price = parseFloat(price) + parseFloat($(this).val());
                price=amountformat(price);
                var id=$(this).attr('attrid');
                totalprice=parseFloat(totalprice) + parseFloat($("#total_"+id).val());
            }
        });
        
        if(price=='NaN'){
            price=0;
        }
        
        if(totalprice=='NaN'){
            totalprice=0;
        }
        
        $("#totalpayment").html(price);
        $("#totalpayamount").val(price);
        $("#requisitiontotal").val(totalprice);
    }

    function addtolist(req_id) {
        var arraData = {
            requisition_id: req_id,
            requisition_code: $("#code_"+req_id).val(),
            outstanding_amount: $("#balance_"+req_id).val(),
            pay_amount: $("#amount_"+req_id).val(),
            req_name: $("#type_"+req_id).val(),
        }

       arrRequisitions.push(arraData);
    }

    function remove(req_id) {
        for (var i = 0; i < arrRequisitions.length; i++) {
            if(req_id==arrRequisitions[i].requisition_id){
                arrRequisitions.splice(i, 1);
            }
        }
    }
    
    function search()
    {
        var supplier = $('#supplier').val();
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'outstanding_payments',
            data: {supplier:supplier, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                $('.outstanding_list').html(return_data);
                $(".commonLoaderV1").hide();
            }
        });
    }
    

</script>
@endsection