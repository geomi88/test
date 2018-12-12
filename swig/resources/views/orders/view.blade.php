@extends('layouts.main')
@section('content')
<div class="contentHolderV1">
    <h1>View Order</h1>

    <div class="formTypeV1 gallHolder">
        <label>Name</label>
        <input type="text" name="productName" id="productName" value="{{$order_details->productName}}" readonly="true">
        <div class="customClear"></div>
        <label>Order Number</label>
        <input type="text" name="orderNumber" id="orderNumber" value="{{$order_details->orderNumber}}" readonly="true">
        <div class="customClear"></div>
        <label>Seller Name</label>
        <textarea name="sellerName" id="sellerName" readonly="true"> {{$order_details->sellerFirstName}} {{$order_details->sellerLastName}}</textarea>
        <div class="customClear"></div>
        <label>Buyer Name</label>
        <textarea name="buyerName" id="buyerName" readonly="true"> {{$order_details->buyerFirstName}} {{$order_details->buyerLastName}}</textarea>
        <div class="customClear"></div>
        <label>Price</label>
        <input type="text" name="productPrice" id="productPrice" value="{{$order_details->sellingPrice}}" readonly="true">
        <div class="customClear"></div>
        <label>Payment Type</label>
        <?php
        if ($order_details->paymentType == 1) {
            $paymentType = "Credit Card";
        } else {
            $paymentType = "Cash on Delivery";
        }
        ?>
        <input type="text" name="paymentType" id="paymentType" value="{{$paymentType}}" readonly="true">

        <div class="customClear"></div>

    </div>
    
    <div class="formTypeV1">
        <h2>Shipping Address</h2>
        <label>Full Name</label>
        <input type="text" value="{{$order_details->fullName}}" readonly>
        <div class="customClear"></div>
        <label>Building Number</label>
        <input type="text" value="{{$order_details->buildingNumber}}" readonly>
        <div class="customClear"></div>
        <label>Street Number</label>
        <input type="text" value="{{$order_details->streetNumber}}" readonly>
        <div class="customClear"></div>
        <label>Zone Number</label>
        <input type="text" value="{{$order_details->zoneNumber}}" readonly>
        <div class="customClear"></div>
        <label>Phone Number</label>
        <input type="text" value="{{$order_details->phoneNumber}}" readonly>
        <div class="customClear"></div>

    </div>
</div>



@endsection
