@extends('layouts.main')
@section('content')
    <script src="{{ URL::asset('js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/fullcalendar.js') }}" type="text/javascript"></script>
    <link type="text/css" href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet" media="all" />
    <link type="text/css" href="{{ URL::asset('css/fullcalendar.min.css') }}" rel="stylesheet" media="all" />

        <div class="innerContent">

            <div class="taskDashboard">
                <ul class="taskBtnHolder">
                    <?php if ($admin_status == 1) { ?>
                        <li><a href="{{ url('dashboard/managementconsole') }}" class="btnV3 lightGreen"><img src="images/iconSales.png" alt="Sales"><span class="darkGreen">Sales</span></a></li>
                        <li><a href="{{ url('dashboard/todo') }}" class="btnV3 lightPink"><img src="images/iconTodo.png" alt="To Do"><span class="darkPink">To Do</span></a></li>
                        <li><a href="{{ url('dashboard/plan') }}" class="btnV3 lightPurple"><img src="images/iconPlan.png" alt="Plan"><span class="darkPurple">View Plan</span></a></li>
                        <li><a href="{{ url('dashboard/plan/createplan') }}" class="btnV3 lightYellow"><img src="images/iconCreatePlan.png" alt="Create Plan"><span class="darkYellow">Create Plan</span></a></li>
                        <li><a href="{{ url('tasks/history') }}" class="btnV3 lightViolet"><img src="images/iconHistory.png" alt="History"><span class="DarkViolet">History</span></a></li>
                        <li><a href="{{ url('dashboard/view_todo') }}" class="btnV3 lightSkyBlue"><img src="images/iconViewTodo.png" alt="View To Do"><span class="darkSkyBlue">View To Do</span></a></li>
                        <li><a href="{{ url('dashboard/assign_task') }}" class="btnV3 lightGrey"><img src="images/imgViewAssignList.png" alt="Assign Task"><span class="DarkGrey">Assign Task</span></a></li>
                        <li><a href="{{ url('dashboard/task_list') }}" class="btnV3 lightPeach"><img src="images/iconTaskList.png" alt="Assign Task List"><span class="DarkPeach">Assign Task List</span></a></li>
                        <li><a href="{{ url('dashboard/track_task') }}" class="btnV3 lightGreen2"><img src="images/iconTrack.png" alt="Track Task"><span class="DarkGreen">Track Task</span></a></li>
                        <li><a href="{{ url('dashboard/suggestion') }}" class="btnV3 lightAddSuggestion"><img src="images/addSuggestion.png" alt="Suggestion"><span class="darkAddSuggestion">Add Suggestion</span></a></li>
                        <li><a href="{{ url('dashboard/view_suggestions') }}" class="btnV3 lightViewSuggestion"><img src="images/viewSuggestion.png" alt="View Suggestion"><span class="darkViewSuggestion">View Suggestion</span></a></li>
                        <li><a href="{{ url('dashboard/employeekpi') }}" class="btnV3 lightYellow"><img src="images/iconPlan.png" alt="KPI"><span class="darkYellow">KPI</span></a></li>
                    <?php } else { ?>
                       
                      <?php  foreach ($user_sub_modules as $user_sub_module) { $clsArray=  explode("/", $user_sub_module->active_logo)?>
                        <li><a href="{{ url($user_sub_module->url) }}" class="btnV3 {{$clsArray[0]}}"><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="Sales"><span class="{{$clsArray[1]}}">{{$user_sub_module->name}}</span></a></li>
                    <?php }}?>
            
                </ul>
                <a class="btnAction print bgGreen" style="margin-bottom: 7px;" id="btnPrint" href="#">Print</a>
               
                <div class="custCol-3 clsselecttasktype">
                    <div class="inputHolder bgSelect">
                        <select class="commoSelect" name="task_type" id="task_type">
                            <option value="">Select Status</option>
                            <option value="1">Own Task</option>
                            <option value="2">Assigned Task</option>
                            <option value="3">Meeting</option>
                            <option value="4">Agenda</option>
                            <option value="5">Training</option>
                        </select>

                    </div>
                </div>
                
                <div style="display: none;">
                    <table>
                        <tbody class="calendarbody" id='calendarbody'>
                            @include('dashboard/result')
                        </tbody>
                    </table>
                </div>
                
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            My Calendar    
                        </div>
                        <div class="panel-body" >
                           <div id='calendar'></div>
                        </div>
                    </div>
            </div>
            <div class="commonLoaderV1"></div>
        </div>

<script>

    $(document).ready(function() {
        
        $('#calendar').fullCalendar({
                header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                },
                defaultDate: '<?php echo date('Y-m-d'); ?>',
                navLinks: true, // can click day/week names to navigate views
                selectable: false,
                selectHelper: false,
                editable: false,
                eventLimit: false, // allow "more" link when too many events
                events: <?php print_r($tasks); ?>,
                displayEventEnd: {
                    month: false,
                    basicWeek: true,
                    "default": true
                },
                nextDayThreshold: '00:25:00',
                timeFormat: 'h(:mm)t',
                dayRender: function (date, cell) {
                    // The cell has a data-date tag with the date we can use vs date.format('YYYY-MM-DD')
                    var theDate = $(cell).data('date');
                    
                    // Find the day number td for the date
                    var fcDaySkel = $("#calendar div.fc-content-skeleton td[data-date='"+theDate+"'].fc-future");
                        fcDaySkel.append('<a class="calAddBtn" title="Create Plan" attrurl="{{ url('dashboard/gotoplan')}}" attrdate='+theDate+'>+</a>');
                }
        });
        
        $('#task_type').on('change', function () {
            $.ajax({
                type: 'POST',
                url: 'dashboard/getevents',
                data: {task_type:$('#task_type').val()},
                success: function (return_data) {
                   
                    $('#calendar').fullCalendar('removeEvents');
                    $('#calendar').fullCalendar('addEventSource', return_data.tasks);         
                    $('#calendar').fullCalendar('refetchEvents' );
                }
            });
        });
    
        $('#btnPrint').click(function() {
            var view = $('#calendar').fullCalendar('getView');
            var dateStart=view.start._d;
            var dateEnd=view.end._d;

            var startDate=dateStart.getFullYear() + '-' + (dateStart.getMonth() + 1) + '-' + dateStart.getDate();
            var endDate=dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + (dateEnd.getDate()-1);
            $(".commonLoaderV1").show();
            $.ajax({
                type: 'POST',
                url: 'dashboard/getplanprint',
                data: {startDate:startDate,endDate:endDate,task_type:$('#task_type').val()},
                success: function (return_data) {
                    if (return_data != ''){
                        $('.calendarbody').html(return_data);
                    } else {
                        $('.calendarbody').html('<tr><td class="noData" colspan="2">No Records Found</td></tr>');
                    }
                }
            });
            
            var heading="Tasks";
            if($('#task_type').val()==2){
                heading="Assigned Tasks";
            }else if($('#task_type').val()==1){
                heading="Own Tasks";
            }else{
                heading="Tasks";
            }
            
            setTimeout(function(){ 
            $(".commonLoaderV1").hide();
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>'+
                                '<div style="text-align:center;"><h1>'+heading+'</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Sl.No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Title</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Start Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> End Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Status </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Created Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Description </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.calendarbody')[0].outerHTML +'</table>');
                win.document.close();
                win.print();
                win.close();
             }, 2000);
        });
        
        
        $('body').on('click','.calAddBtn', function () {
           var strdate=$(this).attr("attrdate");
           var strurl=$(this).attr("attrurl");
           window.location.href = strurl+"/"+strdate;
        });
        
        $('body').on('click','.fc-today', function () {
           window.location.href = '{{url("dashboard/todo")}}';
        });
        
    });
</script>
@endsection