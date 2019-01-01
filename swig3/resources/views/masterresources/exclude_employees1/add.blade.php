@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function ()
    {
        $("#bankinsertion").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
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
                                        url: "../bank/checkbankname",
                                        type: "post",
                                        data:
                                                {
                                                    name: function () {
                                                        return $.trim($("#name").val());
                                                    }
                                                },
                                        dataFilter: function (data)
                                        {
                                            var json = JSON.parse(data);
                                            if (json.msg == "true") {
                                                //return "\"" + "That Company name is taken" + "\"";

                                                $('.areaName').addClass('ajaxLoaderV1');
                                                $('.areaName').removeClass('validV1');
                                                $('.areaName').addClass('errorV1');
                                                // valid="false"
                                            }
                                            else
                                            {
                                                $('.areaName').addClass('ajaxLoaderV1');
                                                $('.areaName').removeClass('errorV1');
                                                $('.areaName').addClass('validV1');
                                                //valid="true";
                                                return true;
                                            }
                                        }
                                    }

                        },
            },

            messages: {
                name: "Enter Bank Name",
            }
        });
    });
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Bank</span></h1>
    </header>	

    <form action="{{ action('Masterresources\BankController@store') }}" method="post" id="bankinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder areaName ">
                        <label>Bank Name</label>
                        <input type="text" name="name" id="name"  onpaste="return false;" autocomplete="off" placeholder="Enter Bank Name">
                        <span class="commonError"></span>
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
                    <input type="submit" value="Create" id="btnCreate" class="commonBtn bgGreen addBtn" id="sub">
                </div>
            </div>

        </div>
    </form>	
</div>

@endsection