<?php $n = $requisitions->perPage() * ($requisitions->currentPage() - 1); ?>   
@foreach ($requisitions as $requisition)

<tr>
    <td>{{$requisition->requisition_code}}</td>
    <td>{{$requisition->title}}</td>
    <td><?php echo date("d-m-Y", strtotime($requisition->created_at)); ?></td>
    <td>{{$requisition->reqstatus}}</td>
    <td>{{$requisition->leave_type_status}}</td>
    <td><?php echo date("d-m-Y", strtotime($requisition->leave_from)); ?></td>
    <td><?php echo date("d-m-Y", strtotime($requisition->leave_to)); ?></td>
    <td>{{$requisition->first_name}}</td>
    <td>{{str_replace("_"," ",$requisition->sys_position)}}</td>
    <td>{{str_limit($requisition->description,'100')}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/leave_report/view', ['id' => Crypt::encrypt($requisition->id)]) }}" target="_blank">View</a>
        </div>
    </td>
</tr>
@endforeach

<?php if (count($requisitions) > 0) { ?>
    <tr class="paginationHolder"><th><div>   {!! $requisitions->render() !!}</div> </th></tr>
        <?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                