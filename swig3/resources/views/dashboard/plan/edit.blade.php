@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />

<div class="innerContent">
    <header class="pageTitle">
        <h1>Update <span>Plan</span></h1>
    </header>	

    <form action="{{ action('Tasks\PlanController@update') }}" method="post" id="frmeditplan">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder shifName ">
                        <label>Title</label>
                        <input type="hidden" id="planid" name="planid" value="{{ $plan->id }}">
                        <input type="hidden" id="oldstatus" name="oldstatus" value="{{$plan->status}}">
                        <input type="text" name="title" id="title" placeholder="Enter Title" autocomplete="off" value="{{$plan->title}}" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
             <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description" id="description" placeholder="Enter Description">{{$plan->description}}</textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                 <div class="custCol-2">
                    <div class="commonCheckHolder clsChkPadding">
                        <label>
                            <input type="checkbox" <?php if($plan->is_all_day_task==1){echo "checked='checked'";}?> name="alldaytask" id="alldaytask" class="selectallday">
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
                        <input type="text" name="start_date" placeholder="Select Start Date" id="start_date" readonly="readonly" value="<?php echo date("d-m-Y", strtotime($plan->start_date));?>">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-2 divtime">
                    <div class="inputHolder">
                        <label>Start Time</label>                        
                        <input type="text" name="start_time" id="start_time" class="txt" placeholder="Select Start Time" value="<?php $stime=date("H:i", strtotime($plan->start_date)); echo $stime;?>">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            
            <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder">
                        <label>End Date</label>
                        <input  type="text" name="end_date" placeholder="Select End Date" id="end_date" readonly="readonly" value="<?php echo date("d-m-Y", strtotime($plan->end_date));?>">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-2 divtime">
                    <div class="inputHolder">
                        <label>End Time</label>        
                        <input type="text" name="end_time" id="end_time" class="txt" placeholder="Select End Time" value="<?php $etime=date("H:i", strtotime($plan->end_date)); echo $etime;?>">                       
                        <span class="commonError"></span> 
                    </div>
                </div>
                
            </div>
           
             <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Status</label>
                        <select class="commoSelect" name="task_status" id="task_status" <?php if($plan->status ==3){ echo "disabled";}?>>
                            <option value="">Select Status</option>
                            <option <?php if ($plan->status ==2) { echo "selected";} ?> value="2">Pending</option>
                            <option <?php if ($plan->status ==3) { echo "selected";} ?> value="3">Completed</option>
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update"  class="commonBtn bgGreen addBtn shift" id="btnupdateplan">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    $(document).ready(function (){
        
        if ($('#alldaytask').is(':checked')) {
            $(".divtime").hide();
        }else{
            $(".divtime").show();
        }
        
        var timepicker = $('#start_time').timepicker({'timeFormat': 'H:i', 'disableTextInput': true});
        $('#end_time').timepicker({'timeFormat': 'H:i', 'disableTextInput': true});

        $("#frmeditplan").validate({
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

            },
            
            messages: {
                title: "Enter Title",
                start_date: "Select Start Date",
                end_date: "Select End Date",
                start_time: "Select Start Time",
                end_time: "Select End Time",
            }
        });
        
        $('#frmeditplan').submit(function(e) {
            
            if(!$('#frmeditplan').valid()){
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
            
            $("#btnupdateplan").attr("disabled", true);
            
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
            minDate: '+1D',
            dateFormat: 'dd-mm-yy',
            onSelect: function (selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate() + 1);
                $("#end_date").datepicker("option", "minDate", selected);
            }
        });

        $("#end_date").datepicker({
             dateFormat: 'dd-mm-yy',
//             yearRange: '1950:c',
             minDate: '+1D',
             changeMonth: true,
             changeYear: true,
             onSelect: function(selected) {
                $("#start_date").datepicker("option","maxDate", selected)
             }
        });
     
    });

    $(document).on('click', '.txt', function () {
    //    $("#container").css("overflow-x", "visible");
        //alert('Working with div id');
    });

 
</script>
@endsection