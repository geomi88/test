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
        <td class="emp_name"><?php echo $employee_name;?></td>
        <td class="emp_job"><?php echo $job_position;?></td>
        <td class="emp_code">{{$employee->username}}</td>
        <td class="emp_new">{{$employee->new_count}}</td>
        <td class="emp_pending">{{$employee->pending_count}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet linkviewtodo">
                <a class="btnAction action bgBlue" id="refer_{{$employee->id}}" href="{{ URL::to('dashboard/assign_task/single_employee', ['id' => Crypt::encrypt($employee->id)]) }}">Assign Task</a>
            </div>
        </td>
    </tr>
    @endforeach
    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                   