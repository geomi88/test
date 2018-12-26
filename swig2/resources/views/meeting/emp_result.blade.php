<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
    @foreach ($employees as $employee)
    <?php $n++; 
    $employee_name = $employee->first_name." ".$employee->alias_name;
    $job_position = str_replace('_',' ',$employee->job_position_name);
    ?>

    <tr>
       
        <td class="checkboxSet">
            <input type="checkbox" id="{{$employee->id}}" <?php if(in_array($employee->id, $arrid)){echo "checked";}?> class="chkemployee">
            <input type="hidden" id="name_{{$employee->id}}" value="<?php echo $employee_name;?>">
            <input type="hidden" id="profilepic_{{$employee->id}}" value="{{$employee->profilepic}}">
            <input type="hidden" id="code_{{$employee->id}}" value="{{$employee->username}}">
            <input type="hidden" id="position_{{$employee->id}}" value="<?php echo $job_position;?>">
        </td>
        <td>{{$employee->username}}</td>
        <td><?php echo $employee_name;?></td>
        <td><?php echo $job_position;?></td>
        
        
    </tr>
    @endforeach
    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                   