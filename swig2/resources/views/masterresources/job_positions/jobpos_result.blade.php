<?php  $n = $jposs->perPage() * ($jposs->currentPage() - 1); ?> 
    @foreach ($jposs as $jpos)
    @if($jpos->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="job_slno">{{ $n }}</td>
        <td class="job_name"><?php echo str_replace('_',' ',$jpos->name);?></td>
        <td class="job_code">{{$jpos->job_code}}</td>
        <td class="job_alias">{{$jpos->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/job_positions/edit', ['id' => Crypt::encrypt($jpos->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/job_positions/'.$status, ['id' => Crypt::encrypt($jpos->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/job_positions/delete', ['id' => Crypt::encrypt($jpos->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($jposs->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $jposs->render() !!}</div> </th></tr>
    <?php } ?>
