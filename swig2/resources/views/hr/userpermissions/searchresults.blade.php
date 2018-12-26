<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
    @foreach ($employees as $employee)
    <?php $n++; 
    $employee_name = $employee->first_name." ".$employee->alias_name;
    ?>

    <tr>
         <td>{{$employee->username}}</td>
        <td><?php echo $employee_name;?></td>
        <td><?php echo str_replace('_',' ',$employee->job_position_name);?></td>
       
        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgGreen" href="{{ URL::to('hr/userpermissions/showdetails', ['id' => Crypt::encrypt($employee->id)]) }}">Permissions</a>
            </div>
        </td>
    </tr>
    @endforeach
     <?php if($employees->lastPage() > 1){ ?>
    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
     <?php }?>          