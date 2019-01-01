@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle chatTitle">
        <h1>Discussion <span>List</span></h1>
    </header>
    <div class="detailsNot allList">
        <?php foreach ($discussions as $discussion) { ?>
            <?php if ($discussion->type == 'CALL') { ?>
                <div class="list call">
            <?php } else if ($discussion->type == 'CHAT') { ?>
                <div class="list msg">
            <?php } else if ($discussion->type == 'MAIL') { ?>
                <div class="list msg">
            <?php } ?>
                    <h4><a href="{{ URL::to('kpi/analyst_discussion/view', ['id' => Crypt::encrypt($discussion->id)]) }}">{{$discussion->subject}}</a></h4>
                        <p>{{$discussion->message}}</p>
                    </div>
                    
                <?php } ?>
            </div>
        </div>
        @endsection
