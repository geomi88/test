<?php $n = $requisitions->perPage() * ($requisitions->currentPage() - 1); ?>   
@foreach ($requisitions as $requisition)

<tr>
    <td>{{$requisition->requisition_code}}</td>
    <td>{{$requisition->title}}</td>
    <td>{{$requisition->name}}</td>
    <td><?php echo date("d-m-Y", strtotime($requisition->updated_at)); ?></td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/payment_advice/add', ['id' => Crypt::encrypt($requisition->id)]) }}">Payment</a>
        </div>
    </td>
</tr>
@endforeach

<?php if(count($requisitions) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $requisitions->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                