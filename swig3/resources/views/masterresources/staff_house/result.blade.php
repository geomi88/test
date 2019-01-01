<?php  $n = $staffhouse->perPage() * ($staffhouse->currentPage() - 1); ?> 
    @foreach ($staffhouse as $staffhouses)
    
    <?php  $n++; ?>
    <tr>
        <td>{{ $n }}</td>
        <td>{{$staffhouses->name}}</td>
      
        <td>{{$staffhouses->region_name}}</td>
        <td>{{$staffhouses->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/staff_house/edit', ['id' => Crypt::encrypt($staffhouses->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/staff_house/delete', ['id' => Crypt::encrypt($staffhouses->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    
    <tr class="paginationHolder"><th><div>   {!! $staffhouse->render() !!}</div> </th></tr>
    
