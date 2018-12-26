@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function ()
    {
        $("#frmpolicyedit").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                name: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    remote: {
                        url: "../checknameunique",
                        type: "post",
                        data:
                                {
                                    name: function () {
                                        return $.trim($("#name").val());
                                    },
                                    cid:$("#cid").val()
                                },
                        dataFilter: function (data)
                        {
                            var json = JSON.parse(data);
                            if (json.msg == "true") {
                                $('.costnameuniq').addClass('ajaxLoaderV1');
                                $('.costnameuniq').removeClass('validV1');
                                $('.costnameuniq').addClass('errorV1');
                                return false;
                            }
                            else
                            {
                                $('.costnameuniq').addClass('ajaxLoaderV1');
                                $('.costnameuniq').removeClass('errorV1');
                                $('.costnameuniq').addClass('validV1');
                                //valid="true";
                                return true;
                            }
                        }
                    }
                }
            },
            messages: {name: {required: "Enter policy Name", remote: "Policy name already exist"}}
        });
    });
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Policy</span></h1>
    </header>	

    <form action="{{ action('Masterresources\PolicymasterController@update') }}" method="post" id="frmpolicyedit">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder costnameuniq">
                        <label>Policy Name</label>
                        <input type="hidden" name="cid" id="cid" value='{{ $policydata->id }}'>                       
                        <input type="text" name="name" id="name" value='{{ $policydata->name }}' autocomplete="off" placeholder="Enter Cost Name" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" value='{{ $policydata->alias_name }}' autocomplete="off" id="alias_name" placeholder="Enter Alias" maxlength="250">
                    </div>
                </div>

            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn addinventorycat">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>


</script>
@endsection
