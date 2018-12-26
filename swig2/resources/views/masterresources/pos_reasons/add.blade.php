@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function()
    {
        $( "#posinsertion" ).validate({
    
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
                            remote: 
                                {
                                    url: "../pos_reasons/checkpos",
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
                                        //return "\"" + "That Company name is taken" + "\"";

                                        $('.posName').addClass('ajaxLoaderV1');
                                        $('.posName').removeClass('validV1');
                                        $('.posName').addClass('errorV1');
                                       // valid="false"
                                        } 
                                        else 
                                        {
                                        $('.posName').addClass('ajaxLoaderV1');
                                        $('.posName').removeClass('errorV1');
                                        $('.posName').addClass('validV1');
                                        //valid="true";
                                       return true;
                                        }
                                    }
                                }

                        }  

                        },
                        
                        messages: {
                        name: "Enter POS Name",
                         }
                    });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>POS Reason</span></h1>
    </header>	

    <form action="{{ action('Masterresources\PosreasonController@store') }}" method="post" id="posinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder posName ">
                        <label>POS Name</label>
                        <input type="text" name="name" id="name"  onpaste="return false;" autocomplete="off" placeholder="Enter Name">
                                <span class="commonError"></span>
                        <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias">
                         <span class="commonError"></span>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addcompany" id="sub">
                </div>
            </div>

        </div>
    </form>	
</div>

@endsection