@extends('layouts.main')
@section('content')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<div class="contentHolderV1">
    <h2>Orders List</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="searchWrapper">
            <form class="searchArea" id="productSearch" action="{{ action('Orders\OrdersController@search') }}" method="post">
                {{ csrf_field() }}
                <input type="text" id="search_key" placeholder="Search by Order Number,Buyer Name or Seller Name" name="search_key" value="{{ $query }}">
                <input type="submit" value="Search" class="commonButton search">
                <div class="customClear"></div>
            </form>
            <a  href="{{ URL::to('orders') }}" title="SetUp" class="btnReset">RESET</a>

            <div class="customClear"></div>

        </div>
    <div class="tableHolder">
    <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
        <thead class="tableHeader">
            <tr>
                <td>#</td>
                <td>Order Number</td>
                <td>Product Name</td>
                <td>Seller Name</td>
                <td>Buyer Name</td>
                <td>Date</td>
                <td>Order Status</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            @if(isset($details))
            <?php $n = $details->perPage() * ($details->currentPage() - 1); ?>
            <?php
            foreach ($details as $order) {
                $n++;
                ?>
                <tr>
                    <td>{{$n}}</td>
                    <td>{{$order->orderNumber}}</td>
                    <td>{{$order->productName}}</td>
                    <td>{{$order->sellerFirstName}} {{$order->sellerLastName}}</td>
                    <td>{{$order->buyerFirstName}} {{$order->buyerLastName}}</td>
                    <td>{{$order->created_at}}</td>

                    <td><select id = "{{$order->id}}" class="orderShipped tableSelect"><option value="0" @if($order->shippingStatus ==0) selected = true @endif >Payment Done</option>
                            <option value="1" @if($order->shippingStatus ==1) selected = true @endif >Shipped</option>
                            <option value="2" @if($order->shippingStatus ==2) selected = true @endif >Delivered</option>
                        </select>
                    </td>

                    <td class="actionHolder">
                        <a class="actionBtn view" href="{{ URL::to('orders/view', ['id' => ($order->id)]) }}" title="Edit"></a>
                    </td>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    </div>
    <ul class="pagination">
        @if($details){!! $details->render() !!}@endif
        @elseif(isset($message))
        <p>{{ $message }}</p>
        @endif

    </ul>
</div>
<script>
    $(document).ready(function () {
        $('.orderShipped').change(function () {
            var shipping_status = $(this).val();
            var order_id = $(this).attr("id");
            var base_path = $('#base_path').val();

            $.ajax({
                url: base_path + "/orders/changeshippingstatus",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"order_id": order_id, "shipping_status": shipping_status},
                success: function (result) {

                }
            });
        });


    });
</script>

@endsection
