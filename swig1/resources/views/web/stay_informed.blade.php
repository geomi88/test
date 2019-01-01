@extends('layout.web.website')
@section('content')
@section('title', 'Stay Informed')
@section('stayinformed', 'active')
@section('pageClass', 'detailPage')
@section('innerClass', 'withoutBanner')
@include('web.includes.banner_inner')
<link type="text/css" href="{{ URL::asset('web')}}/css/select2.min.css" rel="stylesheet" media="all" />
<script src="{{ URL::asset('web')}}/js/select2.full.min.js" type="text/javascript"></script>

<script src="{{ URL::asset('common')}}/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('common')}}/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('common')}}/js/additional-methods.js" type="text/javascript"></script>
<style>
span.select2-selection.select2-selection--multiple {
    border: 1px solid #ddd!important;
}
</style>
                <section class="bottomSection">
                       <div class="row contactus-text">
                                <div class="small-12 medium-12 large-12 column">
                                    <h2>An exceptional experience since 1994 with great expertise in residential real estate in and around Antwerp.</h2>
                                </div>
                                 <p> Our professional and enthusiastic team of certified and BIV-certified real estate agents guarantees a fair and high quality guidance when looking for a new home as well as finding a
                                 new destination for your real estate.</p>
                                 <br>
                                <p>Both the tenant, the landlord, the buyer and the seller will receive an optimal service in which personal attention is central.</p>
                        </div>


                    <div class="row">
                        <div class="ourServices">
<!--                            <h3>Our Services</h3>
                            <div class="serviceTop">
                                <div class="serviceContent">
                                    <a href="{{URL::to('owner/login')}}">
                                    <figure class="bgService owners"><img src="images/imgOwner.png" alt=""></figure>
                                    </a>
                                    <p>
                                    <span>For Owners</span>
                                    Are you owner? Log in here and follow all the details of your file.
                                    </p>
                                </div>
                                <div class="serviceContent">
                                     <a href="{{URL::to('web/stayinformed')}}">
                                    <figure class="bgService stayInform"><img src="images/imgStayInform.png" alt=""></figure>
                                     </a>
                                    <p>
                                    <span>Stay Informed</span>
                                    Buying or renting? We are happy to keep you informed of the new buildings and projects.
                                    </p>
                                </div>
                                <div class="serviceContent">
                                    <a href="{{URL::to('web/newconstruction')}}">
                                    <figure class="bgService newConstruction"><img src="images/imgNewConstruction.png" alt=""></figure>
                                   </a>
                                    <p>
                                    <span>New Construction</span>
                                    View our range of new construction projects in and around Antwerp
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="small-12 medium-12 large-12 column">
                                    <div class="contactMapHolder">
                                        <iframe width="100%" height="600" src="https://maps.google.com/maps?width=100%&amp;height=600&amp;hl=en&amp;q=1%20Grafton%20Street%2C%20Dublin%2C%20Ireland+(My%20Business%20Name)&amp;ie=UTF8&amp;t=&amp;z=15&amp;iwloc=B&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                    </div>
                                </div>
                            </div>-->
                            <form method="post" action="{{ action('Web\WebController@savestayinformed') }}" id="savestayinformed" enctype="multipart/form-data" class="formTypeV1">
                            <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
                            {{ csrf_field() }}
                            <div class="row serviceBottom newContact">
                                <div class="small-12 medium-8 large-8 column serviceLeft">
                                    <h3>THE MASTER, YOUR HOUSE OF TRUST</h3>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="First Name" name="first_name" id="first_name">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="Last name" name="last_name" id="last_name">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="Street & number" name="street_number" id="street_number">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="City" name="city" id="city">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="Postcode" name="zip" id="zip">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="District" name="district" id="district">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="Email" name="email" id="email">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="Phone number" name="phone" id="phone">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput searchFilterWidth dropdownContact dropdown dropList forSaleDrop">
                                        <input type="text" placeholder="For Sale" readonly="readonly" id="for_tenure_dd" data-rel="1">
                                        <ul style="display: none;">
                                            <li data-rel="1">{{__('for_sale')}}</li>
                                            <li data-rel="2">{{__('for_rent')}}</li>
                                        </ul>
                                    </div>
                                    <div class="small-12 medium-6 large-4 column ">
                                        <div class="row">
                                            <div class="small-6 medium-6 large-6 column  newStyleInput pdRight">
                                                <input type="number" placeholder="Min" name="min_price" id="min_price">
                                            </div>
                                            <div class="small-6 medium-6 large-6 column  newStyleInput ">
                                                <input type="number" placeholder="Max" id="max_price" name="max_price">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="small-12 medium-6 large-4 column ">

                                    <div class="buildingTypeDropdown select2Dropdown select2FullDropdown" id="building">

                                        <select id="building_type"  name="building_type[]" class="form-control select2" data-placeholder="Building Type"
                                            multiple="multiple">

                                            <option value="1">{{__('houses')}}</option>
                                            <option value="2">{{__('apartments')}}</option>
                                            <option value="3">{{__('commercials')}}</option>
                                            <option value="4">{{__('office')}}</option>
                                        </select>
                    <!--                    <input type="text" id="type_dd"  placeholder="All Types" readonly>
                                        <ul>
                                            <li data-rel="1">Houses</li>
                                            <li data-rel="2">Apartments</li>
                                            <li data-rel="3">Commercial</li>
                                            <li data-rel="4">Office</li>
                                        </ul>-->
                                    </div>
                                    </div>
                                    <div class="small-12 medium-6 large-4 column ">

                                    <div class="placeDropdown select2Dropdown select2FullDropdown" id="places" >
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
                                    </div>
                                    <div class="small-12 medium-12 large-12 column newStyleInput noPd">
                                    <textarea row="5" cols="45" placeholder="Your message or extra comment..." name="message" id="message"></textarea>
                                    </div>
                                    <div class="customClear"></div>
                                    <div class="checkboxHolder">
                                    <label>
                                    <input type="checkbox" name="stay_informed" id="stay_informed">
                                    <em></em>
                                    </label>
                                    <span>Stay informed of our offer </span>
                                    </div>
                                    <div class="checkboxHolder">
                                    <label>
                                    <input type="checkbox" name="privacy_policy" id="privacy_policy">
                                    <em></em>
                                    </label>
                                    <span>
                                        I have read and agree to the disclaimer and privacy policy.<br> I hereby consent to the
                                            processing of my data and to contact me.</span>
                                    </div>
                                    <div class="small-12 medium-12 large-12 inputHolder column ">
                                        <input type="submit" value="Submit" id="btnsaveStayInformed">
                                    </div>
                                    <div class="submitMessage stayInformedSuccess" id="confirmation_message"></div>

                                </div>
                                <div class="small-12 medium-4 large-4 column serviceRight">
                                <h3>Real estate agency luxestate</h3>
                                <ul>
                                    <li>Jordaenskaai 21, 2000 Antwerp </li>
                                    <li>T : 5653265656</li>
                                    <li>E : info@luxestate .be</li>
                                </ul>
                                <p>BIV-certified real estate broker-broker</p>
                                <ul class="listTop">
                                    <li>BIV 200.803 - Belgium</li>
                                    <li>VAT BE 0453.256.254</li>
                                </ul>
                                <p>
                                Supervisory authority: Professional<br> Institute of Real Estate Agents, Rue de
                                Luxembourg <br>16 B, 1000 Brussels.
                                Subject to the code of ethics of the IPI.<br>
                                Recognized Quality Member of CIB<br>
                                You will find the applicable corporate CIS corporate rules under CIB.be
                                </p>
                                <ul class="socialFooter">
                                    <li><img src="images/iconTwitterFooter.png" alt=""></li>
                                    <li><img src="images/iconFbFooter.png" alt=""></li>
                                    <li><img src="images/iconInFooter.png" alt=""></li>
                                </ul>
                                </div>
                            </div>
                            <input type="hidden" id="tenure_type" name="tenure_type">

                            </form>
                        </div>
                    </div>
                </section>
    <script>

      $(document).ready(function ($) {
        $("#savestayinformed").validate({
                            errorElement: 'span',
                            errorClass: "webErrorMsg",
                            ignore: '',
                            highlight: function (element, errorClass) {
                                $(element).addClass('valErrorV1');
                                $("#" + element.id + "_chosen").find('.chosen-single').addClass('valErrorV1');
                            },
                            unhighlight: function (element, errorClass, validClass) {
                                $(element).removeClass('valErrorV1');
                            },
                            rules: {

                                first_name: {
                                    required: true,
                                },
                                last_name: {
                                    required: true,
                                },
                                email: {
                                    required: true,
                                    email:true,
                                },
                                phone: {
                                    required: true,
                                    digits: true,
                                },
                                message: {
                                    required: true,
                                },
                                privacy_policy : {
                                    required: true,
                                },
                                street_number : {
                                    required: true,
                                },
                                city : {
                                    required: true,
                                },
                                zip : {
                                    required: true,
                                    digits: true,
                                },
                                district : {
                                    required: true,
                                }




                            },
                            messages: {

                                first_name: {
                                    required: "Please Enter First Name",
                                },
								last_name: {
                                    required: "Please Enter Last Name",
                                },
                                email: {
                                    required: "Please Enter Email Address",
                                    email:"Please enter a valid email address"
                                },

								phone: {
                                    required: "Please Enter Phone Number",
                                },
                                message: {
                                    required: "Please Enter Message",
                                },
                                privacy_policy: {
                                    required: "Please Agree to Terms And Conditions",
                                },
                                street_number: {
                                    required: "Please Enter Street & Number",
                                },
                                city: {
                                    required: "Please Enter City",
                                },
                                zip: {
                                    required: "Please Enter Postal Code",
                                },
                                district: {
                                    required: "Please Enter District",
                                },

                            },
                        });
                $('body').on('click', "#btnsaveStayInformed", function (e) {
                            if (!$("#savestayinformed").valid()) {
                                return false;
                            }

                            var base_path = $('#base_path').val();
                            var first_name = $('#first_name').val();
                            var last_name = $('#last_name').val();
                            var email = $('#email').val();
                            var phone = $('#phone').val();
                            var message = $('#message').val();
                            var street_number = $('#street_number').val();
                            var city = $('#city').val();
                            var zip = $('#zip').val();
                            var district = $('#district').val();
                            var min_price = $('#min_price').val();
                            var max_price = $('#max_price').val();
                            var places_list = $('#places_list').val();
                            var building_type = $('#building_type').val();
                            var tenure_type = $('#for_tenure_dd').attr('data-rel');
                            var stay_informed = 0;
                            if ($('#stay_informed').is(":checked"))
                            {
                                stay_informed = 1;
                            }
                            $("#btnsaveStayInformed").attr("disabled", "disabled");
                            $.ajax({
                                type: 'post',
                                url: base_path+'/web/savestayinformed',
                                data: {first_name: first_name,last_name:last_name,email:email,phone:phone,message:message,
                                    stay_informed:stay_informed,street_number:street_number,city:city,zip:zip,tenure_type:tenure_type,
                                    district:district,min_price:min_price,max_price:max_price,places_list:places_list,building_type:building_type
                                },
                                async: false,

                                success: function (data) {
                                    $('#savestayinformed')[0].reset();
                                    //$('.select2-selection__choice').html('');
                                    //$("#privacy_policy").prop("checked",false);
                                    $('#confirmation_message').show();
                                    $('#confirmation_message').text('Successfully Submitted');
                                    setTimeout(function() {
                                        $('#confirmation_message').fadeOut('fast');
                                    }, 3000);

                                    $("#btnsaveStayInformed").attr("disabled", false);
                                }

                            });
                            e.preventDefault();
                        });
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
    </script>
 @endsection
