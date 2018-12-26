<?php  $n = $empwarehouseData->perPage() * ($empwarehouseData->currentPage() - 1); ?> 
    @foreach ($empwarehouseData as $eachData)
    @if($eachData->status==1)
    <?php $status='Enable'; $status_action = 'Disable'; ?>
    @else
    <?php $status='Disable'; $status_action = 'Enable' ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="inve_code">{{$eachData->username}}: {{$eachData->first_name}}</td>
        <td class="inve_name">{{$eachData->name}}</td>
        <td class="inve_group">{{$status}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
		<div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('elegantclub/warehose_allocation/edit', ['id' => Crypt::encrypt($eachData->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('elegantclub/warehose_allocation/'.$status_action, ['id' => Crypt::encrypt($eachData->id)]) }}"><?php echo $status_action; ?></a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('elegantclub/warehose_allocation/delete', ['id' => Crypt::encrypt($eachData->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if($empwarehouseData->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $empwarehouseData->render() !!}</div> </th></tr>
    <?php } ?>