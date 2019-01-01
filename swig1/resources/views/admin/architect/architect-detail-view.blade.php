@extends('layout.admin.menu')
@section('content')
@section('title', 'Property Details')
<?php
$architect_details = $render_params['architect_details'];
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
							<div class="row">
								<div class="col-6"><h2 class="mt-2 text-capitalize mb-3">{{$architect_details->name_en}}</h2></div>

								<div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">basic information</label>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">email</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle text-capitalize">{{$architect_details->email}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Phone Number</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle text-capitalize">{{$architect_details->phone}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Address</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$architect_details->address_en}}</div>
								</div>

								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($architect_details->description_en); ?>
                                                                    </div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">additional description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($architect_details->additional_description_en); ?>
                                                                    </div>
								</div>


								<div class="row neighbourhoodGallery">
									<div class="col-12">
										<label class="labelStyle mb-3 text-capitalize">image gallery</label>
									</div>
																@foreach($gallery as $galImages)
									<div class="col-3">
										<figure><img class="img-fluid" src="{{URL::asset('uploads/architect-gallery')}}/{{$galImages->image}}"></figure>
									</div>
																@endforeach
								</div>

							</div>
							<div class="mt-2">
									<a class="actnIcons" href="{{ URL::to('admin/architect-edit', ['id' => Crypt::encrypt($architect_details->id)]) }}">
									<button type="button" class="btnStyle mr-1">edit details</button></a>
									<a class="actnIcons" href="{{ URL::to('admin/architect-listing') }}">
                                    <button type="button" class="cancelBtnStyle">Back</button></a>
							</div>
						</div>
						<div id="ka" class="tabContent">
							<div class="row">
								<div class="col-6"><h2 class="mt-2 text-capitalize mb-3">{{$architect_details->name_ka}}</h2></div>

								<div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">basic information</label>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">email</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle text-capitalize">{{$architect_details->email}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Phone Number</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle text-capitalize">{{$architect_details->phone}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Address</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$architect_details->address_ka}}</div>
								</div>

								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($architect_details->description_ka); ?>
                                                                    </div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">additional description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($architect_details->additional_description_ka); ?>
                                                                    </div>
								</div>


								<div class="row neighbourhoodGallery">
									<div class="col-12">
										<label class="labelStyle mb-3 text-capitalize">image gallery</label>
									</div>
																@foreach($gallery as $galImages)
									<div class="col-3">
										<figure><img class="img-fluid" src="{{URL::asset('uploads/architect-gallery')}}/{{$galImages->image}}"></figure>
									</div>
																@endforeach
								</div>

							</div>
								<div class="mt-2">
									<a class="actnIcons" href="{{ URL::to('admin/architect-edit', ['id' => Crypt::encrypt($architect_details->id)]) }}">
									<button type="button" class="btnStyle mr-1">edit details</button></a>
									<a class="actnIcons" href="{{ URL::to('admin/architect-listing') }}">
                                    <button type="button" class="cancelBtnStyle">Back</button></a>
								</div>
						</div>
						<div id="ru" class="tabContent">
							<div class="row">
								<div class="col-6"><h2 class="mt-2 text-capitalize mb-3">{{$architect_details->name_ru}}</h2></div>

								<div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">basic information</label>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">email</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle text-capitalize">{{$architect_details->email}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Phone Number</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle text-capitalize">{{$architect_details->phone}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Address</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$architect_details->address_ru}}</div>
								</div>

								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($architect_details->description_ru); ?>
                                                                    </div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">additional description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($architect_details->additional_description_ru); ?>
                                                                    </div>
								</div>


								<div class="row neighbourhoodGallery">
									<div class="col-12">
										<label class="labelStyle mb-3 text-capitalize">image gallery</label>
									</div>
																@foreach($gallery as $galImages)
									<div class="col-3">
										<figure><img class="img-fluid" src="{{URL::asset('uploads/architect-gallery')}}/{{$galImages->image}}"></figure>
									</div>
																@endforeach
								</div>

							</div>
								<div class="mt-2">
									<a class="actnIcons" href="{{ URL::to('admin/architect-edit', ['id' => Crypt::encrypt($architect_details->id)]) }}">
									<button type="button" class="btnStyle mr-1">edit details</button></a>
									<a class="actnIcons" href="{{ URL::to('admin/architect-listing') }}">
                                    <button type="button" class="cancelBtnStyle">Back</button></a>
								</div>
						</div>

						</div>
					</div>
				</div>
@endsection