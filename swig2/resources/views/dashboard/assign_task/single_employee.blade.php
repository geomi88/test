@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />
   <div class="innerContent">
        <a class="btnBack" href="{{ URL::to('dashboard/assign_task')}}">Back</a>
        <header class="pageTitle">
            <h1> Assign <span>Task</span></h1>
        </header>
       
        <div class="listContainerV1 clsempdetails">
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

            <ul class="taskBtnHolder">
                <li><a href="{{ URL::to('dashboard/assign_task/get_todo', ['id' => Crypt::encrypt($employe->id)]) }}" class="btnV3 lightSkyBlue"><img src="../../../images/iconViewTodo.png" alt="View To Do"><span class="darkSkyBlue">View To Do</span></a></li>
                <li><a href="{{ URL::to('dashboard/assign_task/viewplan', ['id' => Crypt::encrypt($employe->id)]) }}" class="btnV3 lightPurple"><img src="../../../images/iconPlan.png" alt="Plan"><span class="darkPurple">View Plan</span></a></li>
                <li><a href="{{ URL::to('dashboard/assign_task/with_employee', ['id' => Crypt::encrypt($employe->id)]) }}" class="btnV3 lightViolet"><img src="../../../images/iconHistory.png" alt="History"><span class="DarkViolet">History</span></a></li>
            </ul>
         </div>
        


<form action="{{ action('Tasks\AssigntaskController@assigntasksingleemployee') }}" method="post" id="frmcreateplan">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder shifName ">
                        <label>Title</label>
                        <input type="hidden" name="emp_id" id="emp_id" value="{{$employe->id}}">
                        <input type="text" name="title" id="title" placeholder="Enter Title" autocomplete="off" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
             <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                 <div class="custCol-2">
                    <div class="commonCheckHolder clsChkPadding">
                        <label>
                            <input type="checkbox" name="alldaytask" id="alldaytask" class="selectallday">
                            <span></span>
                            <em>Multi Days</em>
                        </label>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder">
                        <label>Start Date</label>
                        <input type="text" name="start_date" placeholder="Select Start Date" id="start_date" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                
                <div class="custCol-2 divtime" >
                    <div class="inputHolder">
                        <label>Start Time</label>                        
                        <input type="text" name="start_time" id="start_time" class="txt" placeholder="Select Start Time">
                        <span class="commonError"></span>
                    </div>
                </div>
                
            </div>
            
            
            <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder">
                        <label>End Date</label>
                        <input  type="text" name="end_date" placeholder="Select End Date" id="end_date" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-2 divtime" >
                    <div class="inputHolder">
                        <label>End Time</label>        
                        <input type="text" name="end_time" id="end_time" class="txt" placeholder="Select End Time">                       
                        <span class="commonError"></span> 
                    </div>
                </div>
                
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Assign Task"  class="commonBtn bgGreen addBtn" id="btnCreateplan">
                </div>
            </div>

        </div>
    </form>
       <div class="commonLoaderV1"></div>
    </div>
<script>
$(document).ready(function () {
    
    var timepicker = $('#start_time').timepicker({'timeFormat': 'H:i', 'disableTextInput': true});
        $('#end_time').timepicker({'timeFormat': 'H:i', 'disableTextInput': true});

        $("#frmcreateplan").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                title: {required: {
                            depends: function () {
                                if ($.trim($(this).val()) == '') {
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            }
                        },},
                start_date:{required: true},
                end_date:{required: true},
                start_time:{required: true},
                end_time:{required: true},
                task_status:{required: true},

            },
           
            messages: {
                title: "Enter Title",
                start_date: "Select Start Date",
                end_date: "Select End Date",
                start_time: "Select Start Time",
                end_time: "Select End Time",
                task_status: "Select Status",
            }
        });
        
        $('#frmcreateplan').submit(function(e) {
            
            if(!$('#frmcreateplan').valid()){
                return false;
            }
            
            if(!$('#alldaytask').is(':checked')){
                var strStartTime=$("#start_date").val()+' '+$("#start_time").val()

                var dateString = strStartTime,
                dateTimeParts = dateString.split(' '),
                timeParts = dateTimeParts[1].split(':'),
                dateParts = dateTimeParts[0].split('-'),
                date;

                date = new Date(dateParts[2], parseInt(dateParts[1], 10) - 1, dateParts[0], timeParts[0], timeParts[1]);
                var startTimestamp=date.getTime();

                var strEndTime=$("#end_date").val()+' '+$("#end_time").val()
                var dateString = strEndTime,
                dateTimeParts = dateString.split(' '),
                timeParts = dateTimeParts[1].split(':'),
                dateParts = dateTimeParts[0].split('-'),
                date1;

                date1 = new Date(dateParts[2], parseInt(dateParts[1], 10) - 1, dateParts[0], timeParts[0], timeParts[1]);
                var endTimestamp=date1.getTime();

                if(parseInt(endTimestamp)<= parseInt(startTimestamp)){
                    alert("End time should be greater than start time, Please select valid start time and end time");
                    return false;
                }
            }
            
            $("#btnCreateplan").attr("disabled", true);
            
        });

        $(".selectallday").change(function() {
            if(this.checked) {
                $(".divtime").hide();
            }else{
                $(".divtime").show();
            }
        });
        
        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: '+0D',
            dateFormat: 'dd-mm-yy',
            onSelect: function (selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate() + 1);
                $("#end_date").datepicker("option", "minDate", selected);
            }
        });

        $("#end_date").datepicker({
             dateFormat: 'dd-mm-yy',
             changeMonth: true,
             minDate: '+1D',
             changeYear: true,
        });
         
});
         
</script>
@endsection