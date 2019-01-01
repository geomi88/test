@extends('layout.agent.agentlayout')
@section('dashboard','active')
@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<input type = "hidden" id= "base_url" value="{{ url('/') }}"/>
<?php 
$totalCount=
        ($widgetData->houseCount+
        $widgetData->apartmentCount+
        $widgetData->commercialCount+
        $widgetData->officeCount+
        $widgetData->parkingCount);
?>
		<div class="topTotalListHolder clearfix text-capitalize">
			<div class="row listBoxPadding eqHeightHolder">
				<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
					<div class="listBoxHolder redBgColor text-center eqHeightInner">
						<div class="totalListLabel">total listing</div>
						<h1>{{$totalCount}}</h1>
					</div>
				</div>
				<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
					<div class="listBoxHolder greenBgColor clearfix eqHeightInner">
						<figure class="float-left"><img src="images/iconHome.png"></figure>
						<div class="float-left labelContent">
							<label>Houses</label>
							<h1>{{$widgetData->houseCount}}</h1>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
					<div class="listBoxHolder yellowBgColor clearfix eqHeightInner">
						<figure class="float-left"><img src="images/iconAppartment.png"></figure>
						<div class="float-left labelContent">
							<label>Apartments</label>
							<h1>{{$widgetData->apartmentCount}}</h1>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
					<div class="listBoxHolder blueBgColor clearfix eqHeightInner">
						<figure class="float-left"><img src="images/iconOffice.png"></figure>
						<div class="float-left labelContent">
							<label>office</label>
							<h1>{{$widgetData->officeCount}}</h1>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
					<div class="listBoxHolder pinkBgColor clearfix eqHeightInner">
						<figure class="float-left"><img src="images/iconCommercial.png"></figure>
						<div class="float-left labelContent">
							<label>Commercial</label>
							<h1>{{$widgetData->commercialCount}}</h1>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
					<div class="listBoxHolder violetBgColor clearfix eqHeightInner">
						<figure class="float-left"><img src="images/iconParking.png"></figure>
						<div class="float-left labelContent">
							<label>parking</label>
							<h1>{{$widgetData->parkingCount}}</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row listSelectHolder eqHeightHolder listBoxPadding">
			<div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-7">
				<div class="mainBoxHolder text-capitalize eqHeightInner">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2>sales by district</h2>
						</div>
						<div class="col-6 text-right">
							<select class="selectStyle mr-1" name="tenure_type" id="tenure_type">
								<option value="1">Sale</option>
                                                                <option value="2">Rent</option>
								
							</select>
							<select class="selectStyle" name="district_id" id="district_id">
								<option value="">All</option>
                                                                @foreach($agentDistricts as $districts)
								<option value="{{$districts->id}}">{{$districts->name}}</option>
                                                                @endforeach
							</select>
						</div>
					</div>
					<hr>
                                                <div class="row">
								<div class="col-12">
									<div class="pieChart" id="districtChart"></div>

								</div>
								
							</div>
				</div>
			</div>
			<div class="col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5">
				<div class="mainBoxHolder eqHeightInner">
					<div class="row">
						<div class="col-5 text-capitalize">
							<h2>Listing By City</h2>
						</div>
						<div class="col-7 text-right">
							<select class="selectStyle mr-1" name="municipality_tenure_type" id="municipality_tenure_type">
								<option value="1">Sale</option>
                                                                <option value="2">Rent</option>
							</select>
							<select class="selectStyle" name="municipality_id" id="municipality_id">
								<option value="">All</option>
								@foreach($agentCity as $city)
								<option value="{{$city->id}}">{{$city->name}}</option>
                                                                @endforeach
							</select>
						</div>
					</div>
					<hr>
					<div class="listByCityHolder">
						<div class="cityName text-uppercase">All</div>
						<ul class="list-unstyled cityListHolder text-capitalize">
							<li>
								<figure class="greenBgColor">
									<img src="images/iconHome.png">
								</figure>
								<label>Houses</label>
                                                                <strong><div id="municipality_houses">{{$cityData->houseCount}}</div></strong>
							</li>
							<li>
								<figure class="yellowBgColor">
									<img src="images/iconAppartment.png">
								</figure>
								<label>Apartments</label>
                                                                <strong><div id="municipality_apartments">{{$cityData->apartmentCount}}</div></strong>
							</li>
							<li>
								<figure class="blueBgColor">
									<img src="images/iconOffice.png">
								</figure>
								<label>office</label>
                                                                <strong><div id="municipality_offices">{{$cityData->officeCount}}</div></strong>
							</li>
							<li>
								<figure class="pinkBgColor">
									<img src="images/iconCommercial.png">
								</figure>
								<label>Commercial</label>
                                                                <strong><div id="municipality_commercial">{{$cityData->commercialCount}}</div></strong>
							</li>
							<li>
								<figure class="violetBgColor">
									<img src="images/iconParking.png">
								</figure>
								<label>parking</label>
                                                                <strong><div id="municipality_parking">{{$cityData->parkingCount}}</div></strong>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="allListingWrapper eqHeightHolder">
			<div class="mainBoxHolder">
				<div class="row">
					<div class="col-6 text-capitalize">
						<h2>your listings</h2>
					</div>
					<div class="col-6 text-right text-capitalize">
						<a class="viewAllLink" href="javascript:void(0);">view all listing</a>
					</div>
				</div>
			</div>
			<div class="row listBoxPadding">
                            @foreach($property_list as $property)
				<div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3">
					<div class="allListBoxHolder eqHeightInner">
                                            <a href="{{ URL::to('agent/property-view', ['id' => Crypt::encrypt($property->id)]) }}">
						<figure class="listImgHolder">
                                                    <?php
                                                                    $image =url('/') . '/default_image/property_default.png';

                                                                    if ($property->images == "" || $property->images == NULL) {
                                                                        ?>

                                                                        <?php
                                                                    } else {
                                                                        $image = url('/') . '/uploads/project-gallery/' . $property->images;

                                                                        if (!@getimagesize($image)) {
                                                                            $image = url('/') .'/default_image/property_default.png';
                                                                        }else{
                                                                            $image =url('/') .'/uploads/project-gallery/' . $property->images;

                                                                        }
                                                                    }
                                                                ?>
                                                    <img class="img-fluid" src="{{URL::asset($image)}}">
                                                </figure>
                                            </a>
						<div class="listContentHolder relative">
							<h3>{{$property->name_en}}</h3>
							<div class="dropdownWrapper">
								<span class="listEditOption dropClick">
                                                                    <img src="images/iconGrayDot.png">
                                                                </span>
								<ul class="list-unstyled listEditDropdown dropdownOpen">
									<li>
										<a href="{{ URL::to('agent/property-view', ['id' => Crypt::encrypt($property->id)]) }}"><img class="img-fluid" src="images/iconView.png"></a>
									</li>
									<li>
										<a href="{{ URL::to('agent/property-edit', ['id' => Crypt::encrypt($property->id)]) }}"><img class="img-fluid" src="images/iconEdit.png"></a>
									</li>
									<li>
										<a href="{{ URL::to('agent/property-remove', ['id' => Crypt::encrypt($property->id)]) }}"><img class="img-fluid" src="images/iconDel.png"></a>
									</li>
								</ul>
							</div>
							<div class="addressWrap">
								<figure><img src="images/iconMap.png"></figure>
								<label>{{ $property->address1}}{{ $property->address2}}<br>  {{ $property->zip}}{{ $property->muncipality}}</label>
							</div>
							<p>
                                                            <?php
                                                            $desc = htmlspecialchars_decode($property->description_en);
                                                            $description = substr($desc, 0, 250);
                                                            echo $description;
                                                            ?>
                                                           
                                                        </p><p> <a href="{{ URL::to('agent/property-view', ['id' => Crypt::encrypt($property->id)]) }}">read more</a></p>
                                                        
						</div>
					</div>
				</div>
                            
                            @endforeach
				
			</div>
		</div>



<script>
function graphData (data) {
	Highcharts.chart('districtChart', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: ''
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Properties',
        colorByPoint: true,
        data: data
    }]
});
}

$(document).ready(function() {
    
        function equalHeight(){
                                        $('.eqHeightHolder').each(function(){  
                                        var highestBox = 0;
                                        $(this).find('.eqHeightInner').each(function(){
                                            if($(this).height() > highestBox){  
                                                highestBox = $(this).height();  
                                            }
                                        })
                                        $(this).find('.eqHeightInner').height(highestBox);
                                    });
                                }
                                
                                equalHeight();
                                
                                
var base_url = $("#base_url").val();
$('#district_id').on('change', function() {
	var district_id =  this.value ;
	var tenure_type = $('#tenure_type').val();

  $.ajax({
    url: base_url+'/agent/getDistrictData',
	data: {"district_id": district_id, "tenure_type": tenure_type},
    type: 'POST',
    async: true,
    dataType: "json",
    success: function (data) {
        graphData(data);
    }
  });
});
$('#tenure_type').on('change', function() {
	var tenure_type =  this.value ;
	var district_id = $('#district_id').val();

  $.ajax({
    url: base_url+'/agent/getDistrictData',
	data: {"district_id": district_id, "tenure_type": tenure_type},
    type: 'POST',
    async: true,
    dataType: "json",
    success: function (data) {
        graphData(data);
    }
  });
});

$('#municipality_tenure_type').on('change', function() {
	var municipality_tenure_type =  this.value ;
	var municipality_id = $('#municipality_id').val();

  $.ajax({
    url: base_url+'/agent/getMunicipalityData',
	data: {"municipality_id": municipality_id, "municipality_tenure_type": municipality_tenure_type},
    type: 'POST',
    async: true,
    dataType: "json",
    success: function (data) {
         
	var	municipality_houses = data.municipality_houses;
	var	municipality_apartments = data.municipality_apartments;
	var	municipality_comercial = data.municipality_comercial;
	var	municipality_offices = data.municipality_offices;
	var	municipality_parking = data.municipality_parking;
	$('#municipality_houses').text(municipality_houses);
	$('#municipality_apartments').text(municipality_apartments);
	$('#municipality_comercial').text(municipality_comercial);
	$('#municipality_offices').text(municipality_offices);
	$('#municipality_parking').text(municipality_parking);

    }
  });
});

$('#municipality_id').on('change', function() {
	var municipality_id =  this.value ;
	var municipality_tenure_type = $('#municipality_tenure_type').val();

  $.ajax({
    url: base_url+'/agent/getMunicipalityData',
	data: {"municipality_id": municipality_id, "municipality_tenure_type": municipality_tenure_type},
    type: 'POST',
    async: true,
    dataType: "json",
    success: function (data) {
        var cityName=$('#municipality_id').find(":selected").text();
       
	var	municipality_houses = data.municipality_houses;
	var	municipality_apartments = data.municipality_apartments;
	var	municipality_commercial = data.municipality_commercial;
	var	municipality_offices = data.municipality_offices;
	var	municipality_parking = data.municipality_parking;
	
	$('#municipality_houses').text(municipality_houses);
	$('#municipality_apartments').text(municipality_apartments);
	$('#municipality_commercial').text(municipality_commercial);
	$('#municipality_offices').text(municipality_offices);
	$('#municipality_parking').text(municipality_parking);
	$('.cityName').text(cityName);

    }
  });
});

 });



graphData(<?php echo $full_pi_graph_data; ?>);
</script>
@endsection		