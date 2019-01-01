@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function ()
    {
        $("#checklistcatupdation").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                name:{
                        required: {
                           depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                       },
                   }
            },
            messages: {name:{required: "Enter Warning Type"}}
        });
    });
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Meeting Room</span></h1>
    </header>	

    <form action="{{ action('Masterresources\MeetingroomController@update') }}" method="post" id="checklistcatupdation">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder checklistcatName">
                        <label>Name</label>
                        <input type="hidden" name="cid" id="cid" value='{{ $categorie_datas->id }}'>                       
                        <input type="text" name="name" id="name" value='{{ $categorie_datas->name }}' autocomplete="off" placeholder="Enter Name" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" class="arabicalign" name="alias_name" value='{{ $categorie_datas->alias_name }}' id="alias_name" placeholder="Enter Alias" maxlength="250">
                    </div>
                </div>
                
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn addinventorycat">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    

</script>
@endsection
