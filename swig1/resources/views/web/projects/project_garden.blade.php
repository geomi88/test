@extends('layout.web.projects.project_layout')
@section('title', 'Project Details') 
@section('content')
<style>
    .map{
        height:252px;
        border:0px solid #000;
        max-width:602px;
        max-height: 255px;
        width:100%;
        margin-bottom: 20px;
    }

</style>
        <section class="layoutWrapper">
             <div class="mobileNav">
               <ul>
                    <li><a href="{{URL::to('project/construction-view',['url'=>$constructionDetails->url])}}">{{__('project')}}</a></li>
                    <li><a href="{{URL::to('project/construction-plan',['url'=>$constructionDetails->url])}}">{{__('price&plan')}}</a></li>
                    <li><a href="{{URL::to('project/construction-surrouding',['url'=>$constructionDetails->url])}}">{{__('surrounding')}}</a></li>
                    <li class="active"><a href="{{URL::to('project/construction-garden',['url'=>$constructionDetails->url])}}">{{__('garden')}}</a></li>
                    <li><a href="{{URL::to('project/construction-perfect-comfort',['url'=>$constructionDetails->url])}}">{{__('perfect-comfort')}}</a></li>
                    <li><a href="{{URL::to('project/construction-contact',['url'=>$constructionDetails->url])}}">{{__('interest')}}</a></li>
                    
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
                                    <li  class="active"><a href="{{URL::to('project/construction-garden',['url'=>$constructionDetails->url])}}">{{__('garden')}}</a></li>
                                    <li><a href="{{URL::to('project/construction-perfect-comfort',['url'=>$constructionDetails->url])}}">{{__('perfect_comfort')}}</a></li>
                                    <li><a href="{{URL::to('project/construction-contact',['url'=>$constructionDetails->url])}}">{{__('interest')}}</a></li>
                
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
                        <div class="text">
                           
                               <?php echo htmlspecialchars_decode($constructionDetails->description);?>
                            
                        </div>
                       
                        <div class="fotogal-slider">
                             <h2>{{__('photo_gallery')}}</h2>
                              <section class="gallery slider galleryImageSlider">
                                    @foreach($galleryImages as $gallery)
                                    <?php
                                    $image =url('/'). '/default_image/property_default.png';

                                    if ($gallery->image == "" || $gallery->image == NULL) {
                                        ?>

                                        <?php
                                    } else {
                                        $image =url('/'). '/uploads/project-garden/' . $gallery->image;

                                        if (!@getimagesize($image)) {
                                            $image =url('/'). '/default_image/property_default.png';
                                        } else {
                                           $image =url('/').  '/uploads/project-garden/' . $gallery->image; 
                                        }
                                    }
                                ?>
                                    <div>
                                        <img src="{{$image}}">
                                    </div>
                                    @endforeach
                              </section>

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
            </div>
            </section>
 </section>
 <!-----image gallery slider--->
 <div class="galleryOverlay">
                <a class="btnClose" href="javascript:void()"></a>
            <div class="galleryWrapper"></div>
        </div>
        <div class="galleryContainer">
                <div id="slideshow" class="fullscreen">
                        <a class="btnGalClose" href="javascript:void(0);"></a>
                        @foreach($galleryImages as $galImages)
                        <div id="img-{{$galImages->id}}" data-img-id="{{$galImages->id}}" class="img-wrapper"  style="background-image: url({{ URL::asset('uploads/project-garden')}}/{{$galImages->image}});"></div>
                        @endforeach

                    <div class="thumbs-container bottom">
                        <div id="prev-btn" class="prev">
                            <i class="fa fa-chevron-left fa-3x"></i>
                        </div>
    
                        <ul class="thumbs">
                            @foreach($galleryImages as $galImages)
                            <li data-thumb-id="{{$galImages->id}}" class="thumb" style="background-image: url({{ URL::asset('uploads/project-garden')}}/{{$galImages->image}});"></li>
                            @endforeach

                        </ul>
    
                        <div id="next-btn" class="next">
                            <i class="fa fa-chevron-right fa-3x"></i>
                        </div>
                    </div>
                </div>
        </div>
@endsection



   

