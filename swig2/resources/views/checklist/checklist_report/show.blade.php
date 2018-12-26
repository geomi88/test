@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('checklist/checklist_report')}}">Back</a>
    <header class="pageTitle">
        <h1>Check List Entry <span></span></h1>
    </header>	

    <div class="reportV1">
        <ul class="custRow">
            
            <li class="custCol-4">
                <b>Main Category</b>
                {{$checklistentry->maincategory}}
            </li>
            <li class="custCol-8">
                <b>Check Point</b>
                {{$checklistentry->checkpoint}}
            </li>
        </ul>

        <ul class="custRow">
            <li class="custCol-4">
                <b>Branch</b>
                {{$checklistentry->br_code}} : {{$checklistentry->br_name}}
            </li>
            <li class="custCol-4">
                <b>Rating</b>
                {{$checklistentry->rating}}
            </li>
            <li class="custCol-4">
                <b>Employee</b>
                {{$checklistentry->first_name}} {{$checklistentry->alias_name}}
            </li>
           
        </ul>
        <ul class="custRow">
            <li class="custCol-4">
                <b>Entry Date</b>
                <?php echo date("d-m-Y", strtotime($checklistentry->entry_date));?>
            </li>
            <li class="custCol-8">
                <b>Comments</b>
                {{$checklistentry->comments}}
            </li>
        </ul>
    </div>
</div>
@endsection