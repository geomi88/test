<?php  $n = $regions->perPage() * ($regions->currentPage() - 1); ?> 
    @foreach ($regions as $region)
    @if($region->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="region_slno">{{ $n }}</td>
        <td class="region_name">{{$region->name}}</td>
        <td class="region_alias">{{$region->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/region/edit', ['id' => Crypt::encrypt($region->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/region/'.$status, ['id' => Crypt::encrypt($region->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/region/delete', ['id' => Crypt::encrypt($region->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($regions->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $regions->render() !!}</div> </th></tr>
    <?php } ?>

