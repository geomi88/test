<?php  $n = $warning_types->perPage() * ($warning_types->currentPage() - 1); ?> 
    @foreach ($warning_types as $warning_type)
    
    <?php  $n++; ?>
    <tr>
        <td class="warning_slno">{{ $n }}</td>
        <td class="warning_name">{{$warning_type->name}}</td>
        <td class="warning_alias">{{$warning_type->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/warning_type/edit', ['id' => Crypt::encrypt($warning_type->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/warning_type/delete', ['id' => Crypt::encrypt($warning_type->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if(count($warning_types) > 0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $warning_types->render() !!}</div> </th></tr>
    <?php } ?>
    
    