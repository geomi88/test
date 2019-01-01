<link type="text/css" href="{{ URL::asset('web')}}/css/select2.min.css" rel="stylesheet" media="all" />
<!--<link type="text/css" href="{{ URL::asset('common')}}/chosen/chosen.min.css" rel="stylesheet" media="all" />
<script src="{{ URL::asset('common')}}/chosen/chosen.jquery.min.js" type="text/javascript"></script>-->


    <script src="{{ URL::asset('web')}}/js/select2.full.min.js" type="text/javascript"></script>
<section class="homeHero" style="background-image: url({{ URL::asset('web/images')}}/bgBanner.jpg)">
    <img src="{{ URL::asset('web/images')}}/bgBanner.jpg" alt="Luxestate">
    <div class="heroContent">
        <span> {{ __('property_for_sale&rent') }}</span>
        <h1> {{ __('search&find') }}<em>{{ __('apartments') }}</em></h1>
        <form action="properties/search" onsubmit="return searchProperties();" id="seach_properties" method="GET" >
            <input type="hidden" value="{{sha1(time())}}" name="session" >
            {!! csrf_field() !!}
            <div class="filterHandle">
                <div class="dropdown dropList">
                    <input id="for_tenure_dd"  data-rel="1" type="text" placeholder="For Sale" readonly>
                    <ul>
                        <li data-rel="1">{{__('for_sale')}}</li>
                        <li data-rel="2">{{__('for_rent')}}</li>
                    </ul>
                </div>
                <div class="buildingTypeDropdown select2Dropdown" id="building">

                     <select id="building_type"  name="building_type[]" class="form-control select2" data-placeholder="Building Type"
                           multiple="multiple">

                        <option value="1">{{__('houses')}}</option>
                        <option value="2">{{__('apartments')}}</option>
                        <option value="3">{{__('commercials')}}</option>
                        <option value="4">{{__('offices')}}</option>
                        <option value="5">{{__('parking')}}</option>
                    </select>
<!--                    <input type="text" id="type_dd"  placeholder="All Types" readonly>
                    <ul>
                        <li data-rel="1">Houses</li>
                        <li data-rel="2">Apartments</li>
                        <li data-rel="3">Commercial</li>
                        <li data-rel="4">Office</li>
                    </ul>-->
                </div>

                <div class="placeDropdown select2Dropdown" id="places" >
                    <?php
$list_places = ListHelper::municipalityList();
?>
                    <select id="places_list"  name="places_list[]" class="form-control select2" data-placeholder="{{__('all_municipalities')}}"
                            placeholder="{{__('all_municipalities')}}" multiple="multiple">
                        <option value=""></option>
                        <?php foreach ($list_places as $place) {?>
                            <option value="{{$place->id}}">{{$place->name}}</option>
                        <?php }?>
                    </select>
                </div>





                 <div class="searchFilterWidth priceRangeDropdown">
                                <input id="price_dd" type="text" placeholder="{{__('all_price_classes')}}" readonly>
                                <ul>
                                    <li class="priceInputHolder">
                                        <div class="halfWidthHolder">
                                            <input type="number" name="fromPrice" id="fromPrice" placeholder="MIN">
                                            <ul>
                                                <li>0</li>
                                                <li>250000</li>
                                                <li>500000</li>
                                            </ul>
                                        </div>
                                        <div class="halfWidthHolder">
                                            <input type="number" name="toPrice" id="toPrice" placeholder="MAX" number>
                                            <ul>
                                                <li>250000</li>
                                                <li>500000</li>
                                                <li>1000000</li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>


                <input type="submit" value="Search">
            </div>
            <input type="hidden" id="for_tenure" name="for_tenure">
            <input type="hidden" id="price" name="price">


        </form>
        <div class="moreProperties">
            <span class="titleText">{{__('browse more property types')}}</span>
            <ul>
                <li><a href="properties/search?session={{sha1(time())}}&building_type[]=1"><figure><img src="{{ URL::asset('web/images')}}/iconHome.png" alt="Home"><figcaption>{{__('houses')}}</figcaption></figure></a></li>
                <li><a href="properties/search?session={{sha1(time())}}&building_type[]=2"><figure><img src="{{ URL::asset('web/images')}}/iconAppartment.png" alt="Appartment"><figcaption>{{__('apartments')}}</figcaption></figure></a></li>
                <li><a href="properties/search?session={{sha1(time())}}&building_type[]=3"><figure><img src="{{ URL::asset('web/images')}}/iconCommercial.png" alt="Commercial"><figcaption>{{__('commercials')}}</figcaption></figure></a></li>
                <li><a href="properties/search?session={{sha1(time())}}&building_type[]=4"><figure><img src="{{ URL::asset('web/images')}}/iconOffice.png" alt="Office"><figcaption>{{__('offices')}}</figcaption></figure></a></li>
            </ul>
        </div>
    </div>
</section>

<script>

//set all the values in hidden field
            function searchProperties() {
                $('#for_type').val("");
                $('#price').val("");
               // $('#place').val("");
                var for_tenure_dd = $('#for_tenure_dd').attr('data-rel');
                if (for_tenure_dd != "") {
                    $('#for_tenure').val(for_tenure_dd);
                }
//                var type_dd = $('#type_dd').attr('data-rel');
//                if (type_dd != "") {
//                    $('#type').val(type_dd);
//                }


                //var price_dd = $('#price_dd').attr('data-rel');
                var price_dd =$('#price_dd').val();
                if (price_dd != "") {
                    $('#price').val(price_dd);
                }
                return true;
            }
            $(document).ready(function ($) {
                  $.fn.select2.defaults.set( "width", "100%" );
                  $('.select2').select2({
                      minimumResultsForSearch: Infinity,
                      language: "nl",
                      width:"100%"
                  });

                  $('.select2.nosearch').on('select2:opening select2:closing', function( event ) {
                      var $searchfield = $(this).parent().find('.select2-search__field');
                      $searchfield.prop('disabled', true);
                  });

                  var citycounter = $("#places .select2-selection__choice").length;
                  var buildingcounter = $("#building .select2-selection__choice").length;


                if (citycounter > 1) {

                    $('#places .select2-selection__choice:not(:first)').hide();
                    $('#places .select2-selection__choice:last').after('<span class="counter"> + ' + (citycounter - 1) + '</span>');
                }
                if (buildingcounter > 1) {

                    $('#building .select2-selection__choice:not(:first)').hide();
                    $('#building .select2-selection__choice:last').after('<span class="counter"> + ' + (buildingcounter - 1) + '</span>');
                }


            });

            $(document).on('change', 'select#places_list', function (event) {
                test();
            });
            $(document).on('change', 'select#building_type', function (event) {
                building();
            });

         function test(){
            var citycounter = $("#places .select2-selection__choice").length;

                if (citycounter > 1) {

                    $('#places .select2-selection__choice:not(:first)').hide();
                    $('#places .select2-selection__choice:last').after('<span class="counter"> + ' + (citycounter - 1) + '</span>');
                }
           }

         function building(){
            var buildingcounter = $("#building .select2-selection__choice").length;

                if (buildingcounter > 1) {

                    $('#building .select2-selection__choice:not(:first)').hide();
                    $('#building .select2-selection__choice:last').after('<span class="counter"> + ' + (buildingcounter - 1) + '</span>');
                }
           }

        $('body').on('click', '.priceRangeDropdown ul li', function () {
              var from= $('#fromPrice').val();
            var to= $('#toPrice').val();
           if(from=="" && to==""){
                var price= '0-0';
                $('#price_dd').val(price);
            }else if(from=="" && to!=""){
                var price= '0'+'-'+to;
                $('#price_dd').val(price);
            }else if(from!="" && to==""){
                var price= from+'-'+'0';
                $('#price_dd').val(price);
            }else{
                var price= from+'-'+to;
                $('#price_dd').val(price);
            }
        });

        $('body').on('keyup', '#fromPrice', function () {
            var from= $('#fromPrice').val();
            var to= $('#toPrice').val();
           if(from=="" && to==""){
                var price= '0-0';
                $('#price_dd').val(price);
            }else if(from=="" && to!=""){
                var price= '0'+'-'+to;
                $('#price_dd').val(price);
            }else if(from!="" && to==""){
                var price= from+'-'+'0';
                $('#price_dd').val(price);
            }else{
                var price= from+'-'+to;
                $('#price_dd').val(price);
            }


        });
        $('body').on('keyup', '#toPrice', function () {

            var from= $('#fromPrice').val();
            var to= $('#toPrice').val();
            if(from=="" && to==""){
                var price= '0-0';
                $('#price_dd').val(price);
            }else if(from=="" && to!=""){
                var price= '0'+'-'+to;
                $('#price_dd').val(price);
            }else if(from!="" && to==""){
                var price= from+'-'+'0';
                $('#price_dd').val(price);
            }else{
                var price= from+'-'+to;
                $('#price_dd').val(price);
            }

        });
</script>

