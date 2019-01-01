<?php  $n = $offices->perPage() * ($offices->currentPage() - 1); ?> 
    @foreach ($offices as $office)
    @if($office->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="dep_slno">{{ $n }}</td>
        <td class="dep_name">{{$office->name}}</td>
        <td class="dep_alias">{{$office->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/office/edit', ['id' => Crypt::encrypt($office->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/office/'.$status, ['id' => Crypt::encrypt($office->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/office/delete', ['id' => Crypt::encrypt($office->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($offices->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $offices->render() !!}</div> </th></tr>
    <?php } ?>

