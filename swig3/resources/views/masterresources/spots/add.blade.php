@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function()
    {       
        $( "#spotinsertion" ).validate({
                    errorElement: "span",
                    errorClass: "commonError",
                    highlight: function(element, errorClass){
                                $(element).addClass('valErrorV1');
                            },
                            unhighlight: function(element, errorClass, validClass){
                                $(element).removeClass("valErrorV1");
                            },

                    rules: {

                    spot_name:{
                            required: {
                                depends: function () { if ($.trim($(this).val()) == '') { $(this).val($.trim($(this).val())); return true;} }
                            },
                            remote: 
                                {
                                    url: "../spot/checkspots",
                                    type: "post",
                                    data: 
                                        {
                                    spot_name: function() {
                                    return $.trim($( "#spot_name" ).val());
                                    }
                                         },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                        //return "\"" + "That Company name is taken" + "\"";
                                        $('.spotName').addClass('ajaxLoaderV1');
                                        $('.spotName').removeClass('validV1');
                                        $('.spotName').addClass('errorV1');
                                       // valid="false"
                                        } 
                                        else 
                                        {
                                        $('.spotName').addClass('ajaxLoaderV1');
                                        $('.spotName').removeClass('errorV1');
                                        $('.spotName').addClass('validV1');
                                        //valid="true";
                                       return true;
                                        }
                                    }
                                }

                        },
                         warehouse_id: {
                                        required: true
                                    }

                        },
                        
                        messages: {
                        spot_name: "Enter Spot Name",
                        warehouse_id:"Please Select Warehouse",
                         }
                    });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Spot</span></h1>
    </header>	

    <form action="{{ action('Masterresources\SpotController@store') }}" method="post" id="spotinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder spotName">
                        <label>Name</label>
                       <input type="text" name="spot_name" id="spot_name" onpaste="return false;" autocomplete="off" placeholder="Enter Name">
                                <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name">
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Choose Warehouse</label>
                        <select class="commoSelect" name="warehouse_id" id="warehouse_id">
                            <option value="">Select Warehouse</option>
                            @foreach ($warehouses as $warehouse)
-                           <option value='{{ $warehouse->id }}'>{{ $warehouse->name}}</option>
-                           @endforeach
                        </select>
                    </div>
                </div>
            </div>         

        </div>
            <input type="submit" value="Create" class="commonBtn bgGreen addBtn" name="submit">
    </form>	
</div>

@endsection
