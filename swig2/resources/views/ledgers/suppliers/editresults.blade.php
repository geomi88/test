<?php $n = $suppliers->perPage() * ($suppliers->currentPage()-1); ?>   
    @foreach ($suppliers as $supplier)
    @if($supplier->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php $n++; 
    $supplier_name = $supplier->first_name." ".$supplier->alias_name;
    ?>

    <tr>
        <td class="sl_no">{{$n}}</td>
        <td class="supplier_code">{{$supplier->code}}</td>
        <td class="supplier_name"><?php echo $supplier_name;?></td>
        <td class="country_name">{{$supplier->country_name}}</td> 
        <td class="supplier_email">{{$supplier->email}}</td>
        <td class="supplier_phno">{{$supplier->mobile_number}}</td>                        

        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
                <div class="actionBtnHolderV1">
                <a class="btnAction edit bgBlue" href="{{ URL::to('suppliers/edit', ['id' => Crypt::encrypt($supplier->id)]) }}">Edit</a>
                <a class="btnAction disable bgOrange"  href="{{ URL::to('suppliers/'.$status, ['id' => Crypt::encrypt($supplier->id)]) }}"><?php echo $status; ?></a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('suppliers/delete', ['id' => Crypt::encrypt($supplier->id)]) }}">Delete</a>
                </div>
            </div>
        </td>

    </tr>
    @endforeach

    <?php if(count($suppliers) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $suppliers->render() !!} </div> </th></tr>
    <?php } ?>