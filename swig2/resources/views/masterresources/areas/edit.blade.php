@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function ()
    {
        $("#areasupdation").validate({
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
                                        url: "../checkareaname",
                                        type: "post",
                                        data:
                                                {
                                                    name: function () {
                                                        return $.trim($("#name").val());
                                                    },
                                                    cid: function () {
                                                        return $("#cid").val();
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
                region:
                        {
                            required: true,
                        }

            },
            submitHandler: function (form) {
                $(".addBtn").attr("disabled", true);
                form.submit();
            },
            messages: {
                name: "Enter Area Name",
                region: "Select Region",
            }
        });
    });
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Area</span></h1>
    </header>	

    <form action="{{ action('Masterresources\AreaController@update') }}" method="post" id="areasupdation">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder areaName ">
                        <label>Area Name</label>
                        <input type="hidden" name="cid" value='{{$areas->id}}' id="cid">
                        <input type="text" name="name" value='{{$areas->name}}' id="name"  onpaste="return false;" autocomplete="off" placeholder="Enter Area Name">
                        <span class="commonError"></span>
                <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" value='{{$areas->alias_name}}' name="alias_name" id="alias_name" placeholder="Enter Alias">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Choose Region</label>
                       <select class="commoSelect" name="region" id="regionselect">
                           <option value="">Select Region</option>
                           @foreach ($regions as $region)                  
                           <option <?php echo ($region->id == $areas->region_id)?"selected":"" ?> value='{{ $region->id }}' >{{ $region->name}}</option>
                           @endforeach     
                        </select>     
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn addcompany" id="sub">
                </div>
            </div>

        </div>
    </form>	
</div>

@endsection