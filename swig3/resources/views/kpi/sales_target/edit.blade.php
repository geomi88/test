@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Update <span>Inventory</span></h1>
    </header>
    
    <form action="{{ action('Inventory\InventoryController@update') }}" method="post" id="frmeditinventory" enctype="multipart/form-data">

        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder pcode">
                        <label>Product Code</label>
                        <input type="hidden" name="cid" id="cid" value='{{ $inventory_data->id }}'>                       
                        <input type="text" name="p_code" value='{{ $inventory_data->product_code }}' id="p_code" onpaste="return false;" autocomplete="off">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Product Name</label>
                        <input type="text" name="name" value='{{ $inventory_data->name }}' id="name">
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
                        <select class="commoSelect" name="inventory_category_id" id="inventory_category_id">
                            <option value=''>Select Category</option>
                            @foreach ($inventory_categories as $inventory_category)
                            <option <?php if ($inventory_category->id == $inventory_data->inventory_category_id) { echo "selected";} ?> value='{{ $inventory_category->id }}'>{{ $inventory_category->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
             <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Minimum Branch Stock</label>
                        <input type="text" name="minQty" id="minQty" class="number" placeholder="Enter Minimum Branch Stock" value="{{ $inventory_data->min_branch_stock }}"  autocomplete="off" maxlength="15">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Maximum Branch Stock</label>
                        <input type="text" name="maxQty" class="number" placeholder="Enter Maximum Branch Stock" id="maxQty" value="{{ $inventory_data->max_branch_stock }}"  autocomplete="off" maxlength="15">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">

                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Warehouse</label>
                        <select class="commoSelect" name="cmbwarehouse" id="cmbwarehouse">
                            <option value=''>Select Warehouse</option>
                            @foreach ($warehouses as $warehouse)
                            <option <?php if ($warehouse->id == $inventory_data->warehouse_id) { echo "selected";} ?> value='{{ $warehouse->id }}'>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                 <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Price</label>
                        <input type="text" name="txtprice" class="number" placeholder="Enter Price" value="{{ $inventory_data->price }}" readonly="true" id="txtprice" autocomplete="off" maxlength="15">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>

            <div class="custRow">

                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Primary Unit</label>
                        <input type="hidden" name="puid" id="puid" value='{{ $inventory_data->primary_unit }}'>                       
                        <select class="commoSelect" name="cmbprimaryunit" id="cmbprimaryunit">
                            <option value=''>Select Unit</option>
                            @foreach ($units as $unit)
                            <option <?php if ($unit->id == $inventory_data->primary_unit) { echo "selected";} ?> value='{{ $unit->id }}'>{{ $unit->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
              
            </div>
            
            <div class="altunitstitle">
               <label> Alternate Units</label>
            </div>
            
             <div class="custRow alternateunits">
                
                 <?php
                    $strHtml = '';
                    foreach ($alternate_units as $units) {
                       $strHtml.= '<div class="custCol-3"><div class="commonCheckHolder"><label>
                               <input type="checkbox" name="selected_units[]" ';
                                if(in_array($units->unit_id, $selectedUnits))
                                {
                                   $strHtml.= "checked"; 
                                }   

                                $strHtml.= ' class="selectUnits" value="'. $units->unit_id .'"><span></span><em>'.$units->unit_name.'</em></label></div></div>';
                    }
                    echo $strHtml;
                 ?>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description"  id="description" placeholder="Enter Description">{{ $inventory_data->description }}</textarea>
                    </div>
                </div>
            </div>
            <div class="custCol-6">
                <div class="inputHolder selectV1">
                    <label>Upload Product Image</label>
                    <input type="file" onchange="readURL(this);" id="productpic" class="productpic" name="productpic" accept=".img, .png,.jpeg,.jpg"/>      
                    <span class="commonError"></span>
                    <img id="profile_previewpic">
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn">
                </div>
            </div>
        </div>
    </form>

</div>
<div class="commonLoaderV1"></div>
<script>

   
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
                inventory_category_id:
                        {
                            required: true,
                        },
                minQty:
                        {
                            required: true,
                            number: true
                        },
                maxQty:
                        {
                            required: true,
                            number: true
                        },
                txtprice:
                        {
                            required: true,
                            number: true
                        },
                cmbwarehouse:
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
                name: "Enter Product Name",
                inventory_group_id: "Select Inventory Group",
                inventory_category_id: "Select Inventory Category",
                minQty:
                        {
                            required: "Enter Minimum Quantity",
                            number: "Enter number only"
                        },
                maxQty:
                        {
                            required: "Enter Maximum Quantity",
                            number: "Enter number only"
                        },
                txtprice:
                        {
                            required: "Enter Price",
                            number: "Enter number only"
                        },
                cmbprimaryunit: "Select Unit",
                cmbwarehouse: "Select Warehouse",
                
            }
        });
        
        $('#cmbprimaryunit').on("change", function () {
            var cmbprimaryunit = $(this).val();
            var primaryunit = $("#puid").val();
            var cid = $("#cid").val();
            if (cmbprimaryunit != '')
            {
                $.ajax({
                    type: 'POST',
                    url: '../getalternateunits',
                    data: '&unitsid=' + cmbprimaryunit + '&primaryunit=' + primaryunit + '&cid=' + cid,
                    success: function (return_data) {

                        $('.alternateunits').html(return_data);
                        $('.alternateunits').show();
                    }
                });
            }
            else
            {
                 $('.alternateunits').hide();
            }

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
       
    $('#frmeditinventory').submit(function(e) {
        if(!$('#frmeditinventory').valid()){
            return false;
        }
        
       if($('input:checkbox.selectUnits:checked').length==0){
            alert("Please Select Atleast One Alternate Unit");
            e.preventDefault();
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

</script>
@endsection