@extends('layouts.main')
@section('content')
   <div class="innerContent">
        <a class="btnBack" href="{{ URL::to('dashboard')}}">Back</a>
        <div class="taskHolder">
            <form action="{{ action('Tasks\TodoController@savetodo') }}" method="post" id="frmAddTask">
                <div class="fieldGroup" id="fieldSet1">
                    <div class="custRow">
                        <div class="custCol-5">
                            <div class="inputHolder">
                                <label>Enter Title</label>
                                <input type="text" name="title" id="title" placeholder="Enter Title" maxlength="230">
                                <span class="commonError"></span> 
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-8">
                            <div class="inputHolder">
                                <label>Task Description</label>
                                <textarea name="description" id="description" placeholder="Enter Task Description"></textarea>
                                <input type="submit" class="btnaddtodo" id="btnAddTask" value="Create">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <ul class="taskCalander">
                                <li><?php echo date('d')?></li>
                                <li><?php echo date('M')?></li>
                                <li><?php echo date('Y')?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
            <div class="colorLabelView">
                <ul>
<!--                    <li class="meeting">Meeting</li>-->
                    <li class="toDo">To do</li>
                    <li class="assign">Assign Task</li>
                    <li class="plan">Plan</li>
                    <li class="agenta">Agenda</li>
                </ul>
            </div> 
            
            <div class="taskManage">
                
                    <div class="taskStatus">
                        <h3>To Do (Today) List</h3>
                        <div class="taskContent dropme5" id="new-task">
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
                                        <a class="btnTaskDelete" attrTaskId="{{$task->id}}"></a>
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
                        <div class="taskContent dropme5 statusHold" id="peding-task">
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
                                        <a class="btnTaskDelete" attrTaskId="{{$task->id}}"></a>
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
                        <div class="taskContent dropme5 statusComplete" id="completed-task">
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
                                        <a class="btnTaskDelete" attrTaskId="{{$task->id}}"></a>
                                        <span class="iconTab" attrId="{{$task->id}}" id="hbutton_{{$task->id}}"></span>
                                    </div>
                                    <div class="history" id="history_{{$task->id}}">
                                        
                                    </div>
                                </div>
                            @endforeach
                            <a href="{{ url('tasks/history') }}" class="btnReadmore">Load more</a>
                        </div>
                    </div>
                
            </div>
        </div>
       <div class="commonLoaderV1"></div>
    </div>
<script>
     $(document).ready(function () {
         
            $(".commonLoaderV1").hide();
            $("#frmAddTask").validate({
                errorElement: "span",
                errorClass: "commonError",
                highlight: function (element, errorClass) {
                    $(element).addClass('valErrorV1');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass("valErrorV1");
                },
                rules: {
                    title: {required: { depends: function () { $(this).val($.trim($(this).val()));return true;}}},
                },
                submitHandler: function (form) {
                    $("#btnAddTask").attr("disabled", true);
                    $(".commonLoaderV1").show();
                    form.submit();
                },
                messages: {
                    title: "Enter task title",
                }
            });
            
            $('#frmAddTask').submit(function(e) {
                if(!$('#frmAddTask').valid()){
                    return false;
                }
                if($.trim($('#frmAddTask').val())==''){
                    return false;
                }
            });

            $('.dropme5').sortable({
                connectWith: '.dropme5',
                items: '> :not(.nodragorsort)',
                update: function( event, ui ) {
                    change_priority();
                }
            });

            $("#peding-task").droppable({

                drop: function (event, ui) {
                    
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    var status = draggable.attr("attrstatus");
                    if(status!=2){
                        change_status(id,2);
                        $("#hbutton_"+id).removeClass("dataLoaded");
                    }
                }
            });
            
            $("#completed-task").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    var status = draggable.attr("attrstatus");
                    
                    if(status!=3){
                        complete_task(id);
                        $("#"+id).addClass('nodragorsort');
                        $("#hbutton_"+id).removeClass("dataLoaded");
                    }
                }
            });

            $("#new-task").droppable({
                
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    var status = draggable.attr("attrstatus");
                    if(status!=1){
                        change_status(id,1);
                        $("#hbutton_"+id).removeClass("dataLoaded");
                    }
                }
            });
            
            $('body').on('click', '.btnTaskDelete', function() {
                var blnConfrm= confirm("Are you sure to remove this task ?");
                if(blnConfrm){
                    var taskid=$(this).attr('attrTaskId');
                    $.ajax({
                        type: 'POST',
                        url: '../tasks/todo/deletetodo',
                        data: '&taskid=' + taskid,
                        success: function (return_data) {
                            if(return_data==1){
                                $("#"+taskid).remove();
                                toastr.success('Task Deleted Successfully!');
                            }else{
                                $("#"+taskid).remove();
                                toastr.error('Sorry There Was Some Problem!');
                            }
                            
                        }
                    });
                }
            });
        
         });
         
         function change_status(taskid,status){
             $.ajax({
                type: 'POST',
                url: '../tasks/todo/change_status',
                data: '&taskid=' + taskid + '&status='+status,
                success: function (return_data) {
                    $("#"+taskid).attr("attrstatus",status)
                }
            });
            
            change_priority();
         }
         
         function complete_task(taskid){
             $.ajax({
                type: 'POST',
                url: '../tasks/todo/complete_task',
                data: '&taskid=' + taskid,
                success: function (return_data) {
                    window.location.href = '{{url("dashboard/todo")}}';
                }
            });
            
         }
         
         $('body').on('click', '.iconTab', function() {
             var taskid = $(this).attr("attrId");
             if($("#hbutton_"+taskid).hasClass("dataLoaded")){
                 return;
             }
             $.ajax({
                type: 'POST',
                url: '../tasks/todo/get_task_history',
                data: '&taskid=' + taskid,
                success: function (return_data) {
                   $("#history_"+taskid).html(return_data);
                   $("#hbutton_"+taskid).addClass("dataLoaded");
                }
            });
            
          });
         
        function change_priority(event, ui){
            
            var arrNewTaskPy=[];
            $(".newtask").each(function(){
                var taskindex = $(this).index();
                var taskid = $(this).attr("id");
                
                 var arraData = {
                    taskindex: taskindex+5,
                    taskid: taskid,
                }
                
                arrNewTaskPy.push(arraData);
            });
            
            var arrPendingTaskPy=[];
            $(".pendingtask").each(function(){
                var taskindex = $(this).index();
                var taskid = $(this).attr("id");
                
                 var arraData = {
                    taskindex: taskindex+5,
                    taskid: taskid,
                }
                
                arrPendingTaskPy.push(arraData);
            });
            
           
           var arrNewTaskPys = encodeURIComponent(JSON.stringify(arrNewTaskPy));
           var arrPendingTaskPys = encodeURIComponent(JSON.stringify(arrPendingTaskPy));
          
            $.ajax({
               type: 'POST',
               url: '../tasks/todo/change_priority',
               data: '&arrNewTaskPys=' + arrNewTaskPys +
                     '&arrPendingTaskPys=' + arrPendingTaskPys,
               success: function (return_data) {

               }
           });

         }
	
         
</script>
@endsection