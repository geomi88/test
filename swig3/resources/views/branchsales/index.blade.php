@extends('layouts.main')
@section('content')
<div class="dashboardList">

<?php foreach ($user_sub_modules as $user_sub_module) { ?>
            <a class="dashboardItems" href="{{ url($user_sub_module->url) }}">
                <div class="dashboardTable">
                    <div class="dashboardTd">
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="{{$user_sub_module->name}}"></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
    <?php } ?>    
 
</div>
@endsection