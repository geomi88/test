@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>

    
    <a class="dashboardItems" href="{{ url('organizationchart/organizationchartnew/add') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconCreateOrganisationChart.png" alt="Organization Chart"></figure>
                <figcaption>Create Organization Chart</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('organizationchart/organizationchartnew/getchartlist') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconListOrganisationChart.png" alt="Organization Chart"></figure>
                <figcaption>List Organization Chart</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('organizationchart/organizationchartnew/editlist') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEditOrganisationChart.png" alt="Organization Chart"></figure>
                <figcaption>Edit Organization Chart</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('organizationchart/policy') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconLeaveRequest.png" alt="Create Policy"></figure>
                <figcaption>Create Policy</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('organizationchart/policy_list') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconPlanning.png" alt="Policy List"></figure>
                <figcaption>Policy List</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('organizationchart/punch_performance') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEmployJobPosition.png" alt="Punch Performance"></figure>
                <figcaption>Add Punch Performance</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('organizationchart/getemployesrating') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEmployeeStatus.png" alt="Punch View"></figure>
                <figcaption>Punch View</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('organizationchart/puncheditindex') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEmployJobPosition.png" alt="Punch Performance"></figure>
                <figcaption>Edit Punch Performance</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('organizationchart/punchreport') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/exceptedEmployees.png" alt="Performance Report"></figure>
                <figcaption>Performance Report</figcaption>
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
