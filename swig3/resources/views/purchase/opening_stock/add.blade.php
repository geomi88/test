@extends('layouts.main')
@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('purchase')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Add Opening Stock</h1>
    </header>
    <div class="inputAreaWrapper">
        <form id="frminventory" method="post" enctype="multipart/form-data">
            <div class="custRow reqCodeDateHolder">
                
                <div class="custCol-6 ">
                    <label>Batch Code : <span>{{$batchcode}}</span></label>
                    <input type="hidden" name="batchcode" id="batchcode" value="{{$batchcode}}">
                </div>
                <div class="custCol-6 alignRight">
                    <label>Date : <span><?php echo date('d-m-Y'); ?></span></label>
                </div>
            </div>
            <div class="custRow ">
                <div class="custCol-4">
                    
                    <div class="inputHolder clsparentdiv">
                        <label>Inventory Item</label>
                        <select id="inventory_id" name="inventory_id" class="inventory_id chosen-select">
                            <option value=''>Select Item</option>
                            @foreach ($inventorydata as $item)
                            <option value="{{ $item->id }}" >{{ $item->product_code}} : {{$item->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                        
                        <input type="hidden" id="ismfgrequired" >
                        <input type="hidden" id="isexprequired" >
                        <input type="hidden" id="itemcompany" >
                    </div>

                    
                </div>
                <div class="custCol-8 suplierView">
                    <div class="tabWrapper">
                        <ul>
                            <li rel="basic" style="background: #71699f;color: #fff">Opening Stock</li>
<!--                            <li rel="budget">Budget</li>
                            <li rel="bank">Bank</li>-->
                        </ul>
                        <div class="tabDtls">
                            <div class="btnTab">Openng Stock</div>
                            <div id="basic" class="tabContent">
                                <div class="tbleListWrapper ">
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody class="tblprevstock">
                                            <tr><td>No Records Found</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="statusMessage">
		<span class="errorMsg" ></span>
        </div>
        <form id="frmaddstock">
            <div class="invertoryFieldHolder clsparentdiv">
                <div class="custRow">
                    <div class="inputHolder checkHolder">
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="stockearea" id="chkwarehouse" checked value="1" type="radio" >
                                <span></span>
                                <em>Warehouse</em>
                            </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="stockearea" id="chkbranch" value="2" type="radio">
                                <span></span>
                                <em>Branch</em>
                            </label>
                        </div>
                    </div>
                    <div class="customClear "></div>
                    <div class="custCol-4 clswarehouse">
                        <div class="inputHolder">
                            <label>Stock Area</label>
                            <select id="warehouse_id" name="warehouse_id" class="chosen-select">
                                <option value=''>Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" >{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <div class="custCol-4 clsbranch" style="display: none;">
                        <div class="inputHolder">
                            <label>Stock Area</label>
                            <select id="branch_id" name="branch_id" class="chosen-select">
                                <option value=''>Select Branch</option>
                                @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" >{{$branch->branch_code}} : {{$branch->name}}</option>
                                @endforeach
                            </select>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <div class="custCol-3 unit">
                        <div class="inputHolder">
                            <label>Unit</label>
                            <select id="unit_id" name="unit_id" class="clsunit">
                                <option value=''>Select Unit</option>
                            </select>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="customClear "></div>
                    
                    <div class="custCol-2 price">
                        <div class="inputHolder">
                            <label>Quantity</label>
                            <input type="text" placeholder="Qty" onpaste="return false;" name="quantity" id="quantity" class="numberwithdot" maxlength="10">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <div class="custCol-2 price">
                        <div class="inputHolder">
                            <label>Price / Unit</label>
                            <input type="text" id="priceperunit" onpaste="return false;" name="priceperunit" placeholder="Price / Unit" class="numberwithdot" maxlength="15">
                            <span class="commonError"></span>
                        </div>
                    </div>

                    <div class="custCol-2 price">
                        <div class="inputHolder">
                            <label>Price</label>
                            <input type="text" placeholder="Price" name="price" id="price" disabled class="numberwithdot">
                            <input type="hidden" name="total_price" id="total_price">
                            
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <div class="custCol-2 price">
                        <div class="inputHolder">
                            <label>Mfg. Date</label>
                            <input type="text" placeholder="Mfg. Date" name="mfgdate" id="mfgdate" readonly class="numberwithdot">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <div class="custCol-2 price">
                        <div class="inputHolder">
                            <label>Expiry</label>
                            <input type="text" placeholder="Expiry" name="exp_date" id="exp_date" readonly  class="numberwithdot">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <div class="custCol-2 addBtnHolder">
                        <a id="add_stock" class="btnIcon lightGreenV3">Add</a>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="tbleListWrapper ">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead class="headingHolder">
                    <tr>
                        <th style="min-width: 120px;">Stock Area</th>
                        <th>Unit</th>
                        <th class="amountAlign">Quantity</th>
                        <th class="amountAlign">Price/Unit</th>
                        <th class="amountAlign">Total Price</th>
                        <th class="tbleActionSet"></th>
                    </tr>
                </thead>
                <tbody id="tblopstock" class="tblopstock">
                    <tr><td>No Stock Added</td></tr>
                    
                </tbody>
            </table>
        </div>
        
        
        <div class="custRow " style="padding-top: 15px;">
            
            <div class="custCol-6">
                <input type="button" id="btnSaveStock" class="btnIcon lightGreenV3" value="Submit">
            </div>
        </div>

    </div>
    <div class="commonLoaderV1"></div>
</div>

<script>
    var arrStockList = [];
    var intGlobalPrimaryUnit = -1;
    $('.statusMessage').hide();
    $('#btnSaveStock').removeAttr('disabled');
    $(function () {
        var v = jQuery("#frminventory").validate({
            rules: {
               
                inventory_id: {
                    required: true,
                },
            },
            messages: {
                inventory_id: "Select Inventory",
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

        $("#frmaddstock").validate({
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
                warehouse_id: {
                            required: function(){
                                return $("#chkwarehouse").prop('checked');
                            }
                        },
                branch_id: {
                    required: function(){
                                return $("#chkbranch").prop('checked');
                            }
                        },
                quantity: {required: true,number:true},
                unit_id: {required: true},
                priceperunit: {required: true,number:true},
                mfgdate: {
                    required: function(){
                                return ($("#ismfgrequired").val()==1)?true:false;
                            }
                        },
                exp_date: {
                    required: function(){
                                return ($("#isexprequired").val()==1)?true:false;
                            }
                        },
            },
            messages: {
                warehouse_id: "Select Warehouse",
                branch_id: "Select Branch",
                quantity: "Quantity",
                unit_id: "Select Unit",
                mfgdate: "Select date",
                exp_date: "Select date",
                priceperunit: {
                    required: "Enter Price / Unit",
                    number: "Enter a valid amount"
                },
            }
        });
        
        $("#chkwarehouse").click(function () {
            $(".clsbranch").hide();
            $(".clswarehouse").show();
            $('#branch_id').val('').trigger('chosen:updated');
        });
        
        $("#chkbranch").click(function () {
            $(".clswarehouse").hide();
            $(".clsbranch").show();
            $('#warehouse_id').val('').trigger('chosen:updated');
        });


        $('#add_stock').on('click', function () {
           
            $('.statusMessage').hide();

            if (!$("#frminventory").valid()) {
                return;
            }
            
            if (!$("#frmaddstock").valid()) {
                return;
            }
            
            if($('#quantity').val()==0){
                alert("Please enter quantity greater than zero");
                return;
            }
            
            if($('#priceperunit').val()==0){
                alert("Please check the price you have entered");
                return;
            }
           
            var conversionvalue = $("#unit_id").find(':selected').attr('attrconversionval');
            if(isNaN(conversionvalue)){
                $('.errorMsg').text("Invalid unit conversion value, Please verify with inventory section."); 
                $('.statusMessage').show();
                return;
            }else{
                $('.statusMessage').hide();
            }
            
            var qtyinprimary=parseFloat($('#quantity').val())*parseFloat(conversionvalue);
            
            var stockareaname='';
            var stockareaid='';
            var stockarea=1;
            if($("#chkwarehouse").prop('checked')){
                stockareaname=$("#warehouse_id :selected").text();
                stockareaid=$('#warehouse_id').val();
                stockarea=1;
            }else{
                stockareaname=$("#branch_id :selected").text();
                stockareaid=$('#branch_id').val();
                stockarea=2;
            }
            
            var intItemDuplicate = 0;
            if (arrStockList.length > 0) {
                for (var i = 0; i < arrStockList.length; i++) {
                    if (stockareaid == arrStockList[i].stockareaid) {
                        intItemDuplicate = 1;
                    }
                }
            }

            if (intItemDuplicate == 1) {
                $('.errorMsg').text("Stock area already selected."); 
                $('.statusMessage').show();
                return;
            }else{
                $('.statusMessage').hide();
            }
            
            var isprimary=0;
            if($("#unit_id").val()==intGlobalPrimaryUnit){
                isprimary=1;
            }
            
            var arraData = {
                stockareaid: stockareaid,
                stockarea: stockarea,
                stockareaname: stockareaname,
                itemid: $("#inventory_id").val(),
                unitid: $('#unit_id').val(),
                unitname: $("#unit_id :selected").text(),
                quantity: parseFloat($('#quantity').val()),
                isprimary:isprimary,
                itemcompany: $('#itemcompany').val(),
                qtyinprimary: parseFloat(qtyinprimary),
                unitprice: parseFloat($('#priceperunit').val()),
                price: parseFloat($('#price').val()),
                mfgdate: $('#mfgdate').val(),
                expdate: $('#exp_date').val(),
            }
          
            arrStockList.push(arraData);
            showstocklist();
            resetproductform();
        });
     
        $("#btnSaveStock").click(function () {
            if (!$("#frminventory").valid()) {
                return;
            }
           
            if (arrStockList.length == 0) {
                alert("Please add atleast one opening stock");
                return;
            }
            
            var blnConfirm = confirm("Are you sure to submit");
            if(!blnConfirm){
               return; 
            }
            
            var arraData = {
                batchcode: $('#batchcode').val(),
            }

            var arrData = JSON.stringify(arraData);
            var arrStock = JSON.stringify(arrStockList);
            

            $('.commonLoaderV1').show();
            $('#btnSaveStock').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: 'updateopeningstock',
                data:{arrData:arrData,arrStockList:arrStock}, 
            }).done(function (data) {
                $('.commonLoaderV1').hide();
                $('#btnSaveStock').removeAttr('disabled');
                window.location.href = '{{url("purchase")}}';
            });
            
           
        });

        $('body').on('change', '.inventory_id', function () {
            clearall();
            if ($("#inventory_id").val() != '') {
                getinventorydata($("#inventory_id").val());
            }
            
        });
        
        $('body').on('change', '#warehouse_id', function () {
            if (!$("#frminventory").valid()) {
                $('#warehouse_id').val('').trigger('chosen:updated');
                return;
            }
          
            getpreviousopeningstock($("#warehouse_id").val(),1);
        });
        
        $('body').on('change', '#branch_id', function () {
            if (!$("#frminventory").valid()) {
                $('#branch_id').val('').trigger('chosen:updated');
                return;
            }
            
            getpreviousopeningstock($("#branch_id").val(),2);
        });

        $('body').on('keyup', '#quantity', function () {
            calculateprice();
        });
        
        $('body').on('keyup', '#priceperunit', function () {
            calculateprice();
        });

        $("#exp_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate:0
        });
        
        $("#mfgdate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            maxDate:0
        });
        
    });
    
    function showstocklist() {
        $("#tblopstock").html('<tr><td>No products added<td></tr>');
        if (arrStockList.length > 0) {
            var strHtml = '';
            var totPrice=0;
            var totQty=0;
            for (var i = 0; i < arrStockList.length; i++) {
                totPrice=parseFloat(totPrice)+parseFloat(arrStockList[i].price);
                totQty=parseFloat(totQty)+parseFloat(arrStockList[i].qtyinprimary);
                
                strHtml += '<tr><td>' + arrStockList[i].stockareaname + '</td><td>' + arrStockList[i].unitname + '</td>\n\
                    <td class="amountAlign">' + amountformat(arrStockList[i].quantity) + '</td>\n\
                    <td class="amountAlign">' + amountformat(arrStockList[i].unitprice) + '</td><td class="amountAlign">' + amountformat(arrStockList[i].price) + '</td>\n\
                    <td class="tbleActionSet"><div class="iconInfoWrapper">' +
                    '</div><a href="javascript:removestock(' + i + ')" class="tbleClose" title="Delete"></a></td></tr>';
            }
            
            var qtyinprimary=totQty;
            totPrice=amountformat(totPrice);
            totQty=amountformat(totQty);
           
            strHtml+='<tr><td></td><td></td><td class="amountAlign" colspan="2"><strong>Total Price</strong></td><td class="amountAlign"><strong>'+ totPrice +'</strong></td><td></td></tr>';
            
            $("#unit_id > option").each(function() {
               
                if(!isNaN(parseFloat($(this).attr('attrconversionval')))){
                    
                    var qtyinaltunit=parseFloat(qtyinprimary)/parseFloat($(this).attr('attrconversionval'));
                    strHtml+='<tr><td></td><td></td><td class="amountAlign" colspan="2"><strong>Total Quantity In </strong><span><strong>'+ this.text +'</strong></span></td><td class="amountAlign"><strong>'+ amountformat(qtyinaltunit)+'</strong></td><td></td></tr>';
                }
            });

            $("#tblopstock").html(strHtml);
        }
    }
    
    function removestock(index) {
        arrStockList.splice(index, 1);
        resetproductform();
        showstocklist();
    }

    function resetproductform() {
        $('#quantity').val('');
        $('#priceperunit').val('');
        $('#price').val('');
        $('#mfgdate').val('');
        $('#exp_date').val('');
        $('#warehouse_id').val('').trigger('chosen:updated');
        $('#branch_id').val('').trigger('chosen:updated');

    }
    
    function clearall(){
        arrStockList = [];
        $("#tblopstock").html('<tr><td>No Stock Added</td></tr>');
        $(".clsunit").html("<option>Select Unit</option>");
        $("#ismfgrequired").val('');
        $("#isexprequired").val('');
        intGlobalPrimaryUnit = -1;
        resetproductform();
    }

    function getinventorydata(id) {
        $.ajax({
            type: 'POST',
            url: 'getinventoryunits',
            data: '&productid=' + id,
            async:false,
            success: function (return_data) { 
              
                if (return_data != -1) {
                    var inventorydata = return_data.inventorydata;
                    var altunits = return_data.altunits;
                    
                    intGlobalPrimaryUnit = inventorydata.primary_unit;
                    $("#ismfgrequired").val(inventorydata.track_manufacturing);
                    $("#isexprequired").val(inventorydata.track_expiry);
                    $("#itemcompany").val(inventorydata.itemcompany);
                    
                    var strHtml = '<option value="' + inventorydata.primary_unit + '" selected attrconversionval="1">' + inventorydata.primaryunitname + '</option>';
                    for (var i = 0; i < altunits.length; i++) {
                        strHtml = strHtml + '<option value="' + altunits[i].unit_id + '" attrconversionval="' + altunits[i].conversion_value + '">' + altunits[i].altunitname + '</option>'
                    }
                    $(".clsunit").html(strHtml);
                    
                }else{
                    $(".clsunit").html('<option>Select Unit</option>');
                    $("#ismfgrequired").val('');
                    $("#isexprequired").val('');
                }
            }
        });
    }
    
    
    function getpreviousopeningstock(stockareaid,stockarea) {
        
        $(".tblprevstock").html('<tr><td>No Records Found<td></tr>');
            var itemid=-1;
            if(stockarea==1){
                itemid=$("#inventory_id").val();
            }else{
                itemid=$("#inventory_id").val();
            }
        
            if (stockareaid != '') {
                $.ajax({
                    type: 'POST',
                    url: 'getprevopeningstock',
                    data: {itemid:itemid,stockareaid:stockareaid,stockarea:stockarea},
                }).done(function (return_data) {
                    if (return_data != -1) {
                        $(".tblprevstock").html(return_data);
                    }
                });
            } else {
                $(".tblprevstock").html('<tr><td>No Records Found<td></tr>');
            }
    }
 
    function calculateprice() {
        var price = 0;
        if($("#priceperunit").val()!='' && $("#quantity").val()!=''){
            price = parseFloat($("#priceperunit").val()) * parseFloat($("#quantity").val());
            price=amountformat(price);
            if(!isNaN(price)){
                $("#price").val(price);
            }
        }else{
            $("#price").val('');
        }
    }

</script>

@endsection