<?php $n = $elegantcustomers->perPage() * ($elegantcustomers->currentPage()-1); ?>   
    @foreach ($elegantcustomers as $customer)
    @if($customer->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
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
        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
                <div class="actionBtnHolderV1">
                <a class="btnAction edit bgLightRed" href="{{ URL::to('elegantclub/edit_customer', ['id' => Crypt::encrypt($customer->id)]) }}">Edit</a>
                <a class="btnAction disable bgOrange"  href="{{ URL::to('elegantclub/customer/'.$status, ['id' => Crypt::encrypt($customer->id)]) }}"><?php echo $status; ?></a>
                </div>
            </div>
        </td>

    </tr>
    @endforeach

    <?php if(count($elegantcustomers) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $elegantcustomers->render() !!} </div> </th></tr>
    <?php } ?>