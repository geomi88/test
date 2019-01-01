<?php  $n = $departments->perPage() * ($departments->currentPage() - 1); ?> 
    @foreach ($departments as $department)
    @if($department->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="dep_slno">{{ $n }}</td>
        <td class="dep_name">{{$department->name}}</td>
        <td class="dep_alias">{{$department->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/department/edit', ['id' => Crypt::encrypt($department->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/department/'.$status, ['id' => Crypt::encrypt($department->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/department/delete', ['id' => Crypt::encrypt($department->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($departments->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $departments->render() !!}</div> </th></tr>
    <?php } ?>

