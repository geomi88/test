@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    <a class="dashboardItems" href="{{ url('reception/visitors_log') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconOffice.png" alt="Visitors Log"></figure>
                <figcaption>Visitors Log</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('reception/visitors_list') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconListEmployees.png" alt="Visitors List"></figure>
                <figcaption>Visitors List</figcaption>
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
