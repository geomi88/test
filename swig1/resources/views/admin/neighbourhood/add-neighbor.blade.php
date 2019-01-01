@extends('layout.admin.menu')
@section('content')
@section('title', 'Add Property')
<?php

$municipalities = $pageData['municipalities'];

$districts = $pageData['district'];
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
        <h1>add new neighbourhood</h1>
    </div>
    <form method="POST" action="<?php echo url('/'); ?>/admin/save-neighbourhood" enctype="multipart/form-data" id="addneighbour" >
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
										<input class="inputStyle" type="text" placeholder="Name" name="name_en" id="name_en">
									</div>
                                                                        <div class="halfWidth mb-3">
										<label class="labelStyle">select district <span class="redColor">*</span></label>
									</div>
									
                                                                      
                                                                        <div class="halfWidth mb-3">
										 <select name="district_en"  id="district_en" class="inputStyle chosen-search"> 
                                                                                    <option value="">Choose</option>
                                                                                    @foreach($districts as $district)
                                                                                    <option value="{{$district->id}}"  >{{$district->name}}</option>
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
                                                                                    <option value="{{$cities->id}}"  >{{$cities->name}}</option>
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
                                                                    <textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_en" id="address_en"></textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                                <div class="col-12">
									<label class="labelStyle text-capitalize">Add description</label>
									<textarea rows="8" name="description_en" id="description_en" class="inputStyle ckeditor" class="contentTextareaStyle" placeholder="Enter content"></textarea>
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
										<input class="inputStyle" type="text" placeholder="Name" name="name_ka" id="name_ka">
									</div>
                                                                        
                                                                       
								</div>
							</div>
							<div class="row">
                                                            <div class="col-6 clearfix">
                                                                <div class="halfWidth mb-6">
                                                                    <label class="labelStyle">Address <span class="redColor">*</span></label>
                                                                </div>
                                                                <div class="halfWidth mb-6">
                                                                    <textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_ka" id="address_ka"></textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                                <div class="col-12">
									<label class="labelStyle text-capitalize">Add description</label>
									<textarea rows="8" name="description_ka" id="description_ka" class="inputStyle ckeditor" class="contentTextareaStyle" placeholder="Enter content"></textarea>
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
										<input class="inputStyle" type="text" placeholder="Name" name="name_ru" id="name_ru">
									</div>
                                                                       
								</div>
							</div>
							<div class="row">
                                                            <div class="col-6 clearfix">
                                                                <div class="halfWidth mb-6">
                                                                    <label class="labelStyle">Address <span class="redColor">*</span></label>
                                                                </div>
                                                                <div class="halfWidth mb-6">
                                                                    <textarea rows="4" class="inputStyle" placeholder="Enter Address" name="address_ru" id="address_ru"></textarea>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                                <div class="col-12">
									<label class="labelStyle text-capitalize">Add description</label>
									<textarea rows="8" name="description_ru" id="description_ru" class="inputStyle ckeditor" class="contentTextareaStyle" placeholder="Enter content"></textarea>
								</div>
                                                           
								

							</div>
						</div>

              
            </div>
            <div class="row">
                <div class="col-3 mb-3 mt-4">
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
            <div class="col-3 mb-3 mt-4">
                                                                <label class="labelStyle">Add gallery images</label>
                                                            </div>


                                                            <div class="col-9 mb-3 appendGalleryElement mt-4">
                                                                <div class="galleryAddHolder">
								<div class="galPrev">
									<div class="previewImageHolder">
										<span class="delImageSelect">X</span>
										<img id='previewbox_previewimg_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
									</div>
									<input class="custmFileInput"  id="gallery_upload_0" name="gallery_upload_0" type="file" onchange="previewFile_add('#gallery_upload_0', '#previewbox_previewimg_0', '160X220')"/>
									<label for="gallery_upload_0" class="customFileUpload">choose file</label>
								</div>
								<a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">
									<figure>
										<img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add more
									</figure>
								</a>
                                                            </div>
							      </div>
                                                    <input type="hidden" name="galleryCount" id="galleryCount" value="0">
								<div class="col-12 mt-2">
									<button type="button" class="btnStyle mr-1" id="btnsaveProperty">Save</button>
									<button type="button" class="cancelBtnStyle">Cancel</button>
								</div>
            </div>
        </div>
      
       
        
        <input type="hidden" id="latitude" name="latitude" />
        <input type="hidden" id="longitude" name="longitude" />
    </form>
</div>

<script>
      var count=0;
      $(document).ready(function () {
                          
                            $('#municipality').chosen();
                            
                            $(document).on('click','.addMoreGalleryBtn',function(){
                                count++;
//                                var param1="#gallery_upload_"+count;
//                                var param2="#previewbox_previewimg_"+count;
//                                var param3="160X220";
                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect">X</span>'+
						'<img id="previewbox_previewimg_'+count+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>'+
						'  <input class="custmFileInput"  id="gallery_upload_'+count+'" name="gallery_upload_'+count+'" type="file" onchange="previewFile_add(&quot;#gallery_upload_'+count+'&quot;,&quot;#previewbox_previewimg_'+count+'&quot;,&quot;160X220&quot;)"/>'+
						'  <label for="gallery_upload_'+count+'" class="customFileUpload">choose file</label>'+
						'</div>'+
                                            '  <a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">'+
					'   <figure>'+
					'   <img class="mr-1" src="{{URL::asset('')}}/admin/images/iconPlus.png"> add more'+
					'   </figure>'+
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

                        //document.getElementById('files').addEventListener('change', handleFileSelect, false);

                      
                    /*    $.validator.addMethod("PropertyNameLang", function (value, element) {

                            var name_ka = $('#name_ka').val();
                            var name_ru = $('#name_ru').val();
                            if (name_ka == "" || name_ru == "") {
                                return false;
                            }
                            else {
                                return true;
                            }


                        }, 'Please check name in Russian and Georgian');

                        $.validator.addMethod("AddressLine1", function (value, element) {

                            var address_line1_ka = $('#address_line1_ka').val();
                            var address_line1_ru = $('#address_line1_ru').val();
                            if (address_line1_ka == "" || address_line1_ka == "") {
                                return false;
                            }
                            else {
                                return true;
                            }


                        }, 'Please check Address Line 1 in Russian and Georgian');

                        $.validator.addMethod("AddressLine2", function (value, element) {

                            var address_line2_ka = $('#address_line2_ka').val();
                            var address_line2_ru = $('#address_line2_ru').val();
                            if (address_line2_ka == "" || address_line2_ka == "") {
                                return false;
                            }
                            else {
                                return true;
                            }


                        }, 'Please check Address Line 2 in Russian and Georgian');
                        $.validator.addMethod("zip", function (value, element) {

                            var zip_ka = $('#zip_ka').val();
                            var zip_ru = $('#zip_ru').val();
                            if (zip_ka == "" || zip_ru == "") {
                                return false;
                            }
                            else {
                                return true;
                            }


                        }, 'Please check zip code in Russian and Georgian');

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

                        $("#addproperty").validate({
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
                                address_line1_en: {
                                    required: true,
                                    AddressLine1: true,
                                },
                                address_line1_ka: {
                                    required: true,
                                },
                                address_line1_ru: {
                                    required: true,
                                },
                                address_line2_en: {
                                    required: true,
                                    AddressLine2: true,
                                },
                                buildingType: {
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
                                    zip: true,
                                },
                                zip_ka: {
                                    required: true,
                                },
                                zip_ru: {
                                    required: true,
                                },
                                municipality_en: {
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
                               
                                construction_year: {
                                    required: true,
                                },
                                availability: {
                                    required: true,
                                },
                               
                                propertyType: {
                                    required: true,
                                },
                                district_en: {
                                    required: true,
                                },
                                property_plan: {
                                    // required:true,
                                    // extension: "docx|rtf|doc|pdf",
                                    extension: "pdf",
                                },
                                parking: {
                                    required: true,
                                },
                                terrace: {
                                    required: true,
                                },
                                gardens: {
                                    required: true,
                                }

                            },
                            messages: {
                                estimated_price: {
                                    required: "Estimated Price is required",
                                },
                                name_en: {
                                    // required: "Please check the property name in English",
                                    required: "Please check the property name in English,Georgian and Russian",
                                },
                                address_line1_en: {
                                    //required: "Please enter the address line in English",
                                    required: "Please check the address line in English,Georgian and Russian",
                                },
                                address_line2_en: {
                                    // required: "Please enter the address line in English",
                                    required: "Please check the address line in English,Georgian and Russian",
                                },
                                zip_en: {
                                    //required: "Please enter the zip in English",
                                    required: "Please check the zip in English,Georgian and Russian",
                                },
                                buildingType: {
                                    required: "Please choose the building type",
                                    // remote: "Please check the zip in English,Georgian and Russian",
                                },
                                district_en: {
                                    required: "Please choose the district",
                                    // remote: "Please check the zip in English,Georgian and Russian",
                                },
                                city_en: {
                                    required: "Please enter the city in English",
                                    //remote: "Please check the city in English,Georgian and Russian",
                                },
                                name_ka: {
                                    required: "Please check the property name in English",
                                },
                                name_ru: {
                                    required: "Please check the property name in Russian",
                                },
                                address_line1_ru: {
                                    required: "Please enter the address line in Russian",
                                },
                                address_line1_ka: {
                                    required: "Please enter the address line in Georgian",
                                },
                                address_line2_ka: {
                                    required: "Please enter the address line in Georgian",
                                },
                                address_line2_ru: {
                                    required: "Please enter the address line in Russian",
                                },
                                zip_ka: {
                                    required: "Please enter the zip in Georgian",
                                },
                                zip_ru: {
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
                                
                                construction_year: {
                                    required: "Please enter the construction year",
                                },
                                availability: {
                                    required: "Please check the availability",
                                },
                                

                                description_en: {
                                    required: "Please check the description in English,Russian and Georgian",
                                },
                                parking: {
                                    required: "Please choose Parking availability",
                                },
                                terrace: {
                                    required: "Please choose Terrace availability",
                                },
                                gardens: {
                                    required: "Please choose Garden availability",
                                },
                                propertyType: {
                                    required: "Please choose the Tenure Type",
                                }
                            },
                        });
                        $('#btnsaveProperty').click(function () {
                            if (!$("#addproperty").valid()) {
                                return false;
                            }

                            $("#btnsaveProperty").attr("disabled", "disabled");
                            $("#addproperty").submit();
                        });*/

</script>
<script>
    $('#description_en').ckeditor();
    $('#description_ka').ckeditor();
    $('#description_ru').ckeditor();
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".datepicker").datepicker({
            // maxDate: new Date(),
            dateFormat: 'dd-M-yy',
            changeMonth: true,
            changeYear: true,
        });
    });




</script>
<script>
 $('body').on('click', '.delImageSelect', function () {
      
         $(this).closest('.galleryAddHolder').remove();
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
                        /*    if (!$("#addproperty").valid()) {
                                return false;
                            }*/

                            $("#btnsaveProperty").attr("disabled", "disabled");
                            $("#addneighbour").submit();
                        });

</script>
@endsection
