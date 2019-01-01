@extends('layouts.main')
@section('content')
<?php //print_r($employees); die(); ?>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Warehouse <span>Allocation</span></h1>
    </header>

    <form action="{{ url('elegantclub/warehose_allocation/store') }}" method="post" id="frmwarehousealloc">
        <input type="hidden" name="ew_id" id="ew_id" value="">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder ">
                        <label>Choose Employee</label>
                        <select class="chosen-select" name="employee_id" id="employee_id">
                            <option value=''>Choose Employee</option>
                           @foreach($employees as $eachEmployee)
                            <option value='{{$eachEmployee->id}}'>{{$eachEmployee->username}}: {{$eachEmployee->first_name}}</option>
                           @endforeach 
                        </select>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder ">
                        <label>Choose Warehouse</label>
                        <select class="chosen-select" name="warehouse_id" id="warehouse_id">
                            <option value=''>Choose Warehouse</option>
                           @foreach($warehouses as $eachWarehouse)
                            <option value='{{$eachWarehouse->id}}'>{{$eachWarehouse->name}}</option>
                           @endforeach  
                        </select>
                    </div>
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
    
    $(document).ready(function ()
    { 
        var v = jQuery("#frmwarehousealloc").validate({ 
            errorElement: "span",
            errorClass: "commonError",
            ignore: '' ,
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
            rules: {
                employee_id:
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
                warehouse_id:
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
            },
            messages: {
                employee_id: "Choose an employee",
                warehouse_id: "Choose a warehouse",
            },
        });
    });
    
    $('#frmwarehousealloc').submit(function (e) {
        if (!$('#frmwarehousealloc').valid()) { //alert('Not valid');
            return false;
        }else{  
            $(".commonLoaderV1").show();
            return true;
        }
    });
    
    
</script>
@endsection