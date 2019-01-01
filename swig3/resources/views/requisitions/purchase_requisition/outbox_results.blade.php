<?php $n = $requisitions->perPage() * ($requisitions->currentPage() - 1); ?>   
@foreach ($requisitions as $requisition)

<tr>
    <td>{{$requisition->requisition_code}}</td>
    <td>{{$requisition->title}}</td>
    <td>{{$requisition->name}}</td>
    <td><?php echo date("d-m-Y", strtotime($requisition->created_at)); ?></td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            @if($requisition->name=="Purchase Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/purchase_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->name=="General Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/general_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->name=="Maintainance Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/maintainance_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
             @elseif($requisition->name=="Advance Payment Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/advance_payment_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->name=="Service Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/service_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
	    @elseif($requisition->name=="Leave Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/leave_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
	    @elseif($requisition->name=="Import Purchase Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/import_purchase_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}">View</a>
            @elseif($requisition->name=="Owner Drawings Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/drawing_requsition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @else
            <a class="btnAction action bgGreen" href="javascript:void(0)">View</a>
            @endif
        </div>
    </td>
</tr>
@endforeach

<?php if(count($requisitions) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $requisitions->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                