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
                         <td>{{$employee->username}}</td>
                        <td><?php echo $employee_name;?></td>
                         <td>{{$employee->gossi_number}}</td> 
                        <td><?php echo str_replace('_',' ',$employee->id_professional_name);?></td>
                        <td>{{$employee->country_name}}</td> 
                         <td>{{$employee->passport_number}}</td> 
                        <td>{{$employee->email}}</td>
                        <td>{{$employee->mobile_number}}</td>                        
                       
                        <td>{{$employee->division_name}}</td>
                        <?php if($employee->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }?>
                         <td>{{$status}}</td>

                    </tr>
                    @endforeach
                    
                    <?php if($employees->lastPage() > 1){ ?>
                    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                   <?php } ?>