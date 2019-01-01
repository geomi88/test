@extends('layout.admin.menu')
@section('content')
@section('title', 'Construction Details')

<div class="adminPageHolder adminAddPropertyHolder">
    <div class="text-capitalize adminTitle">
        <h1>Add gallery images</h1>
    </div>
    <div class="mainBoxHolder">
        <form action="<?php echo url('/'); ?>/admin/save-gallery" method="post" id="galleryForm" enctype="multipart/form-data">
          {{ csrf_field() }}  
            <input type="hidden" name="projectId" id="projectId" value="<?php echo $projectId; ?>">
            <div class="tabWrapper tabOuterHolder">

                <div id="en" class="tabContent">
                    <div class="row">

                        <div class="col-3 mb-3 ">
                            <label class="labelStyle">Add preview image</label>
                        </div>
                        <div class="col-9 mb-3 mt-4">
                            <div class="previewImageHolder">
                                <img id="previewbox_previewimg" src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
                            </div>
                            <input class="custmFileInput" id="preview_upload" name="preview_upload" type="file" onchange="previewFile('#preview_upload', '#previewbox_previewimg', '178X246')" />
                            <label for="preview_upload" class="customFileUpload">choose file</label>
                            <a href="javascript:void(0)" style="display: none;"  onclick="deletePreview('#previewbox_previewimg')">Delete Image</a>

                        </div>
                        
                        
                        <div class="col-3 mb-3">
                            <label class="labelStyle">Add image slider</label>
                        </div>
                        <div class="col-9 mb-3 appendSliderElement">
                            <div class="galleryAddHolder">
                                <div class="galPrev">
                                    <div class="previewImageHolder">
                                        <span class="delImageSelect">X</span>
                                        <img id='previewbox_slider_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
                                    </div>
                                    <input class="custmFileInput"  id="slider_upload_0" name="slider_upload_0" type="file" onchange="previewSliderFile('#slider_upload_0', '#previewbox_slider_0', '780X460')"/>
                                    <label for="slider_upload_0" class="customFileUpload">choose file</label>
                                </div>
                                <a class="addPropertyBtn addMoreSliderBtn" href="javascript:void(0);">
                                    <figure>
                                        <img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add more
                                    </figure>
                                </a>
                            </div>
                           <input type="hidden" name="sliderCount" id="sliderCount" value="0">
                        </div>


                        <div class="col-3 mb-3">
                            <label class="labelStyle">Add gallery images <span class="redColor">*</span></label>
                        </div>
                        <div class="col-9 mb-3 appendGalleryElement">
                            <div class="galleryAddHolder">
                                <div class="galPrev">
                                    <div class="previewImageHolder">
                                        <span class="delImageSelect">X</span>
                                        <img id='previewbox_previewimg_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
                                    </div>
                                    <input class="custmFileInput"  id="gallery_upload_0" name="gallery_upload_0" type="file" onchange="previewGalleryFile('#gallery_upload_0', '#previewbox_previewimg_0', '160X220')"/>
                                    <label for="gallery_upload_0" class="customFileUpload">choose file</label>
                                </div>
                                <a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">
                                    <figure>
                                        <img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add more
                                    </figure>
                                </a>
                            </div>
                           <input type="hidden" name="galleryCount" id="galleryCount" value="0">
                        </div>
                        <div class="col-3 mb-3">
                            <label class="labelStyle">Add surrounding images <span class="redColor">*</span></label>
                        </div>
                        <div class="col-9 mb-3 appendSurroundingElement">
                            <div class="galleryAddHolder">
                                <div class="galPrev">
                                    <div class="previewImageHolder">
                                        <span class="delImageSelect">X</span>
                                        <img id='previewbox_surrounding_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
                                    </div>
                                    <input class="custmFileInput"  id="surrounding_upload_0" name="surrounding_upload_0" type="file" onchange="previewSurroundFile('#surrounding_upload_0', '#previewbox_surrounding_0', '160X220')"/>
                                    <label for="surrounding_upload_0" class="customFileUpload">choose file</label>
                                </div>
                                <a class="addPropertyBtn addMoreSurroundingBtn" href="javascript:void(0);">
                                    <figure>
                                        <img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add more
                                    </figure>
                                </a>
                            </div>
                            <input type="hidden" name="surroundCount" id="surroundCount" value="0">
                        </div>
                        <div class="col-3 mb-3">
                            <label class="labelStyle">Add garden images <span class="redColor">*</span></label>
                        </div>
                        <div class="col-9 mb-3 appendGardenElement">
                            <div class="galleryAddHolder">
                                <div class="galPrev">
                                    <div class="previewImageHolder">
                                        <span class="delImageSelect">X</span>
                                        <img id='previewbox_garden_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
                                    </div>
                                    <input class="custmFileInput"  id="garden_upload_0" name="garden_upload_0" type="file" onchange="previewGardenFile('#garden_upload_0', '#previewbox_garden_0', '160X220')"/>
                                    <label for="garden_upload_0" class="customFileUpload">choose file</label>
                                </div>
                                <a class="addPropertyBtn addMoreGardenBtn" href="javascript:void(0);">
                                    <figure>
                                        <img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add more
                                    </figure>
                                </a>
                            </div>
                            <input type="hidden" name="gardenCount" id="gardenCount" value="0">
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btnStyle mr-1" id="btnSaveGallery">Save</button>
                        <button type="button" class="cancelBtnStyle">Cancel</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
	
<script>
var gallerycount=0;
var slidercount=0;
var surroundcount=0;
var gardencount=0;
$(document).on('click','.addMoreGalleryBtn',function(){
                                gallerycount++;

                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect">X</span>'+
						'<img id="previewbox_previewimg_'+gallerycount+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>'+
						'  <input class="custmFileInput"  id="gallery_upload_'+gallerycount+'" name="gallery_upload_'+gallerycount+'" type="file" onchange="previewGalleryFile(&quot;#gallery_upload_'+gallerycount+'&quot;,&quot;#previewbox_previewimg_'+gallerycount+'&quot;,&quot;160X220&quot;)"/>'+
						'  <label for="gallery_upload_'+gallerycount+'" class="customFileUpload">choose file</label>'+
						'</div>'+
                                            '  <a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">'+
					'   <figure>'+
					'   <img class="mr-1" src="{{URL::asset('')}}/admin/images/iconPlus.png"> add more'+
					'   </figure>'+
					'</a>  '+
                                '</div>';
                                 $('.appendGalleryElement').append(html);
                            });
                            
$(document).on('click','.addMoreSliderBtn',function(){
                                slidercount++;

                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect">X</span>'+
						'<img id="previewbox_slider_'+slidercount+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>'+
						'  <input class="custmFileInput"  id="slider_upload_'+slidercount+'" name="slider_upload_'+slidercount+'" type="file" onchange="previewSliderFile(&quot;#slider_upload_'+slidercount+'&quot;,&quot;#previewbox_slider_'+slidercount+'&quot;,&quot;780X460&quot;)"/>'+
						'  <label for="slider_upload_'+slidercount+'" class="customFileUpload">choose file</label>'+
						'</div>'+
                                            '  <a class="addPropertyBtn addMoreSliderBtn" href="javascript:void(0);">'+
					'   <figure>'+
					'   <img class="mr-1" src="{{URL::asset('')}}/admin/images/iconPlus.png"> add more'+
					'   </figure>'+
					'</a>  '+
                                '</div>';
                                 $('.appendSliderElement').append(html);
                            });
$(document).on('click','.addMoreSurroundingBtn',function(){
                                surroundcount++;

                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect">X</span>'+
						'<img id="previewbox_surrounding_'+surroundcount+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>'+
						'  <input class="custmFileInput"  id="surrounding_upload_'+surroundcount+'" name="surrounding_upload_'+surroundcount+'" type="file" onchange="previewSurroundFile(&quot;#surrounding_upload_'+surroundcount+'&quot;,&quot;#previewbox_surrounding_'+surroundcount+'&quot;,&quot;160X220&quot;)"/>'+
						'  <label for="surrounding_upload_'+surroundcount+'" class="customFileUpload">choose file</label>'+
						'</div>'+
                                            '  <a class="addPropertyBtn addMoreSurroundingBtn" href="javascript:void(0);">'+
					'   <figure>'+
					'   <img class="mr-1" src="{{URL::asset('')}}/admin/images/iconPlus.png"> add more'+
					'   </figure>'+
					'</a>  '+
                                '</div>';
                                 $('.appendSurroundingElement').append(html);
                            });
$(document).on('click','.addMoreGardenBtn',function(){
                                gardencount++;

                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect">X</span>'+
						'<img id="previewbox_garden_'+gardencount+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>'+
						'  <input class="custmFileInput"  id="garden_upload_'+gardencount+'" name="garden_upload_'+gardencount+'" type="file" onchange="previewGardenFile(&quot;#garden_upload_'+gardencount+'&quot;,&quot;#previewbox_garden_'+gardencount+'&quot;,&quot;160X220&quot;)"/>'+
						'  <label for="garden_upload_'+gardencount+'" class="customFileUpload">choose file</label>'+
						'</div>'+
                                            '  <a class="addPropertyBtn addMoreGardenBtn" href="javascript:void(0);">'+
					'   <figure>'+
					'   <img class="mr-1" src="{{URL::asset('')}}/admin/images/iconPlus.png"> add more'+
					'   </figure>'+
					'</a>  '+
                                '</div>';
                                 $('.appendGardenElement').append(html);
                            });
                            
                            


</script>
<script>
function previewSliderFile(field, previewField, imgHW) {
  
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
            
            var image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                var height = this.height;
                var width = this.width;

                imgsize = imgHW.split('X');
                var img_status = 1;
                if (typeof (imgsize[0]) != undefined) {
                    if (height >= imgsize[0]) {
                        img_status = 0;
                    }

                }
                if (typeof (imgsize[1]) != undefined) {
                    if (width >= imgsize[1]) {
                        img_status = 0;
                    }
                }

                if (img_status == 0) {
                    alert('Please upload an image less than ' + imgHW + '( H X W)');
                     $('.deletepreview').hide();
                    $(field).val('');
                    if (old_src != '') {
                        $(previewField).attr('src', old_src);
                    } else {
                        $(previewField).attr('src', luxObject['noImage']);
                    }
                   
                } 
                if (img_status == 1) {
                    var sliderCount=  $('#sliderCount').val();
                    sliderCount++;
                    $('#sliderCount').val(sliderCount);
                    }

            };

        }
    }
}
</script>

<script>
function previewGalleryFile(field, previewField, imgHW) {
  
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
            
            var image = new Image();
            image.src = e.target.result;
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
</script>
<script>
function previewSurroundFile(field, previewField, imgHW) {
  
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
            
            var image = new Image();
            image.src = e.target.result;
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
                    var surroundCount=  $('#surroundCount').val();
                    surroundCount++;
                    $('#surroundCount').val(surroundCount);
                    }

            };

        }
    }
}
</script>
<script>
function previewGardenFile(field, previewField, imgHW) {
  
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
            
            var image = new Image();
            image.src = e.target.result;
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
                    var gardenCount=  $('#gardenCount').val();
                    gardenCount++;
                    $('#gardenCount').val(gardenCount);
                    }

            };

        }
    }
}

$('#btnSaveGallery').click(function(){
   $('#galleryForm').submit(); 
});
</script>
@endsection