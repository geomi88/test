@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Division</span></h1>
    </header>	

    <form action="{{ action('Masterresources\DivisionController@update') }}" method="post" id="divisioninsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder divisionName ">
                        <label>Division Name</label>
                         <input type="hidden" name="cid" value='{{$division->id}}' id="cid">
                        <input type="text" name="name" id="name" value='{{$division->name}}' onpaste="return false;" autocomplete="off" placeholder="Enter Division Name">
                        <span class="commonError"></span>
                <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" value='{{$division->alias_name}}' id="alias_name" placeholder="Enter Alias">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Choose Department</label>
                        <select class="commoSelect" name="department" id="regionselect">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department) 
                             <option <?php echo ($department->id == $division->department_id)?"selected":"" ?> value='{{ $department->id }}' >{{ $department->name}}</option>
                            @endforeach
                        </select>    
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn addcompany" id="sub">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    $(document).ready(function ()
    {
        $("#divisioninsertion").validate({
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
                                        url: "../checkdevisionname",
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

                                                $('.divisionName').addClass('ajaxLoaderV1');
                                                $('.divisionName').removeClass('validV1');
                                                $('.divisionName').addClass('errorV1');
                                                // valid="false"
                                            }
                                            else
                                            {
                                                $('.divisionName').addClass('ajaxLoaderV1');
                                                $('.divisionName').removeClass('errorV1');
                                                $('.divisionName').addClass('validV1');
                                                //valid="true";
                                                return true;
                                            }
                                        }
                                    }

                        },
                department:
                        {
                            required: true,
                        }

            },
            
            messages: {
                name: "Enter Division name",
                department: "Select Department",
            }
        });
    });
</script>
@endsection