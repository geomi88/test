@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle chatTitle">
        <h1>Sales Analysis<span> Discussions</span></h1>
    </header>
    <div class="chatHolder">
        <div class="chatDtl">
            <?php if ($discussion_topic->creator_id == Session::get('login_id')) { ?>
                <div class="otherChat">
            <?php } else { ?>    
                <div class="myChat">
            <?php } ?>
                <b><?php echo $discussion_topic->first_name." ".$discussion_topic->alias_name; ?></b>
                <span>{{ $discussion_topic->message }}</span>
                <em><?php echo date("d-m-Y H:m:s", strtotime($discussion_topic->created_at)); ?></em>
            </div>
            <?php foreach ($messages as $message) { ?>
                <?php if ($message->creator_id == Session::get('login_id')) { ?>
                    <div class="otherChat">
                    <?php } else { ?>    
                        <div class="myChat">
                        <?php } ?>
                        <b><?php echo $message->first_name." ".$message->alias_name; ?></b>
                        <span><?php echo $message->message; ?></span>
                        <em><?php echo date("d-m-Y H:m:s", strtotime($message->created_at)); ?></em>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="replyChat">
            <form method="post" action="{{ url('kpi/analyst_discussion/sendreply') }}">
            <input type="hidden" name="branch_id" value="{{ $discussion_topic->branch_id }}">
            <input type="hidden" name="parent_id" value="{{ $discussion_topic->id }}">
            <textarea placeholder="Comment" name="message" id="message" required></textarea>
            <input type="submit" class="commonBtn bgGreen addBtn" value="Send">
            </form>
        </div>
    </div>

</div>

@endsection
