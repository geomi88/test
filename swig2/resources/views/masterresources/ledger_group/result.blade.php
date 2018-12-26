<?php  $n = $ledgers->perPage() * ($ledgers->currentPage() - 1); ?> 
    @foreach ($ledgers as $ledger)
    @if($ledger->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++;?>
    <tr>
        <td class="ledger_slno">{{ $n }}</td>
        <td class="ledger_name">{{$ledger->name}}</td>
        <td class="ledger_parent">{{$ledger->parentname}}</td>
        <td class="ledger_type">{{$ledger->type}}</td>
        <td class="ledger_alias">{{$ledger->alias_name}}</td>
      
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/ledger_group/edit', ['id' => Crypt::encrypt($ledger->id)]) }}">Edit</a>
		<!--<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/ledger_group/'.$status, ['id' => Crypt::encrypt($ledger->id)]) }}"><?php echo $status; ?></a>-->
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/ledger_group/delete', ['id' => Crypt::encrypt($ledger->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php // if($ledgers->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $ledgers->render() !!}</div> </th></tr>
    <?php // } ?>
