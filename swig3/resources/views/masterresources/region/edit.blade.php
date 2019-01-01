@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Region</span></h1>
    </header>	
    @foreach ($regions as $region)
    @endforeach
    <form action="{{ action('Masterresources\RegionController@update') }}" method="post" id="regioninsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">                           
                <div class="custCol-4">
                    <div class="inputHolder regionName">
                        <label>Region Name</label>
                        <input type="hidden" name="cid" id="cid" placeholder="Enter Name" value="{{ $region->id}}">  
                        <input type="text" name="name" id="name" placeholder="Enter Region Name" value="{{ $region->name}}">
                          <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name" value="{{ $region->alias_name}}">
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn addregion">
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    $(document).ready(function()
{
     $( "#regioninsertion" ).validate({
    
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
                                depends: function () {

                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                            remote:
                                {
                                    url: "../checkregionname",
                                    type: "post",
                                    data:
                                            {
                                                name: function () {
                                                    return $.trim($("#name").val());
                                                },
                                                cid: function () {
                                                    return $.trim($("#cid").val());
                                                }
                                            },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                            //return "\"" + "That Company name is taken" + "\"";

                                            $('.regionName').addClass('ajaxLoaderV1');
                                            $('.regionName').removeClass('validV1');
                                            $('.regionName').addClass('errorV1');
                                            return false;
                                        }
                                        else
                                        {
                                            $('.regionName').addClass('ajaxLoaderV1');
                                            $('.regionName').removeClass('errorV1');
                                            $('.regionName').addClass('validV1');
                                            return true;
                                        }
                                    }
                                }

                        },
                        },
                        submitHandler: function(form) {  form.submit(); },
                        messages: {
                        name: {required: "Enter Region Name", remote: "Region Name Already Exists"}
                         }
                    });
});
</script>
@endsection
