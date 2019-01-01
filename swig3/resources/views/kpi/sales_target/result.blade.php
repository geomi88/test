<?php  $n = $targets->perPage() * ($targets->currentPage() - 1); ?> 
    @foreach ($targets as $target)
    <?php  $n++; ?>
    <tr>
        <td class="tar_code">{{$target->branch_code}}</td>
        <td class="tar_name">{{$target->name}}</td>
        <td class="tar_quarter">{{$target->quarter}}</td>
        <td class="tar_amount">{{Customhelper::numberformatter($target->target_amount)}}</td>

    </tr>
    @endforeach
    <?php if($targets->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $targets->render() !!}</div> </th></tr>
    <?php } ?>