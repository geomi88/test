@extends('layouts.main')
@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('checklist/checklist_entry')}}">Back</a>
    <form id="frmchkpoint">
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Branch</label>
                    <select  name="cmbbranch" id="cmbbranch" class="selectbranch">
                        <option selected value=''>Select Branch</option>
                        @foreach ($allbranches as $branch)
                        <option value="{{$branch->branch_id}}">{{$branch->code}} : {{$branch->branch_name}}</option>
                        @endforeach
                    </select>
                    
                    <span class="commonError"></span>
                </div>
            </div>
        </div>
    </form>
    <div class="divquestions">
        @include('checklist/checklist_entry/result')
    </div>
    
    <a class="commonBtn addBtn bgGreen" id="btnsavechkpoint">Submit</a>
    <div class="commonLoaderV1"></div>
</div>
<script>
    $(document).ready(function ()
    {
        
        $("#frmchkpoint").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                cmbbranch: {required: true},
            },
            
            messages: {
                cmbbranch: "Select Branch",
            }
        });
        
        $('#btnsavechkpoint').on('click', function () {
            
            if(!$("#frmchkpoint").valid()){
                return;
            }
            
            var arrPointList = [];
            $(".clschckpoints").each(function() {
                var intId=this.id;
                var optionname='rating_'+intId;
                
                if (!$('input[name='+optionname+']:checked').val() ) {  
                    $("#"+intId).addClass("clsNotSelected");
                }else{
                    $("#"+intId).removeClass("clsNotSelected");
                    var arraData = {
                        checkpoint: intId,
                        rating: $('input[name='+optionname+']:checked').val(),
                        comment: $("#txtcomment_"+intId).val(),
                    }

                    arrPointList.push(arraData);
                }

            });

            if ($('.clsNotSelected').length!=0) {
                return;
            }

            var arrQueries = encodeURIComponent(JSON.stringify(arrPointList));

            $('#btnsavechkpoint').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../../checklist_entry/store',
                data: '&arrPointList=' + arrQueries+'&cmbbranch=' + $("#cmbbranch").val()+'&maincategory=' + $("#maincategory").val(),
                success: function (return_data) {
                    $('#btnsavechkpoint').removeAttr('disabled');
                    window.location.href = '{{url("checklist/checklist_entry")}}';
                },
                error: function (return_data) {
                    $('#btnsavechkpoint').removeAttr('disabled');
                    window.location.href = '{{url("checklist/checklist_entry")}}';
                }
            });

            $('#btnsavechkpoint').removeAttr('disabled');
        })

    });

 $('.selectbranch').on("change", function () {
     
    $.ajax({
         type: 'POST',
         url: '../getbranchentry',
         data: {branch_id: $("#cmbbranch").val(),maincategory:$("#maincategory").val()},
         beforeSend: function () {
             $(".commonLoaderV1").show();
         },
         success: function (return_data) {
             if (return_data != '')
             {
                 $('.divquestions').html(return_data);
                 $(".commonLoaderV1").hide();
             }

         }
     });
});
    
</script>
@endsection
