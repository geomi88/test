@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function()
    {
        $( "#companyinsertion" ).validate({
    
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
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            },
                            remote: 
                                {
                                    url: "../companies/checkcompanies",
                                    type: "post",
                                    data: 
                                        {
                                    name: function() {
                                    return $( "#name" ).val();
                                    }
                                         },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                        //return "\"" + "That Company name is taken" + "\"";

                                        $('.companyName').addClass('ajaxLoaderV1');
                                        $('.companyName').removeClass('validV1');
                                        $('.companyName').addClass('errorV1');
                                       // valid="false"
                                        } 
                                        else 
                                        {
                                        $('.companyName').addClass('ajaxLoaderV1');
                                        $('.companyName').removeClass('errorV1');
                                        $('.companyName').addClass('validV1');
                                        //valid="true";
                                       return true;
                                        }
                                    }
                                }

                        }  

                        },
                         submitHandler: function(form) {  form.submit(); },
                        messages: {
                        name: "Enter Company name",
                         }
                    });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Company</span></h1>
    </header>	

    <form action="{{ action('Masterresources\CompaniesController@store') }}" method="post" id="companyinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder companyName ">
                        <label>Company Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter Company Name" onpaste="return false;" autocomplete="off">
                                <span class="commonError"></span>
                        <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" placeholder="Enter Alias" id="alias_name" >
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