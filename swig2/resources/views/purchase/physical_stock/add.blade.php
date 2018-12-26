@extends('layouts.main')
@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('purchase')}}">Back</a>
    <header class="pageTitleV3" >
        <h1 style="padding-bottom: 30px;">Add Physical Stock</h1>
    </header>
    <div class="inputAreaWrapper">
        <form id="frmphysicalstock" method="post" enctype="multipart/form-data" style="padding-bottom: 20px;">
            <div class="custRow reqCodeDateHolder" style="padding-bottom: 10px;">
                
                <div class="custCol-12">
                    <label>Date : <span><?php echo date('d-m-Y'); ?></span></label>
                </div>
            </div>
            
            <div class="custRow ">
                <div class="custCol-6 clsparentdiv">
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

                    <div class="inputHolder clswarehouse">
                        <label>Stock Area</label>
                        <select id="warehouse_id" name="warehouse_id" class="chosen-select">
                            <option value=''>Select Warehouse</option>
                            @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" >{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                    
                     <div class="inputHolder clsbranch" style="display: none;">
                        <label>Stock Area</label>
                        <select id="branch_id" name="branch_id" class="chosen-select">
                            <option value=''>Select Branch</option>
                            @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" >{{$branch->branch_code}} : {{$branch->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                    <div class="inputHolder clsparentdiv">
                            <label>Inventory Item</label>
                            <select id="inventory_id" name="inventory_id" class="inventory_id chosen-select">
                                <option value=''>Select Item</option>
                                @foreach ($inventorydata as $item)
                                <option value="{{ $item->id }}" attrprimaryunit="{{$item->primary_unit}}">{{ $item->product_code}} : {{$item->name}}</option>
                                @endforeach
                            </select>
                            <span class="commonError"></span>

                            <input type="hidden" id="primary_unit" >
                            <input type="hidden" id="ismfgrequired" >
                            <input type="hidden" id="isexprequired" >
                            <input type="hidden" id="itemcompany" >
                        </div>
                </div>

            </div>
        </form>

        <div class="statusMessage">
            <span class="errorMsg" ></span>
        </div>
        
        <div class="listHolderType1" >

            <div class="listerType1 reportLister"> 
                
                <div class="clsstocktable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td></td>
                                <td>Batch code</td>
                                <td class="amountAlign">Added stock</td>
                                <td class="amountAlign">Current stock</td>
                                <td>Unit</td>
                                <td>New stock</td>
                                
                            </tr>
                        </thead>

                        <tbody class="tblbatchlist" id='tblbatchlist'>
                            @include('purchase/physical_stock/result')
                        </tbody>

                    </table>
                </div>
                <div class="commonLoaderV1"></div>
            </div>

        </div>
        
        <div class="custRow" style="padding-top: 7px;padding-bottom: 7px;" >
            <div class="custCol-2">
                <div class="commonCheckHolder">
                    <label >
                        <input id="chkshownewbatch" type="checkbox">
                        <span></span><em>Create New Batch</em>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="clsnewphysicalstock" style="display: none;">
            <div class="custRow reqCodeDateHolder">
                <div class="custCol-6 ">
                    <label>Batch Code : <span>{{$batchcode}}</span></label>
                    <input type="hidden" name="batchcode" id="batchcode" value="{{$batchcode}}">
                </div>
                <div class="custCol-6 alignRight">
                    <label>Date : <span><?php echo date('d-m-Y'); ?></span></label>
                </div>
            </div>

            <form id="frmaddstock">
                <div class="invertoryFieldHolder clsparentdiv">
                    <div class="custRow">

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Stock Area</label>
                                <select id="new_area_id" name="new_area_id" disabled>
                                    
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                       
                        <div class="custCol-3 unit">
                            <div class="inputHolder">
                                <label>Unit</label>
                                <select id="new_unit_id" name="new_unit_id" class="clsunit">
                                    <option value=''>Select Unit</option>
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="customClear "></div>

                        <div class="custCol-2 price">
                            <div class="inputHolder">
                                <label>Quantity</label>
                                <input type="text" placeholder="Qty" onpaste="return false;" name="new_quantity" id="new_quantity" class="numberwithdot" maxlength="10">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-2 price">
                            <div class="inputHolder">
                                <label>Price / Unit</label>
                                <input type="text" id="new_priceperunit" onpaste="return false;" name="new_priceperunit" placeholder="Price / Unit" class="numberwithdot" maxlength="15">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-2 price">
                            <div class="inputHolder">
                                <label>Price</label>
                                <input type="text" placeholder="Price" name="new_price" id="new_price" disabled class="numberwithdot">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-2 price">
                            <div class="inputHolder">
                                <label>Mfg. Date</label>
                                <input type="text" placeholder="Mfg. Date" name="new_mfgdate" id="new_mfgdate" readonly class="numberwithdot">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-2 price">
                            <div class="inputHolder">
                                <label>Expiry</label>
                                <input type="text" placeholder="Expiry" name="new_exp_date" id="new_exp_date" readonly class="numberwithdot">
                                <span class="commonError"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
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
    var stockitems = [];
    var intGlobalPrimaryUnit = -1;
    $('.statusMessage').hide();
    $('#btnSaveStock').removeAttr('disabled');
    $(function () {
        var v = jQuery("#frmphysicalstock").validate({
            rules: {
                
                warehouse_id: {
                    required: function () {
                        return $("#chkwarehouse").prop('checked');
                    }
                },
                branch_id: {
                    required: function () {
                        return $("#chkbranch").prop('checked');
                    }
                },
                inventory_id: {required: true},
            },
            messages: {
                warehouse_id: "Select Warehouse",
                branch_id: "Select Branch",
                inventory_id: "Select Inventory",
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

        var frmaddstockvalidator=$("#frmaddstock").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                new_quantity: {required: true,number:true},
                new_unit_id: {required: true},
                new_priceperunit: {required: true,number:true},
                new_mfgdate: {
                    required: function(){
                                return ($("#ismfgrequired").val()==1)?true:false;
                            }
                        },
                new_exp_date: {
                    required: function(){
                                return ($("#isexprequired").val()==1)?true:false;
                            }
                        },
            },
            messages: {
                new_quantity: "Enter Quantity",
                new_unit_id: "Select Unit",
                new_mfgdate: "Select date",
                new_exp_date: "Select date",
                new_priceperunit: {
                    required: "Enter Price / Unit",
                    number: "Enter a valid amount"
                },
            }
        });
        
        $("#chkwarehouse").click(function () {
            $(".clsbranch").hide();
            $(".clswarehouse").show();
            $('#branch_id').val('').trigger('chosen:updated');
            getbatches();
        });

        $("#chkbranch").click(function () {
            $(".clsbranch").show();
            $(".clswarehouse").hide();
            $('#warehouse_id').val('').trigger('chosen:updated');
            getbatches();
        });
        
      
        $("#btnSaveStock").click(function () {
            if (!$("#frmphysicalstock").valid()) {
                return;
            }

            if($('input:checkbox.chkbatchstock:checked').length==0 && $("#chkshownewbatch").prop("checked")==false){
                alert("Please select atleast one batch to update stock or Create a new batch");
                return;
            }
            
            var stockareaid='';
            if($('input[name=stockearea]:checked').val()==1){
                stockareaid=$("#warehouse_id").val();
            }

            if($('input[name=stockearea]:checked').val()==2){
                stockareaid=$("#branch_id").val();
            }
            
            $(".chkbatchstock").each(function() {
                var attrid=$(this).attr('attrid');
                if($(this).prop('checked')){
                    if($('#txtqty_'+attrid).val()==''){
                        $('#txtqty_'+attrid).addClass("notentered");
                    }else{
                        $('#txtqty_'+attrid).removeClass("notentered");
                    }
                }else{
                    $('#txtqty_'+attrid).removeClass("notentered");
                }
            });
            
            if($('.notentered').length!=0){
                $('.errorMsg').text("Please fill the required fields"); 
                $('.statusMessage').show();
                return false;
            }else{
                $('.statusMessage').hide();
            }
            
            var stockNewData=[];
            if($("#chkshownewbatch").prop("checked")){
                if (!$("#frmaddstock").valid()) {
                    return;
                }
                var new_conversionvalue = $("#new_unit_id").find(':selected').attr('attrconversionval');
                var new_qtyinprimary=parseFloat($('#new_quantity').val())*parseFloat(new_conversionvalue);
                
                var isprimary=0;
                if($("#new_unit_id").val()==intGlobalPrimaryUnit){
                    isprimary=1;
                }
                
                var stockarea=1;
                if($("#chkwarehouse").prop('checked')){
                    stockarea=1;
                }else{
                    stockarea=2;
                }
            
                stockNewData = {
                    batchcode: $('#batchcode').val(),
                    stockarea: stockarea,
                    stockareaid: stockareaid,
                    item_id: $('#inventory_id').val(),
                    qtyinprimary: parseFloat(new_qtyinprimary),
                    unitid: $('#new_unit_id').val(),
                    quantity: parseFloat($('#new_quantity').val()),
                    isprimary:isprimary,
                    itemcompany: $('#itemcompany').val(),
                    unitprice: parseFloat($('#new_priceperunit').val()),
                    price: parseFloat($('#new_price').val()),
                    mfgdate: $('#new_mfgdate').val(),
                    expdate: $('#new_exp_date').val(),
                }
            }
           
            $(".chkbatchstock:checked").each(function() {
                var attrid=$(this).attr('attrid');
                var unitid=$("#cmbunit_"+attrid).val();
                
                var conversionvalue = $("#cmbunit_"+attrid).find(':selected').attr('attrconversionval');
                if(isNaN(conversionvalue)){
                    $('.errorMsg').text("Invalid unit conversion value, Please verify with inventory section."); 
                    $('.statusMessage').show();
                    stockitems=[];
                    return;
                }else{
                    $('.statusMessage').hide();
                }

                var qtyinprimary=parseFloat($('#txtqty_'+attrid).val())*parseFloat(conversionvalue);
            
                var arraData = {
                    stockid: attrid,
                    unitid: unitid,
                    qtyinprimary: qtyinprimary,
                    quantity: parseFloat($('#txtqty_'+attrid).val()),
                }

                stockitems.push(arraData);
            });
            
            var stockNewData = JSON.stringify(stockNewData);
            var stockList = JSON.stringify(stockitems);

            $('.commonLoaderV1').show();
            $('#btnSaveStock').attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                url: 'updatephysicalstock',
                data: {stockNewData: stockNewData, arrStockList: stockList},
            }).done(function (data) {
                $('.commonLoaderV1').hide();
                $('#btnSaveStock').removeAttr('disabled');
                window.location.href = '{{url("purchase")}}';
            });

        });

        $('body').on('change', '.inventory_id', function () {
            if($("#inventory_id option:selected").attr("attrprimaryunit")){
                intGlobalPrimaryUnit=$("#inventory_id option:selected").attr("attrprimaryunit");
            }else{
                intGlobalPrimaryUnit=-1;
            }
            
            $("#chkshownewbatch").prop("checked",false);
            $(".clsnewphysicalstock").hide();
            resetproductform();
            
            if($("#inventory_id").val()!=''){
                getinventorydata($("#inventory_id").val());
            }
            
            getbatches();
        });
        
        $("#chkshownewbatch").click(function () {
            if (!$("#frmphysicalstock").valid()) {
                $(this).prop("checked",false);
            }
            
            if($(this).prop("checked")){
                $("#new_area_id").html('<option value="'+$("#inventory_id").val()+'">'+$("#inventory_id option:selected").text()+'</option>');
                $(".clsnewphysicalstock").show();
            }else{
                frmaddstockvalidator.resetForm();
                $(".clsnewphysicalstock").hide();
            }
            
        });

        $('body').on('change', '#warehouse_id', function () {
            getbatches();
        });

        $('body').on('change', '#branch_id', function () {
            getbatches();
        });

        $('body').on('keyup', '#new_quantity', function () {
            calculateprice();
        });

        $('body').on('keyup', '#new_priceperunit', function () {
            calculateprice();
        });

        $("#new_exp_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate:0
        });
        
        $("#new_mfgdate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            maxDate:0
        });

    });


    function getbatches() {
        var stockareaid='';
        if($('input[name=stockearea]:checked').val()==1){
            stockareaid=$("#warehouse_id").val();
        }
        
        if($('input[name=stockearea]:checked').val()==2){
            stockareaid=$("#branch_id").val();
        }
        
        if($("#inventory_id").val()!='' && stockareaid!=''){
            $('.commonLoaderV1').show();
            
            $.ajax({
                type: 'POST',
                url: 'getbatches',
                data: {item_id:$("#inventory_id").val(),stockarea_id:stockareaid},
            }).done(function (return_data) {
                $('.commonLoaderV1').hide();
                if (return_data != -1) {
                    $(".tblbatchlist").html(return_data);
                } else {
                    $(".tblbatchlist").html('<tr><td colspan="5">No Records Found<td></tr>');
                }
            });
        
        } else{
            $(".tblbatchlist").html('<tr><td colspan="5">No Records Found<td></tr>');
        }
    }
   

    function calculateprice() {
        var price = 0;
        if ($("#new_priceperunit").val() != '' && $("#new_quantity").val() != '') {
            price = parseFloat($("#new_priceperunit").val()) * parseFloat($("#new_quantity").val());
            price = amountformat(price);
            if (!isNaN(price)) {
                $("#new_price").val(price);
            }
        } else {
            $("#new_price").val('');
        }
    }
    
    function getinventorydata(id) {
        $.ajax({
            type: 'POST',
            url: 'getinventoryunits',
            data: '&productid=' + id
        }).done(function (return_data) {
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

            } else {
                intGlobalPrimaryUnit=-1
                $(".clsunit").html('<option>Select Unit</option>');
                $("#ismfgrequired").val('');
                $("#isexprequired").val('');
                $("#itemcompany").val('');
            }
        });
        
        return 1;
    }
    
    function resetproductform() {
        $('#new_quantity').val('');
        $('#new_priceperunit').val('');
        $('#new_price').val('');
        $('#new_mfgdate').val('');
        $('#new_exp_date').val('');
        
    }
    
</script>

@endsection