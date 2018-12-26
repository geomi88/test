<?php  $n = $branches->perPage() * ($branches->currentPage() - 1); ?> 
    @foreach ($branches as $branch)
    @if($branch->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="branch_slno">{{ $n }}</td>
        <td class="branch_name">{{$branch->name}}</td>
        <td class="branch_area">{{$branch->area_name}}</td>
        <td class="branch_code">{{$branch->branch_code}}</td>
        <td class="branch_alias">{{$branch->alias_name}}</td>
         <td class="branch_code">{{$branch->branch_phone}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/branch/edit', ['id' => Crypt::encrypt($branch->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/branch/'.$status, ['id' => Crypt::encrypt($branch->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/branch/delete', ['id' => Crypt::encrypt($branch->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($branches->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $branches->render() !!}</div> </th></tr>
    <?php } ?>
