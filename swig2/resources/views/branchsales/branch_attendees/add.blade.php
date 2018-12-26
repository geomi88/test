@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Branch Attendees</span></h1>
    </header>	

    <form action="{{ url('branchsales/branch_attendees/store') }}" method="post" id="battendinsertaion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custCol-6">
                <div class="inputHolder bgSelect fieldMargin">


                    <div class="commonCheckHolder radioRender">
                        <label>

                            <input id="branchhead" type="radio" name="branchhead" value="cashier" onclick="handleClick('cashier');" checked>
                            <span></span>
                            <em>Cashier</em>
                        </label>
                    </div>

                    <div class="commonCheckHolder radioRender">
                        <label>

                            <input id="branchhead" type="radio" name="branchhead" value="barista" onclick="handleClick('barista');">
                            <span></span>
                            <em>Barista</em>
                        </label>
                    </div>

                </div>
            </div>
            <div class="customClear"></div>

            <div class="employedetails">
            <?php if (count($cashier_details) > 0) { ?>
            
                <div class="listContainerV1">
                    <div class="empList">
                        <input type="hidden" name="cashier_id" value="{{$cashier_details->id}}" >
                        <figure class="imgHolder">
                            <img src="<?php echo $cashier_details->profilepic; ?>" alt="">
                        </figure>
                        <div class="details">
                            <b>{{$cashier_details->first_name}} {{$cashier_details->alias_name}}</b>
                            <p>Designation : <span>Cashier</span></p>
                            <figure class="flagHolder">
                                <img src="{{ URL::asset('images/flags/'.$cashier_details->flag_name) }}" alt="Flag">
                                <figcaption>{{$cashier_details->country_name}}</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>

                </div>
            

            <?php } if (count($cashier_allocation_det) > 0) { ?>
            
                <input type="hidden" name="shift_start" id="shift_start" value="{{$cashier_allocation_det->start_time}}" class="shift_start">
                <input type="hidden" name="shift_end" id="shift_end" value="{{$cashier_allocation_det->end_time}}" class="shift_end">
                <input type="hidden" name="shift_min" id="shift_min"  class="shift_min">
                <input type="hidden" name="shift_br" id="shift_br"  class="shift_br">
                <div class="custRow">
                    <div class="custCol-4">
                        <input type="hidden" name="branch_id" value="{{$cashier_allocation_det->branch_id}}">
                        <div class="inputHolder bgSelect">
                            <label>Branch Name</label>
                            <b>{{$cashier_allocation_det->branch_name}}</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <input type="hidden" name="job_shift" value="{{$cashier_allocation_det->shift_id}}">
                        <div class="inputHolder bgSelect">
                            <label>Shift Name</label>
                            <b>{{$cashier_allocation_det->shift_name}}</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder bgSelect">
                            <label>Today's Date</label>
                            <b><?php echo date('d-m-Y'); ?></b>
                            <span class="commonError"></span>
                        </div>
                    </div>

                </div>
            
            <?php } ?>    
            </div>
            <div class="custCol-4" id="divBarista">
                <div class="inputHolder">
                    <label id="codeid">Barista Code</label>
                    <select name="employee_id" id="employee_id">
                        <option selected value=''>Select Code</option>
                        @foreach ($employees as $employee)
                        <option value='{{ $employee->emp_id }}'>{{ $employee->emp_code}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
                
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Time In</label>                        
                        <input type="text" name="tin" id="tin" class="txt">
                        <!--<button type="button" id="setTimeButton1">Set current time</button>-->
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Time out</label>        
                    <input type="text" name="tout" id="tout" class="txt" >                       
                    <!--<button type="button" id="setTimeButton2">Set current time</button>-->
                    <span class="commonError"></span> 
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Over Time</label>                        
                    <input type="text" name="ot" id="ot" readonly="readonly">
                    <span class="commonError"></span>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Total Time</label>                        
                    <input type="text" name="tt" id="tt" readonly="readonly">
                    <span class="commonError"></span>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <input type="submit" value="Submit" class="commonBtn bgGreen addBtn" name="submit">
            </div>
        </div>

</div>
</form>
</div>

<script>
    $(document).ready(function ()
    {

        var timepicker = $('#tin').timepicker({'timeFormat': 'h:i a', 'disableTextInput': true});
        $('#tout').timepicker({'timeFormat': 'h:i a', 'disableTextInput': true});
        
        $('#divBarista').hide();
        
        $(".txt").change(function () {
            var start = $('#shift_start').val();
            var end = $('#shift_end').val();
            

            if (start != "" && end != "")
            {
                var timeStart = new Date("01/01/2007 " + start);
                var timeEnd = new Date("01/01/2007 " + end);

                var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
                var ot = 0;
                var minutes = diff % 60;
                var hours = (diff - minutes) / 60;
                if (hours < 0)
                    hours = 24 + hours;
                $('#shift_br').val(hours);
                $('#shift_min').val(minutes);


            } 

            var time1 = $('#tin').val();
            var time2 = $('#tout').val();
            var sft1 = $('#shift_br').val();
            var sft2 = $('#shift_min').val();

            if (time1 && time2)
            {
                var timeStart = new Date("01/01/2007 " + time1);
                var timeEnd = new Date("01/01/2007 " + time2);

                var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
                var ot = 0;
                var minutes = diff % 60;
                var hours = (diff - minutes) / 60;
                if (hours < 0)
                    hours = 24 + hours;

                var time_Start = new Date("01/01/2007 " + sft1 + ":" + sft2);
                var time_End = new Date("01/01/2007 " + hours + ":" + minutes);
                if (time_Start < time_End)
                {
                    var real_ot = (time_End - time_Start) / 60000;
                    var minutes1 = real_ot % 60;
                    var hours1 = (real_ot - minutes1) / 60;
                    if (hours1 < 0)
                        hours1 = 24 + hours1;
                    $('#ot').val(hours1 + ":" + minutes1);
                }
                else
                {
                    $('#ot').val('');
                }
                $('#tt').val(hours + ":" + minutes);
            }
        });

        
//        $('.job_shift').on("change", function () {
//            //alert('sad');
//            var shiftid = $('#job_shift').val();
//            $.ajax({
//                type: 'POST',
//                url: '../branch_attendees/shiftdetails',
//                data: 'shiftid=' + shiftid,
//                beforeSend: function () {
//                    $(".selectedshift").html('Loading...');
//                    $('.selectedshift').show();
//                },
//                success: function (return_data) {
//                    var result = $.parseJSON(return_data);
//                    var start = result[0];
//                    var end = result[1];
//
//
//
//                    if (start && end != "")
//                    {
//                        var timeStart = new Date("01/01/2007 " + start);
//                        var timeEnd = new Date("01/01/2007 " + end);
//                        //alert(start); alert(end);
//                        var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
//                        var ot = 0;
//                        var minutes = diff % 60;
//                        var hours = (diff - minutes) / 60;
//                        if (hours < 0)
//                            hours = 24 + hours;
//                        $('#shift_br').val(hours);
//                        $('#shift_min').val(minutes);
//                        $('.selectedshift').html(hours + ':' + minutes);
//                        $('.selectedshift').show();
//                    }
//                    else
//                    {
//                        $('.selectedshift').hide();
//                        $('#shift_br').val('');
//                        $('#shift_min').val('');
//                    }
//
//                }
//            });
//
//        });


////////////////////form validate//////////////////
        $("#battendinsertaion").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                
                employee_id:
                        {
                            required: true,
                        },
                tin:
                        {
                            required: true,
                        },
                tout:
                        {
                            required: true,
                        }

            },
            submitHandler: function () {
                form.submit();
            },
            messages: {
                employee_id: "Select employee",
                tin: "Select Time In",
                tout: "Select Time Out",
                ot: "Enter Over Time",
            }
        });


        //////////////////populate deopdown/////////////////

        $('#employee_id').on("change", function () {
            var employee_id = $(this).val();
            if (employee_id != '')
            {
                $.ajax({
                    type: 'POST',
                    url: '../branch_attendees/getbarista',
                    data: 'employee_id=' + employee_id,
                    success: function (return_data) {
                        $('.employedetails').html(return_data);
                        
                    }
                });
            }else{
                $('.employedetails').html('');
            }
            

        });

    });

////////////////////// radio button on change////////////
    function handleClick(data) {

        if (data == 'cashier')
        {
            $.ajax({
                type: 'POST',
                url: '../branch_attendees/getcashier',
                success: function (return_data) {
                    $('.employedetails').html(return_data);
                }
            });
            $('#divBarista').hide();

        }
        else if (data == 'barista')
        {
           $('.employedetails').html('');
            $('#divBarista').show();
        }
        
        $(".txt").val('');
        $("#ot").val('');
        $("#tt").val('');
    }
    //////////////////time picker/////////////////
    $('#setTimeButton1').on('click', function () {
        $('#tin').timepicker('setTime', new Date());

    });
    $('#setTimeButton2').on('click', function () {
        $('#tout').timepicker('setTime', new Date());
    });
    $(document).on('click', '.txt', function () {
        $("#container").css("overflow-x", "visible");
        //alert('Working with div id');
    });
</script>
@endsection