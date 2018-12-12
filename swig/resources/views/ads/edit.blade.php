@extends('layouts.main')
@section('content')
<div class="contentHolderV1">
    <h1>Edit Ad</h1>

        <form method="post" action="{{ action('Ads\AdsController@update') }}" id="upateadform" enctype="multipart/form-data" class="formTypeV1">
            {{ csrf_field() }}
            <input type="hidden" id="base_path" value="<?php echo url('/');?>">
            <input type="hidden" name="ad_id" id="ad_id" value="{{$ad_details->id}}">
            <div>
                <label>Ad Location</label>
                <h2>{{$ad_details->adLocation}}</h2>
                <div class="customClear"></div>
                <label>Url</label>
                <input type="text" name="adUrl" id="adUrl" value="{{$ad_details->url}}">
                <div class="customClear"></div>
                <div class="urlError error"></div>                                
                <label>Image</label>
                <img id="preview_img" src="<?php echo url('/');?>{{$ad_details->image}}">
                <div class="customClear"></div>
                <!--<input type="file" name="adImage" id="adImage" onchange="readImage( this )">-->
                <input type="file" name="adImage" id="adImage">
                <div class="previewImage">
                </div>
               <!-- <img id="preview_img" src=""/>-->
                <div class="customClear"></div>
                <div class="imageError error"></div>
            </div>
<!--            <input type="hidden" name="x" id="x" value="">
            <input type="hidden" name="y" id="y" value="">
            <input type="hidden" name="x2" id="x2" value="">
            <input type="hidden" name="y2" id="y2" value="">
            <input type="hidden" name="w" id="w" value="">
            <input type="hidden" name="h" id="h" value="">-->
            <input class="commonButton" id="saveAd" type="button" value="SAVE"> 
        </form>
        
         

</div>  

<div class="customClear"></div>
</div>


<script src= "{{ URL::asset('js/jquery.Jcrop.min.js') }}"></script>
<link rel="stylesheet" href="{{ URL::asset('css/jquery.Jcrop.css') }}" type="text/css" />
<script>
    //for image preview
    function readImage(input) {
    if ( input.files && input.files[0] ) {
        var FR= new FileReader();
        FR.onload = function(e) {
            $('#preview_img').attr( "src", e.target.result );
            $('#preview_img').Jcrop({
                //aspectRatio: 1,
                allowResize: false,
                allowMove:true,
                setSelect:   [50, 0, 400,200],
                allowSelect:false,
                onSelect:function(c){
                    $('#x').val(c.x);
                    $('#y').val(c.y);
                    $('#x2').val(c.x2);
                    $('#y2').val(c.y2);
                    $('#w').val(c.w);
                    $('#h').val(c.h);
                }
             });
             //$('#base').text( e.target.result ); //this is the base64 encoded image
             var img = e.target.result;
        };       
        FR.readAsDataURL( input.files[0] );
       // $("#preview_img").css("visibility","visible");
    }
}

    $(document).ready(function () {

        $("#saveAd").click(function (event) {
            event.preventDefault();
            var base_path = $('#base_path').val();
            var ad_id = $('#ad_id').val();
            var url = $('#adUrl').val();
            url = url.trim();
                        
            var errors = 0;
            if (url == '') {
                $('.urlError').html('Please enter Url');
                errors = 1;
            } else {
                $('.urlError').html('');
            }
            
            if (errors == 1)
            {
                return false;
            }            
            else
            {
                $( "#upateadform" ).submit();
            }
        });       
    });
</script>
<script src= "{{ URL::asset('js/jquery.Jcrop.min.js') }}"></script>
<link rel="stylesheet" href="{{ URL::asset('css/jquery.Jcrop.css') }}" type="text/css" />
<script language="Javascript">
    /*jQuery(function($) {
        $('#preview_img').Jcrop({
           aspectRatio: 1,
           allowResize: false,
           allowMove:true,
           setSelect:   [50, 0, 300,300],
           allowSelect:false
        });
    });*/
</script>
@endsection

