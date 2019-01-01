@extends('layout.admin.menu')
@section('content')
@section('title', 'Construction Details')

<style>
    .map{
        height:252px;
        border:0px solid #000;
        max-width:100%;
        max-height: 219px;
        // margin-left:135px;
        margin-bottom: 20px;
    }

</style>
<div class="adminPageHolder adminAddPropertyHolder">
    <div class="mainBoxHolder">
        <div class="tabWrapper tabOuterHolder">
            <ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
                <li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">EN</a></li>
                <li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">ka</a></li>
                <li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
                
                <li class="text-capitalize addFloorBtn">
                    <?php if($project->floor_insert=='1'){ ?>
                       <a href="{{ URL::to('admin/list-floor', ['id' => Crypt::encrypt($project->id)]) }}">Edit floor</a>
                    <?php }else{ ?>
                    <a href="{{ URL::to('admin/add-floor', ['id' => Crypt::encrypt($project->id)]) }}">add Floor</a>
                    <?php } ?>
                </li>
                <li class="text-capitalize addGalleryBtn">
                    <?php if($project->gallery_insert=='1'){ ?>
                     <a href="{{ URL::to('admin/edit-gallery', ['id' => Crypt::encrypt($project->id)]) }}">Edit Gallery</a>
                       
                   <?php }else{ ?>
                    <a href="{{ URL::to('admin/add-gallery', ['id' => Crypt::encrypt($project->id)]) }}">add Gallery</a>
                    <?php } ?>
                </li>
                <strong class="viewerCount mt-2">number of Interests : <span>{{$project_interest}}</span></strong>
								
            </ul>
            <div id="en" class="tabContent">
                <div class="row">
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">name</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle text-capitalize">{{$project->name_en}}</div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">URL</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">{{$project->url}}</div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">description</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                            <?php  echo htmlspecialchars_decode($project->description_en);?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="labelStyle mb-3 text-capitalize subInnerTitle">Surroundings</label>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">description</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                           <?php  echo htmlspecialchars_decode($project->surrounding_description_en);?>
                        </div>
                    </div>

                    <div class="col-3 mt-3">
                        <label class="labelStyle mb-3 text-capitalize">Garden</label>
                    </div>
                    <div class="col-9 mt-3">
                        <div class="detailContentStyle">
                           <?php  echo htmlspecialchars_decode($project->garden_description_en);?>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">Perfect Comfort</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                             <?php  echo htmlspecialchars_decode($project->comfort_description_en);?>
                        </div>
                    </div>
                </div>

            </div>
            <div id="ka" class="tabContent">
                <div class="row">
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">name</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle text-capitalize">{{$project->name_ka}}</div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">URL</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">{{$project->url}}</div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">description</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                             <?php  echo htmlspecialchars_decode($project->description_ka);?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="labelStyle mb-3 text-capitalize subInnerTitle">Surroundings</label>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">description</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                            <?php  echo htmlspecialchars_decode($project->surrounding_description_ka);?>
                        </div>
                    </div>

                    <div class="col-3 mt-3">
                        <label class="labelStyle mb-3 text-capitalize">Garden</label>
                    </div>
                    <div class="col-9 mt-3">
                        <div class="detailContentStyle">
                            <?php  echo htmlspecialchars_decode($project->garden_description_en);?>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">Perfect Comfort</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                            <?php  echo htmlspecialchars_decode($project->comfort_description_ka);?>
                        </div>
                    </div>
                </div>

            </div>
            <div id="ru" class="tabContent">
                <div class="row">
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">name</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle text-capitalize">{{$project->name_ru}}</div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">URL</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">{{$project->url}}</div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">description</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">										
                            <?php  echo htmlspecialchars_decode($project->description_ru);?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="labelStyle mb-3 text-capitalize subInnerTitle">Surroundings</label>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">description</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                           <?php  echo htmlspecialchars_decode($project->description_ru);?>
                        </div>
                    </div>

                    <div class="col-3 mt-3">
                        <label class="labelStyle mb-3 text-capitalize">Garden</label>
                    </div>
                    <div class="col-9 mt-3">
                        <div class="detailContentStyle">
                            <?php  echo htmlspecialchars_decode($project->garden_description_ru);?>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="labelStyle mb-3 text-capitalize">Perfect Comfort</label>
                    </div>
                    <div class="col-9">
                        <div class="detailContentStyle">
                           <?php  echo htmlspecialchars_decode($project->comfort_description_ru);?>
                        </div>
                    </div>
                </div>

            </div>


        </div>
        <div class="row">
            <div class="col-12">
                <label class="labelStyle mb-3 text-capitalize subInnerTitle">information obligation</label>
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">Estimated Price</label>
                    </div>
                    <div class="col-6">
                        <?php 
                        $price=$project->price;
                        if($project->price==""){
                            $price=0;
                        }?>
                        <div class="detailContentStyle text-capitalize">{{env('CURRENCY')}} {{$price}}</div>
                    </div>
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">urban planning permits</label>
                    </div>
                    <div class="col-6">
                        <div class="detailContentStyle text-capitalize"><?php if($project->planning_permit=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
                    </div>
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">destination</label>
                    </div>
                    <div class="col-6">
                        <div class="detailContentStyle text-capitalize">{{$project->destination}}</div>
                    </div>
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">subpoenas</label>
                    </div>
                    <div class="col-6">
                        <div class="detailContentStyle text-capitalize"><?php if($project->subpoenas=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
                    </div>
                    
                </div>
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">pre-emption right</label>
                    </div>
                    <div class="col-6">
                        <div class="detailContentStyle text-capitalize"><?php if($project->pre_emption_right=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
                    </div>
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">sub-division permit</label>
                    </div>
                    <div class="col-6">
                        <div class="detailContentStyle text-capitalize"><?php if($project->subdivision_permit=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
                    </div>
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">judicial sayings</label>
                    </div>
                    <div class="col-6">
                        <div class="detailContentStyle text-capitalize"><?php if($project->judicial_sayings=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
                    </div>
                    <div class="col-6">
                        <label class="labelStyle mb-3 text-capitalize">flood area</label>
                    </div>
                    <div class="col-6">
                        <div class="detailContentStyle text-capitalize"><?php if($project->flood_area=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
                    </div>
                </div>
            </div>
            
            <div class="col-3 mt-3">
                        <label class="labelStyle text-capitalize">location map</label>
                    </div>
                    <div class="col-9 mt-3">
                        <div class="mapHolder">
                            <div id="map" class="map">
                                
                            </div>
                        </div>
                    </div>
        </div>

        <div class="mt-2">
            <a href="{{ URL::to('admin/construction-edit', ['id' => Crypt::encrypt($project->id)]) }}"><button type="button" class="btnStyle mr-1">edit details</button></a>
            <a href="{{ URL::to('admin/construction-listing') }}"><button type="button" class="cancelBtnStyle">Cancel</button></a>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE"></script>


<script>
    
 $(document).ready(function () {
         geolocator();
        });
    
    
    var Lat = '<?php echo $project->latitude ;?> ';
    var Lng = '<?php echo $project->longitude ;?>';

  //  var Lat = "41.716667";
   // var Lng = "44.783333";

    function geolocator() {
        var geocoder = new google.maps.Geocoder();
        var address = "Tbilisi";
        geocoder.geocode({'address': address}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();
                Lat = latitude;
                Lng = longitude;
            } else {
                alert("geocoder Request failed \n or \n country not found.")
            }
            geoLoc();
        });
    }

    function geoLoc() {
        var latlng = new google.maps.LatLng(Lat, Lng);
        var map = new google.maps.Map(document.getElementById('map'), {
            center: latlng,
            zoom: 9,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false
        });
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: 'Set lat/lon values for this property',
            draggable: true
        });
        google.maps.event.addListener(marker, 'dragend', function (a) {
            var div = document.createElement('div');
            document.getElementById('latitude').value = a.latLng.lat();
            document.getElementById('longitude').value = a.latLng.lng();
            document.getElementsByTagName('body')[0].appendChild(div);
            displayLocation(a.latLng.lat(), a.latLng.lng());
        });
    }

    window.onload = function () {
        var latlng = new google.maps.LatLng(Lat, Lng);
        var map = new google.maps.Map(document.getElementById('map'), {
            center: latlng,
            zoom: 9,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false
        });
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: 'Set lat/lon values for this property',
            draggable: true
        });
        google.maps.event.addListener(marker, 'dragend', function (a) {
            var div = document.createElement('div');
            document.getElementById('latitude').value = a.latLng.lat();
            document.getElementById('longitude').value = a.latLng.lng();
            document.getElementsByTagName('body')[0].appendChild(div);
            displayLocation(a.latLng.lat(), a.latLng.lng());
        });
    };

 </script>
 @endsection