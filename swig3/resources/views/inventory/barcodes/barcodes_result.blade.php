<?php  $n = $barcodes->perPage() * ($barcodes->currentPage() - 1); ?> 
    @foreach ($barcodes as $barcode)
    
    <?php  $n++; ?>
    <tr>
        <td class="avail_no">{{ $n }}</td>
        <td class="avail_date"><?php echo date("d-m-Y", strtotime($barcode->created_at));?></td>
        <td class="avail_code">{{$barcode->barcode_string}}</td>
        <td class="avail_by">{{$barcode->created_by_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('inventory/barcode/edit', ['id' => Crypt::encrypt($barcode->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('inventory/barcode/delete', ['id' => Crypt::encrypt($barcode->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php // if($barcodes->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $barcodes->render() !!}</div> </th></tr>
    <?php // } ?>
