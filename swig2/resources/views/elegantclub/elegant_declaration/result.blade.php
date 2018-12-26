<?php  $n = $declarations->perPage() * ($declarations->currentPage() - 1); ?> 
    @forelse ($declarations as $eachData)
    <?php  $n++; ?>
    <tr>
        <td>{{ $n }}</td>
        <td class="inve_code">{{$eachData->title}}</td>
        <td class="inve_name">{{$eachData->declaration_content}}</td>
        <td class="inve_name">{{$eachData->created_by}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
		<div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('elegantclub/declaration/edit', ['id' => Crypt::encrypt($eachData->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('elegantclub/declaration/delete', ['id' => Crypt::encrypt($eachData->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @empty
    <td colspan="5">
        No Data
    </td>
    @endforelse
    <?php if($declarations->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $declarations->render() !!}</div> </th></tr>
    <?php } ?>