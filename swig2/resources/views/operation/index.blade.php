@extends('layouts.main')
@section('content')
<div class="dashboardList">

<?php if ($employee_details->admin_status == 1) { ?>
    <a class="dashboardItems" href="{{ url('operation/resource_allocation') }}" value="resource_allocation">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconResAllocation.png')}}" alt="Company" name="resource_allocation"></figure>
               
                <figcaption>Resource Allocation</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('operation/resource_listing') }}" value="resource_listing">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconResList.png')}}" alt="Company" name="resource_listing"></figure>
                
                <figcaption>Resource Listing</figcaption>
            </div>
        </div>
    </a>
    
      <a class="dashboardItems" href="{{ url('operation/pos_cashier_edit') }}" value="pos_cashier_edit">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconPosCashierEdit.png')}}" alt="pos_cashier_edit" name="pos_cashier_edit"></figure>
                
                <figcaption>POS Cashier Edit</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('operation/pos_supervisor_edit') }}" value="pos_supervisor_edit">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconPosSupervisorEdit.png')}}" alt="pos_supervisor_edit" name="pos_supervisor_edit"></figure>
                
                <figcaption>POS Supervisor Edit</figcaption>
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