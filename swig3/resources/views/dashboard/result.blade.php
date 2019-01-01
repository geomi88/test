    <?php $n=0; ?>
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
        <td>{{ $n }}</td>
        <td>{{$plan->title}}</td>
        <td><?php echo $start;?></td>
        <td><?php echo $end;?></td>
        <td><?php echo $status;?></td>
        <td><?php echo date("d-m-Y", strtotime($plan->created_at));?></td>
        <td>{{$plan->description}}</td>
    </tr>
    @endforeach
    
   