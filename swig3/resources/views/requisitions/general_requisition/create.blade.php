@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Create General Requisition</h1>
    </header>
    <div class=" inputAreaWrapper">
        <form id="frmaddrequisition" method="post">
        <div class="custRow reqCodeDateHolder">
            <div class="custCol-6">
                <label>Requisition Date : <span><?php echo date('d-m-Y'); ?></span></label>

            </div>
            <div class="custCol-6 alignRight">
                <label>Requisition Code : <span>{{$requisitioncode}}</span></label>  
                <input type="hidden" value="{{$requisitioncode}}" id="requisition_code" name="requisition_code">
            </div>
        </div>
        <div class="custRow ">
            <div class="custCol-5">
                <div class="inputHolder  ">
                    <label>Requisition Title</label>
                    <input type="text" placeholder="Enter Title" id="title" name="title" value="" maxlength="200" autocomplete="off">
                    <span class="commonError"></span>
                </div>
                <div class="inputHolder  ">
                    <label>Description</label>
                    <textarea placeholder="Enter Description" id="description" name="description"></textarea>
                    <span class="commonError"></span>
                </div>
                <div class="inputHolder  ">
                    <label>Amount </label>
                    <input type="text" placeholder="Enter Amount" onpaste="return false;" id="amount" name="amount" class="numberwithdot" maxlength="15" value="" autocomplete="off">
                    <span class="commonError"></span>
                </div>
                
            </div>
        </div>
            
        </form>
        <div class="customClear"></div>
        <form id="frmdocs" name="frmdocs" method="post" enctype="multipart/form-data">
            <div class="custRow" >
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Upload Document</label>
                        <input type="file" name="req_doc" class="reqdocument" id="req_doc" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">
                        <span class="commonError"></span>
                    </div>
                </div>

            </div>
            <div class="custRow">
                <div class="inputBtnHolder">
                    <input type="button" id="btnSaveRequisition" class="btnIcon  lightGreenV3" value="Submit">
                </div>
            </div>
        </form> 
        
    </div>
</div>
    <div class="commonLoaderV1"></div>
<script>
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
                amount: {
                    required: true,
                },
            },
            messages: {
                title: "Enter Title",
                amount: "Enter Amount",
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
        $("#btnSaveRequisition").click(function () { 
            if (!$("#frmaddrequisition").valid()) {
                return;
            } 
            var arraData = {
                requisition_code: $('#requisition_code').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                price: amountformat($("#amount").val()),
            }
            var arrData = JSON.stringify(arraData);
            var documents = new FormData($('#frmdocs')[0]);
            documents.append('arrData', arrData);
            
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
            
            
        });
    });
</script>
@endsection