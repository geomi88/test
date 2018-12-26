@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Company</span></h1>
    </header>	
@foreach ($companies as $company)
@endforeach
    <form action="{{ action('Masterresources\CompaniesController@update') }}" method="post" id="companyinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder companyName">
                        <label>Company Name</label>
                        <input type="hidden" id="cid" name="cid" value="{{ $company->id }}">
                       <input type="text" name="name" value="{{ $company->name }}" placeholder="Enter Company Name" id="name" onpaste="return false;" autocomplete="off">
                                <span class="commonError"></span>
                        <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" placeholder="Enter Alias" value="{{ $company->alias_name }}" name="alias_name" id="company_up_alias" placeholder="Enter Alias Name">
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn addcompany">
                </div>
            </div>

        </div>
    </form>	
</div>

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
                                    url: "../checkcompanies",
                                    type: "post",
                                    data:{
                                    name: function(){return $( "#name" ).val();},
                                    cid:  function(){return $( "#cid" ).val();}
                                    },          
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                        //return "\"" + "That Company name is taken" + "\"";

                                        $('.companyName').addClass('ajaxLoaderV1');
                                        $('.companyName').removeClass('validV1');
                                        $('.companyName').addClass('errorV1');
                                        } 
                                        else 
                                        {
                                        $('.companyName').addClass('ajaxLoaderV1');
                                        $('.companyName').removeClass('errorV1');
                                        $('.companyName').addClass('validV1');
                                        return 'true';
                                        }
                                    }
                                }

                        }  

                        },
                        messages: {
                        name: "Enter Company name",
                         }
                    });
});
</script>

@endsection
