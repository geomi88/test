<?php  $n = $sales->perPage() * ($sales->currentPage() - 1);?>
                   
@foreach ($sales as $sale)

 <tr>
     <td class="sale_code">{{$sale->code}}</td>
     <td class="sale_branch">{{$sale->branch_name}}</td>
     <td class="sale_q1target"><?php echo Customhelper::numberformatter($sale->q1target); ?></td>
     <td class="sale_q2target"><?php echo Customhelper::numberformatter($sale->q2target); ?></td>
     <td class="sale_q3target"><?php echo Customhelper::numberformatter($sale->q3target); ?></td>
     <td class="sale_q4target"><?php echo Customhelper::numberformatter($sale->q4target); ?></td>
     <td class="sale_total"><?php echo Customhelper::numberformatter($sale->total); ?></td>
     

 </tr>
 @endforeach
 
 <tr style="font-weight:bold;">
     <td></td>
     <td>Total</td>
     <td><?php echo Customhelper::numberformatter($totq1target); ?></td>
     <td><?php echo Customhelper::numberformatter($totq2target); ?></td>
     <td><?php echo Customhelper::numberformatter($totq3target); ?></td>
     <td><?php echo Customhelper::numberformatter($totq4target); ?></td>
     <td><?php echo Customhelper::numberformatter($tottotal); ?></td>
 </tr>
<?php if($sales->lastPage() > 1){ ?>
<tr class="paginationHolder"><th><div>   {!! $sales->render() !!}</div> </th></tr>
<?php } ?> 