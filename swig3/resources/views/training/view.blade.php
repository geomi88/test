@extends('layouts.main')
@section('content')
<div class="innerContent">
    <div class="fieldGroup meetingHolder">
        <header class="pageTitle">
            <h1>Training <span>Details</span></h1>
        </header>

       
        <div class="viewMeetingWrapper">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Training Title</label>
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

                      
                    </div>

                   

                    <div class="inputHolder">
                        <label>Training Room</label>
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
            <form action="{{ action('Training\TrainingController@updateparticipation') }}" method="post" id="frmupdateparticipation">
                <input type="hidden" name="training_id" value="{{$meeting_details->id}}">

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
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Reason</label>
                                <textarea name="reason" id="reason"></textarea>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <input type="submit" value="Update" id="btnUpdate" style="margin-top: 108px;" class="commonBtn bgGreen addBtn" id="sub">
                        </div>
                    </div>
                    
                </div>
            </form>
        <?php } else { ?>
            <form action="{{ action('Training\TrainingController@addnote') }}" method="post" id="frmaddnote">
                <input type="hidden" name="training_id" value="{{$meeting_details->id}}">

                <div class="custRow">
                    <div class="custCol-3">
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
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Note</label>
                                <textarea name="note" id="note">{{$meeting_details->note}}</textarea>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <input type="submit" value="Save" id="btnAdd" class="commonBtn bgGreen addBtn" id="notebtn">
                        </div>
                    </div>
                    
                </div>
            </form>
        <?php } ?>
        <h3>Added Participants (MTG Employees)</h3>
        <div class="listerType1" style="min-height: 100px !important;">
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
                        <td>{{$meeting_details->owner_first_name}} {{$meeting_details->owner_last_name}} (Training Creator)</td>
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
            
        </div>
        
        <h3>Added Participants (New Employees)</h3>
        <div class="listerType1">
            <table style="width: 100%;" cellspacing="0" cellpadding="0">
                <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>Name</td>
                        <td>Mail Id</td>
                        <td>Phone</td>
                        <td>Status</td>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($training_guests as $guest)
                    <tr>
                        <td>{{$guest->guest_name}}</td>
                        <td>{{$guest->guest_email}}</td>
                        <td>{{$guest->guest_phone}}</td>
                        <td>Participating</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>


    </div>
    <div class="commonLoaderV1"></div>
</div>

<script>
    jQuery(document).ready(function ($) {
        
        $("#frmaddnote").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                note: {required: true},
            },
            messages: {
                note: "Enter Note",
               
            }
        });
        
        $("#frmupdateparticipation").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                reason: {required: true},
            },
            messages: {
                reason: "Enter Reason",
            }
        });


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