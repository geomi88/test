<?php  $n = $sales->perPage() * ($sales->currentPage() - 1);

$netSale=0;
$totaltax=0;
$saleAmount=0;
$cash_percent=0;
$tax_percent=0;
?> 
                   
                   @foreach ($sales as $sale)
                   <?php 
                  
                   
                     $saleAmount=$sale->total_sale;
                   if($sale->total_sale==""){
                       $saleAmount=0;
                   } 
                   $cash_percent=Customhelper::calculate_vat($taxpercent,$saleAmount);
                 
                   
                  
                   $tax_percent=$saleAmount-$cash_percent;
                       $netSale+=$cash_percent;
                       $totaltax+=$tax_percent;  
                   ?>
                            <tr>
                                <td>{{$sale->code}}</td>
                                <td>{{$sale->branch_name}}</td>
                                <td><?php echo Customhelper::numberformatter($saleAmount);?></td>
                                <td><?php echo Customhelper::numberformatter($cash_percent);?></td>
                                <td><?php echo Customhelper::numberformatter($tax_percent);?></td>
                             
                            </tr>
                            @endforeach
                            <tr>
                                <td><b>Total</b></td>
                                <td></td>
                                <td><b><?php echo Customhelper::numberformatter($sum_total_sales);?></b></td>
                               <td><b><?php echo Customhelper::numberformatter($netSale);?></b></td>
                              <td><b><?php echo Customhelper::numberformatter($totaltax);?></b></td>
                              
                            </tr>  
                <?php if($sales->lastPage() > 1){ ?>
                                         
        <tr class="paginationHolder"><th><div>   {!! $sales->render() !!}</div> </th></tr>
      <?php } ?> 