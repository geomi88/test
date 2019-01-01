@extends('layouts.main')
@section('content')
<script>
    var arrItems=<?php echo $rfqitems;?>;
</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('rfq')}}">Back</a>
    <header class="pageTitleV3">
        <h1><span>RFQ</span></h1>
    </header>
    <div class="inputAreaWrapper">
        <form id="frmaddrequisition" method="post" enctype="multipart/form-data">
            <div class="custRow reqCodeDateHolder">
                <div class="custCol-6">
                    <label>Date : <span><?php echo date('d-m-Y',  strtotime($rfqdata->created_at)); ?></span></label>
                </div>
                <div class="custCol-6 alignRight">
                    <label>RFQ Code : <span>{{$rfqdata->rfq_code}}</span></label>
                </div>
            </div>
            <div class="custRow ">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Title<span> *</span></label>
                        <input type="text" id="title" name="title" value="{{$rfqdata->title}}" autocomplete="off" placeholder="Enter Title" disabled maxlength="250">
                        <input type="hidden" value="{{$rfqdata->rfq_code}}" id="rfq_code" name="rfq_code">
                        <input type="hidden" value="{{$rfqdata->id}}" id="requisition_id" name="requisition_id">
                        <span class="commonError"></span>
                    </div>
                    <div class="inputHolder">
                        <label>Select Supplier<span> *</span></label>
                        <select id="supplier_id" name="supplier_id" class="supplier_id" disabled>
                            <option value=''>Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" <?php if($supplier->id==$rfqdata->supplier_id){ echo "selected";}?> >{{ $supplier->code}}:{{$supplier->first_name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>

                    
                </div>
                <?php if(count($budgetdata)>0){?>
                        <input type="hidden"  name="pending" id="pending" value="{{$budgetdata->pending}}" >
                <?php }?>
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
                                            <?php $strRegistred = "Registered";
                                            if ($supplierdata->registration_type == 0) {
                                                $strRegistred = "Not Registered";
                                            }?>
                                            <tr><td>{{$supplierdata->code}}</td><td>{{$supplierdata->first_name}} {{$supplierdata->alias_name}}</td><td>{{$strRegistred}}</td></tr>
                                            <tr><td>{{$supplierdata->nationality}}</td><td>{{$supplierdata->mobile_number}}</td><td>{{$supplierdata->email}}</td></tr>
                                            <tr><td>Contact Info :</td><td>{{$supplierdata->contact_number}}</td><td>{{$supplierdata->contact_email}}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="btnTab">Budget</div>
                            <div id="budget" class="tabContent">
                                <div class="tbleListWrapper ">
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody class="tblbudget">
                                            <?php if(count($budgetdata)>0){?>
                                            
                                            <tr><td>Total : {{$budgetdata->format_initial}}</td></tr>
                                            <tr><td>Used : {{$budgetdata->format_used}}</td></tr>
                                            <tr><td>Pending : {{$budgetdata->format_balance}}</td></tr>
                                            <?php }else{?>
                                            <tr><td>No Records Found</td></tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="btnTab">Bank</div>
                            <div id="bank" class="tabContent">
                                <div class="tbleListWrapper ">
                                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody class="tblbank">
                                            
                                            <tr><td>Swift Code : {{$supplierdata->bank_swift_code}}</td><td> Name : {{$supplierdata->bank_branch_name}}</td></tr>
                                            <tr><td>Beneficiary : {{$supplierdata->bank_beneficiary_name}}</td><td>Ac No : {{$supplierdata->bank_account_number}}</td></tr>
                                            <tr><td>Country : {{$supplierdata->bankcountry}}</td><td>Credit Limit : {{$supplierdata->creditlimitformated}}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <input type="hidden" name="createddate" id="createddate" value="{{$rfqdata->created_at}}">
        
        <div class="statusMessage">
            <span class="errorMsg" ></span>
	</div>
        
        <form id="frmProduct">
            <div class="invertoryFieldHolder">
                <div class="custRow">
                    <div class="custCol-4" style="padding-bottom: 5px;">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input type="checkbox" name="chkpreferred" id="chkpreferred" class="preferredonly" <?php if($rfqdata->isallproducts==2){ echo "checked";}?> >
                                <span></span>
                                <em>Preferred Products</em>
                            </label>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input type="checkbox" name="chkallproducts" id="chkallproducts" class="preferredonly" <?php if($rfqdata->isallproducts==1){ echo "checked";}?> >
                                <span></span>
                                <em>All Products</em>
                            </label>
                        </div>
                    </div>
                    <div class="customClear "></div>
                    <div class="custCol-4 inventory">
                        <div class="inputHolder">
                            <label>Inventory</label>
                            <div class="bgSelect">                                                          
                                <input type="text" name="product" id="product" autocomplete="off" placeholder="Enter Code or Name">

                                <ul class="add_product_list classscroll">
                                </ul>

                                <input type="hidden"  name="product_id" id="product_id" >
                                <input type="hidden"  name="product_code" id="product_code" >
                                <input type="hidden"  name="product_name" id="product_name" >
                                <input type="hidden"  name="usedQuantity" id="usedQuantity" >
                                
                                <input type="hidden" class="productbalanceAmount"  name="productbalanceAmount" id="productbalanceAmount" >
                           
                            </div>
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
                    <div class="custCol-1 qty">
                        <div class="inputHolder">
                            <label>Quantity</label>
                            <input type="text" placeholder="Qty" onpaste="return false;" name="quantity" id="quantity" class="numberwithdot">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-2 price">
                        <div class="inputHolder">
                            <label>Price / Unit</label>
                            <input type="text" id="priceperunit" onpaste="return false;" name="priceperunit" placeholder="Price / Unit" class="numberwithdot">
                            <input type="hidden" id="priceperunithidden" name="priceperunithidden">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-2 price">
                        <div class="inputHolder">
                            <label>Price</label>
                            <input type="text" placeholder="Price" name="price" id="price" disabled class="numberwithdot">
                            <input type="hidden" name="total_price" id="total_price">
                            <input type="hidden" name="tempvattotal" id="tempvattotal" value="{{$rfqdata->total_vat}}">
                            
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-2 addBtnHolder">
                        <div class="iconInfoWrapper">

                            <a href="javascript:void(0);" class="tbleInfo btnTooltip" title="information"></a>
                            <div class="tooltipInfo">
                                <a href="javascript:void(0);" class="infoClose"></a>
                                <strong>Budget Details</strong>
                                <span>Initial Budget Amount:<strong class="initialBudget"></strong> </span>
                                <span>Balance Budget Amount:<strong class="balanceAmount"></strong> </span>
                                <span>Stock in Branch:<strong class="branchStock"></strong> </span>
                               <span>Stock in Warehouse:<strong class="warehouseStock"></strong> </span>
                               <span>Budget Quantity:<strong class="budgetQuantity"></strong> </span>
                                <span>Remaining Quantity:<strong class="remQuantity"></strong> </span>
                            </div>
                        </div>
                        <a id="add_product" class="btnIcon lightGreenV3">Add</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="tbleListWrapper ">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead class="headingHolder">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="amountAlign">Quantity</th>
                        <th>Unit</th>
                        <th class="amountAlign">Price/Unit</th>
                        <th class="amountAlign">Price</th>
                        <th class="tbleActionSet"></th>
                    </tr>
                </thead>
                <tbody id="tblproducts" class="tblproducts">
                    <tr><td>No Products Added</td></tr>
                </tbody>
            </table>
        </div>
        

        <div class="custRow">
            <div class="custCol-6">
                <label>Created By : <span>{{$rfqdata->empname}}</span></label>
            </div>
        </div>
        
        <form id="frmdocs" name="frmdocs" method="post" enctype="multipart/form-data">
            <div class="rfqGroup">
                <div class="custRow" >
                    <div class="custCol-4">
                        <div class="inputHolder checkHolder">
                            <label>Mode of payment :</label>
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input  name="payment_mode" <?php if($rfqdata->payment_mode=="Cash"){ echo "checked";}?> value="Cash" type="radio" class="clsmodeofpay"  >
                                    <span></span>
                                    <em>Cash</em>
                                </label>
                            </div>
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input  name="payment_mode" value="Credit" <?php if($rfqdata->payment_mode=="Credit"){ echo "checked";}?> type="radio" class="clsmodeofpay" >
                                    <span></span>
                                    <em>Credit</em>
                                </label>
                            </div>

                        </div>
                    </div>

                    <div class="custCol-4 clscreditdays" style="<?php if($rfqdata->payment_mode!="Credit"){ echo "display: none;";}?>">
                        <div class="inputHolder">
                            <label>Credit days :</label>
                            <input placeholder="Enter credit days" class="numberwithdot" value="{{$rfqdata->creditdays}}" name="creditdays" id="creditdays" autocomplete="off" type="text" maxlength="10">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Terms of payment :</label>
                            <input placeholder="Enter terms of payment" name="payment_terms" id="payment_terms" value="{{$rfqdata->payment_terms}}" autocomplete="off" type="text" maxlength="250">
                            <span class="commonError"></span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="rfqGroup">
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Delivery place :</label>
                            <input placeholder="Enter delivery place" name="delivery_place" value="{{$rfqdata->delivery_place}}" id="delivery_place" autocomplete="off" type="text" maxlength="250">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Delivery required date in warehouse :</label>
                            <input placeholder="Enter delivery date" name="delivery_date" readonly value="<?php if($rfqdata->delivery_date){ echo date('d-m-Y',  strtotime($rfqdata->delivery_date));}?>" id="delivery_date" autocomplete="off" type="text">
                            <span class="commonError"></span>

                        </div>
                    </div>
                                        
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Purchase planning date <span>* </span>:</label>
                            <input placeholder="Enter planning date" name="planning_date" readonly value="<?php if($rfqdata->planning_date){ echo date('d-m-Y',  strtotime($rfqdata->planning_date));}?>" id="planning_date" autocomplete="off" type="text">
                            <span class="commonError"></span>

                        </div>
                    </div>
                </div>
            </div>

            <div class="rfqGroup">
                <div class="custRow stockDiv" >
                    <div class="custCol-3" >
                        <div class="inputHolder">
                            <label>Available stock in hand <span>* </span>:</label>
                            <input placeholder="Enter stock in hand" class="numberwithdot" name="stockinhand" id="stockinhand" value="{{$rfqdata->stock_in_hand}}" autocomplete="off" type="text" maxlength="20">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-3">
                        <div class="inputHolder">
                            <label>Available stock till the period :</label>
                            <input placeholder="From date" name="stock_period_from" readonly id="stock_period_from" value="<?php if($rfqdata->stock_period_from){ echo date('d-m-Y',  strtotime($rfqdata->stock_period_from));}?>" autocomplete="off" type="text">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-3">
                        <div class="inputHolder">
                            <label>To :</label>
                            <input placeholder="To date" name="stock_period_to" readonly id="stock_period_to" value="<?php if($rfqdata->stock_period_to){ echo date('d-m-Y',  strtotime($rfqdata->stock_period_to));}?>" autocomplete="off" type="text">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-3" >
                        <div class="inputHolder">
                            <label>In Days:</label>
                            <input placeholder="In Days" class="numberwithdot" name="indaysstock" id="indaysstock" value="{{$rfqdata->indays}}" autocomplete="off" type="text" maxlength="20" disabled="">
                            <span class="commonError"></span>

                        </div>
                    </div>
                </div>
            </div>

            <div class="rfqGroup">
                <div class="custRow stockDiv" >
                    <div class="custCol-3" >
                        <div class="inputHolder">
                            <label>RFQ stock total <span>* </span>:</label>
                            <input placeholder="Enter stock total" class="numberwithdot" value="{{$rfqdata->stock_total}}" readonly name="stocktotal" id="stocktotal" autocomplete="off" type="text" maxlength="20">
                            <span class="commonError"></span>

                        </div>
                    </div>

                    <div class="custCol-3">
                        <div class="inputHolder">
                            <label>RFQ Order forecast period <span>* </span>:</label>
                            <input placeholder="From date" name="forecast_from" readonly value="<?php if($rfqdata->forecast_from){ echo date('d-m-Y',  strtotime($rfqdata->forecast_from));}?>" id="forecast_from" autocomplete="off" type="text">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-3">
                        <div class="inputHolder">
                            <label>To <span>* </span>:</label>
                            <input placeholder="To date" name="forecast_to" readonly id="forecast_to" value="<?php if($rfqdata->forecast_to){ echo date('d-m-Y',  strtotime($rfqdata->forecast_to));}?>" autocomplete="off" type="text">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-3" >
                        <div class="inputHolder">
                            <label>In Days:</label>
                            <input placeholder="In Days" class="numberwithdot" name="indaysrfqstock" id="indaysrfqstock" value="{{$rfqdata->indaysrfq}}" autocomplete="off" type="text" maxlength="20" disabled="">
                            <span class="commonError"></span>

                        </div>
                    </div>
                </div>
            </div>

            <div class="rfqGroup">
                <div class="custRow" >
                    <div class="custCol-8" >
                        <div class="inputHolder">
                            <label>Product specification :</label>
                            <textarea id="productspec" name="productspec" placeholder="Enter product specification">{{$rfqdata->product_spec}}</textarea>
                            <span class="commonError"></span>

                        </div>
                    </div>

                </div>
            </div>
            <div class="rfqGroup">
                <div class="custRow" >
                    <div class="custCol-6">
                        <div class="inputHolder checkHolder">
                            <label>Is this is an approved supplier ? </label>
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input  name="approvedsupplier" <?php if($rfqdata->isapprovedsupplier=="Yes"){ echo "checked";}?> value="Yes" type="radio"   >
                                    <span></span>
                                    <em>Yes</em>
                                </label>
                            </div>
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input  name="approvedsupplier" <?php if($rfqdata->isapprovedsupplier=="No"){ echo "checked";}?> value="No" type="radio"  >
                                    <span></span>
                                    <em>No</em>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="rfqGroup">
                <div class="custRow" >
                    <div class="custCol-4" >
                        <div class="inputHolder">
                            <label>Last purchase date :</label>
                            <input placeholder="Enter last purchase date" readonly value="<?php if($rfqdata->last_purchase_date){ echo date('d-m-Y',  strtotime($rfqdata->last_purchase_date));}?>" name="last_purchase_date" id="last_purchase_date" autocomplete="off" type="text" maxlength="10">
                            <span class="commonError"></span>

                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Last purchase quantity :</label>
                            <input placeholder="Enter last purchase quantity" value="{{$rfqdata->last_qty}}" name="last_qty" id="last_qty" class="numberwithdot" autocomplete="off" type="text" maxlength="20">
                            <span class="commonError"></span>

                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Last purchase value :</label>
                            <input placeholder="Enter last purchase value" value="{{$rfqdata->last_value}}" name="last_value" id="last_value" class="numberwithdot" autocomplete="off" type="text" maxlength="20">
                            <span class="commonError"></span>

                        </div>
                    </div>
                </div>
            </div>

            <div class="custRow ">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea id="description" name="description" placeholder="Enter Description">{{$rfqdata->description}}</textarea>
                        <span class="commonError"></span>
                    </div>
                </div>
<!--                <div class="custCol-6 ">
                    <div class="inputHolder">
                        <label>Upload Document</label>
                        <input type="file" name="req_doc" class="reqdocument" id="req_doc" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">
                        <span class="commonError"></span>
                    </div>
                </div>-->
            </div>
        
      
        </form>      
     
        
        <div class="custRow" style="margin-top: 10px;">
            <div class="custCol-12">
                <div class="commonCheckHolder" style="padding-bottom: 10px;">
                    <label >
                        <input id="chkAgree" type="checkbox">
                        <span></span><em>Confirm RFQ</em>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="custRow">
            <div class="custCol-6">
                <input type="button" id="btnapprove" class="btnIcon lightGreenV3" value="Update">
            </div>
        </div>
        
      
        
    </div>
<input type="hidden" name="initialPrice" id="initialPrice">
<input type="hidden" name="initialQuantity" id="initialQuantity">
<input type="hidden" name="remQuantity" id="remQuantity">
<input type="hidden" name="remPrice" id="remPrice">
<div class="commonLoaderV1"></div>
</div>
<div class="commonModalHolder">
    <div class="modalContent printModal">
        <a href="javascript:void(0);" class="btnAction print bgGreen btnImgPrint" style="display: none;">Print</a>
        <a href="javascript:void(0)" class="btnModalClose">Close(X)</a>
        <div id="printHolder">
            <iframe id="frame" style="width:100%;height:100%;"></iframe>
        </div>
    </div>
</div>

<script>
    var arrProductList = arrItems;
    var intGlobalEditIndex = -1;
    var intGlobalPrimaryUnit = -1;
    $('#initialPrice').val('0'); 
    $('#initialQuantity').val('0');
    $('#remQuantity').val('0'); 
    $('#remPrice').val('0');
    $('.statusMessage').hide();
    showproductlist();
    $('#btnapprove').removeAttr('disabled');
    $('#btnreject').removeAttr('disabled');
    
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
            },
            messages: {
                title: "Enter Title",
                supplier_id: "Select Supplier",
            },
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });

        $("#frmProduct").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                product: {required: true},
                quantity: {required: true,number:true},
                unit_id: {required: true},
                priceperunit:{number:true},
            },
            messages: {
                product: "Select Product",
                quantity: "Quantity",
                unit_id: "Select Unit",
                priceperunit: { number: "Enter a valid amount"},
            }
        });
        
        $("#frmdocs").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                planning_date: {required: true},
                stockinhand: {required: true},
                stock_period_from: {
                    required:function(){
                                return (parseFloat($('#stockinhand').val())>0);
                            }
                },
                stock_period_to: {
                    required:function(){
                                return (parseFloat($('#stockinhand').val())>0);
                            }
                },
                forecast_from: {required: true},
                forecast_to: {required: true},
            },
            messages: {
                planning_date: "Planning date is required",
                stockinhand: "Stock in hand is required",
                stock_period_from: "Stock period from is required",
                stock_period_to: "Stock period to is required",
                forecast_from: "Forecast from date is required",
                forecast_to: "Planning date is required",
            
            }
        });
        
        $("#chkpreferred").click(function () {
            
             $('.infoClose').click();
             
            if($("#supplier_id").val()==''){
                alert("Please Select Supplier");
                $("#chkpreferred").prop('checked',false);
                $("#chkallproducts").prop('checked',true);
            }else{
                $("#chkallproducts").prop('checked',false);
                $("#chkpreferred").prop('checked',true);
            }
            resetproductform();
             $(".add_product_list").html('');
        });
        
        $("#chkallproducts").click(function () {
            
             $('.infoClose').click();
            
            $("#chkpreferred").prop('checked',false);
            $("#chkallproducts").prop('checked',true);
            resetproductform();
             $(".add_product_list").html('');
        });

        $('#product').keyup(function () {
            $(".add_product_list").html('');
            $('#product_id').val('');
            $('#product_code').val('');
            $('#product_name').val('');
            var searchkey = $(this).val();

            if ($("#chkpreferred").prop('checked') && $("#supplier_id").val() == '') {
                alert("Please Select Supplier");
                $("#product").val('');
                return;
            }
            
            var preferredonly=0;
            if($("#chkpreferred").prop('checked')){
                preferredonly=1;
            }
            
            jQuery.ajax({
                url: "../../autocompleteinventory",
                type: 'POST',
                data: {searchkey: searchkey, preferredonly: preferredonly, supplierid: $("#supplier_id").val()},
                success: function (result) {
                    var total = result.length;
                    if (total == 0) {
                        resetproductform();
                    }
                    var liname = '';
                    $.each(result, function (i, value) {
                        liname += '<li id=' + value['id'] + ' attrcode=' + value['product_code'] + ' attrname="' + value['name'] + '">' + value['name'] + ' (' + value['product_code'] + ')' + '</li>';
                    });

                    $(".add_product_list").html(liname);

                    var $selectText = $('.bgSelect input');
                    var $selectLi = $('.bgSelect li');

                    var selectval;
                    var Drop = 0;

                    $('body').click(function () {
                        $('.bgSelect ul').hide();
                    });

                    $selectText.click(function () {
                        $('.bgSelect ul').hide();
                        $('.bgSelect ul').slideDown();
                    });
                    
                    $('.bgSelect ul').hide();
                    $('.bgSelect ul').slideDown();
                    
                    $selectLi.click(function () {
                        Drop = 1;
                        selectval = $(this).text();

                        $('#product').val(selectval);
                        $(this).parent().parent().find('.commonError').hide();
                        
                        $('#product_id').val($(this).attr('id'));
                        $('#product_code').val($(this).attr('attrcode'));
                        $('#product_name').val($(this).attr('attrname'));
                        getinventorydata($(this).attr('id'));
                    });

                },
            });
        });

        $('#add_product').on('click', function () {
            
            $('.tooltipInfo').hide();
            $('.statusMessage').hide();
                

            if (!$("#frmProduct").valid()) {
                return;
            }

            if($('#product_id').val()==''){
                alert("Please Select Product");
                return;
            }
            
            if($('#quantity').val()==0){
                alert("Please enter quantity greater than zero");
                return;
            }

            if(arrProductList.length>0 && intGlobalEditIndex==-1){
                alert("Please add only one item");
                return;
            }


            if(intGlobalEditIndex!=-1){
                arrProductList.splice(intGlobalEditIndex, 1);
                intGlobalEditIndex=-1;
            }

//            var intItemDuplicate = 0;
//            if (arrProductList.length > 0) {
//                for (var i = 0; i < arrProductList.length; i++) {
//                    if ($('#product_id').val() == arrProductList[i].product_id) {
//                        intItemDuplicate = 1;
//                    }
//                }
//            }
//
//            if (intItemDuplicate == 1) {
//                alert("Product Already Selected");
//                return;
//            }
            
            var isprimary=0;

            
            if($("#unit_id").val()==intGlobalPrimaryUnit){
                isprimary=1;
                var price = 0;

                if($("#quantity").val()!=''){

                    var price = $("#price").val();
                    var budgetQuantity=  $(".budgetQuantity").html();
                    var usedQuantity= $("#usedQuantity").val();
                    var remainingQuantity=budgetQuantity-usedQuantity;
                    var balanceInvAmount= $(".productbalanceAmount").val();
                    var totalQuantity = $("#quantity").val();
                  
                }
                
            } else {
                var price = 0;

                var conversionvalue = $("#unit_id").find(':selected').attr('attrconversionval');
                var quantity=$("#quantity").val();

                if($("#quantity").val()!=''){
                    var totalQuantity = conversionvalue * parseFloat(quantity);
                    var price = $("#price").val(); ;
                    var budgetQuantity=  $(".budgetQuantity").html();
                    var usedQuantity= $("#usedQuantity").val();
                    var remainingQuantity=budgetQuantity-usedQuantity;
                    var balanceInvAmount= $(".productbalanceAmount").val();

                }

            }
            
            var arraData = {
                product_id: $('#product_id').val(),
                product_code: $('#product_code').val(),
                product_name: $("#product_name").val(),
                quantity: $("#quantity").val(),
                unit_id: $("#unit_id").val(),
                isprimary: isprimary,
                unitname: $("#unit_id :selected").text(),
                priceperunit: amountformat($("#priceperunit").val()),
                price: amountformat($("#price").val()),
                qty_in_primary:totalQuantity,
                remQuantity:$('#remQuantity').val(),
                initialPrice:$('#initialPrice').val(),
                initialQuantity:$('#initialQuantity').val(),
                remPrice:$('#remPrice').val(),
            }

            arrProductList.push(arraData);
            showproductlist();
            resetproductform();
            $(".warehouseStock").text("");  
            $(".branchStock").text("");
            $(".initialBudget").text("");
            $(".budgetQuantity").text("");
            $(".balanceAmount").text("");
        });
        
        $("#btnapprove").click(function () {
            $("#reject_reason").removeClass("valErrorV1");
            $("#reject_reason-error").html('');
              
            if (!$("#frmaddrequisition").valid()) {
                return;
            }

            if (arrProductList.length == 0) {
                alert("Please add one product");
                return false;
            }
            
            if (!$("#frmdocs").valid()) {
                return false;
            }
            if($("#stockinhand").val() > 0) {
                if (date_difference($("#stock_period_to").val(), $("#forecast_from").val(), true) <= 0) {
                    alert('RFQ Order forecast period should greater than Available stock in hand To date');
                    return false;
                }
            }
            var blnConfirm = confirm("Are you sure to submit ?");
            if(!blnConfirm){
               return flase; 
            }
            
            var blnrfdstatus=2;
            if($("#chkAgree").prop('checked') == true){
               blnrfdstatus=1;
            }
            
            var arraData = {
                rfq_code: $('#rfq_code').val(),
                requisition_id: $('#requisition_id').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                supplier_id: $("#supplier_id").val(),
                total_price: amountformat($("#total_price").val()),
                total_vat: $("#total_vat").val(),
                
                payment_mode: $('input[name=payment_mode]:checked').val(),
                creditdays: $("#creditdays").val(),
                payment_terms: $("#payment_terms").val(),
                delivery_place: $("#delivery_place").val(),
                delivery_date: $("#delivery_date").val(),
                planning_date: $("#planning_date").val(),
                
                stockinhand: $("#stockinhand").val(),
                stock_period_from: $("#stock_period_from").val(),
                stock_period_to: $("#stock_period_to").val(),
                stocktotal: $("#stocktotal").val(),
                forecast_from: $("#forecast_from").val(),
                forecast_to: $("#forecast_to").val(),
                
                productspec: $("#productspec").val(),
                approvedsupplier: $('input[name=approvedsupplier]:checked').val(),
                last_purchase_date: $("#last_purchase_date").val(),
                last_qty: $("#last_qty").val(),
                last_value: $("#last_value").val(),
                blnrfdstatus: blnrfdstatus,
            }

            var arrData = JSON.stringify(arraData);
            var arrProductsList = JSON.stringify(arrProductList);
            
            var documents = new FormData($('#frmdocs')[0]);
            documents.append('arrData', arrData);
            documents.append('arrItems', arrProductsList);
            
            $('.commonLoaderV1').show();
            $('#btnapprove').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../update',
                data:documents, 
                contentType: false,  
                processData: false,
                success: function (return_data) {
                    $('.commonLoaderV1').hide();
                    $('#btnapprove').removeAttr('disabled');
                    window.location.href = '{{url("requisitions/editrfq")}}';
                }
            });
     
        });


        $('body').on('keyup', '#quantity', function () {
            calculateprice();
        });

        $('body').on('keyup', '#priceperunit', function () {
            calculateprice();
        });
        
        $('body').on('keyup', '#total_vat', function () {
            calculatevat();
        });
        
        $('body').on('click', '.clsmodeofpay', function () {
            if($(this).val()=="Credit"){
                $(".clscreditdays").show();
            }else{
                $(".clscreditdays").hide();
            }
            
        });
        
        function format_date(date_input, add_day=false, days=''){
            var ret_date = date_input;
            if(add_day === true && days != ''){
                const [day, month, year] = date_input.split("-");
                ret_date = new Date(year, month - 1, day);
                var ret_dat = new Date(ret_date.setDate(ret_date.getDate() + days));
                var date_a = ret_dat.getDate();
                var month_a = ret_dat.getMonth() + parseInt(1);
                var year_a = ret_dat.getFullYear();
                ret_date = date_a + '-' + month_a + '-' + year_a;
            }
            return ret_date;
        }
        $("#delivery_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
        });
        
        $("#planning_date").datepicker({
            changeMonth: true,
            changeYear: true,
            minDate:'0',
            dateFormat: 'dd-mm-yy'
        });
        
        currentDate = new Date(); 
        currentDate.setDate(currentDate.getDate() - 30);
        
        $("#stock_period_from").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate: currentDate,
            onSelect: function(selectedDate) {
                selectedDate = format_date(selectedDate,true,1);
                $("#stock_period_to").datepicker("option", "minDate", selectedDate);
                selectedDate = format_date(selectedDate,true,1);
                $("#forecast_from").datepicker("option", "minDate", selectedDate);
                selectedDate = format_date(selectedDate,true,1);
                $("#forecast_to").datepicker("option", "minDate", selectedDate);
                var diff = date_difference($('#stock_period_from').val(),$('#stock_period_to').val());
                $('#indaysstock').val(diff+1);
            }
        });
        $("#stock_period_to").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
             onSelect: function(selectedDate) {
                selectedDate = format_date(selectedDate,true,1);
                $("#forecast_from").datepicker("option", "minDate", selectedDate);
                selectedDate = format_date(selectedDate,true,1);
                $("#forecast_to").datepicker("option", "minDate", selectedDate);
                var diff = date_difference($('#stock_period_from').val(),$('#stock_period_to').val());
                $('#indaysstock').val(diff+1);
            }
        });
        $("#forecast_from").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate: $("#stock_period_to").val(),
             onSelect: function(selectedDate) {
                selectedDate = format_date(selectedDate,true,1);
                $("#forecast_to").datepicker("option", "minDate", selectedDate);
                var diffe = date_difference($('#forecast_from').val(),$('#forecast_to').val());
                $('#indaysrfqstock').val(diffe+1);
            }
        });
        $("#forecast_to").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(selectedDate) {
                var differ = date_difference($('#forecast_from').val(),$('#forecast_to').val());
                $('#indaysrfqstock').val(differ+1);
            }
        });
        
        function date_difference(date1,date2,with_sign=false){
            const [day1, month1, year1] = date1.split("-");
                date1 = new Date(year1, month1 - 1, day1);
            const [day2, month2, year2] = date2.split("-");
                date2 = new Date(year2, month2 - 1, day2);
            if(with_sign === true){
                var timeDiff = date2.getTime() - date1.getTime();
                var diffDays = (timeDiff / (1000 * 3600 * 24));
            }
            else{
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
            }
            
            if(isNaN(diffDays)){
                return '';
            }  
            
            return diffDays;
        }
        var datePurchase = new Date();
        datePurchase.setDate(datePurchase.getDate() - 1);
        $("#last_purchase_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            maxDate : datePurchase
        });
        
        $('.btnModalClose').on('click', function () {
            $('#frame').attr("src", "");
            $('.commonModalHolder').hide()
        });
        
        $('body').on('click', '.btnImgPrint', function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('#printHolder').html());
            win.document.close();
            win.print();
            win.close();
        });
 
    });

    function showproductlist() {
        $("#tblproducts").html('<tr><td>No products added<td></tr>');
        
        if (arrProductList.length > 0) {
            var strHtml = '';
            var totPrice=0;
            var totVat=0;
            var grandTot=0;
            for (var i = 0; i < arrProductList.length; i++) {
                totPrice=parseFloat(totPrice)+parseFloat(arrProductList[i].price);
                strHtml += '<tr><td>' + arrProductList[i].product_code + '</td><td>' + arrProductList[i].product_name + '</td>\n\
                            <td class="amountAlign">' + arrProductList[i].quantity + '</td><td>' + arrProductList[i].unitname + '</td>\n\
                        <td class="amountAlign">' + amountformat(arrProductList[i].priceperunit) + '</td><td class="amountAlign">' + amountformat(arrProductList[i].price) + '</td>' +
                        '<td class="tbleActionSet"><div class="iconInfoWrapper">' +
                        '<a href="javascript:budgetInfo(' + i + ')" class="tbleInfo btnTooltip" title="information"></a>' +
                        '<div class="tooltipInfo inventoryBudget">' +
                        '<a href="javascript:void(0);" class="infoClose"></a>' +
                        '<strong>Budget Details</strong>' +
                        '<span>Intial Budget Amount:<strong class="initialBgt'+i+'"></strong> </span>' +
                        '<span>Balance Budget Amount:<strong class="balanceBgt'+i+'"></strong> </span>' +
                        '<span>Stock in Branch:<strong class="branchStk'+i+'"></strong> </span>' +
                        '<span>Stock in Warehouse:<strong class="warehouseStk'+i+'"></strong> </span>' +
                        '<span>Budget Quantity:<strong class="quantityBgt'+i+'"></strong> </span>' +
                        '<span>Remaining Quantity:<strong class="remQuantity'+i+'"></strong> </span>' +
                        '</div>' +
                        '</div><a href="javascript:editproduct(' + i + ')" class="tbleEdit" title="Edit"></a>' +
                        '<a href="javascript:removeproduct(' + i + ')" class="tbleClose" title="Delete"></a></td></tr>';

            }
//            totVat=parseFloat(totPrice)*5/100;
            totPrice=amountformat(totPrice);
            
            totVat=amountformat($("#tempvattotal").val());
           
            grandTot=amountformat(parseFloat(totVat)+parseFloat(totPrice));
            strHtml+='<tr><td></td><td></td><td></td><td></td><td class="amountAlign"><strong>Sub Total</strong></td><td class="amountAlign"><strong>'+ totPrice +'</strong></td><td></td></tr>';
            strHtml+='<tr><td></td><td></td><td></td><td></td><td class="amountAlign"><strong>VAT Amount</strong></td><td class="amountAlign"><input type="text" id="total_vat" value='+ totVat +' class="vatInput numberwithdot"></td><td></td></tr>';
            strHtml+='<tr><td></td><td></td><td></td><td></td><td class="amountAlign"><span><strong>Total Amount</strong></span></td><td class="amountAlign"><span><strong id="tdgrandtotal">'+ grandTot +'</strong></span></td><td></td></tr>';
           
            $("#total_price").val(grandTot);
            $("#stocktotal").val(arrProductList[0].quantity);
            $("#tblproducts").html(strHtml);
            
        }
    }
    

    
    function budgetInfo(index){
        $.ajax({
            type: 'POST',
            url: '../getinventorydataQuarter',
            data: '&productid=' + arrProductList[index].product_id+ '&createddate=' + $('#createddate').val(),
            async:false,
            success: function (return_data) { 
               // console.log(return_data);
                if (return_data != -1) {
                    var inventorydata = return_data.inventorydata;
                    var altunits = return_data.altunits;
                    var budgetDetails=return_data.budgetDetails;
                    
                    
                    if(isNaN(budgetDetails.quantity) || budgetDetails.quantity==null){
                   budgetDetails.quantity=0;
                }if(isNaN(budgetDetails.usedQuantity) || budgetDetails.usedQuantity==null){
                   budgetDetails.usedQuantity=0;
                }
                    
                    $('.initialBgt'+index).text(budgetDetails.format_initial);
                    $('.balanceBgt'+index).text(budgetDetails.format_balance);
                    $('.branchStk'+index).text(return_data.branchStock);
                    $('.warehouseStk'+index).text(return_data.warehosueStock);
                    $('.quantityBgt'+index).text(budgetDetails.quantity); 
                    
                     var remQuantity=parseFloat(budgetDetails.quantity)-parseFloat(budgetDetails.usedQuantity);
                   //  var remPrice=parseFloat(budgetDetails.price)-parseFloat(budgetDetails.usedData);
                if(isNaN(remQuantity)){
                    remQuantity=0;
                } 
                   $('.remQuantity'+index).text(remQuantity); 
                     
                }
            }
        });
    }

    function removeproduct(index) {
        arrProductList.splice(index, 1);
        resetproductform();
        showproductlist();
    }

    function editproduct(index) {
        $('#product_id').val(arrProductList[index].product_id);
        $("#product_code").val(arrProductList[index].product_code);
        $("#product_name").val(arrProductList[index].product_name);
        $("#product").val(arrProductList[index].product_name+" ("+arrProductList[index].product_name+")");
        $("#quantity").val(arrProductList[index].quantity);
        $("#price").val(amountformat(arrProductList[index].price));
        
        getinventorydata(arrProductList[index].product_id);
       
        $("#unit_id").val(arrProductList[index].unit_id);
        $("#priceperunit").val(amountformat(arrProductList[index].priceperunit));
        intGlobalEditIndex = index;
    }

    function resetproductform() {
        $('#product').val('');
        $('#product_id').val('');
        $('#product_code').val('');
        $('#product_name').val('');
        $('#quantity').val('');
        $(".clsunit").html("<option>Select Unit</option>");
        $('#priceperunit').val('');
        $('#price').val('');
        $('.infoClose').click();
        clearContent();
        intGlobalPrimaryUnit = -1;
    }

    function getinventorydata(id) {
        $.ajax({
            type: 'POST',
            url: '../getinventorydataQuarter',
            data: '&productid=' + id+ '&createddate=' + $('#createddate').val(),
            async:false,
            success: function (return_data) {
               
                if (return_data != -1) { 
                    var inventorydata = return_data.inventorydata;
                    var altunits = return_data.altunits;
                    var budgetDetails=return_data.budgetDetails;
                    
                    intGlobalPrimaryUnit = inventorydata.primary_unit;
                    var strHtml = '<option value="' + inventorydata.primary_unit + '" selected attrconversionval="1">' + inventorydata.primaryunitname + '</option>';
                    for (var i = 0; i < altunits.length; i++) {
                        strHtml = strHtml + '<option value="' + altunits[i].unit_id + '" attrconversionval="' + altunits[i].conversion_value + '">' + altunits[i].altunitname + '</option>'
                    }
                    $(".clsunit").html(strHtml);
                    var price=amountformat(inventorydata.price);
                    $("#priceperunithidden").val(price);
                    if(Object.keys(budgetDetails).length>0){
                        
                        if(isNaN(budgetDetails.quantity) || budgetDetails.quantity==null){
                            budgetDetails.quantity=0;
                        }
                        
                        if(isNaN(budgetDetails.usedQuantity) || budgetDetails.usedQuantity==null){
                            budgetDetails.usedQuantity=0;
                        }
                        $(".initialBudget").text(budgetDetails.format_initial);    
                        $(".balanceAmount").text(budgetDetails.format_balance); 
                        $(".budgetQuantity").text(budgetDetails.quantity);
                        $(".productbalanceAmount").val(budgetDetails.balance); 
                        $("#usedQuantity").val(budgetDetails.usedQuantity);
                        
                        var remQuantity=parseFloat(budgetDetails.quantity)-parseFloat(budgetDetails.usedQuantity);
                         if(isNaN(remQuantity)){
                         remQuantity=0;
                        }
                        $(".remQuantity").text(remQuantity);
                    }else{
                        $(".initialBudget").text(0);    
                        $(".balanceAmount").text(0); 
                        $(".budgetQuantity").text(0);
                        $(".productbalanceAmount").val(0);
                    }
                    
                    $(".warehouseStock").text(return_data.warehosueStock); 
                    $(".branchStock").text(return_data.branchStock);
                    
                    var remPrice=parseFloat(budgetDetails.price)-parseFloat(budgetDetails.usedData);
                    if(isNaN(remPrice)){
                        remPrice=0;
                    }
                    
                    $('#initialPrice').val(budgetDetails.price); 
                    $('#initialQuantity').val(budgetDetails.quantity); 
                    $('#remQuantity').val(remQuantity); 
                    $('#remPrice').val(remPrice);

                }
            }
        });
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
       
    function calculatevat() {
        if (arrProductList.length > 0) {
            var totPrice=0;
            for (var i = 0; i < arrProductList.length; i++) {
                totPrice=parseFloat(totPrice)+parseFloat(arrProductList[i].price);
            }
            var totalvat=$("#total_vat").val();
            var vatplustotal=0;
            if(totalvat=='' || isNaN(totalvat)){
                vatplustotal=amountformat(parseFloat(totPrice));
            }else{
                vatplustotal=amountformat(parseFloat(totPrice)+parseFloat(totalvat));
                $("#tempvattotal").val(totalvat);
            }
            
            $("#tdgrandtotal").html(vatplustotal);
        }
    }
    
    
    function clearContent(){
       $('.initialBudget').text("");
       $('.balanceAmount').text(""); 
       $('.branchStock').text("");
       $('.warehouseStock').text("");
       $('.budgetQuantity').text("");
       $('.remQuantity').text("");
    }
    
    function calculate_days(from,to){
        var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
        var fromAr = from.split('-');
        var fromDate = fromAr[2] + ',' + fromAr[1] + ',' + fromAr[0];
        var toAr = to.split('-');
        var toDate = toAr[2] + ',' + toAr[1] + ',' + toAr[0];
        var firstDate = new Date(fromDate);
        var secondDate = new Date(toDate);

        var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
        return diffDays; 
    }
</script>

@endsection