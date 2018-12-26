@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    
    
     <a class="dashboardItems" href="{{ url('requisitions/rfq/add') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconInsurance.png')}}" alt="RFQ" name="rfq"></figure>
                <figcaption>Create RFQ</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('requisitions/editrfq') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconInsurance.png')}}" alt="Edit RFQ" name="rfq"></figure>
                <figcaption>Edit RFQ</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('requisitions/rfq') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconInsurance.png')}}" alt="RFQ List" name="rfq"></figure>
                <figcaption>RFQ List</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('requisitions/approvedlist') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconInsurance.png')}}" alt="Approved RFQ" name="rfq"></figure>
                <figcaption>Approved RFQ List</figcaption>
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
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="rfq"></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
<?php }} ?>
</div>
@endsection
