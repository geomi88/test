@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    
    <a class="dashboardItems" href="{{ url('suppliers/add') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconAddSupplier.png')}}" alt="Suppliers" name="Suppliers"></figure>
                <figcaption>Add Suppliers</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('suppliers') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconSupplierList.png')}}" alt="Suppliers" name="Suppliers"></figure>
                <figcaption>List Suppliers</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('suppliers/editlist') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconEditSupplier.png')}}" alt="Suppliers" name="Suppliers"></figure>
                <figcaption>Edit Suppliers</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('customers/add') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconAddConsumer.png')}}" alt="Customers" name="Customers"></figure>
                <figcaption>Add Customer</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('customers') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconListConsumer.png')}}" alt="Customers" name="Customers"></figure>
                <figcaption>List Customers</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('customers/editlist') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconEditConsumer.png')}}" alt="Customers" name="Customers"></figure>
                <figcaption>Edit Customers</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('assets/add') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconAddAsset.png')}}" alt="Assets" name="Assets"></figure>
                <figcaption>Add Asset</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('assets') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconListAsset.png')}}" alt="Assets" name="Assets"></figure>
                <figcaption>List Assets</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('assets/editlist') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconEditAsset.png')}}" alt="Assets" name="Assets"></figure>
                <figcaption>Edit Assets</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('ledgers/general_ledgers') }}" >
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconLedger.png')}}" alt="General Ledgers" General Ledgers="Assets"></figure>
                <figcaption>General Ledgers</figcaption>
            </div>
        </div>
    </a>

<?php
    } else {
        foreach ($user_sub_modules as $user_sub_module) {
            ?>
            <a class="dashboardItems" href="{{ url($user_sub_module->url) }}">
                <div class="dashboardTable">
                    <div class="dashboardTd">
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="Warehouse"></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
    <?php }} ?>

</div>
@endsection
