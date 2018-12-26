@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Create <span>Inventory Request</span></h1>
    </header>	

    <form action="" method="post" id="frminventoryrequest">
        <div class="fieldGroup" id="fieldSet1">
<!--            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Request Id</label>
                        <input type="text" name="request_id" id="request_id" readonly="readonly" value="">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>-->
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Branch Code</label>
                        <select  name="branch_id" id="branch_id">
                            <option selected value=''>Select Branch</option>
                            @foreach ($supervisor_branches as $branch)
                            <option value='{{ $branch->branch_id }}'>{{ $branch->branch_code.'-'.$branch->branch_name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Warehouse</label>
                        <select class="commoSelect" name="warehouse_id" id="warehouse_id">
                            <option value=''>Select Warehouse</option>
                            @foreach ($warehouses as $warehouse)
                            <option value='{{ $warehouse->id }}'>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>



        </div>
    </form>	
    <form id="frmproductlist">
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder bgSelect">
                    <label>Select Product</label>
                    <select name="product_id" id="product_id">
                        <option value=''>Select Product</option>
                        @foreach ($products as $product) 
                        <option value='{{ $product->id }}'>{{ $product->product_code}} : {{ $product->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="custCol-2">
                <div class="inputHolder product_quantity">
                    <label>Quantity</label>
                    <input type="text" name="quantity" class="number" id="quantity" onpaste="return false;" autocomplete="off">
                </div>
            </div>
            <div class="custCol-2">
                <div class="inputHolder product_unit">
                    <label>Unit</label>
<!--                    <select name="unit_id" id="unit_id">
                        <option value=''>Select Unit</option>
                    </select>-->
                    
                    <input type="text" name="unit" id="unit" readonly="true">
                    <input type="hidden" name="unit_id" id="unit_id">
                </div>
            </div>
            <div class="custCol-3">
                <a class="btnAction action bgGreen" id="add_product" style="margin-top: 35px;"> Add</a>
            </div>
        </div>
       
        <div class="listHolderType1">
            <div class="listerType1"> 
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>Product</td>
                            <td>Quantity</td>
                            <td>Unit</td>
                            <td>Remove</td>
                        </tr>
                    </thead>
                    <tbody id="producctlist">
                        <tr><td>No products added</td></tr>
                    </tbody>
                </table>

            </div>					
        </div>

    </form>
    <div class="customClear"></div>
    <div class="commonLoaderV1"></div>
    
    <div class="custRow">
        <div class="custCol-4">
            <input type="button" value="Create" id="btnCreateItemReq" class="commonBtn bgGreen addBtn" >
        </div>
    </div>
</div>
<script>
    var arrProductsList = [];
    $(document).ready(function ()
    {

        $("#frminventoryrequest").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                branch_id: {required: true},
                warehouse_id: {required: true},
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                branch_id: "Select Branch",
                warehouse_id: "Select Warehouse",
            }
        });

        $("#frmproductlist").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                product_id: {required: true},
                quantity: {required: true},
                unit: {required: true},
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                product_id: "Select Product",
                quantity: "Enter Quantity",
                unit: "Select Unit",
            }
        });


        $('#btnCreateItemReq').on('click', function () {

            if (!$("#frminventoryrequest").valid()) {
                return;
            }

            if (arrProductsList.length == 0) {
                alert("Please Add Atleast One Product");
                return;
            }

            var arraData = {
                branch_id: $("#branch_id").val(),
                inventory_group_id: $("#inventory_group_id").val(),
                warehouse_id: $("#warehouse_id").val(),
            }

            arraData = encodeURIComponent(JSON.stringify(arraData));
            var arrProductList = encodeURIComponent(JSON.stringify(arrProductsList));
            
            $('#btnCreateItemReq').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../inventory_request/store',
                data: '&arraData=' + arraData + '&arrProductList=' + arrProductList,
                success: function (return_data) {
                    $('#btnCreateItemReq').removeAttr('disabled');
                    window.location.href = '{{url("supervisors/inventory_request")}}';
                },
                error: function (return_data) {
                    $('#btnCreateItemReq').removeAttr('disabled');
                    window.location.href = '{{url("supervisors/inventory_request")}}';
                }
            });
            
            $('#btnCreateItemReq').removeAttr('disabled');
        })


        $('#add_product').on('click', function () {
            
            if($("#branch_id").val()==''){
                alert("Please Select Branch");
                return;
            }
            
            if (!$("#frmproductlist").valid()) {
                return;
            }
            
            if(parseFloat($("#quantity").val())==0){
                alert("Quantity cannot be zero");
                return;
            }
            
            var intItemDuplicate = 0;
            if(arrProductsList.length>0){
                for(var i=0;i<arrProductsList.length;i++){
                    if($("#product_id").val() == arrProductsList[i].product_id){
                        intItemDuplicate = 1;
                    }
                }
            }
            
            if(intItemDuplicate == 1){
                alert("Product Already Selected");
                return;
            }
            
            var intMaxValue = 0;
            var intPresentStock = 0;
            $.ajax({
                type: 'POST',
                url: '../inventory_request/checkmaximumqty',
                data: '&product_id=' + $("#product_id").val() +
                      '&branch_id=' + $("#branch_id").val(),
                async:false,
                success: function (return_data) {
                    intMaxValue = return_data.maxValue;
                    intPresentStock = return_data.presentStock;
                }
                
            });
            
            
            if((parseInt($("#quantity").val()) + parseInt(intPresentStock)) > parseInt(intMaxValue)){
                alert("Request Qty ("+$("#quantity").val()+") + Present Stock ("+intPresentStock+") should not exceed Maximum limit ("+intMaxValue+")");
                return;
            }
            
            var arraData = {
                product_id: $("#product_id").val(),
                product_name: $("#product_id :selected").text(),
                quantity: $("#quantity").val(),
                unit_id: $("#unit_id").val(),
                unit: $("#unit").val(),
//                unit: $("#unit_id :selected").text(),
            }

            arrProductsList.push(arraData);
            $("#product_id").val('');
            $("#quantity").val('');
            $("#unit_id").val('');
            $("#unit").val('');
            showproductlist();

        })
        
       $('.number').keypress(function(event) {

            if(event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46) {
                 return true;
            } else if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)){
                 event.preventDefault();
            }
             
            if($(this).val() == parseFloat($(this).val()).toFixed(2))
            {
                event.preventDefault();
            }
            
            return true;
       });
        
        $('#product_id').on('change', function () {
            if($('#product_id').val() != ''){
                $.ajax({
                    type: 'POST',
                    url: '../inventory_request/getunits',
                    data: '&product_id=' + $("#product_id").val(),
                    async:false,
                    success: function (return_data) {
                       
//                        $('#unit_id').html(return_data);
                        $('#unit').val(return_data.name);
                        $('#unit_id').val(return_data.unitid);
                    }

                });
            }else{
                $('#unit_id').html("<option value=''>Select Unit</option>");
            }
        });
    
    });

    function showproductlist()
    {
        $("#producctlist").html('<tr><td>No products added<td></tr>');
        if (arrProductsList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrProductsList.length; i++) {
                strHtml += '<tr><td>' + arrProductsList[i].product_name + '</td><td>' + arrProductsList[i].quantity + '</td>\n\
                    <td>' + arrProductsList[i].unit + '</td><td><a href="javascript:remove(' + i + ')">Remove</a></td></tr>';
            }
            $("#producctlist").html(strHtml);
        }
    }

    function remove(index) {
        arrProductsList.splice(index, 1);
        showproductlist();
    }

</script>
@endsection