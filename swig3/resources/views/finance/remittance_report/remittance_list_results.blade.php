<?php $n = $payments->perPage() * ($payments->currentPage() - 1); ?>   
@foreach ($payments as $payment)

<tr>
    <td class="paymentcodeC">{{$payment->payment_code}}</td>
    <td class="dateC"><?php echo date("d-m-Y", strtotime($payment->created_at)); ?></td>
    <td class="dateR"><?php if($payment->remitted_date){ echo date("d-m-Y", strtotime($payment->remitted_date));}else { '';} ?></td>
    <td class="clscode">{{$payment->code}}</td>
    <td class="nameC">{{$payment->first_name}}</td>
    <td class="modeC">{{$payment->paymode}}</td>
    <td class="remittanceC">{{$payment->remittance_number}}</td>
    @if($payment->remittance_number != '')
    <td class="statusC" style="color: green;">{{'Paid'}}</td>
    @else
    <td class="statusC" style="color: red;">{{'Not Paid'}}</td>
    @endif
    <td class="amountC" style="text-align:right;padding-right: 20px;"><?php echo Customhelper::numberformatter($payment->total_amount); ?></td>
    <td class="datecountC">{{$payment->daycount}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('finance/remittance_report/view', ['id' => Crypt::encrypt($payment->id)]) }}" target="_blank">View</a>
        </div>
    </td>
</tr>
@endforeach

<?php if(count($payments) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $payments->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                