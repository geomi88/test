<?php $n = $action_list->perPage() * ($action_list->currentPage() - 1); ?>   
@forelse ($action_list as $order)

<tr>
    <td>{{$order->purchase_number}}</td>
    <td><?php echo date("d-m-Y", strtotime($order->created_at)); ?></td>
    <td>{{$order->action_made}}</td>
    <td>{{$order->created_by}}</td>
</tr>
@empty
    <tr><td colspan="2">No Records Found</td></tr>
@endforelse

<?php if($action_list->lastpage() > 1){ ?>
    <tr class="paginationHolder"><th><div>   {!! $action_list->render() !!}</div> </th></tr>
<?php } ?>