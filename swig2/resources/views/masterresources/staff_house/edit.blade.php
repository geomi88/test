@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Staff House</span></h1>
    </header>	

    <form action="{{ action('Masterresources\StaffhouseController@update') }}" method="post" id="frmstafhouseadd">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">

                <div class="custCol-4">
                    <div class="inputHolder posCode">
                        <label>Staff House Name</label>
                        <input type="text" name="name" id="name" value="{{$staffhouse->name}}" placeholder="Enter Staff House Name" autocomplete="off" maxlength="200">
                        <input type="hidden" name="cid" id="cid" value="{{$staffhouse->id}}">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" value="{{$staffhouse->alias_name}}" placeholder="Enter Alias" autocomplete="off" maxlength="200">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" >
                        <label>Choose Region</label>
                        <select class="commoSelect" name="staffhouseregion" id="staffhouseregion">
                            <option value="">Select Region</option>
                            @foreach ($regions as $region) 
                            <option value='{{ $region->id }}' <?php if ($region->id == $staffhouse->staffhouseregion) {echo "selected";} ?>>{{ $region->name}}</option>
                            @endforeach
                        </select>    
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn" id="sub">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    $(document).ready(function ()
    {
        $("#frmstafhouseadd").validate({
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
                                depends: function () {

                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                            remote:
                                    {
                                        url: "../checkname",
                                        type: "post",
                                        data: {name: $.trim($("#name").val()), cid: $("#cid").val()
                                        },
                                        dataFilter: function (data)
                                        {
                                            var json = JSON.parse(data);
                                            if (json.msg == "true") {

                                                $('.posCode').addClass('ajaxLoaderV1');
                                                $('.posCode').removeClass('validV1');
                                                $('.posCode').addClass('errorV1');
                                                return false;
                                            }
                                            else
                                            {
                                                $('.posCode').addClass('ajaxLoaderV1');
                                                $('.posCode').removeClass('errorV1');
                                                $('.posCode').addClass('validV1');
                                                return true;
                                            }
                                        }
                                    }

                        },
                staffhouseregion: {required: true, }
            },
            messages: {
                name: "Enter Staff House Name ",
                staffhouseregion: "Select Region",
            }
        });
    });
</script>
@endsection