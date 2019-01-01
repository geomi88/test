<?php  $n = $costnames->perPage() * ($costnames->currentPage() - 1); ?> 
    @foreach ($costnames as $cost)
    
    <?php  $n++; ?>
    <tr>
        <td class="category_slno">{{ $n }}</td>
        <td class="category_name">{{$cost->name}}</td>
        <td class="category_alias">{{$cost->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/costname/edit', ['id' => Crypt::encrypt($cost->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/costname/delete', ['id' => Crypt::encrypt($cost->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if(count($costnames) > 0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $costnames->render() !!}</div> </th></tr>
    <?php } ?>
    
    