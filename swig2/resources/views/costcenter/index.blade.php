@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    <a class="dashboardItems" href="{{ url('costcenter/cost_allocation/add') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/addSales.jpg" alt="Branch Cost Allocation"></figure>
                <figcaption>Branch Cost Allocation</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('costcenter/cost_analysis') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/addSalesList.jpg" alt="Branch Cost Analysis"></figure>
                <figcaption>Branch Cost Analysis</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('costcenter/costcenter_report') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/costCentreReport.png" alt="Cost Center Report"></figure>
                <figcaption>Cost Center Report</figcaption>
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
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="{{$user_sub_module->name}}"></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
    <?php }} ?>
</div>
@endsection
