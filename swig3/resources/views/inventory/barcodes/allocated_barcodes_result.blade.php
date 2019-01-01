<?php  $n = $barcodes->perPage() * ($barcodes->currentPage() - 1); ?> 
    @foreach ($barcodes as $barcode)
    
    <?php  $n++; ?>
    <tr>
        <td class="alloc_slno">{{ $n }}</td>
        <td class="alloc_date"><?php echo date("d-m-Y", strtotime($barcode->allocated_date));?></td>
        <td class="alloc_bar_num">{{$barcode->barcode_string}}</td>
        <td class="alloc_prod_num">{{$barcode->product_name}}</td>
        <td class="alloc_prod_code">{{$barcode->product_code}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgGreen" href="{{ URL::to('inventory/barcode/release', ['id' => Crypt::encrypt($barcode->inventory_barcode_id)]) }}">Release</a>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($barcodes->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $barcodes->render() !!}</div> </th></tr>
    <?php } ?>
