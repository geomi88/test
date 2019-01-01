@extends('layout.admin.menu')
@section('content')
       
                    <div class="adminPageHolder adminAddPropertyHolder">
				<div class="text-capitalize adminTitle">
					<h1>edit profile</h1>
				</div>
				<div class="mainBoxHolder">
					<form method="POST" action="<?php echo url('/'); ?>/admin/update-account" enctype="multipart/form-data" id="saveAdmin" >
                                              {{ csrf_field() }}
                                            <div class="row">
                                                <div class="col-3 mb-3">
						 <label class="labelStyle text-capitalize">Name <span class="redColor">*</span></label>
						</div>
                                                <input type='hidden' name='profile_id' id='profile_id' value='{{\Crypt::encrypt($adminData->id)}}'>
                                                <div class="col-9 mb-3">
                                                     <input class="inputStyle" name='name' id='name' type="text" placeholder="Enter Name" value='{{$adminData->name}}'>
                                                </div>
                                            
						<div class="col-3 mb-3">
							<label class="labelStyle text-capitalize">password <span class="redColor">*</span>
							</label>
						</div>
						<div class="col-9 mb-3">
							<input class="inputStyle" name='password' id='password' type="password" placeholder="Enter Password">
						</div>
						<div class="col-3 mb-3">
							<label class="labelStyle text-capitalize">confirm password <span class="redColor">*</span></label>
						</div>
						<div class="col-9 mb-3">
							<input class="inputStyle" name='confirmpassword' id='confirmpassword' type="password" placeholder="Comfirm Password">
						</div>
						<div class="col-3 mb-3">
							<label class="labelStyle text-capitalize">image</label>
						</div>
						<div class="col-9 mb-3">
							<div class="previewImageHolder">
                                                             <?php
                                                                $image =url('/') . '/default_image/property_default.png';

                                                                if ($adminData->images == "" || $adminData->images == NULL) {
                                                                    ?>

                                                                    <?php
                                                                } else {
                                                                    $image = url('/') . '/uploads/agent/' . $adminData->images;

                                                                    if (!@getimagesize($image)) {
                                                                        $image = url('/') .'/default_image/property_default.png';
                                                                    }else{
                                                                        $image =url('/') .'/uploads/agent/' . $adminData->images;

                                                                    }
                                                                }
                                                        ?>
                                                            <img id="previewbox_previewimg" src="{{$image}}">
                                                        </div>
                                                        <input class="custmFileInput" id="preview_upload" name="preview_upload" type="file" onchange="previewFile('#preview_upload','#previewbox_previewimg','140X140')" />
							<label for="preview_upload" class="customFileUpload">choose file</label>
							<a href="javascript:void(0)" style="display: none;"  onclick="deletePreview('#previewbox_previewimg')">Delete Image</a>
                                                          
							
						</div>
					</div>
                                        </form>
					<div class="mt-2">
						<button id="btnsaveAccount" type="button" class="btnStyle mr-1">Save</button>
						<button type="button" class="cancelBtnStyle">Cancel</button>
					</div>
				</div>
			</div>


<script>
    
  $.validator.addMethod("ifEmpty", function (value, element) {
            var val=this;
            if(this==""){
                return false;
            }else{
                return true;
            } 
            }, 'Please insert your password');
    
     $('#btnsaveAccount').click(function () {
           if (!$("#saveAdmin").valid()) {
                return false;
          }

            $("#btnsaveAccount").attr("disabled", "disabled");
            $("#saveAdmin").submit();
        });         
    
    
     $("#saveAdmin").validate({
            errorElement: 'span',
            errorClass: "errorMsg",
            ignore: '',
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                $("#" + element.id + "_chosen").find('.chosen-single').addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('valErrorV1');
            },
            rules: {
               
                
                name: {
                    required: true,
                 
                }, 
                password : {
                    minlength :8,
                    ifEmpty: true,
                },
                confirmpassword : {
                    minlength :8,
                    equalTo : "#password"
                }
              
            },
            messages: {
                name: {
                    required:"Please enter your name",
                 },

               password: {
                   minlength:"Please enter minimum 8 characters",
                   
                },

                equalTo: {
                    equalTo:"Password mismatches",
                  }, 
               
               
            },

        });
</script>
@endsection