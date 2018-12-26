@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />
<script>

$(window).on('hashchange', function () {
    if (window.location.hash) {
        var page = window.location.hash.replace('#', '');
        if (page == Number.NaN || page <= 0) {
            return false;
        } else {
            getData(page);
        }
    }
});
$(document).ready(function ()
{
    $(document).on('click', '.pagination a', function (event)
    {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        //var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        getData(page);
    });


});

function getData(page) {

    var search_key = $('#search_key').val();
    var job_position = $('#job_position').val();
    var searchbycode = $('#searchbycode').val();
    var sortordname = $('#sortordname').val();
    var sortordjob = $('#sortordjob').val();
    var sortordcode = $('#sortordcode').val();
    var pagelimit = $('#page-limit').val();

    var strids = '';
    if (arrEmployeeList.length > 0) {
        for (var i = 0; i < arrEmployeeList.length; i++) {
            strids = strids + arrEmployeeList[i].emp_id + ',';
        }
    }

    $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: {search_key: search_key, job_position: job_position, searchbycode: searchbycode, sortordname: sortordname, sortordjob: sortordjob, sortordcode: sortordcode, strids: strids, pagelimit: pagelimit},
            })
            .done(function (data)
            {
                console.log(data);

                $(".employee_list").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                alert('No response from server');
            });
}



</script>
<div class="innerContent">

    <div class="fieldGroup meetingHolder">
        <form action="{{ action('Training\TrainingController@savemeeting') }}" method="post" id="frmcreatemeeting">
            <input type="hidden" id="empdetails" name="empdetails" value="">
            <input type="hidden" id="selected_custom_dates" name="selected_custom_dates" value="">
            <input type="hidden" id="selected_guests" name="selected_guests" value="">
            <input type="hidden" id="daily_selected_days" name="daily_selected_days" value="">
            <input type="hidden" id="weekly_selected_days" name="weekly_selected_days" value="">

            <header class="pageTitle">
                <h1>Create <span>Training</span></h1>
            </header>

            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Training Title</label>
                        <input type="text" name="title" id="title" maxlength="250" autocomplete="off" placeholder="Enter Title">
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
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Start Date</label>
                        <input type="text" name="start_date" id="start_date" placeholder="Start Date" readonly="readonly">
                    </div>
                </div>

                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Start Time</label>                        
                        <input type="text" name="start_time" placeholder="Start Time" id="start_time">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Duration</label>
                        <input type="text" name="duration" id="duration" placeholder="Duration">
                    </div>
                </div>

                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>End Date</label>
                        <input type="text" name="end_date" placeholder="End Date" id="end_date" readonly="readonly">
                    </div>
                </div>
            </div>


            <div class="custRow">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Repeat</label>
                        <select class="repeatOptn" name="repeat_option" id="repeat_option">
                            <option value="-1">Select</option>
                            <option>Daily</option>
                            <option>Weekly</option>
                            <option>Custom</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="repeatSelect">
                <div class="repeatFields" id="Daily">
                    <div class="custRow">
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="daily_monday" id="daily_Monday" class="dailyMeetingchk">
                                        <span></span>
                                        <em>Mon</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="daily_tuesday" id="daily_Tuesday" class="dailyMeetingchk">
                                        <span></span>
                                        <em>Tue</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="daily_wednesday" id="daily_Wednesday" class="dailyMeetingchk">
                                        <span></span>
                                        <em>Wed</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="daily_thursday" id="daily_Thursday" class="dailyMeetingchk">
                                        <span></span>
                                        <em>Thr</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="daily_friday" id="daily_Friday" class="dailyMeetingchk">
                                        <span></span>
                                        <em>Fri</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="daily_saturday" id="daily_Saturday" class="dailyMeetingchk">
                                        <span></span>
                                        <em>Sat</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="daily_sunday" id="daily_Sunday" class="dailyMeetingchk">
                                        <span></span>
                                        <em>Sun</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="repeatFields" id="Weekly">
                    <div class="custRow">
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <label>Repeat Every</label>
                                <input type="text" name="repeat_number_week" id="repeat_number_week">
                            </div>
                        </div>
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <label class="weekLabel">Week</label>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="weekly_monday" id="weekly_Monday" class="weeklyMeetingchk">
                                        <span></span>
                                        <em>Mon</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="weekly_tuesday" id="weekly_Tuesday" class="weeklyMeetingchk">
                                        <span></span>
                                        <em>Tue</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="weekly_wednesday" id="weekly_Wednesday" class="weeklyMeetingchk">
                                        <span></span>
                                        <em>Wed</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="weekly_thursday" id="weekly_Thursday" class="weeklyMeetingchk">
                                        <span></span>
                                        <em>Thr</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="weekly_friday" id="weekly_Friday" class="weeklyMeetingchk">
                                        <span></span>
                                        <em>Fri</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="weekly_saturday" id="weekly_Saturday" class="weeklyMeetingchk">
                                        <span></span>
                                        <em>Sat</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-3">
                            <div class="inputHolder">
                                <div class="commonCheckHolder">
                                    <label>
                                        <input type="checkbox" name="weekly_sunday" id="weekly_Sunday" class="weeklyMeetingchk">
                                        <span></span>
                                        <em>Sun</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="repeatFields" id="Custom">
                    <div class="custRow">
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <label>Choose Date</label>
                                <input type="text" name="repeat_date" id="repeat_date">
                            </div>
                        </div>
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <a class="commonBtn bgGreen addBtn repeat_date_btn" href="javascript:void(0)">Add</a>
                            </div>
                        </div>
                    </div>
                    <div class="selectedDate">

                    </div>
                </div>
            </div>    
            <div class="custRow">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Training Room</label>
                        <select class="" name="meeting_room" id="meeting_room" class="meeting_room">
                            <option value="-1">Select</option>
                            @foreach ($meeting_rooms as $meeting_room)
                            <option value="{{$meeting_room->id}}">{{$meeting_room->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <h3>Add Participants</h3>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <div class="commonCheckHolder">
                            <label>
                                <input type="radio" name="empType" value="mtgEmp" class="empType mtgEmpchk">
                                <span></span>
                                <em>MTG Employee</em>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <div class="commonCheckHolder">
                            <label>
                                <input type="radio" name="empType" value="guestEmp" class="meeting_employees guestEmpchk">
                                <span></span>
                                <em>New Employee</em>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            <div class="meeting_mtg_employees">
                <div class="listHolderType1">

                    <div class="listerType1 reportLister"> 
                        <input type="hidden" value="" id="sortordname" name="sortordname">
                        <input type="hidden" value="" id="sortordjob" name="sortordjob">
                        <input type="hidden" value="" id="sortordcode" name="sortordcode">
                        <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                        <div id="postable">
                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                <thead class="listHeaderTop">
                                    <tr class="headingHolder">
                                        <td>
                                        </td>
                                        <td>
                                            Employee Code
                                            <div class="sort">
                                                <a href="javascript:void(0)" class="btnUp codeup"></a>
                                                <a href="javascript:void(0)" class="btnDown codedown"></a>
                                            </div>
                                        </td>
                                        <td>
                                            Employee Name
                                            <div class="sort">
                                                <a href="javascript:void(0)" class="btnUp nameup"></a>
                                                <a href="javascript:void(0)" class="btnDown namedown"></a>
                                            </div>
                                        </td>

                                        <td>
                                            Job Position
                                            <div class="sort">
                                                <a href="javascript:void(0)" class="btnUp jobup"></a>
                                                <a href="javascript:void(0)" class="btnDown jobdown"></a>
                                            </div>
                                        </td>



                                    </tr>
                                </thead>

                                <thead class="listHeaderBottom">

                                    <tr class="headingHolder">
                                        <td>
                                        </td>
                                        <td class="filterFields">
                                            <div class="custCol-12">
                                                <input type="text" id="searchbycode" name="searchbycode" autocomplete="off" placeholder="Enter Employee Code">
                                            </div>
                                        </td>
                                        <td class="filterFields">
                                            <div class="custCol-12">
                                                <input type="text" id="search_key" name="search_key" autocomplete="off" placeholder="Enter Employee Name">
                                            </div>
                                        </td>
                                        <td>

                                            <div class="custCol-12">
                                                <select class="job_position" name="job_position" id="job_position">
                                                    <option value="">All</option>
                                                    @foreach ($job_positions as $job_position)
                                                    <option value="{{$job_position->id}}"><?php echo str_replace('_', ' ', $job_position->name); ?></option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </td>


                                    </tr>

                                </thead>

                                <tbody class="employee_list" id='employee_list'>
                                    @include('training/emp_result')
                                </tbody>

                            </table>
                        </div>
                        <div class="commonLoaderV1"></div>
                    </div>

                </div>
            </div>
        </form>

        <form action="" id="frmaddguest">
            <div class="empTypeDtl" id="guestEmp">
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Name</label>
                            <input type="text" name="guest_name" maxlength="250" placeholder="Enter Name" id="guest_name">
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Email</label>
                            <input type="text" name="guest_email" maxlength="100" placeholder="Enter Email" id="guest_email">
                        </div>
                    </div>
                    <div class="custCol-3">
                        <div class="inputHolder">
                            <label title="Phone No. is used for future evaluations please enter with care">Phone<span title="Phone No. is used for future evaluations please enter with care"> **</span></label>
                            <input type="text" name="guest_phone" placeholder="Phone" id="guest_phone" title="Phone No. is used for future evaluations please enter with care" maxlength="50" class="numberwithdot">
                        </div>
                    </div>
                    <div class="custCol-1">
                        <a href="javascript:void(0)" class="commonBtn bgGreen addBtn addGuestBtn" style="margin-top: 24px;">Add</a>
                    </div>
                </div>
            </div>
        </form>
        
        
        <div class="addedemployees">
            <h3>Added Participants (MTG Employees)</h3>
            <div class="listHolderType1" style="margin-top: 20px;">
                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Code</td>
                                <td>Employee Name</td>
                                <td>Job Position</td>
                                <td>Is Organizer</td>
                                <td>Remove</td>
                            </tr>
                        </thead>

                        <tbody id="selectedemplist">
                            <tr><td>No Employee Selected</td></tr>
                        </tbody>
                    </table>

                </div>					
            </div>
        </div>

        <div class="addedguest">
            <h3>Added Participants (New Employees)</h3>
            <div class="listHolderType1" style="margin-top: 20px;">
                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Name</td>
                                <td>Email</td>
                                <td>Phone</td>
                                <td>Remove</td>
                            </tr>
                        </thead>

                        <tbody id="addedguestlist">
                            <tr><td>No Employee Added</td></tr>
                        </tbody>
                    </table>

                </div>					
            </div>
        </div>




        <div class="custRow">
            <div class="custCol-6">
                <div class="commonCheckHolder clsChkPadding">
                    <label >
                        <input id="chkAgree" class="selectUnits" type="checkbox">
                        <span></span><em>I agree that details entered is 100% correct</em>
                    </label>
                </div>
            </div>
        </div>

        <div class="custRow">
            <div class="custCol-4">
                <input type="button" value="Create" id="btnCreate" onclick="return funBeforeSubmission();" class="commonBtn bgGreen addBtn" id="sub">
            </div>
        </div>
    </div>

    <div class="infoBox" style="display: none;">

    </div>

</div>
<script>
    $(document).ready(function () {

        var timepicker = $('#start_time').timepicker({'timeFormat': 'H:i', 'disableTextInput': true, 'step': '15'});
        $('#duration').timepicker({'timeFormat': 'H:i', 'disableTextInput': true, 'step': '15'});
        $('#start_time').on('changeTime', function () {
            $('#duration').val('');
            var str_time = ($(this).val());
            var str_time_exploded = str_time.split(':');
            var str_time_hr = str_time_exploded[0];
            var str_time_min = str_time_exploded[1];

            if (str_time_min > 0)
            {
                var max_duration = (parseInt(23) - parseInt(str_time_hr)) + ":" + (parseInt(60) - parseInt(str_time_min));
            }
            else
            {
                var max_duration = (parseInt(24) - parseInt(str_time_hr)) + ":00";
            }
            //$('#duration').timepicker({'timeFormat': 'H:i', 'disableTextInput': true,'step': '15',maxTime: max_duration});
            $('#duration').timepicker('option', 'maxTime', max_duration);
        });

        $("#frmcreatemeeting").validate({
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
                start_time: {required: true},
                duration: {required: true},
                repeat_number_week: {required: true},
                guest_name: {required: true},
                guest_email: {required: true, email: true},
                guest_phone: {required: true, number: true},
            },
            messages: {
                title: "Enter Title",
                description: "Enter Description",
                start_date: "Select Start Date",
                end_date: "Select End Date",
                start_time: "Select Start Time",
                duration: "Select Duration",
                repeat_number_week: "Select repeat duration",
                guest_name: "Enter Name",
                guest_email: {
                    required: "Enter Email ID",
                    email: "Enter a valid mail id",
                },
                guest_phone: {
                    required: "Enter Phone Number",
                    number: "Enter Number Only",
                }
            }
        });

        $("#frmaddguest").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                guest_name: {required: true},
                guest_email: {required: true, email: true},
                guest_phone: {required: true, number: true},
            },
            messages: {
                guest_name: "Enter Name",
                guest_email: {
                    required: "Enter Email ID",
                    email: "Enter a valid mail id",
                },
                guest_phone: {
                    required: "Enter Phone Number",
                    number: "Enter Number Only",
                }
            }
        });


        $(".selectallday").change(function () {
            if (this.checked) {
                $(".divtime").hide();
            } else {
                $(".divtime").show();
            }
        });

        $('body').on('click', '.infoBox a', function () {
            $(".infoBox").hide();
        });

        $('body').on('change', '#meeting_room', function () {

            if (!$("#frmcreatemeeting").valid()) {
                $("#meeting_room").val('-1');
                return;
            }

            if (daily_selected_days.length > 0) {
                var arrdaily = JSON.stringify(daily_selected_days);
                $("#daily_selected_days").val(arrdaily);
            }
            if (weekly_selected_days.length > 0) {
                var arrweekly = JSON.stringify(weekly_selected_days);
                $("#weekly_selected_days").val(arrweekly);
            }
            if (selected_dates.length > 0) {

                var arrdates = JSON.stringify(selected_dates);
                $("#selected_custom_dates").val(arrdates);
            }

            $.ajax({
                type: 'POST',
                url: 'checkroomavailability',
                data: {start_date: $("#start_date").val(),
                    start_time: $("#start_time").val(),
                    duration: $("#duration").val(),
                    end_date: $("#end_date").val(),
                    repeat_option: $("#repeat_option").val(),
                    weekly_selected_days: $("#weekly_selected_days").val(),
                    daily_selected_days: $("#daily_selected_days").val(),
                    selected_custom_dates: $("#selected_custom_dates").val(),
                    meeting_room: $("#meeting_room").val()},
                success: function (data) {
                    var result = data.meetings;

                    if (result.length > 0) {

                        var msg = "<h3>Training room " + $("#meeting_room :selected").text() + " already engaged for meetings/training</h3> <ul>";
                        for (var i = 0; i < result.length; i++) {
                            msg = msg + "<li><span>" + result[i].title + " from " + result[i].start + " to " + result[i].end + "</span></li>";
                        }

                        msg = msg + "</ul><a class='commonBtn addBtn bgRed' rel='No'>Close</a>";
                        $(".infoBox").html(msg);
                        $(".infoBox").show();
                    }

                }
            });

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

        $("#repeat_date").datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: '+1D',
            dateFormat: 'dd-mm-yy',
        });
        if ($("#start_date").val() != '') {
            $("#end_date").datepicker("option", "minDate", $("#start_date").val());
        }

    });

</script>


<script>
    var arrEmployeeList = [];

    $(document).ready(function () {
        $('.meeting_mtg_employees').hide();
        $('#guestEmp').hide();
        $('#search_key').bind('keyup', function () {
            search();
        });
        $('#job_position').on('change', function () {
            search();
        });

        $('#searchbycode').bind('keyup', function () {
            search();
        });

        $(".nameup").on('click', function () {
            $('#sortordname').val('ASC');
            $('#sortordjob').val('');
            $('#sortordcode').val('');
            search();
        });

        $(".namedown").on('click', function () {
            $('#sortordname').val('DESC');
            $('#sortordjob').val('');
            $('#sortordcode').val('');
            search();
        });

        $(".jobup").on('click', function () {
            $('#sortordname').val('');
            $('#sortordjob').val('ASC');
            $('#sortordcode').val('');
            search();
        });

        $(".jobdown").on('click', function () {
            $('#sortordname').val('');
            $('#sortordjob').val('DESC');
            $('#sortordcode').val('');
            search();
        });

        $(".codeup").on('click', function () {
            $('#sortordname').val('');
            $('#sortordjob').val('');
            $('#sortordcode').val('ASC');
            search();
        });

        $(".codedown").on('click', function () {
            $('#sortordname').val('');
            $('#sortordjob').val('');
            $('#sortordcode').val('DESC');
            search();
        });

        $('#page-limit').on("change", function () {
            search();
        });

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');
            $('#search_key').val('');
            $('#job_position').val('');
            $('#searchbycode').val('');
            $('#page-limit').val(10);
            search();
        });

        $('body').on('click', '.chkemployee', function () {
            if ($(this).is(":checked")) {

                var empid = $(this).attr('id');
                if (!$("#frmcreatemeeting").valid()) {
                    $('#' + empid).attr('checked', false);
                    return;
                }

                if (daily_selected_days.length > 0) {
                    var arrdaily = JSON.stringify(daily_selected_days);
                    $("#daily_selected_days").val(arrdaily);
                }
                if (weekly_selected_days.length > 0) {
                    var arrweekly = JSON.stringify(weekly_selected_days);
                    $("#weekly_selected_days").val(arrweekly);
                }
                if (selected_dates.length > 0) {

                    var arrdates = JSON.stringify(selected_dates);
                    $("#selected_custom_dates").val(arrdates);
                }


                $.ajax({
                    type: 'POST',
                    url: 'checkemployeeavailability',
                    data: {start_date: $("#start_date").val(),
                        start_time: $("#start_time").val(),
                        duration: $("#duration").val(),
                        end_date: $("#end_date").val(),
                        repeat_option: $("#repeat_option").val(),
                        weekly_selected_days: $("#weekly_selected_days").val(),
                        daily_selected_days: $("#daily_selected_days").val(),
                        selected_custom_dates: $("#selected_custom_dates").val(),
                        empid: empid},
                    success: function (data) {
                        var result = data.meetings;

                        if (result.length > 0) {

                            var msg = "<h3>Employee " + $("#name_" + empid).val() + " already engaged in meetings/trainings </h3><ul>";
                            for (var i = 0; i < result.length; i++) {
                                msg = msg + "<li>" + result[i].title + " from " + result[i].start + " to " + result[i].end + "</li>";
                            }

                            msg = msg + "</ul><a class='commonBtn addBtn bgRed' rel='No'>Close</a>";
                            $(".infoBox").html(msg);
                            $(".infoBox").show();

                        }

                    }
                });

                addtolist($(this).attr('id'));

            } else {
                remove($(this).attr('id'));
            }

            $(".commonLoaderV1").hide();
        })

        
        $('body').on('click', '#btnCreate', function () {
            
            if (!$('#frmcreatemeeting').valid()) {
                return false;
            }
            
            

            if (arrEmployeeList.length == 0 && guests.length == 0) {
                alert("No participants added");
                return false;
            }
            
            if ($("#chkAgree").prop('checked') == false) {
                alert("Please tick the agreement that the details are 100% correct");
                return false;
            }

            var blnConfirm= confirm('Are you sure submit?');
            
            if(!blnConfirm){
                return false;
            }

            $(".commonLoaderV1").show();
            
            if (arrEmployeeList.length > 0) {

                var arremp = JSON.stringify(arrEmployeeList);
                $("#empdetails").val(arremp);
            }
            if (selected_dates.length > 0) {

                var arrdates = JSON.stringify(selected_dates);
                $("#selected_custom_dates").val(arrdates);
            }
            if (guests.length > 0) {

                var arrguests = JSON.stringify(guests);
                $("#selected_guests").val(arrguests);
            }
            if (daily_selected_days.length > 0) {
                var arrdaily = JSON.stringify(daily_selected_days);
                $("#daily_selected_days").val(arrdaily);
            }
            if (weekly_selected_days.length > 0) {
                var arrweekly = JSON.stringify(weekly_selected_days);
                $("#weekly_selected_days").val(arrweekly);
            }
            
            $("#frmcreatemeeting").submit();
            
        });

    });

    function search()
    {
        var search_key = $('#search_key').val();
        var job_position = $('#job_position').val();
        var searchbycode = $('#searchbycode').val();
        var sortordname = $('#sortordname').val();
        var sortordjob = $('#sortordjob').val();
        var sortordcode = $('#sortordcode').val();
        var pagelimit = $('#page-limit').val();
        var strids = '';
        if (arrEmployeeList.length > 0) {
            for (var i = 0; i < arrEmployeeList.length; i++) {
                strids = strids + arrEmployeeList[i].emp_id + ',';
            }
        }

        $.ajax({
            type: 'POST',
            url: 'createtraining',
            data: {search_key: search_key, job_position: job_position, searchbycode: searchbycode, sortordname: sortordname, sortordjob: sortordjob, sortordcode: sortordcode, strids: strids, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.employee_list').html('');
                    $('.employee_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.employee_list').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

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
            profilepic: $("#profilepic_" + employeeid).val(),
            code: $("#code_" + employeeid).val(),
            designation: $("#position_" + employeeid).val(),
            is_organizer: 0,
        }

        if (intItemDuplicate != 1) {
            arrEmployeeList.push(arraData);
            showselectedemps();
        }
    }

    function remove(employeeid) {
        for (var i = 0; i < arrEmployeeList.length; i++) {
            if (employeeid == arrEmployeeList[i].emp_id) {
                arrEmployeeList.splice(i, 1);
                $('#' + employeeid).attr('checked', false);
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
                strHtml += '<tr><td>' + arrEmployeeList[i].code + '</td><td>' + arrEmployeeList[i].designation + '</td><td>' + arrEmployeeList[i].emp_name + '</td>\n\
                            <td><input name="is_organizer" type="radio" id="' + arrEmployeeList[i].emp_id + '" class="isOrganizerchk"></td><td><a href="javascript:remove(' + arrEmployeeList[i].emp_id + ')">Remove</a></td></tr>';
            }
            $("#selectedemplist").html(strHtml);
        }
    }
    $('.addedemployees').show();
    $('.addedguest').show();
    $(".mtgEmpchk").on('click', function () {
        $('#guestEmp').hide();
        $('.meeting_mtg_employees').show();
    });
    $(".guestEmpchk").on('click', function () {
        $('.meeting_mtg_employees').hide();
        $('#guestEmp').show();
    });


    var selected_dates = [];
    $(".repeat_date_btn").on('click', function () {
        var selected_date = $('#repeat_date').val();
        var dup_date = 0;

        for (var i = 0; i < selected_dates.length; i++) {
            if (selected_date == selected_dates[i]) {
                dup_date = 1;
            }
        }
        if (dup_date != 1) {
            $('.selectedDate').append('<div class="date" id="date_' + selected_date + '">' + selected_date + '<a href="javascript:removedate(' + "'" + selected_date + "'" + ')">X</a></div>');
            selected_dates.push(selected_date);
            $('#repeat_date').val('');
        }
        //$('#selected_custom_dates').val(JSON.stringify(selected_dates));
    });


    function removedate(selected_date) {
        $("#date_" + selected_date).remove();
        for (var i = 0; i < selected_dates.length; i++) {
            if (selected_date == selected_dates[i]) {
                selected_dates.splice(i, 1);
            }
        }
    }

    var guests = [];
    var guest_count = [];
    $(".addGuestBtn").on('click', function () {

        if (!$('#frmaddguest').valid()) {
            return false;
        }

        var guest_name = $('#guest_name').val();
        var guest_email = $('#guest_email').val();
        var guest_phone = $('#guest_phone').val();

        var number_of_guests = guest_count.length;
        var arraData = {
            guest_id: number_of_guests,
            guest_name: guest_name,
            guest_email: guest_email,
            guest_phone: guest_phone,
        }
        if (guest_name != '' && guest_email != '' && guest_phone != '')
        {
            guests.push(arraData);
            guest_count.push(1);
            $('#guest_name').val('');
            $('#guest_email').val('');
            $('#guest_phone').val('');
        }
        showaddedguests();
    });

    function showaddedguests()
    {
        $("#addedguestlist").html('<tr><td>No New Employee Added<td></tr>');
        if (guests.length > 0) {
            var strHtml = '';
            for (var i = 0; i < guests.length; i++) {
                strHtml += '<tr><td>' + guests[i].guest_name + '</td><td>' + guests[i].guest_email + '</td><td>' + guests[i].guest_phone + '</td>\n\
                            <td><a href="javascript:removeguest(' + guests[i].guest_id + ')">Remove</a></td></tr>';
            }
            $("#addedguestlist").html(strHtml);
        }
    }

    function removeguest(guest_id) {
        for (var i = 0; i < guests.length; i++) {
            if (guest_id == guests[i].guest_id) {
                guests.splice(i, 1);
            }
        }
        showaddedguests();
    }
    $('body').on('click', '.isOrganizerchk', function () {
        var employeeid = $(this).attr('id');

        for (var i = 0; i < arrEmployeeList.length; i++) {
            if (employeeid == arrEmployeeList[i].emp_id) {
                //if ($('.isOrganizerchk').is(':checked')) {
                arrEmployeeList[i].is_organizer = 1;
            } else {
                arrEmployeeList[i].is_organizer = 0;
            }
        }


    });

    var daily_selected_days = [];
    $(".dailyMeetingchk").on('click', function () {
        var checked_day = $(this).attr('id');
        var day = checked_day.split('_');
        day = day[1];
        if ($(this).is(':checked')) {
            daily_selected_days.push(day);
        }
        else
        {
            daily_selected_days.splice($.inArray(day, daily_selected_days), 1);
        }
    });

    var weekly_selected_days = [];
    $(".weeklyMeetingchk").on('click', function () {
        var checked_day = $(this).attr('id');
        var day = checked_day.split('_');
        day = day[1];
        if ($(this).is(':checked')) {
            weekly_selected_days.push(day);
        }
        else
        {
            weekly_selected_days.splice($.inArray(day, weekly_selected_days), 1);
        }
    });

    function funBeforeSubmission() {

     
    }
</script>
@endsection