@extends('layout.agent.agentlayout')
@section('property','active')
@section('content')
<?php 

?>
		<div class="adminPageHolder agentAdminHolder adminAddPropertyHolder">
			<div class="mainBoxHolder">
				<div class="tabWrapper tabOuterHolder">
					<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
						<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">EN</a></li>
						<li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
						<li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">ka</a></li>
					</ul>
					<div id="en" class="tabContent">
						<div class="row">
							<div class="col-6"><h2 class="mt-2 text-capitalize mb-3">property name</h2>{{$property->name_en}}</div>
							<div class="col-6">
								<strong class="viewerCount mt-2">number of views : <span></span></strong>
							</div>
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize subInnerTitle">basic information</label>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">type</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle text-capitalize">{{$property->buildingType}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">Reference Number</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle text-capitalize">{{$property->reference_number}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">price</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle">{{$property->estimated_price}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">address</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle">{{$property->address_line1_en}} {{$property->address_line2_en}} <br>
									{{$property->zip}} <br>
									{{$property->munc_name}} <br>
									{{$property->district}}
								</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">description</label>
							</div>
							<div class="col-9">
                                                            <div class="detailContentStyle">
															<?php echo htmlspecialchars_decode($property->description_en); ?>
                                                            </div>
							</div>
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize subInnerTitle">Description & Features</label>
							</div>
							<div class="col-6">
								<div class="row">
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">area</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->total_area}} m²</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Habitable Area</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->habitable_area}} m²</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Baths</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_baths}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Garages</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_garages}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Underground Parking</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->underground_parking=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Availability</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php echo date('d-M-Y',strtotime($property->availability));?></div>
									</div>
								</div>
							</div>
							<div class="col-6">
								<div class="row">
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Floors</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_floors}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Beds</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_beds}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Balcony</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_balcony}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Terraces</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->terrace=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Year Of Construction</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php echo date('d-M-Y',strtotime($property->construction_year));?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Gardens</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->garden=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
								</div>
							</div>
							<div class="col-12">
								       <label class="labelStyle mb-3 text-capitalize subInnerTitle clearfix">
                                                                            <span class="mt-2 list-inline-item" >Obligation Information</span>
                                                                            <a download target="_blank" href="{{URL::asset('uploads/property-plan')}}/{{$property->property_plan}}">
                                                                                <button type="button" class="btnStyle mr-1 float-right">Download Property Plan</button>
                                                                            </a>
                                                                        </label>
                                                                </div>
							<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Planning Permits</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->planning_permits=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Subpoenas</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->subpoenas=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Judicial Sayings</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->judicial_sayings=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Neighborhoods</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->neighborname}}</div>
								</div>
						</div>
						<div class="row neighbourhoodGallery">
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize">image gallery</label>
							</div>
							@foreach($gallery as $galImages)
								<div class="col-3">
									<figure><img class="img-fluid" src="{{URL::asset('uploads/property-gallery')}}/{{$galImages->image}}"></figure>
								</div>
                                                            @endforeach	
						</div>
						<div class="mt-2">
								<a class="actnIcons" href="{{ URL::to('agent/property-edit', ['id' => Crypt::encrypt($property->id)]) }}">
									<button type="button" class="btnStyle mr-1">edit details</button>
								</a>
								<a class="actnIcons" href="{{ URL::to('agent/property-listing') }}">
									<button type="button" class="cancelBtnStyle">Back</button>
								</a>							
						</div>
					</div>
                                    
                                    <div id="ru" class="tabContent">
						<div class="row">
							<div class="col-6"><h2 class="mt-2 text-capitalize mb-3">property name</h2></div>
							<div class="col-6">
								<strong class="viewerCount mt-2">number of views : <span>350</span></strong>
							</div>
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize subInnerTitle">basic information</label>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">type</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle text-capitalize">{{$property->buildingType}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">Reference Number</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle text-capitalize">{{$property->reference_number}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">price</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle">{{$property->estimated_price}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">address</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle">{{$property->address_line1_ru}} {{$property->address_line2_ru}} <br>
									{{$property->zip}} <br>
									{{$property->munc_name}} <br>
									{{$property->district}}
								</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">description</label>
							</div>
							<div class="col-9">
                                                            <div class="detailContentStyle">
															<?php echo htmlspecialchars_decode($property->description_ru); ?>
                                                            </div>
							</div>
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize subInnerTitle">Description & Features</label>
							</div>
							<div class="col-6">
								<div class="row">
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">area</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->total_area}} m²</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Habitable Area</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->habitable_area}} m²</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Baths</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_baths}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Garages</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_garages}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Underground Parking</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->underground_parking=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Availability</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php echo date('d-M-Y',strtotime($property->availability));?></div>
									</div>
								</div>
							</div>
							<div class="col-6">
								<div class="row">
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Floors</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_floors}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Beds</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_beds}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Balcony</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_balcony}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Terraces</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->terrace=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Year Of Construction</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php echo date('d-M-Y',strtotime($property->construction_year));?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Gardens</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->garden=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
								</div>
							</div>
							<div class="col-12">
								       <label class="labelStyle mb-3 text-capitalize subInnerTitle clearfix">
                                                                            <span class="mt-2 list-inline-item" >Obligation Information</span>
                                                                            <a download target="_blank" href="{{URL::asset('uploads/property-plan')}}/{{$property->property_plan}}">
                                                                                <button type="button" class="btnStyle mr-1 float-right">Download Property Plan</button>
                                                                            </a>
                                                                        </label>
                                                                </div>
							<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Planning Permits</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->planning_permits=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Subpoenas</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->subpoenas=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Judicial Sayings</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->judicial_sayings=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Neighborhoods</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->neighborname}}</div>
								</div>
						</div>
						<div class="row neighbourhoodGallery">
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize">image gallery</label>
							</div>
							@foreach($gallery as $galImages)
								<div class="col-3">
									<figure><img class="img-fluid" src="{{URL::asset('uploads/property-gallery')}}/{{$galImages->image}}"></figure>
								</div>
                                                            @endforeach	
						</div>
						<div class="mt-2">
								<a class="actnIcons" href="{{ URL::to('agent/property-edit', ['id' => Crypt::encrypt($property->id)]) }}">
									<button type="button" class="btnStyle mr-1">edit details</button>
								</a>
								<a class="actnIcons" href="{{ URL::to('agent/property-listing') }}">
									<button type="button" class="cancelBtnStyle">Back</button>
								</a>							
						</div>
					</div>
                                    
                                    <div id="ka" class="tabContent">
						<div class="row">
							<div class="col-6"><h2 class="mt-2 text-capitalize mb-3">property name</h2></div>
							
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize subInnerTitle">basic information</label>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">type</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle text-capitalize">{{$property->buildingType}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">Reference Number</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle text-capitalize">{{$property->reference_number}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">price</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle">{{$property->estimated_price}}</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">address</label>
							</div>
							<div class="col-9">
								<div class="detailContentStyle">{{$property->address_line1_ka}} {{$property->address_line2_ka}} <br>
									{{$property->zip}} <br>
									{{$property->munc_name}} <br>
									{{$property->district}}
								</div>
							</div>
							<div class="col-3">
								<label class="labelStyle mb-3 text-capitalize">description</label>
							</div>
							<div class="col-9">
                                                            <div class="detailContentStyle">
															<?php echo htmlspecialchars_decode($property->description_ka); ?>
                                                            </div>
							</div>
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize subInnerTitle">Description & Features</label>
							</div>
							<div class="col-6">
								<div class="row">
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">area</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->total_area}} m²</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Habitable Area</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->habitable_area}} m²</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Baths</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_baths}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Garages</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_garages}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Underground Parking</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->underground_parking=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Availability</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php echo date('d-M-Y',strtotime($property->availability));?></div>
									</div>
								</div>
							</div>
							<div class="col-6">
								<div class="row">
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Floors</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_floors}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Beds</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_beds}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Balcony</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize">{{$property->no_of_balcony}}</div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Number Of Terraces</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->terrace=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Year Of Construction</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php echo date('d-M-Y',strtotime($property->construction_year));?></div>
									</div>
									<div class="col-6">
										<label class="labelStyle mb-3 text-capitalize">Gardens</label>
									</div>
									<div class="col-6">
										<div class="detailContentStyle text-capitalize"><?php if($property->garden=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
									</div>
								</div>
							</div>
							<div class="col-12">
								       <label class="labelStyle mb-3 text-capitalize subInnerTitle clearfix">
                                                                            <span class="mt-2 list-inline-item" >Obligation Information</span>
                                                                            <a download target="_blank" href="{{URL::asset('uploads/property-plan')}}/{{$property->property_plan}}">
                                                                                <button type="button" class="btnStyle mr-1 float-right">Download Property Plan</button>
                                                                            </a>
                                                                        </label>
                                                                </div>
							<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Planning Permits</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->planning_permits=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Subpoenas</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->subpoenas=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Judicial Sayings</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle"><?php if($property->judicial_sayings=='1'){ echo "Yes"; }else{ echo "No"; } ?></div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Neighborhoods</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->neighborname}}</div>
								</div>
						</div>
						<div class="row neighbourhoodGallery">
							<div class="col-12">
								<label class="labelStyle mb-3 text-capitalize">image gallery</label>
							</div>
							@foreach($gallery as $galImages)
								<div class="col-3">
									<figure><img class="img-fluid" src="{{URL::asset('uploads/property-gallery')}}/{{$galImages->image}}"></figure>
								</div>
                                                            @endforeach	
						</div>
						
						<div class="mt-2">
								<a class="actnIcons" href="{{ URL::to('agent/property-edit', ['id' => Crypt::encrypt($property->id)]) }}">
									<button type="button" class="btnStyle mr-1">edit details</button>
								</a>
								<a class="actnIcons" href="{{ URL::to('agent/property-listing') }}">
									<button type="button" class="cancelBtnStyle">Back</button>
								</a>							
						</div>
					</div>
                                    
                                    
					
				</div>
			</div>
		</div>
@endsection