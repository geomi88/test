<?php  $n = $plans->perPage() * ($plans->currentPage() - 1); ?> 
    @foreach ($plans as $plan)
    <?php
    $n++;
    if ($plan->is_all_day_task) {
        $start=date("d-m-Y", strtotime($plan->start_date));
        $end=date("d-m-Y", strtotime($plan->end_date));
    } else {
        $start=date("d-m-Y H:i:s", strtotime($plan->start_date));
        $end=date("d-m-Y H:i:s", strtotime($plan->end_date));
    }
    
    if($plan->status==3){
        $status="Completed";
    }else if($plan->status==2){
        $status="Pending";
    }else{
        $status="New";
    }
    
    ?>
    <tr>
        <td class="task_slno">{{ $n }}</td>
        <td class="task_name">{{$plan->first_name}} {{$plan->alias_name}}</td>
        <td class="task_title">{{$plan->title}}</td>
        <td class="task_start"><?php echo $start;?></td>
        <td class="task_end"><?php echo $end;?></td>
        <td class="task_status"><?php echo $status;?></td>
        <td class="task_date"><?php echo date("d-m-Y", strtotime($plan->created_at));?></td>
        <td class="task_description">{{$plan->description}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
                    <?php if($plan->status==3){?>
                        <a class="btnAction edit bgBlue" href="{{ URL::to('dashboard/track_task/editcompletedtask', ['id' => Crypt::encrypt($plan->id)]) }}">Edit</a>
                    <?php }else{?>
                        <a class="btnAction edit bgBlue" href="{{ URL::to('dashboard/track_task/edit', ['id' => Crypt::encrypt($plan->id)]) }}">Edit</a>
                    <?php } ?>
                    <a class="btnAction delete bgLightRed" href="{{ URL::to('dashboard/track_task/deleteplan', ['id' => Crypt::encrypt($plan->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($plans->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $plans->render() !!}</div> </th></tr>
    <?php } ?>
