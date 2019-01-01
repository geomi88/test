<?php  $n = $ledgers->perPage() * ($ledgers->currentPage() - 1); ?> 
    @foreach ($ledgers as $ledger)
    @if($ledger->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="ledger_slno">{{ $n }}</td>
        <td class="ledger_code">{{$ledger->ledger_code}}</td>
        <td class="ledger_name">{{$ledger->name}}</td>
        <td class="ledger_start"><?php echo date("d-m-Y", strtotime($ledger->start_time));?></td>
        <td class="ledger_end"><?php echo date("d-m-Y", strtotime($ledger->end_time));?></td>
        <td class="ledger_amount">{{$ledger->amount}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/ledger/edit', ['id' => Crypt::encrypt($ledger->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/ledger/'.$status, ['id' => Crypt::encrypt($ledger->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/ledger/delete', ['id' => Crypt::encrypt($ledger->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($ledgers->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $ledgers->render() !!}</div> </th></tr>
    <?php } ?>
