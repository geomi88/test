<?php $n = $declarations->perPage() * ($declarations->currentPage() - 1); ?> 
@forelse ($declarations as $eachData)
<?php $n++; ?>
<tr>
    <td class="log_slno">{{ $n }}</td>
    <td class="log_date"><?php echo date("d-m-Y H:i", strtotime($eachData->created_at)); ?></td>
    <td class="p_searchbycustomername">{{$eachData->cus_name}}</td>
    <td class="mobile_number">{{$eachData->mobile_number}}</td>
    <td class="cus_repeat">{{$eachData->repeat_count}}</td>
    <td class="cus_branch">{{$eachData->branch_name}}</td>
    <td class="p_createdby">{{$eachData->cashier_name}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('crm/all_customers/view', ['id' => Crypt::encrypt($eachData->mobile_number),'branch'=>Crypt::encrypt($eachData->branch_id),'created_by'=> Crypt::encrypt($eachData->created_by)]) }}">View</a>
        </div>
    </td>
</tr>
@empty
<tr>
<td colspan="5">
    No Data
</td>
</tr>
@endforelse
<?php if ($declarations->lastPage() > 1) { ?>
    <tr class="paginationHolder"><th><div>   {!! $declarations->render() !!}</div> </th></tr>
<?php } ?>