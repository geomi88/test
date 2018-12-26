@extends('layouts.main')
@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Import Purchase Requisition</h1>
    </header>
    <div class="inputAreaWrapper">
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
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Requisition Title</label>
                        <input type="text" id="title" name="title" autocomplete="off" placeholder="Enter Title" maxlength="250">
                        <input type="hidden" value="{{$requisitioncode}}" id="requisition_code" name="requisition_code">
                        <input type="hidden" value="Supplier" id="party_type" name="party_type">
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
        
        <div class="statusMessage">
		<span class="errorMsg" ></span>
        </div>
        <form id="frmProduct">
            <div class="invertoryFieldHolder">
                <div class="custRow">
                    <div class="custCol-4" style="padding-bottom: 5px;">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input type="checkbox" name="chkpreferred" id="chkpreferred" class="preferredonly">
                                <span></span>
                                <em>Preferred Products</em>
                            </label>
                        </div>
                    </div>
                    <div class="custCol-3">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input type="checkbox" name="chkallproducts" id="chkallproducts" class="preferredonly" checked>
                                <span></span>
                                <em>All Products</em>
                            </label>
                        </div>
                    </div>
                    <div class="customClear "></div>
                    <div class="custCol-4 prInventory">
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
                                <input type="hidden"  name="pending" id="pending" >
                                <input type="hidden" class="productbalanceAmount"  name="productbalanceAmount" id="productbalanceAmount" >
                           
                            </div>
                            <span class="commonError"></span>
                        </div>
                    </div>

                    <div class="custCol-4 inventory">
                        <div class="inputHolder">
                            <label>RFQ Code.</label>
                            <div class="bgSelect">                                                          
                                <input type="text" name="rfqnumber" id="rfqnumber" autocomplete="off" placeholder="Enter RFQ Code">
                    
                                <input type="hidden"  name="rfq_id" id="rfq_id" >
                                <input type="hidden"  name="rfq_code" id="rfq_code" >
                                <input type="hidden"  name="rfq_url" id="rfq_url" >
                           
                            </div>
                            <span id="spninvalidrfq" class="commonError"></span>
                        </div>
                    </div>
                    <div class="clsRFQcode" id="clsRFQcode"></div>
                    <div class="customClear "></div>
                    
                    <div class="custCol-3 unit">
                        <div class="inputHolder">
                            <label>Unit</label>
                            <select id="unit_id" name="unit_id" class="clsunit">
                                <option value=''>Select Unit</option>
                            </select>
                            <span class="commonError"></span>
                        </div>
                    </div>
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
                            <input type="hidden" id="priceperunithidden" name="priceperunithidden">
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
                    <div class="custCol-2 addBtnHolder">
                        <div class="iconInfoWrapper">

                            <a href="javascript:void(0);" class="tbleInfo btnTooltip" title="information" ></a>
                            <div class="tooltipInfo">
                                <a href="javascript:void(0);" class="infoClose"></a>
                                <strong>Budget Details of Current Year</strong>
                                <span>Intial Budget Amount:<strong class="initialBudget"></strong> </span>
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
                        <th>RFQ</th>
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
        
        <form id="frmdocs" name="frmdocs" method="post" enctype="multipart/form-data">
          
            <div class="custRow clsReqItemSeparator" >
                <div class="custCol-4">
                    <div class="inputHolder checkHolder">
                        <label>Mode of Payment :</label>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="payment_mode" checked value="Cash" type="radio" class="clsmodeofpay"  >
                                <span></span>
                                <em>Cash</em>
                            </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="payment_mode" value="Credit" type="radio" class="clsmodeofpay" >
                                <span></span>
                                <em>Credit</em>
                            </label>
                        </div>

                    </div>
                </div>
                
                <div class="custCol-4 clscreditdays" style="display: none;">
                    <div class="inputHolder">
                        <label>Credit Days :</label>
                        <input placeholder="Enter Credit Days" class="numberwithdot" name="creditdays" id="creditdays" autocomplete="off" type="text" maxlength="10">
                        <span class="commonError"></span>

                    </div>
                </div>
               
            </div>
            
            <div class="custRow" >
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Terms of Payment :</label>
                        <input placeholder="Enter Terms of Payment" name="payment_terms" id="payment_terms" autocomplete="off" type="text" maxlength="200">
                        <span class="commonError"></span>

                    </div>
                </div>
                
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Delivery Place :</label>
                        <input placeholder="Enter Delivery Place" name="delivery_place" id="delivery_place" autocomplete="off" type="text" maxlength="250">
                        <span class="commonError"></span>

                    </div>
                </div>
            </div>
            
            <div class="custRow" >
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Delivery Date :</label>
                        <input placeholder="Enter Delivery Date" name="delivery_date" id="delivery_date" autocomplete="off" type="text">
                        <span class="commonError"></span>

                    </div>
                </div>
                <div class="custCol-4 ">
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
                <div class="inputHolder">
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
    <div class="commonLoaderV1"></div>
</div>

<script>
    var arrProductList = [];
    var intGlobalEditIndex = -1;
    var intGlobalPrimaryUnit = -1;
    var intValidRfq=-1;
    $('.statusMessage').hide();
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
            },
            messages: {
                title: "Enter Title",
                supplier_id: "Select Supplier",
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
                rfqnumber: {required: true},
                quantity: {required: true,number:true},
                unit_id: {required: true},
                priceperunit: {required: true,number:true},
            },
            messages: {
                product: "Select Product",
                rfqnumber: "Enter RFQ Code",
                quantity: "Quantity",
                unit_id: "Select Unit",
                priceperunit: {
                 required: "Enter Price / Product",
                 number: "Enter a valid amount"
                 },
            }
        });
        
        $("#chkpreferred").click(function () {
             clearContent();
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
             clearContent();
             
            $("#chkpreferred").prop('checked',false);
            $("#chkallproducts").prop('checked',true);
            resetproductform();
             $('.infoClose').click();
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
                url: "../autocompleteinventory",
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
                        $(this).closest('.bgSelect').find('ul').hide();
                        $(this).closest('.bgSelect').find('ul').slideDown();
                    });
                    
                    $(this).closest('.bgSelect').find('ul').hide();
                    $(this).closest('.bgSelect').find('ul').slideDown();
                        
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

        $('#rfqnumber').on('blur', function () {
            getrfqdata($.trim($('#rfqnumber').val()));
        });
        
//        $('#rfqnumber').on('keyup', function () {
//            getrfqdata($.trim($('#rfqnumber').val()));
//        });
        
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
            
            if($('#priceperunit').val()==0){
                alert("Please check the price you have entered");
                return;
            }

            if ($("#rfqnumber").val()!='' && intValidRfq!=1) {
                $('.errorMsg').text("Invalid RFQ"); 
                $('.statusMessage').show();
                return;
            }else{
                $('.statusMessage').hide();
            }
            
            if(intGlobalEditIndex!=-1){
                arrProductList.splice(intGlobalEditIndex, 1);
                intGlobalEditIndex=-1;
            }

            var intItemDuplicate = 0;
            if (arrProductList.length > 0) {
                for (var i = 0; i < arrProductList.length; i++) {
                    if ($('#product_id').val() == arrProductList[i].product_id) {
                        intItemDuplicate = 1;
                    }
                }
            }

            if (intItemDuplicate == 1) {
                alert("Product Already Selected");
                return;
            }
            
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
                 
                 if(parseFloat(totalQuantity)>parseFloat(remainingQuantity)){
                     $('.errorMsg').text("Quantity exceeds your remaining quantity"); 
                     $('.statusMessage').show();
                    
                     return;
                 }

                 if(parseFloat(price)>parseFloat(balanceInvAmount)){
                     $('.errorMsg').text("Total price exceeds your balance amount"); 
                     $('.statusMessage').show();
                    
                     return;
                 }
                  
              }
                
            } else {
                 var price = 0;
                 var conversionvalue = $("#unit_id").find(':selected').attr('attrconversionval');
                 var quantity=$("#quantity").val();
          
             if($("#quantity").val()!=''){
                 var totalQuantity = conversionvalue * parseFloat(quantity);
                    var price = $("#price").val();
                 var budgetQuantity=  $(".budgetQuantity").html();
                 var usedQuantity= $("#usedQuantity").val();
                 var remainingQuantity=budgetQuantity-usedQuantity;
                 var balanceInvAmount= $(".productbalanceAmount").val();
                 if(parseFloat(totalQuantity)>parseFloat(remainingQuantity)){
                     $('.errorMsg').text("Quantity exceeds your remaining quantity"); 
                    $('.statusMessage').show();
                   
                    return;
                 }
                 if(parseFloat(price)>parseFloat(balanceInvAmount)){
                      $('.errorMsg').text("Total price exceeds your inventory budget"); 
                     $('.statusMessage').show();
                    
                     return;
                 }
                  
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
                budgetAmount:$('.initialBudget').text(),
                balanaceAmount:$(".balanceAmount").text(),
                stockBranch:$(".branchStock").text(),
                stockWarehouse:$('.warehouseStock').text(),
                budgetQuan:$(".budgetQuantity").text(),
                qty_in_primary:totalQuantity,
                remQuantity:remainingQuantity,
                rfq_id:$("#rfq_id").val(),
                rfq_code:$("#rfq_code").val(),
                rfq_url:$("#rfq_url").val(),
            }

            arrProductList.push(arraData);
            showproductlist();
            resetproductform();
            $(".warehouseStock").text("");  
            $(".branchStock").text("");
            $(".initialBudget").text("");
            $(".budgetQuantity").text("");
            $(".balanceAmount").text("");
            $(".productbalanceAmount").val("0");
           
        });
     
        $("#btnSaveRequisition").click(function () {
            if (!$("#frmaddrequisition").valid()) {
                return;
            }

            if (arrProductList.length == 0) {
                alert("Please add atleast one product");
                return;
            }
            
            var requisitionTotalPrice=  $("#total_price").val();
            var pending=  $("#pending").val();
            
         if(isNaN(pending)){
             pending=0;
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
            
            var isallproducts=1;
            if($("#chkpreferred").prop('checked')){
                isallproducts=2;
            }
            
            var arraData = {
                requisition_code: $('#requisition_code').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                supplier_id: $("#supplier_id").val(),
                party_type: $("#party_type").val(),
                isallproducts: isallproducts,
                total_price: amountformat($("#total_price").val()),
                total_vat: $("#total_vat").val(),
                payment_mode: $('input[name=payment_mode]:checked').val(),
                creditdays: $("#creditdays").val(),
                payment_terms: $("#payment_terms").val(),
                delivery_place: $("#delivery_place").val(),
                delivery_date: $("#delivery_date").val(),
            }

            var arrData = JSON.stringify(arraData);
            var arrProductsList = JSON.stringify(arrProductList);
            
            var documents = new FormData($('#frmdocs')[0]);
            documents.append('arrData', arrData);
            documents.append('arrItems', arrProductsList);
            
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
                    $('#btnSaveRequisition').removeAttr('disabled');
                    window.location.href = '{{url("requisitions/outbox")}}';
                }
            });
            $('#btnSaveRequisition').removeAttr('disabled');
        });

        $('body').on('change', '.supplier_id', function () {
           arrProductList.length =0;
           resetproductform();
             $('.statusMessage').hide();
            $("#tblproducts").html('<tr><td>No products added<td></tr>');
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

        $('body').on('keyup', '#quantity', function () {
            calculateprice();
        });
        
        $('body').on('keyup', '#priceperunit', function () {
            calculateprice();
        });

        $('body').on('keyup', '#total_vat', function () {
            calculatevat();
        });
        
        $('body').on('change', '#unit_id', function () {
//            calculateprice();
        });

        $('body').on('click', '.clsmodeofpay', function () {
            if($(this).val()=="Credit"){
                $(".clscreditdays").show();
            }else{
                $(".clscreditdays").hide();
            }
            
    });
    
        $("#delivery_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
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
                
                var strrfqlnk='<td></td>';
                if(arrProductList[i].rfq_url!=''){
                    strrfqlnk='<td ><a href="'+ arrProductList[i].rfq_url +'" target="_blank">' + arrProductList[i].rfq_code + '</a></td>';
                }
                    
                strHtml += '<tr><td>' + arrProductList[i].product_code + '</td><td>' + arrProductList[i].product_name + '</td>'+strrfqlnk+'\n\
                            <td class="amountAlign">' + arrProductList[i].quantity + '</td><td>' + arrProductList[i].unitname + '</td>\n\
                        <td class="amountAlign">' + arrProductList[i].priceperunit + '</td><td class="amountAlign">' + arrProductList[i].price + '</td>' +
                        '<td class="tbleActionSet"><div class="iconInfoWrapper">' +
                        '<a href="javascript:void(0);" class="tbleInfo btnTooltip" title="information"></a>' +
                        '<div class="tooltipInfo">' +
                        '<a href="javascript:void(0);" class="infoClose"></a>' +
                        '<strong>Budget Details of Current Quarter</strong>' +  
                        '<span>Intial Budget Amount: <strong>'+arrProductList[i].budgetAmount+'</strong> </span>' +
                        '<span>Balance Budget Amount:<strong>'+arrProductList[i].balanaceAmount+'</strong> </span>' +
                        '<span>Stock in Warehouse: <strong>'+ arrProductList[i].stockWarehouse+'</strong> </span>' +
                        '<span>Stock in Branch: <strong>'+ arrProductList[i].stockBranch+'</strong> </span>' +
                        '<span>Budget Quantity: <strong>'+arrProductList[i].budgetQuan+'</strong> </span>' +
                        '<span>Remaining Quantity: <strong>'+arrProductList[i].remQuantity+'</strong> </span>' +
                        '</div>' +
                        '</div><a href="javascript:editproduct(' + i + ')" class="tbleEdit" title="Edit"></a>' +
                        '<a href="javascript:removeproduct(' + i + ')" class="tbleClose" title="Delete"></a></td></tr>';

            }
            totVat=parseFloat(totPrice)*5/100;
            totPrice=amountformat(totPrice);
            totVat=amountformat(totVat);
            grandTot=amountformat(parseFloat(totVat)+parseFloat(totPrice));
            strHtml+='<tr><td></td><td></td><td></td><td></td><td class="amountAlign" colspan="2"><strong>Sub Total</strong></td><td class="amountAlign"><strong>'+ totPrice +'</strong></td><td></td></tr>';
            strHtml+='<tr><td></td><td></td><td></td><td></td><td class="amountAlign" colspan="2"><strong>VAT Amount (5%)</strong></td><td class="amountAlign"><strong><input type="text" id="total_vat" value='+ totVat +' class="vatInput numberwithdot"></strong></td><td></td></tr>';
            strHtml+='<tr><td></td><td></td><td></td><td></td><td class="amountAlign" colspan="2"><span><strong>Total Amount</strong></span></td><td class="amountAlign"><span><strong id="tdgrandtotal">'+ grandTot +'</strong></span></td><td></td></tr>';
            
            $("#total_price").val(grandTot);
            $("#tblproducts").html(strHtml);
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
            }
            
            $("#tdgrandtotal").html(vatplustotal);
        }
    }
    
    function budgetinfo(index){ 
        
        var budHtml = '';
        $.ajax({
            type: 'POST',
            url: 'getinventorydata',
            data: '&productid=' + arrProductList[index].product_id,
            success: function (return_data) { 
                
           
                if (return_data != -1) { 

                    var budgetDetails=return_data.budgetDetails;
                    var initial=0;
                    var balance=0;
                    var quantity=0;
                    if(Object.keys(budgetdata).length>0){
                        initial = budgetDetails.format_initial;
                        balance = budgetDetails.format_balance;
                        quantity = budgetDetails.quantity;
                    }
                    budHtml = '<a href="javascript:void(0);" class="infoClose"></a>' +
                        '<strong>Budget Details</strong>' +
                        '<span>Intial Budget Amount:<strong>'+initial+'</strong> </span>' +
                        '<span>Balance Budget Amount:<strong>'+balance+'</strong> </span>' +
                        '<span>Stock in Warehouse:<strong>'+return_data.warehosueStock+'</strong> </span>' +
                        '<span>Budget Quantity:<strong>'+quantity+'</strong> </span>' ;
                
                        $(".inventorybudget").html(budHtml);

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
        $("#product").val(arrProductList[index].product_name+" ("+arrProductList[index].product_code+")");
        $("#quantity").val(arrProductList[index].quantity);
        $("#price").val(arrProductList[index].price);
        
        getinventorydata(arrProductList[index].product_id);
       
        $("#unit_id").val(arrProductList[index].unit_id);
        $("#priceperunit").val(arrProductList[index].priceperunit);
        
        $('#rfq_id').val(arrProductList[index].rfq_id);
        $("#rfq_code").val(arrProductList[index].rfq_code);
        $("#rfq_url").val(arrProductList[index].rfq_url);
        $("#rfqnumber").val(arrProductList[index].rfq_code);
        intGlobalEditIndex = index;
        
        if(arrProductList[index].rfq_code){
            $("#clsRFQcode").html('<a href="'+arrProductList[index].rfq_url+'" id="lnkrfd" target="_blank">'+arrProductList[index].rfq_code+'</a>'); 
        }else{
            $("#clsRFQcode").html('');
        }
        
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
        
        $("#rfqnumber").val('');
        $("#rfq_id").val('');
        $("#rfq_code").val('');
        $("#rfq_url").val('');
        $("#clsRFQcode").html('');
        intGlobalPrimaryUnit = -1;
        clearContent();
    }

    function clearRfqData(){
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
    }
    
    function getinventorydata(id) {
        $.ajax({
            type: 'POST',
            url: 'getinventorydata',
            data: '&productid=' + id,
            async:false,
            success: function (return_data) { 
              
                if (return_data != -1) {
                    var inventorydata = return_data.inventorydata;
                    var altunits = return_data.altunits;
                    var budgetdata=return_data.budgetDetails;
                    
                    intGlobalPrimaryUnit = inventorydata.primary_unit;
                    var strHtml = '<option value="' + inventorydata.primary_unit + '" selected attrconversionval="1">' + inventorydata.primaryunitname + '</option>';
                    for (var i = 0; i < altunits.length; i++) {
                        strHtml = strHtml + '<option value="' + altunits[i].unit_id + '" attrconversionval="' + altunits[i].conversion_value + '">' + altunits[i].altunitname + '</option>'
                    }
                    $(".clsunit").html(strHtml);
                    var price=amountformat(inventorydata.price);
                    $("#priceperunithidden").val(price); 
                    $(".warehouseStock").text(return_data.warehosueStock); 
                    $(".branchStock").text(return_data.branchStock);
             if(Object.keys(budgetdata).length>0){
                  if(isNaN(budgetdata.quantity) || budgetdata.quantity==null){
                   budgetdata.quantity=0;
                }if(isNaN(budgetdata.usedQuantity) || budgetdata.usedQuantity==null){
                   budgetdata.usedQuantity=0;
                }
                $(".initialBudget").text(budgetdata.format_initial);    
                $(".balanceAmount").text(budgetdata.format_balance); 
                $(".budgetQuantity").text(budgetdata.quantity);
                $(".productbalanceAmount").val(budgetdata.balance); 
                $("#usedQuantity").val(budgetdata.usedQuantity);
                
                
                var remQuantity=parseFloat(budgetdata.quantity)-parseFloat(budgetdata.usedQuantity);
                if(isNaN(remQuantity)){
                    remQuantity=0;
                }
                $(".remQuantity").text(remQuantity);
                
             }else{
                $(".initialBudget").text(0);    
                $(".balanceAmount").text(0); 
                $(".budgetQuantity").text(0);
                $(".productbalanceAmount").val(0);
                $(".budgetQuantity").text(0);
                $("#usedQuantity").val(0);
                $("#remQuantity").val(0);
            }
            
//                    $("#priceperunit").val(price);
                }else{
                   
            $(".warehouseStock").text(""); 
            $(".branchStock").text("");
            $("#usedQuantity").val(0);
                    
                    
                }
            }
        });
    }
  
    function getrfqdata(code) {
        intValidRfq=-1;
  
        if(code!=''){
            $.ajax({
                type: 'POST',
                url: '../getrfqdata',
                data: {searchkey: code, supplierid: $("#supplier_id").val()},
                beforeSend: function () {
                    $(".commonLoaderV1").show();
                },
                success: function (return_data) { 
                 
                    if(return_data!=-1 && !jQuery.isEmptyObject(return_data)){
                        $("#rfq_id").val(return_data.id); 
                        $("#rfq_code").val(return_data.rfq_code); 
                        $("#rfq_url").val(return_data.rfqurl); 
                        $("#clsRFQcode").html('<a href="'+return_data.rfqurl+'" id="lnkrfd" target="_blank">'+return_data.rfq_code+'</a>'); 
                        
                        $('#spninvalidrfq').html('');
                        $('#spninvalidrfq').hide();
                        $('#rfqnumber').removeClass('valErrorV1'); 
                        intValidRfq=1;
                        
                        var arrDetails=return_data.arrDetails;
                       
                        $('#product_id').val(arrDetails.item_id);
                        $('#product_code').val(arrDetails.product_code);
                        $('#product_name').val(arrDetails.name);
                        $('#product').val(arrDetails.name+' ('+arrDetails.product_code+')');

                        $('#quantity').val(arrDetails.quantity);
                        $('#priceperunit').val(arrDetails.unit_price);

                        getinventorydata(arrDetails.item_id);
                        $('#unit_id').val(arrDetails.alternate_unit_id);
                        calculateprice();
                        
                    } else{
                        $("#rfq_id").val(''); 
                        $("#rfq_code").val(''); 
                        $("#rfq_url").val(''); 
                        $("#clsRFQcode").html(''); 
                        $('#spninvalidrfq').html('Invalid RFQ'); 
                        $('#spninvalidrfq').show(); 
                        $('#rfqnumber').addClass('valErrorV1'); 
                         intValidRfq=-1;
                        clearRfqData();
                    }
                    
                    $(".commonLoaderV1").hide();
                },
                error: function () { 
                    $(".commonLoaderV1").hide();
                },
                
            });
            
        }else{
            $("#rfq_id").val(''); 
            $("#rfq_code").val(''); 
            $("#rfq_url").val(''); 
            $("#clsRFQcode").html(''); 
            intValidRfq=-1;
            $('#spninvalidrfq').hide();
            $('#spninvalidrfq').html('');
            $('#rfqnumber').removeClass('valErrorV1'); 
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
       
  
   function clearContent(){
       $('.initialBudget').text("");
       $('.balanceAmount').text(""); 
       $('.branchStock').text("");
       $('.warehouseStock').text("");
       $('.budgetQuantity').text("");
       $('.remQuantity').text("");
    }
 
</script>

@endsection