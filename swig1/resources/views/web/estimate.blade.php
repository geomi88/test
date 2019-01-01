@extends('layout.web.website')
@section('content')
@section('title', 'Architect List')
@section('estimate', 'active')
@section('pageClass', 'detailPage')
@section('innerClass', 'withoutBanner')
@include('web.includes.banner_inner')
<link type="text/css" href="{{ URL::asset('web')}}/css/select2.min.css" rel="stylesheet" media="all" />
<script src="{{ URL::asset('web')}}/js/select2.full.min.js" type="text/javascript"></script>

<script src="{{ URL::asset('common')}}/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('common')}}/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('common')}}/js/additional-methods.js" type="text/javascript"></script>
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
                                    <figure class="bgService owners">
                                        <img src="images/imgOwner.png" alt="">
                                    </figure>
                                    </a>
                                    <p>
                                    <span>For Owners</span>
                                    Are you owner? Log in here and follow all the details of your file.
                                    </p>
                                </div>
                                <div class="serviceContent">
                                    <a href="{{URL::to('web/stayinformed')}}">
                                    <figure class="bgService stayInform">
                                        <img src="images/imgStayInform.png" alt="">
                                    </figure>
                                    </a>
                                    <p>
                                    <span>Stay Informed</span>
                                    Buying or renting? We are happy to keep you informed of the new buildings and projects.
                                    </p>
                                </div>
                                <div class="serviceContent">
                                    <a href="{{URL::to('web/newconstruction')}}">
                                    <figure class="bgService newConstruction">
                                        <img src="images/imgNewConstruction.png" alt="">
                                    </figure>
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
                            <form method="post" action="{{ action('Web\WebController@saveestimate') }}" id="saveestimate" enctype="multipart/form-data" class="formTypeV1">
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
                                    <input type="text" placeholder="Telephone number" name="tele_phone" id="tele_phone">
                                    </div>
                                    <div class="small-12 medium-6 large-4 column  newStyleInput">
                                    <input type="text" placeholder="Mobile number" name="mobile_phone" id="mobile_phone">
                                    </div>
                                    <div class="small-12 medium-12 large-12 column newStyleInput">
                                    <textarea row="5" cols="45" placeholder="I want to ask estimation for..." name="message" id="message"></textarea>
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
                                        <input type="submit" value="Submit" id="btnsaveEstimate">
                                    </div>
                                    <div class="submitMessage" id="confirmation_message"></div>
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
                        </div>
                    </div>
                </section>
    <script>

      $(document).ready(function ($) {
        $("#saveestimate").validate({
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
                                tele_phone: {
                                    required: true,
                                    digits: true,
                                },
                                mobile_phone: {
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

								tele_phone: {
                                    required: "Please Enter Phone Number",
                                },
                                mobile_phone: {
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
                $('body').on('click', "#btnsaveEstimate", function (e) {
                            if (!$("#saveestimate").valid()) {
                                return false;
                            }
                            $("#btnsaveEstimate").attr("disabled", "disabled");
                            var base_path = $('#base_path').val();
                            var first_name = $('#first_name').val();
                            var last_name = $('#last_name').val();
                            var email = $('#email').val();
                            var tele_phone = $('#tele_phone').val();
                            var mobile_phone = $('#mobile_phone').val();
                            var message = $('#message').val();
                            var street_number = $('#street_number').val();
                            var city = $('#city').val();
                            var zip = $('#zip').val();
                            var district = $('#district').val();

                            var stay_informed = 0;
                            if ($('#stay_informed').is(":checked"))
                            {
                                stay_informed = 1;
                            }
                            $.ajax({
                                type: 'post',
                                url: base_path+'/web/saveestimate',
                                data: {first_name: first_name,last_name:last_name,email:email,tele_phone:tele_phone,mobile_phone:mobile_phone,message:message,
                                    stay_informed:stay_informed,street_number:street_number,city:city,zip:zip,
                                    district:district
                                },
                                async: false,

                                success: function (data) {
                                    $('#saveestimate')[0].reset();
                                    //$("#privacy_policy").prop("checked",false);
                                    $('#confirmation_message').show();
                                    $('#confirmation_message').text('Successfully Submitted');
                                    setTimeout(function() {
                                        $('#confirmation_message').fadeOut('fast');
                                    }, 3000);

                                    $("#btnsaveEstimate").attr("disabled", false);
                                }

                            });
                            e.preventDefault();
                        });
                    });
    </script>
 @endsection
