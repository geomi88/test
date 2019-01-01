
<?php if(count($property_list)>0){?>
<div class="row listBoxPadding">

<?php $n = $property_list->perPage() * ($property_list->currentPage() - 1); ?>  
@foreach ($property_list as $property)

<div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 ">
					<div class="allListBoxHolder eqHeightInner">
                                            
                                            <?php
                                                            $val = '';
                                                            if ($property->main_image == "" || $property->main_image == NULL) {
                                                                $filename = url('/') . '/default_image/property_default.png';
                                                            } else {
                                                                $filename = url('/') . "/uploads/property-gallery/" . $property->main_image;
                                                                $val = $property->main_image;
                                                            }
                                                            if (!@getimagesize($filename)) {
                                                                $filename = url('/') . '/default_image/property_default.png';
                                                            }
                                                            ?> 
                                            <a href="{{ URL::to('agent/property-view', ['id' => Crypt::encrypt($property->id)]) }}">
						<figure class="listImgHolder">
                                                    <img class="img-fluid" src="{{$filename}}">
                                                </figure>
                                            </a>
						<div class="listContentHolder relative">
							<h3>{{$property->name_en}}</h3>
							<div class="dropdownWrapper">
								<span class="listEditOption dropClick">
                                                                    <img src="{{URL::asset('agent')}}/images/iconGrayDot.png">
                                                                </span>
								<ul class="list-unstyled listEditDropdown dropdownOpen">
									<li>
										<a href="{{ URL::to('agent/property-view', ['id' => Crypt::encrypt($property->id)]) }}"><img class="img-fluid" src="{{URL::asset('agent')}}/images/iconView.png"></a>
									</li>
									<li>
										<a href="{{ URL::to('agent/property-edit', ['id' => Crypt::encrypt($property->id)]) }}"><img class="img-fluid" src="{{URL::asset('agent')}}/images/iconEdit.png"></a>
									</li>
									<li>
										<a onclick="return confirmation()" href="{{ URL::to('agent/property-remove', ['id' => Crypt::encrypt($property->id)]) }}"><img class="img-fluid" src="{{URL::asset('agent')}}/images/iconDel.png"></a>
									</li>
								</ul>
							</div>
							<div class="addressWrap">
								<figure><img src="{{URL::asset('agent')}}/images/iconMap.png"></figure>
								<label>{{$property->address_line1_en}} {{$property->address_line2_en}}<br> {{$property->zip_en}} {{$property->muncipality}}</label>
							</div>
							<p>
                                                            <?php
                                                            $desc = htmlspecialchars_decode($property->description_en);
                                                            $description = substr($desc, 0, 250);
                                                            echo $description;
                                                            ?></p>
                                                        
							<p>	<a href="{{ URL::to('agent/property-view', ['id' => Crypt::encrypt($property->id)]) }}">read more</a>
							</p>
                                                        
						</div>
					</div>
				</div>

@endforeach
 </div>
<?php } ?>
<?php if ($property_list->lastPage() > 1) { ?>


    <div class="col-12">
        {!! $property_list->render() !!}
    </div>



<?php } ?>
			