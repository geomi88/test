@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    <a class="dashboardItems" href="{{ url('masterresources/check_list_category') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/checkListCategory.png" alt="Check List Category"></figure>
                <figcaption>Check List Category</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/check_list') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/checkList.png" alt="Check List"></figure>
                <figcaption>Check List</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/checklist_entry') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/checkListEntry.png" alt="Check List Entry"></figure>
                <figcaption>Check List Entry</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/checklist_report') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/checkListReport.png" alt="Check List Report"></figure>
                <figcaption>Check List Report</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/warnings') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/warning.png" alt="Warnings"></figure>
                <figcaption>Warnings</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/editwarnings') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconWraningEdit.png" alt="Edit Warnings"></figure>
                <figcaption>Edit Warnings</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/warnings_report') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/warningReport.png" alt="Warnings Report"></figure>
                <figcaption>Warnings Report</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/graphindex') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconCategoryWarning.png" alt="Warnings By Category"></figure>
                <figcaption>Warnings By Category</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('checklist/ratingindex') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconRatingGraph.png" alt="Check Points Rating Graph"></figure>
                <figcaption>Check Points Rating Graph</figcaption>
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
