<?php $n = $taxlist->perPage() * ($taxlist->currentPage()-1); ?>   
                    @foreach ($taxlist as $taxlists)
                    @if($taxlists->status==0)
                    <?php $status='Enable'; ?>
                    @else
                    <?php $status='Disable'; ?>
                    @endif
                    <?php $n++; 
                     ?>

                    <tr>
                         <td>{{$taxlists->name}}</td> 
                        <td>{{$taxlists->tax_percent}}%</td>
                        
                       
                        <?php 
                        if($taxlists->tax_applicable_from==""){
                            $applicableDate="";
                        }else{
                            $applicableDate= date('d-m-Y',strtotime($taxlists->tax_applicable_from));
                        }
                        
                        ?>
                        <td> <?php echo  $applicableDate;?></td>                        
                       
                       
                        <?php if($taxlists->status== 1){
                            $status="Enable";
                        }else{
                            $status="Disable";
                        }?>
<!--                         <td>{{$status}}</td>-->

                    </tr>
                    @endforeach
                    <tr class="paginationHolder"><th><div>  {!! $taxlist->render() !!} </div> </th></tr>
                   