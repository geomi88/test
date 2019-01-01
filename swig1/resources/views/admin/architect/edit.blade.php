@extends('layout.admin.menu')
@section('content')
@section('title', 'Add Architect')
<?php
$architect_details = $render_params['architect_details'];
$gallery = $render_params['gallery'];
$gallery = $render_params['gallery'];
$projectsList=$render_params['projectsList'];
?>
    <div class="adminPageHolder adminAddPropertyHolder">
				<div class="text-capitalize adminTitle">
					<h1>Edit Architect</h1>
				</div>
				<form method="POST" action="<?php echo url('/'); ?>/admin/save-architect-updates" enctype="multipart/form-data" id="editarchitect" >
        		{{ csrf_field() }}
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
						<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
							<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">EN</a></li>
							<li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">KA</a></li>
							<li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">RU</a></li>
						</ul>
						<div id="en" class="tabContent">
							<div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">name <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Architect Name" name="name_en" id="name_en" value="{{$architect_details->name_en}}">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">address <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_en" id="address_en">{{$architect_details->address_en}}</textarea>
								</div>

								<div class="col-12 mb-3">
									<label class="labelStyle text-capitalize">Add description <span class="redColor">*</span>
									</label>
									<div>
										<textarea rows="8" class="contentTextareaStyle" placeholder="Enter content" name="description_en" id="description_en">{{$architect_details->description_en}}</textarea>
									</div>

								</div>

								<div class="col-12 mb-3">

									<label class="labelStyle text-capitalize">Add more description</label>
									<div>
										<textarea rows="4" class="contentTextareaStyle" placeholder="Enter content" name="additional_description_en" id="additional_description_en">{{$architect_details->additional_description_en}}</textarea>
									</div>
								</div>
							</div>
						</div>

						<div id="ka" class="tabContent">
							<div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">name <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Architect Name" name="name_ka" id="name_ka" value="{{$architect_details->name_ka}}">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">address <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_ka" id="address_ka">{{$architect_details->address_ka}}</textarea>
								</div>

								<div class="col-12 mb-3">
									<label class="labelStyle text-capitalize">Add description <span class="redColor">*</span>
									</label>
									<div>
										<textarea rows="8" class="contentTextareaStyle" placeholder="Enter content" name="description_ka" id="description_ka" >{{$architect_details->description_ka}}</textarea>
									</div>

								</div>

								<div class="col-12 mb-3">

									<label class="labelStyle text-capitalize">Add more description</label>
									<div>
										<textarea rows="4" class="contentTextareaStyle" placeholder="Enter content" name="additional_description_ka" id="additional_description_ka" >{{$architect_details->additional_description_ka}}</textarea>
									</div>
								</div>
							</div>
						</div>

						<div id="ru" class="tabContent">
							<div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">name <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Architect Name" name="name_ru" id="name_ru" value="{{$architect_details->name_ru}}">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">address <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_ru" id="address_ru">{{$architect_details->address_ru}}</textarea>
								</div>

								<div class="col-12 mb-3">
									<label class="labelStyle text-capitalize">Add description <span class="redColor">*</span>
									</label>
									<div>
										<textarea rows="8" class="contentTextareaStyle" placeholder="Enter content" name="description_ru" id="description_ru">{{$architect_details->description_ru}}</textarea>
									</div>

								</div>

								<div class="col-12 mb-3">

									<label class="labelStyle text-capitalize">Add more description</label>
									<div>
										<textarea rows="4" class="contentTextareaStyle" placeholder="Enter content" name="additional_description_ru" id="additional_description_ru">{{$architect_details->additional_description_ru}}</textarea>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="mainBoxHolder">
            		<div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">phone <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Enter Phone" name="phone" id="phone" value="{{$architect_details->phone}}">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">email <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Enter Email" name="email" id="email" value="{{$architect_details->email}}">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">projects <span class="redColor">*</span></label>
								</div>
                                                                <?php 
                                                                $checkArray=array();
                                                                if($architect_details->project_id!=""){
                                                                $checkArray=explode(',',$architect_details->project_id);
                                                                }
                                                                ?>
								<div class="col-9 mb-3">
									<select class="inputStyle chosen-search" id="project" name="project" multiple="multiple">
										<option value="">choose Project</option>
                                                                                @foreach($projectsList as $projects)
                                                                                <option value="{{$projects->id}}" <?php if(in_array($projects->id, $checkArray)){ echo "selected" ; } ?>>{{$projects->name}}</option>
                                                                                @endforeach
                                       
                                        
									</select>
                                                                    <input type="hidden" id="projects" name="projects" value="{{$architect_details->project_id}}">
								</div>
								<div class="row text-capitalize">

                <div class="col-3 mb-3">
                    <label class="labelStyle">Add image</label>
                </div>
                <div class="col-9 mb-3">
                    <div class="previewImageHolder">
                        <img id="previewbox_previewimg" src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
                    </div>
                    <input class="custmFileInput" id="preview_upload" name="preview_upload" type="file" onchange="previewFile('#preview_upload', '#previewbox_previewimg', '178X246')" />
                    <label for="preview_upload" class="customFileUpload">choose file</label>
                    <a href="javascript:void(0)" style="display: none;"  onclick="deletePreview('#previewbox_previewimg')">Delete Image</a>

                </div>
                <div class="col-3 mb-3">
							<label class="labelStyle">Add gallery images</label>
						</div>
                                                <div class="col-9 mb-3">
                                                    <div class="uploadGalleryPrevHolder  mb-2">

                                                        <?php $i = 0;?>
                                                        @foreach($gallery as $galImage)
                                                        <?php
$val = '';
$filename = '';
if ($galImage->image == "" || $galImage->image == null) {
    $filename = url('/') . '/default_image/imgPlaceholder.jpg';
} else {
    $filename = url('/') . "/uploads/architect-gallery/" . $galImage->image;
    $val = $galImage->image;
}
if (!@getimagesize($filename)) {
    $filename = url('/') . '/default_image/imgPlaceholder.jpg';
}
?>

                                                        <div class="previewImageHolder">
                                                            <span class="delImageSelect previewGalImg" onclick="removeImg({{$galImage->id}})">X</span>
                                                            <img src="{{$filename}}">
                                                        </div>
                                                        <?php $i++;?>
                                                        @endforeach
                                                    </div>

                                                    <div class="appendGalleryElement">
										<div class="galleryAddHolder">
											<div class="galPrev">
												<div class="previewImageHolder">
													<span class="delImageSelect delPreviewImg">X</span>
													<img id='previewbox_previewimg_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
												</div>
												<input class="custmFileInput" name="gallery_upload_0" id="gallery_upload_0" type="file" onchange="previewFile_add('#gallery_upload_0', '#previewbox_previewimg_0', '405X300')"/>
												<label for="gallery_upload_0" class="customFileUpload">choose file</label>
											</div>
											<a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">
												<figure>
													<img class="mr-1" src="{{URL::asset('agent')}}/images/iconPlus.png"> add more
												</figure>
											</a>
										</div>
									</div>
                                                </div>


                                    <input type="hidden" name="galleryCount" id="galleryCount" value="0">


            </div>
							</div>
							<div class="mt-2">
								<button type="button" id="btnsaveArchitect" class="btnStyle mr-1">Save</button>
                                <a class="actnIcons" href="{{ URL::to('admin/architect-listing') }}">
                                <button type="button" class="cancelBtnStyle">Back</button></a>							</div>
						</div>
                        <input type="hidden" id="architect_id" name="architect_id" value="{{\Crypt::encrypt($architect_details->id)}}" />

							</form>
						</div>

    <script>
      var count=0;
      $(document).ready(function () {
          $('#project').chosen();
		  $(document).on('click','.addMoreGalleryBtn',function(){
                                count++;

                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect delPreviewImg" >X</span>'+
						'<img id="previewbox_previewimg_'+count+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>  '+
						'  <input class="custmFileInput"  id="gallery_upload_'+count+'" name="gallery_upload_'+count+'" type="file" onchange="previewFile_add(&quot;#gallery_upload_'+count+'&quot;,&quot;#previewbox_previewimg_'+count+'&quot;,&quot;405X300&quot;)"/>'+
						'   <label for="gallery_upload_'+count+'" class="customFileUpload">choose file</label>'+
						'</div>  '+
                                            '   <a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">'+
					'  <figure>'+
					'  <img class="mr-1" src="{{URL::asset('')}}/admin/images/iconPlus.png"> add more'+
					'  </figure>'+
					'</a>  '+
                                '</div>';
                                 $('.appendGalleryElement').append(html);
                            });
 });




 function previewMultiFile(field, previewField, imgHW) {
                            //Check File API support
                            $('.previewDiv').length;
                            var output = $('.previewDiv');
                            var files = document.querySelector(field).files;
                            $('.previewDiv').html("");
                            for (var i = 0; i < files.length; i++) {
                                var file = files[i];
                                //Only pics
                                if (!file.type.match('image'))
                                    continue;

                                var picReader = new FileReader();
                                picReader.addEventListener("load", function (event) {
                                    var picFile = event.target;
                                    if (file.size > 10485760) {
                                        alert('Upload a valid image less than 10 MB');
                                        $(field).val('');
                                        $('.deletepreview').hide();
                                        return false;
                                    }
                                    if (file.type != "image/jpeg" && file.type != "image/png") {
                                        alert('Upload a valid image file (.jpeg / .png)');
                                        $('.deletepreview').hide();
                                        $(field).val('');
                                        return false;
                                    }

                                    var image = new Image();

                                    //Set the Base64 string return from FileReader as source.
                                    image.src = picFile.result;

                                    //Validate the File Height and Width.
                                    image.onload = function () {
                                        var height = this.height;
                                        var width = this.width;
                                        imgsize = imgHW.split('X');
                                        var img_status = 1;
                                        if (typeof (imgsize[0]) != undefined) {
                                            if (height < imgsize[0]) {
                                                img_status = 0;
                                            }

                                        }
                                        if (typeof (imgsize[1]) != undefined) {
                                            if (width < imgsize[1]) {
                                                img_status = 0;

                                            }
                                        }

                                        if (img_status == 0) {
                                            alert('Please upload an image larger than ' + imgHW + '( H X W)');
                                            $('.deletepreview').hide();
                                            $(field).val('');
                                            return false;
                                        }

                                        var div = '';
                                        if (img_status == 1) {
                                            var div = "<div class='previewImageHolder'><span rel=" + field + " class='delImageSelect'>X</span><img id='previewbox_previewimg[]' src='" + picFile.result + "'" + "title='" + picFile.name + "'/></div>";
                                            output.append(div);
                                        }

                                    }
                                });

                                picReader.readAsDataURL(file);
                            }

                        }

						 function previewFile_add(field, previewField, imgHW) {

  var preview = document.querySelector(previewField);
  var file = document.querySelector(field).files[0];
  //10 mb max
  if (file.size > 10485760) {
      alert('Upload a valid image less than 10 MB');
      $(field).val('');
      $('.deletepreview').hide();
      return false;
  }
  if (file.type != "image/jpeg" && file.type != "image/png") {
      alert('Upload a valid image file (.jpeg / .png)');
       $('.deletepreview').hide();
      $(field).val('');
      return false;
  }
  var reader = new FileReader();
  var old_src = preview.src;
  reader.addEventListener("load", function () {
      preview.src = reader.result;
     // $(previewField).attr('data-old-src', old_src);
      $(previewField).css('visibility', 'visible');
      $('.deletepreview').show();
      $("input[name='hiddenImageName']").val(old_src);
  }, false);

  if (file) {
      reader.readAsDataURL(file);
      reader.onload = function (e) {
          //Initiate the JavaScript Image object.
          var image = new Image();
          //Set the Base64 string return from FileReader as source.
          image.src = e.target.result;
          //Validate the File Height and Width.
          image.onload = function () {
              var height = this.height;
              var width = this.width;

              imgsize = imgHW.split('X');
              var img_status = 1;
              if (typeof (imgsize[0]) != undefined) {
                  if (height < imgsize[0]) {
                      img_status = 0;
                  }

              }
              if (typeof (imgsize[1]) != undefined) {
                  if (width < imgsize[1]) {
                      img_status = 0;
                  }
              }

              if (img_status == 0) {
                  alert('Please upload an image larger than ' + imgHW + '( H X W)');
                   $('.deletepreview').hide();
                  $(field).val('');
                  if (old_src != '') {
                      $(previewField).attr('src', old_src);
                  } else {
                      $(previewField).attr('src', luxObject['noImage']);
                  }

              }
              if (img_status == 1) {
                  var galleryCount=  $('#galleryCount').val();
                  galleryCount++;
                  $('#galleryCount').val(galleryCount);
                  }

          };

      }
  }
}

   $('body').on('click', '.previewGalImg', function () {
     $(this).closest('div').remove();
  });

    $('body').on('click', '.delPreviewImg', function () {
     $(this).closest('.galleryAddHolder').remove();
  });

  function removeImg(id){
      $.ajax({
        url: '<?php echo url('/') ?>/admin/architect-image-remove',
            data: {"imgId": id},
        type: 'POST',
        async: true,
        dataType: "json",
        success: function (data) {

       }
      });

  }

$.validator.addMethod("PropertyNameLang", function (value, element) {

var name_ka = $('#name_ka').val();
var name_ru = $('#name_ru').val();
if (name_ka == "" || name_ru == "") {
	return false;
}
else {
	return true;
}


}, 'Please check name in Russian and Georgian');

$.validator.addMethod("Address", function (value, element) {

var address_ka = $('#address_ka').val();
var address_ru = $('#address_ru').val();
if (address_ka == "" || address_ru == "") {
	return false;
}
else {
	return true;
}


}, 'Please enter address in all languages');


$.validator.addMethod("description", function (value, element) {

var description_ka = $('#description_ka').val();
var description_ru = $('#description_ru').val();
if (description_ka == "" || description_ru == "") {
	return false;
}
else {
	return true;
}


}, 'Please check description in Russian and Georgian');



	$("#editarchitect").validate({
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

                                name_en: {
                                    required: true,
                                    PropertyNameLang: true,
                                },
                                name_ka: {
                                    required: true,
                                },
                                name_ru: {
                                    required: true,
                                },
                                description_en: {
                                    required: true,
                                    description: true,
                                },
                                description_ka: {
                                    required: true,
                                },
                                description_ru: {
                                    required: true,
                                },
                                address_en: {
                                    required: true,
                                    Address: true,
                                },
                                address_ka: {
                                    required: true,
                                },
                                address_ru: {
                                    required: true,
                                },

                                phone: {
                                    required: true,
                                },
								email: {
                                    required: true,
									email:true,
                                }


                            },
                            messages: {

                                name_en: {
                                    required: "Please check the name in English,Georgian and Russian",
                                },
								name_ka: {
                                    required: "Please check the name in English",
                                },
                                name_ru: {
                                    required: "Please check the name in Russian",
                                },

								address_en: {
                                    required: "Please check the address  in English,Georgian and Russian",
                                },
                                address_ru: {
                                    required: "Please enter the address in Russian",
                                },
                                address_ka: {
                                    required: "Please enter the address in Georgian",
                                },

								description_en: {
                                    required: "Please check the description  in English,Georgian and Russian",
                                },
                                description_ru: {
                                    required: "Please enter the description in Russian",
                                },
                                description_ka: {
                                    required: "Please enter the description in Georgian",
                                },

                                phone: {
                                    required: "Please enter Phone number",
                                },
								email: {
                                    required: "Please enter Email Address",
									email:"Please enter a valid email address"
                                }
                            },
                        });
                        $('#btnsaveArchitect').click(function () {
                            if (!$("#editarchitect").valid()) {
                                return false;
                            }

                            $("#btnsaveArchitect").attr("disabled", "disabled");
                            $("#editarchitect").submit();
                        });
						</script>
<script>
    $('#description_en').ckeditor();
    $('#description_ka').ckeditor();
    $('#description_ru').ckeditor();
    $('#additional_description_en').ckeditor();
    $('#additional_description_ka').ckeditor();
    $('#additional_description_ru').ckeditor();
    
     $('#project').change(function(){
        var selectedValues = $('#project').val();
      
        if (selectedValues != null) {
            $('#projects').val(selectedValues.join());
        } else {
            $('#projects').val('');
        }
       
   });
</script>
@endsection
