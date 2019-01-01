<?php  $n = $branches->perPage() * ($branches->currentPage() - 1); ?> 
    @foreach ($branches as $branche)
    <?php  $n++; 
   
    
    ?>
    <tr>
        <td class="new_code">{{$branche->branch_code}}</td>
        <td class="new_name">{{$branche->name}}</td>
        <td class="new_date"><?php echo date("d-m-Y", strtotime($branche->branch_start_date));?></td>
        <td class="new_area">{{$branche->area}}</td>
        <td class="new_region">{{$branche->region}}</td>
        <td class="new_alias">{{$branche->alias_name}}</td>
        <td class="new_address">{{$branche->address}}</td>
        
    </tr>
    @endforeach
    <?php if($branches->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $branches->render() !!}</div> </th></tr>
    <?php } ?>
    
    