@extends('layout.admin.menu')
@section('content')
@section('title', 'Property Details')
<?php
$property = $render_params['property'];
$gallery = $render_params['gallery'];

?>
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
						<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
							<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">en</a></li>
							<li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">ka</a></li>
							<li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
						</ul>
                                            
                                            <div id="en" class="tabContent">
							<h2 class="mt-2 text-capitalize mb-3">{{$property->name_en}}</h2>
							<div class="row">
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">District</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->districtname}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">address</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->address_en}}
									</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($property->description_en);?>
                                                                    </div>
								</div>
							</div>
							
							
						</div>
                                             <div id="ka" class="tabContent">
							<h2 class="mt-2 text-capitalize mb-3">{{$property->name_ru}}</h2>
							<div class="row">
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">District</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->districtname}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">address</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->address_ru}}
									</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($property->description_ru);?>
                                                                    </div>
								</div>
							</div>
							
							
						</div>
						 <div id="ru" class="tabContent">
							<h2 class="mt-2 text-capitalize mb-3">{{$property->name_ka}}</h2>
							<div class="row">
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">District</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->districtname}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">address</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$property->address_ka}}
									</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($property->description_ka);?>
                                                                    </div>
								</div>
							</div>
							
							
						</div>
						
					</div>

                                    <div class="row neighbourhoodGallery">
                                        <div class="col-12">
                                            <label class="labelStyle mb-3 text-capitalize">image gallery</label>
                                        </div>
                                        @foreach($gallery as $galImages)
                                        <div class="col-3">
                                            <?php
                                            
                                                $image = 'default_image/property_default.png';

                                                                if ($galImages->image == "" || $galImages->image == NULL) {
                                                                   
                                                                } else {
                                                                    $image = url('/') . '/uploads/neighborhood/' . $galImages->image;

                                                                    if (!@getimagesize($image)) {
                                                                         $image = 'default_image/property_default.png';
                                                                        } else{
                                                                            
                                                                    }
                                                                }
                                            ?>
                                            <figure>
                                                <img class="img-fluid" src="{{URL::asset($image)}}">
                                            </figure>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-2">
                                        <a class="actnIcons" href="{{ URL::to('admin/neighbourhood-edit', ['id' => Crypt::encrypt($property->id)]) }}">
                                            <button type="button" class="btnStyle mr-1">edit details</button></a>
                                        <a class="actnIcons" href="{{ URL::to('admin/neighbourhood-listing', ['id' => Crypt::encrypt($property->id)]) }}">
                                            <button type="button" class="cancelBtnStyle">Cancel</button></a>
                                    </div>

                                </div>
			</div>
@endsection