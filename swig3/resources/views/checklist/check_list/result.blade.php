<?php  $n = $checklist->perPage() * ($checklist->currentPage() - 1); ?> 
    @foreach ($checklist as $list)
    @if($list->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    
    <?php
        
        if($list->all_day==1){
            $daystring="All Days";
        }else{
            $daystring=$list->daystring;
            $daystring=str_replace("1", "Sun", $daystring);
            $daystring=str_replace("2", "Mon", $daystring);
            $daystring=str_replace("3", "Tue", $daystring);
            $daystring=str_replace("4", "Wed", $daystring);
            $daystring=str_replace("5", "Thu", $daystring);
            $daystring=str_replace("6", "Fri", $daystring);
            $daystring=str_replace("7", "Sat", $daystring);
        }
    ?>
    <tr>
        
        <td class="check_point">{{$list->checkpoint}}</td>
        <td class="check_name">{{$list->categoryname}}</td>
        <td class="check_pos">{{$list->jobposition}}</td>
        <td class="check_days">{{$daystring}}</td>
        <td class="check_alias">{{$list->alias}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
		<div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('checklist/check_list/edit', ['id' => Crypt::encrypt($list->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('checklist/check_list/'.$status, ['id' => Crypt::encrypt($list->id)]) }}"><?php echo $status; ?></a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('checklist/check_list/delete', ['id' => Crypt::encrypt($list->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php // if($checklist->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $checklist->render() !!}</div> </th></tr>
    <?php // } ?>