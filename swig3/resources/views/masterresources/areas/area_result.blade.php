<?php  $n = $areas->perPage() * ($areas->currentPage() - 1); ?> 
    @foreach ($areas as $area)
    @if($area->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="area_slno">{{ $n }}</td>
        <td class="area_name">{{$area->name}}</td>
        <td class="area_region">{{$area->region_name}}</td>
        <td class="area_alias">{{$area->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/areas/edit', ['id' => Crypt::encrypt($area->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/areas/'.$status, ['id' => Crypt::encrypt($area->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/areas/delete', ['id' => Crypt::encrypt($area->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($areas->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $areas->render() !!}</div> </th></tr>
    <?php } ?>
