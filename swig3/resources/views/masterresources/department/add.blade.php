@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Department</span></h1>
    </header>	

    <form action="{{ action('Masterresources\DepartmentController@store') }}" method="post" id="departmentinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Department Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter Department Name">
                          <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name">
                        
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn adddepartment">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function()
{
     $( "#departmentinsertion" ).validate({
    
                    errorElement: "span",
                    errorClass: "commonError",
                    highlight: function(element, errorClass){
                                $(element).addClass('valErrorV1');
                            },
                            unhighlight: function(element, errorClass, validClass){
                                $(element).removeClass("valErrorV1");
                            },

                    rules: {

                    name: 
                        {
                            required: {
                                depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                            },
                        }  

                        },
                         submitHandler: function() {  form.submit(); },
                        messages: {
                        name: "Enter Department Name",
                         }
                    });
});
</script>
@endsection
