@extends('layout.admin.menu')
@section('content')
@section('title', 'Property Edit')
<?php

$municipalities = $pageData['municipalities'];
$districts = $pageData['district'];
$property = $pageData['property'];
$gallery = $pageData['gallery'];

?>
<style>
    .map{
        height:252px;
        border:0px solid #000;
        max-width:255px;
        max-height: 219px;
       // margin-left:135px;
        margin-bottom: 20px;
    }
    
</style>
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="text-capitalize adminTitle">
					<h1>Edit Neighborhood details</h1>
				</div>
                        <form method="POST" action="<?php echo url('/'); ?>/admin/save-neighbourhood-updates" enctype="multipart/form-data" id="updateNeighbor" >
                            {{ csrf_field() }}
					
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
						<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
							<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">en</a></li>
							<li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">ka</a></li>
							<li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
						</ul>
						<div id="en" class="tabContent">
							<h2 class="mt-2 text-capitalize">basic information <span class="subInfo">(* these are mandatory fields)</span></h2>
							<div class="row">
								<div class="col-6 mt-4 text-capitalize">
									
									
									<div class="halfWidth mb-3">
										<label class="labelStyle">name <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
										<input class="inputStyle" type="text" placeholder="Name" name="name_en" id="name_en" value="{{$property->name_en}}">
									</div>
                                                                        <div class="halfWidth mb-3">
										<label class="labelStyle">select district <span class="redColor">*</span></label>
									</div>
									
                                                                      
                                                                        <div class="halfWidth mb-3">
										 <select name="district_en"  id="district_en" class="inputStyle chosen-search"> 
                                                                                    <option value="">Choose</option>
                                                                                    @foreach($districts as $district)
                                                                                    <option value="{{$district->id}}" <?php if($property->district_id==$district->id) { echo "selected";} ?>  >{{$district->name}}</option>
                                                                                    @endforeach
                                                                                </select>
									</div>
                                                                        <div class="clearfix"></div>
                                                                        <div class="halfWidth mb-3">
										<label class="labelStyle">select City <span class="redColor">*</span></label>
									</div>
									
                                                                        <div class="halfWidth mb-3">
										 <select name="city_en"  id="city_en" class="inputStyle chosen-search"> 
                                                                                    <option value="">Choose</option>
                                                                                    @foreach($municipalities as $cities)
                                                                                    <option value="{{$cities->id}}" <?php if($property->municipality_id==$cities->id) { echo "selected";} ?>  >{{$cities->name}}</option>
                                                                                    @endforeach
                                                                                </select>
									</div>
								</div>
							</div>
							<div class="row">
                                                            <div class="col-6 clearfix">
                                                                <div class="halfWidth mb-6">
                                                                    <label class="labelStyle">Address <span class="redColor">*</span></label>
                                                                </div>
                                                                <div class="halfWidth mb-6">
                                                                    <textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_en" id="address_en" >{{$property->address_en}}</textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                                <div class="col-12">
									<label class="labelStyle text-capitalize">Add description</label>
									<textarea rows="8" name="description_en" id="description_en" class="inputStyle ckeditor" class="contentTextareaStyle" placeholder="Enter content" >{{$property->description_en}}</textarea>
								</div>
                                                           
								

							</div>
						</div>
                                            	<div id="ka" class="tabContent">
							<h2 class="mt-2 text-capitalize">basic information <span class="subInfo">(* these are mandatory fields)</span></h2>
							<div class="row">
								<div class="col-6 mt-4 text-capitalize">
									
									
									<div class="halfWidth mb-3">
										<label class="labelStyle">name <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
										<input class="inputStyle" type="text" placeholder="Name" name="name_ka" id="name_ka" value="{{$property->name_ka}}">
									</div>
                                                                        
								</div>
							</div>
							<div class="row">
                                                            <div class="col-6 clearfix">
                                                                <div class="halfWidth mb-6">
                                                                    <label class="labelStyle">Address <span class="redColor">*</span></label>
                                                                </div>
                                                                <div class="halfWidth mb-6">
                                                                    <textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_ka" id="address_ka" >{{$property->address_ka}}</textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                                <div class="col-12">
									<label class="labelStyle text-capitalize">Add description</label>
									<textarea rows="8" name="description_ka" id="description_ka" class="inputStyle ckeditor" class="contentTextareaStyle" placeholder="Enter content">{{$property->description_ka}}</textarea>
								</div>
                                                           
								

							</div>
						</div>
                                            	<div id="ru" class="tabContent">
							<h2 class="mt-2 text-capitalize">basic information <span class="subInfo">(* these are mandatory fields)</span></h2>
							<div class="row">
								<div class="col-6 mt-4 text-capitalize">
									
									
									<div class="halfWidth mb-3">
										<label class="labelStyle">name <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
										<input class="inputStyle" type="text" placeholder="Name" name="name_ru" id="name_ru" value="{{$property->name_ru}}">
									</div>
                                                                        
									
								</div>
							</div>
							<div class="row">
                                                            <div class="col-6 clearfix">
                                                                <div class="halfWidth mb-6">
                                                                    <label class="labelStyle">Address <span class="redColor">*</span></label>
                                                                </div>
                                                                <div class="halfWidth mb-6">
                                                                    <textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_ru" id="address_ru" >{{$property->address_ru}}</textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                                <div class="col-12">
									<label class="labelStyle text-capitalize">Add description</label>
									<textarea rows="8" name="description_ru" id="description_ru" class="inputStyle ckeditor" class="contentTextareaStyle" placeholder="Enter content">{{$property->description_ru}}</textarea>
								</div>
                                                           
								

							</div>
						</div>
					
						
					</div>
				</div>
				
				
				<div class="mainBoxHolder">
					<div class="text-capitalize">
						<h2>add images</h2>
					</div>
					<hr>
					<div class="row text-capitalize">
						
                                                <div class="col-3 mb-3">
							<label class="labelStyle">Add preview image</label>
						</div>
						<div class="col-9 mb-3">
                                                   <?php
                                                            $val = '';
                                                            if ($property->mainimage == "" || $property->mainimage == NULL) {
                                                                $filename = url('/') . '/default_image/imgPlaceholder.jpg';
                                                            } else {
                                                                $filename = url('/') . "/uploads/property-gallery/" . $property->mainimage;
                                                                $val = $property->mainimage;
                                                            }
                                                            if (!@getimagesize($filename)) {
                                                                $filename = url('/') . '/default_image/imgPlaceholder.jpg';
                                                            }
                                                            ?> 
                                                      <div class="previewImageHolder">
                                                            <img id="previewbox_previewimg" src="{{$filename}}">
                                                        </div>
                                                        <input class="custmFileInput" id="preview_upload" name="preview_upload" type="file" onchange="previewFile('#preview_upload','#previewbox_previewimg','178X246')" />
							<label for="preview_upload" class="customFileUpload">choose file</label>
							 <a href="javascript:void(0)" style="display: none;"  onclick="deletePreview('#previewbox_previewimg')">Delete Image</a>
                                                          
                                                      
						</div>
                                                <div class="col-3 mb-3">
							<label class="labelStyle">Add gallery images</label>
						</div>
                                                <div class="col-9 mb-3">
                                                    <div class="uploadGalleryPrevHolder  mb-2">

                                                        <?php $i = 0; ?>
                                                        @foreach($gallery as $galImage)
                                                        <?php
                                                        $val = '';
                                                        $filename = '';
                                                        if ($galImage->image == "" || $galImage->image == NULL) {
                                                            $filename = url('/') . '/default_image/imgPlaceholder.jpg';
                                                        } else {
                                                            $filename = url('/') . "/uploads/neighborhood/" . $galImage->image;
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
                                                        <?php $i++; ?>
                                                        @endforeach
                                                    </div>
                                                    
                                                    <div class="appendGalleryElement">
										<div class="galleryAddHolder">
											<div class="galPrev">
												<div class="previewImageHolder">
													<span class="delImageSelect delPreviewImg">X</span>
													<img id='previewbox_previewimg_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
												</div>
												<input class="custmFileInput" name="gallery_upload_0" id="gallery_upload_0" type="file" onchange="previewFile_add('#gallery_upload_0', '#previewbox_previewimg_0', '160X220')"/>
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
					<button type="button" id="btnsaveProperty" class="btnStyle">Save</button>
				</div>
                                <input type="hidden" id="latitude" name="latitude" value="{{$property->latitude}}"/>
                                <input type="hidden" id="longitude"  name="longitude" value="{{$property->longitude}}" />
                                <input type="hidden" id="propertId" name="propertyId" value="{{\Crypt::encrypt($property->id)}}" />
                        </form>
                        </div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE"></script>
<script>
    var count=$('#galleryCount').val();
$(document).ready(function(){
    
    $('#buildingType').chosen();
    $('#municipality').chosen();
        $(document).on('click','.addMoreGalleryBtn',function(){
                                count++;
//                                var param1="#gallery_upload_"+count;
//                                var param2="#previewbox_previewimg_"+count;
//                                var param3="160X220";
                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect delPreviewImg" >X</span>'+
						'<img id="previewbox_previewimg_'+count+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>  '+
						'  <input class="custmFileInput"  id="gallery_upload_'+count+'" name="gallery_upload_'+count+'" type="file" onchange="previewFile_add(&quot;#gallery_upload_'+count+'&quot;,&quot;#previewbox_previewimg_'+count+'&quot;,&quot;160X220&quot;)"/>'+
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

$("#addproperty__").validate({
            errorElement: 'span',
            errorClass: "commonErrorV1",
            ignore: '',
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                $("#" + element.id + "_chosen").find('.chosen-single').addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('valErrorV1');
            },
            rules: {
                estimated_price: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },

                name_en: {
                    required: { 
                       depends: function () {
                       var name_ka=$('#name_ka').val();
                       var name_ru=$('#name_ru').val();
                       if(name_ka==""){
                           return false;
                       }
                       else if(name_ru==""){
                           return false;
                       }else {
                           if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                           }
                       }
                        
                     },
                    
                    }, 
                 }, 
                name_ka: {
                    required: true,
                },
                name_ru: {
                    required: true,
                },
                address_line1_en: {
                   depends: function () {
                       var address_line1_ka=$('#address_line1_ka').val();
                       var address_line1_ka=$('#address_line1_ru').val();
                       if(address_line1_ka==""){
                           return false;
                       }
                       if(address_line1_ka==""){
                           return false;
                       }
                        
                    },
                required: true,
                }, 
                address_line1_ka: {
                    required: true,
                },
                address_line1_ru: {
                    required: true,
                },
                address_line2_en: {
                    required: true,
                }, 
                address_line2_ka: {
                    required: true,
                },
                address_line2_ru: {
                    required: true,
                },
                zip_en: {
                    required: true,
                     depends: function () {
                       var zip_ka=$('#zip_ka').val();
                       var zip_ru=$('#zip_ru').val();
                       if(zip_ka==""){
                           return false;
                       }
                       if(zip_ru==""){
                           return false;
                       }
                        
                    },
                }, 
                zip_ka: {
                    required: true,
                },
                zip_ru: {
                    required: true,
                },
                municipality_en: {
                    required: true,
                      depends: function () {
                       var municipality_ka = $('#municipality_ka').val();
                       var municipality_ru=$('#municipality_ru').val();
                       if(municipality_ka==""){
                           return false;
                       }
                       if(municipality_ru==""){
                           return false;
                       }
                        
                    },
                },
                
                municipality_ka: {
                    required: true,
                },
                municipality_ru: {
                    required: true,
                },
                total_area: {
                    required: true,
                },
                habitable_area: {
                    required: true,
                },
                no_of_baths: {
                    required: true,
                }, 
                no_of_garages: {
                    required: true,
                },
                no_of_floors: {
                    required: true,
                },
                no_of_beds: {
                    required: true,
                },
                no_of_balcony: {
                    required: true,
                },
                terraces: {
                    required: true,
                },
                parking: {
                    required: true,
                },
                construction_year: {
                     required: true,
                },
                availability: {
                     required: true,
                },
                gardens: {
                    required: true,
                },
                propertyType: {
                    required: true,
                },
                property_plan:{
                   // required:true,
                   // extension: "docx|rtf|doc|pdf",
                    extension: "pdf",
                }
            },
            messages: {
                estimated_price: {
                    required:"Estimated Price is required",
                 },

                name_en: {
                    required: "Please check the property name in English",
                   // remote: "Please check the property name in English,Georgian and Russian",
                }, 
                name_ka: {
                      required: "Please check the property name in English",
                },
                name_ru: {
                    required: "Please check the property name in Russian",
                },
                address_line1_en: {
                    required: "Please enter the address line in English",
                   // remote: "Please check the address line in English,Georgian and Russian",
                }, 
                address_line1_ka: {
                    required: "Please enter the address line in Georgian",
                },
                address_line1_ru: {
                    required: "Please enter the address line in Russian",
                },
                address_line2_en: {
                    required: "Please enter the address line in English",
                    //remote: "Please check the address line in English,Georgian and Russian",
                }, 
                address_line2_ka: {
                   required: "Please enter the address line in Georgian",
                },
                address_line2_ru: {
                    required: "Please enter the address line in Russian",
                },
                zip_en: {
                    required: "Please enter the zip in English",
                   // remote: "Please check the zip in English,Georgian and Russian",
                }, 
                zip_ka: {
                   required: "Please enter the zip in Georgian",
                },
                zip_ru: {
                    required: "Please enter the zip in Russian",
                },
                city_en: {
                    required: "Please enter the city in English",
                    //remote: "Please check the city in English,Georgian and Russian",
                }, 
                city_ka: {
                    required: "Please enter the zip in Georgian",
                },
                city_ru: {
                   required: "Please enter the zip in Russian",
                },
                total_area: {
                     required: "Please enter the total area",
                },
                habitable_area: {
                    required: "Please enter the habitable area",
                },
                no_of_baths: {
                    required: "Please enter the number of baths",
                }, 
                no_of_garages: {
                   required: "Please enter the number of garages",
                },
                no_of_floors: {
                    required: "Please enter the number of floors",
                },
                no_of_beds: {
                    required: "Please enter the number of beds",
                },
                no_of_balcony: {
                    required: "Please enter the number of balcony",
                },
                terrace: {
                    required: "Please enter the number of terraces",
                },
                parking: {
                    required: "Please check if parking available or not",
                },
                construction_year: {
                     required: "Please enter the construction year",
                },
                availability: {
                     required: "Please check the availability",
                },
                gardens: {
                    required: "Please check if garden is available or not",
                },
                propertyType: {
                    required: "Please choose the property type",
                },
            },

        });
        
        
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
 $('#btnsaveProperty').click(function () {
          /*  if (!$("#addproperty").valid()) {
                return false;
            }*/

            $("#btnsaveProperty").attr("disabled", "disabled");
            $("#updateNeighbor").submit();
        });

</script>
<script>
    $('#description_en').ckeditor();  
    $('#description_ka').ckeditor();
    $('#description_ru').ckeditor();
</script>
<script type="text/javascript">
    $(document).ready(function() {
      $(".datepicker").datepicker({
           // maxDate: new Date(),
            dateFormat: 'dd-M-yy',
            changeMonth: true,
            changeYear: true,
           
        });
    });
                
     
     $('body').on('click', '.previewGalImg', function () {
       $(this).closest('div').remove();
    });
    
      $('body').on('click', '.delPreviewImg', function () {
       $(this).closest('.galleryAddHolder').remove();
    });
    
    function removeImg(id){
        $.ajax({
          url: '<?php echo url('/')?>/admin/neighbourhoodImg-remove',
              data: {"imgId": id},
          type: 'POST',
          async: true,
          dataType: "json",
          success: function (data) {
              
         }
        });
        
    }
</script>

@endsection
		