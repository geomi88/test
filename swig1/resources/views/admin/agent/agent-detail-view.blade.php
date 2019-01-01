@extends('layout.admin.menu')
@section('content')
@section('title', 'Agent Details')
<?php 
$agentDetails=$render_params['agentDetails'];
$buildingCount=$render_params['buildingCount'];


?>
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
						<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
							<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">EN</a></li>
                                                </ul>
						<div id="en" class="tabContent">
							<div class="row">
                                                            <div class="col-3">
                                                                <figure class="agentProfileImg">
                                                                <?php
                                                                $image = 'default_image/property_default.png';

                                                                if ($agentDetails->image == "" || $agentDetails->image == NULL) {
                                                                    ?>
                                                                    <img class="img-fluid " alt="agent1" src="{{ URL::asset($image)}}">
                                                                    <?php
                                                                } else {
                                                                    $image = url('/') . '/uploads/agent/' . $agentDetails->image;

                                                                    if (!@getimagesize($image)) {
                                                                        ?>
                                                                        <img class="img-fluid " alt="agent1" src="{{ URL::asset($image)}}">
                                                                    <?php } else { ?>
                                                                             <img class="img-fluid " alt="agent1" src="{{ URL::asset($image)}}">
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                                    </figure>
                                                            </div>
								<div class="col-9">
									<div class="row">
										<div class="col-3">
											<label class="labelStyle mb-3 text-capitalize">name</label>
										</div>
										<div class="col-9">
											<div class="detailContentStyle text-capitalize">{{$agentDetails->name}}</div>
										</div>
										<div class="col-3">
											<label class="labelStyle mb-3 text-capitalize">since</label>
										</div>
										<div class="col-9">
											<div class="detailContentStyle text-capitalize"><?php echo date("d-M-Y",strtotime($agentDetails->member_since)); ?></div>
										</div>
										<div class="col-3">
											<label class="labelStyle mb-3 text-capitalize">assigned district</label>
										</div>
										<div class="col-9">
											<div class="detailContentStyle text-capitalize">{{$agentDetails->districtname}}</div>
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">address</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$agentDetails->address}}
									</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">phone number</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$agentDetails->mobile_number}}</div>
								</div>
                                                            <div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">office number</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$agentDetails->office_phone}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">email id</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$agentDetails->email}}</div>
								</div>
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">description</label>
								</div>
								<div class="col-9">
                                                                    <div class="detailContentStyle">
                                                                        <?php echo htmlspecialchars_decode($agentDetails->description); ?>
                                                                    </div>
								</div>
								<div class="col-12">
									<label class="labelStyle mb-3 text-capitalize subInnerTitle">property listing</label>
								</div>
                                                            @foreach($buildingCount as $key=>$count)
								<div class="col-3">
									<label class="labelStyle mb-3 text-capitalize">{{$key}}</label>
								</div>
								<div class="col-9">
									<div class="detailContentStyle">{{$count}}</div>
								</div>
                                                            @endforeach
								<div class="col-5 mt-4">
                                                                    <a href="{{ URL::to('admin/agent-property-listing',['id'=> Crypt::encrypt($agentDetails->id)]) }}">
                                                                    <button type="button" class="btnStyle mr-1">view all listings</button></a>
								</div>
								<div class="col-7 mt-4 text-right">
									 <a href="{{ URL::to('admin/agent-edit', ['id' => Crypt::encrypt($agentDetails->id)]) }}">
                                                                             <button type="button" class="btnStyle mr-1">edit details</button>
                                                                         </a>
                                                                         <a href="{{ URL::to('admin/agent-listing') }}">
                                                                         <button type="button" class="cancelBtnStyle">Cancel</button>
                                                                         </a>
								</div>
							</div>
							
						</div>
						
					</div>
				</div>
			</div>
			
@endsection