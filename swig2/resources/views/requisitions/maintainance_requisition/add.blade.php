@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Create Maintenance Requisition</h1>
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
                    <input type="text" placeholder="Enter Title" value="" id="title" autocomplete="off" name="title" maxlength="200">
                    <span class="commonError"></span>
                </div>
                <div class="inputHolder  ">
                    <label>Select Center</label>
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input name="cost_center" id="branch" value="Branch" checked type="radio" class="costCenter">
                            <span></span>
                            <em>Branch</em>
                        </label>
                    </div>
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input name="cost_center" id="office" value="Office" type="radio" class="costCenter">
                            <span></span>
                            <em>Office</em>
                        </label>
                    </div>
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input name="cost_center" id="warehouse" value="Warehouse" type="radio" class="costCenter">
                            <span></span>
                            <em>Warehouse</em>
                        </label>
                    </div>
                    <div class="commonCheckHolder radioRender">
                        <label>
                            <input name="cost_center" id="staffhouse" value="Staff House" type="radio" class="costCenter">
                            <span></span>
                            <em>Staff House</em>
                        </label>
                    </div>
                </div>
                <div class="inputHolder clsparentdiv">
                    <label class="center_name">Select Branch</label>
                    <select id="centerSelect" name="centerSelect" class="centerSelect chosen-select">
                        <option value=''>Select an option</option>
                        @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" >{{$branch->name}}</option>
                        @endforeach
                    </select>
                    <span class="commonError"></span>
                </div>
                <div class="inputHolder  ">
                    <label>Amount </label>
                    <input type="text" placeholder="Enter Amount" onpaste="return false;" value="" id="amount" name="amount" class="numberwithdot" maxlength="15">
                    <span class="commonError"></span>
                </div>
                <div class="inputHolder  ">
                    <label>Description</label>
                    <textarea placeholder="Enter Description" id="description" name="description"></textarea>
                    <span class="commonError"></span>
                </div>
                
                
            </div>

        </div>
       </form>
        
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
            <div class="inputBtnHolder">
                <input type="button" id="btnSaveRequisition" class="btnIcon  lightGreenV3" value="Submit">
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
                centerSelect: {
                    required: true,
                },
                amount: {
                    required: true,
                },
            },
            messages: {
                title: "Enter Title",
                amount: "Enter Amount",
                centerSelect: "Select Center",
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
        
        $(".costCenter").click(function(){
           $('#centerSelect').html('');
           var center = $(this).val();
           $('.center_name').html('Select '+center);
           $.ajax({
                type: 'POST',
                url: 'get_cost_center',
                data:{center: center}, 
                success: function (return_data) {
                    var str ='<option value="">Select an option</option>';
                    for(var i=0;i<return_data.length;i++){
                        str += '<option value="'+ return_data[i]['id'] +'" >'+return_data[i]['name']+'</option>';
                    }
                    $('#centerSelect').html(str);
                    $('.centerSelect').trigger("chosen:updated");
                }
            });
        });
        
        $("#btnSaveRequisition").click(function () {
            if (!$("#frmaddrequisition").valid()) {
                return;
            } 
            var arraData = {
                requisition_code: $('#requisition_code').val(),
                title: $('#title').val(),
                centerSelect: $('#centerSelect').val(),
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
                    window.location.href = '{{url("requisitions/outbox")}}';
                }
            });
            
            $('#btnSaveRequisition').removeAttr('disabled');
            
        });
    });
</script>
@endsection