<?php  $n = $meetings->perPage() * ($meetings->currentPage() - 1); ?> 
    @foreach ($meetings as $meeting)
    <?php
    $n++;
    if ($meeting->is_all_day_task) {
        $start=date("d-m-Y", strtotime($meeting->start_date));
        $end=date("d-m-Y", strtotime($meeting->end_date));
    } else {
        $start=date("d-m-Y H:i:s", strtotime($meeting->start_date));
        $end=date("d-m-Y H:i:s", strtotime($meeting->end_date));
    }
    
    if($meeting->status==3){
        $status="Completed";
    }else if($meeting->status==2){
        $status="Pending";
    }else{
        $status="New";
    }
    
    ?>
    <tr>
        <td>{{ $n }}</td>
        <td>{{$meeting->title}}</td>
        <td><?php echo $start;?></td>
        <td><?php echo $end;?></td>
        <td><?php echo date("d-m-Y", strtotime($meeting->created_at));?></td>
        <td>{{$meeting->description}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction disable bgOrange" href="{{ URL::to('training/view', ['id' => Crypt::encrypt($meeting->id)]) }}">View</a>
                <?php if ($meeting->owner_id == Session::get('login_id')) { ?>
                <a class="btnAction edit bgBlue" href="{{ URL::to('training/edit', ['id' => Crypt::encrypt($meeting->id)]) }}">Edit</a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('training/delete', ['id' => Crypt::encrypt($meeting->id)]) }}">Delete</a>
                <?php }  ?>
		</div>
            </div>
        </td>

    </tr>
    @endforeach
    
    <?php // if($meetings->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $meetings->render() !!}</div> </th></tr>
    <?php // } ?>
