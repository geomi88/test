<?php  $n = $spots->perPage() * ($spots->currentPage() - 1); ?> 
    @foreach ($spots as $spot)
    @if($spot->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="spot_slno">{{ $n }}</td>
        <td class="spot_name">{{$spot->name}}</td>
        <td class="spot_warehouse">{{$spot->warehouse_name}}</td>
        <td class="spot_alias">{{$spot->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/spot/edit', ['id' => Crypt::encrypt($spot->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/spot/'.$status, ['id' => Crypt::encrypt($spot->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/spot/delete', ['id' => Crypt::encrypt($spot->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($spots->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $spots->render() !!}</div> </th></tr>
    <?php } ?>
