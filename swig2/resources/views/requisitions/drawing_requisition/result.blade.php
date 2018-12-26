<?php $n = $requisitions->perPage() * ($requisitions->currentPage() - 1); ?>   
@foreach ($requisitions as $requisition)

<tr>
    <td>{{$requisition->requisition_code}}</td>
    <td>{{$requisition->title}}</td>
    <td><?php echo date("d-m-Y", strtotime($requisition->created_at)); ?></td>
    <!--<td>{{$requisition->req_name}}</td>-->
    @if($requisition->status==4)
           <td>Approved</td> 
           @elseif($requisition->status==5)
           <td>Rejected</td> 
           @else
           <td>Pending</td> 
           @endif
    
    <td class="btnHolder">
        <div class="actionBtnSet">
            
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/drawing_requsition/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
             
        </div>
    </td>
</tr>
@endforeach

<?php if(count($requisitions) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $requisitions->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                