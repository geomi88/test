<?php  $n = $shifts->perPage() * ($shifts->currentPage() - 1); ?> 
    @foreach ($shifts as $shift)
    @if($shift->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="shift_slno">{{ $n }}</td>
        <td class="shift_name">{{$shift->name}}</td>
        <td class="shift_alias">{{$shift->alias_name}}</td>
        <td class="shift_start">{{$shift->start_time}}</td>
        <td class="shift_end">{{$shift->end_time}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/job_shifts/edit', ['id' => Crypt::encrypt($shift->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/job_shifts/'.$status, ['id' => Crypt::encrypt($shift->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/job_shifts/delete', ['id' => Crypt::encrypt($shift->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($shifts->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $shifts->render() !!}</div> </th></tr>
    <?php } ?>

