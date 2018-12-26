<?php  $n = $inventories->perPage() * ($inventories->currentPage() - 1); ?> 
    @foreach ($inventories as $inventorie)
    @if($inventorie->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  
        $n++; 

        $arrAltUnits=explode(",", $inventorie->altunits);
        
    ?>
    <tr>
        <td class="inve_code">{{$inventorie->product_code}}</td>
        <td class="sup_code">{{$inventorie->supplier_icode}}</td>
        <td class="inve_name">{{$inventorie->name}}</td>
        <td class="inve_group">{{$inventorie->grp_name}}</td>
        <td class="inve_cat">{{$inventorie->category_name}}</td>
        <td class="primary">{{$inventorie->primearyunit}}</td>
        
        <?php if(isset($arrAltUnits[0])){?>
            <td class="altunit1">{{$arrAltUnits[0]}}</td>
        <?php } else { ?>
            <td class="altunit1"></td>
        <?php } ?>
            
        <?php if(isset($arrAltUnits[1])){?>
            <td class="altunit2">{{$arrAltUnits[1]}}</td>
        <?php } else { ?>
            <td class="altunit2"></td>
        <?php } ?>
            
        <?php if(isset($arrAltUnits[2])){?>
            <td class="altunit3">{{$arrAltUnits[2]}}</td>
        <?php } else { ?>
            <td class="altunit3"></td>
        <?php } ?>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
		<div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('inventory/inventory_items/edit', ['id' => Crypt::encrypt($inventorie->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('inventory/inventory_items/'.$status, ['id' => Crypt::encrypt($inventorie->id)]) }}"><?php echo $status; ?></a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('inventory/inventory_items/delete', ['id' => Crypt::encrypt($inventorie->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if($inventories->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $inventories->render() !!}</div> </th></tr>
    <?php } ?>