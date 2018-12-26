@extends('layouts.main')
@section('content')
<header class="pageTitle">
    <h1>Sales <span>Analysis</span></h1>
</header>

<div class="salesNot">

    <div class="detailsNot">
        <h3>Recent Actions</h3>
        <?php foreach ($notifications as $notification) { ?>
            <div class="list call">
                <a href="{{ URL::to('kpi/analyst_discussion/view', ['id' => Crypt::encrypt($notification->discussion_parent_id)]) }}"><h4>{{$notification->subject}}</h4></a>
                <p>{{$notification->first_name}} replied to your message</p>
            </div>
        <?php } ?>
    </div>
</div>
<div class="customClear"></div>
<div class="salesFilter">
    <div class="leftSide">
        <label>Filter By : <?php if($search_key==''){echo "Month";}else{echo $search_key;} ?></label>
        <form method="post" action="">
            <input type="hidden" name="search_key" value="Month">
            <input type="submit" class="filterBtn bgBlue" value="Month">
        </form>
        <form method="post" action="">
            <input type="hidden" name="search_key" value="Week">
            <input type="submit" class="filterBtn bgGreen" value="Week">
        </form>
        <form method="post" action="">
            <input type="hidden" name="search_key" value="Quarter">
            <input type="submit" class="filterBtn bgRed" value="Quarter">
        </form>
    </div>
    <!--<div class="rightSide">
        <label>Filter By date</label>
        <div class="choose">
            12-03-17
        </div>
        <div class="choose">
            12-03-17
        </div>
        <a class="btnFilter bgBlue" href="">Ok</a>
        <div class="customClear"></div>
    </div>-->
    <div class="customClear"></div>
</div>

<div class="salesDetails">
    <div class="salesPerformance allSalesContents">
        <div class="graphSingle">
            <h3 class="commonHeadingV2 mainGraphHeading">Sales Target <span>Graph</span></h3>
            <form method="post" action="" class="mainGraphFilter">
                        <select name="selected_year" id="selected_year" onchange="this.form.submit()">
                            <?php 
                            $current_year = date('Y');
                            for($y=$current_year;$y>=($current_year-1);$y--) {
                            ?>
                            <option value="<?php echo $y;?>" <?php if($selected_year==$y) { echo "selected";}?>><?php echo $y;?></option>
                            <?php } ?>
                        </select>
                        <?php if($selected_quarter=='') { $selected_quarter=ceil(date('n')/3) ;}?>
                        <select name="selected_quarter" id="selected_quarter" onchange="this.form.submit()">
                            <!--<option value="0" <?php if($selected_quarter==0) { echo "selected";}?>>Yearly</option>-->
                            <option value="1" <?php if($selected_quarter==1) { echo "selected";}?>>First Quarter</option>
                            <option value="2" <?php if($selected_quarter==2) { echo "selected";}?>>Second Quarter</option>
                            <option value="3" <?php if($selected_quarter==3) { echo "selected";}?>>Third Quarter</option>
                            <option value="4" <?php if($selected_quarter==4) { echo "selected";}?>>Fourth Quarter</option>
                        </select>
            </form>
            <div class="customClear"></div>
            <div class="salesContentHolder">
                @widget('AllBranchSalesTarget', ['search_key' => "$search_key",'selected_year' => "$selected_year",'selected_quarter' => "$selected_quarter"])
               
            </div>
        </div>


    </div>

    @widget('BranchesSales', ['search_key' => "$search_key"])



    <div class="customClear"></div>

    <div class="custRow">
        <div class="custCol-6">
            <h3 class="commonHeadingV2">Top <span>Sales</span></h3>
            <div class="salesContentHolder">
                @widget('HighSales', ['search_key' => "$search_key"])
            </div>
        </div>
        <div class="custCol-6">
            <h3 class="commonHeadingV2">Low <span>Sales</span></h3>
            <div class="salesContentHolder">
                @widget('LowSales', ['search_key' => "$search_key"])
            </div>
        </div>
    </div>


</div>
@endsection
