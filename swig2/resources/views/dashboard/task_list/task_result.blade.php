<?php  $n = $tasks->perPage() * ($tasks->currentPage() - 1); ?> 
    @foreach ($tasks as $task)
    <?php
    $n++;
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
    <tr>
        <td class="task_slno">{{ $n }}</td>
        <td class="task_title">{{$task->title}}</td>
        <td class="task_start"><?php echo $start;?></td>
        <td class="task_end"><?php echo $end;?></td>
        <td class="task_status"><?php echo $status;?></td>
        <td class="task_date"><?php echo date("d-m-Y", strtotime($task->created_at));?></td>
        <td class="task_assigned">{{$task->assignedname}}</td>
        <td class="task_description">{{$task->description}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet linkviewtodo">
                <a class="btnAction action bgBlue" href="{{ URL::to('dashboard/task_list/edit', ['id' => Crypt::encrypt($task->id)]) }}">Edit Task</a>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($tasks->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $tasks->render() !!}</div> </th></tr>
    <?php } ?>
