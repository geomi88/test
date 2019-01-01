@extends('layout.admin.menu')
@section('content')
@section('title', 'Estimates')

			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="text-capitalize adminTitle">
					<h1>Estimates Detail View</h1>
				</div>
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
						
						<div id="en" class="tabContent">
							<div class="row">
								
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">First Name</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->first_name}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Last Name</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->last_name}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Street</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->street_number}}</div>
								</div>
								
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">City</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->city}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Post Code</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->zip}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">District</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->district}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Email</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->email}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Phone</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->tele_phone}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Mobile</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->mobile_phone}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">Details</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$estimate_details->message}}
									</div>
								</div>
							</div>
							
						</div>
						
					</div>
				</div>
			</div>
@endsection