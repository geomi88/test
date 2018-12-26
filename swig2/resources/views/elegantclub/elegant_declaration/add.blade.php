@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add Elegant <span>Declaration</span></h1>
    </header>

    <form action="{{ url('elegantclub/declaration/store') }}" method="post" id="elegantclud_dec_frm">
        <input type="hidden" name="decl_id" id="decl_id" value="">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Title</label>
                        <input type="text" name="title" id="title" autocomplete="off" placeholder="Enter title" maxlength="100">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Content</label>
                        <textarea name="content" id="content" maxlength="255" placeholder="Enter Declaration Content"></textarea>
                    </div>
                </div>
            </div>
          </div>
           <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" id="btnCreate" class="commonBtn bgGreen addBtn">
                </div>
            </div>
        </div>
    </form>

</div>
<div class="commonLoaderV1"></div>
<script>
    
    $(document).ready(function ()
    { 
        var v = jQuery("#elegantclud_dec_frm").validate({ 
            errorElement: "span",
            errorClass: "commonError",
            ignore: '' ,
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
                title:
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
                content:
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
            },
            messages: {
                title: "Enter Title",
                content: "Enter declaration content",
            },
        });
    });
    
    $('#elegantclud_dec_frm').submit(function (e) {
        if (!$('#elegantclud_dec_frm').valid()) { //alert('Not valid');
            return false;
        }else{  
            $(".commonLoaderV1").show();
            return true;
        }
    });
    
    
</script>
@endsection