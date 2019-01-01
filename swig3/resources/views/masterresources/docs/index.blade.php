@extends('layouts.main')
@section('content')
<div class="dashboardList">

    <a class="dashboardItems" href="{{ url('mis/planning') }}" value="planning">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="../images/iconPlanning.png" alt="Company" name="planning"></figure>
                <input type="hidden" name="doc" value="PLANNING">
                <figcaption>Planning</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('mis/recipe') }}" value="recipe">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <input type="hidden" name="doc" value="RECIPE">
                <figure><img src="../images/iconRecipe.png" alt="Region" name="recipe"></figure>
                <figcaption>Recipe</figcaption>
            </div>
        </div>
    </a>
    
     <a class="dashboardItems" href="{{ url('mis/kpi') }}" value="kpi">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <input type="hidden" name="doc" value="KPI">
                <figure><img src="../images/iconKpi.png" alt="Branch" name="kpi" name="kpi"></figure>
                <figcaption>Kpi</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('mis/chart') }}"  value="chart">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <input type="hidden" name="doc" value="CHART">
                <figure><img src="../images/iconChart.png" alt="Branch" name="chart"></figure>
                <figcaption>Chart</figcaption>
            </div>
        </div>
    </a>
    
   

</div>
@endsection