<?php $n = $punchdata->perPage() * ($punchdata->currentPage() - 1); ?> 
@foreach ($punchdata as $data)

<?php $n++; ?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <td class="log_emp">{{$data->guest_phone}} : {{$data->guest_name}}</td>
    <td class="">{{$data->exceptionalcount}}<input type="text" class="exceptionalcount" disabled></td>
    <td class="">{{$data->effectivecount}}<input type="text" class="effectivecount" disabled></td>
    <td class="">{{$data->inconsistentcount}}<input type="text" class="inconsistentcount" disabled></td>
    <td class="">{{$data->unsatisfactorycount}}<input type="text" class="unsatisfactorycount" disabled></td>
    <td class="">{{$data->notacceptablecount}}<input type="text" class="notacceptablecount" disabled></td>
    
</tr>
@endforeach

<?php if (count($punchdata) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $punchdata->render() !!}</div> </th></tr>
<?php } ?>
