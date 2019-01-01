<?php if(count($accounts)>0) {?>
<?php  $n = $accounts->perPage() * ($accounts->currentPage() - 1);?>
        
<?php $total = 0; 
$used = 0;
$balance = 0;
?>
@foreach ($accounts as $account) <?php $n++; ?>
<?php
 $usedPercent = round((($account->used*100)/$account->price_budget),2);
 $balancePercent = round(((($account->price_budget-$account->used)*100)/$account->price_budget),2);
 ?>
 <tr>
     <td class="sl_no">{{$n}}</td>
     <td class="ledger_code">{{$account->code}}</td>
     <td class="ledger_name">{{$account->first_name}}</td>
     <td class="total_amount" align="right"><?php echo Customhelper::numberformatter($account->price_budget); ?></td>
     <td class="used_amount" align="right"><?php echo Customhelper::numberformatter($account->used); ?></td>
     <td class="used_percent"  align="right"><?php echo $usedPercent.' %'; ?></td>
     <td class="budget_variance" align="right"><?php echo Customhelper::numberformatter($account->price_budget-$account->used); ?></td>
     <td class="variance_percent"  align="right"><?php echo $balancePercent.' %'; ?></td>

     <?php $total = $total + $account->price_budget; 
     $used = $used + $account->used;
     $balance = $balance + ($account->price_budget-$account->used);
     ?>

 </tr>
 
 @endforeach
 
 <tr style="font-weight:bold;">
     <td></td>
     <td></td>
     <td>Total</td>
     <td align="right"><?php echo Customhelper::numberformatter($total); ?></td>
     <td align="right"><?php echo Customhelper::numberformatter($used); ?></td>
     <td></td>
     <td align="right"><?php echo Customhelper::numberformatter($balance); ?></td>
 </tr>
 
 
 
<?php if($accounts->lastPage() > 1){ ?>
<tr class="paginationHolder"><th><div>   {!! $accounts->render() !!}</div> </th></tr>
<?php } ?> 
<?php } ?>