@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('purchase/update_po_status')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Update PO Status</h1>
    </header>

    <div class="inputAreaWrapper">
        <div class="custRow reqCodeDateHolder">

            <div class="custCol-6 ">
                <label>PO Code : <span>{{ $order_details->order_code }}</span></label>
            </div>
            <div class="custCol-6 alignRight">
                <label>PO Date : <span><?php echo date('d-m-Y', strtotime($order_details->created_at)); ?></span></label>
            </div>
        </div>

        <form id="frmpostatus" name="frmpostatus" method="post" style="padding-top: 20px">
            <div class="custRow">
                <div class="custCol-6">
                    <label>PO Status</label>
                    <div class="inputHolder">
                        <textarea id="po_status" name="po_status" placeholder="PO Status"></textarea>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custRow ">
                    <div class="custCol-4">
                        <input type="hidden" id="po_id" value="{{ $id }}">
<!--                        <div class="inputHolder checkHolder">
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input name="end_po" checked="" value="1"  type="radio">
                                    <span></span>
                                    <em>Complete PO</em>
                                </label>
                            </div>
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input name="end_po" value="2"  type="radio">
                                    <span></span>
                                    <em>Preclosure PO</em>
                                </label>
                            </div>
                            <span class="commonError"></span>
                        </div>-->
                    </div>
                </div>
                <div class="custRow ">
                    <div class="custCol-4">
                        <input id="btnupdatepo" class="btnIcon lightGreenV3" value="Create" type="button">
                    </div>
                </div>
            </div>
        </form>
        <div class="custRow reqCodeDateHolder clsReqItemSeparator" style="margin-top: 20px;">

            <div class="custCol-6 ">
                <label>Previous Status List</label>
            </div>

        </div>
        <div class="approverDetailsWrapper">
            <div class="tbleListWrapper">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="headingHolder ">
                        <tr>
                            <th>PO Status</th>
                            <th style="min-width: 135px;">Updated On</th>
                            <th style="min-width: 180px;">Updated By</th>
                        </tr>
                    </thead>
                    <tbody id="load_po_status">
                        @include("purchase/update_po_status/status_result")
                    </tbody>
                </table>
            </div>
        </div>
        <div class="customClear"></div>
    </div>
    <div class="commonLoaderV1"></div>
</div>

<script>

    $(function () {
        $("#frmpostatus").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                po_status: {
                     required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },

            },
            messages: {
                po_status: "Enter PO Status",

            }
        });

    });
    $("#btnupdatepo").click(function (data) {
        if ($("#frmpostatus").valid()) {
            var po_status = $("#po_status").val();
            var id = $("#po_id").val();
            var close_status = $("input[type='radio'][name='end_po']:checked").val();
            if (typeof close_status == 'undefined') {
                close_status = 0;
            }
            
            $('.commonLoaderV1').show();
            $('#btnupdatepo').attr('disabled','disabled');
            $.ajax({
                url: "../update",
                type: "post",
                data: {update_text: po_status, id: id}
            }).done(function (data) {
                if (data == 0) {
                    toastr.error("Something went wrong");
                } else {
                    $("#load_po_status").html(data);
                    toastr.success("Status Updated Successfully");
                    $("#frmpostatus").find("textarea,input[type='radio']").val('');
                }
                $('.commonLoaderV1').hide();
                $('#btnupdatepo').removeAttr('disabled');
            })
        }
    });

    // By Default make radio button unchecked

    $("input[type='radio'][name='end_po']").removeAttr('checked');


</script>
@endsection

