<?php  $n = $meeting_rooms->perPage() * ($meeting_rooms->currentPage() - 1); ?> 
    @foreach ($meeting_rooms as $meeting_room)
    
    <?php  $n++; ?>
    <tr>
        <td class="meeting_slno">{{ $n }}</td>
        <td class="meeting_room">{{$meeting_room->name}}</td>
        <td class="meeting_alias">{{$meeting_room->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/meeting_room/edit', ['id' => Crypt::encrypt($meeting_room->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/meeting_room/delete', ['id' => Crypt::encrypt($meeting_room->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if(count($meeting_rooms) > 0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $meeting_rooms->render() !!}</div> </th></tr>
    <?php } ?>
    
    