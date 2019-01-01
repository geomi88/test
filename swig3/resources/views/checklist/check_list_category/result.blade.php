<?php  $n = $checklist_categories->perPage() * ($checklist_categories->currentPage() - 1); ?> 
    @foreach ($checklist_categories as $category)
    @if($category->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="sl_no">{{ $n }}</td>
        <td class="cat_name">{{$category->name}}</td>
        <td class="alias_name">{{$category->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/check_list_category/edit', ['id' => Crypt::encrypt($category->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/check_list_category/'.$status, ['id' => Crypt::encrypt($category->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/check_list_category/delete', ['id' => Crypt::encrypt($category->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php // if($checklist_categories->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $checklist_categories->render() !!}</div> </th></tr>
    <?php // } ?>
    
    