<?php $n = $punchdata->perPage() * ($punchdata->currentPage() - 1); ?> 
@foreach ($punchdata as $data)

<?php $n++; ?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <td class="log_emp">{{$data->username}} : {{$data->first_name}}</td>
    <td class="log_name">{{$data->ratingname}}</td>
    <td class="log_purp">{{$data->ratedbyname}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
            <div class="actionBtnHolderV1">
                <a class="btnAction edit bgBlue" href="{{ URL::to('organizationchart/punch_performance/edit', ['id' => Crypt::encrypt($data->id)]) }}">Edit</a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('organizationchart/punch_performance/delete', ['id' => Crypt::encrypt($data->id)]) }}">Delete</a>
            </div>
        </div>
    </td>
</tr>
@endforeach

<?php if (count($punchdata) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $punchdata->render() !!}</div> </th></tr>
<?php } ?>
