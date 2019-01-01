<?php $n = $requisitions->perPage() * ($requisitions->currentPage() - 1); ?>   
@foreach ($requisitions as $requisition)

<tr>
    <td>{{$requisition->requisition_code}}</td>
    <td>{{$requisition->title}}</td>
    <td>{{$requisition->req_name}}</td>
    <td><?php echo date("d-m-Y", strtotime($requisition->createdat)); ?></td>
    <td>{{$requisition->actionstatus}}</td>
    
    
    <td class="btnHolder">
        <div class="actionBtnSet">
            @if($requisition->req_name=="Purchase Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/purchase_userwise_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->req_name=="General Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/general_userwise_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->req_name=="Advance Payment Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/advance_userwise_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->req_name=="Leave Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/leave_userwise_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->req_name=="Maintainance Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/maintainance_userwise_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
            @elseif($requisition->req_name=="Service Requisition")
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/service_userwise_requisition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
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
                