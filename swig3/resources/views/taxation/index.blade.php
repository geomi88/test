@extends('layouts.main')
@section('content')
<div class="dashboardList">

<!--    <a class="dashboardItems" href="{{ url('mis/planning') }}" value="planning">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="{{ URL::asset('images/iconPlanning.png')}}" alt="Company" name="planning"></figure>
                <input type="hidden" name="doc" value="PLANNING">
                <figcaption>Planning</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('mis/recipe') }}" value="recipe">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <input type="hidden" name="doc" value="RECIPE">
                <figure><img src="{{ URL::asset('images/iconRecipe.png')}}" alt="Region" name="recipe"></figure>
                <figcaption>Recipe</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('mis/kpi') }}" value="kpi">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <input type="hidden" name="doc" value="KPI">
                <figure><img src="{{ URL::asset('images/iconKpi.png')}}" alt="Branch" name="kpi" name="kpi"></figure>
                <figcaption>Kpi</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('mis/chart') }}"  value="chart">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <input type="hidden" name="doc" value="CHART">
                <figure><img src="{{ URL::asset('images/iconChart.png')}}" alt="Branch" name="chart"></figure>
                <figcaption>Chart</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('mis/pos_sales') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconPos.png" alt="POS sales"></figure>
                <figcaption>POS Sales</figcaption>
            </div>
        </div>
    </a>-->
   
        <?php
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
            ?>
</div>
@endsection
