@extends('layout.web.website')
@section('content')
@section('title', 'Neighborhood')
@section('neighborhood', 'active')
@section('pageClass', 'detailPage')
@section('innerClass', 'withoutBanner')
@include('web.includes.banner_inner')

<section class="contentWrapper">
    <div class="pageCenter">
        <ul class="breadcrumbsHolder">
            <li>
                <a href="javascript:void();">home <span>/</span></a>
            </li>
            <li>
                <a href="javascript:void();">neighborhood<span>/</span></a>
            </li>
            <li>
                <a href="javascript:void();">{{$neighborhood_detail->name}}</a>
            </li>
        </ul>
        <div class="row headingSection">
            <div class="small-6 medium-8 large-8 column">
                <h2>{{$neighborhood_detail->name}}</h2>
            </div>
            <div class="small-6 medium-4 large-4 column">
                <ul class="ratingAndShare">

                    <li class="share"></li>
                    <li class="print"></li>
                </ul>
            </div>
        </div>
        <div class="detailWrapper">
            <div class="detailGallery">

                <?php
             
                $image = url('/').'/default_image/property_default.png';

                if ($neighborhood_detail->images == "" || $neighborhood_detail->images == NULL) {
                    ?>

                    <?php
                } else {
                    $image = url('/').'/uploads/neighborhood/' . $neighborhood_detail->images;

                    if (!@getimagesize($image)) {
                        $image = url('/').'/default_image/property_default.png';
                    } else {
                       $image =url('/').'/uploads/neighborhood/' . $neighborhood_detail->images;
 
                    }
                }
                ?>
                <div class="galleryLeft" style="background-image: url({{$image}});">
                    <figure><img src="{{$image}}" alt=""></figure>
                </div>
                <div class="galleryRight">
                    <ul>
                        @foreach($gallery as $images)
                        <?php
                        $image = 'default_image/property_default.png';

                        if ($images->image == "" || $images->image == NULL) {
                            ?>

                            <?php
                        } else {
                             $image = url('/') . '/uploads/neighborhood/' . $images->image;

                           

                            if (!@getimagesize($image)) {
                                $image = 'default_image/property_default.png';
                            } else {
                                 $image = '/uploads/neighborhood/' . $images->image;
                            }
                        }
                        ?>
                        <li> <a href="javascript:void(0);">see all photos</a>
                            <figure><img src="{{URL::asset($image)}}"></figure>
                        </li>
                        @endforeach
  
                    </ul>
                </div>
            </div>

            <div class="detailContent">
                <div class="detailLeft">
                     <?php echo htmlspecialchars_decode($neighborhood_detail->description); ?>
                
                    <a class="readMoreBtn" href="#">Read More + </a>
                    <div class="nearByneigh">
                        <div class="tabHeading neighborhoodView">
                            <ul>
                                <li class="semiBoldFont" data-tab="Reference">Near by Neighborhoods</li>

                            </ul>
                            <a class="btnCommon green btnFloorPlan" href="{{URL::to('web/neighborhoods')}}">View Neighborhood</a>
                        </div>
                    </div>
                    <div class="row small-up-1 medium-up-2 large-up-2 nearNeighbHolder">
                        @foreach($nerabyNeigborhood as $nearby)
                        <?php
                        $image = 'default_image/property_default.png';

                        if ($nearby->images == "" || $nearby->images == NULL) {
                            ?>

                            <?php
                        } else {
                            $image = url('/') .'/uploads/neighborhood/' . $nearby->images;

                            if (!@getimagesize($image)) {
                                $image = 'default_image/property_default.png';
                            } else {
                               $image = '/uploads/neighborhood/' . $nearby->images; 
                            }
                        }
                        ?>
                         
                        <div class="column constructionList">
                            <figure><img src="{{URL::asset($image)}}" alt="">
                                <div class="mask">
                                    <div class="textContent center">
                                        <span>{{$nearby->name}} </span>
                                    </div>
                                </div>
                            </figure>
                        </div>
                        @endforeach

                    </div>
                </div>

                <div class="detailRight">
                    <h5>Discover this Neighbourhood</h5>
                    <div class="locationMap">
                       <div class="map" id='map'>
                            
                        </div>

                    <div class="mapHolder">
                       
                    
                    </div>
                        
                        <input type="hidden" name="latitude" id="latitude" value="{{$neighborhood_detail->latitude}}">
                        <input type="hidden" name="longitude" id="longitude" value="{{$neighborhood_detail->longitude}}">
                        
                        <h5>Interesting Points</h5>
                        <ul class="recgPoints">
                            <li class="selected" onclick="initMap()" ><figure><img src="{{ URL::asset('web/images')}}/iconRecognition.png" alt=""></figure>Recognition Points</li>
                            <li onclick="changeLoc('school');"><figure><img src="{{ URL::asset('web/images')}}/iconSchool.png" alt=""></figure>Schools</li>
                            <li onclick="changeLoc('university');"><figure><img src="{{ URL::asset('web/images')}}/iconUniversity.png" alt=""></figure>University</li>
                            <li onclick="changeLoc('museum');"><figure><img src="{{ URL::asset('web/images')}}/iconMuseum.png" alt=""></figure>Museums</li>
                            <li onclick="changeLoc('hospital');"><figure><img src="{{ URL::asset('web/images')}}/iconHospital.png" alt=""></figure>Hospitals</li>
                            <li onclick="changeLoc('park');"><figure><img src="{{ URL::asset('web/images')}}/iconParks.png" alt=""></figure>Parks</li>
                        </ul>
                        <a class="btnCommon" href="<?php echo url('/');?>/web/neighborhoods">All Neighborhoods</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="similarProperties">
    <div class="row small-up-1 medium-up-3 large-up-3 allListingWrapper">
        <h2><span>SIMILAR</span>PROPERTIES</h2>

        @foreach($similar as $similarPlot)
        <div class="column allListBoxHolder">
            <?php
            $slug = substr(strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $similarPlot->name)), 0, 25);
            ?>
            <div class="listHandle">
                <a href="{{URL('/')}}/property_view/{{$slug}}/{{$similarPlot->reference_number}}">
                    <figure class="listImgHolder">
                        <?php if ($similarPlot->mainimage == NULL) { ?>
                            <img src="{{ URL::asset('default_image')}}/property_default.png" alt="Villa with pool for Rent">
                        <?php } else { ?>
                            <img src="{{ URL::asset('uploads/property-gallery')}}/{{$similarPlot->mainimage}}" alt="Villa with pool for Rent">
                        <?php } ?>
                    </figure>
                </a>
                <div class="listDetails eqHeightInner">
                    <span class="propertyName">{{$similarPlot->name}}</span>
                    <p>{{$similarPlot->address1}}{{$similarPlot->address2}}{{$similarPlot->zip}}{{$similarPlot->municipality}}</p>
                    <span class="Price">{{$similarPlot->estimated_price}}</span>
                </div>
                <ul class="roomDetails">
                    <li><figure><img src="{{ URL::asset('web/images')}}/iconBed.png" alt=""><figcaption>{{$similarPlot->no_of_beds}} Beds</figcaption></figure></li>
                    <li><figure><img src="{{ URL::asset('web/images')}}/iconBath.png" alt=""><figcaption>{{$similarPlot->no_of_baths}} Baths</figcaption></figure></li>
                    <li><figure><img src="{{ URL::asset('web/images')}}/iconSqFeet.png" alt=""><figcaption>{{$similarPlot->total_area}} </figcaption></figure></li>
                </ul>
            </div>
        </div>
        @endforeach
    </div>
</div>
 @include('web.includes.our_services')



<div class="galleryOverlay">
    <a class="btnClose" href="javascript:void()"></a>
    <div class="galleryWrapper"></div>
</div>

 <div class="galleryContainer">
                <div id="slideshow" class="fullscreen">
                        <a class="btnGalClose" href="javascript:void(0);"></a>
                        @foreach($sliderGallery as $galImages)
                        <div id="img-{{$galImages->id}}" data-img-id="{{$galImages->id}}" class="img-wrapper <?php if($galImages->main_image=='1'){ echo "active" ;} ?>"  style="background-image: url({{ URL::asset('uploads/neighborhood')}}/{{$galImages->image}});"></div>
                        @endforeach
<!--                    <div id="img-1" data-img-id="1" class="img-wrapper active" style="background-image: url('images/img1.jpg')"></div>
                    <div id="img-2" data-img-id="2" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>
                    <div id="img-3" data-img-id="3" class="img-wrapper" style="background-image: url('images/img3.jpg')"></div>
                    <div id="img-4" data-img-id="4" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>
                    <div id="img-1" data-img-id="5" class="img-wrapper" style="background-image: url('images/img1.jpg')"></div>
                    <div id="img-2" data-img-id="6" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>
                    <div id="img-3" data-img-id="7" class="img-wrapper" style="background-image: url('images/img3.jpg')"></div>
                    <div id="img-4" data-img-id="8" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>-->
                    <div class="thumbs-container bottom">
                        <div id="prev-btn" class="prev">
                            <i class="fa fa-chevron-left fa-3x"></i>
                        </div>
    
                        <ul class="thumbs">
                            @foreach($sliderGallery as $galImages)
                            <li data-thumb-id="{{$galImages->id}}" class="thumb  <?php if($galImages->main_image=='1'){ echo "active" ;} ?>" style="background-image: url({{ URL::asset('uploads/neighborhood')}}/{{$galImages->image}});"></li>
                            @endforeach
<!--                            <li data-thumb-id="1" class="thumb active" style="background-image: url('images/img1-thumb.jpg')"></li>
                            <li data-thumb-id="2" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>
                            <li data-thumb-id="3" class="thumb" style="background-image: url('images/img3-thumb.jpg')"></li>
                            <li data-thumb-id="4" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>
                            <li data-thumb-id="5" class="thumb" style="background-image: url('images/img1-thumb.jpg')"></li>
                            <li data-thumb-id="6" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>
                            <li data-thumb-id="7" class="thumb" style="background-image: url('images/img3-thumb.jpg')"></li>
                            <li data-thumb-id="8" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>-->
                        </ul>
    
                        <div id="next-btn" class="next">
                            <i class="fa fa-chevron-right fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE&libraries=places&callback=initMap" async defer></script>
<script>
     

      var map;
      var infowindow;
      var points = ['school','museums','hospital','university','park'];
        
      function initMap() {
          var latitude=$('#latitude').val();
          var longitude=$('#longitude').val();
         if(latitude=="" && longitude==""){
            
            latitude = "41.716667";
            longitude = "44.783333";
         }
          
        var pyrmont = {lat: parseFloat(latitude), lng:parseFloat(longitude)};

        map = new google.maps.Map(document.getElementById('map'), {
          center: pyrmont,
          zoom: 15
        });

        infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);
         var points = ['school','museum','hospital','university','park'];
        for(var m in points) {
        service.nearbySearch({
          location: pyrmont,
          radius: 1000,
          type: [points[m]]
        }, callback);
        
    }
      }

      function callback(results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            createMarker(results[i]);
          }
        }
      }

      function createMarker(place) {
        var placeLoc = place.geometry.location;
     
        var iconPoints = ['school','museum','hospital','university','park'];
        var iconName="";
        for(var j=0;j< place.types.length;j++){
            if($.inArray(place.types[j], iconPoints) !== -1){
           iconName=place.types[j];
        
            }
        }
      
	var iconType = {};
	    iconType['school'] = "<?php echo url('/') ?>/web/images/iconSchool.png";
	    iconType['university'] = "<?php echo url('/') ?>/web/images/iconUniversity.png";
            iconType['museum'] = "<?php echo url('/') ?>/web/images/iconMuseum.png";
            iconType['hospital'] = "<?php echo url('/') ?>/web/images/iconHospital.png";
            iconType['park'] = "<?php echo url('/') ?>/web/images/iconParks.png";

        var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location,
	  icon: iconType[iconName]
        });

        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent(place.name);
          infowindow.open(map, this);
        });
      }
      
      function changeLoc(type){
         initMap1();
      //  google.maps.event.trigger(map, 'resize');
         function initMap1() {
             
            var latitude=$('#latitude').val();
            var longitude=$('#longitude').val();
         
          
        var pyrmont = {lat: parseFloat(latitude), lng:parseFloat(longitude)};

        map = new google.maps.Map(document.getElementById('map'), {
          center: pyrmont,
          zoom: 15
        });

        infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);
         service.nearbySearch({
          location: pyrmont,
          radius: 1000,
          type: [type]
        }, callbacks);
        
  
      }

      function callbacks(results, status) {
        if (status === google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            createMarkers(results[i]);
          }
        }
      }

      function createMarkers(place) {
        var placeLoc = place.geometry.location;
    
        var iconPoints = ['school','museum','hospital','university','park'];
        var iconName=type;
        
	var iconType = {};
	    iconType['school'] = "<?php echo url('/') ?>/web/images/iconSchool.png";
	    iconType['university'] = "<?php echo url('/') ?>/web/images/iconUniversity.png";
            iconType['museum'] = "<?php echo url('/') ?>/web/images/iconMuseum.png";
            iconType['hospital'] = "<?php echo url('/') ?>/web/images/iconHospital.png";
            iconType['park'] = "<?php echo url('/') ?>/web/images/iconParks.png";

        var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location,
	  icon: iconType[iconName]
        });

        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent(place.name);
          infowindow.open(map, this);
        });
      }
          
      }
      
      $('.recgPoints li').on('click',function(){
      if($('.recgPoints li').hasClass('selected')){
          $('.recgPoints li').removeClass('selected');
      }
      $(this).addClass('selected');
      });
      
    </script>
@endsection