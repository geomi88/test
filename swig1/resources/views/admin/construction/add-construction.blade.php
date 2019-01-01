@extends('layout.admin.menu')
@section('content')
@section('title', 'Property List')
<?php
$buildingTypes = $pageData['buildings'];
$neighborhoods = $pageData['neighborhoods'];
$municipalities = $pageData['municipalities'];
$reference_number = $pageData['reference_number'];
$districts = $pageData['district'];
?>
<style>
    .map{
        height:252px;
        border:0px solid #000;
        max-width:255px;
        max-height: 265px;
        // margin-left:135px;
        margin-bottom: 20px;
    }

</style>
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="text-capitalize adminTitle">
					<h1>Add Construction</h1>
				</div>
                            <form method="POST" action="<?php echo url('/'); ?>/admin/save-construction" enctype="multipart/form-data" id="addConstruction" >
                                {{ csrf_field() }}
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
						<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
							<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">EN</a></li>
							<li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">ka</a></li>
							<li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
						</ul>
						<div id="en" class="tabContent">
							<div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">name</label>
								</div>
								<div class="col-9 mb-3 relative">
									<input class="inputStyle" type="text" placeholder="Enter Name" name="name_en" id="name_en">
									
								</div>
								
								<div class="col-3">
									<label class="labelStyle text-capitalize">description</label>
								</div>
								<div class="col-9">
									<textarea  name="description_en" id="description_en" class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
									
								</div>
								
								<div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">surroundings</label>
								</div>
								<div class="col-3">
									<label class="labelStyle text-capitalize">description</label>
								</div>
								<div class="col-9">
									<textarea  name="surrounding_description_en" id="surrounding_description_en"  class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
                                                                        <div class="clearfix"></div>
                                                                </div>
								<div class="col-12 mt-4">
                                                                    <div class="row">
                                        <div class="col-6 clearfix">

                                            <div class="halfWidth mb-3">
                                                <label class="labelStyle">Fetch address from map <span class="redColor">*</span></label>
                                            </div>
                                            <div class="halfWidth mb-3 pl-3">
                                                <div class="mapHolder">
                                                <div id="map" class="map"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 clearfix addressManualHolder">
                                           
                                            <strong>Enter Address Manually</strong>
                                            <div class="clearfix"></div>
                                            <div class="halfWidth mb-3 pl-3">
                                                <label class="labelStyle">Address line 1</label>
                                            </div>
                                            <div class="halfWidth mb-3 relative">
                                                <input class="inputStyle" type="text" name="address_line1_en" id="address_line1_en" placeholder="Address line 1">
                                            </div>
                                            <div class="halfWidth mb-3 pl-3">
                                                <label class="labelStyle">Address line 2</label>
                                            </div>
                                            <div class="halfWidth mb-3 relative">
                                                <input class="inputStyle" type="text" name="address_line2_en" id="address_line2_en" placeholder="Address line 2">
                                            </div>
                                            <div class="halfWidth mb-3 pl-3">
                                                <label class="labelStyle">Zip Code</label>
                                            </div>
                                            <div class="halfWidth mb-3 relative">
                                                <input class="inputStyle" type="text" id="zip_en" name="zip_en" placeholder="zip">
                                            </div>
                                            <div class="halfWidth mb-3 pl-3">
                                                <label class="labelStyle">City</label>
                                            </div>
                                            <div class="halfWidth mb-3 relative munc">
                                                <select name="municipality_en"  id="municipality_en" class="inputStyle chosen-search"> 
                                                    <option value="">Choose</option>
                                                    @foreach($municipalities as $cities)
                                                    <option value="{{$cities->id}}">{{$cities->name}}</option>
                                                    @endforeach
                                                </select>
                <!--										<input class="inputStyle" type="text" name="city_en" id="city_en"  placeholder="city">-->
                                            </div>
                                            <div class="halfWidth mb-3 pl-3">
                                                <label class="labelStyle">Districts</label>
                                            </div>
                                            <div class="halfWidth mb-3 relative dist">
                                                <select name="district_en"  id="district_en" class="inputStyle chosen-search"> 
                                                    <option value="">Choose</option>
                                                    @foreach($districts as $district)
                                                    <option value="{{$district->id}}">{{$district->name}}</option>
                                                    @endforeach
                                                </select>
                <!--										<input class="inputStyle" type="text" name="city_en" id="city_en"  placeholder="city">-->
                                            </div>
                                        </div>
                        
                                                                    </div>
                                                                </div>
								<div class="col-3 mt-3">
									<label class="labelStyle text-capitalize">garden</label>
								</div>
								<div class="col-9 mt-3">
									<textarea   name="garden_description_en" id="garden_description_en"  class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
								<div class="col-3 mt-3">
									<label class="labelStyle text-capitalize">perfect comfort</label>
								</div>
								<div class="col-9 mt-3">
									<textarea  name="comfort_description_en" id="comfort_description_en"  class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
							</div>
							
						</div>
						<div id="ka" class="tabContent">
							<div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">name</label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Enter Name" name="name_ka" id="name_ka">
								</div>
<!--								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">URL</label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Enter Url">
									<span class="fieldInfo">www.luxestate.com</span>
								</div>-->
								<div class="col-3">
									<label class="labelStyle text-capitalize">description</label>
								</div>
								<div class="col-9">
									<textarea rows="4" name="description_ka" id="description_ka" class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
								
								<div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">surroundings</label>
								</div>
								<div class="col-3">
									<label class="labelStyle text-capitalize">description</label>
								</div>
								<div class="col-9">
									<textarea rows="4" name="surrounding_description_ka" id="surrounding_description_ka"  class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
                                                             
                                                                <div class="col-12 mt-2 addressManualHolder">
                                                                    <strong>Enter Address Manually</strong>
                                                                </div>
                                                                <div class="col-3">
                                                                    <label class="labelStyle">Address line 1</label>
                                                                </div>
                                                                <div class="col-9">
                                                                    <input class="inputStyle" type="text" name="address_line1_ka" id="address_line1_ka" placeholder="Address line 1">
                                                                </div>

                                                                <div class="col-3">
                                                                    <label class="labelStyle">Address line 2</label>
                                                                </div>
                                                                <div class="col-9">
                                                                   <input class="inputStyle" type="text" name="address_line2_ka" id="address_line2_ka" placeholder="Address line 2">
                                                                </div>

                                                                <div class="col-3">
                                                                    <label class="labelStyle">Zip Code</label>
                                                                </div>
                                                                <div class="col-9">
                                                                    <input class="inputStyle" type="text" id="zip_ka" name="zip_ka" placeholder="zip">
                                                                </div>
                                                           
                                                                <div class="col-3">
                                                                    <label class="labelStyle">City</label>
                                                                </div>
                                                                <div class="col-9">
                                                                      <select name="municipality_ka"  id="municipality_ka" class="inputStyle chosen-search"> 
                                                                        <option value="">Choose</option>
                                                                        @foreach($municipalities as $cities)
                                                                        <option value="{{$cities->id}}">{{$cities->name}}</option>
                                                                        @endforeach
                                                                     </select>
                                                                </div>

                                                            <div class="col-3">
                                                                    <label class="labelStyle">Districts</label>
                                                                </div>
                                                                <div class="col-9">
                                                                    <select name="district_ka"  id="district_ka" class="inputStyle chosen-search"> 
                                                                        <option value="">Choose</option>
                                                                        @foreach($districts as $district)
                                                                        <option value="{{$district->id}}">{{$district->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                           
								<div class="col-3 mt-3">
									<label class="labelStyle text-capitalize">garden</label>
								</div>
								<div class="col-9 mt-3">
									<textarea rows="4" name="garden_description_ka" id="garden_description_ka" class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
								<div class="col-3 mt-3">
									<label class="labelStyle text-capitalize">perfect comfort</label>
								</div>
								<div class="col-9 mt-3">
									<textarea rows="4" name="comfort_description_ka" id="comfort_description_ka" class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
							</div>
							
						</div>
						<div id="ru" class="tabContent">
							<div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">name</label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Enter Name" name="name_ru" id="name_ru">
								</div>

								<div class="col-3">
									<label class="labelStyle text-capitalize">description</label>
								</div>
								<div class="col-9">
									<textarea rows="4" name="description_ru" id="description_ru"  class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
								
								<div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">surroundings</label>
								</div>
								<div class="col-3">
									<label class="labelStyle text-capitalize">description</label>
								</div>
								<div class="col-9">
									<textarea rows="4" name="surrounding_description_ru" id="surrounding_description_ru" class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
								
                                                            
                                                            
                                                            <div class="col-12 mt-2 addressManualHolder">
                                                                    <strong>Enter Address Manually</strong>
                                                                </div>
                                                                <div class="col-3">
                                                                    <label class="labelStyle">Address line 1</label>
                                                                </div>
                                                                <div class="col-9">
                                                                    <input class="inputStyle" type="text" name="address_line1_ru" id="address_line1_ru" placeholder="Address line 1">
                                                                </div>
                                                            
                                                                <div class="col-3">
                                                                    <label class="labelStyle">Address line 2</label>
                                                                </div>
                                                                <div class="col-9">
                                                                    <input class="inputStyle" type="text" name="address_line2_ru" id="address_line2_ru" placeholder="Address line 1">
                                                                </div>
                                                            
                                                                <div class="col-3">
                                                                    <label class="labelStyle">Zip Code</label>
                                                                </div>
                                                                <div class="col-9">
                                                                    <input class="inputStyle" type="text" name="zip_ru" id="zip_ru" placeholder="Address line 1">
                                                                </div>
                                                            
                                                                <div class="col-3">
                                                                    <label class="labelStyle">City</label>
                                                                </div>
                                                                <div class="col-9">
                                                                <select name="municipality_ru"  id="municipality_ru" class="inputStyle chosen-search"> 
                                                                    <option value="">Choose</option>
                                                                    @foreach($municipalities as $cities)
                                                                    <option value="{{$cities->id}}">{{$cities->name}}</option>
                                                                    @endforeach
                                                                </select>  
                                                                </div>
                                                            
                                                                <div class="col-3">
                                                                    <label class="labelStyle">Districts</label>
                                                                </div>
                                                                <div class="col-9">
                                                                <select name="district_ru"  id="district_ru" class="inputStyle chosen-search"> 
                                                                        <option value="">Choose</option>
                                                                        @foreach($districts as $district)
                                                                        <option value="{{$district->id}}">{{$district->name}}</option>
                                                                        @endforeach
                                                                </select>
                                                                </div>
                                                            
								<div class="col-3 mt-3">
									<label class="labelStyle text-capitalize">garden</label>
								</div>
								<div class="col-9 mt-3">
									<textarea rows="4" name="garden_description_ru" id="garden_description_ru" class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
								<div class="col-3 mt-3">
									<label class="labelStyle text-capitalize">perfect comfort</label>
								</div>
								<div class="col-9 mt-3">
									<textarea rows="4" name="comfort_description_ru" id="comfort_description_ru" class="inputStyle ckeditor" placeholder="Enter Description"></textarea>
								</div>
							</div>
							
						</div>
                                            <div class="row">
                                            <div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">information obligation</label>
								</div>
                                                
                                                                <div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">Project URL</label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" type="text" placeholder="Enter Url" name="url" id="url">
									<span class="fieldInfo">.luxestate.com</span>
								</div>
                                                              <div class="col-3 mb-3">
                                                                    <label class="labelStyle text-capitalize">Estimated Price</label>
                                                                </div>
                                                                <div class="col-9 mb-3 mt-2">
                                                                 <input class="inputStyle" type="text" name="price" id="price" placeholder="Estimated Price" >
                                          
                                                                </div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">urban planning permits</label>
								</div>
								<div class="col-9 mb-3 mt-2">
									<div class="radioCheckStyleHolder float-left text-capitalize">
										  <input id="planning_permits1" name="planning_permits" type="radio" value="1">
                                                                                  <label for="planning_permits1"><span>yes</span></label>
									</div>
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="planning_permits2" name="planning_permits" type="radio" value="0">
                                                                                <label for="planning_permits2"><span>no</span></label>
									</div>
									
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">destination</label>
								</div>
								<div class="col-9 mb-3">
									<input class="inputStyle" name="destination" id="destination" type="text" placeholder="Residential Area">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">subpoenas</label>
								</div>
								<div class="col-9 mb-3 mt-2">
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="subpoenas1" name="subpoenas" type="radio" value="1">
                                                                                <label for="subpoenas1"><span>yes</span></label>
									</div>
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="subpoenas2" name="subpoenas" type="radio" value="0">
                                                                                <label for="subpoenas2"><span>no</span></label>
									</div>
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">judicial sayings</label>
								</div>
								<div class="col-9 mb-3 mt-2">
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="judicial_sayings1" name="judicial_sayings" type="radio" value="1">
                                                                                <label for="judicial_sayings1"><span>yes</span></label>
									</div>
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="judicial_sayings2" name="judicial_sayings" type="radio" value="0">
                                                                                <label for="judicial_sayings2"><span>no</span></label>
									</div>
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">pre-emption right</label>
								</div>
								<div class="col-9 mb-3 mt-2">
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="pre_emption_right1" name="pre_emption_right" type="radio" value="1">
                                                                                <label for="pre_emption_right1"><span>yes</span></label>
									</div>
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="pre_emption_right2" name="pre_emption_right" type="radio" value="0">
                                                                                <label for="pre_emption_right2"><span>no</span></label>
									</div>
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">sub-division permit</label>
								</div>
								<div class="col-9 mb-3 mt-2">
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="subdivision_permit1" name="subdivision_permit" type="radio" value="1">
                                                                                <label for="subdivision_permit1"><span>yes</span></label>
									</div>
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="subdivision_permit2" name="subdivision_permit" type="radio" value="0">
                                                                                <label for="subdivision_permit2"><span>no</span></label>
									</div>
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">flood area</label>
								</div>
								<div class="col-9 mb-3 mt-2">
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="flood_area1" name="flood_area" type="radio" value="1">
                                                                                <label for="flood_area1"><span>yes</span></label>
									</div>
									<div class="radioCheckStyleHolder float-left text-capitalize">
										<input id="flood_area2" name="flood_area" type="radio" value="0">
                                                                                <label for="flood_area2"><span>no</span></label>
									</div>
								</div>
                                                                <div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">upload floor plan</label>
								</div>
								<div class="col-9 mb-3">
									<input class="custmFileInput" id="floorplan" name="floorplan" type="file">
									<label for="floorplan" class="customFileUpload text-lowercase mt-1">Upload File</label>
								</div>
                                        </div>
                                            <div class="mt-2">
								<button id="btnsaveProperty" type="button" class="btnStyle mr-1">Save</button>
								<a href="{{ URL::to('admin/construction-listing') }}"><button type="button" class="cancelBtnStyle">Cancel</button></a>
       
							</div>
                                            <input type="hidden" id="latitude" name="latitude" />
                                            <input type="hidden" id="longitude" name="longitude" />
					</div>
                                    
				</div>
                        </form>
			</div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE"></script>

<script>
    
    $(document).ready(function () {
         
         $('#description_en').ckeditor();
         $('#description_ka').ckeditor(); 
         $('#description_ru').ckeditor();
         
         $('#garden_description_en').ckeditor();
         $('#garden_description_ka').ckeditor();
         $('#garden_description_ru').ckeditor();
         
         $('#comfort_description_en').ckeditor();
         $('#comfort_description_ka').ckeditor();
         $('#comfort_description_ru').ckeditor();
         
         $('#surrounding_description_en').ckeditor();
         $('#surrounding_description_ka').ckeditor();
         $('#surrounding_description_ru').ckeditor();
         
        geolocator(); 
    });
    //Google location

    var Lat = "41.716667";
    var Lng = "44.783333";

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

    function displayLocation(latitude, longitude) {
        var geocoder;
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(latitude, longitude);
        geocoder.geocode(
                {'latLng': latlng},
        function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                
                if (results[0]) {
                   
                    var add = results[0].address_components;
                    var dist = results[0].address_components.length;
                    var distKey=dist-1;
                    var muncKey=distKey-1;
                   
                    var div = document.createElement('div');
                    /*$('.munc').html("");
                    $('.munc').html("<input class='inputStyle' type='text' id='municipality_en' name='municipality_en' placeholder='Municipality'>" );
                    $('.dist').html("");
                    $('.dist').html("<input class='inputStyle' type='text' id='district_en' name='district_en' placeholder='District'>");
                    */
                   if (add[0] != null) {
                        document.getElementById('address_line1_en').value = add[0].long_name;
                    }
                    else {
                        document.getElementById('address_line1_en').value = "";
                    }
                    if (add[1] != null) {
                        document.getElementById('address_line2_en').value = add[1].long_name;
                    }
                    else {
                        document.getElementById('address_line2_en').value = "";
                    }
                    /*if (add[muncKey] != null) {
                        document.getElementById('municipality_en').value = add[muncKey].long_name;
                    }
                    else {
                        document.getElementById('municipality_en').value = "";
                    } 
                    if (add[distKey] != null) {
                      //  document.getElementById('district_en').value = add[distKey].long_name;
                    }
                    else {
                      //  document.getElementById('district_en').value = "";
                    }*/

                }
                else {
                    alert("address not found");
                }
            }
            else {
                alert("Geocoder failed due to: " + status);
            }
        }
        );
    }

</script>
<script>
 $('#btnsaveProperty').click(function () {
                           if (!$("#addConstruction").valid()) {
                                return false;
                            }

                            $("#btnsaveProperty").attr("disabled", "disabled");
                           $("#addConstruction").submit();
                        });
  
  
  
                        $.validator.addMethod("PropertyNameLang", function (value, element) {

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
                        

                    $.validator.addMethod("gardendescription", function (value, element) {

                                           var garden_description_ka = $('#garden_description_ka').val();
                                           var garden_description_ru = $('#garden_description_ru').val();
                                           if (garden_description_ka == "" || garden_description_ru == "") {
                                               return false;
                                           }
                                           else {
                                               return true;
                                           }


                                       }, 'Please check garden description in Russian and Georgian');

                            $.validator.addMethod("surrounddescription", function (value, element) {

                                                   var surrounding_description_ka = $('#surrounding_description_ka').val();
                                                   var surrounding_description_ru = $('#surrounding_description_ru').val();
                                                   if (surrounding_description_ka == "" || surrounding_description_ru == "") {
                                                       return false;
                                                   }
                                                   else {
                                                       return true;
                                                   }


                                               }, 'Please check surrounding description in Russian and Georgian');
                        
                            $.validator.addMethod("comfortdescription", function (value, element) {

                                                    var comfort_description_ka = $('#comfort_description_ka').val();
                                                    var comfort_description_ru = $('#comfort_description_ru').val();
                                                    if (comfort_description_ka == "" || comfort_description_ru == "") {
                                                        return false;
                                                    }
                                                    else {
                                                        return true;
                                                    }


                                                }, 'Please check comfort description in Russian and Georgian');

    
        $("#addConstruction").validate({
                            errorElement: 'span',
                            errorClass: "errorMsg",
                            ignore: [],
                            highlight: function (element, errorClass) {
                                $(element).addClass('valErrorV1');
                                $("#" + element.id + "_chosen").find('.chosen-single').addClass('valErrorV1');
                            },
                            unhighlight: function (element, errorClass, validClass) {
                                $(element).removeClass('valErrorV1');
                            },
                            rules: {
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
                                   // description: true,
                                },
                                  garden_description_en: {
                                    required: true,
                                    gardendescription: true,
                                },
                                surrounding_description_en: {
                                    required: true,
                                    surrounddescription: true,
                                },
                                comfort_description_en: {
                                    required: true,
                                   comfortdescription: true,
                                },
                               description_ka: {
                                    required: true,
                                },
                                description_ru: {
                                    required: true,
                                },
                               
                                garden_description_ka: {
                                    required: true,
                                },
                                garden_description_ru: {
                                    required: true,
                                },
                               
                                surrounding_description_ka: {
                                    required: true,
                                },
                                surrounding_description_ru: {
                                    required: true,
                                },
                                
                               comfort_description_ka: {
                                    required: true,
                                },
                                comfort_description_ru: {
                                    required: true,
                                },
                                address_line1_en: {
                                    required: true,
                                   // AddressLine1: true,
                                },
                               
                                address_line2_en: {
                                    required: true,
                                   // AddressLine2: true,
                                },
                                
                               zip_en: {
                                    required: true,
                                    //zip: true,
                                },
                                
                                municipality_en: {
                                    required: true,
                                },
                                price: {
                                    required: true,
                                },
                                
                                url:{
                                   required: true,
                                   maxlength:10,
                                   remote: {
                                        url: "<?php echo url('/')?>/admin/construction/checkProjectName",
                                        type: "post",
                                        data:
                                            {
                                                projectUrl: function () {
                                                        return $.trim($("#url").val());
                                                },
                                               
                                                },
                                                dataFilter: function (data)
                                                   {
                                                    
                                                       if (data == 1) {
                                                           return false;
                                                       } else {
                                                           return true;
                                                       }
                                                   }
                                            },
                                },
                                
                                destination: {
                                    required: true,
                                },
                                district_en: {
                                    required: true,
                                },
                                property_plan: {
                                    // required:true,
                                    // extension: "docx|rtf|doc|pdf",
                                    extension: "pdf",
                                }
                                },
                            messages: {
                               
                                name_en: {
                                    // required: "Please check the property name in English",
                                    required: "Please check name in English,Georgian and Russian",
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
                               
                                price: {
                                    required: "Please enter the price",
                                },
                               
                              
                                url:{
                                required: "Please enter the url",
                                remote:"URL already exists"
                            
                                },
                                description_en: {
                                     required: "Please enter the description ",
                                     description: "Please enter the description in Georgian and Russian",
                                   
                                },
                                garden_description_en: {
                                    required: "Please enter the garden description ",
                                    gardendescription: "Please check the comfort description in Georgian and Russian",
                                },
                                surrounding_description_en: {
                                    required: "Please enter the surrounding description ",
                                    surrounddescription: "Please check the comfort description in Georgian and Russian",
                                },
                                comfort_description_en: {
                                    required: "Please enter the comfort description ",
                                    comfortdescription: "Please check the comfort description in Georgian and Russian",
                                },
                            },
                        });

</script>
	@endsection		