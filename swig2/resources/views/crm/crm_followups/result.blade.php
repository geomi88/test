<?php $n = $all_feedback->perPage() * ($all_feedback->currentPage() - 1); ?> 
@forelse ($all_feedback as $row)

<?php $n++;
$status = 'Pending';
if($row->is_closed == 2){
    $status = 'Closed';
}else if($row->is_closed == 3){
    $status = 'Following';
}
?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <td class="customer_name">{{$row->customer_name}}</td>
    <td class="log_date"><?php echo date("d-m-Y H:i", strtotime($row->created_at)); ?></td>
    <td class="mobile_number">{{$row->mobile_number}}</td>
    <td class="cus_branch">{{$row->branch_name}} </td>
    <td class="feed_created_by">{{$row->created_by}} </td>
    <td class="customer_comments">{{str_limit($row->customer_comment,100)}}</td>
    <td class="customer_status">{{$status}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
            <div class="actionBtnHolderV1">
                <a class="btnAction edit bgBlue" href="{{ URL::to('crm/all_crm_feedback/feedback_view', ['id' => Crypt::encrypt($row->id), 'page' => 'crm_followups']) }}">View</a>
            </div>
        </div>
    </td>

</tr>
@empty
<tr>
    <td colspan="8">
        No Data Found
    </td>
</tr>
@endforelse

<?php if ($all_feedback->lastpage() > 1) { ?>
    <tr class="paginationHolder"><th><div>   {!! $all_feedback->render() !!}</div> </th></tr>
<?php } ?>
