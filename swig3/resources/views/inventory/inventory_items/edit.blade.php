@extends('layouts.main')
@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('inventory/inventory_items') }}">Back</a>
    <header class="pageTitle">
        <h1>Update <span>Inventory</span></h1>
    </header>
    
    <form action="{{ action('Inventory\InventoryController@update') }}" method="post" id="frmeditinventory" enctype="multipart/form-data">
        <input type="hidden" id="altunitdetails" name="altunitdetails" value="">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Choose Company</label>
                        <select class="commoSelect" name="company_id" id="company_id">
                            <option value=''>Choose Company</option>
                            @foreach ($companies as $eachCompany)
                            <option value='{{ $eachCompany->id }}' <?php if($eachCompany->id == $inventory_data->company_id){ ?>selected="selected" <?php } ?>>{{ $eachCompany->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                
                <div class="custCol-4">
                    <div class="inputHolder pcode">
                        <label>Product Code</label>
                        <input type="hidden" name="cid" id="cid" value='{{ $inventory_data->id }}'>                       
                        <input type="text" name="p_code" value='{{ $inventory_data->product_code }}' id="p_code" onpaste="return false;" autocomplete="off" placeholder="Enter Product Code">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                
                <div class="custCol-4">
                    <div class="inputHolder scode">
                        <label>Supplier I Code</label>
                        <input type="text" name="supplier_icode" value='{{ $inventory_data->supplier_icode }}' id="supplier_icode" autocomplete="off" placeholder="Enter Supplier I Code" maxlength="50">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Product Name</label>
                        <input type="text" name="name" value='{{ $inventory_data->name }}' id="name" placeholder="Enter Product Name">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Inventory Group</label>
                        <select class="commoSelect" name="inventory_group_id" id="inventory_group_id">
                            @foreach ($inventory_groups as $inventory_group)
                            <option <?php if ($inventory_group->id == $inventory_data->inventory_group_id) { echo "selected";} ?> value='{{ $inventory_group->id }}'>{{ $inventory_group->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Inventory Category</label>
                        <input type="text" id="txtcategoryname" value="{{$inventory_data->category_name}}" name="txtcategoryname" placeholder="Select Inventory Category" readonly="true">
                        <input type="hidden" id="inventory_category_id" name="inventory_category_id" value="{{$inventory_data->inventory_category_id}}">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow" >
                <div class="custCol-4">
                    <div class="inputHolder checkHolder">
                        <label>Track manufacturing ? </label>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="trackmanufacturing" <?php if($inventory_data->track_manufacturing==1){ echo "checked";}?> value="1" type="radio">
                                <span></span>
                                <em>Yes</em>
                            </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="trackmanufacturing" <?php if($inventory_data->track_manufacturing==2){ echo "checked";}?> value="2" type="radio" >
                                <span></span>
                                <em>No</em>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder checkHolder">
                        <label>Track expiry ? </label>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="trackexpiry" <?php if($inventory_data->track_expiry==1){ echo "checked";}?> value="1" type="radio">
                                <span></span>
                                <em>Yes</em>
                            </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="trackexpiry" <?php if($inventory_data->track_expiry==2){ echo "checked";}?> value="2" type="radio"  >
                                <span></span>
                                <em>No</em>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <div class="custRow">

                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Primary Unit</label>
                        <input type="hidden" name="puid" id="puid" value='{{ $inventory_data->primary_unit }}'>                       
                        <select class="commoSelect" name="cmbprimaryunit" id="cmbprimaryunit" disabled>
                            <option value=''>Select Unit</option>
                            @foreach ($units as $unit)
                            <option <?php if ($unit->id == $inventory_data->primary_unit) { echo "selected";} ?> value='{{ $unit->id }}'>{{ $unit->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
              
            </div>
            
            <div class="altunitstitle" style="margin-bottom: 8px;">
               <label> <b>Alternate Units</b></label>
            </div>
            
             <div class="custRow alternateunits">
             <div class="privilegeCont units">
             <div class="custRow">
                
                 <?php
                    $strHtml = '';
                    foreach ($alternate_units as $units) {
                       $strHtml.= '<div class="custCol-3"><div class="commonCheckHolder"><label>
                               <input type="checkbox" name="selected_units[]" ';
                                if(in_array($units->unit_id, $selectedUnits))
                                {
                                   $strHtml.= "checked disabled"; 
                                }   

                                $strHtml.= ' class="selectUnits" id="'. $units->unit_id .'" value="'. $units->unit_name .'"><span></span><em>'.$units->unit_name.'</em></label></div></div>';
                    }
                    echo $strHtml;
                 ?>
            </div>
            </div>
            </div>
            
            <div class="listHolderType1 altunitslist" style="margin-top: 20px;">
                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Alternate unit</td>
                                <td>Conversion Value</td>
                                <td>Remove</td>
                            </tr>
                        </thead>
                        <tbody id="selectedunitslist">
                            
                           
                        </tbody>
                    </table>

                </div>					
            </div>
            
            <div class="custRow">
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description"  id="description" placeholder="Enter Description">{{ $inventory_data->description }}</textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                     <div class="inputHolder bgSelect">
                         <label>Barcode</label>
                         <select class="barcodeSelect" name="barcode" id="barcode">
                             <?php if(count($inventory_barcode) > 0) {?>
                             <option value='{{ $inventory_barcode->barcode_id }}'>{{ $inventory_barcode->barcode_string}}</option>
                             <?php } else { ?>
                             <option value='-1'>Select Barcode</option>
                             <?php } ?>
                             @foreach ($barcodes as $barcode)
                             <option value='{{ $barcode->id }}'>{{ $barcode->barcode_string }}</option>
                             @endforeach
                         </select>
                         <span class="commonError"></span>
                     </div>
                </div>

            </div>
            <div class="commonModalHolder" style="display: none;">
                <div class="modalContent folderCategory">
                    <div class="modalTop">
                        <a href="#" class="btnModalClose">Close (X)</a>
                        <h2>Inventory Category</h2>
                    </div>
                    <nav class="innerNavV1 folderOptn">
                        <ul>
                            @foreach ($parentcategory as $parent)
                            <li ><span>+</span><a id="{{$parent->id}}">{{ $parent->name }}</a>
                                <ul>
                                @foreach ($childlevel1 as $child)
                                    @if($parent->id==$child->parentid)
                                    <li>
                                        <span class="clschild" usr-id="{{$child->id}}" usr-count="{{$child->childcount}}">+</span>
                                        <a id="{{$child->id}}">{{$child->name}}</a>
                                        <ul id="ul-{{$child->id}}"></ul>
                                    </li>
                                    @endif
                                @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder selectV1">
                        <label>Upload Product Image</label>
                        <input type="file" onchange="readURL(this);" id="productpic" class="productpic" name="productpic" accept=".img, .png,.jpeg,.jpg"/>      
                        <span class="commonError"></span><br>
                        <img id="profile_previewpic" src="{{$inventory_data->pic_url}}">
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn">
                </div>
            </div>
        </div>
    </form>

</div>
<div class="commonLoaderV1"></div>
<script>

    
   var arrAltUnitList = <?php echo json_encode($inventory_selectedUnits); ?>;
   var unedited_unitlist = <?php echo json_encode($inventory_selectedUnits); ?>;
   $(document).ready(function ()
    {
        
        
        $("#frmeditinventory").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                company_id:
                        {
                            required: {
                                depends: function () {
                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                        },
                supplier_icode:{
                    remote:
                                    {
                                        url: "../checksuppliericode",
                                        type: "post",
                                        data:
                                                {
                                                    s_code: function () {
                                                        return $.trim($("#supplier_icode").val());
                                                    },
                                                    cid: function () { return $("#cid").val();}
                                                },
                                        dataFilter: function (data)
                                        {
                                            var json = JSON.parse(data);
                                            if (json.msg == "true") {
                                                //return "\"" + "That Company name is taken" + "\"";

                                                $('.scode').addClass('ajaxLoaderV1');
                                                $('.scode').removeClass('validV1');
                                                $('.scode').addClass('errorV1');
                                            }
                                            else
                                            {
                                                $('.scode').addClass('ajaxLoaderV1');
                                                $('.scode').removeClass('errorV1');
                                                $('.scode').addClass('validV1');
                                                return true;
                                            }
                                        }
                                    }
                },
                p_code:
                        {
                            required: {
                                depends: function () {
                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                            remote:
                                {
                                    url: "../checkproductcode",
                                    type: "post",
                                    data:
                                            {
                                                p_code: function () { return $.trim($("#p_code").val());},
                                                cid: function () { return $("#cid").val();}
                                            },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                            //return "\"" + "That Company name is taken" + "\"";

                                            $('.pcode').addClass('ajaxLoaderV1');
                                            $('.pcode').removeClass('validV1');
                                            $('.pcode').addClass('errorV1');
                                        }
                                        else
                                        {
                                            $('.pcode').addClass('ajaxLoaderV1');
                                            $('.pcode').removeClass('errorV1');
                                            $('.pcode').addClass('validV1');
                                            return true;
                                        }
                                    }
                                }
                        },
                name:
                        {
                            required: {
                                depends: function () {
                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                        },
                inventory_group_id:
                        {
                            required: true,
                        },
                txtcategoryname:
                        {
                            required: true,
                        },
//                minQty:
//                        {
//                            required: true,
//                            number: true
//                        },
//                maxQty:
//                        {
//                            required: true,
//                            number: true
//                        },
//                txtprice:
//                        {
//                            required: true,
//                            number: true
//                        },
//                cmbwarehouse:
//                        {
//                            required: true,
//                        },
                cmbprimaryunit:
                        {
                            required: true,
                        },
                
            },
 
            messages: {
                company_id: "Choose a company",
                p_code: "Enter Product Code",
                name: "Enter Product Name",
                inventory_group_id: "Select Inventory Group",
                txtcategoryname: "Select Inventory Category",
//                minQty:
//                        {
//                            required: "Enter Minimum Quantity",
//                            number: "Enter number only"
//                        },
//                maxQty:
//                        {
//                            required: "Enter Maximum Quantity",
//                            number: "Enter number only"
//                        },
//                txtprice:
//                        {
//                            required: "Enter Price",
//                            number: "Enter number only"
//                        },
                cmbprimaryunit: "Select Unit",
//                cmbwarehouse: "Select Warehouse",
                
            }
        });
        
        //    Dec 8 - 2018 -- Bergin
        
        $('#cmbprimaryunit').on("change", function (event) {
            event.preventDefault(); 
            return false;
//            arrAltUnitList = [];
//            showselectedunits();
//            var cmbprimaryunit = $(this).val();
//            var primaryunit = $("#puid").val();
//            var cid = $("#cid").val();
//            if (cmbprimaryunit != '')
//            {
//                $.ajax({
//                    type: 'POST',
//                    url: '../getalternateunits',
//                    data: '&unitsid=' + cmbprimaryunit + '&primaryunit=' + primaryunit + '&cid=' + cid,
//                    success: function (return_data) {
//
//                        $('.alternateunits').html(return_data);
//                        $('.alternateunits').show();
//                    }
//                });
//            }
//            else
//            {
//                 $('.alternateunits').hide();
//            }

        });
        
    });
    
    $('.number').keypress(function(event) {

        if(event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.which == 0) {
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
       

    
    $('body').on('click', '#chkisconsumable', function () {
        
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
       
    });
    
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#profile_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('.clschild').click(function(){
       var parentid=$(this).attr('usr-id');
       var count=$(this).attr('usr-count');
       
       if(count>0 && $("#ul-"+parentid+" li").length==0){
            $.ajax({
            type: 'POST',
            url: '../getchilds',
            data: '&parentid=' + parentid,
            success: function (return_data) {

                $("#ul-"+parentid).html(return_data);
                
            }
        });
       }
       
    });    
    
    $("#txtcategoryname").click(function(){
        $(".commonModalHolder").show();
    });    
    
    $(".btnModalClose").click(function(){
        $(".commonModalHolder").hide();
    });    
    function addtolist(unitid) {
        $(".commonLoaderV1").show();
        

        var arraData = {
            unit_id: unitid,
            unit_name: $("#" + unitid).val(),
            conversion_value: '',
        }

        
            arrAltUnitList.push(arraData);
            showselectedunits();
        
    }

    function remove(unit_id) {
        for (var i = 0; i < arrAltUnitList.length; i++) {
            if (unit_id == arrAltUnitList[i].unit_id) {
                arrAltUnitList.splice(i, 1);
                $('#' + unit_id).attr('checked', false);
            }
        }
        showselectedunits();
    }

    function showselectedunits()
    {
        $("#selectedunitslist").html('<tr><td>No Units Selected<td></tr>');
        var primary_unit = $('#cmbprimaryunit :selected').text();
        if (arrAltUnitList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrAltUnitList.length; i++) {
                try{
                    if((unedited_unitlist[i].unit_id > 0) && (arrAltUnitList[i].unit_id == unedited_unitlist[i].unit_id)){
                        var remove_tag = '';
                        var is_editable = 'disabled';
                    }
                    else{
                        var remove_tag = '<a href="javascript:remove(' + arrAltUnitList[i].unit_id + ')">Remove</a>';
                        var is_editable = '';
                    }
                }
               catch(e){
                    var remove_tag = '<a href="javascript:remove(' + arrAltUnitList[i].unit_id + ')">Remove</a>';
                    var is_editable = '';
                }
                strHtml += '<tr><td> 1 '+ arrAltUnitList[i].unit_name +' equals </td><td><input type="text" class="numberwithdot conversionnumber" autocomplete="off" value="'+arrAltUnitList[i].conversion_value +'" id="txt_'+ arrAltUnitList[i].unit_id +'"'+ is_editable +'> '+ primary_unit +'</td>\n\
                            <td>'+ remove_tag + '</td></tr>';
            }
            $("#selectedunitslist").html(strHtml);
        }
    }

    $('body').on('click', '.selectUnits', function () {
        $('.altunitslist').show();
        if ($(this).is(":checked")) {
            if(arrAltUnitList.length<3){
                addtolist($(this).attr('id'));
            }else{
                alert("Only 3 alternate units are allowed");
                $(this).prop("checked",false);
            }
        }
        else {
            remove($(this).attr('id'));
        }

        $(".commonLoaderV1").hide();
    });

    
    $('#frmeditinventory').submit(function (e) {
        
        if(!$('#frmeditinventory').valid()){
            return false;
        }
        
        var intZeroValue=0;
        if (arrAltUnitList.length > 0) {

            for (var i = 0; i < arrAltUnitList.length; i++) {
                if($("#txt_"+arrAltUnitList[i].unit_id).val()!=''){
                    $("#txt_"+arrAltUnitList[i].unit_id).removeClass("notentered");
                    arrAltUnitList[i].conversion_value=parseFloat($("#txt_"+arrAltUnitList[i].unit_id).val());
                    
                    if(parseFloat($("#txt_"+arrAltUnitList[i].unit_id).val())==0){
                        intZeroValue=1;
                        $("#txt_"+arrAltUnitList[i].unit_id).addClass("notentered");
                    }
                    
                }else{
                    $("#txt_"+arrAltUnitList[i].unit_id).addClass("notentered");
                }

            }
            var arrunits = JSON.stringify(arrAltUnitList);
            $("#altunitdetails").val(arrunits);

        } else {
            var arrunits = [];
            arrunits = JSON.stringify(arrunits);
            $("#altunitdetails").val(arrunits);
        }
        
        if(intZeroValue==1){
            alert("Conversion value can not be zero");
            return false;
        }
        
        if($('.notentered').length!=0){
            return false;
        }
        
        var blnConfirm=confirm("Are you sure to submit?");
        if(!blnConfirm){
            return false;
        }
        
         $("#btnUpdate").attr("disabled","disabled");
        
    });
    
    $('body').on('blur', '.conversionnumber', function () {
       for (var i = 0; i < arrAltUnitList.length; i++) {
            if (this.id == 'txt_'+arrAltUnitList[i].unit_id) {
                arrAltUnitList[i].conversion_value=$("#txt_"+arrAltUnitList[i].unit_id).val();
            }
        }

    });

    showselectedunits();
    

</script>
@endsection