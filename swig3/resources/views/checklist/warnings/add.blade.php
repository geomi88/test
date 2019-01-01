@extends('layouts.main')
@section('content')
<script>
$(document).ready(function()
{

    $( "#frmwarningsave" ).validate({
        errorElement: "span",
        errorClass: "commonError",
        highlight: function(element, errorClass){
                    $(element).addClass('valErrorV1');
                },
                unhighlight: function(element, errorClass, validClass){
                    $(element).removeClass("valErrorV1");
                },

        rules: {
                title: {required: {
                        depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                    }},  
                cmbbranch: {required: true},
                cmbemployee: {required: true},
                cmbwarning: {required: true},
            },
        messages: {
                title:"Enter Title",
                cmbbranch: "Select Branch",
                cmbemployee: "Select Employee",
                cmbwarning: "Select Warning Type",
            }
    });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Create <span>Warning</span></h1>
    </header>	

    <form action="{{ action('Checklist\WarningsController@store') }}" method="post" id="frmwarningsave">
        <div class="fieldGroup" id="fieldSet1">
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
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Employee</label>
                        <select  name="cmbemployee" id="cmbemployee" class="selectemployee">
                            <option selected value=''>Select Employee</option>
                           
                        </select>

                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Warning Type</label>
                        <select  name="cmbwarning" id="cmbwarning" class="selectwarning">
                            <option selected value=''>Select Warning Type</option>
                            @foreach ($warning_types as $warning_type)
                                <option value="{{$warning_type->id}}">{{$warning_type->name}}</option>
                            @endforeach
                        </select>

                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder shifName ">
                        <label>Title</label>
                        <input type="text" name="title" id="title" placeholder="Enter Title" autocomplete="off" maxlength="250">
                        <span class="commonError"></span>
                    </div>
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
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addinventorycat">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
  $('.selectbranch').on("change", function () {
     
    $.ajax({
         type: 'POST',
         url: 'getbranchemployees',
         data: {branch_id: $("#cmbbranch").val()},
         success: function (return_data) {
                $('.selectemployee').html(return_data);

         }
     });
});
</script>

@endsection
