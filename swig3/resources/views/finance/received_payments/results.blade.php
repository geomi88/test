<?php $n = $payments->perPage() * ($payments->currentPage() - 1); ?>   
@foreach ($payments as $payment)

<tr>
    <td>{{$payment->payment_code}}</td>
    <td><?php echo date("d-m-Y", strtotime($payment->created_at)); ?></td>
    <td>{{$payment->paymode}}</td>
    <td style="text-align:right;padding-right: 20px;"><?php echo Customhelper::numberformatter($payment->total_amount); ?></td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('finance/received_payments/view', ['id' => Crypt::encrypt($payment->id)]) }}">View</a>
        </div>
    </td>
</tr>
@endforeach

<?php if(count($payments) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $payments->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                