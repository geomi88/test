<?php  $n = $suggestions->perPage() * ($suggestions->currentPage() - 1); ?> 
    @foreach ($suggestions as $suggestion)
    <?php
    $n++;
    
    if($suggestion->status==2){
        $status="Noted";
    }else{
        $status="New";
    }
    
    ?>
    <tr>
        <td class="suggestion_no">{{ $n }}</td>
        <td class="suggestion_title">{{$suggestion->title}}</td>
        <td class="suggestion_to">{{$suggestion->submitted_to}}</td>
        <td class="suggestion_status"><?php echo $status;?></td>
        <td class="suggestion_date"><?php echo date("d-m-Y", strtotime($suggestion->created_at));?></td>
        <td class="suggestion_description">{{$suggestion->description}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('dashboard/suggestion/edit', ['id' => Crypt::encrypt($suggestion->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('dashboard/suggestion/delete', ['id' => Crypt::encrypt($suggestion->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($suggestions->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $suggestions->render() !!}</div> </th></tr>
    <?php } ?>
