@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Office</span></h1>
    </header>	
    @foreach ($offices as $office)
    @endforeach
    <form action="{{ action('Masterresources\OfficeController@update') }}" method="post" id="officeinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Office Name</label>
                        <input type="hidden" id="cid" name="cid" value="{{ $office->id }}">
                        <input type="text" name="name" id="name" value="{{ $office->name }}" placeholder="Enter Office Name">
                         <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" value="{{ $office->alias_name }}" placeholder="Enter Alias Name">
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn editdepartment">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function()
{

        $( "#officeinsertion" ).validate({
    
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
                        messages: {
                        name: "Enter Office Name",
                         }
                    });
});
</script>
@endsection
