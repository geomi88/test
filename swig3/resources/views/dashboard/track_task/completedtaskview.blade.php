@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('dashboard/track_task')}}">Back</a>
    <header class="pageTitle">
        <h1>Task <span>Details</span></h1>
        
    </header>	
    
    <div class="reportV1 mapView">
        <?php
            if ($task->is_all_day_task) {
                $start=date("d-m-Y", strtotime($task->start_date));
                $end=date("d-m-Y", strtotime($task->end_date));
            } else {
                $start=date("d-m-Y H:i:s", strtotime($task->start_date));
                $end=date("d-m-Y H:i:s", strtotime($task->end_date));
            }
            
            if($task->status==3){
                $status="Completed";
            }else if($task->status==2){
                $status="Pending";
            }else{
                $status="New";
            }
        ?>
        
        <ul class="custRow">
            <li class="custCol-12">
                <b>Title</b>
                {{$task->title}}
            </li>
        </ul>
        
        <ul class="custRow">
            <li class="custCol-12">
                <b>Description</b>
                {{$task->description}}
            </li>
        </ul>
        
        <ul class="custRow">
            <li class="custCol-5">
                <b>Start Date</b>
                <?php echo $start;?>
            </li>
            <li class="custCol-5">
                <b>End Date</b>
                <?php echo $end;?>
            </li>
        </ul>
        
        <ul class="custRow">
            <li class="custCol-5">
                <b>Assigned By</b>
                {{$task->assignedname}}
            </li>
            <li class="custCol-5">
                <b>Status</b>
                <?php echo $status;?>
            </li>
        </ul>
    </div>
    
   
</div>
@endsection