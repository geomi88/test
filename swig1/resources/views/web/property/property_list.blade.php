@extends('layout.web.website')
@section('content')
@section('title', 'Profile Update')
@section('pageClass', 'productList')
@section('innerClass', 'withoutBanner')
@include('web.includes.banner_inner')
<script src="{{ URL::asset('common')}}/js/range-slider.js" type="text/javascript"></script>
<script src="{{ URL::asset('common')}}/js/iscroll.js" type="text/javascript"></script>

<style>
    .listItem:last-child{
        margin-bottom: 0;
    }
    
</style>
<section class="contentWrapper listHolder">
    <div class="pageCenter">
        <ul class="breadcrumbsHolder">
                            <li>
                                <a href="{{url('/')}}">{{__('home')}} <span>/</span></a>
                            </li>
                            
                            <li>
                                <a href="javascript:void();">{{__('property')}}</a>
                            </li>
                        </ul>
        <div class="row headingSection">
            <div class="small-12 medium-7 large-8 column">
                <h2> <?php
                    if(array_key_exists('tenure_type',$filer_array)){
                        if($filer_array['tenure_type']=='1'){?>
                            @section('sale', 'active') {{__('for_sale')}} <?php }  
                    ?>
                        <?php if($filer_array['tenure_type']=='2'){?>
                          @section('rent', 'active')   {{__('for_rent')}} <?php }  
                            ?> 
                    in Georgia
                    <input type='hidden' name='tenure_type' id='tenure_type' value='{{$filer_array['tenure_type']}}'>
                    <?php } else{?>
                    {{__('new_construction')}}
                    <input type='hidden' name='tenure_type' id='tenure_type' value=''>
                    
                    <?php } ?>
                    <span>{{ __('in_and_around') }}</span>
                </h2>
            </div>
            <div class="small-12 medium-5 large-4 column filterTextRight">
                <div class="dropdown dropList filter_options">
                    <input type="text" id="sort_by" value="All"  dara-rel="0" placeholder="SORT BY:  Price" readonly="">
                    <ul>
                        <li data-rel="0">All</li> 
                        <li data-rel="1">Price Asc</li>
                        <li data-rel="2">Price Desc</li>
                        <li data-rel="3">New</li>
                    </ul>
                </div>
                <div class="dropdown dropList filter_options">
                    <input id="show_number" value="SHOW :  3" data-rel="0" value="All" type="text" placeholder="SHOW :  3" readonly="">
                    <ul>
                        <li data-rel="3">SHOW : 3</li> 
                        <li data-rel="10">SHOW : 10</li>
                        <li data-rel="20">SHOW : 20</li> 
                        <li data-rel="50">SHOW : 50</li>
                        <li data-rel="100">SHOW : 100</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="listContent">
            <div class="productLeft">
                <div class="leftContent">
                    <strong>All Types</strong>
                    <div class="mobTabContent">
                        <?php
                        $building_type = array();
                        if (isset($filer_array['building_type'])) {
                            $building_type = $filer_array['building_type'];
                        }
                        ?>
                        <ul class="allType filter_checkbox">
                            <li>
                                <figure><img src="{{ URL::asset('web/images')}}/iconHouseList.png" alt="Home"></figure>
                                <span>{{__('houses')}}</span>
                                <label>
                                    <input 
                                    <?php
                                    if (in_array(1, $building_type)) {
                                        echo ' checked="" ';
                                    }
                                    ?>
                                        value="1" type="checkbox" name="building_type[]" >
                                    <em></em>
                                </label>
                            </li>
                            <li>
                                <figure><img src="{{ URL::asset('web/images')}}/iconApartment.png" alt="Apartment"></figure>
                                <span>{{__('apartments')}}</span>
                                <label>
                                    <input <?php
                                    if (in_array(2, $building_type)) {
                                        echo ' checked="" ';
                                    }
                                    ?> type="checkbox" name="building_type[]"  value="2"  >
                                    <em></em>
                                </label>
                            </li>
                            <li>
                                <figure><img src="{{ URL::asset('web/images')}}/iconCommercialList.png" alt="Commercial"></figure>
                                <span>{{__('commercials')}}</span>
                                <label>
                                    <input  <?php
                                    if (in_array(3, $building_type)) {
                                        echo ' checked="" ';
                                    }
                                    ?>  type="checkbox" name="building_type[]"  value="3" >
                                    <em></em>
                                </label>
                            </li>
                            <li>
                                <figure><img src="{{ URL::asset('web/images')}}/iconListOffice.png" alt="Offices"></figure>
                                <span>{{__('offices')}}</span>
                                <label>
                                    <input type="checkbox" <?php
                                    if (in_array(4, $building_type)) {
                                        echo ' checked="" ';
                                    }
                                    ?> name="building_type[]"   value="4" >
                                    <em></em>
                                </label>
                            </li>
                            <li>
                                <figure><img src="{{ URL::asset('web/images')}}/iconListOffice.png" alt="Parking"></figure>
                                <span>{{__('parking')}}</span>
                                <label>
                                    <input type="checkbox" <?php
                                    if (in_array(5, $building_type)) {
                                        echo ' checked="" ';
                                    }
                                    ?> name="building_type[]"   value="5" >
                                    <em></em>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="leftContent gemeentes">
                    <strong>{{__('all_municipalities')}}</strong>
                    <div class="mobTabContent">
                        <div class="inputSearch">
                            <input class="city_filter" type="text" placeholder="Enter City">
                            <input type="submit">
                        </div> 
                        <div class="cityArea">
                            <div class="cityScroll">
                                <div id="scroller">
                                    <ul id="city_list" class="filter_checkbox">
                                        <?php
                                       $list_places = ListHelper::municipalityList();
                                       // die();
                                        $muncipality_id = array();
                                        if (isset($filer_array['muncipality_id'])) {
                                            $muncipality_id = $filer_array['muncipality_id'];
                                        }
                                        ?>
                                        <?php foreach ($list_places as $place) {
                                            ?>
                                            <li>
                                                <label>
                                                    <input   
                                                    <?php
                                                    if (in_array($place->id, $muncipality_id)) {
                                                        echo " checked='checked' ";
                                                    }
                                                    ?>
                                                        value="{{$place->id}}" type="checkbox" name="places[]">
                                                    <em></em>
                                                </label>
                                                <span>{{$place->name}} ({{$place->property_count}}) 
                                                    <?php // print_r($muncipality_id) ;  var_dump((in_array($place->id, $muncipality_id)));    ?>
                                                </span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="leftContent">
                    <strong>Price Range</strong>
                    <div class="mobTabContent">
                        <div class="rangeSlider">
                            <div class="range-slider">
                                <input type="text"  id="price_range" class="js-range-slider" value="" />
                            </div>
                            <div class="priceRange">
                                Price Range:
                            </div>
                        </div>
                    </div>  
                </div>
                <input type="hidden" name="priceFilter" id="priceFilter" value="<?php echo $filer_array['pricerange'];?>">
                <div class="leftContent roomBooking">
                    <strong>Filter by</strong>
                    <div class="mobTabContent">
                        <div class="selectRooms Bedroom">
                            <label for="bed_rooms">BedRooms</label>
                            <div class="selectInput"><input type="text" name="bath_rooms" id="bed_rooms" value="0"></div>
                            <div class="customClear"></div>
                        </div>
                        <div class="selectRooms">
                            <label for="bath_rooms">Bathrooms</label>
                            <div class="selectInput"><input type="text" name="bath_rooms" id="bath_rooms" value="0"></div>
                            <div class="customClear"></div>
                        </div>
                        <ul class="filter_checkbox">
                            <li>
                                <span>Terrace</span>
                                <label>
                                    <input type="checkbox" id="terrace">
                                    <em></em>
                                </label>
                            </li>
                            <li>
                                <span>Garden</span>
                                <label>
                                    <input type="checkbox" id="garden">
                                    <em></em>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="productRight allListingWrapper" > 
                <div id="product_results">
                    
                </div>
                <div class="row pagination">

        </div>
            </div>
        </div>
        
    </div>
</section>
    @include('web.includes.our_services')
<script>
$(document).ready(function ($) {
    getData();
    $('.city_filter').keyup(function () {
        loadCityFilter();
    });
    
   
    $(document).on('click', '.pagination a', function (event)
    {
        event.preventDefault();

        // $('.pagination').find('li').removeClass('active');
        // $('.pagination').find('li').removeAttr('aria-current');
        //$(this).parent('li').addClass('active');
        // $(this).parent('li').attr('aria-current','page');
        var page = $(this).attr('href').split('page=')[1];
        $('#currentPage').val(page);
        getData();

    });

   /* $('.filter_options li,.filter_checkbox li em').click( function () {
        setTimeout(function () {
            getData();
        }, 50);
    });*/
    
     $('body').on('click', '.filter_options li,.filter_checkbox li em', function () {
                  setTimeout(function () {
                     getData();
                     }, 50);
        });

    $('#bed_rooms,#bath_rooms').on('change', function () {
        setTimeout(function () {
            getData();
        }, 50);
    }); 
    $('#price_range').on('change', function () {
        $('#priceFilter').val('');
        setTimeout(function () {
            getData();
        }, 50);
    });
});
var xhrStarted = null;
function getData() {
    var building_type = $(".allType input:checkbox:checked").map(function () {
        return $(this).val();
    }).get();
    var places = $(".cityArea input:checkbox:checked").map(function () {
       
        return $(this).val();
    }).get();
    var price_range = $('#price_range').val();
    var PriceFilter = $('#priceFilter').val();
    if(PriceFilter!=""){
       price_range=PriceFilter;
     }
    var price_range = price_range.split(";");
    var price_range_to = "";
    var price_range_from = 0;
    if (price_range.length == 2) {
        price_range_from = price_range[0];
        price_range_to = price_range[1];

    }
    if (typeof (xhrStarted) != undefined && xhrStarted != null) {
        xhrStarted.abort();
        xhrStarted = null;
    }
    var bed = $('#bed_rooms').val();
    var bath = $('#bath_rooms').val();
    var terrace = 0;
    var garden = 0;
    var tenure_type=$('#tenure_type').val();
    if ($('#garden').is(':checked')) {
        garden = 1;
    }
    if ($('#terrace').is(':checked')) {
        terrace = 1;
    }
    var show_number = $('#show_number').attr('data-rel');
    var sort_by = $('#sort_by').attr('data-rel');
    var pageNum = 1;
    if( $('#currentPage').length>0){
       pageNum= $('#currentPage').val();
    }
    
    startLoader();
    xhrStarted = $.ajax(
            {
                url: 'ajax_search?page=' + pageNum,
                type: "post",
                datatype: "json",
                data: {
                    building_type: building_type, places: places, price_range_from: price_range_from, price_range_to: price_range_to,
                    bed: bed,tenure_type:tenure_type, 
                    bath: bath, terrace: terrace, garden: garden, sort_by: sort_by, show_number: show_number},
            })
            .done(function (data)
            {
                stopLoader();
                if (typeof (data) == 'object') {
                    var htmlData = data;
                    if (!htmlData.error) {
                        //if item count is 0 and page number is >1 
                        if (htmlData.itemCount == 0) {
                            if (pageNum > 1) {
                                $('#currentPage').val(1);
                                getData();
                            }
                        }
                        $("#product_results").html(htmlData.html);
                        $('.pagination').html(htmlData.paginateHtml);
                    } else {
                        alert("Failed to load Data");
                    }
                    
                      //all listing change background image
                        $(".allListBoxHolder").each(function() {
                           var imgPath = $(this).find(".listImgHolder img").attr('src');
                           console.log(imgPath);
                           $(this).find(".listImgHolder").css('background-image', 'url("'+ imgPath +'")');
                       });
                }

                location.hash = pageNum;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                stopLoader();
            });
}

var xhrCity = null;
function loadCityFilter() {
    var city_text = $('.city_filter').val();
    if (typeof (xhrCity) != undefined && xhrCity != null) {
        xhrCity.abort();
        xhrCity = null;
    }
    xhrCity = $.ajax(
            {
                url: 'ajax_city_filter/' + city_text,
                type: "get",
                datatype: "json",
            })
            .done(function (data)
            {
                if (typeof (data) == 'object') {
                    var htmlData = data;
                    if (!htmlData.error) {
                        $("#city_list").html(htmlData.html);
                    } else {
                        alert("Failed to load Data");
                    }
                }

            })
}
function triggerGridLi(num) {
    $('.tabMenu li').eq(num).trigger('click');
}
</script>

@endsection