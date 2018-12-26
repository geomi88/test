<?php  $n = $charts->perPage() * ($charts->currentPage() - 1); ?> 
    @foreach ($charts as $chart)
    
    <?php  $n++; ?>
    <tr>
        <td>{{ $n }}</td>
        <td>{{$chart->name}}</td>
        <td>{{$chart->category}}</td>
        <td><?php echo str_replace("_", " ", $chart->based_on)?></td>
        <td>{{$chart->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet linkviewtodo">
                <a class="btnAction action bgBlue" href="{{ URL::to('organizationchart/organizationchart/edit', ['id' => Crypt::encrypt($chart->id)]) }}">Edit Chart</a>
            </div>
        </td>
    </tr>
    @endforeach
    <?php // if($charts->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $charts->render() !!}</div> </th></tr>
    <?php // } ?>
    
    