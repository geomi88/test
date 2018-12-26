<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
    @foreach ($employees as $employee)
    <?php $n++; 
    $employee_name = $employee->first_name." ".$employee->alias_name;
    ?>

    <tr>
        <td class="emp_name"><?php echo $employee_name;?></td>
        <td class="emp_job"><?php echo str_replace('_',' ',$employee->job_position_name);?></td>
        <td class="emp_code">{{$employee->username}}</td>
        <td class="emp_new">{{$employee->new_count}}</td>
        <td class="emp_pending">{{$employee->pending_count}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet linkviewtodo">
                <a class="btnAction action bgBlue" href="{{ URL::to('tasks/view_todo/gettodo', ['id' => Crypt::encrypt($employee->id)]) }}">View to do</a>
            </div>
        </td>
    </tr>
    @endforeach
    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                   