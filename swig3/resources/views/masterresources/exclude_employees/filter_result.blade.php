<?php $total_cash = 0;?>
@foreach ($sale_details_data as $sale_details)
<?php $total_cash = $total_cash+$sale_details->cash_collection;?>
<tr>
    <td><?php if($sale_details->collection_status==1){ echo "Collected";}else{?><input type="checkbox" id="{{$sale_details->id}}" class="chkpossales"><?php }?></td>
   
    <td><?php echo date("d-m-Y", strtotime($sale_details->pos_date));?></td>
    <td>{{$sale_details->branch_name}}</td>
    <td>{{$sale_details->jobshift_name}}</td>
    <td>{{$sale_details->total_sale}}</td>
    <td>{{$sale_details->cash_collection}}</td>
    <td>{{$sale_details->credit_sale}}</td>
    <td>{{$sale_details->bank_sale}}</td>
    <td>{{$sale_details->meal_consumption}}</td>
    <td>{{$sale_details->difference}}</td>
     <td>{{$sale_details->reason}}</td>
</tr>
@endforeach

<script>
    $("#total").html("Total : "+{{$total_cash}})
</script>