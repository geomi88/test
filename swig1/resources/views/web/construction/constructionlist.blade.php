@extends('layout.web.website')
@section('content')
@section('title', 'Construction List')
@section('construction', 'active')
@section('pageClass', 'detailPage')
@section('innerClass', 'withoutBanner')
@include('web.includes.banner_inner')

                <section class="contentWrapper neigh">
                    <div class="pageCenter">
                        <ul class="breadcrumbsHolder">
                            <li>
                                <a href="{{url('/')}}">home <span>/</span></a>
                            </li>
                           
                            <li>
                                <a href="javascript:void();">{{__('new_construction')}}</a>
                            </li>
                        </ul>
                        
                        <div class="row headingSection">
                            <div class="small-12 medium-12 large-12 column">
                                <h2>{{__('new_construction')}} in Georgia</h2>
                                <p class="text">Click on the neighborhood for more information or view the properties in that neighborhood</p>
                            </div>
                        </div>
                        <div class="row small-up-1 medium-up-3 large-up-3">
                            @foreach($construction_list as $constructions)
                             <?php
                                    $image =url('/') . '/default_image/property_default.png';

                                    if ($constructions->images == "" || $constructions->images == NULL) {
                                        ?>

                                        <?php
                                    } else {
                                        $image = url('/') . '/uploads/project-gallery/' . $constructions->images;

                                        if (!@getimagesize($image)) {
                                            $image = url('/') .'/default_image/property_default.png';
                                        }else{
                                            $image =url('/') .'/uploads/project-gallery/' . $constructions->images;

                                        }
                                    }
                            ?>
                            <div class="column constructionList">
                                <figure>
                                     <a href="{{URL::to('project/construction-view',['url'=>$constructions->url])}}">
                                    <img src="{{$image}}" alt="">
                                <div class="mask">
                                    <div class="textContent center">
                                     <span>{{$constructions->name}}</span>
                                    </div>
                                </div>
                                     </a>
                                </figure>
                            </div>
                            @endforeach
                            
                            
                        </div>
                        
                    </div>
                </section>
                <div class="similarProperties">
                    <div class="row small-up-1 medium-up-3 large-up-3 allListingWrapper eqHeightHolder">
                            <h2><span>Latest</span>PROPERTIES</h2>
                            @foreach($similar as $similarPlot)
                    <div class="column allListBoxHolder">
                        <?php
                        $slug = substr(strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $similarPlot->name)), 0, 25);
                        ?>
                        <div class="listHandle">
                            <a href="{{URL('/')}}/property_view/{{$slug}}/{{$similarPlot->reference_number}}">
                                <figure class="listImgHolder">
                                    <?php if ($similarPlot->mainimage == NULL) { ?>
                                        <img src="{{ URL::asset('default_image')}}/property_default.png" alt="Villa with pool for Rent">
                                    <?php } else { ?>
                                        <img src="{{ URL::asset('uploads/property-gallery')}}/{{$similarPlot->mainimage}}" alt="Villa with pool for Rent">
                                    <?php } ?>
                                </figure>
                            </a>
                            <div class="listDetails eqHeightInner">
                                <span class="propertyName">{{$similarPlot->name}}</span>
                                <p>{{$similarPlot->address1}}{{$similarPlot->address2}}{{$similarPlot->zip}}{{$similarPlot->municipality}}</p>
                                <span class="Price">{{env('CURRENCY')}}{{$similarPlot->estimated_price}}</span>
                            </div>
                            <ul class="roomDetails">
                                <li><figure><img src="{{ URL::asset('web/images')}}/iconBed.png" alt=""><figcaption>{{$similarPlot->no_of_beds}} Beds</figcaption></figure></li>
                                <li><figure><img src="{{ URL::asset('web/images')}}/iconBath.png" alt=""><figcaption>{{$similarPlot->no_of_baths}} Baths</figcaption></figure></li>
                                <li><figure><img src="{{ URL::asset('web/images')}}/iconSqFeet.png" alt=""><figcaption>{{$similarPlot->total_area}} </figcaption></figure></li>
                            </ul>
                        </div>
                    </div>
        @endforeach
                    </div>
                </div>
 @include('web.includes.our_services')
           
        <div class="galleryOverlay">
                <a class="btnClose" href="javascript:void()"></a>
            <div class="galleryWrapper"></div>
        </div>

        <div class="galleryContainer">
                <div id="slideshow" class="fullscreen">
                        <a class="btnGalClose" href="javascript:void(0);"></a>
                    <div id="img-1" data-img-id="1" class="img-wrapper active" style="background-image: url('images/img1.jpg')"></div>
                    <div id="img-2" data-img-id="2" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>
                    <div id="img-3" data-img-id="3" class="img-wrapper" style="background-image: url('images/img3.jpg')"></div>
                    <div id="img-4" data-img-id="4" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>
                    <div id="img-1" data-img-id="5" class="img-wrapper" style="background-image: url('images/img1.jpg')"></div>
                    <div id="img-2" data-img-id="6" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>
                    <div id="img-3" data-img-id="7" class="img-wrapper" style="background-image: url('images/img3.jpg')"></div>
                    <div id="img-4" data-img-id="8" class="img-wrapper" style="background-image: url('images/img2.jpg')"></div>
                    <div class="thumbs-container bottom">
                        <div id="prev-btn" class="prev">
                            <i class="fa fa-chevron-left fa-3x"></i>
                        </div>
    
                        <ul class="thumbs">
                            <li data-thumb-id="1" class="thumb active" style="background-image: url('images/img1-thumb.jpg')"></li>
                            <li data-thumb-id="2" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>
                            <li data-thumb-id="3" class="thumb" style="background-image: url('images/img3-thumb.jpg')"></li>
                            <li data-thumb-id="4" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>
                            <li data-thumb-id="5" class="thumb" style="background-image: url('images/img1-thumb.jpg')"></li>
                            <li data-thumb-id="6" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>
                            <li data-thumb-id="7" class="thumb" style="background-image: url('images/img3-thumb.jpg')"></li>
                            <li data-thumb-id="8" class="thumb" style="background-image: url('images/img2-thumb.jpg')"></li>
                        </ul>
    
                        <div id="next-btn" class="next">
                            <i class="fa fa-chevron-right fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
 <script>
     $(document).ready(function(){
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
                                
                              $(window).load(function(){
                                    equalHeight();
                                });  

}); 

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
         
    </script>
 @endsection
   