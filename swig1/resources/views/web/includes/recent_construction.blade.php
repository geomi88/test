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
