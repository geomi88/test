<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
                    @foreach ($employees as $employee)
                  
                    <?php $n++; 
                    $employee_name = $employee->first_name." ".$employee->alias_name;
                    ?>

                    <tr>
                         <td class="mor_code">{{$employee->employee_code}}</td>
                        <td class="mor_name"><?php echo $employee_name;?></td>
                        
                         <td class="mor_branch">{{$employee->branch_code.'-'.$employee->branch_name}}</td>
                         <td class="mor_region">{{$employee->region}}</td>
                        
                    </tr>
                    @endforeach
                     <?php if($employees->lastPage() > 1){ ?>
                    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                     <?php }?>