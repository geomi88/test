<?php $n = $accounts->perPage() * ($accounts->currentPage() - 1); ?>  
                       
                            @foreach($accounts as $account)
                            <?php
                            $readonly = '';
                            $quarter = '';
                            $Year = date('Y');
                            
                            if ($year < $Year) {
                                $readonly = 'readonly';
                            }
                            ?>
                            <tr> 
                                <?php if($account->code) {?>
                                    <td>{{$account->code.' : '.$account->first_name.' '.$account->last_name}}</td>  
                                <?php } else {?>
                                    <td>{{$account->first_name.' '.$account->last_name}}</td>  
                                <?php } ?>
                                
                                
                                    <?php if($type=='Inventory'){ 
                                        $quantity='';
                                        if($account->quantity!=0){
                                            $quantity=$account->quantity;
                                        }
                                        ?>
                                        <td>{{$account->primunit}}</td>  
                                        <td class="supplier_amount">
                                            <input type="text" id="quan_{{$account->type_id}}" value="{{$quantity}}"  class="numberwithdot budget_quantity" <?php echo $readonly; ?>>
                                        </td>
                                   <?php } ?>
                                <td class="supplier_amount">
                                    <?php
                                    $price = '';
                                    if($account->price!=0){ $price = $account->price; }
                                    ?>
                                    <input type="text" id="{{$account->type_id}}" value="{{$price}}" rel="{{$account->budget_id}}" class="numberwithdot budget_amount" <?php echo $readonly; ?>> 
                                </td>
                            </tr>
                            @endforeach
                        
                    <?php if(count($accounts) > 0){ ?>
    <tr class="paginationHolder"><th><div>   {!! $accounts->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                     
                            
                        
            