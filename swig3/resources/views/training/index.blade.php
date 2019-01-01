@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    <a class="dashboardItems" href="{{ url('training/createtraining') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconCreateMeeting.png" alt="Create Training"></figure>
                <figcaption>Create Training</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('training/training_list') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconMeetingList.png" alt="Training List"></figure>
                <figcaption>Training List</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('training/training_performance') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEmployJobPosition.png" alt="Training Performance"></figure>
                <figcaption>Add Training Performance</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('training/getemployesrating') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEmployeeStatus.png" alt="Performance View"></figure>
                <figcaption>Performance View (MTG Employees)</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('training/getnewemployesrating') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEmployeeStatus.png" alt="Performance View"></figure>
                <figcaption>Performance View (New Employees)</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('training/punchreport') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/exceptedEmployees.png" alt="Training Report"></figure>
                <figcaption>Training Report (MTG Employees)</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('training/punchreportnew') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/exceptedEmployees.png" alt="Training Report"></figure>
                <figcaption>Training Report (New Employees)</figcaption>
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
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt=""></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
    <?php }} ?>

</div>
@endsection
