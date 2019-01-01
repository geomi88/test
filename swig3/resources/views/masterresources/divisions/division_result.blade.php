<?php  $n = $divisions->perPage() * ($divisions->currentPage() - 1); ?> 
    @foreach ($divisions as $division)
    @if($division->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="division_slno">{{ $n }}</td>
        <td class="division_name">{{$division->name}}</td>
        <td class="division_dept">{{$division->dept_name}}</td>
        <td class="division_alias">{{$division->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/divisions/edit', ['id' => Crypt::encrypt($division->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/divisions/'.$status, ['id' => Crypt::encrypt($division->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/divisions/delete', ['id' => Crypt::encrypt($division->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($divisions->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $divisions->render() !!}</div> </th></tr>
    <?php } ?>
