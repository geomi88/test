<?php $n = $policies->perPage() * ($policies->currentPage() - 1); ?> 
@foreach ($policies as $policy)
<?php 

    $n++; 
    $strdata=strip_tags($policy->content);
    $stringtoshow=substr(strip_tags($strdata), 0, 300);
    if(strlen($strdata)>300){
        $stringtoshow.="...";
    }
?>
<tr>
    <td >{{ $n }}</td>
    <td >{{$policy->name}}</td>
    <td >{{$policy->alias_name}}</td>
    <td >{{$stringtoshow}}</td>
    <td ><?php echo date("d-m-Y", strtotime($policy->created_at)); ?></td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
            <div class="actionBtnHolderV1">
                <a class="btnAction edit bgBlue" href="{{ URL::to('organizationchart/policy/edit', ['id' => Crypt::encrypt($policy->id)]) }}">Edit</a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('organizationchart/policy/delete', ['id' => Crypt::encrypt($policy->id)]) }}">Delete</a>
            </div>
        </div>
    </td>
</tr>
@endforeach

<?php if (count($policies) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $policies->render() !!}</div> </th></tr>
<?php } ?>
