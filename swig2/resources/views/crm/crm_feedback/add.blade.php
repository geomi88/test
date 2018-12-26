@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>CRM Feedback</span></h1>
    </header>	

    <form action="{{ url('crm/crm_feedback/store') }}" method="post" id="frmaddvisitor">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" id="customer_name" autocomplete="off" placeholder="Enter Customer Name" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Mobile Number</label>
                        <input type="text" name="customer_mobile" id="customer_mobile" autocomplete="off" placeholder="Enter Customer Mobile" maxlength="10">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Branch</label>
                        <select id="branch" name="branch" class="chosen-select">
                            <option value=''>Select Branch</option>
                               @foreach($branch as $row)
                               <option value="{{ $row->id }}">{{ $row->branch_code }} : {{ $row->name }}</option>
                               @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Customer Comment</label>
                        <textarea name="customer_comment" id="customer_comment" placeholder="Enter Customer Comment"></textarea>
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
                customer_name:
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
                branch:
                        {
                            required: true,
                        },
                customer_mobile:
                        {
                            required: true,
                            number: true,
                            minlength:10,
                            maxlength:10,
                        },
                customer_comment:
                        {
                            required: true,
                        }
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                name: "Enter Customer name",
                branch: "Selet Branch",
                mobile: {required: "Enter Mobile", number: "Enter numbers only"},
                customer_comment: "Enter Customer Comments"
            }
        });


    });


</script>
@endsection