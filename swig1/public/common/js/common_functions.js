$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
luxObject = [];
luxObject['noImage'] = basePath + '/default_image/imgPlaceholder.jpg';

jQuery.validator.addMethod("phoneNumber", function(value, element) 
{
return this.optional(element) || /^[0-9-+s()\.][0-9\s-()\.]*$/.test(value);
}, "Alphabets or Empty blank spaces are not allowed");

jQuery.validator.addMethod("strictEmail", function(value, element) 
{
return this.optional(element) || /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9])+$/.test(value);
}, "Please enter a valid email ");

jQuery.validator.addMethod('strictUrl', function(value, element) {
            var url = $.validator.methods.url.bind(this);
            return url(value, element) || url('http://' + value, element) || url('https://' + value, element);
        }, 'Please enter a valid URL');



// Use this code for image preview
//field= '#abc' ,previewField='#abc_preview,imgHW='100X100

function previewFile(field, previewField, imgHW) {
   
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

            };

        }
    }
}

function deletePreview(previewField) {
    $("input[type='file']").css('visibility', 'hidden');
    $('.deletepreview').hide();

    $(previewField).attr('src', c7Object['noImage']);
    if (typeof ($(previewField).attr('data-old-src') != undefined)) {
        $(previewField).attr('src', $(previewField).attr('data-old-src'));
    }
    $("input[type='file']").val('');
    $("input[name='hiddenImageName']").val('');
}
function deletePreviewMultiple(previewField, Thisfield, fileInputId) {
    $(previewField).next('.deletepreview').hide();
    $(previewField).css('visibility', 'hidden');
    $(previewField).attr('src', c7Object['noImage']);
    if (typeof ($(previewField).attr('data-old-src') != undefined)) {
        $(previewField).attr('src', $(previewField).attr('data-old-src'));
    }
    $(fileInputId).val('');
    $(Thisfield).hide();
}
