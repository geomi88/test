<?php $n = $suppliers->perPage() * ($suppliers->currentPage()-1); ?>   
    @foreach ($suppliers as $supplier)
    
    <?php $n++; 
    $supplier_name = $supplier->first_name." ".$supplier->alias_name;
    ?>

    <tr>
        <td class="sl_no">{{$n}}</td>
        <td class="supplier_code">{{$supplier->code}}</td>
        <td class="supplier_name"><?php echo $supplier_name;?></td>
        <td class="country">{{$supplier->country_name}}</td> 
        <td class="mailid">{{$supplier->email}}</td>
        <td class="phno">{{$supplier->mobile_number}}</td>                        

        <?php if($supplier->status== 1){
            $status="Enabled";
        }else{
            $status="Disabled";
        }?>
         <td class="status">{{$status}}</td>
         <td ><a class="btnAction edit bgBlue documentfile" value href="suppliers/view/{{\Crypt::encrypt($supplier->id)}}">View</a></td>
    </tr>
    @endforeach

    <?php if(count($suppliers) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $suppliers->render() !!} </div> </th></tr>
    <?php } ?>