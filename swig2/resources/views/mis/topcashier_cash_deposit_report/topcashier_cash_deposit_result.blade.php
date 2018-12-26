<?php $n = $cash_collections->perPage() * ($cash_collections->currentPage() - 1); ?> 
@foreach ($cash_collections as $cash_collection)
<?php $n++; ?>
<tr>
    <td class="cash_collected_date"> <?php echo date("d-m-Y", strtotime($cash_collection->updated_at)); ?></td>
    <td class="cash_deposited_by">{{$cash_collection->deposited_by}}</td>
    <td class="cash_amount">{{Customhelper::numberformatter($cash_collection->amount)}}</td>
    <td class="cash_bankName">{{$cash_collection->bank_name}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('mis/topcashier_cash_deposit_report/showdepositdetails', ['id' => Crypt::encrypt($cash_collection->pos_ids)]) }}">View</a>
        </div>
    </td>
</tr>
@endforeach
 <?php if($cash_collections->lastPage() > 1){ ?>
<tr class="paginationHolder"><th><div>   {!! $cash_collections->render() !!}</div> </th></tr>
 <?php } ?>