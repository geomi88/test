@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    <a class="dashboardItems" href="{{ url('employee') }}" value="planning">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconListEmployees.png')}}" alt="Company" name="Employees"></figure>
                <figcaption>List Employees</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('employee/add') }}" value="planning">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconAddEmployee.png')}}" alt="Company" name="Employees"></figure>
                <figcaption>Add Employees</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('employee/edit') }}" value="planning">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconEditEmployee.png')}}" alt="Company" name="Employees"></figure>
                <figcaption>Edit Employees</figcaption>
            </div>
        </div>
    </a>
<!--    delete employee-->
    <a class="dashboardItems" href="{{ url('employee/delete') }}" value="planning">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconDeleteEmployee.png')}}" alt="Company" name="Employees"></figure>
                <figcaption>Delete Employees</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('hr/userpermissions') }}" value="User Permissions">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconEligibility.png')}}" alt="User Permissions" name="User_Permissions"></figure>
                <figcaption>User Permissions</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('hr/countrywise') }}" value="Employee By Nationality">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconEmployNationality.png')}}" alt="Employee By Nationality" name="Employee_By_Nationality"></figure>
                <figcaption>Employee By Nationality</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('hr/job_positionwise') }}" value="Employee By Job Position">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconEmployJobPosition.png')}}" alt="Employee By Job Position" name="User_Permissions"></figure>
                <figcaption>Employee By Job Position</figcaption>
            </div>
        </div>
    </a>
   
    <a class="dashboardItems" href="{{ url('hr/menu_order') }}" value="User Permissions">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/menuOrder.png')}}" alt="Menu Order" name="Menu_order"></figure>
                <figcaption>Menu Order</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('hr/payroll') }}" value="Payroll">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconpayrol.png')}}" alt="Payroll" name="Payroll"></figure>
                <figcaption>Payroll</figcaption>
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
    <?php }
} ?>
</div>
@endsection