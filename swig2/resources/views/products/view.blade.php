@extends('layouts.main')
@section('content')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<div class="contentHolderV1">
    <h1>View Product</h1>


    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="formTypeV1 gallHolder">
        <label>Name</label>
        <input type="text" name="productName" id="productName" value="{{$product_details->productName}}" readonly="true">
        <div class="customClear"></div>
        <label>Description</label>
        <textarea name="productDescription" id="productDescription" readonly="true"> {{$product_details->productDescription}}</textarea>

        <div class="customClear"></div>
        <label>Price</label>
        <input type="text" name="productPrice" id="productPrice" value="{{$product_details->productPrice}}" readonly="true">
        <div class="customClear"></div>
        <label>Category Name </label>
        <input type="text" name="categoryName" id="categoryName" value="{{$product_details->categoryName}}" readonly="true">
        <div class="customClear"></div>
        <label>Seller Name</label>
        <input type="text" name="sellerName" id="sellerName" value="{{$product_details->sellerFirstName}} {{$product_details->sellerLastName}}" readonly="true">
        <div class="customClear"></div>
        <label>Quantity</label>
        <input type="text" name="quantity" id="quantity" value="{{$product_details->quantity}}" readonly="true">
        <div class="customClear"></div>
        <input type="hidden" name="product_id" id="product_id" value="{{$product_details->productId}}">
        <label class="gallHeading">Images</label>
        <div class="ProductImages">
            <ul>
                @foreach($product_details->images as $image)
                <li style="background-image: url('<?php echo url('/'); ?>{{$image->image}}');"></li>
                @endforeach
            </ul>
        </div>
         <label class="dealschkbox"><input type="checkbox" name="featured_chk" id="featured_chk" <?php if($product_details->featuredStatus == 1) {?> checked <?php } ?>> Featured Product</label>
    </div>


</div>  


<script>
    $(document).ready(function () {
        $('input[name="featured_chk"]').change(function () {
            var base_path = $('#base_path').val();
            var product_id = $('#product_id').val();
            if (this.checked) {
                var is_featured = 1;
            } else
            {
                var is_featured = 0;
            }
            $.ajax({
                url: base_path + "/products/changefeatured",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"product_id": product_id, "is_featured": is_featured},
                success: function (result) {

                }
            });
        });
    });
</script>

@endsection
