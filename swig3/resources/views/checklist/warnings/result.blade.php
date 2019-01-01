<?php  $n = $warnings->perPage() * ($warnings->currentPage() - 1); ?> 
    @foreach ($warnings as $warning)
    
    
    <tr>
        
        <td class="check_title">{{$warning->title}}</td>
        <td class="check_name">{{$warning->first_name}}</td>
        <td class="check_warning">{{$warning->warning_name}}</td>
        <td class="check_branch">{{$warning->br_code}} : {{$warning->br_name}}</td>
        <td class="check_description">{{$warning->description}}</td>
        
        
    </tr>
    @endforeach
    <?php if(count($warnings)>0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $warnings->render() !!}</div> </th></tr>
    <?php } ?>
    
    