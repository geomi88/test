<?php  $n = $charts->perPage() * ($charts->currentPage() - 1); ?> 
    @foreach ($charts as $chart)
    @if($chart->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td>{{ $n }}</td>
        <td>{{$chart->name}}</td>
        <td>{{$chart->category}}</td>
        <td>{{$chart->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
                    <a class="btnAction edit bgBlue" href="{{ URL::to('organizationchart/organizationchartnew/edit', ['id' => Crypt::encrypt($chart->id)]) }}">Edit</a>
                    <a class="btnAction disable bgOrange"  href="{{ URL::to('organizationchart/organizationchartnew/'.$status, ['id' => Crypt::encrypt($chart->id)]) }}"><?php echo $status; ?></a>
                    <a class="btnAction delete bgLightRed" href="{{ URL::to('organizationchart/organizationchartnew/delete', ['id' => Crypt::encrypt($chart->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
        
    </tr>
    @endforeach
    <?php // if($charts->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $charts->render() !!}</div> </th></tr>
    <?php // } ?>
    
    