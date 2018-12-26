@extends('layouts.main')
@section('content')
<?php //print_r($employees); die(); ?>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add CRM <span>Customer</span></h1>
    </header>

    <form action="{{ url('crm/enter_data/store') }}" method="post" id="frmwarehousealloc">
        <input type="hidden" name="cust_id" id="cust_id" value="">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder pcode">
                        <label>Phone Number</label>
                        <input type="tel" name="ph_no" id="ph_no" onkeypress="return isNumberKey(event)" autocomplete="off" placeholder="Enter Phone Number" maxlength="10" class="keyboard">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" id="customer_name" autocomplete="off" placeholder="Enter Name" class="keyboard" maxlength="200">
                        <span class="commonError"></span>
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
                ph_no:
                        {
                            required: {
                                depends: function () {
                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                            number: true,
                            minlength:10,
                            maxlength:10,
                        },
            },
            messages: {
                customer_name: "Enter Customer Name",
                ph_no:{
                   required : "Enter Phone Number",
                   remote: "Phone No Already exists",
               },
            },
        });
    });
    
    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
    
    $('#frmwarehousealloc').submit(function (e) {
        if (!$('#frmwarehousealloc').valid()) { //alert('Not valid');
            return false;
        }else{  
            $(".commonLoaderV1").show();
            return true;
        }
    });
    $('#ph_no').keyboard();
    $('#customer_name').keyboard();
    
    $(document).on('click','.popover .jqbtk-row button',function(event){
        if($("#ph_no").val().length > 10){
            $("#ph_no").val($("#ph_no").val().slice(0,-1));
            $('#frmwarehousealloc').valid();
        }
    });
 $('.popover-content').hide();
    $('.keyboard').on('click',function(){
        if ($(this).attr('type')=='tel'){
             $('.popover-content').show();
                    $('.popover').removeClass("textKeys");
        }else if($(this).attr('type')=='text'){
            $('.popover-content').show();
                    $('.popover').addClass("textKeys");
                    //$('.popover-content').css({width:'100%'});
        }
    });
</script>
@endsection