<?php $n = $punchdata->perPage() * ($punchdata->currentPage() - 1); ?> 
@foreach ($punchdata as $data)

<?php $n++; ?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <?php if($data->traine_type==2){
        $empname=$data->guest_phone." : ".$data->guest_name;
        $empcategory="New";
    }else{
        $empname=$data->username." : ".$data->first_name;
        $empcategory="Existing";
        
    }
?>
    <td class="log_emp">{{$empname}}</td>
    <td class="log_emp">{{$empcategory}}</td>
    <td class="log_name">{{$data->ratingname}}</td>
    <td class="log_purp">{{$data->ratedbyname}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
            <div class="actionBtnHolderV1">
                <a class="btnAction delete bgLightRed" href="{{ URL::to('training/training_performance/delete', ['id' => Crypt::encrypt($data->id)]) }}">Delete</a>
            </div>
        </div>
    </td>
</tr>
@endforeach

<?php if (count($punchdata) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $punchdata->render() !!}</div> </th></tr>
<?php } ?>
