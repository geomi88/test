<?php  $n = $chart_categories->perPage() * ($chart_categories->currentPage() - 1); ?> 
    @foreach ($chart_categories as $category)
    
    <?php  $n++; ?>
    <tr>
        <td class="category_slno">{{ $n }}</td>
        <td class="category_name">{{$category->name}}</td>
        <td class="category_alias">{{$category->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/chartcategory/edit', ['id' => Crypt::encrypt($category->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/chartcategory/delete', ['id' => Crypt::encrypt($category->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if(count($chart_categories) > 0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $chart_categories->render() !!}</div> </th></tr>
    <?php } ?>
    
    