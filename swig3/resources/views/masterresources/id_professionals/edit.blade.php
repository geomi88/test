@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function ()
    {
        $("#jobposinsertion").validate({
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

                                                $('.posCode').addClass('ajaxLoaderV1');
                                                $('.posCode').removeClass('validV1');
                                                $('.posCode').addClass('errorV1');
                                                // valid="false"
                                            }
                                            else
                                            {
                                                $('.posCode').addClass('ajaxLoaderV1');
                                                $('.posCode').removeClass('errorV1');
                                                $('.posCode').addClass('validV1');
                                                //valid="true";
                                                return true;
                                            }
                                        }
                                    }

                        }


            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                job_code: "Enter Job Code",
            }
        });
    });
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>ID Profession</span></h1>
    </header>	
   
    <form action="{{ action('Masterresources\IdprofessionalController@update') }}" method="post" id="jobposinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
<!--                <div class="custCol-4">
                    <div class="inputHolder posCode ">
                        <label>Job Position Code</label>
                        <input type="hidden" name="cid" id="cid" value="{{$idprof->id}}">
                        <input type="text" name="job_code" id="job_code"  onpaste="return false;" autocomplete="off" value="{{$idprof->job_code}}" placeholder="Enter Job Position Code" maxlength="50">
                        <span class="commonError"></span>
                <input type="text" name="name" id="name"  placeholder="Enter Company Name">
                    </div>
                </div>-->
                <div class="custCol-4">
                    <div class="inputHolder posCode">
                        <label>Position Name</label>
                         <input type="hidden" name="cid" id="cid" value="{{$idprof->id}}">
                        <input type="text" name="name" id="name" autocomplete="off" value="<?php echo str_replace('_',' ',$idprof->name);?>" placeholder="Enter ID Profession Name" maxlength="50">
                        <span class="commonError"></span>
                <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" value="{{$idprof->alias_name}}" placeholder="Enter Alias" maxlength="50">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn job_pos" id="sub">
                </div>
            </div>

        </div>
    </form>	
</div>

@endsection