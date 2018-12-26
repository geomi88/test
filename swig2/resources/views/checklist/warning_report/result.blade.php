<?php  $n = $warnings->perPage() * ($warnings->currentPage() - 1); ?> 
    @foreach ($warnings as $warning)
    <tr>
        <td class="war_code">{{$warning->emp_code}}</td>
        <td class="war_name">{{$warning->first_name}}</td>
        <td class="war_nation">{{$warning->nationality}}</td>
        <td class="war_job"><?php echo str_replace("_", " ", $warning->job_position);?></td>
        <td class="war_type">{{$warning->warning_name}}</td>
        <td class="war_title">{{$warning->title}}</td>
        <td class="war_date"><?php echo date("d-m-Y", strtotime($warning->created_at));?></td>
        <td class="war_branch">{{$warning->br_code}} : {{$warning->br_name}}</td>
        <td class="war_by">{{$warning->reportedby}}</td>
        <td class="war_description">{{$warning->description}}</td>
    </tr>
    @endforeach
    <?php if(count($warnings) > 0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $warnings->render() !!}</div> </th></tr>
    <?php } ?>
    
    