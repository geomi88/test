<?php  $n = $inventory_categories->perPage() * ($inventory_categories->currentPage() - 1); ?> 
    @foreach ($inventory_categories as $inventory_category)
    @if($inventory_category->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="inventory_slno">{{ $n }}</td>
        <td class="inventory_category">{{$inventory_category->name}}</td>
        <td class="inventory_main">{{$inventory_category->parent_name}}</td>
        <td class="inventory_alias">{{$inventory_category->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/inventory_category/edit', ['id' => Crypt::encrypt($inventory_category->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/inventory_category/'.$status, ['id' => Crypt::encrypt($inventory_category->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/inventory_category/delete', ['id' => Crypt::encrypt($inventory_category->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if($inventory_categories->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $inventory_categories->render() !!}</div> </th></tr>
    <?php } ?>
    
    