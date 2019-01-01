<?php  $n = $checklistentries->perPage() * ($checklistentries->currentPage() - 1); ?> 
    @foreach ($checklistentries as $point)
    <?php  $n++; 
   
    
    ?>
    <tr>
        <td class="check_code">{{$point->username}}</td>
        <td class="check_name">{{$point->first_name}} {{$point->alias_name}}</td>
        <td class="check_job"><?php echo str_replace('_',' ',$point->job_position);?></td>
        <td class="check_category">{{$point->maincategory}}</td>
        <td class="check_point">{{$point->checkpoint}}</td>
        <td class="check_date"><?php echo date("d-m-Y", strtotime($point->entry_date));?></td>
        <td class="check_branch">{{$point->br_code}} : {{$point->br_name}}</td>
        <td class="check_rating">{{$point->rating}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet linkviewtodo">
                <a class="btnAction action bgGreen"  href="{{ URL::to('checklist/checklist_report/getdetails', ['id' => Crypt::encrypt($point->id)]) }}">View</a>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if($checklistentries->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $checklistentries->render() !!}</div> </th></tr>
    <?php } ?>
    
    