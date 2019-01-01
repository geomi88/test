@extends('layouts.main')
@section('content')
<script>
$(document).ready(function()
{

    $( "#frmchecklistsave" ).validate({
        errorElement: "span",
        errorClass: "commonError",
        highlight: function(element, errorClass){
                    $(element).addClass('valErrorV1');
                },
                unhighlight: function(element, errorClass, validClass){
                    $(element).removeClass("valErrorV1");
                },

        rules: {

            name: {
                    required: {
                        depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                    },
                    remote: 
                        {
                            url: "../check_list_category/checkcategories",
                            type: "post",
                            data: 
                                {
                            name: function() {
                                return $.trim($( "#name" ).val());
                                }
                            },
                            dataFilter: function (data)
                            {
                                var json = JSON.parse(data);
                                if (json.msg == "true") {
                                    $('.checklistcatName').addClass('ajaxLoaderV1');
                                    $('.checklistcatName').removeClass('validV1');
                                    $('.checklistcatName').addClass('errorV1');
                                    return false;
                                } 
                                else{
                                    $('.checklistcatName').addClass('ajaxLoaderV1');
                                    $('.checklistcatName').removeClass('errorV1');
                                    $('.checklistcatName').addClass('validV1');
                                    return true;
                                }
                            }
                        }
                }  
            },
            messages: {name:{required: "Enter Check list Category Name",remote: "Category Name Already Exists"}}
    });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Category</span></h1>
    </header>	

    <form action="{{ action('Checklist\ChecklistcategoryController@store') }}" method="post" id="frmchecklistsave">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder checklistcatName">
                        <label>Name</label>
                        <input type="text" name="name" id="name"  autocomplete="off" placeholder="Enter Name" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name" maxlength="250">
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
  
</script>

@endsection
