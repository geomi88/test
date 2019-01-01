@extends('layout.admin.menu')
@section('content')
@section('title', 'Property Edit')
<?php
$buildingTypes = $pageData['buildings'];
$neighborhoods = $pageData['neighborhoods'];
$municipalities = $pageData['municipalities'];
$districts = $pageData['district'];
$property = $pageData['property'];
$gallery = $pageData['gallery'];

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
					<h1>Edit property details</h1>
				</div>
                        <form method="POST" action="<?php echo url('/'); ?>/admin/save-property-updates" enctype="multipart/form-data" id="addproperty" >
                            {{ csrf_field() }}
				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2 class="mt-2">basic information</h2>
						</div>
						<div class="col-6 text-right">
							<select class="inputStyle" id="propertyType" name="propertyType">
								<option value="0">Choose Type</option>
								<option value="1" <?php if ($property->tenure_type == '1') { echo "selected";}?> >Sale</option>
								<option value="2" <?php if ($property->tenure_type == '2') { echo "selected";}?> >Rent</option>
								
							</select>
						</div>
					</div>
					<hr>
					<div class="row text-capitalize">
						<div class="col-6">
							<div class="halfWidth mb-3">
								<label class="labelStyle">type of building<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<select id="buildingType" name="buildingType" class="inputStyle chosen-search">
                                                                    <option value="">Choose</option>
                                                                    @foreach($buildingTypes as $buildings)
									<option value="{{$buildings->id}}" <?php if ($property->building_type == $buildings->id) { echo "selected";}?>>{{$buildings->name}}</option>
                                                                    @endforeach
								</select>
							</div>
							<div class="clearfix"></div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Estimated Price<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" name="estimated_price" id="estimated_price"  type="text" placeholder="Estimated Price" value="{{$property->estimated_price}}">
							</div>
						</div>
						<div class="col-6">
							<div class="halfWidth mb-3 pl-3">
								<label class="labelStyle">Reference Number<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3">
                                                            <input class="inputStyle" name="reference_num" id="reference_num"  type="text" placeholder="Reference number" value="{{$property->reference_number}}" readonly="true">
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>	
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
						<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
							<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">en</a></li>
							<li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">ka</a></li>
							<li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
						</ul>
						<div id="en" class="tabContent">
							
							<div class="row">
								<div class="col-6 clearfix">
                                                                    <div class="halfWidth mb-3">
                                                                        <label class="labelStyle">Property Name<span class="redColor">*</span></label>
                                                                    </div>
                                                                    <div class="halfWidth mb-3 pr-3">
                                                                        <input class="inputStyle" name="name_en" id="name_en" type="text" placeholder="Property Name" value="{{$property->name_en}}">
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <div class="halfWidth mb-3">
                                                                        <label class="labelStyle">Fetch address from map <span class="redColor">*</span></label>
                                                                    </div>
                                                                    <div class="halfWidth mb-3 pr-3">
                                                                        <div class="mapHolder">
                                                                        
                                                                          <div id="map" class="map"></div>      
                                                                        
                                                                        </div>
                                                                       
                                                                    </div>
								</div>
								<div class="col-6 clearfix addressManualHolder">
									<div class="orHolder"><span>OR</span></div>
									<strong>Enter Address Manually</strong>
									<div class="clearfix"></div>
									<div class="halfWidth mb-3 pl-3">
										<label class="labelStyle">Address line 1</label>
									</div>
									<div class="halfWidth mb-3">
										<input class="inputStyle" type="text" name="address_line1_en" id="address_line1_en" placeholder="Address line 1" value="{{$property->address_line1_en}}">
									</div>
									<div class="halfWidth mb-3 pl-3">
										<label class="labelStyle">Address line 2</label>
									</div>
									<div class="halfWidth mb-3">
										<input class="inputStyle" type="text" name="address_line2_en" id="address_line2_en" placeholder="Address line 2" value="{{$property->address_line2_en}}">
									</div>
									<div class="halfWidth mb-3 pl-3">
										<label class="labelStyle">Zip Code</label>
									</div>
									<div class="halfWidth mb-3">
										<input class="inputStyle" type="text" id="zip_en" name="zip_en" placeholder="zip" value="{{$property->zip_en}}">
									</div>
									<div class="halfWidth mb-3 pl-3">
										<label class="labelStyle">City</label>
									</div>
									<div class="halfWidth mb-3 munc">
                                                                            <select name="municipality_en"  id="municipality_en" class="inputStyle chosen-search"> 
                                                                                <option value="">Choose</option>
                                                                                @foreach($municipalities as $cities)
                                                                                <option value="{{$cities->id}}" <?php if ($property->municipality_id == $cities->id) { echo "selected";}?>>{{$cities->name}}</option>
                                                                                @endforeach
                                                                            </select>
<!--										<input class="inputStyle" type="text" name="city_en" id="city_en"  placeholder="city">-->
									</div>
                                                                        <div class="halfWidth mb-3 pl-3">
										<label class="labelStyle">Districts</label>
									</div>
                                                                        <div class="halfWidth mb-3 dist">
                                                                            <select name="district_en"  id="district_en" class="inputStyle chosen-search"> 
                                                                                <option value="">Choose</option>
                                                                                @foreach($districts as $district)
                                                                                <option value="{{$district->id}}"  <?php if ($property->district_id == $district->id) { echo "selected";}?>>{{$district->name}}</option>
                                                                                @endforeach
                                                                            </select>
<!--										<input class="inputStyle" type="text" name="city_en" id="city_en"  placeholder="city">-->
									</div>
								</div>
								<div class="col-12">
									<label class="labelStyle text-capitalize">description</label>
									<!--description editior wrapper-->
                                                                        <div class="descriptionEditor">
                                                                            <textarea class="ckeditor" name="description_en" id="description_en">{{$property->description_en}}</textarea>
                                                                            
                                                                            
                                                                        </div>
								</div>
							</div>
						</div>
						<div id="ka" class="tabContent">
							
                                                            <div class="row">
								<div class="col-6 clearfix">
                                                                    <div class="halfWidth mb-3">
										<label class="labelStyle">Property Name<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
										<input class="inputStyle" type="text" placeholder="Property Name" id="name_ka" name="name_ka" value="{{$property->name_ka}}">
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Enter Address Manually </label>
									</div>
									<div class="halfWidth mb-3 pr-3">
<!--										<div class="mapHolder">
											<iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d15781.46719815546!2d76.87786109999999!3d8.56068315!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1541137212383" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
										</div>-->
									</div> 
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Address line 1 <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                            <input class="inputStyle" type="text" placeholder="Address line 1" id="address_line1_ka" name="address_line1_ka" value="{{$property->address_line1_ka}}">
									
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Address line 2 <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                            <input class="inputStyle" type="text" placeholder="Address line 2" id="address_line2_ka" name="address_line2_ka" value="{{$property->address_line2_ka}}">
									
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Zip Code<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                           <input class="inputStyle" type="text" placeholder="zip" id="zip_ka" name="zip_ka" value="{{$property->zip_ka}}">
									
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">City<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                          <select name="municipality" id="municipality_ka" class="inputStyle chosen-search">
                                                                                <option value="">Choose</option>
                                                                                @foreach($municipalities as $cities)
                                                                                <option value="{{$cities->id}}"  <?php if ($property->municipality_id == $cities->id) { echo "selected";}?>>{{$cities->name}}</option>
                                                                                @endforeach
                                                                            </select>
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">District<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                          <select name="district_ka"  id="district_ka" class="inputStyle chosen-search"> 
                                                                                <option value="">Choose</option>
                                                                                @foreach($districts as $district)
                                                                                <option value="{{$district->id}}"  <?php if ($property->district_id == $district->id) { echo "selected";}?> >{{$district->name}}</option>
                                                                                @endforeach
                                                                            </select>
									</div>
								</div>
								
								<div class="col-12">
									<label class="labelStyle text-capitalize">description</label>
									<!--description editior wrapper-->
                                                                        <div class="descriptionEditor">
                                                                           <textarea class="ckeditor" name="description_ka" id="description_ka">{{$property->description_ka}}</textarea>
                                                                        </div>
								</div>
							</div>
						</div>
						<div id="ru" class="tabContent">
							
							<div class="row">
								
								<div class="col-6 clearfix">
                                                                    <div class="halfWidth mb-3">
										<label class="labelStyle">Property Name<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
										<input class="inputStyle" type="text" placeholder="Property Name" id="name_ru" name="name_ru" value="{{$property->name_ru}}">
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Enter Address Manually </label>
									</div>
									<div class="halfWidth mb-3 pr-3">
<!--										<div class="mapHolder">
											<iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d15781.46719815546!2d76.87786109999999!3d8.56068315!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1541137212383" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
										</div>-->
									</div> 
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Address line 1 <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                            <input class="inputStyle" type="text" placeholder="Address line 1" id="address_line1_ru" name="address_line1_ru" value="{{$property->address_line1_ru}}">
									
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Address line 2 <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                            <input class="inputStyle" type="text" placeholder="Address line 2" id="address_line2_ru" name="address_line2_ru" value="{{$property->address_line2_ru}}">
									
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Zip Code<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                           <input class="inputStyle" type="text" placeholder="zip" id="zip_ru" name="zip_ka" value="{{$property->zip_ru}}">
									
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">City<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                          <select name="municipality" id="municipality_ru[]" class="inputStyle chosen-search">
                                                                                <option value="">Choose</option>
                                                                                @foreach($municipalities as $cities)
                                                                                <option value="{{$cities->id}}"  <?php if ($property->municipality_id == $cities->id) { echo "selected";}?>>{{$cities->name}}</option>
                                                                                @endforeach
                                                                            </select>
									</div>
                                                                    <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">District<span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 pr-3">
                                                                          <select name="district_ru"  id="district_ru[]" class="inputStyle chosen-search"> 
                                                                                <option value="">Choose</option>
                                                                                @foreach($districts as $district)
                                                                                <option value="{{$district->id}}"  <?php if ($property->district_id == $district->id) { echo "selected";}?> >{{$district->name}}</option>
                                                                                @endforeach
                                                                            </select>
									</div>
								</div>
								
								<div class="col-12">
									<label class="labelStyle text-capitalize">description</label>
									<!--description editior wrapper-->
                                                                        <div class="descriptionEditor">
                                                                            <textarea class="ckeditor" name="description_ru" id="description_ru">{{$property->description_ru}}</textarea>
                                                                        </div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="mainBoxHolder">
					<div class="text-capitalize">
						<h2>description & features</h2>
					</div>
					<hr>
					<div class="row text-capitalize">
						<div class="col-6 clearfix mt-3">
                                                    <div class="halfWidth mb-3">
                                                        <label class="labelStyle">New Property<span class="redColor">*</span></label>
                                                    </div>
                                                    <div class="halfWidth mb-2 pr-3 relative">
                                                        <div class="checkboxStyleHolder mt-2">
                                                            <input id="new_property" name="new_property" type="checkbox" <?php if($property->new_property=='1'){ echo "checked"; } ?> value='1'>
                                                            <label for="new_property"><span></span></label>
                                                        </div>
                                                    </div>
                                                 <div class="clearfix"></div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Total area<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" type="text" placeholder="Total area" id="total_area" name="total_area" value="{{$property->total_area}}">
							</div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Habitable area<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" type="text" placeholder="Habitable area" id="habitable_area" name="habitable_area" value="{{$property->habitable_area}}">
							</div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Number of Baths<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" type="number" placeholder="Number of Baths" min="0" id="no_of_baths" name="no_of_baths" value="{{$property->no_of_baths}}">
							</div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Number of Garages<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" type="number" placeholder="Number of Garages" min="0" id="no_of_garages" name="no_of_garages" value="{{$property->no_of_garages}}">
							</div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Underground parking<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mt-2 mb-3 pr-3">
								<div class="radioCheckStyleHolder float-left">
                                                                        <input id="parking1" name="parking" type="radio" <?php if($property->underground_parking=="1"){ echo "checked";} ?>>
									<label for="parking1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="parking2" name="parking" type="radio" <?php if($property->underground_parking=="0"){ echo "checked";} ?>>
									<label for="parking2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Availability<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
                                                            <input class="inputStyle  datepicker" type="text" id="availability" name="availability" value="<?php echo date('d-M-Y',strtotime($property->availability))?>">
							
                                                        </div>
						</div>
						<div class="col-6 clearfix mt-3">
							<div class="halfWidth mb-3">
								<label class="labelStyle">Number of floors<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" type="number" placeholder="Number of Floors" min="0" id="no_of_floors" name="no_of_floors" value="{{$property->no_of_floors}}">
							</div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Number of  Beds<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" type="number" placeholder="Number of Beds" min="0" id="no_of_beds" name="no_of_beds" value="{{$property->no_of_beds}}">
							</div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Number of Balcony<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle" type="number" placeholder="Number of Balcony" min="0" id="no_of_balcony" name="no_of_balcony" value="{{$property->no_of_balcony}}">
							</div>
                                                        <div class="halfWidth mb-3">
								<label class="labelStyle">Year of construction<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<input class="inputStyle  datepicker" type="text" id="construction_year" name="construction_year" value="<?php echo date('d-M-Y',strtotime($property->construction_year));?>">
							</div>
                                                        
							<div class="halfWidth mb-3">
								<label class="labelStyle">Terrace<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mt-2 mb-3 pr-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="terrace1" name="terrace" type="radio" <?php if($property->terrace=="1"){ echo "checked";} ?>>
									<label for="terrace1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="terrace2" name="terrace" type="radio" <?php if($property->terrace=="0"){ echo "checked";} ?>>
									<label for="terrace2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">gardens<span class="redColor">*</span></label>
							</div>
							<div class="halfWidth mt-2 mb-3 pr-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="gardens1" name="gardens" type="radio" <?php if($property->garden=="1"){ echo "checked";} ?>>
									<label for="gardens1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="gardens2" name="gardens" type="radio" <?php if($property->garden=="0"){ echo "checked";} ?>>
									<label for="gardens2"><span>no</span></label>
								</div>
							</div>
                                                        <div class="clearfix"></div>
						</div>
					</div>
				</div>
				<div class="mainBoxHolder">
					<div class="text-capitalize">
						<h2>obligation information</h2>
					</div>
					<hr>
					<div class="row text-capitalize mt-4">
						<div class="col-6">
							<div class="halfWidth mb-3">
								<label class="labelStyle">Planning Permits</label>
							</div>
							<div class="halfWidth mb-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="planning_permits1" name="planning_permits" type="radio"  value="1" <?php if($property->planning_permits=="1"){ echo "checked";} ?>>
									<label for="planning_permits1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="planning_permits2" name="planning_permits" type="radio"  value="0" <?php if($property->planning_permits=="0"){ echo "checked";} ?>>
									<label for="planning_permits2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Subpoenas</label>
							</div>
							<div class="halfWidth mb-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="subpoenas1" name="subpoenas" type="radio"  value="1" <?php if($property->subpoenas=="1"){ echo "checked";} ?>>
									<label for="subpoenas1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="subpoenas2" name="subpoenas" type="radio"  value="0" <?php if($property->subpoenas=="0"){ echo "checked";} ?>>
									<label for="subpoenas2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Judicial sayings</label>
							</div>
							<div class="halfWidth mb-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="judicial_sayings1" name="judicial_sayings" type="radio"  value="1" <?php if($property->judicial_sayings=="1"){ echo "checked";} ?>>
									<label for="judicial_sayings1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="judicial_sayings2" name="judicial_sayings" type="radio"  value="0" <?php if($property->judicial_sayings=="0"){ echo "checked";} ?>>
									<label for="judicial_sayings2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
                                                        <div class="halfWidth mb-3">
								<label class="labelStyle">Pre-emption Right</label>
							</div>
							<div class="halfWidth mb-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="pre_emption_right1" name="pre_emption_right" type="radio"   value="1" <?php if($property->pre_emption_right=="1"){ echo "checked";} ?>>
									<label for="pre_emption_right1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="pre_emption_right2" name="pre_emption_right" type="radio"  value="0" <?php if($property->pre_emption_right=="0"){ echo "checked";} ?>>
									<label for="pre_emption_right2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
                                                        <div class="halfWidth mb-3">
								<label class="labelStyle">Subdivision Permit</label>
							</div>
							<div class="halfWidth mb-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="subdivision_permit1" name="subdivision_permit" type="radio"  value="1" <?php if($property->subdivision_permit=="1"){ echo "checked";} ?>>
									<label for="subdivision_permit1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="subdivision_permit2" name="subdivision_permit" type="radio"  value="0" <?php if($property->subdivision_permit=="0"){ echo "checked";} ?>>
									<label for="subdivision_permit2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
                                                        <div class="halfWidth mb-3">
								<label class="labelStyle">Flood Area</label>
							</div>
							<div class="halfWidth mb-3">
								<div class="radioCheckStyleHolder float-left">
									<input id="flood_area1" name="flood_area" type="radio"  value="1" <?php if($property->flood_area=="1"){ echo "checked";} ?>>
									<label for="flood_area1"><span>yes</span></label>
								</div>
								<div class="radioCheckStyleHolder float-left">
									<input id="flood_area2" name="flood_area" type="radio"  value="0" <?php if($property->flood_area=="0"){ echo "checked";} ?>>
									<label for="flood_area2"><span>no</span></label>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="halfWidth mb-3">
								<label class="labelStyle">Neighborhoods</label>
							</div>
							<div class="halfWidth mb-3 pr-3">
								<select class="inputStyle chosen-search" id="neighborhood" name="neighborhood">
                                                                    <option value="">Choose</option>
                                                                    @foreach($neighborhoods as $neighbors)
									<option value="{{$neighbors->id}}"  <?php if ($property->neighborhood == $neighbors->id) { echo "selected";}?>>{{$neighbors->name}}</option>
                                                                    @endforeach
								</select>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				<div class="mainBoxHolder">
					<div class="text-capitalize">
						<h2>add images</h2>
					</div>
					<hr>
					<div class="row text-capitalize">
						<div class="col-3 mb-3">
							<label class="labelStyle">Upload New Property Plan</label>
						</div>
						<div class="col-9 mb-3">
                                                    <input class="custmFileInput" id="property_plan" name='property_plan' type="file" />
							<label for="property_plan" class="customFileUpload">choose file</label>
						</div>
                                                <div class="col-3 mb-3">
							<label class="labelStyle">Add preview image</label>
						</div>
						<div class="col-9 mb-3">
                                                   <?php
                                                            $val = '';
                                                            if ($property->mainimage == "" || $property->mainimage == NULL) {
                                                                $filename = url('/') . '/default_image/imgPlaceholder.jpg';
                                                            } else {
                                                                $filename = url('/') . "/uploads/property-gallery/" . $property->mainimage;
                                                                $val = $property->mainimage;
                                                            }
                                                            if (!@getimagesize($filename)) {
                                                                $filename = url('/') . '/default_image/imgPlaceholder.jpg';
                                                            }
                                                            ?> 
                                                      <div class="previewImageHolder">
                                                            <img id="previewbox_previewimg" src="{{$filename}}">
                                                        </div>
                                                        <input class="custmFileInput" id="preview_upload" name="preview_upload" type="file" onchange="previewFile('#preview_upload','#previewbox_previewimg','178X246')" />
							<label for="preview_upload" class="customFileUpload">choose file</label>
							 <a href="javascript:void(0)" style="display: none;"  onclick="deletePreview('#previewbox_previewimg')">Delete Image</a>
                                                          
                                                      
						</div>
                                                <div class="col-3 mb-3">
							<label class="labelStyle">Add gallery images</label>
						</div>
                                                <div class="col-9 mb-3">
                                                    <div class="uploadGalleryPrevHolder  mb-2">

                                                        <?php $i = 0; ?>
                                                        @foreach($gallery as $galImage)
                                                        <?php
                                                        $val = '';
                                                        $filename = '';
                                                        if ($galImage->image == "" || $galImage->image == NULL) {
                                                            $filename = url('/') . '/default_image/imgPlaceholder.jpg';
                                                        } else {
                                                            $filename = url('/') . "/uploads/property-gallery/" . $galImage->image;
                                                            $val = $galImage->image;
                                                        }
                                                        if (!@getimagesize($filename)) {
                                                            $filename = url('/') . '/default_image/imgPlaceholder.jpg';
                                                        }
                                                        ?>  

                                                        <div class="previewImageHolder">
                                                            <span class="delImageSelect previewGalImg" onclick="removeImg({{$galImage->id}})">X</span>
                                                            <img src="{{$filename}}">
                                                        </div>
                                                        <?php $i++; ?>
                                                        @endforeach
                                                    </div>
                                                    
                                                    <div class="appendGalleryElement">
										<div class="galleryAddHolder">
											<div class="galPrev">
												<div class="previewImageHolder">
													<span class="delImageSelect delPreviewImg">X</span>
													<img id='previewbox_previewimg_0' src="{{URL::asset('/')}}/default_image/imgPlaceholder.jpg">
												</div>
												<input class="custmFileInput" name="gallery_upload_0" id="gallery_upload_0" type="file" onchange="previewFile_add('#gallery_upload_0', '#previewbox_previewimg_0', '160X220')"/>
												<label for="gallery_upload_0" class="customFileUpload">choose file</label>
											</div>
											<a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">
												<figure>
													<img class="mr-1" src="{{URL::asset('agent')}}/images/iconPlus.png"> add more
												</figure>
											</a>
										</div>
									</div>
                                                </div>

                                           
                                           <input type="hidden" name="galleryCount" id="galleryCount" value="0">
					</div>
					<button type="button" id="btnsaveProperty" class="btnStyle">Save</button>
				</div>
                                <input type="hidden" id="latitude" name="latitude" value="{{$property->latitude}}"/>
                                <input type="hidden" id="longitude"  name="longitude" value="{{$property->longitude}}" />
                                <input type="hidden" id="propertId" name="propertyId" value="{{\Crypt::encrypt($property->id)}}" />
                        </form>
                        </div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE"></script>
<script>
    var count=$('#galleryCount').val();
$(document).ready(function(){
    
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
    
    geolocator();
    $('#buildingType').chosen();
    $('#municipality').chosen();
        $(document).on('click','.addMoreGalleryBtn',function(){
                                count++;
//                                var param1="#gallery_upload_"+count;
//                                var param2="#previewbox_previewimg_"+count;
//                                var param3="160X220";
                                var html='<div class="galleryAddHolder">'+
                                            '<div class="galPrev">'+
                                                '<div class="previewImageHolder">'+
						'<span class="delImageSelect delPreviewImg" >X</span>'+
						'<img id="previewbox_previewimg_'+count+'" src="{{URL::asset('')}}/default_image/imgPlaceholder.jpg">'+
						'</div>  '+
						'  <input class="custmFileInput"  id="gallery_upload_'+count+'" name="gallery_upload_'+count+'" type="file" onchange="previewFile_add(&quot;#gallery_upload_'+count+'&quot;,&quot;#previewbox_previewimg_'+count+'&quot;,&quot;160X220&quot;)"/>'+
						'   <label for="gallery_upload_'+count+'" class="customFileUpload">choose file</label>'+
						'</div>  '+
                                            '   <a class="addPropertyBtn addMoreGalleryBtn" href="javascript:void(0);">'+
					'  <figure>'+
					'  <img class="mr-1" src="{{URL::asset('')}}/admin/images/iconPlus.png"> add more'+
					'  </figure>'+
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

   $("#addproperty__").validate({
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
                                   required: {
                                        depends: function () {
                                            if ($.trim($(this).val()) == '') {
                                                $(this).val($.trim($(this).val()));
                                                return true;
                                            }
                                        }
                                    },
                                    PropertyNameLang: true,
                                },
                                name_ka: {
                                    required: {
                                            depends: function () {
                                                if ($.trim($(this).val()) == '') {
                                                    $(this).val($.trim($(this).val()));
                                                    return true;
                                                }
                                            }
                                        },
                                },
                                name_ru: {
                                   required: {
                                        depends: function () {
                                            if ($.trim($(this).val()) == '') {
                                                $(this).val($.trim($(this).val()));
                                                return true;
                                            }
                                        }
                                    },
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
                                /* terraces: {
                                 required: true,
                                 },
                                 parking: {
                                 required: true,
                                 },*/
                                construction_year: {
                                    required: true,
                                },
                                availability: {
                                    required: true,
                                },
                                /*gardens: {
                                 required: true,
                                 },*/
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
                                /* city_ka: {
                                 required: "Please enter the zip in Georgian",
                                 },
                                 city_ru: {
                                 required: "Please enter the zip in Russian",
                                 },*/
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
                                    required: "Please enter the garages",
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
                                /* terrace: {
                                 required: "Please enter the number of terraces",
                                 },
                                 parking: {
                                 required: "Please check if parking available or not",
                                 },*/
                                construction_year: {
                                    required: "Please enter the construction year",
                                },
                                availability: {
                                    required: "Please check the availability",
                                },
                                /* gardens: {
                                 required: "Please check if garden is available or not",
                                 },*/

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
         /* if (!$("#addproperty").valid()) {
                return false;
            }*/

            $("#btnsaveProperty").attr("disabled", "disabled");
            $("#addproperty").submit();
        });

</script>
<script>
    $('#description_en').ckeditor();  
    $('#description_ka').ckeditor();
    $('#description_ru').ckeditor();
</script>
<script type="text/javascript">
    $(document).ready(function() {
      $(".datepicker").datepicker({
           // maxDate: new Date(),
            dateFormat: 'dd-M-yy',
            changeMonth: true,
            changeYear: true,
           
        });
    });
                
               
</script>
<script>
      //Google location

    var Lat = "41.716667";
    var Lng = "44.783333";

    function geolocator() {
        var geocoder = new google.maps.Geocoder();
        var address = "Tbilisi";
        geocoder.geocode({ 'address': address }, function (results, status) {
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
            { 'latLng': latlng },
            function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    //console.log(results);
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
                            // document.getElementById('district_en').value = add[distKey].long_name;
                         }
                        else {
                             //document.getElementById('district_en').value = "";
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
    
     $('body').on('click', '.previewGalImg', function () {
       $(this).closest('div').remove();
    });
    
      $('body').on('click', '.delPreviewImg', function () {
       $(this).closest('.galleryAddHolder').remove();
    });
    
    function removeImg(id){
        $.ajax({
          url: '<?php echo url('/')?>/admin/image-remove',
              data: {"imgId": id},
          type: 'POST',
          async: true,
          dataType: "json",
          success: function (data) {
              
         }
        });
        
    }
</script>

@endsection
		