<?php  $n = $banks->perPage() * ($banks->currentPage() - 1); ?> 
    @foreach ($banks as $bank)
    @if($bank->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="bank_slno">{{ $n }}</td>
        <td class="bank_name">{{$bank->name}}</td>
        <td class="bank_alias">{{$bank->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/bank/edit', ['id' => Crypt::encrypt($bank->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/bank/'.$status, ['id' => Crypt::encrypt($bank->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/bank/delete', ['id' => Crypt::encrypt($bank->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($banks->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $banks->render() !!}</div> </th></tr>
    <?php } ?>

