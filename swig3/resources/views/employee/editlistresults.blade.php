<?php $n = $employees->perPage() * ($employees->currentPage()-1); ?>   
                    @foreach ($employees as $employee)
                    @if($employee->status==0)
                    <?php $status='Enable'; ?>
                    @else
                    <?php $status='Disable'; ?>
                    @endif
                    <?php $n++; 
                    $employee_name = $employee->first_name." ".$employee->alias_name;
                    if($employee->status== 1){
                            $status1="Enabled";
                        }else{
                            $status1="Disabled";
                        }
                    ?>

                    <tr> 
                        <td>{{$employee->username}}</td>
                        <td><?php echo $employee_name;?></td>
                        <td><?php echo str_replace('_',' ',$employee->job_position_name);?></td>
                        <td><?php echo str_replace('_',' ',$employee->professionname);?></td>
                        
                        <td>{{$employee->country_name}}</td> 
                        <td>{{$employee->email}}</td>
                        <td>{{$employee->mobile_number}}</td>                        
                       
                        <td>{{$employee->division_name}}</td>
                        <td>{{$status1}}</td>
                        <td class="btnHolder">
                            <div class="actionBtnSet">
                                <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
                                <div class="actionBtnHolderV1">
                                <a class="btnAction edit bgBlue" href="{{ URL::to('employee/edit', ['id' => Crypt::encrypt($employee->id)]) }}">Edit</a>
                                <a class="btnAction disable bgOrange" usr-attr="{{$employee->username}}" href="{{ URL::to('employee/'.$status, ['id' => Crypt::encrypt($employee->id)]) }}"><?php echo $status; ?></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                     <?php if($employees->lastPage() > 1){ ?>
                    <tr class="paginationHolder"><th><div>  {!! $employees->render() !!} </div> </th></tr>
                     <?php } ?>