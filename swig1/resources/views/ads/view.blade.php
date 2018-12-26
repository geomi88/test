@extends('layouts.main')
@section('content')
<div class="contentHolderV1">
    <h1>View Ad</h1>

            <input type="hidden" id="base_path" value="<?php echo url('/');?>">
            <div class="formTypeV1">
                <label>Ad Location</label>
                <input type="text" name="adLocation" id="adLocation" value="{{$ad_details->adLocation}}" readonly="true">
                <div class="customClear"></div>
                <label>Url</label>
                <input type="text" name="adUrl" id="adUrl" value="{{$ad_details->url}}" readonly="true">
                <div class="customClear"></div>
                
                <label>Image</label>
                <img src="<?php echo url('/');?>{{$ad_details->image}}" style="width:150px;height:150px;">
            </div>


</div>  

<div class="customClear"></div>



@endsection
