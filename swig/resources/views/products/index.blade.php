@extends('layouts.main')
@section('content')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>


<div class="contentHolderV1">
    <h2>Products Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">

    <div class="searchWrapper">
        <form class="searchArea" id="productSearch" action="{{ action('Products\ProductsController@search') }}" method="post">
            {{ csrf_field() }}
            <input type="text" id="search_key" placeholder="Search by Name,Price or Seller Name" name="search_key" >
            <input type="submit" value="Search" class="commonButton search">
            <div class="customClear"></div>
        </form>
        <div class="customClear"></div>
        <form class="searchArea" id="deals" action="{{ action('Products\ProductsController@deals') }}" method="post">
            {{ csrf_field() }}
            <label class="dealschkbox"><input type="checkbox" name="deal_chk" id="deal_chk"> Show Deals</label>
        </form>
    </div>
    <div class="tableHolder">
    <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
        <thead class="tableHeader">
            <tr>
                <td>#</td>
                <td>Product Name</td>
                <td>Price</td>
                <td>Offer Price</td>
                <td>Seller Name</td>
                <td>Date</td>
                <td>Product Status</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            <?php $n = $products->perPage() * ($products->currentPage() - 1); ?>
                <?php
                foreach ($products as $product) {
                $n++;
                ?>
                <tr>
                    <td>{{$n}}</td>
                        <td>{{$product->name}}</td>
                        <td><?php if ($product->price > 1) { ?>{{$product->price}} QAR<?php } ?></td>
                        <td><?php if ($product->offerPrice > 1) { ?>{{$product->offerPrice}} QAR<?php } ?></td>
                        <td>{{$product->sellerFirstName}} {{$product->sellerLastName}}</td>
                        <td>{{$product->created_at}}</td>
                        <td><select id = "{{$product->id}}" class="productVerified tableSelect"><option value="1" @if($product->isVerified ==1) selected = true @endif >Approved</option>
                                <option value="0" @if($product->isVerified ==0) selected = true @endif >Pending Approval</option>
                                <option value="2" @if($product->isVerified ==2) selected = true @endif >Verified</option>
                                <option value="3" @if($product->isVerified ==3) selected = true @endif >Rejected</option>
                            </select>
                            <form class="resonToolTip" action="{{ action('Products\ProductsController@addreason') }}" method="post">
                                {{ csrf_field() }}
                                <h4>Reason</h4>
                                <a class="tooltipClose" href="javascript:void(0)">X</a>
                                <textarea name="rejectReason" required></textarea>
                                <div class="urlError error"></div>
                                <input type="hidden" name="product_id" value="{{$product->id}}">
                                <input type="submit" value="submit">
                            </form>
                        </td>

                    <td class="actionHolder">
                        <a class="actionBtn view" href="{{ URL::to('products/view', ['id' => ($product->id)]) }}" title="Edit"></a>
                    </td>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    </div>
    <ul class="pagination">
        {!! $products->render() !!}
    </ul>
</div>
<script>
    $(document).ready(function () {
        $('.productVerified').change(function () {
            var verified_status = $(this).val();
            var prod_id = $(this).attr("id");
            var base_path = $('#base_path').val();

            $.ajax({
                url: base_path + "/products/verify",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"product_id": prod_id, "is_verified": verified_status},
                success: function (result) {

                }
            });
        });

        $('input[name="deal_chk"]').change(function () {
            var base_path = $('#base_path').val();
            if (this.checked) {
                $('#deal_key').val(1);
                $("#deals").submit();
            } else
            {
                window.location = base_path + "/products";
            }
        });
    });
</script>

@endsection
