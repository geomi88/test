@extends('layout.web.website')
@section('content')
@section('title', 'Architect Details')
@section('architects', 'active')
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
                                <a href="javascript:void();">{{__('architects')}}<span>/</span></a>
                            </li>
                            <li>
                                <a href="javascript:void();">{{$architect_detail->name}}</a>
                            </li>
                        </ul>
                        
                        <div class="row headingSection">
                            <div class="small-12 medium-12 large-12 column mgBottom head ">
                                <h2>{{$architect_detail->name}}</h2> 
                            </div>
                            <div class="row slide">
                                <div class="small-12 medium-5 large-5 column">
                                      <section class="variable slider architect">
                                          <?php
                                          $image = 'default_image/property_default.png';
                                          if (count($gallery) > 0) {
                                              ?>
                                              @foreach($gallery as $images)
                                              <?php
                                              if ($images->image == "" || $images->image == NULL) {
                                                  ?>

                                                  <?php
                                              } else {
                                                  $image = url('/').'/uploads/architect-gallery/' . $images->image;

                                                  if (!@getimagesize($image)) {
                                                      $image = 'default_image/property_default.png';
                                                  } else {
                                                       $image = '/uploads/architect-gallery/' . $images->image;

                                                  }
                                              }
                                              ?>
                                              <div>
                                                  <img alt='first' src="{{URL::asset($image)}}">
                                              </div>
                                              @endforeach
                                          <?php } else { ?>
                                              <div>
                                                  <img alt='test' src="{{URL::asset($image)}}">
                                              </div>
                                          <?php } ?>
                                      </section>
                                </div>
                                 <div class="small-12 medium-7 large-7 column slideText">
                                     <div id='shortTop'>
                                    <p> 
                                        <?php
                                        $descTop = htmlspecialchars_decode($architect_detail->description);
                                        $descriptionTop = substr($descTop, 0, 1236);
                                        echo $descriptionTop;
                                        ?>
                                   
                                    
                                    </p>
                                     </div>
                                    <div id='fullTop'>
                                        
                                        <?php echo  htmlspecialchars_decode($architect_detail->description);?>
                                        
                                    </div>
                                <div class="readmore ">
                                <button class="read-more btnTop">READ MORE + </button>
                                </div>
                                 </div>
                            </div>



                          </div>


                        <div class="row headingSection">
                            <div class="row interviewText">
                                <div class="small-12 medium-12 large-12 column">
                                    <div id='shortBtm'> 
                               
                                  <p>
                                         <?php
                                        $desc = htmlspecialchars_decode($architect_detail->additional_description);
                                        $description = substr($desc, 0, 690);
                                        echo $description;
                                        ?>


                                     </p>
                                     </div> 
                                    <div id='fullBtm'>
                                        <?php echo  htmlspecialchars_decode($architect_detail->additional_description);?>
                                    </div>
                                  </div>
                                </div>
                                <div class="readmore ">
                                <button class="read-more btnBtm">READ MORE + </button>
                                 </div>
                            </div>
                        </div>
                        
                  
                </section>
                <div class="similarProperties">
                    <div class="row small-up-1 medium-up-3 large-up-3">
                            <h2><span>Running</span>PROJECTS</h2>
                            
                            <?php if(count($architectProjects)>0){ ?>
                             @foreach($architectProjects as $projects)
                             <?php
                                    $image =url('/') . '/default_image/property_default.png';

                                    if ($projects->images == "" || $projects->images == NULL) {
                                        ?>

                                        <?php
                                    } else {
                                        $image = url('/') . '/uploads/project-gallery/' . $projects->images;

                                        if (!@getimagesize($image)) {
                                            $image = url('/') .'/default_image/property_default.png';
                                        }else{
                                            $image =url('/') .'/uploads/project-gallery/' . $projects->images;

                                        }
                                    }
                            ?>
                            <div class="column constructionList">
                                <figure>
                                     <a href="{{URL::to('project/construction-view',['url'=>$projects->url])}}">
                                    <img src="{{$image}}" alt="">
                                <div class="mask">
                                    <div class="textContent center">
                                     <span>{{$projects->name}}</span>
                                    </div>
                                </div>
                                     </a>
                                </figure>
                            </div>
                            @endforeach
                            <?php } else{?>
                                  {{__('no_projects_under_this_architect')}}
                          <?php  } ?>
                   
                    </div>
                </div>
                <section class="bottomSection">
                 @include('web.includes.our_services')  
                </section>
  
    

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
    $('#fullTop').hide();
    $('#fullBtm').hide();
    $('.btnTop').click(function(){
      $('#shortTop').hide(); 
      $('#fullTop').show(); 
      $('.btnTop').remove();  
      });
    $('.btnBtm').click(function(){
      $('#shortBtm').hide(); 
      $('#fullBtm').show(); 
      $('.btnBtm').remove();
    });
</script>
  @endsection