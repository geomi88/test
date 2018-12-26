@extends('layouts.main')
@section('content')
   
   <div class="innerContent">
       <?php if($backbutton=='show'){?>
            <a class="btnBack" href="{{ URL::to('dashboard/assign_task/single_employee', ['id' => Crypt::encrypt($employe->id)]) }}">Back</a>
       <?php } else { ?>
            <a class="btnBack" href="{{ URL::to('dashboard/view_todo')}}">Back</a>
       <?php }?>
        
        <header class="pageTitle">
            <h1> To Do <span>List</span></h1>
            
        </header>
       
        <div class="taskHolder">
            
            <div class="listContainerV1">
                <div class="empList">
                    <figure class="imgHolder">
                        <img src="<?php echo $employe->profilepic; ?>" alt="">
                    </figure>
                    <div class="details">
                        <b>{{$employe->first_name}} {{$employe->alias_name}}</b>
                        <p>Designation : <span><?php echo str_replace('_', ' ', $employe->designation); ?></span></p>
                        <figure class="flagHolder">
                            <img src="{{ URL::asset('images/flags/'.$employe->flag_name) }}" alt="Flag">
                            <figcaption>{{$employe->country_name}}</figcaption>
                        </figure>
                    </div>
                    <div class="customClear"></div>
                </div>
            </div>
            <div class="colorLabelView">
                <ul>
<!--                    <li class="meeting">Meeting</li>-->
                    <li class="toDo">To do</li>
                    <li class="assign">Assign Task</li>
                    <li class="plan">Plan</li>
                    <li class="agenta">Agenda</li>
                </ul>
            </div>
            
            <div class="taskManage clsviewtodo">
                
                    <div class="taskStatus">
                        <h3>To Do (Today) List</h3>
                        <div class="taskContent clsnewplan" id="new-task">
                            @foreach ($new_tasks as $task)
                               
                                <div class="tasklist newtask {{$task->strColorClass}}" id="{{$task->id}}" attrpriority="{{$task->priority}}" attrstatus="{{$task->status}}">
                                    <div class="TitleWrapper">
                                        <h4>{{$task->title}}</h4>
                                        <div class="toolDescription">
                                            <p>{{$task->description}}</p>
                                        </div>
                                        <span class="iconDescription"></span>
                                        <span class="time"><?php echo date("H:i:s", strtotime($task->created_at)); ?></span>
                                        <span class="date"><?php echo date("d-m-Y", strtotime($task->created_at)); ?></span>
                                        <span class="iconTab" attrId="{{$task->id}}" id="hbutton_{{$task->id}}"></span>
                                    </div>
                                    <div class="history" id="history_{{$task->id}}">
                                        
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="taskStatus">
                        <h3>In Progress (WIP)</h3>
                        <div class="taskContent statusHold" id="peding-task">
                            @foreach ($pending_tasks as $task)
                                
                                <div class="tasklist pendingtask {{$task->strColorClass}}" id="{{$task->id}}" attrpriority="{{$task->priority}}" attrstatus="{{$task->status}}">
                                    <div class="TitleWrapper">
                                        <h4>{{$task->title}}</h4>
                                        <div class="toolDescription">
                                            <p>{{$task->description}}</p>
                                        </div>
                                        <span class="iconDescription"></span>
                                        <span class="time"><?php echo date("H:i:s", strtotime($task->created_at)); ?></span>
                                        <span class="date"><?php echo date("d-m-Y", strtotime($task->created_at)); ?></span>
                                        <span class="iconTab" attrId="{{$task->id}}" id="hbutton_{{$task->id}}"></span>
                                    </div>
                                    <div class="history" id="history_{{$task->id}}">
                                        
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="taskStatus">
                        <h3>Completed</h3>
                        <div class="taskContent statusComplete" id="completed-task">
                            @foreach ($completed_tasks as $task)
                               
                                <div class="tasklist nodragorsort {{$task->strColorClass}}" id="{{$task->id}}" attrpriority="{{$task->priority}}" attrstatus="{{$task->status}}">
                                    <div class="TitleWrapper">
                                        <h4>{{$task->title}}</h4>
                                        <div class="toolDescription">
                                            <p>{{$task->description}}</p>
                                        </div>
                                        <span class="iconDescription"></span>
                                        <span class="time"><?php echo date("H:i:s", strtotime($task->updated_at)); ?></span>
                                        <span class="date"><?php echo date("d-m-Y", strtotime($task->updated_at)); ?></span>
                                        <span class="iconTab" attrId="{{$task->id}}" id="hbutton_{{$task->id}}"></span>
                                    </div>
                                    <div class="history" id="history_{{$task->id}}">
                                        
                                    </div>
                                </div>
                            @endforeach
                            <a href="{{ URL::to('todohistorywithemployee', ['id' => Crypt::encrypt($employe->id)]) }}" class="btnReadmore">Load more</a>
                        </div>
                    </div>
                
            </div>
        </div>
       <div class="commonLoaderV1"></div>
    </div>
<script>
$(document).ready(function () {

    $('body').on('click', '.iconTab', function() {
        var taskid = $(this).attr("attrId");
        if($("#hbutton_"+taskid).hasClass("dataLoaded")){
            return;
        }
        
        $.ajax({
           type: 'POST',
           url: '../../todo/get_task_history',
           data: '&taskid=' + taskid,
           success: function (return_data) {
              $("#history_"+taskid).html(return_data);
              $("#hbutton_"+taskid).addClass("dataLoaded");
           }
       });

    });
          
});
         
</script>
@endsection