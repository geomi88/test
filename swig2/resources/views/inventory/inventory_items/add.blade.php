@extends('layouts.main')
@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('inventory/inventory_items') }}">Back</a>
    <header class="pageTitle">
        <h1>Add <span>Inventory</span></h1>
    </header>

    <form action="{{ url('inventory/inventory_items/store') }}" method="post" id="frmaddinventory" enctype="multipart/form-data">
        <input type="hidden" id="altunitdetails" name="altunitdetails" value="">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Choose Company</label>
                        <select class="commoSelect" name="company_id" id="company_id">
                            <option value=''>Choose Company</option>
                            @foreach ($companies as $eachCompany)
                            <option value='{{ $eachCompany->id }}'>{{ $eachCompany->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder pcode">
                        <label>Product Code</label>
                        <input type="text" name="p_code" id="p_code" onpaste="return false;" autocomplete="off" placeholder="Enter Product Code" maxlength="50">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                
                <div class="custCol-4">
                    <div class="inputHolder scode">
                        <label>Supplier I Code</label>
                        <input type="text" name="supplier_icode" id="supplier_icode" autocomplete="off" placeholder="Enter Supplier I Code" maxlength="50">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Product Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter Product Name" maxlength="100">
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
                            <option value='{{ $inventory_group->id }}'>{{ $inventory_group->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Inventory Category</label>
                        <input type="text" id="txtcategoryname" name="txtcategoryname" placeholder="Select Inventory Category" readonly="true">
                        <input type="hidden" id="inventory_category_id" name="inventory_category_id">
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
                                <input  name="trackmanufacturing" checked value="1" type="radio">
                                <span></span>
                                <em>Yes</em>
                            </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="trackmanufacturing" value="2" type="radio" >
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
                                <input  name="trackexpiry" checked value="1" type="radio">
                                <span></span>
                                <em>Yes</em>
                            </label>
                        </div>
                        <div class="commonCheckHolder radioRender">
                            <label>
                                <input  name="trackexpiry" value="2" type="radio"  >
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
                        <select class="commoSelect" name="cmbprimaryunit" id="cmbprimaryunit">
                            <option value=''>Select Unit</option>
                            @foreach ($units as $unit)
                            <option value='{{ $unit->id }}'>{{ $unit->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>

               

            </div>

            <div class="altunitstitle" style="display: none;margin-bottom: 8px;">
                <label> <b>Alternate Units</b></label>
            </div>
            
            <div class="custRow alternateunits" style="margin-bottom: 20px;">
            </div>
            
            <div class="listHolderType1 altunitslist" style="display: none;">
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
                            <tr><td>No Units Selected</td></tr>
                        </tbody>
                    </table>

                </div>					
            </div>
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Barcode</label>
                        <select class="barcodeSelect" name="barcode" id="barcode">
                            <option value='-1'>Select Barcode</option>
                            @foreach ($barcodes as $barcode)
                            <option value='{{ $barcode->id }}'>{{ $barcode->barcode_string}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>

<!--                 <div class="custCol-3">
                    <div class="commonCheckHolder" style="margin-top: 42px;">
                        <label>
                            <input id="chkisconsumable" name="chkisconsumable" value="0" type="checkbox"><span></span>
                            <em>Is Consumable</em>
                        </label>
                    </div>
                </div>-->

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
                        <span class="commonError"></span>
                        <img id="profile_previewpic">
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" id="btnCreate" class="commonBtn bgGreen addBtn">
                </div>
            </div>
        </div>
    </form>

</div>
<div class="commonLoaderV1"></div>
<script>
    var arrAltUnitList = [];

    $(document).ready(function ()
    {
        $("#frmaddinventory").validate({
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
                                        url: "../inventory_items/checksuppliericode",
                                        type: "post",
                                        data:
                                                {
                                                    s_code: function () {
                                                        return $.trim($("#supplier_icode").val());
                                                    }
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
                                        url: "../inventory_items/checkproductcode",
                                        type: "post",
                                        data:
                                                {
                                                    p_code: function () {
                                                        return $.trim($("#p_code").val());
                                                    }
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

                cmbprimaryunit:
                        {
                            required: true,
                        },
            },
            messages: {
                p_code: "Enter Product Code",
                company_id: "Choose a company",
                name: "Enter Product Name",
                inventory_group_id: "Select Inventory Group",
                txtcategoryname: "Select Inventory Category",

                cmbprimaryunit: "Select Unit",
//                cmbwarehouse: "Select Warehouse",
            }
        });

        $('#cmbprimaryunit').on("change", function () {
            arrAltUnitList = [];
            showselectedunits();
            var cmbprimaryunit = $(this).val();
            if (cmbprimaryunit != '')
            {
                $.ajax({
                    type: 'POST',
                    url: '../inventory_items/getalternateunits',
                    data: 'unitsid=' + cmbprimaryunit,
                    success: function (return_data) {

                        $('.alternateunits').html(return_data);
                        $('.alternateunits').show();
                        $('.altunitstitle').show();
                    }
                });
            }
            else
            {
                $('.alternateunits').hide();
                $('.altunitstitle').hide();
                $('.altunitslist').hide();
            }

        });

    });
    
    $('body').on('click', '#chkisconsumable', function () {
        
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
       
    });
    
    $('.number').keypress(function (event) {

        if (event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.which == 0) {
            return true;
        } else if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }

        if ($(this).val() == parseFloat($(this).val()).toFixed(2))
        {
            event.preventDefault();
        }

        return true;
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

    $('.clschild').click(function () {
        var parentid = $(this).attr('usr-id');
        var count = $(this).attr('usr-count');


        if (count > 0 && $("#ul-" + parentid + " li").length == 0) {
            $.ajax({
                type: 'POST',
                url: '../inventory_items/getchilds',
                data: '&parentid=' + parentid,
                success: function (return_data) {

                    $("#ul-" + parentid).html(return_data);

                }
            });
        }

    });

    $("#txtcategoryname").click(function () {
        $(".commonModalHolder").show();
    });

    $(".btnModalClose").click(function () {
        $(".commonModalHolder").hide();
    });


    function addtolist(unitid) {
        $(".commonLoaderV1").show();
//        var intItemDuplicate = 0;
//        if (arrAltUnitList.length > 0) {
//            for (var i = 0; i < arrAltUnitList.length; i++) {
//                if (unitid == arrAltUnitList[i].emp_id) {
//                    intItemDuplicate = 1;
//                }
//            }
//        }

        var arraData = {
            unit_id: unitid,
            unit_name: $("#" + unitid).val(),
            conversion_value: '',
        }

        //if (intItemDuplicate != 1) {
            arrAltUnitList.push(arraData);
            showselectedunits();
        //}
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
                strHtml += '<tr><td> 1 '+arrAltUnitList[i].unit_name+' equals </td><td><input type="text" class="numberwithdot conversionnumber" autocomplete="off" value="'+arrAltUnitList[i].conversion_value +'" id="txt_'+ arrAltUnitList[i].unit_id +'"> '+ primary_unit +'</td>\n\
                            <td><a href="javascript:remove(' + arrAltUnitList[i].unit_id + ')">Remove</a></td></tr>';
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
            
        } else {
            remove($(this).attr('id'));
        }

        $(".commonLoaderV1").hide();
    })

    $('#frmaddinventory').submit(function (e) {
        
        if (!$('#frmaddinventory').valid()) {
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
        
        var blnConfirm=confirm("Please verify conversion values if any, It can not be changed once saved. Are you sure to submit?");
        if(!blnConfirm){
            return false;
        }
        
        $("#btnCreate").attr("disabled","disabled");
        
    });
    
    $('body').on('blur', '.conversionnumber', function () {
       for (var i = 0; i < arrAltUnitList.length; i++) {
            if (this.id == 'txt_'+arrAltUnitList[i].unit_id) {
                arrAltUnitList[i].conversion_value=$("#txt_"+arrAltUnitList[i].unit_id).val();
            }
        }

    });
    
</script>
@endsection