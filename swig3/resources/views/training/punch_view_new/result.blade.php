<?php 
if(count($employedata)>0){
$n = $employedata->perPage() * ($employedata->currentPage() - 1); ?> 
@foreach ($employedata as $data)

<?php $n++; ?>
<tr>
    <td >{{ $n }}</td>
    <td >{{$data->guest_phone}} : {{$data->guest_name}}</td>
    <td ><?php if($data->created_at){ echo date('d-m-Y', strtotime($data->created_at)); }?></td>
    <td >{{$data->ratedbyname}}</td>
    <td >{{$data->reason}}</td>
    
</tr>
@endforeach
<tr class="paginationHolder"><th><div>   {!! $employedata->render() !!}</div> </th></tr>

<?php } ?>
