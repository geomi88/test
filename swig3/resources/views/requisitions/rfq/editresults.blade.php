<?php $n = $rfqs->perPage() * ($rfqs->currentPage() - 1); ?>   
@foreach ($rfqs as $rfq)

<tr>
    <td>{{$rfq->rfq_code}}</td>
    <td>{{$rfq->title}}</td>
    <td><?php echo date("d-m-Y", strtotime($rfq->created_at)); ?></td>
    <td>{{$rfq->code}}</td>
    <td>{{$rfq->first_name}}</td>
    <td class="amountAlign"><?php echo Customhelper::numberformatter($rfq->total_price); ?></td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
            <div class="actionBtnHolderV1">
            <a class="btnAction edit bgBlue" href="{{ URL::to('requisitions/rfq/edit', ['id' => Crypt::encrypt($rfq->id)]) }}">Edit</a>
            <a class="btnAction delete bgLightRed" href="{{ URL::to('requisitions/rfq/delete', ['id' => Crypt::encrypt($rfq->id)]) }}">Delete</a>
            </div>
        </div>
    </td>
</tr>
@endforeach

<?php if(count($rfqs) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $rfqs->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                