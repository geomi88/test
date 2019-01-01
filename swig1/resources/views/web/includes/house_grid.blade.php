<div class="row small-up-1 medium-up-3 large-up-3 propertyGridHolder  allListingWrapper eqHeightHolder">
    <?php
// print_r($properties);
if (count($properties) == 0) {?>
        <div class="column"><h2>{{ __('no_properties') }}</h2> </div>

        <?php
} else {
    foreach ($properties as $eachProperty) {
        $slug = substr(strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $eachProperty->name)), 0, 25);
        ?>
            <div class="column allListBoxHolder">
                <div class="listHandle">
                   <a href="{{URL('/')}}/property_view/{{$slug}}/{{$eachProperty->reference_number}}">
                     <?php
$image = 'default_image/property_default.png';

        if ($eachProperty->images == "" || $eachProperty->images == null) {
            ?>
                 <figure class="listImgHolder"><img src="{{ URL::asset($image)}}" alt=""></figure>
                <?php
} else {
            $image = url('/') . '/uploads/property-gallery/' . $eachProperty->images;

            if (!@getimagesize($image)) {
                ?>
                   <figure class="listImgHolder"><img src="{{ URL::asset($image)}}" alt=""></figure>
                <?php } else {?>
                    <figure class="listImgHolder"><img src="{{ URL::asset($image)}}" alt=""></figure>
                <?php
}
        }
        ?>

                    </a>
                    <div class="listDetails eqHeightInner">
                        <span class="propertyName">{{ $eachProperty->name}}</span>
                        <p>{{ $eachProperty->address1}}{{ $eachProperty->address2}} {{ $eachProperty->zip}}{{ $eachProperty->municipality}}</p>
                        <span class="Price">{{env('CURRENCY')}} {{ $eachProperty->estimated_price}}</span>
                    </div>
                    <ul class="roomDetails">
                        <li><figure><img src="{{ URL::asset('web/images')}}/iconBed.png" alt=""><figcaption>  {{ $eachProperty->no_of_beds}} Beds</figcaption></figure></li>
                        <li><figure><img src="{{ URL::asset('web/images')}}/iconBath.png" alt=""><figcaption>{{ $eachProperty->no_of_baths}} Baths</figcaption></figure></li>
                        <li><figure><img src="{{ URL::asset('web/images')}}/iconSqFeet.png" alt=""><figcaption>{{ $eachProperty->total_area}} m²</figcaption></figure></li>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>

        <?php }
    ?>


    <?php }?>
</div>
<script>
     //all listing change background image
                $(".allListingWrapper .allListBoxHolder").each(function() {
                    var imgPath = $(this).find(".listImgHolder img").attr('src');
                    $(this).find(".listImgHolder").css('background-image', 'url("'+ imgPath +'")');
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

                                equalHeight();
 </script>