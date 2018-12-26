
<?php $n = $item_requests->perPage() * ($item_requests->currentPage() - 1); ?>   
@foreach ($item_requests as $item_request)

<tr>
    <td>{{$item_request->request_id}}</td>
    <td><?php echo str_replace('_',' ',$item_request->request_status);?></td>
    <td>{{$item_request->branch_code.'-'.$item_request->branch_name}}</td>
    <td>{{$item_request->warehouse}}</td>
    <td><?php echo date('d-m-Y', strtotime($item_request->created_at)); ?></td>                        
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('supervisors/inventory_request/showdetails', ['id' => Crypt::encrypt($item_request->id)]) }}">View</a>
        </div>
    </td>
</tr>
@endforeach
<?php if($item_requests->lastPage() > 1){ ?>
    <tr class="paginationHolder"><th><div>   {!! $item_requests->render() !!}</div> </th></tr>
<?php } ?>                