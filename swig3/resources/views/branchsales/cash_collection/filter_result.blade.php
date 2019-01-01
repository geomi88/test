@if(count($sale_details_data) > 0)
<?php $total_cash = 0;?>
@foreach ($sale_details_data as $sale_details)
<?php $total_cash = $total_cash+$sale_details->cash_collection;
$edited_by = '';
if($sale_details->edited_code != ''){
    $edited_by = $sale_details->edited_code.' : '.$sale_details->edited_fname;
}
?>
<tr>
    <td class="coll_stat"><?php if($sale_details->collection_status==1){ echo "Collected";}else{?><input type="checkbox" id="{{$sale_details->id}}" class="chkpossales"><?php }?></td>
   
    <td class="date_pos"><?php echo date("d-m-Y", strtotime($sale_details->pos_date));?></td>
    
    <td class="job_shift">{{$sale_details->jobshift_name}}</td>
    <td class="branch_nme">{{$sale_details->branch_name}}</td>
    <td class="opening_amt">{{$sale_details->opening_amount}}</td>
    <td class="tot_sale">{{$sale_details->total_sale}}</td>
    <td class="sale_amt">{{$sale_details->total_sale-$sale_details->tax_in_mis}}</td>
    <td class="tax_amt">{{$sale_details->tax_in_mis}}</td>
    <td class="tot_cash_sale">{{$sale_details->cash_sale}}</td>
    <td class="cash_coll">{{$sale_details->cash_collection}}</td>
    <td class="cash_diff">{{$sale_details->cash_sale-$sale_details->cash_collection}}</td>
    <td class="tot_bank_slae">{{$sale_details->bank_sale}}</td>
    <td class="bank_coll">{{$sale_details->bank_collection}}</td>
    <td class="bank_diff">{{$sale_details->bank_sale-$sale_details->bank_collection}}</td>
    <td class="credit_sale">{{$sale_details->credit_sale}}</td>
    <td class="net_diff">{{$sale_details->difference}}</td>
    <td class="meal_cons">{{$sale_details->meal_consumption}}</td>
    <td class="cashier_name">{{$sale_details->cashier_code}} : {{$sale_details->cashier_fname}}</td>
    <td class="edited_by">{{$edited_by}}</td>
    <td class="reason_c">{{$sale_details->reason}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/collection_details/show', ['id' => Crypt::encrypt($sale_details->id)]) }}">View</a>
        </div>
    </td>
</tr>
@endforeach

<script>
    $("#total").html("Total : "+{{$total_cash}})
    $("#printTotal").html({{$total_cash}})
</script>
@endif