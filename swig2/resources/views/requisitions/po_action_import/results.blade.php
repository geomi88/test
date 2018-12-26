<?php $n = $purchaseorders->perPage() * ($purchaseorders->currentPage() - 1); ?>   
@foreach ($purchaseorders as $order)

<tr>
    <td>{{$order->order_code}}</td>
    <td>{{$order->requisition_code}}</td>
    <td>{{$order->payment_code}}</td>
    <td><?php echo date("d-m-Y", strtotime($order->created_at)); ?></td>
    <td class="amountAlign"><?php echo Customhelper::numberformatter($order->amount); ?></td>
    <td class="amountAlign"><?php echo Customhelper::numberformatter($order->total_price); ?></td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/purchase_order_list/view', ['id' => Crypt::encrypt($order->id)]) }}" target="_blank">View</a>
        </div>
    </td>
</tr>
@endforeach

<?php if (count($purchaseorders) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $purchaseorders->render() !!}</div> </th></tr>
        <?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                