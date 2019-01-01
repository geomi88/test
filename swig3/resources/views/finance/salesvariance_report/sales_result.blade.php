<?php  $n = $sales->perPage() * ($sales->currentPage() - 1);?>
                   
@foreach ($sales as $sale)

<?php
    $salesper=0;
    $varianceper=100;
    if($sale->target_amount!=0){
        $salesper=$sale->quarter_sale/$sale->target_amount*100;
        $varianceper=100-$salesper;
    }

    $salespercent=Customhelper::numberformatter($salesper);
    $variancepercent=Customhelper::numberformatter($varianceper);
?>
 <tr>
     <td class="branch_code">{{$sale->code}}</td>
     <td class="branch_name">{{$sale->branch_name}}</td>
     <td class="quarter_sale"><?php echo Customhelper::numberformatter($sale->quarter_sale); ?></td>
     <td class="quarter_target"><?php echo Customhelper::numberformatter($sale->target_amount); ?></td>
     <td class="sales_per">{{$salespercent}} %</td>
     <td class="varience"><?php echo Customhelper::numberformatter($sale->variance); ?></td>
     <td class="varience_per">{{$variancepercent}} %</td>

 </tr>
 @endforeach
 
 <tr style="font-weight:bold;">
     <td></td>
     <td>Total</td>
     <td><?php echo Customhelper::numberformatter($totQuarterSale); ?></td>
     <td><?php echo Customhelper::numberformatter($totTarget); ?></td>
     <td></td>
     <td><?php echo Customhelper::numberformatter($totVariance); ?></td>
     <td></td>

 </tr>
<?php if($sales->lastPage() > 1){ ?>
<tr class="paginationHolder"><th><div>   {!! $sales->render() !!}</div> </th></tr>
<?php } ?> 