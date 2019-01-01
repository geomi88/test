<?php
$n = $sales->perPage() * ($sales->currentPage() - 1); ?> 
    @foreach ($sales as $salesData)
    <?php  $n++; 
   
    
    ?>
    <tr>
        <td class="sales_username">{{$salesData->username}}</td>
        <td class="sales_fname">{{$salesData->first_name}} {{$salesData->alias_name}}</td>
        <td class="sales_branch_name">{{$salesData->branch_code}}-{{$salesData->branchname}}</td>
        <td class="sales_pos_date"><?php echo date('d-m-Y',strtotime($salesData->pos_date));?></td>
        <?php  if($salesData->total_sale==""){
            $salesData->total_sale=0;
        } ?>
        
        <td class="sales_total_sale"><?php echo Customhelper::numberformatter($salesData->total_sale);?></td>
        
        
<!--        <td class="btnHolder">
            <div class="actionBtnSet linkviewtodo">
                </div>
        </td>-->
    </tr>
    @endforeach
    <?php if($sales->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $sales->render() !!}</div> </th></tr>
    <?php } ?>
    
    