<?php  $n = $warehouses->perPage() * ($warehouses->currentPage() - 1); ?> 
    @foreach ($warehouses as $warehouse)
    @if($warehouse->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="warehouse_slno">{{ $n }}</td>
        <td class="warehouse_name">{{$warehouse->name}}</td>
        <td class="warehouse_region">{{$warehouse->region_name}}</td>
        <td class="warehouse_manager">{{$warehouse->manager}}</td>
        <td class="warehouse_alias">{{$warehouse->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/warehouse/edit', ['id' => Crypt::encrypt($warehouse->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/warehouse/'.$status, ['id' => Crypt::encrypt($warehouse->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/warehouse/delete', ['id' => Crypt::encrypt($warehouse->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($warehouses->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $warehouses->render() !!}</div> </th></tr>
    <?php } ?>
