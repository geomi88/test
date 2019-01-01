@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />
<script>
$(document).ready(function ()
{
    var timepicker = $('#tin').timepicker({'timeFormat': 'h:i a', 'disableTextInput': true});
    $('#tout').timepicker({'timeFormat': 'h:i a', 'disableTextInput': true});

    $("#shiftinsertion").validate({
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
                        required: true,
                        remote:
                                {
                                    url: "../job_shifts/checkshifts",
                                    type: "post",
                                    data:
                                            {
                                                name: function () {
                                                    return $("#name").val();
                                                }
                                            },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                            //return "\"" + "That Company name is taken" + "\"";

                                            $('.shifName').addClass('ajaxLoaderV1');
                                            $('.shifName').removeClass('validV1');
                                            $('.shifName').addClass('errorV1');
                                            // valid="false"
                                        }
                                        else
                                        {
                                            $('.shifName').addClass('ajaxLoaderV1');
                                            $('.shifName').removeClass('errorV1');
                                            $('.shifName').addClass('validV1');
                                            //valid="true";
                                            return true;
                                        }
                                    }
                                }

                    },
            tin:
                    {
                        required: true,
                    },
            tout:
                    {
                        required: true,
                    }

        },
        submitHandler: function () {
            form.submit();
        },
        messages: {
            name: "Enter Shift name",
        }
    });
});

$(document).on('click', '.txt', function () {
    $("#container").css("overflow-x", "visible");
    //alert('Working with div id');
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Job Shift</span></h1>
    </header>	

    <form action="{{ action('Masterresources\JobshiftController@store') }}" method="post" id="shiftinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder shifName ">
                        <label>Shift Name</label>
                        <input type="text" name="name" id="name"  onpaste="return false;" autocomplete="off">
                        <span class="commonError"></span>
                <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" >
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Shift Start Time</label>                        
                        <input type="text" name="tin" id="tin" class="txt">
                        <!--<button type="button" id="setTimeButton1">Set current time</button>-->
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Shift End Time</label>        
                        <input type="text" name="tout" id="tout" class="txt" >                       
                        <!--<button type="button" id="setTimeButton2">Set current time</button>-->
                        <span class="commonError"></span> 
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create"  class="commonBtn bgGreen addBtn shift" id="sub">
                </div>
            </div>

        </div>
    </form>	
</div>

@endsection