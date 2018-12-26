@extends('layouts.main')
@section('content')
<div class="innerContent">
    <div class="fieldGroup meetingHolder">
        <header class="pageTitle">
            <h1>Meeting <span>Details</span></h1>
        </header>
        <!--<div class="btmSpace">
            <a class="filterBtn bgBlue" href="#agenda">View Agenda</a>
            <a class="filterBtn bgGreen" href="">Create Agenda</a>
            <div class="customClear"></div>
        </div>-->
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <a class="commonBtn bgGreen addBtn btnUpload" href="{{ URL::to('meeting/agenda/add_agenda', ['id' => $meeting_details->id]) }}">Add Agenda</a>
                </div>
            </div>
        </div>
        <div class="viewMeetingWrapper">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Meeting Title</label>
                        <p class="dataElements">{{$meeting_details->title}}</p>
                    </div>
                    <div class="inputHolder">
                        <label>Description</label>
                        <p class="dataElements">{{$meeting_details->description}}</p>
                    </div>
                </div>


                <div class="custCol-6">
                    <div class="custRow">
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Start Date</label>
                                <p class="dataElements"><?php echo date("d-m-Y", strtotime($meeting_details->start_date)); ?></p>
                            </div>
                        </div>


                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Time</label>
                                <p class="dataElements"><?php echo date("g:i a", strtotime($meeting_details->start_date)); ?></p>
                            </div>
                        </div>
                    </div>
                    <!--<div class="custCol-3">
                        <div class="inputHolder">
                            <label>Duration</label>
                            <p class="dataElements">12Hr</p>
                        </div>
                    </div>-->


                    <div class="custRow">
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>End Date</label>
                                <p class="dataElements"><?php echo date("d-m-Y", strtotime($meeting_details->end_date)); ?></p>
                            </div>
                        </div>

                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Time</label>
                                <p class="dataElements"><?php echo date("g:i a", strtotime($meeting_details->end_date)); ?></p>
                            </div>
                        </div>

                        <!--<div class="custCol-3">
                            <div class="inputHolder">
                                <label>Duration</label>
                                <p class="dataElements">12Hr</p>
                            </div>
                        </div>-->
                    </div>

                    <!--<div class="custRow">
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <label>Repeat</label>
                                <p class="dataElements">monthly</p>
                            </div>
                        </div>
                    </div>-->



                    <!--<div class="custRow">
                        <div class="custCol-3">
                            <div class="inputHolder">
                                <label>Participants Type</label>
                                <p class="dataElements">Guest Employee</p>
                            </div>
                        </div>
                    </div>-->



                    <!--<div class="empTypeDtl" id="guestEmp">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Name</label>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Email</label>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Phone</label>
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>-->

                    <div class="inputHolder">
                        <label>Meeting Room</label>
                        <p class="dataElements">{{$meeting_details->meeting_room}}</p>
                    </div>

                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Note</label>
                    <p class="dataElements">{{$meeting_details->note}}</p>
                </div>
            </div>
        </div>
        <?php if ($meeting_details->owner_id != Session::get('login_id')) { ?>
            <form action="{{ action('Meeting\MeetingController@updateparticipation') }}" method="post" id="frmupdateparticipation">
                <input type="hidden" name="meeting_id" value="{{$meeting_details->id}}">

                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <div class="commonCheckHolder">
                                <label>
                                    <input name="empType" value="empReason" class="empType" type="checkbox">
                                    <span></span>
                                    <em>Not Participating</em>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="empTypeDtl" id="empReason">
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Reason</label>
                                <textarea name="reason" id="reason"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn" id="sub">
                        </div>
                    </div>
                </div>
            </form>
        <?php } else { ?>
            <form action="{{ action('Meeting\MeetingController@addnote') }}" method="post" id="frmaddnote">
                <input type="hidden" name="meeting_id" value="{{$meeting_details->id}}">

                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder">
                            <div class="commonCheckHolder">
                                <label>
                                    <input name="notechk" value="notechkval" class="notechk" type="checkbox">
                                    <span></span>
                                    <em>Add Note</em>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="noteDtl" id="notechkval" style="display:none">
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Note</label>
                                <textarea name="note" id="note"></textarea>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <input type="submit" value="Add" id="btnAdd" class="commonBtn bgGreen addBtn" id="notebtn">
                        </div>
                    </div>

                </div>
            </form>
        <?php } ?>
        <h3>Added Participants</h3>
        <div class="listerType1">
            <table style="width: 100%;" cellspacing="0" cellpadding="0">
                <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>Emp Code</td>
                        <td>Emp Name</td>
                        <td>Mail Id</td>
                        <td>Status</td>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>{{$meeting_details->owner_username}}</td>
                        <td>{{$meeting_details->owner_first_name}} {{$meeting_details->owner_last_name}} (Meeting Creator)</td>
                        <td>{{$meeting_details->owner_email}}</td>
                        <td>Participating</td>
                    </tr>
                    <?php foreach ($meeting_attendees as $employee) { ?>
                        <tr>

                            <td>{{$employee->username}}</td>
                            <td>{{$employee->first_name}} {{$employee->last_name}} <?php if ($employee->is_organizer == 1) { ?> (Organizer)<?php } ?></td>
                            <td>{{$employee->email}}</td>
                            <td><?php if ($employee->availability_status == 1) { ?> Participating <?php } else { ?> <em class="warningToolTip">
                                        <div class="warningDtl">
                                            <h5>{{$employee->comment}}</h5>

                                        </div>
                                    </em> Not Participating<?php } ?></td>
                        </tr>
                    <?php } ?>


                </tbody>
            </table>
            <div class="commonLoaderV1"></div>
        </div>

        <h3>Agenda</h3>
        <div class="listerType1">
            <table style="width: 100%;" cellspacing="0" cellpadding="0" id="agenda">
                <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>Date</td>
                        <td>Agenda Title</td>
                        <td>Employee Name</td>
                        <td>Status</td>
                        <td>Update</td>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    foreach ($meeting_agendas as $meeting_agenda) {
                        if ($meeting_agenda->status == 1) {
                            $agenda_status = "New";
                        }
                        if ($meeting_agenda->status == 2) {
                            $agenda_status = "Pending";
                        }
                        if ($meeting_agenda->status == 3) {
                            $agenda_status = "Completed";
                        }
                        ?>
                        <tr>
                            <td><?php echo date("d-m-Y", strtotime($meeting_agenda->start_date)); ?></td>
                            <td>{{$meeting_agenda->title}}</td>
                            <td>{{$meeting_agenda->first_name}} {{$meeting_agenda->last_name}}</td>
                            <td><?php echo $agenda_status; ?></td>
                            <td><?php if ($meeting_agenda->attendee_id == Session::get('login_id') || $meeting_details->owner_id == Session::get('login_id')) { ?>
                                    <a class="btnAction action bgBlue" href="{{ URL::to('meeting/agenda/edit', ['id' => Crypt::encrypt($meeting_agenda->id)]) }}">Update</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>

            <div class="commonLoaderV1"></div>
        </div>

    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        var notechk = $('.notechk:checked').val();
        $('#' + notechk).show();
        $('.notechk').change(function () {
            $('.noteDtl').hide();
            if ($('.notechk').is(':checked')) {
                var notechk = $(this).val();
                $('#' + notechk).show();
            }

        });
    });
</script>
@endsection