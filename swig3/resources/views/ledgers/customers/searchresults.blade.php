<?php $n = $customers->perPage() * ($customers->currentPage()-1); ?>   
    @foreach ($customers as $customer)
    
    <?php $n++; 
    $customer_name = $customer->first_name." ".$customer->alias_name;
    ?>

    <tr>
        <td class="sl_no">{{$n}}</td>
        <td class="customer_code">{{$customer->code}}</td>
        <td class="customer_name"><?php echo $customer_name;?></td>
        <td class="customer_country">{{$customer->country_name}}</td> 
        <td class="customer_email">{{$customer->email}}</td>
        <td class="customer_phno">{{$customer->mobile_number}}</td>                        

        <?php if($customer->status== 1){
            $status="Enabled";
        }else{
            $status="Disabled";
        }?>
         <td class="customer_status">{{$status}}</td>

    </tr>
    @endforeach

    <?php if(count($customers) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $customers->render() !!} </div> </th></tr>
    <?php } ?>