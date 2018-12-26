<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
                    @foreach ($employees as $employee)
                  
                    <?php $n++; 
                    $employee_name = $employee->first_name." ".$employee->alias_name;
                    ?>

                    <tr>
                         <td class="employee_code">{{$employee->employee_code}}</td>
                        <td class="employee_name"><?php echo $employee_name;?></td>
                        
                         <td class="branch">{{$employee->branch_code.'-'.$employee->branch_name}}</td>
                        
                    </tr>
                    @endforeach
                    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                   