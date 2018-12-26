<?php $n = $visitors->perPage() * ($visitors->currentPage() - 1); ?> 
@foreach ($visitors as $visitor)

<?php $n++; ?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <td class="log_name">{{$visitor->name}}</td>
    <td class="log_date"><?php echo date("d-m-Y H:i", strtotime($visitor->date_time)); ?></td>
    <td class="log_emp">{{$visitor->username}} : {{$visitor->first_name}}</td>
    <td class="log_purp">{{$visitor->purpose}}</td>
    <td class="log_mob">{{$visitor->mobile}}</td>
    <td class="log_email">{{$visitor->email}}</td>
   
</tr>
@endforeach

<?php if (count($visitors) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $visitors->render() !!}</div> </th></tr>
<?php } ?>
