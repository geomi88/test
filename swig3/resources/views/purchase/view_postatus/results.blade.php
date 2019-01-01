<?php $n = $po_details->perPage() * ($po_details->currentPage() - 1); ?>   
@forelse ($po_details as $order)

<tr>
    <td>{{$order->order_no}}</td>
    <td><?php echo date("d-m-Y", strtotime($order->created_at)); ?></td>
    <td>{{$order->requisition_number}}</td>
    <td>{{$order->po_type}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action viewBtn bgGreen" href="{{ URL::to('purchase/view_postatus/view', ['id' => Crypt::encrypt($order->id)]) }}" >View PO</a>
        </div>
    </td>
</tr>
@empty
<tr>
    <td></td>
    <td></td>
    <td>No Records Found</td>
    <td></td>
    <td></td>
</tr>
@endforelse

<?php if ($po_details->lastpage() > 1) { ?>
    <tr class="paginationHolder"><th><div>   {!! $po_details->render() !!}</div> </th></tr>
<?php } ?>