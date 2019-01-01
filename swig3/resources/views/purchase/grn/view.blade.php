@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('purchase/grn')}}">Back</a>
    
    <header class="pageTitleV3">
        <h1>Add GRN</h1>
    </header>

    <div class="inputAreaWrapper">
        <div class="custRow reqCodeDateHolder">

            <div class="custCol-6 ">
                <label>PO Code : <span>{{$orderdata->order_code}}</span></label>
                <input type="hidden" id="po_id" name="po_id" value="{{$orderdata->id}}">
                <input type="hidden" id="po_code" name="po_code" value="{{$orderdata->order_code}}">
                <input type="hidden" id="grn_code" name="grn_code" value="{{$batchcode}}">
                <input type="hidden" id="stock_entered" name="stock_entered" value="{{$orderdata->stock_entered}}">
            </div>
            <div class="custCol-6 alignRight">
                <label>Date : <span><?php echo date('d-m-Y',  strtotime($orderdata->created_at)); ?></span></label>
            </div>
        </div>
        
        <div class="custRow">
            <div class="custCol-6">
                <label><strong>Supplier : {{$orderdata->suppliercode}} {{$orderdata->supplierfname}}</strong></label>
            </div>
            
        </div>

        <div class="tbleListWrapper ">
            <table cellpadding="0" cellspacing="0" style="width: 100%;padding-bottom: 15px;">
                <thead class="headingHolder">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="amountAlign">Quantity</th>
                        <th>Unit</th>
                        <th class="amountAlign">Price/Unit</th>
                        <th class="amountAlign">Total</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($orderitems as $item)
                    <tr>
                        <td>{{$item->product_code}}</td>
                        <td>{{$item->productname}}</td>
                        <td class="amountAlign"><?php echo Customhelper::numberformatter($item->quantity); ?></td>
                        <td>{{$item->unit}}</td>
                        <td class="amountAlign"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                        <td class="amountAlign"><?php echo Customhelper::numberformatter($item->total_price); ?></td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <?php if(count($orderitems)>0){?>
        
            <div class="custRow reqCodeDateHolder">

                <div class="custCol-6 ">
                    <label>PO Stock Summary</label>
                </div>

            </div>
            <div class="approverDetailsWrapper">
                <div class="tbleListWrapper">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder ">
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th >Unit</th>
                                <th class="amountAlign">Quantity</th>
                                <th class="amountAlign">Entered Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderitems as $item)
                            <tr >
                                <td>{{$item->product_code}}</td>
                                <td>{{$item->productname}}</td>
                                <td>{{$item->unit}}</td>
                                <td class="amountAlign"><?php echo Customhelper::numberformatter($item->quantity); ?></td>
                                <td class="amountAlign"><?php echo Customhelper::numberformatter($item->entered_stock); ?></td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        <?php } ?>
        <div class="custRow reqCodeDateHolder" style="padding-top: 10px;">

            <div class="custCol-6 ">
                <label>GRN No : <span>{{$batchcode}}</span></label>
            </div>
            <div class="custCol-6 alignRight">
                <label>Date : <span><?php echo date('d-m-Y'); ?></span></label>
            </div>
        </div>

        <form id="frmwarehouse" method="POST">
            <div class="custRow" style="padding-bottom:10px;">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Warehouse</label>
                        <select class="" name="warehouse" id="warehouse" class="warehouse">
                            <option value="">Select</option>
                            @foreach ($warehouses as $warehouse)
                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="statusMessage" style="display: none;">
		<span class="errorMsg" ></span>
        </div>
        
        <div class="listHolderType1">

            <div class="listerType1 reportLister"> 
                
                <div class="clsstocktable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td></td>
                                <td>Item Code</td>
                                <td>Item Name</td>
                                <td> Unit</td>
                                <td class="amountAlign">Quantity</td>
                                <td>Mfd. Date.</td>
                                <td>Expiry Date.</td>
                            </tr>
                        </thead>

                        <tbody class="employee_list" id='employee_list'>
                            @foreach ($orderitems as $item)
                            <tr >
                                <td><input type="checkbox" checked attrid="{{$item->requisition_item_id}}" attrunitid="{{$item->alternate_unit_id}}" attrisprimary="{{$item->purchase_in_primary_unit}}" attrmfd="{{$item->track_manufacturing}}" attrexp="{{$item->track_expiry}}" attrunitprice="{{$item->unit_price}}" attritemcompany="{{$item->itemcompany}}" class="chkinventory"></td>
                                <td>{{$item->product_code}}</td>
                                <td>{{$item->productname}}</td>
                                <td>{{$item->unit}}</td>
                                <td class="amountAlign"><input type="text" id="txtqty_{{$item->requisition_item_id}}" class="clsquantity numberwithdot" maxlength="15" autocomplete="off" onpaste="return false;" style="width: 100px;"></td>
                                <td ><input type="text" id="txtmfd_{{$item->requisition_item_id}}" class="clsmanfdate" readonly style="width: 100px;"></td>
                                <td ><input type="text" id="txtexp_{{$item->requisition_item_id}}" class="clsexpdate" readonly style="width: 100px;"></td>
                            </tr>
                           @endforeach
                 
                    
                        </tbody>

                    </table>
                </div>
                <div class="commonLoaderV1"></div>
            </div>

        </div>
        
         <div class="custRow">
            <div class="custCol-6">
                <input type="button" id="btnUpdateStock" class="btnIcon lightGreenV3" value="Create">
            </div>
        </div>

        <div style="padding-top: 30px;">
            
        
         <?php if(count($prevgrns)>0){?>   
            
            <div class="custRow reqCodeDateHolder" style="padding-bottom: 10px;">

                <div class="custCol-6 ">
                    <label>Previous GRN'S</label>
                </div>

            </div>
            @foreach($prevgrns as $grn)
           
            <div class="custRow">
                <div class="custCol-6">
                    <label>GRN No : <span>{{$grn->batch_code}}</span></label>
                </div>
                <div class="custCol-6">
                    <label>Warehouse : <span>{{$grn->name}}</span></label>
                </div>
            </div>

            <div class="approverDetailsWrapper" style="padding-bottom: 20px;">
                <div class="tbleListWrapper">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder ">
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th >Unit</th>
                                <th class="amountAlign">Quantity</th>
                                <th>Mfd. Date</th>
                                <th>Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grn->items as $item)
                            <tr >
                                <td>{{$item->product_code}}</td>
                                <td>{{$item->productname}}</td>
                                <td>{{$item->unit}}</td>
                                <td class="amountAlign"><?php echo Customhelper::numberformatter($item->purchase_quantity); ?></td>
                                <td><?php if($item->mfg_date){ echo date('d-m-Y',  strtotime($item->mfg_date));}?></td>
                                <td><?php if($item->exp_date){ echo date('d-m-Y',  strtotime($item->exp_date));}?></td>

                            </tr>
                            @endforeach
                            

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="customClear"></div>
             @endforeach
            <?php }?>

        </div>
</div>
</div>

<script>
    $(function () {
        
        $("#frmwarehouse").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                warehouse: {required: true},
                
            },
            messages: {
                warehouse: "Select warehouse",
                
            }
        });
        
        $("#btnUpdateStock").click(function(){
            if (!$("#frmwarehouse").valid()) {
                return;
            }
           
            if($('input:checkbox.chkinventory:checked').length==0){
                alert("Please select atleast one item to enter stock");
                return
            }
            
            var stockData = {
                warehouse: $('#warehouse').val(),
                po_id: $('#po_id').val(),
                po_code: $('#po_code').val(),
                grn_code: $('#grn_code').val(),
                stock_entered: $('#stock_entered').val(),
            }
            
            $(".chkinventory").each(function() {
                var itemid=$(this).attr('attrid');
                
                if($(this).prop('checked')){
                    if($('#txtqty_'+itemid).val()==''){
                        $('#txtqty_'+itemid).addClass("notentered");
                    }else{
                        $('#txtqty_'+itemid).removeClass("notentered");
                    }

                    if($('#txtmfd_'+itemid).val()=='' && $(this).attr('attrmfd')==1){
                        $('#txtmfd_'+itemid).addClass("notentered");
                    }else{
                        $('#txtmfd_'+itemid).removeClass("notentered");
                    }

                    if($('#txtexp_'+itemid).val()=='' && $(this).attr('attrexp')==1){
                        $('#txtexp_'+itemid).addClass("notentered");
                    }else{
                        $('#txtexp_'+itemid).removeClass("notentered");
                    }
                    
                }else{
                    $('#txtqty_'+itemid).removeClass("notentered");
                    $('#txtmfd_'+itemid).removeClass("notentered");
                    $('#txtexp_'+itemid).removeClass("notentered");
                }
                
            });
            
            if($('.notentered').length!=0){
                $('.errorMsg').text("Please fill the required fields"); 
                $('.statusMessage').show();
                return false;
            }else{
                $('.statusMessage').hide();
            }
            
            var blnConfirm = confirm("Are you sure to submit");
            if (!blnConfirm) {
                return;
            }
            
            var stockitems=[];
            $(".chkinventory:checked").each(function() {
                var itemid=$(this).attr('attrid');
                var unitid=$(this).attr('attrunitid');
                var isprimary=$(this).attr('attrisprimary');
                var unitprice=$(this).attr('attrunitprice');
                var itemcompany=$(this).attr('attritemcompany');
                
                var arraData = {
                    itemid: itemid,
                    unitid: unitid,
                    isprimary: isprimary,
                    unitprice: unitprice,
                    itemcompany: itemcompany,
                    quantity: parseFloat($('#txtqty_'+itemid).val()),
                    qtyinprimary: '',
                    mfddate: $('#txtmfd_'+itemid).val(),
                    expdate: $('#txtexp_'+itemid).val(),
                }

                stockitems.push(arraData);
            });
            
            var stockData = JSON.stringify(stockData);
            var stockitems = JSON.stringify(stockitems);
            
            $('.commonLoaderV1').show();
            $('#btnUpdateStock').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../updatestock',
                data:{stockData:stockData,stockitems:stockitems}
            }).done(function(data) { 
                $('.commonLoaderV1').hide();
                $('#btnUpdateStock').removeAttr('disabled');
                window.location.href = data.redirecturl;
            });
            
        });
        
        $(".clsmanfdate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            maxDate:0
        });

        $(".clsexpdate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate:'0'
        });
    });
</script>
@endsection

