@extends('layouts.main')
@section('content')
<head>
    <script type='text/javascript' src="{{ URL::asset('js/jquery-migrate.js') }}"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=true&key=AIzaSyDapZQltpUuickj7SRYriyGYK2i6XxruBI" type="text/javascript"></script>
    <script type='text/javascript' src="{{ URL::asset('js/gmaps.js') }}"></script>

</head>

<script>
$(document).ready(function ()
{
    $("#branchinsertion").validate({
        errorElement: "span",
        errorClass: "commonError",
        highlight: function (element, errorClass) {
            $(element).addClass('valErrorV1');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass("valErrorV1");
        },
        rules: {
            branch_code: {
                required: {
                    depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                },
                remote:
                        {
                            url: "../checkbranchcode",
                            type: "post",
                            data:
                                    {
                                        branch_code: function () {
                                            return $.trim($("#branch_code").val());
                                        },
                                        cid: function () {
                                            return $("#cid").val();
                                        }
                                    },
                            dataFilter: function (data)
                            {
                                var json = JSON.parse(data);
                                if (json.msg == "true") {
                                    // return "\"" + "That Company name is taken" + "\"";
                                    $('.branchCode').addClass('ajaxLoaderV1');
                                    $('.branchCode').removeClass('validV1');
                                    $('.branchCode').addClass('errorV1');
                                    document.getElementById("#branch_code-error").style.display = "none"


                                    // valid="false"
                                }
                                else
                                {
                                    $('.branchCode').addClass('ajaxLoaderV1');
                                    $('.branchCode').removeClass('errorV1');
                                    $('.branchCode').addClass('validV1');

                                    //valid="true";
                                    return true;
                                }
                            }
                        }

            },
            'job_shifts[]': {
                required: true,
                maxlength: 2
            },
            latitude: {required: true},
            longitude: {required: true},
            name: {required: true},
            area: {required: true},
            start_date: {required: true},
            bottom_sale_line: {required: true, number: true},
            branch_phone: {
                    required: {
                                depends: function () {
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            },
                    number: true
                },
        },
        
        messages: {
            name: "Enter Branch name",
            latitude: "Enter Latitude",
            longitude: "Enter Longitude",
            branch_code: "Enter Branch Code",
            area: "Select Area",
            start_date: "Select Branch Start Date",
             'job_shifts[]': {
                required: "Please Select Shifts",
                maxlength: "Please Select Only 2 Shifts"
            },
            bottom_sale_line: {required: "Enter Bottom Sale Line", number: "Enter Numbers Only"},
            branch_phone: {
                    required: "Enter Contact Number",
                    number: "Enter Number Only"
                   
                },
        },
    });

        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
        });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Branch</span></h1>
    </header>	

    <form action="{{ action('Masterresources\BranchController@update') }}"  method="post" id="branchinsertion">
        <div class="fieldGroup branchEdit" id="fieldSet1">
            <div class="custRow">    
                <input type="hidden" name="cid" id="cid" placeholder="Enter Name" value="{{ $branch->id}}">                   
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="arealist">
                        <label>Choose Area</label>
                        <select class="commoSelect" name="area" id="editarea">                                                  
                            @foreach ($areas as $area)                  
                            <option <?php echo ($area->id == $branch->area_id) ? "selected" : "" ?> value='{{ $area->id }}' >{{ $area->name}}</option>
                            @endforeach     
                        </select>    
                    </div>
                </div>
            </div>
            <div class="custRow ">
                <div class="custCol-4">
                   <div class="inputHolder" style="text-align: left;">
                        <label>Select Shifts</label>  
                            <?php
                        $array = explode(',', $branch->shift_id);
                        ?>
                        @foreach ($job_shifts as $job_shift)
                        <?php
                        if (in_array($job_shift->id, $array)) {

                            echo '<input name="job_shifts[]" class="shifts" type="checkbox" value="' . $job_shift->id . '" checked="checked"><em>' . $job_shift->name.'</em>' ;
                        } else {

                            echo '<input name="job_shifts[]" class="shifts" type="checkbox" value="' . $job_shift->id . '"><em>' . $job_shift->name.'</em>' ;
                        }
                        ?>
                        @endforeach  
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Branch Name</label>
                        <input type="hidden" id="cid" name="cid" value="{{ $branch->id }}">
                        <input type="text" name="name" id="name" placeholder="Enter Name" value="{{ $branch->name}}" maxlength="150">
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder branchCode">
                        <label>Branch Code</label>
                        <input type="text" name="branch_code" id="branch_code" placeholder="Enter Branch Code" maxlength="100" value="{{ $branch->branch_code}}">
                    </div>
                </div>
                
                <div class="custCol-4">
                <div class="inputHolder">
                    <label>Branch Contact No</label>
                    <input type="text" name="branch_phone" id="branch_phone" value="{{ $branch->branch_phone}}" autocomplete="off" placeholder="Enter Branch Contact No" maxlength="100">
                    <span class="commonError"></span>
                </div>
            </div>
                </div>
                 <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Opening Change Fund</label>
                            <input type="text" name="fund" id="fund" placeholder="Enter Opening Change Fund" value="{{ $branch->opening_fund}}">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                     <div class="custCol-4">
                <div class="inputHolder">
                    <label>&nbsp;</label>
                    <input type="checkbox" name="fund_editable" id="fund_editable" style="width: 15px;height: 33px;display: inline-block;" <?php if($branch->opening_fund_editable== 1){ echo  "checked";} ?> value="1">                   
                   <font style="font-size: 16px; vertical-align: top;padding: 0 10px 0 10px;vertical-align: top;">Opening Fund Non Editable</font>
                     <span class="commonError"></span>
                </div>
            </div>
                </div>
                <div class="custRow">
					<div class="custCol-4">
                        <div class="inputHolder">
                            <label>Bottom Sale Line (Per Month)</label>
                            <input type="text"  name="bottom_sale_line_month" id="bottom_sale_line_month" value="<?php echo ($branch->bottom_sale_line);?>">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Bottom Sale Line (Per Day)</label>
                            <input type="text" readonly name="bottom_sale_line" id="bottom_sale_line" placeholder="Enter Bottom Sale Line" value="<?php echo number_format($branch->bottom_sale_line/30, 2, '.', '');?>">
                            <span class="commonError"></span>
                        </div>
                    </div>                    
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Alias</label>
                            <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name" value="{{ $branch->alias_name}}">
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Branch Start Date</label>
                            <input type="text" name="start_date" placeholder="Select Branch Start Date" id="start_date" readonly="readonly" value="<?php if($branch->branch_start_date!=null){echo date("d-m-Y", strtotime($branch->branch_start_date));}?>">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    </div>
               

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="latitude" placeholder="Enter Latitude" value="{{ $branch->latitude}}">

                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect" id="branchregionlist">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="longitude" placeholder="Enter Longiutde" value="{{ $branch->longitude}}">

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
                        <textarea name="address" id="address" placeholder="Enter Address" >{{ $branch->address}}</textarea>
                    </div>
                </div>
            </div>


            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn addbranch">
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


</div>
<div class="listerV1">
    <?php
    $locations = array();
    ?>
    @foreach ($branchmaps as $branchmap)
    <?php
    /* Marker #1 */
    $locations[] = array(
        'google_map' => array(
            'lat' => $branchmap->latitude,
            'lng' => $branchmap->longitude,
        ),
        'location_address' => $branchmap->address,
        'location_name' => $branchmap->address,
    );
    ?>
    @endforeach
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
    if ($map_lat != '' && $map_lng != '') {
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
        <?php
    }
}//end foreach locations     
?>

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
        //center: UK
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
        if (errors == 1)
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
    /*$("#bottom_sale_line").keyup(function () {
            var bottom_sale_line = parseInt($('#bottom_sale_line').val());
            if (isNaN(bottom_sale_line)) 
            { 
                bottom_sale_line_month= 0;
            }
               var bottom_sale_line_month = parseInt(bottom_sale_line) * 30;

                $('#bottom_sale_line_month').val(bottom_sale_line_month);
                
        });
		*/
		$("#bottom_sale_line_month").keyup(function () {
            var bottom_sale_line_month = parseInt($('#bottom_sale_line_month').val());
            if (isNaN(bottom_sale_line)) 
            { 
                bottom_sale_line= 0;
            }
               var bottom_sale_line = parseFloat(bottom_sale_line_month) / 30;
               
               if(isNaN(bottom_sale_line)){
                   
                    $('#bottom_sale_line').val("");
                   
               }else{

                $('#bottom_sale_line').val(bottom_sale_line.toFixed(2));
            }  
        });
</script>
@endsection