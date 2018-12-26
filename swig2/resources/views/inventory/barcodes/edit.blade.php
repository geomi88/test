@extends('layouts.main')
@section('content')
<head>
    <script type='text/javascript' src="{{ URL::asset('js/jquery-migrate.js') }}"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=true&key=AIzaSyDapZQltpUuickj7SRYriyGYK2i6XxruBI" type="text/javascript"></script>
    <script type='text/javascript' src="{{ URL::asset('js/gmaps.js') }}"></script>

</head>
<script>
$(document).ready(function ()
{
    $("#barcodeinsertion").validate({
        errorElement: "span",
        errorClass: "commonError",
        highlight: function (element, errorClass) {
            $(element).addClass('valErrorV1');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('valErrorV1');
        },
        rules: {
            barcode_string: {
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
                            url: "../checkbarcode",
                            type: "post",
                            data:
                                    {
                                        barcode_string: function () {
                                            return $.trim($("#barcode_string").val());
                                        },
                                        barcode_id: function () {
                                            return $.trim($("#barcode_id").val());
                                        }
                                    },
                            dataFilter: function (data)
                            {
                                var json = JSON.parse(data);
                                if (json.msg == "true") {
                                    $('.barcode_string').addClass('ajaxLoaderV1');
                                    $('.barcode_string').removeClass('validV1');
                                    $('.barcode_string').addClass('errorV1');
                                    document.getElementById("#barcode_string-error").style.display = "none"


                                }
                                else
                                {
                                    $('.barcode_string').addClass('ajaxLoaderV1');
                                    $('.barcode_string').removeClass('errorV1');
                                    $('.barcode_string').addClass('validV1');

                                    return true;
                                }
                            }
                        }

            },
        },
        submitHandler: function () {
            form.submit();
        },
        messages: {
            barcode_string: "Enter Code",
        }
    });

});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Barcode</span></h1>
    </header>	

    <form action="{{ action('Inventory\BarcodeController@update') }}"  method="post" id="barcodeinsertion">

        <div class="custRow">

            <div class="custCol-4">
                <div class="inputHolder barcode_string">
                    <label>Barcode Number</label>
                    <input type="hidden" name="barcode_id" id="barcode_id" value="{{$barcode_details->id}}">
                    <input type="text" name="barcode_string" id="barcode_string" value="{{$barcode_details->barcode_string}}" placeholder="Enter Barcode Number" onpaste="return false;" autocomplete="off">
                    <span class="commonError"></span>
                </div>
            </div>

        </div> 



        <div class="custRow">
            <div class="custCol-4">
                <input type="submit" value="Update" id="btnCreate" class="commonBtn bgGreen addBtn addbarcode">
            </div>
        </div>


    </form>	
</div>



@endsection