@extends('layouts.main')
@section('content')
    <div class="fieldGroup meetingHolder">
        <header class="pageTitle">
            <h1>Agenda <span>Details</span></h1>
        </header>
        <!--<div class="btmSpace">
            <a class="filterBtn bgBlue" href="#agenda">View Agenda</a>
            <a class="filterBtn bgGreen" href="">Create Agenda</a>
            <div class="customClear"></div>
        </div>-->
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Title</label>
                    <p class="dataElements">{{$agenda_details->title}}</p>
                </div>
            </div>
        </div>

        

        <div class="custRow">
            <div class="custCol-3">
                <div class="inputHolder">
                    <label>Start Date</label>
                    <p class="dataElements"><?php echo date("d-m-Y", strtotime($agenda_details->start_date)); ?></p>
                </div>
            </div>

<!--            <div class="custCol-3">
                <div class="inputHolder">
                    <label>Time</label>
                    <p class="dataElements"><?php echo date("g:i a", strtotime($agenda_details->start_date)); ?></p>
                </div>
            </div>-->

           
        </div>

        <div class="custRow">
            <div class="custCol-3">
                <div class="inputHolder">
                    <label>End Date</label>
                    <p class="dataElements"><?php echo date("d-m-Y", strtotime($agenda_details->end_date)); ?></p>
                </div>
            </div>

<!--            <div class="custCol-3">
                <div class="inputHolder">
                    <label>Time</label>
                    <p class="dataElements"><?php echo date("g:i a", strtotime($agenda_details->end_date)); ?></p>
                </div>
            </div>-->

           
        </div>

        
        <?php if($agenda_details->owner_id == Session::get('login_id') || $agenda_details->owner_id == Session::get('login_id')) {?>
        <form action="{{ action('Meeting\AgendaController@update') }}" method="post" id="frmupdateagenda">
        <input type="hidden" name="agenda_id" value="{{$agenda_details->id}}">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Description</label>
                        <textarea name="description" id="description">{{$agenda_details->description}}</textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Status</label>
                        <select class="" name="agenda_status" id="agenda_status">
                            <option value="-1">Select</option>
                            <option value="2" <?php if($agenda_details->status == 2) {?> selected<?php }?>>Pending</option>
                            <option value="3" <?php if($agenda_details->status == 3) {?> selected<?php }?>>Completed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" id="btnUpdate" class="commonBtn bgGreen addBtn" id="sub">
                </div>
            </div>
        </form>
        <?php } ?>
        

    </div>

    <!--<a class="commonBtn bgRed addBtn btnUpload" href="javascript:void(0)">Delete</a>-->

@endsection