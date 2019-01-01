<?php $n = $customers->perPage() * ($customers->currentPage()-1); ?>   
    @foreach ($customers as $customer)
    @if($customer->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
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

        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
                <div class="actionBtnHolderV1">
                <a class="btnAction edit bgBlue" href="{{ URL::to('customers/edit', ['id' => Crypt::encrypt($customer->id)]) }}">Edit</a>
                <a class="btnAction disable bgOrange"  href="{{ URL::to('customers/'.$status, ['id' => Crypt::encrypt($customer->id)]) }}"><?php echo $status; ?></a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('customers/delete', ['id' => Crypt::encrypt($customer->id)]) }}">Delete</a>
                </div>
            </div>
        </td>

    </tr>
    @endforeach

    <?php if(count($customers) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $customers->render() !!} </div> </th></tr>
    <?php } ?>