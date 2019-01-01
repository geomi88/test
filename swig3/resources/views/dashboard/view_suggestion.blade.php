@extends('layouts.main')
@section('content')

<style>
    .btnTaskDownload {
    background: url('../images/download.png') no-repeat 0 0 / cover;
    width: 15px;
    height: 20px;
    top: 14px;
   right: 85px;
   cursor: pointer;
   position :absolute;
}

</style>
   <div class="innerContent">
        <a class="btnBack" href="{{ URL::to('dashboard')}}">Back</a>
        <div class="taskHolder">
            
            
            <div class="taskManage" style="padding-top: 30px;">
                
                    <div class="taskStatus">
                        <h3>Suggestions/Complaints List</h3>
                        <div class="taskContent dropme5" id="new-task">
                            @foreach ($new_suggestons as $suggestion)
                                
                                <div class="tasklist newtask" id="{{$suggestion->id}}" attrstatus="{{$suggestion->status}}">
                                    <div class="TitleWrapper">
                                        <h4>{{$suggestion->title}} (By {{$suggestion->first_name}})</h4>
                                        <div class="toolDescription">
                                            <p>{{$suggestion->description}}</p>
                                        </div>
                                        <span class="iconDescription"></span>
                                        <span class="time"><?php echo date("H:i:s", strtotime($suggestion->created_at)); ?></span>
                                        <span class="date"><?php echo date("d-m-Y", strtotime($suggestion->created_at)); ?></span>
                                        <a class="btnTaskDelete" attrTaskId="{{$suggestion->id}}"></a>
                                       <?php if($suggestion->attachment!=""){?> <a class="btnTaskDownload"  href="{{$suggestion->attachment}}" download></a><?php } ?>
                                       
                                    </div>
                                    
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="taskStatus">
                        <h3>Noted Suggestions/Complaints</h3>
                        <div class="taskContent dropme5 statusComplete" id="completed-task">
                            @foreach ($noted_suggestons as $suggestion)
                                
                                <div class="tasklist nodragorsort" id="{{$suggestion->id}}" attrstatus="{{$suggestion->status}}">
                                    <div class="TitleWrapper">
                                        <h4>{{$suggestion->title}} (By {{$suggestion->first_name}})</h4>
                                        <div class="toolDescription">
                                            <p>{{$suggestion->description}}</p>
                                        </div>
                                        <span class="iconDescription"></span>
                                        <span class="time"><?php echo date("H:i:s", strtotime($suggestion->updated_at)); ?></span>
                                        <span class="date"><?php echo date("d-m-Y", strtotime($suggestion->updated_at)); ?></span>
                                        <a class="btnTaskDelete" attrTaskId="{{$suggestion->id}}"></a>
                                         <?php if($suggestion->attachment!=""){?> <a class="btnTaskDownload"  href="{{$suggestion->attachment}}" download></a><?php } ?>
                                       
                                    </div>
                                    
                                </div>
                            @endforeach
                        </div>
                    </div>
                
            </div>
        </div>
       <div class="commonLoaderV1"></div>
    </div>
<script>
     $(document).ready(function () {
         
            $(".commonLoaderV1").hide();
            

            $('.dropme5').sortable({
                connectWith: '.dropme5',
                items: '> :not(.nodragorsort)',
                update: function( event, ui ) {
                    
                }
            });

           
            
            $("#completed-task").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    var status = draggable.attr("attrstatus");
                    
                    if(status!=3){
                        complete_task(id);
                    }
                }
            });

            
            $('body').on('click', '.btnTaskDelete', function() {
                var blnConfrm= confirm("Are you sure to remove this suggestion ?");
                if(blnConfrm){
                    var taskid=$(this).attr('attrTaskId');
                    $.ajax({
                        type: 'POST',
                        url: '../tasks/view_suggestions/deletesuggestion',
                        data: '&taskid=' + taskid,
                        success: function (return_data) {
                            if(return_data==1){
                                $("#"+taskid).remove();
                                toastr.success('Suggestion Deleted Successfully!');
                            }else{
                                $("#"+taskid).remove();
                                toastr.error('Sorry There Was Some Problem!');
                            }
                            
                        }
                    });
                }
            });
            
              
        
         });
         
       
         
         function complete_task(taskid){
             $.ajax({
                type: 'POST',
                url: '../tasks/view_suggestions/complete_task',
                data: '&taskid=' + taskid,
                success: function (return_data) {
                    window.location.href = '{{url("dashboard/view_suggestions")}}';
                }
            });
            
         }
         
</script>
@endsection