@extends('layouts.main')
@section('content')


<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Suggestion</span>/<span>Complaints</span></h1>
    </header>	

    <form action="{{ action('Tasks\SuggestionController@save') }}" method="post" id="frmaddsuggestion" enctype="multipart/form-data">
        <div class="fieldGroup" id="fieldSet1">
             <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Submit To</label>
                        <select  name="cmbsumbitto" id="cmbsumbitto">
                            <option selected value=''>Select</option>
                            <option value='Owner'>Owner</option>
                            <option value='CEO'>CEO</option> 
                            <option value='Owner/CEO'>Owner/CEO</option>
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder shifName ">
                        <label>Title</label>
                        <input type="text" name="title" id="title" placeholder="Enter Title" autocomplete="off" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
             <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>
             <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder selectV1">
                                    <label>Upload Attachment</label>
                                    <input type="file" onchange="readURL(this);" id="attachment" class="attachment" name="attachment" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
<!--                                    <img id="file_previewpic">-->
                                </div>
                </div>
            </div>
            


            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create"  class="commonBtn bgGreen addBtn shift" id="btnCreateplan">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    $(document).ready(function (){
        
        
       
        $("#frmaddsuggestion").validate({
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
                        },},
                cmbsumbitto:{required: true},
            },
           
            messages: {
                title: "Enter Title",
                cmbsumbitto: "Select Submit To",
            }
        });
        
        
    });
    
   function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
//            reader.onload = function (e) {
//                $('#file_previewpic')
//                        .attr('src', e.target.result)
//                        .width(150)
//                        .height(150);
//            };
            reader.readAsDataURL(input.files[0]);
        }
    }
 
</script>
@endsection