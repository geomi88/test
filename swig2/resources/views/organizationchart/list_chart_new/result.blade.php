<?php  $n = $charts->perPage() * ($charts->currentPage() - 1); ?> 
    @foreach ($charts as $chart)
    
    <?php  $n++; ?>
    <tr>
        <td>{{ $n }}</td>
        <td>{{$chart->name}}</td>
        <td>{{$chart->category}}</td>
        <td>{{$chart->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet linkviewtodo">
                <a class="btnAction action bgBlue" href="{{ URL::to('organizationchart/organizationchartnew/viewchart', ['id' => Crypt::encrypt($chart->id)]) }}">View Chart</a>
            </div>
        </td>
    </tr>
    @endforeach
    <?php // if($charts->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $charts->render() !!}</div> </th></tr>
    <?php // } ?>
    
    