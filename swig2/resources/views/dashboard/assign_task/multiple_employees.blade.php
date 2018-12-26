@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />
<script>
    var arrEmployeeList = <?php echo $arrjson; ?>;
  
    function remove(employeeid) {
        for (var i = 0; i < arrEmployeeList.length; i++) {
            if(employeeid==arrEmployeeList[i].emp_id){
                arrEmployeeList.splice(i, 1);
                $('#'+employeeid).attr('checked', false);
            }
        }
        showselectedemps();
    }
    
    function showselectedemps()
    {
        $("#selectedemplist").html('<tr><td>No Employee Selected<td></tr>');
        if (arrEmployeeList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrEmployeeList.length; i++) {
                strHtml += '<tr><td>' + arrEmployeeList[i].emp_name + '</td><td>' + arrEmployeeList[i].designation + '</td><td>' + arrEmployeeList[i].code + '</td>\n\
                            <td><a href="javascript:remove(' + arrEmployeeList[i].emp_id + ')">Remove</a></td></tr>';
            }
            $("#selectedemplist").html(strHtml);
        }
    }
</script>

   <div class="innerContent">
        <a class="btnBack" href="{{ URL::to('dashboard/assign_task')}}">Back</a>
        <header class="pageTitle">
            <h1> Assign <span>Task</span></h1>
        </header>
       <form action="{{ action('Tasks\AssigntaskController@backtoemplist') }}" method="post" id="frmadditionalemps">
            <div class="fieldGroup" id="fieldSet1">
                <input type="hidden" id="empdetails" name="empdetails" value="">
                <div class="custRow">
                    <div class="custCol-12">
                        <input type="submit" value=""  class="right commonBtn bgGreen addBtn" id="btn">
                    </div>
                </div>

            </div>
        </form>
        <div class="listHolderType1" style="margin-top: 20px;">
            <div class="listerType1"> 
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>Employee Name</td>
                            <td>Job Position</td>
                            <td>Code</td>
                            <td>Remove</td>
                        </tr>
                    </thead>
                    <tbody id="selectedemplist">
                        
                    </tbody>
                </table>
            </div>					
        </div>
       
        <form action="{{ action('Tasks\AssigntaskController@assigntaskmultiemployee') }}" method="post" id="frmcreateplan">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder shifName ">
                        <label>Title</label>
                        <input type="hidden" name="emp_ids" id="emp_ids" value="">
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
        
        if(arrEmployeeList.length==0){
            alert("Please Select Atleast One Employee");
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
        
        var emp_ids='';
        for (var i = 0; i < arrEmployeeList.length; i++) {
            emp_ids=emp_ids+arrEmployeeList[i].emp_id+",";
        }
        $("#emp_ids").val(emp_ids);
        
        $("#btnCreateplan").attr("disabled", true);

    });

    $('#frmadditionalemps').submit(function(e) {
        if(arrEmployeeList.length>0){
            var arremp=JSON.stringify(arrEmployeeList);
            $("#empdetails").val(arremp);
        }
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
    
    showselectedemps();
});
         
</script>
@endsection