@extends('layout.admin.menu')
@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<input type = "hidden" id= "base_url" value="{{ url('/') }}"/>
<div class="adminPageHolder">
				<div class="topTotalListHolder clearfix text-capitalize">
					<div class="row listBoxPadding eqHeightHolder">
						<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
							<div class="listBoxHolder redBgColor text-center eqHeightInner">
								<div class="totalListLabel">total listing</div>
								<h1>{{$listing_properties->properties_count}}</h1>
							</div>
						</div>
						<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
							<div class="listBoxHolder greenBgColor clearfix eqHeightInner">
								<figure class="float-left"><img src="images/iconHome.png"></figure>
								<div class="float-left labelContent">
									<label>Houses</label>
									<h1>{{$houses->properties_count}}</h1>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
							<div class="listBoxHolder yellowBgColor clearfix eqHeightInner">
								<figure class="float-left"><img src="images/iconAppartment.png"></figure>
								<div class="float-left labelContent">
									<label>Apartments</label>
									<h1>{{$apartments->properties_count}}</h1>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
							<div class="listBoxHolder blueBgColor clearfix eqHeightInner">
								<figure class="float-left"><img src="images/iconOffice.png"></figure>
								<div class="float-left labelContent">
									<label>office</label>
									<h1>{{$offices->properties_count}}</h1>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
							<div class="listBoxHolder pinkBgColor clearfix eqHeightInner">
								<figure class="float-left"><img src="images/iconCommercial.png"></figure>
								<div class="float-left labelContent">
									<label>Commercial</label>
									<h1>{{$commercial->properties_count}}</h1>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
							<div class="listBoxHolder violetBgColor clearfix eqHeightInner">
								<figure class="float-left"><img src="images/iconParking.png"></figure>
								<div class="float-left labelContent">
									<label>parking</label>
									<h1>{{$parking->properties_count}}</h1>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row listSelectHolder eqHeightHolder listBoxPadding">
					<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
						<div class="mainBoxHolder text-capitalize eqHeightInner">
							<div class="row">
								<div class="col-5 text-capitalize">
									<h2>sales by district</h2>
								</div>
								<div class="col-7 text-right">
									<select class="selectStyle mr-1" name="tenure_type" id="tenure_type">
										<option value="2" <?php if ($tenure_type == 2) {echo "selected";}?>>Rent</option>
										<option value="1" <?php if ($tenure_type == 1) {echo "selected";}?>>Sale</option>
									</select>
									<select class="selectStyle" name="district_id" id="district_id">
										<?php foreach ($districts as $district) {?>
											<option value="<?php echo $district->id; ?>" <?php if ($district->id == $district_id) {echo "selected";}?>><?php echo $district->name; ?></option>
										<?php }?>
									</select>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-12">
									<div class="pieChart" id="districtChart"></div>

								</div>
								<!-- <div class="col-6">
									<div class="graphLabelHolder">
										<label><span class="greenBgColor"></span> Houses</label>
										<label><span class="yellowBgColor"></span> Apartments</label>
										<label><span class="blueBgColor"></span> Office</label>
										<label><span class="pinkBgColor"></span> commercial</label>
										<label><span class="violetBgColor"></span> parking</label>
									</div>
								</div> -->
							</div>
						</div>
					</div>
					<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
						<div class="mainBoxHolder eqHeightInner">
							<div class="row">
								<div class="col-4 text-capitalize">
									<h2>Listing By City</h2>
								</div>
								<div class="col-8 text-right">
								<select class="selectStyle mr-1" name="municipality_tenure_type" id="municipality_tenure_type">
										<option value="2" <?php if ($municipality_tenure_type == 2) {echo "selected";}?>>Rent</option>
										<option value="1" <?php if ($municipality_tenure_type == 1) {echo "selected";}?>>Sale</option>
									</select>
									<select class="selectStyle" name="municipality_id" id="municipality_id">
										<?php
                                                                                    foreach ($municipalities as $municipality) {?>
											<option value="<?php echo $municipality->id; ?>" <?php if ($municipality->id == $municipality_id) {echo "selected";}?>><?php echo $municipality->name; ?></option>
										<?php }?>
									</select>
								</div>
							</div>
							<hr>
							<div class="listByCityHolder">
								<div class="cityName text-uppercase">{{$municipality_name}}</div>
								<ul class="list-unstyled cityListHolder text-capitalize">
									<li>
										<figure class="greenBgColor">
											<img src="images/iconHome.png">
										</figure>
										<label>Houses</label>
										<strong><div id="municipality_houses">{{$municipality_houses->properties_count}}</div></strong>
									</li>
									<li>
										<figure class="yellowBgColor">
											<img src="images/iconAppartment.png">
										</figure>
										<label>Apartments</label>
										<strong><div id="municipality_apartments">{{$municipality_apartments->properties_count}}</div></strong>
									</li>
									<li>
										<figure class="blueBgColor">
											<img src="images/iconOffice.png">
										</figure>
										<label>Office</label>
										<strong><div id="municipality_offices">{{$municipality_offices->properties_count}}</div></strong>
									</li>
									<li>
										<figure class="pinkBgColor">
											<img src="images/iconCommercial.png">
										</figure>
										<label>Commercial</label>
										<strong><div id="municipality_commercial">{{$municipality_commercial->properties_count}}</div></strong>
									</li>
									<li>
										<figure class="violetBgColor">
											<img src="images/iconParking.png">
										</figure>
										<label>Parking</label>
										<strong><div id="municipality_parking">{{$municipality_parking->properties_count}}</div></strong>
									</li>
								</ul>
							</div>
						</div>
					</div>
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
var base_url = $("#base_url").val();
$('#district_id').on('change', function() {
	var district_id =  this.value ;
	var tenure_type = $('#tenure_type').val();

  $.ajax({
    url: base_url+'/admin/getDistrictData',
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
    url: base_url+'/admin/getDistrictData',
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
    url: base_url+'/admin/getMunicipalityData',
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
	$('#municipality_houses').text(municipality_houses.properties_count);
	$('#municipality_apartments').text(municipality_apartments.properties_count);
	$('#municipality_comercial').text(municipality_comercial.properties_count);
	$('#municipality_offices').text(municipality_offices.properties_count);
	$('#municipality_parking').text(municipality_parking.properties_count);

    }
  });
});

$('#municipality_id').on('change', function() {
	var municipality_id =  this.value ;
	var municipality_tenure_type = $('#municipality_tenure_type').val();

  $.ajax({
    url: base_url+'/admin/getMunicipalityData',
	data: {"municipality_id": municipality_id, "municipality_tenure_type": municipality_tenure_type},
    type: 'POST',
    async: true,
    dataType: "json",
    success: function (data) {
	var	municipality_houses = data.municipality_houses;
	var	municipality_apartments = data.municipality_apartments;
	var	municipality_commercial = data.municipality_commercial;
	var	municipality_offices = data.municipality_offices;
	var	municipality_parking = data.municipality_parking;
	var	municipality = data.municipality;
	$('#municipality_houses').text(municipality_houses.properties_count);
	$('#municipality_apartments').text(municipality_apartments.properties_count);
	$('#municipality_commercial').text(municipality_commercial.properties_count);
	$('#municipality_offices').text(municipality_offices.properties_count);
	$('#municipality_parking').text(municipality_parking.properties_count);
	$('.cityName').text(municipality.name);

    }
  });
});

 });



graphData(<?php echo $full_pi_graph_data; ?>);
</script>
@endsection
