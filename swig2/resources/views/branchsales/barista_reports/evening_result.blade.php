<?php  $n = $baristas->perPage() * ($baristas->currentPage() - 1); ?> 
    @foreach ($baristas as $point)
    <?php  $n++; ?>
    <tr>
        <td class="eve_code">{{$point->emp_code}}</td>
        <td class="eve_name">{{$point->first_name}} {{$point->alias_name}}</td>
        <td class="eve_branch">{{$point->br_code}} : {{$point->br_name}}</td>
        <td class="eve_region">{{$point->region_name}}</td>
        
     
    </tr>
    @endforeach
    <?php if($baristas->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $baristas->render() !!}</div> </th></tr>
    <?php } ?>
    
    