<!--Lists the property list after ajax search-->
<?php if (count($property_list) == 0) {?>
    <div class="noItems">
        <p>No Properties Found for your search</p>
    </div>
<?php }?>
<?php foreach ($property_list as $list) {?>
    <div class="listItem allListBoxHolder">

            <?php
$slug = substr(strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $list->name)), 0, 25);
    ?>


            <?php
$image = url('/').'/default_image/property_default.png';

    if ($list->images == "" || $list->images == null) {
        ?>
                <figure class="listLarge listImgHolder  bContain">
                    <a href="{{URL('/')}}/property_view/{{$slug}}/{{$list->reference_number}}"> <img src="{{ URL::asset($image)}}" alt=""></a>
                </figure>

                <?php
} else {
        $image = url('/') . '/uploads/property-gallery/' . $list->images;

        if (!@getimagesize($image)) {
            ?>
                    <figure class="listLarge listImgHolder  bContain">
                      <a href="{{URL('/')}}/property_view/{{$slug}}/{{$list->reference_number}}">  <img src="{{ URL::asset($image)}}" alt=""></a>
                    </figure>

                <?php } else {?>
                    <figure class="listLarge listImgHolder ">
                      <a href="{{URL('/')}}/property_view/{{$slug}}/{{$list->reference_number}}"><img src="{{ URL::asset($image)}}" alt=""></a>
                    </figure>

                <?php
}
    }
    ?>

         <div class="itemDetails">
            <h2>{{ $list->name}}</h2>
            <span>{{ $list->address1}}{{ $list->address2}} {{ $list->zip}}{{ $list->muncipality}} </span>
            <ul>
                <li>
                    <figure><img src="{{ URL::asset('web/images')}}/iconListBed.png" alt="">
                    </figure>
                    <p>{{__('Bedrooms')}}</p>
                    <em>{{ $list->no_of_baths}}</em>
                </li>
                <li>
                    <figure><img src="{{ URL::asset('web/images')}}/iconBathtub.png" alt="">
                    </figure>
                    <p>{{__('Bathrooms')}}</p>
                    <em>{{ $list->no_of_beds}}</em>
                </li>
                <li>
                    <figure><img src="{{ URL::asset('web/images')}}/icon360.png" alt="">
                    </figure>
                    <p>{{__('Surface')}}</p>
                    <em>{{ $list->total_area}} m²</em>
                </li>
                <li>
                    <figure><img src="{{ URL::asset('web/images')}}/icnTerrace.png" alt="">
                    </figure>
                    <p>{{__('Terrace')}}</p>
                    <em><?php if ($list->terrace == '1') {echo "Yes";} else {echo "No";}?></em>
                </li>
            </ul>

            <div class="btnHandle">
                <a class="btnCommon blue xcvcv"  href="{{URL('/')}}/property_view/{{$slug}}/{{$list->reference_number}}" >{{__('would_you_like')}} </a>

                <!--<a class="btnCommon green" href="{{URL('/')}}/plan_view/{{$slug}}/{{\Crypt::encrypt($list->id)}}">{{__('plan_view')}}</a>-->
                <a class="btnCommon green" onclick="interestedBtn({{$list->id}})"  href="#interestedForm">{{__('plan_view')}}</a>
            </div>
        </div>
        <div class="itemPrice">
            <?php if($list->new_property=='1'){?>
            <figure>
                <img src="{{ URL::asset('web/images')}}/iconNew.png" alt="">
            </figure>
            <?php } ?>

            <div class="customClear"></div>
            <div class="priceWrapper">
                <span>{{env('CURRENCY')}} {{ $list->estimated_price}}</span>
                <em> {{ $list->muncipality}}</em>
            </div>
            <a class="orange btnCommon" onclick="interestedBtn({{$list->id}})"  href="#interestedForm">{{__('interested')}}?</a>
        </div>

    </div>
<?php }?>

<script>
 function interestedBtn(id){
     $('#propertyId').val('');
     $('#propertyId').val(id);
  } 
</script>