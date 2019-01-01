@extends('layouts.main')
@section('content')
<head>
    <script type='text/javascript' src="{{ URL::asset('js/jquery-migrate.js') }}"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=true&key=AIzaSyDapZQltpUuickj7SRYriyGYK2i6XxruBI" type="text/javascript"></script>
    <script type='text/javascript' src="{{ URL::asset('js/gmaps.js') }}"></script>

</head>

<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Branch</span></h1>
    </header>	

    <form action="" method="post" id="branchinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Choose Company</label>
                        <select class="commoSelect" name="company" id="company">
                            <option >select company</option>
                            @foreach ($companies as $company)
                            <option value='{{ $company->id }}'>{{ $company->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Choose Region</label>
                        <select class="commoSelect" name="region" id="regionselect">
                            <option value="-1">Select Region</option>
                        </select>    
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Branch Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter Name">
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Branch Code</label>
                        <input type="text" name="branch_code" id="branch_code" placeholder="Enter Branch Code">
                    </div>
                </div>
                
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name">
                    </div>
                </div>
                
                <div class="custCol-4">
                    
                </div>

            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="latitude" placeholder="Enter Latitude">

                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="longitude" placeholder="Enter Longiutde">

                    </div>
                </div>

                <div class="custCol-2" id="btnaddmarker">
                    <div class="inputHolder">
                        <label>Locate On map</label>
                        <img src="{{ URL::asset('images/mapIcon.png')}}" alt="Map">                       
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Branch Address</label>
<!--                        <input type="text" name="address" id="address" placeholder="Enter Address">-->
                        <textarea name="address" id="address" placeholder="Enter Address"></textarea>
                    </div>
                </div>
            </div>


            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addbranch">
                </div>
            </div>

            <div class="custRow">

                <div class="custCol-8 mapView">
                    <div class="mapViewDtl">
                        <div class="google-map-wrap" itemscope itemprop="hasMap" itemtype="http://schema.org/Map">
                            <div id="google-map" class="google-map">
                            </div><!-- #google-map -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>	

    <div class="listerType1"> 
        <div class="headingHolder deskCont">
            <ul class="custRow">
                <li class="custCol-1">No</li>
                <li class="custCol-4">Branch Name</li>
                <li class="custCol-4">Alias</li>
            </ul>
        </div>
        <div class="listerV1">
            <?php
            $n = 1;
            $locations = array();
            ?>
            @foreach ($branches as $branch)
            <ul class="custRow">
                <li class="custCol-1"><span class="mobileCont">No</span>{{$n}}</li>
                <li class="custCol-4"><span class="mobileCont">Branch Name</span>{{ $branch->name }}</li>
                <li class="custCol-4"><span class="mobileCont">Alias</span>{{ $branch->alias_name }}</li>
                <li class="custCol-2 btnHolder"><a class="btnAction edit bgRed" href="javascript:void(0)">Edit</a></li>
            </ul>
            <?php
            $n++;


            /* Marker #1 */
            $locations[] = array(
                'google_map' => array(
                    'lat' => $branch->latitude,
                    'lng' => $branch->longitude,
                ),
                'location_address' => $branch->address,
                'location_name' => $branch->address,
            );
            ?>
            @endforeach
        </div>
        <div>
        </div>

    </div>
</div>





<?php
/* Set Default Map Area Using First Location */
$map_area_lat = isset($locations[0]['google_map']['lat']) ? $locations[0]['google_map']['lat'] : '';
$map_area_lng = isset($locations[0]['google_map']['lng']) ? $locations[0]['google_map']['lng'] : '';
?>

<script>
jQuery(document).ready(function ($) {

    /* Do not drag on mobile. */
    var is_touch_device = 'ontouchstart' in document.documentElement;

    var map = new GMaps({
        el: '#google-map',
        lat: '<?php echo $map_area_lat; ?>',
        lng: '<?php echo $map_area_lng; ?>',
        scrollwheel: false,
        draggable: !is_touch_device
    });

    /* Map Bound */
    var bounds = [];

<?php
/* For Each Location Create a Marker. */
foreach ($locations as $location) {
    $name = $location['location_name'];
    $addr = $location['location_address'];
    $map_lat = $location['google_map']['lat'];
    $map_lng = $location['google_map']['lng'];
    ?>
        /* Set Bound Marker */
        var latlng = new google.maps.LatLng(<?php echo $map_lat; ?>, <?php echo $map_lng; ?>);
        bounds.push(latlng);
        /* Add Marker */
        map.addMarker({
            lat: <?php echo $map_lat; ?>,
            lng: <?php echo $map_lng; ?>,
            title: '<?php echo $name; ?>',
            infoWindow: {
                content: '<p><?php echo $name; ?></p>'
            }
        });
<?php } //end foreach locations     ?>

    /* Fit All Marker to map */
    map.fitLatLngBounds(bounds);

    /* Make Map Responsive */
    var $window = $(window);
    function mapWidth() {
        var size = $('.google-map-wrap').width();
        $('.google-map').css({width: size + 'px', height: (size / 2) + 'px'});
    }
    mapWidth();
    $(window).resize(mapWidth);

});
</script>
<div class="commonModalHolder">
    <div class="modalContent">
        <a href="javascript:void(0)" class="btnModalClose">Close(X)</a>


        <div id="map_go" class="mine"></div>
    </div>
</div>
<script>

    directionsService = new google.maps.DirectionsService();
    directionsDisplay = new google.maps.DirectionsRenderer();

    

    var noStreetNames = [{
            featureType: "road",
            elementType: "labels",
            stylers: [{
                    visibility: "off"}]}];

    hideLabels = new google.maps.StyledMapType(noStreetNames, {
        name: "hideLabels"
    });


    var myOptions = {
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: UK
    }

    var showPosition = function (position) {
        var userLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

        var marker = new google.maps.Marker({
            position: userLatLng,
            title: 'Your Location',
            draggable: true,
            map: map
        });

        var infowindow = new google.maps.InfoWindow({
            //content: '<div id="infodiv" style="width: 300px">300px wide infowindow!  if the mouse is not here, will close after 3 seconds</div>'
        });

        google.maps.event.addListener(marker, 'dragend', function () {
            infowindow.open(map, marker)
            map.setCenter(marker.getPosition())
        });

        google.maps.event.addListener(marker, 'mouseover', function () {
            infowindow.open(map, marker)
        });

        google.maps.event.addListener(marker, 'mouseout', function () {
            t = setTimeout(function () {
                infowindow.close()
            }, 3000);
        });

        google.maps.event.addListener(infowindow, 'domready', function () {
            $('#infodiv').on('mouseenter', function () {
                clearTimeout(t);
            }).on('mouseleave', function () {
                t = setTimeout(function () {
                    infowindow.close()
                }, 1000);
            })
        });

        var input = document.getElementById('nptsearch');
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.bindTo('bounds', map);

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            infowindow.close();
            var place = autocomplete.getPlace();
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(7);
            }

            var image = new google.maps.MarkerImage(
                    place.icon, new google.maps.Size(71, 71), new google.maps.Point(0, 0), new google.maps.Point(17, 34), new google.maps.Size(35, 35));
            marker.setIcon(image);
            marker.setPosition(place.geometry.location);

            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);
        });

        map.setCenter(marker.getPosition());
    }

//navigator.geolocation.getCurrentPosition(showPosition);

    map = new google.maps.Map(document.getElementById("map_go"), myOptions);
    directionsDisplay.setMap(map);

    map.mapTypes.set('hide_street_names', hideLabels);

    function offsetCenter(latlng, offsetx, offsety) {
        var scale = Math.pow(2, map.getZoom());
        var nw = new google.maps.LatLng(
                map.getBounds().getNorthEast().lat(), map.getBounds().getSouthWest().lng());

        var worldCoordinateCenter = map.getProjection().fromLatLngToPoint(latlng);
        var pixelOffset = new google.maps.Point((offsetx / scale) || 0, (offsety / scale) || 0)

        var worldCoordinateNewCenter = new google.maps.Point(
                worldCoordinateCenter.x - pixelOffset.x, worldCoordinateCenter.y + pixelOffset.y);

        var newCenter = map.getProjection().fromPointToLatLng(worldCoordinateNewCenter);

        map.setCenter(newCenter);
    }

    function addmarker(latilongi) {

        var marker = new google.maps.Marker({
            position: latilongi,
            title: '',
            draggable: false,
            map: map
        });

        var infowindow = new google.maps.InfoWindow({
            //content: '<div id="infodiv2">infowindow!</div>'
        });
        //map.setZoom(15);
        map.setCenter(marker.getPosition())
        //infowindow.open(map, marker)
    }

    $(window).on('resize', function () {
        var currCenter = map.getCenter();
        google.maps.event.trigger(map, 'resize');
        map.setCenter(currCenter);
    })

    $('#btnlabels').toggle(function () {
        map.setZoom(15);
        map.setMapTypeId('hide_street_names')
    }, function () {
        map.setMapTypeId(google.maps.MapTypeId.ROADMAP)
    })

    $('#btnoffset').on('click', function () {
        offsetCenter(map.getCenter(), 0, -100)
    })

    $('#btnaddmarker').on('click', function () {
        //lat = document.getElementById("latitude");
        var selected_lat = $('#latitude').val();
        var selected_lon = $('#longitude').val();
        var errors = 0;
        if (selected_lat == '') {
            $('#latitude').addClass('valErrorV1');
            $('#latitude').attr("placeholder", "Enter Latitude");
            errors = 1;
        }
        else {
            $('#latitude').removeClass('valErrorV1');
        }
        if (selected_lon == '') {
            $('#longitude').addClass('valErrorV1');
            $('#longitude').attr("placeholder", "Enter Longitude");
            errors = 1;
        }
        else {
            $('#longitude').removeClass('valErrorV1');
        }
        if(errors==1)
        {
            return false;
        }
        var selected_position = new google.maps.LatLng(selected_lat, selected_lon);
        addmarker(selected_position);
        $('.commonModalHolder').show();
        var currCenter = map.getCenter();
        google.maps.event.trigger(map, 'resize');
        map.setCenter(currCenter);
    })

    

    $('.btnModalClose').on('click', function () {
        $('.commonModalHolder').hide()

    })
</script>
@endsection