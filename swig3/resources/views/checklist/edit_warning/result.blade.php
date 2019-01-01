<?php  $n = $warnings->perPage() * ($warnings->currentPage() - 1); ?> 
    @foreach ($warnings as $warning)
    
    
    <tr>
        
        <td class="war_title">{{$warning->title}}</td>
        <td class="war_name">{{$warning->first_name}}</td>
        <td class="war_type">{{$warning->warning_name}}</td>
        <td class="war_branch">{{$warning->br_code}} : {{$warning->br_name}}</td>
        <td class="war_description">{{$warning->description}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('checklist/warnings/edit', ['id' => Crypt::encrypt($warning->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('checklist/warnings/delete', ['id' => Crypt::encrypt($warning->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if(count($warnings)>0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $warnings->render() !!}</div> </th></tr>
    <?php } ?>
    
    