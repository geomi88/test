<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
                    @foreach ($employees as $employee)
                    @if($employee->status==0)
                    <?php $status='Enable'; ?>
                    @else
                    <?php $status='Disable'; ?>
                    @endif
                    <?php $n++; 
                    $employee_name = $employee->first_name." ".$employee->alias_name;
                    ?>

                    <tr> 
                        <td class="alignCenter"><input type="checkbox" class="alltSelected" name="selected_pos[]" value="{{$employee->id}}" id="selected_pos[]">
                        </td>                   
                         <td>{{$employee->username}}</td>
                        <td><?php echo $employee_name;?></td>
                        <td><?php echo str_replace('_',' ',$employee->job_position_name);?></td>
                      
                        <td>{{$employee->division_name}}</td>

                    </tr>
                    @endforeach
                    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                   