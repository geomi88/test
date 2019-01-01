@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Visitor</span></h1>
    </header>	

    <form action="{{ action('Reception\ReceptionController@store') }}" method="post" id="frmaddvisitor">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Date & Time</label>
                        <input type="text" name="date_time" id="date_time" readonly autocomplete="off" value="<?php echo date("d-m-Y H:i"); ?>" maxlength="50">
                        <span class="commonError"></span>
                    </div>
                </div>

            </div>
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Visitor Name</label>
                        <input type="text" name="name" id="name" autocomplete="off" placeholder="Enter Visitor Name" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>

            </div>

            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>To Meet</label>
                        <select id="to_meet" name="to_meet" class="chosen-select">
                            <option value=''>Select Employee</option>
                            @foreach ($employees as $employe)
                            <option value="{{ $employe->id }}" >{{$employe->username}} : {{$employe->first_name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>


            </div>

            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Company</label>
                        <input type="text" name="company" id="company" autocomplete="off" placeholder="Enter Company" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>

            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Mobile</label>
                        <input type="text" name="mobile" id="mobile" autocomplete="off" placeholder="Enter Mobile" maxlength="20">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Email</label>
                        <input type="email" name="email" id="email" autocomplete="off" placeholder="Enter Email" maxlength="50">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-8">
                    <div class="inputHolder">
                        <label>Purpose</label>
                        <textarea name="purpose" id="purpose" placeholder="Enter Purpose"></textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function ()
    {
        $("#frmaddvisitor").validate({
            errorElement: "span",
            errorClass: "commonError",
            ignore: [],
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id = $(element).attr("id") + "_chosen";
                if ($(element).hasClass('valErrorV1')) {
                    $("#" + id).find('.chosen-single').addClass('chosen_error');
                }
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
                        },
                to_meet:
                        {
                            required: true,
                        },
                mobile:
                        {
                            required: true,
                            number: true,
                        },
                purpose:
                        {
                            required: true,
                        }
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                name: "Enter visitor name",
                to_meet: "Selet employee to meet",
                mobile: {required: "Enter Mobile", number: "Enter numbers only"},
                purpose: "Enter purpose"
            }
        });


    });


</script>
@endsection
