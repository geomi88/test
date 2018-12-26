<div class="innerContent">
    <header class="pageTitle">
        <h1>POS <span>Sales Report</span></h1>
    </header>	
   
    <div class="fieldGroup" id="fieldSet1">

        <div class="customClear"></div>
    </div>
    <div class="listHolderType1">
        
        <div class="listerType1 reportLister"> 

            <table cellpadding="0" cellspacing="0" border="1" style="width: 100%;">
                <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>
                            Supervisor Name
                        </td>
                        <td>
                            Branch Name
                        </td>
                        <td>
                            Shift Name
                        </td>
                        <td>
                            Total Sale
                        </td>
                        <td>
                            Cash Collection
                        </td>
                        <td>
                            Difference
                        </td>
                        <td>
                            Start Date-End Date
                        </td>
                        
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                    <tr class="headingHolder">
                        <td class="filterFields">
                            
                        </td>
                        <td>
                        </td>
                        <td class="">
                            
                        </td>
                        <td class="filterFields">
                           
                        </td>
                        <td class="filterFields">
                            
                        </td>

                        <td class="filterFields">
                       
                        </td>
                        <td class="filterFields">
                           
                        </td>
                        <td></td>
                    </tr>
                </thead>
                <tbody class="pos" id='pos'>
                 
                    <?php $n = $pos_sales->perPage() * ($pos_sales->currentPage() - 1); ?>   
                    @foreach ($pos_sales as $pos_sale)

                    <tr>
                        <td>{{$pos_sale->employee_name}}</td>
                        <td>{{$pos_sale->branch_name}}</td>
                        <td>{{$pos_sale->jobshift_name}}</td> 
                        <td>{{$pos_sale->total_sale}}</td>
                        <td>{{$pos_sale->cash_collection}}</td>                        
                        <td><?php echo (($pos_sale->total_sale) - ($pos_sale->cash_collection)); ?> </td>
                        <td>{{$pos_sale->pos_date}}</td>
                       
                    </tr>
                    @endforeach
                    <tr class="paginationHolder"><th><div>   {!! $pos_sales->render() !!}</div> </th></tr>
                
                   
                </tbody>

            </table>
            <div class="commonLoaderV1"></div>
        </div>

    </div>
</div>