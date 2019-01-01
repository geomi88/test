<script src="{{ URL::asset('common')}}/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('common')}}/js/additional-methods.js" type="text/javascript"></script>
<section class="bottomSection">
    <div class="row">
        <div class="ourServices">
            <h3>Our Services</h3>
            <div class="serviceTop">
                <div class="serviceContent">
                    <a href="{{URL::to('owner/login')}}">
                    <figure class="bgService owners"><img src="{{URL::asset('web')}}/images/imgOwner.png" alt=""></figure>
                    </a>
                    <p>
                        <span>For Owners</span>
                        Are you owner? Log in here and follow all the details of your file.
                    </p>
                </div>
                <div class="serviceContent">
                     <a href="{{URL::to('web/stayinformed')}}">
                    <figure class="bgService stayInform"><img src="{{URL::asset('web')}}/images/imgStayInform.png" alt=""></figure>
                    </a>
                    <p>
                        <span>Stay Informed</span>
                        Buying or renting? We are happy to keep you informed of the new buildings and projects.
                    </p>
                </div>
                <div class="serviceContent">
                    <a href="{{URL::to('web/newconstruction')}}">
                    <figure class="bgService newConstruction"><img src="{{URL::asset('web')}}/images/imgNewConstruction.png" alt=""></figure>
                    </a>
                    <p>
                        <span>New Construction</span>
                        View our range of new construction projects in and around Antwerp
                    </p>
                </div>
            </div>
            <form method="post" action="{{ action('Web\WebController@savecontactform') }}" id="savecontactform" enctype="multipart/form-data" class="formTypeV1">
            <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
            <input type="hidden" name="propertyId" id="propertyId">
            <div class="row serviceBottom" id="interestedForm">
                <div class="small-12 medium-6 large-6 column serviceLeft">
                    {{ csrf_field() }}
                    <h3>THE MASTER, YOUR HOUSE OF TRUST</h3>
                    <div class="small-12 medium-6 large-6 column inputHolder">
                        <input type="text" placeholder="First Name" name="first_name" id="first_name">
                    </div>
                    <div class="small-12 medium-6 large-6 column inputHolder">
                        <input type="text" placeholder="Last Name" name="last_name" id="last_name">
                    </div>
                    <div class="small-12 medium-6 large-6 column inputHolder">
                        <input type="text" placeholder="Email Address" name="email" id="email">
                    </div>
                    <div class="small-12 medium-6 large-6 column inputHolder">
                        <input type="text" placeholder="Phone" name="phone" id="phone">
                    </div>
                    <div class="small-12 medium-12 large-12 column inputHolder textareaHolder contactMessage">
                        <textarea row="5" cols="45" placeholder="Message" name="message" id="message"></textarea>
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
                    <div class="small-12 medium-12 large-12 column inputHolder submitMessage">
                        <input type="submit" value="Submit" id="btnsaveContactForm">
                    </div>
                    <div class="submitMessage" id="confirmation_message"></div>
                </div>
                <div class="small-12 medium-6 large-6 column serviceRight">
                    <h3>Real estate agency luxestate</h3>
                    <ul>
                        <li>Jordaenskaai 21, 2000 Antwerp </li>
                        <li>T : 5653265656</li>
                        <li>E : info@luxestate .be</li>
                    </ul>
                    <p>BIV-certified real estate broker-broker</p>
                    <ul class="listTop">
                        <li>BIV 200.803 - Belgium</li>
                        <li>VAT BE 0453.256.2549</li>
                    </ul>
                    <p>
                        Supervisory authority: Professional Institute of Real Estate Agents, Rue de<br>
                        Luxembourg 16 B, 1000 Brussels.<br>
                        Subject to the code of ethics of the IPI.<br>
                        Recognized Quality Member of CIB<br>
                        You will find the applicable corporate CIS corporate rules under CIB.be
                    </p>
                    <ul class="socialFooter">
                        <li><img src="{{URL::asset('web')}}/images/iconTwitterFooter.png" alt=""></li>
                        <li><img src="{{URL::asset('web')}}/images/iconFbFooter.png" alt=""></li>
                        <li><img src="{{URL::asset('web')}}/images/iconInFooter.png" alt=""></li>
                    </ul>
                </div>
            </div>
            </form>

        </div>
    </div>
    <script>
        $("#savecontactform").validate({
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

                            },
                        });
                        $(document).ready(function() {
                            $('body').on('click', "#btnsaveContactForm", function (e) {
                            if (!$("#savecontactform").valid()) {
                                return false;
                            }


                            var base_path = $('#base_path').val();
                            var first_name = $('#first_name').val();
                            var last_name = $('#last_name').val();
                            var email = $('#email').val();
                            var phone = $('#phone').val();
                            var message = $('#message').val();
                            var propertyId = $('#propertyId').val();
                            var stay_informed = 0;
                            if ($('#stay_informed').is(":checked"))
                            {
                                stay_informed = 1;
                            }
                            $("#btnsaveContactForm").attr("disabled", "disabled");
                            $.ajax({
                                type: 'post',
                                url: base_path+'/web/savecontactform',
                                data: {first_name: first_name,last_name:last_name,email:email,phone:phone,message:message,stay_informed:stay_informed,propertyId:propertyId},
                                async: false,

                                success: function (data) {
                                    $('#first_name').val('');
                                    $('#last_name').val('');
                                    $('#email').val('');
                                    $('#phone').val('');
                                    $('#message').val(''); 
                                    $('#propertyId').val('');
                                    //$("#privacy_policy").prop("checked",false);
                                    $('#confirmation_message').show();
                                    $('#confirmation_message').text('Successfully Submitted');
                                    setTimeout(function() {
                                        $('#confirmation_message').fadeOut('fast');
                                    }, 3000);

                                    $("#btnsaveContactForm").attr("disabled", false);
                                }

                            });
                            e.preventDefault();
                        });
                    });
    </script>
</section>
