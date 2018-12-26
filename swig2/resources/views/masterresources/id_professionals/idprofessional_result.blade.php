<?php  $n = $idprof->perPage() * ($idprof->currentPage() - 1); ?> 
    @foreach ($idprof as $idprofs)
    @if($idprofs->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td>{{ $n }}</td>
        <td><?php echo str_replace('_',' ',$idprofs->name);?></td>
      
        <td>{{$idprofs->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/id_professionals/edit', ['id' => Crypt::encrypt($idprofs->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/id_professionals/'.$status, ['id' => Crypt::encrypt($idprofs->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/id_professionals/delete', ['id' => Crypt::encrypt($idprofs->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($idprof->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $idprof->render() !!}</div> </th></tr>
    <?php } ?>
