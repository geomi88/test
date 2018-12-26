@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Branch Attendees Report <span></span></h1>
    </header>
    <div class="innerContent">
        <header class="pageTitle">
            <h1>Branch Attendees Report <span></span></h1>
        </header>	

        <div class="reportV1">
            <ul class="custRow">
                <li class="custCol-1 alignCenter">
                    <b>Shift</b>
                    {{$branch_attendees_data->shift_name}}
                </li>
                <li class="custCol-1 alignCenter">
                    <b>Branch Name</b>
                    {{$branch_attendees_data->branch_name}}
                </li>

                <li class="custCol-7">
                    <b>Employee Name</b>
                    {{$branch_attendees_data->first_name}}{{$branch_attendees_data->middle_name}}{{$branch_attendees_data->last_name}}
                <li class="custCol-7">
                    <b>Total Time</b>
                    {{$branch_attendees_data->total_time}}

            </ul>

            <ul class="custRow">
                <li class="custCol-8">
                    <b>Time In</b>
                    <input type="hidden" name="it" id="it" value="{{$branch_attendees_data->time_in}}">
                    {{$branch_attendees_data->time_in}}
                    <b>Time Out</b><input type="hidden" name="ot" id="ot" value="{{$branch_attendees_data->time_out}}">
                    {{$branch_attendees_data->time_out}}
                    <b>Over Time</b>
                    {{$branch_attendees_data->over_time}}

                </li>
            </ul>
        </div>
    </div>

    @endsection