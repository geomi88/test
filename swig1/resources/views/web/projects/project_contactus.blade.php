@extends('layout.web.projects.project_layout')
@section('title', 'Project Details') 
@section('content')
        <section class="layoutWrapper">
             <div class="mobileNav">
                <ul>
                        <li><a href="{{URL::to('project/construction-view',['url'=>$constructionDetails->url])}}">{{__('project')}}</a></li>
                        <li><a href="{{URL::to('project/construction-plan',['url'=>$constructionDetails->url])}}">{{__('price&plan')}}</a></li>
                        <li><a href="{{URL::to('project/construction-surrouding',['url'=>$constructionDetails->url])}}">{{__('surrounding')}}</a></li>
                        <li><a href="{{URL::to('project/construction-garden',['url'=>$constructionDetails->url])}}">{{__('garden')}}</a></li>
                        <li><a href="{{URL::to('project/construction-perfect-comfort',['url'=>$constructionDetails->url])}}">{{__('perfect_comfort')}}</a></li>
                        <li class="active"><a href="{{URL::to('project/construction-contact',['url'=>$constructionDetails->url])}}">{{__('interest')}}</a></li>
                         
                </ul>
            </div>
            <section id="container" class="Omgeving-page content">
                 <div class="responsiveIcon iconMobNav">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            <div class="row OmgevingWrapper">
                <div class="contentTop">
                     <div class="OmgevingLeft small-12 medium-5 large-5 column">
                        <div class="pageHead">
                                <div class="header-inner">
                                   <div class="pageLogo">
                                       <a href="<?php echo url('/'); ?>" >
                                        <figure>
                                            <img src="{{ URL::asset('web/images')}}/logoLuxEstateWhite.png" alt="Luxestate">
                                        </figure>
                                       </a>
                                    </div>
                                </div>
                            <input type="hidden" name="projectId" id="projectId" value="{{\Crypt::encrypt($constructionDetails->id)}}" >
                            <div class="customClear"></div>
                            <div class="head-bg"> 
                                <div class="header-inner">   
                                   <div class="title">     
                                    <div class="title-text">
                                     <span>Luxestate Brokers Presents </span> 
                                    <h2>{{$constructionDetails->name}}</h2>
                                    </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="customClear"></div>
                     <div class="social">
                       <ul class="socialIcons">
                            <li> Share with us</li>
                            <li><img src="{{ URL::asset('web/images')}}/shareIconFB.png" alt=""></li>
                            <li><img src="{{ URL::asset('web/images')}}/shareIconTwitter.png" alt=""></li>
                            <li><img src="{{ URL::asset('web/images')}}/shareIconIn.png" alt=""></li>
                        </ul>
                    </div>
                    </div>
                     <div class="OmgevingRight small-12 medium-7 large-7 column">
                          <div class="resp-head-bg"> 
                            <div class="header-inner">   
                                <div class="title">     
                                <div class="title-text">
                                  <span>Luxestate Brokers Presence </span> 
                                  <h2>{{$constructionDetails->name}}</h2>
                                </div>
                                </div>
                            </div>
                          </div>
                          <div class="sliderNav">
                             <div class="pageLogo">
                                 <a href="<?php echo url('/'); ?>" >
                                        <figure>
                                            <img src="{{ URL::asset('web/images')}}/logoLuxEstateWhite.png" alt="Luxestate">
                                        </figure>
                                 </a>
                             </div>
                          </div>
                        <section class="home slider">
                            @foreach($sliderImages as $slider)
                                    <?php
                                    $image =url('/'). '/default_image/property_default.png';

                                    if ($slider->image == "" || $slider->image == NULL) {
                                        ?>

                                        <?php
                                    } else {
                                        $image =url('/'). '/uploads/project-slider/' . $slider->image;

                                        if (!@getimagesize($image)) {
                                            $image =url('/'). '/default_image/property_default.png';
                                        } else {
                                           $image = url('/'). '/uploads/project-slider/' . $slider->image; 
                                        }
                                    }
                                ?>
                                    <div>
                                        <img src="{{$image}}">
                                    </div>
                                    @endforeach           
                            
                         </section>
                       <div class="new-menu">
                        <div class="top-bar" id="desktop-menu" role="navigation" >
                            <div>
                                <ul>
                                    <li><a href="{{URL::to('project/construction-view',['url'=>$constructionDetails->url])}}">{{__('project')}}</a></li>
                                    <li><a href="{{URL::to('project/construction-plan',['url'=>$constructionDetails->url])}}">{{__('price&plan')}}</a></li>
                                    <li><a href="{{URL::to('project/construction-surrouding',['url'=>$constructionDetails->url])}}">{{__('surrounding')}}</a></li>
                                    <li><a href="{{URL::to('project/construction-garden',['url'=>$constructionDetails->url])}}">{{__('garden')}}</a></li>
                                    <li><a href="{{URL::to('project/construction-perfect-comfort',['url'=>$constructionDetails->url])}}">{{__('perfect-comfort')}}</a></li>
                                    <li class="active"><a href="{{URL::to('project/construction-contact',['url'=>$constructionDetails->url])}}">{{__('interest')}}</a></li>
                
                                </ul>
                            </div>
                           
                        </div>
                    </div>    
                     </div>
                     <div class="customClear"></div>
                </div>
                <div class="contentBottom">
                <div class="OmgevingLeft small-12 medium-5 large-5 column">
                    <div class="respSocial">
                         <ul class="socialIcons">
                            <li> Share with us</li>
                            <li><img src="{{ URL::asset('web/images')}}/shareIconFB.png" alt=""></li>
                            <li><img src="{{ URL::asset('web/images')}}/shareIconTwitter.png" alt=""></li>
                            <li><img src="{{ URL::asset('web/images')}}/shareIconIn.png" alt=""></li>
                        </ul>
                    </div>
                    <div class="OmgevingLeftInner">
                        <div class="infoContent">
                          <div class="dividerLine">
                                    <h3>{{__('information_obligation')}}</h3>
                            <ul>
                                <li>{{__('planning_permits')}} </li>
                                <li><?php if($constructionDetails->planning_permit=='1'){ ?> {{__('yes')}} <?php }else{?> {{__('no')}} <?php  } ?></li>
                                <li>{{__('destination')}} </li>
                                <li><?php if($constructionDetails->destination=="") { echo "-"; }else{ echo $constructionDetails->destination;} ?></li>
                                <li>{{__('subpoenas')}}  </li>
                                <li><?php if($constructionDetails->subpoenas=='1'){ ?> {{__('yes')}} <?php }else{?> {{__('no')}} <?php  } ?></li>
                                <li>{{__('judicial_statements')}}</li>
                                <li><?php if($constructionDetails->judicial_sayings=='1'){ ?> {{__('yes')}} <?php }else{?> {{__('no')}} <?php  } ?></li>
                                <li>{{__('pre_emption_right')}}</li>
                                <li><?php if($constructionDetails->pre_emption_right=='1'){ ?> {{__('yes')}} <?php }else{?> {{__('no')}} <?php  } ?></li>
                                <li>{{__('sub_division_permit')}} </li>
                                <li><?php if($constructionDetails->subdivision_permit=='1'){ ?> {{__('yes')}} <?php }else{?> {{__('no')}} <?php  } ?></li>
                                <li>{{__('flood_area')}} </li>
                               </ul>
                            <p><?php if($constructionDetails->flood_area=='0'){?>
                                    {{__('not_located_in_a_flood_prone_area')}}
                                    <?php }else{ ?> 
                                    {{__('located_in_a_flood_prone_area')}}  
                                    <?php } ?></p>
                          </div>
<!--                            <div class="cotactUs-form plan">
                                <form class="form">
                                      <h2>CONTACT US</h2>
                                      <input type="Name:" placeholder="First Name">
                                      <input type="Name:" placeholder="Last Name">
                                      <input type="Email:" placeholder="Email Address">
                                      <input type="Number:" placeholder="Phone Number">
                                      <input type="text:" placeholder="Write your comment">
                                      <button class="btnSubmit"> Submit </button>
                                </form>
                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="OmgevingRight small-12 medium-7 large-7 column">
                     <div class="contentRight">     
                    </div> 
                    <div class="OmgevingRightInner">
                          <div class="contenFirstRow">
                            <div class="row pageContent pageText">
                                <div class="small-12 medium-12 large-12 column">
                                  <h1 class="headText">{{__('interested')}}</h1>
                                  <h2 class="subHeading">Makelaarskantoor Luxestate</h2>
                                </div>
                              <div class="row">
                                <div class="small-12 medium-6 large-6 column">
                                  <p >
                                    Jordaenskaai 21, 2000 Antwerp <br>
                                    T : 5653265656<br>
                                    E : info@luxestate .be
                                    <br>
                                    <br>
                                    BIV-certified real estate broker-broker
                                    <br>
                                    <br>
                                    BIV 200.803 - Belgium<br>
                                    VAT BE 0453.256.254<br>
                                 </p>
                                </div>
                                <div class="small-12 medium-6 large-6 column detailsmgTop">
                                  <div class="rightDetails">
                                      <span>Toezichthoudende autoriteit:</span>
                                      <p>
                                       Beroepsinstituut van Vastgoedmakelaars,
                                       Luxemburgstraat 16B te 1000 Brussel
                                       Onderworpen aan de deontologische code BIV
                                     </p>
                                     <div class="textMgTop">
                                       <span>Lid van de Confederatie voor Immobiliënberoepen</span>
                                        <p>
                                         De van toepassing zijnde corporatieve beroepsregels 
                                         CIB vindt U onder CIB.be
                                       </p>
                                     </div>
                                  </div>  
                                </div>
                            </div>
                            </div>
                          </div> 
                          <div class="customClear"></div>
                          <div class="contactForm">
                           <form method="post" action="" id="savecontactform" enctype="multipart/form-data" class="formTypeV1">
                                <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
                                {{ csrf_field() }}
                            <div class="small-12 medium-12 large-12 column serviceLeft">
                                    
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
                                    <div class="small-12 medium-12 large-12 column inputHolder textareaHolder">
                                        <textarea row="5" cols="45" placeholder="Type Here" name="message" id="message"></textarea>
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
                                            <input type="checkbox" >
                                            <em></em>
                                        </label>
                                        <span>
                                            I have read and agree to the disclaimer and privacy policy.<br> I hereby consent to the
                                            processing of my data and to contact me.</span>
                                    </div> 
                                    <div class="small-12 medium-12 large-12 column inputHolder">
                                       <input type="submit" value="Submit" id="btnsaveContactForm">
                                    </div>
                                    <div class="submitMessage" id="confirmation_message"></div>
                                </div>
                                <div class="customClear"></div>
                               </form>
                          </div>

                          
                
                    </div>
                </div>
                <div class="customClear"></div>
                </div>
                <div class="share respSocial">
                    <ul class="socialIcons">
                        <li> Share with us</li>
                        <li><img src="{{ URL::asset('web/images')}}/shareIconFB.png" alt=""></li>
                        <li><img src="{{ URL::asset('web/images')}}/shareIconTwitter.png" alt=""></li>
                        <li><img src="{{ URL::asset('web/images')}}/shareIconIn.png" alt=""></li>
                    </ul>
                </div>
            </section>
        </section> 
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
                            var stay_informed = 0;
                            if ($('#stay_informed').is(":checked"))
                            {
                                stay_informed = 1;
                            }
                            $("#btnsaveContactForm").attr("disabled", "disabled");
                            $.ajax({
                                type: 'post',
                                url: base_path+'/construction-contact/savecontactform',
                                data: {first_name: first_name,last_name:last_name,email:email,phone:phone,message:message,stay_informed:stay_informed},
                                async: false,

                                success: function (data) {
                                    $('#first_name').val('');
                                    $('#last_name').val('');
                                    $('#email').val('');
                                    $('#phone').val('');
                                    $('#message').val('');
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
@endsection
