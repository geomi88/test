<?php $n = $visitors->perPage() * ($visitors->currentPage() - 1); ?> 
@foreach ($visitors as $visitor)

<?php $n++; ?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <td class="log_name">{{$visitor->name}}</td>
    <td class="log_date"><?php echo date("d-m-Y H:i", strtotime($visitor->date_time)); ?></td>
    <td class="log_emp">{{$visitor->username}} : {{$visitor->first_name}}</td>
    <td class="log_purp">{{$visitor->purpose}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
            <div class="actionBtnHolderV1">
                <a class="btnAction edit bgBlue" href="{{ URL::to('reception/visitors_log/edit', ['id' => Crypt::encrypt($visitor->id)]) }}">Edit</a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('reception/visitors_log/delete', ['id' => Crypt::encrypt($visitor->id)]) }}">Delete</a>
            </div>
        </div>
    </td>
</tr>
@endforeach

<?php if (count($visitors) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $visitors->render() !!}</div> </th></tr>
<?php } ?>
