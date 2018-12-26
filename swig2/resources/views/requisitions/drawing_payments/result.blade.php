<?php $n = $requisitions->perPage() * ($requisitions->currentPage() - 1); ?>   
@foreach ($requisitions as $requisition)

<tr>
    <td>{{$requisition->payment_code}}</td>
    <td>{{$requisition->fromname}} {{$requisition->fromalias}}</td>
    <td>{{$requisition->toname}} {{$requisition->toalias}}</td>
    <td class="amountAlign"><?php echo Customhelper::numberformatter($requisition->total_amount); ?></td>
    @if($requisition->status==2)
        <td>Approved</td> 
        @elseif($requisition->status==1)
        <td>Pending</td>
        @else
        <td>Rejected</td> 
        @endif

        <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/payment_completed_advice/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
        </div>
    </td>    
           
</tr>
@endforeach

<?php if(count($requisitions) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $requisitions->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                