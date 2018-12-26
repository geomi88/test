@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />

<div class="innerContent">


    <form action="{{ action('Meeting\AgendaController@save_agenda') }}" method="post" id="addagendaform">
        <input type="hidden" id="empdetails" name="empdetails" value=""> 
        <input type="hidden" id="meeting_id" name="meeting_id" value="{{$meeting_id}}"> 
        <div class="fieldGroup meetingHolder">
                            <header class="pageTitle">
                                <h1>Create <span>Agenda</span></h1>
                            </header>

                            <div class="flexslider meetingTimes">
                                <ul class="slides">
                                    <?php 
                                    $current_date = date('Y-m-d');
                                    foreach($meetings as $meeting) {
                                        $meeting_date_exploded = explode(' ',$meeting->start_date);
                                        $meeting_date =  explode('-',$meeting_date_exploded[0]);
                                        $meeting_day = $meeting_date[2];
                                        $meeting_month = $meeting_date[1];
                                        $meeting_year = $meeting_date[0];
                                    ?>
                                    
                                    <li <?php if($current_date > $meeting->start_date) {?>class="prev" <?php } ?> <?php if($meeting->id == $meeting_id) { ?>class="selected"<?php }?>><a href="{{ URL::to('meeting/agenda/add_agenda', ['id' => $meeting->id]) }}"><em><?php echo $meeting_day;?></em><?php echo $meeting_month."-".$meeting_year;?></a></li>
                                    
                                    <?php } ?>
                                </ul>
                            </div>
                           
                            <div class="custRow">
                                <div class="custCol-4">
                                    <div class="inputHolder">
                                        <label>Title</label>
                                        <input type="text" name="title" id="title">
                                    </div>
                                </div>
                            </div>

                            <div class="custRow">
                                <div class="custCol-4">
                                    <div class="inputHolder">
                                        <label>Description</label>
                                        <textarea name="description" id="description"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="custRow">
                                <div class="custCol-3">
                                    <div class="inputHolder">
                                        <label>Start Date</label>
                                        <input type="text" name="start_date" id="start_date">
                                    </div>
                                </div>
                            </div>

                            <div class="custRow">
                                <div class="custCol-3">
                                    <div class="inputHolder">
                                        <label>End Date</label>
                                        <input type="text" name="end_date" id="end_date">
                                    </div>
                                </div>
                            </div>

                            

                                    <h3>Added Participants</h3>
                                    <div class="listerType1">
                                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                                        <thead class="listHeaderTop">
                                            <tr class="headingHolder">
                                                <td></td>
                                                <td>Emp Code</td>
                                                <td>Emp Name</td>
                                                <td>Mail Id</td>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            
                                            <?php foreach($meeting_attendees as $employee) {?>
                                            <tr>
                                                <td class="checkboxSet">
                                                    <input type="checkbox" id="{{$employee->user_id}}" class="chkemployee">
                                                    <input type="hidden" id="name_{{$employee->user_id}}" value="<?php echo $employee->first_name." ".$employee->last_name;?>">
                                                    <input type="hidden" id="code_{{$employee->user_id}}" value="{{$employee->username}}">
                                                </td>
                                                <td>{{$employee->username}}</td>
                                                <td>{{$employee->first_name}} {{$employee->last_name}}</td>
                                                <td>{{$employee->email}}</td>
                                                
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <div class="commonLoaderV1"></div>
                                </div>
                                <div class="custRow">
                                    <div class="custCol-4">
                                        <input type="submit" value="Create" id="btnCreate" class="commonBtn bgGreen addBtn" id="sub">
                                    </div>
                                </div>
                        </div>
                    </form>
</div>
<script>
    $(document).ready(function () {

        
        $("#addagendaform").validate({
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
                    }, },
                description: {required: true},
                start_date: {required: true},
                end_date: {required: true},
                
            },
            messages: {
                title: "Enter Title",
                description : "Enter Description",
                start_date: "Select Start Date",
                end_date: "Select End Date",
                
            }
        });

        $('#addagendaform').submit(function (e) {
            if (!$('#addagendaform').valid()) {
                return false;
            }

            
                
        });

        

        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: '+0D',
            dateFormat: 'dd-mm-yy',
            onSelect: function (selected) {

                $("#end_date").datepicker("option", "minDate", selected);
            }
        });

        $("#end_date").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            minDate: '+0D',
            changeYear: true,
        });

        if ($("#start_date").val() != '') {
            $("#end_date").datepicker("option", "minDate", $("#start_date").val());
        }

    });

    


</script>


<script>
    var arrEmployeeList = [];

    $(document).ready(function () {
        
        

        $('body').on('click', '.chkemployee', function () {
            if ($(this).is(":checked")) {
                addtolist($(this).attr('id'));
            } else {
                remove($(this).attr('id'));
            }

            $(".commonLoaderV1").hide();
        })

        $('#addagendaform').submit(function (e) {
            if (arrEmployeeList.length > 0) {
                
                    var arremp = JSON.stringify(arrEmployeeList);
                    $("#empdetails").val(arremp);
                

            } 
        });

    });

    
    function addtolist(employeeid) {

        $(".commonLoaderV1").show();
        var intItemDuplicate = 0;
        if (arrEmployeeList.length > 0) {
            for (var i = 0; i < arrEmployeeList.length; i++) {
                if (employeeid == arrEmployeeList[i].emp_id) {
                    intItemDuplicate = 1;
                }
            }
        }

        var arraData = {
            emp_id: employeeid,
            emp_name: $("#name_" + employeeid).val(),
            code: $("#code_" + employeeid).val(),
        }

        if (intItemDuplicate != 1) {
            arrEmployeeList.push(arraData);
            
        }
    }

    function remove(employeeid) {
        for (var i = 0; i < arrEmployeeList.length; i++) {
            if (employeeid == arrEmployeeList[i].emp_id) {
                arrEmployeeList.splice(i, 1);
                $('#' + employeeid).attr('checked', false);
            }
        }
        
    }

</script>
@endsection