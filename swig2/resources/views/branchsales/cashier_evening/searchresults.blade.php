<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
                    @foreach ($employees as $employee)
                  
                    <?php $n++; 
                    $employee_name = $employee->first_name." ".$employee->alias_name;
                    ?>

                    <tr>
                         <td class="eve_code">{{$employee->employee_code}}</td>
                        <td class="eve_name"><?php echo $employee_name;?></td>
                        
                         <td class="eve_branch">{{$employee->branch_code.'-'.$employee->branch_name}}</td>
                         <td class="eve_region">{{$employee->region}}</td>
                        
                    </tr>
                    @endforeach
                     <?php if($employees->lastPage() > 1){ ?>
                    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                     <?php }?>