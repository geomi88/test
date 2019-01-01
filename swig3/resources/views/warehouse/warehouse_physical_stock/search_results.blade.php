<input type="hidden" id="branch_id" value="{{$warehouseId}}">
 <input type="hidden" id="subId" value="{{$savedStockId}}">
 
   <?php  $n = 0;?> 
                        @foreach ($products as $product)
                        <?php
                        if(key_exists($product->id, $stockSavedData)){
                            $txtvalue = $stockSavedData[$product->id];
                        }else if(key_exists($product->id, $stockPreviousData)){
                            $txtvalue = $stockPreviousData[$product->id];
                        }else{
                            $txtvalue = "";
                        }
                        
                        if($txtvalue==0){
                            $txtvalue = "";
                        }
                        
                        $n++;
                        ?>
                        <tr>
                            <td>{{ $n }}</td>
                            <td>{{$product->product_code}}</td>
                            <td>{{$product->name}}</td>
                            <td><input type="text" id="{{$product->id}}" class="clsproductqty number" usr-attr="<?php echo $txtvalue;?>" maxlength="15" usr-max="{{$product->max_branch_stock}}" usr-pcode="{{$product->product_code}}" style="width: 170px;" value="<?php echo $txtvalue;?>"></td>
                            <td>{{$product->max_branch_stock}}</td>
                        </tr>
                        @endforeach
                       