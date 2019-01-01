<?php $n = $punchdata->perPage() * ($punchdata->currentPage() - 1); ?> 
@foreach ($punchdata as $data)

<?php $n++; ?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <td class="log_emp">{{$data->username}} : {{$data->first_name}}</td>
    <td class="log_name">{{$data->ratingname}}</td>
    <td class="log_purp">{{$data->ratedbyname}}</td>
    
</tr>
@endforeach

<?php if (count($punchdata) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $punchdata->render() !!}</div> </th></tr>
<?php } ?>
