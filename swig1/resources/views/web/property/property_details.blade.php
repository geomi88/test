@extends('layout.web.website')
@section('content')
@section('title', 'Profile Details')
@section('pageClass', 'detailPage')
@section('innerClass', 'withoutBanner')
@include('web.includes.banner_inner')
<?php
$property=$render_params['details'];
$gallery=$render_params['gallery'];
$sliderGallery=$render_params['sliderGallery'];
$similar=$render_params['similar'];
if($property=="" || $property==NULL){
    
}else{
?>
<style>
    .map{
        height:252px;
        border:0px solid #000;
        max-width:290;
        max-height: 219px;
        // margin-left:135px;
        margin-bottom: 20px;
    }

</style>
<section class="contentWrapper">
    <div class="pageCenter" id="pageCenter">
        <ul class="breadcrumbsHolder">
                            <li>
                                <a href="{{url('/')}}">home <span>/</span></a>
                            </li>
                            <li>
                                <a href="javascript:history.go(-1);">{{__('property')}} <span>/</span></a>
                            </li>
                            <li>
                                <a href="javascript:void();">{{$property->name}}</a>
                            </li>
                        </ul>
        <div class="row">
            <div class="small-12 medium-8 large-8 column headingSection">
                <h2>{{$property->name}}
                <span>{{ $property->address1}}{{ $property->address2}} {{ $property->zip}}{{ $property->municipality}} </span>
                </h2>
            </div>
            <div class="small-12 medium-4 large-4 column">
                <ul class="ratingAndShare">
<!--                    <li class="rating"><img src="{{ URL::asset('web/images')}}/imgRating.png" alt=""></li>-->
                    <li class="share">

                        <!-- Go to www.addthis.com/dashboard to customize your tools -->
                        <div class="addthis_inline_share_toolbox_eno5"></div>
            
                    </li>
                    <li class="print" onclick="window.print()"></li>
                </ul>
            </div>
        </div>
        <div class="detailWrapper">
            <div class="detailGallery">
                  <?php
                         $image = 'default_image/property_default.png';

                             if ($property->mainimage == "" || $property->mainimage == NULL) {
                              } else {
                              $image = url('/') . '/uploads/property-gallery/' . $property->mainimage;
                                 if (!@getimagesize($image)) {
                                  $image = 'default_image/property_default.png';
                                } else { 
                                 }
                              }
                        ?>
                
                
                <div class="galleryLeft" style="background-image: url({{URL::asset($image)}});">
                    <figure><img src="{{ URL::asset($image)}}" alt=""></figure>
                </div>
                <?php if(count($gallery)>0){?>
                <div class="galleryRight">
                    <ul>
                        @foreach($gallery as $galImages)
                       
                        <li>
                             <a href="javascript:void(0);">see all  photos</a>
                            <figure>
                                <img src="{{ URL::asset('uploads/property-gallery')}}/{{$galImages->image}}" alt="">
                            </figure>
                            
                        </li>
                        @endforeach
                    </ul>
                </div>
                <?php } ?>
            </div>
            <div class="galleryBottom">
                <ul>
                    <li class="price">{{env('CURRENCY')}} {{$property->estimated_price}}</li>
                    <li><figure><img src="{{ URL::asset('web/images')}}/iconListBed.png" alt=""></figure><span>{{__('Bedrooms')}} : {{$property->no_of_beds}}</span></li>
                    <li><figure><img src="{{ URL::asset('web/images')}}/iconBathtub.png" alt=""></figure><span>{{__('Bathrooms')}} : {{$property->no_of_baths}}</span></li>
                    <li><figure><img src="{{ URL::asset('web/images')}}/icon360.png" alt=""></figure><span>{{__('Surface')}} : {{$property->total_area}} m²</span></li>
                    <li><figure><img src="{{ URL::asset('web/images')}}/icnTerrace.png" alt=""></figure><span>{{__('Terrace')}} :<?php if($property->terrace=='1'){ echo "Yes"; }else{ echo "No"; } ?></span></li>
                    <li class="btnRequestShow"><a class="btnCommon yellow"  onclick="interestedBtn({{$property->id}})"  href="#interestedForm">{{__('request_showing')}}</a></li>
                </ul>
            </div>
            <div class="detailContent">
                <div class="detailLeft">
                   
                    <?php echo htmlspecialchars_decode($property->description);?>
                    <h5 class="titleDetail">Property details</h5>
                    <div class="detailTabWrapper">
                        <div class="tabHeading">
                            <ul>
                                <li>Reference No : {{$property->reference_number}}</li>
                                <li>Information Obligation</li>
                            </ul>
                            <a class="btnCommon green btnFloorPlanNew" download target="_blank" href="{{URL::asset('uploads/property-plan')}}/{{$property->property_plan}}">Download Floor Plan</a>
                        </div>
                        <div class="" >
                            <div class="propertyDetail">
                                <div class="propertyDetailLeft">
                                    <ul>
                                        <li><span>Planning permits :</span><?php if($property->planning_permits=='1'){ echo "Yes"; }else{ echo "No"; } ?></li>
                                        <li><span>Year Of Construction :</span><?php echo date('Y',strtotime($property->construction_year)); ?></li>
                                        <li><span>Subpoenas :</span><?php if($property->subpoenas=='1'){ echo "Yes"; }else{ echo "No"; } ?></li>
                                    </ul>
                                </div>
                                <div class="propertyDetailRight">
                                    <ul>
                                        <li><span>Pre-emption right  :</span><?php if($property->pre_emption_right=='1'){ echo "Yes"; }else{ echo "No"; } ?></li>
                                        <li><span>Subdivision permit :</span><?php if($property->subdivision_permit=='1'){ echo "Yes"; }else{ echo "No"; } ?></li>
                                        <li><span>Flood area :</span><?php if($property->flood_area=='1'){ echo "Yes"; }else{ echo "No"; } ?></li></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <h5 class="titleDetail">Neighborhood</h5>
                    <div class="neibourhood">
                        <?php if($property->neighborname==""){ echo "No Neighborhood found";}else{ ?>
                         
                        <?php
             
                            $image = url('/').'/default_image/property_default.png';

                            if ($property->neighborImage == "" || $property->neighborImage == NULL) {
                                ?>

                                <?php
                            } else {
                                $image = url('/').'/uploads/neighborhood/' . $property->neighborImage;

                                if (!@getimagesize($image)) {
                                    $image = url('/').'/default_image/property_default.png';
                                } else {
                                   $image =url('/').'/uploads/neighborhood/' . $property->neighborImage;

                                }
                            }
                        ?>
                        <a href="{{ URL::to('web/neighborhood/neighborhood-view', ['id' => Crypt::encrypt($property->neighborId)]) }}">
                        <figure>
                          <img src="{{$image}}" alt="">
                        </figure> </a>
                        <div class="textNeibour">
                            <span>{{$property->neighborname}}</span>
                            <p>
                                <?php
                                $desc=substr($property->neighbordesc,'0','300');
                                echo htmlspecialchars_decode($desc);?>
                                <a href="javascript:void()">Readmore +</a>   
                            </p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
              
                <div class="detailRight">
                    <h5>Location Map</h5>
                    <div class="locationMap">
                       <div class="map" id='map'>
                            
                        </div>

                    <div class="mapHolder">
                       
                    
                    </div>
                        
                        <input type="hidden" name="latitude" id="latitude" value="{{$property->latitude}}">
                        <input type="hidden" name="longitude" id="longitude" value="{{$property->longitude}}">
                        
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
    <div class="row small-up-1 medium-up-3 large-up-3 allListingWrapper eqHeightHolder">
        <h2><span>SIMILAR</span>PROPERTIES</h2>
        
        @foreach($similar as $similarPlot)
        <div class="column allListBoxHolder">
            <?php
                $slug = substr(strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $similarPlot->name)), 0, 25);
            ?>
            <div class="listHandle">
                <a href="{{URL('/')}}/property_view/{{$slug}}/{{$similarPlot->reference_number}}">
                <figure class="listImgHolder">
                    <?php if($similarPlot->mainimage==NULL){ ?>
                    <img src="{{ URL::asset('default_image')}}/property_default.png" alt="Villa with pool for Rent">
                    <?php } else{?>
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
<?php } ?>
 @include('web.includes.our_services')
 <div class="galleryOverlay">
                <a class="btnClose" href="javascript:void()"></a>
            <div class="galleryWrapper"></div>
        </div>

        <div class="galleryContainer">
                <div id="slideshow" class="fullscreen">
                        <a class="btnGalClose" href="javascript:void(0);"></a>
                        @foreach($sliderGallery as $galImages)
                        <div id="img-{{$galImages->id}}" data-img-id="{{$galImages->id}}" class="img-wrapper <?php if($galImages->main_image=='1'){ echo "active" ;} ?>"  style="background-image: url({{ URL::asset('uploads/property-gallery')}}/{{$galImages->image}});"></div>
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
                            <li data-thumb-id="{{$galImages->id}}" class="thumb  <?php if($galImages->main_image=='1'){ echo "active" ;} ?>" style="background-image: url({{ URL::asset('uploads/property-gallery')}}/{{$galImages->image}});"></li>
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
   
                function equalHeight(){
                                        $('.eqHeightHolder').each(function(){  
                                        var highestBox = 0;
                                        $(this).find('.eqHeightInner').each(function(){
                                            if($(this).height() > highestBox){  
                                                highestBox = $(this).height();  
                                            }
                                        })
                                        $(this).find('.eqHeightInner').height(highestBox);
                                    });
                                }
                                
                                $(window).load(function(){
                                    equalHeight();
                                });
 </script>
 
  <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var map;
      var infowindow;
      var points = ['school','museums','hospital','university','park'];
        
      function initMap() {
          var latitude=$('#latitude').val();
          var longitude=$('#longitude').val();
         
          
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
    <script>
    function interestedBtn(id){
        $('#propertyId').val('');
        $('#propertyId').val(id);
     } 
   </script>
   <!-- Go to www.addthis.com/dashboard to customize your tools -->
   <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-507d582c189a3e37"></script>
   <script>
		function printDiv(divName){
			var printContents = document.getElementById(divName).innerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
		}
	</script>                    
@endsection
