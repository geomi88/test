@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Cost Name</span></h1>
    </header>	

    <form action="{{ action('Masterresources\CostnameController@store') }}" method="post" id="frmcostname">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder costnameuniq">
                        <label>Cost Name</label>
                        <input type="text" name="name" id="name"  autocomplete="off" placeholder="Enter Cost Name" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias" maxlength="250">
                    </div>
                </div>

            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addinventorycat">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    $(document).ready(function ()
    {
        $("#frmcostname").validate({
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
                        url: "checknameunique",
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
            messages: {
                name: {required: "Enter Cost Name", remote: "Cost name already exist"},
            }
        });
    });
</script>

@endsection
