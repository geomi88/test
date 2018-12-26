@extends('layouts.main')
@section('content')
<script>
$(document).ready(function()
{

    $( "#frmwarningedit" ).validate({
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
            },
        messages: {
                title:"Enter Title",
                cmbbranch: "Select Branch",
                cmbemployee: "Select Employee",
            }
    });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Update <span>Warning</span></h1>
    </header>	

    <form action="{{ action('Checklist\WarningsController@update') }}" method="post" id="frmwarningedit">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Branch</label>
                        <input type="hidden" name="cid" id="cid" value='{{ $warnings_data->id }}'>                       
                        <select  name="cmbbranch" id="cmbbranch" class="selectbranch">
                            <option selected value=''>Select Branch</option>
                            @foreach ($allbranches as $branch)
                            <option value="{{$branch->branch_id}}" <?php if($warnings_data->branch_id==$branch->branch_id){echo "selected";}?>>{{$branch->code}} : {{$branch->branch_name}}</option>
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
                           @foreach ($employees as $employee)
                            <option value="{{$employee->emp_id}}" <?php if($warnings_data->employee_id==$employee->emp_id){echo "selected";}?>>{{$employee->type}} :: {{$employee->first_name}}</option>
                            @endforeach
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
                                <option value="{{$warning_type->id}}" <?php if($warnings_data->warning_type==$warning_type->id){echo "selected";}?>>{{$warning_type->name}}</option>
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
                        <input type="text" name="title" id="title" placeholder="Enter Title" autocomplete="off" maxlength="250" value="{{$warnings_data->title}}">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
             <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter Description">{{$warnings_data->description}}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn addinventorycat">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
  $('.selectbranch').on("change", function () {
     
    $.ajax({
         type: 'POST',
         url: '../getbranchemployees',
         data: {branch_id: $("#cmbbranch").val()},
         success: function (return_data) {
                $('.selectemployee').html(return_data);

         }
     });
});
</script>

@endsection
