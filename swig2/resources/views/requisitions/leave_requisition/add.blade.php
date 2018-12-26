@extends('layouts.main')
@section('content')
<div class="innerContent">

    <div class="statusMessage">
        <span class="errorMsg" ></span>
<!--<span class="successMsg" ></span>-->
    </div>
    <div class="customClear"></div>
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Create Leave Requisition</h1>
    </header>
    <div class=" inputAreaWrapper">
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
                <input type="hidden"  name="pending" id="pending" >  
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Requisition Title</label>
                        <input type="text" id="title" name="title" autocomplete="off" placeholder="Enter Title" maxlength="250">
                        <input type="hidden" value="{{$requisitioncode}}" id="requisition_code" name="requisition_code">
                        <span class="commonError"></span>
                    </div>


                </div>

                <!--                            <div class="custCol-4">
                                                <div class="inputHolder  ">
                                                    <label>Requisition Title</label>
                                                    <input type="text" placeholder="Enter Title" value="">
                                                    <span class="commonError"></span>
                                                </div>
                                            </div>-->
                <div class="customClear"></div>
                <div class="custCol-4">

                    <div class="inputHolder leaderInfo  custCol-6" style="width:46%;" >


                        <input type="text" id="created_at_from" name="created_at_from" value="" placeholder="From ">
                        <span class="commonError"></span>


                    </div>
                    <div class="inputHolder leaderInfo  custCol-6" style="width:46%;" > 

                        <input type="text" id="created_at_to" name="created_at_to" value="" placeholder="To ">
                        <span class="commonError"></span>



                    </div>
                </div>
                <div class="customClear"></div>
                <div class="custRow clsRadioAlignLeave" style="margin-top: 10px;">
                    <label>Leave Type</label>
                    <div class="custCol-3">
                        <input type="radio" id="full_day" name="leave_length" checked value="0"> 
                        <label>Full Day</label>
                    </div>
                    <div class="custCol-3">
                        <input type="radio" id="half_day" name="leave_length" value="1" >
                        <label>Half Day</label>
                    </div>
                    
                    <div class="custCol-3">
                        <input type="radio" id="annual_vacation" name="leave_length" value="2" >
                        <label> Annual Vacation </label>
                    </div>
                </div>
                <div class="custRow clsRadioAlignLeave" style="margin-bottom: 10px;padding-top: 10px;">
                    <div class="custCol-3">
                        <input type="radio" id="sick_leave" name="leave_length" value="3" >
                        <label> Sick Leave </label>
                    </div>

                    <div class="custCol-3">
                        <input type="radio" id="maternity_leave" name="leave_length" value="4" >
                        <label>Maternity leave</label>
                    </div>

                    <div class="custCol-3">
                        <input type="radio" id="emergency_leave" name="leave_length" value="5" >
                        <label>Emergency Leave</label>
                    </div>

                    <div class="custCol-3">
                        <input type="radio" id="business_leave" name="leave_length" value="6" >
                        <label>Business Leave</label>
                    </div>

                    <span class="commonError"></span>


                </div>

                <div class="customClear"></div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Reason</label>
                        <textarea id="description" name="description" placeholder="Enter Reason"></textarea>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="customClear"></div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Attach Document</label>
                        <input type="file" name="attach_document" class="reqdocument" id="attach_document" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-12">
                    <div class="inputBtnHolder">
                        <input type="button" id="btnSaveRequisition" class="btnIcon  lightGreenV3" value="Submit">
                    </div>
                </div>
            </div>
        </form>
    </div>



    <div class="commonLoaderV1"></div>
</div>


<div class="customClear "></div>

<script>
    var documents = [];
    $('.statusMessage').hide();


    $("#btnSaveRequisition").click(function () {

        if (!$("#frmaddrequisition").valid()) {
            return;
        }

        var blnConfirm = confirm("Are you sure to submit");
        if (!blnConfirm) {
            return;
        }



        var arraData = {
            requisition_code: $('#requisition_code').val(),
            title: $('#title').val(),
            description: $('#description').val(),
            leave_from: $("#created_at_from").val(),
            leave_to: $("#created_at_to").val(),
            leave_length: $("input[name='leave_length']:checked").val(),
        }

//        var arrData = encodeURIComponent(JSON.stringify(arraData));
        var arrProductsList = "";
        var arrData = JSON.stringify(arraData);
        var documents = new FormData($('#frmaddrequisition')[0]);
        documents.append('arrData', arrData);
        $('.commonLoaderV1').show();
        $('#btnSaveRequisition').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: 'store',
            data: documents,
            contentType: false,
            processData: false,
            success: function (return_data) {
                $('.commonLoaderV1').hide();
                window.location.href = '{{url("requisitions/outbox")}}';
            }
        });

        $('#btnSaveRequisition').removeAttr('disabled');

    });


    $(function () {
        $("#created_at_from").datepicker({
            changeMonth: true,
            changeYear: true,
            setDate: new Date(),
            minDate: new Date(),
            dateFormat: 'dd-mm-yy',
            onSelect: function (selected) {
                $("#created_at_to").datepicker("option", "minDate", selected)
                // search();
            }
        });

        $("#created_at_to").datepicker({
            changeMonth: true,
            minDate: new Date(),
            changeYear: true, dateFormat: 'dd-mm-yy'
        });


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
                created_at_from: {
                    required: true,
                },
                created_at_to: {
                    required: true,
                },
                description: {
                    required: true,
                },
            },
            messages: {
                title: "Enter Title",
                created_at_from: "<?php
$myHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo $myHTML;
?>Choose From date",
                created_at_to: "<?php
$myHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo $myHTML;
?>Choose To date",
                description: "Enter your reason",
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

    });


</script>
@endsection