<?php $n = $elegantcustomers->perPage() * ($elegantcustomers->currentPage()-1); ?>   
    @foreach ($elegantcustomers as $customer)
    
    <?php $n++; 
    $customer_name = $customer->name_english." ".$customer->name_arabic;
    ?>

    <tr>
        <td class="sl_no">{{$n}}</td>
        <td class="created_by">{{$customer->empCode}} {{$customer->empName}}</td>
        <td class="supplier_code">{{$customer->customer_code}}</td>
        <td class="supplier_name"><?php echo $customer_name;?></td>
        <td class="country">{{$customer->city}}</td> 
        <td class="mailid">{{$customer->email_1}}</td>
        <td class="phno">{{$customer->mobile_1}}</td>                        

        <?php if($customer->status== 1){
            $status="Enabled";
        }else{
            $status="Disabled";
        }?>
         <td class="status">{{$status}}</td>
         <td ><a class="btnAction edit bgBlue documentfile" value href="customer/view/{{\Crypt::encrypt($customer->id)}}">View</a></td>

    </tr>
    @endforeach

    <?php if(count($elegantcustomers) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $elegantcustomers->render() !!} </div> </th></tr>
    <?php } ?>