<?php  $n = $ledgers->perPage() * ($ledgers->currentPage() - 1); ?> 
    @foreach ($ledgers as $ledger)
    @if($ledger->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++;?>
    <tr>
        <td>{{ $n }}</td>
        <td>{{$ledger->code}}</td>
        <td>{{$ledger->name}}</td>
        <td>{{$ledger->groupname}}</td>

        <td class="right"><?php echo Customhelper::numberformatter($ledger->opening_balance); ?></td>
        <td>{{$ledger->alias_name}}</td>
      
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('ledgers/general_ledgers/edit', ['id' => Crypt::encrypt($ledger->id)]) }}">Edit</a>
                <a class="btnAction disable bgOrange"  href="{{ URL::to('ledgers/general_ledgers/'.$status, ['id' => Crypt::encrypt($ledger->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('ledgers/general_ledgers/delete', ['id' => Crypt::encrypt($ledger->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php // if($ledgers->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $ledgers->render() !!}</div> </th></tr>
    <?php // } ?>
