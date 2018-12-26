<?php $n = $rfqs->perPage() * ($rfqs->currentPage() - 1); ?> 
@forelse ($rfqs as $rfq)

<tr>
    <td>{{$rfq->rfq_code}}</td>
    <td>{{$rfq->title}}</td>
    <td><?php echo date("d-m-Y", strtotime($rfq->created_at)); ?></td>
    <td>{{$rfq->code}}</td>
    <td>{{$rfq->first_name}}</td>
    <td class="amountAlign"><?php echo Customhelper::numberformatter($rfq->total_price); ?></td>
    <td>{{ $rfq->rfq_status }}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/rfq/view', ['id' => Crypt::encrypt($rfq->id)]) }}" target="_blank">View</a>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="2">No Records Found</td></tr>
@endforelse

<?php if($rfqs->lastpage() > 1){ ?>
    <tr class="paginationHolder"><th><div>   {!! $rfqs->render() !!}</div> </th></tr>
<?php } ?>
                