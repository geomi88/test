@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('dashboard')}}">Back</a>
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
    
    <form action="{{ action('Tasks\AssigntaskController@update') }}" method="post" id="frmeditplan">
        <div class="fieldGroup" id="fieldSet1">
             <div class="custRow">
                <div class="custCol-4">
                    <input type="hidden" id="planid" name="planid" value="{{$task->id}}">
                    <input type="hidden" id="oldstatus" name="oldstatus" value="{{$task->status}}">
                    <div class="inputHolder bgSelect">
                        <label>Status</label>
                        <select class="commoSelect" name="task_status" id="task_status">
                            <option value="">Select Status</option>
                            <option <?php if ($task->status ==2) { echo "selected";} ?> value="2">Pending</option>
                            <option <?php if ($task->status ==3) { echo "selected";} ?> value="3">Completed</option>
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update"  class="commonBtn bgGreen addBtn" id="btnupdateplan">
                </div>
            </div>

        </div>
    </form>
</div>
@endsection