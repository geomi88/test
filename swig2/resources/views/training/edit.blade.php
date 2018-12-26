@extends('layouts.main')
@section('content')

<div class="innerContent">


    <form action="{{ action('Training\TrainingController@update') }}" method="post" id="frmupdatemeeting">
        <input type="hidden" name="id" value="{{$meeting_details->id}}">
        <div class="fieldGroup meetingHolder">
            <header class="pageTitle">
                <h1>Edit <span>Training</span></h1>
            </header>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Training Title</label>
                        <input type="text" name="title" autocomplete="off" maxlength="250" id="title" value="{{$meeting_details->title}}">
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description" id="description">{{$meeting_details->description}}</textarea>
                    </div>
                </div>
            </div>
            

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnCreate" class="commonBtn bgGreen addBtn" id="sub">
                </div>
            </div>
        </div>
    </form>
        
    </div>
                
</div>
<script>
    $(document).ready(function () {
        
        
        $("#frmcreatemeeting").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                title: {required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    }, },
                description: {required: true},
                
            },
            messages: {
                title: "Enter Title",
                description: "enter Description",
                
            }
        });

    }
</script>
@endsection