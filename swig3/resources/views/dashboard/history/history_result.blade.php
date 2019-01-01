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
    }else{
        $status="Deleted";
    }
    
    ?>
    <tr>
        <td class="history_slno">{{ $n }}</td>
        <td class="history_title">{{$plan->title}}</td>
        <td class="history_start"><?php echo $start;?></td>
        <td class="history_end"><?php echo $end;?></td>
        <td class="history_status"><?php echo $status;?></td>
        <td class="history_date"><?php echo date("d-m-Y", strtotime($plan->created_at));?></td>
        <td class="history_description">{{$plan->description}}</td>
        
    </tr>
    @endforeach
    
    <?php if($plans->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $plans->render() !!}</div> </th></tr>
    <?php } ?>
