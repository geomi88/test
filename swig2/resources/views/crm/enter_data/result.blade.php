<?php $n = $crm_customers->perPage() * ($crm_customers->currentPage() - 1); ?> 
@forelse ($crm_customers as $eachData)
<?php $n++; ?>
    <tr>
        <td>{{ $n }}</td>
        <td class="inve_code">{{$eachData->cus_name}}</td>
        <td class="inve_name">{{$eachData->mobile_number}}</td>
        <td class="inve_name">{{$eachData->repeat_count}}</td>
    </tr>
@empty
<tr>
<td colspan="5">
    No Data
</td>
</tr>
@endforelse
<?php if ($crm_customers->lastPage() > 1) { ?>
    <tr class="paginationHolder"><th><div>   {!! $crm_customers->render() !!}</div> </th></tr>
<?php } ?>