@extends('layouts.main')
@section('content')


<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Warehouse</span></h1>
    </header>	

    <form action="{{ action('Masterresources\WarehouseController@store') }}" method="post" id="warehouseinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                
                <div class="custCol-4">
                    <div class="inputHolder warehouseName">
                        <label>Name</label>
                        <input type="text" name="warehouse_name" id="warehouse_name" onpaste="return false;" autocomplete="off" maxlength="100" placeholder="Enter Name">
                                <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name" maxlength="100">
                    </div>
                </div>
            </div>
            <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Choose Region</label>
                        <select class="commoSelect" name="regionselect" id="regionselect">
                            <option value="">Select Region</option>
                            @foreach ($regions as $region) 
                            <option value='{{ $region->id }}'>{{ $region->name}}</option>
                            @endforeach
                        </select>    
                    </div>
                </div>
            
            <div class="custCol-4">
                <div class="inputHolder bgSelect">
                    <label>Choose Manager</label>
                    <select class="commoSelect" name="warehousemgr" id="warehousemgr">
                        <option value="">Select Manager</option>
                        @foreach ($warehousemanagers as $mgr) 
                        <option value='{{ $mgr->id }}'>{{ $mgr->first_name}} {{ $mgr->alias_name}}</option>
                        @endforeach
                    </select>    
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addwarehouse">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function()
    {       
        $( "#warehouseinsertion" ).validate({
                    errorElement: "span",
                    errorClass: "commonError",
                    highlight: function(element, errorClass){
                                $(element).addClass('valErrorV1');
                            },
                            unhighlight: function(element, errorClass, validClass){
                                $(element).removeClass("valErrorV1");
                            },

                    rules: {

                    warehouse_name:{
                            required: {
                                depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                            },
                            remote: 
                                {
                                    url: "../warehouse/checkwarehouse",
                                    type: "post",
                                    data: 
                                        {
                                    warehouse_name: function() {
                                    return $.trim($("#warehouse_name" ).val());
                                    }
                                         },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                        //return "\"" + "That Company name is taken" + "\"";
                                        $('.warehouseName').addClass('ajaxLoaderV1');
                                        $('.warehouseName').removeClass('validV1');
                                        $('.warehouseName').addClass('errorV1');
                                       // valid="false"
                                        } 
                                        else 
                                        {
                                        $('.warehouseName').addClass('ajaxLoaderV1');
                                        $('.warehouseName').removeClass('errorV1');
                                        $('.warehouseName').addClass('validV1');
                                        //valid="true";
                                       return true;
                                        }
                                    }
                                }

                        } ,
                        regionselect:{required: true},
                        warehousemgr : {
                                required :true
                        }
                        
                        },
                       
                        messages: {
                        warehouse_name: "Enter Warehouse Name",
                        warehousemgr: "Select Manager",
                        regionselect: "Select Region",
                         }
                    });
});
</script>

@endsection
