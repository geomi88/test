@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function ()
    {
        $('.persons').hide();
        $('.tabContents').hide();
    });

    function get_supervisor(branch_id)
    {
        $('.persons').hide();
        $('.tabContents').hide();
        $("#shiftid li").removeClass('selected');
        $('.shift_tab').show();
        $('#branch_id_val').val(branch_id);
        $.ajax({
            type: 'POST',
            url: 'resource_listing/get_supervisor',
            data: {branch_id: branch_id},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    
                    $(".branch_per").show();
                    $('.branch_manager').html(return_data);
                }
                else
                {
                    
                    $(".branch_per").show();
                    $('.branch_manager').html('<p class="noData">No Supervisor Found</p>');
                }
            },
            complete: function () {
                $(".commonLoaderV1").hide();
            }
        });
        show_shifts();
    }
    
    function get_region_manager(region_id)
    {
        $('.persons').hide();
        $('.tabContents').hide();
       
        $.ajax({
            type: 'POST',
            url: 'resource_listing/get_region_manager',
            data: {region_id: region_id},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    
                    $(".reg_per").show();
                    $('.region_manager').html(return_data);
                }
                else
                {
                    
                    $(".reg_per").show();
                    $('.region_manager').html('<p class="noData">No Region Manager Found</p>');
                }
            },
            complete: function () {
                $(".commonLoaderV1").hide();
            }
        });
    }
    
    function get_area_manager(area_id)
    {
        $('.persons').hide();
        $('.tabContents').hide();
        $.ajax({
            type: 'POST',
            url: 'resource_listing/get_area_manager',
            data: {area_id: area_id},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                   
                    $(".area_per").show();
                    $('.area_manager').html(return_data);
                }
                else
                {
                    
                    $(".area_per").show();
                    $('.area_manager').html('<p class="noData">No Area Manager Found</p>');
                }
            },
            complete: function () {
                $(".commonLoaderV1").hide();
            }
            
        });
    }
    
    function get_cashier(id){
        
        var branch_id = $('#branch_id_val').val();
        $.ajax({
            type: 'POST',
            url: 'resource_listing/get_cashier',
            data: {branch_id: branch_id, shift_id: id},
			async: false,
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    
                    $(".tabContents").show();
                    $('.allocated_cashiers').html(return_data);
                }
                else
                {
                    
                    $(".tabContents").show();
                    $('.allocated_cashiers').html('<p class="noData">No Cashier Found</p>');
                }
            },
            complete: function () {
                $(".commonLoaderV1").hide();
            }
        });
        
        get_barista(id);
    }

    function get_barista(id)
    {
        $('.allocated_baristas').html('');
        var branch_id = $('#branch_id_val').val();
        
        $.ajax({
            type: 'POST',
            url: 'resource_listing/get_barista',
            data: {branch_id: branch_id, shift_id: id},
			async: false,
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $(".tabContents").show();
                    $('.allocated_baristas').html(return_data);
                }
                else
                {
                    $(".tabContents").show();
                    $('.allocated_baristas').html('<p class="noData">No Barista Found</p>');
                }
            },
            complete: function () {
                $(".commonLoaderV1").hide();
            },
            complete: function () {
                $(".commonLoaderV1").hide();
            }
            
        });
		$(".commonLoaderV1").hide();
        
    }
    
    function show_shifts()
    {
        var branch_id = $('#branch_id_val').val();

        $.ajax({
            type: 'POST',
            url: 'resource_listing/show_shifts',
            data: {branch_id: branch_id},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.shift').html(return_data);
                    
                }
                else
                {
                    $('.shift').html('');
                    
                }
            },
            complete: function () {
                $(".commonLoaderV1").hide();
            }
            
        });

    }

</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Resource <span>Listing</span></h1>

    </header>	


    <div class="resListHolder">
        <nav class="innerNavV1">
            <ul id="myid">
                @foreach ($regions as $region)
                <li><span>+</span><a class="region_id" onclick="get_region_manager({{$region['id']}})" value="{{$region['id']}}">{{$region['name']}}</a>
                    <ul class="all_areas">
                        @foreach ($region['areas'] as $area)
                        <li><span>+</span><a class="area_id" onclick="get_area_manager({{$area['id']}})" value="{{$area['id']}}">{{$area['name']}}</a>

                            <ul>
                                @foreach ($area['branches'] as $branch)
                                <li><a class="branch_id" onclick="get_supervisor({{$branch['id']}})" value="{{$branch['id']}}">{{$branch['name']}}</a></li>
                                @endforeach   
                            </ul>
                        </li>
                        @endforeach  
                    </ul>
                </li>
                @endforeach                                    
            </ul>
        </nav>
        <input type="hidden" id="branch_id_val" value=""/>
        <aside class="innerContentV1">
            <div class="persons reg_per">

                <h3 class="headingV1"><span>Region Manager</span></h3>
                <div class="listContainerV1 region_manager">
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src="images/imgProfileLarge.jpg" alt="Profile">
                        </figure>
                        <div class="details">
                            <b>Name 1</b>
                            <p>Designation : <span>Designation</span></p>
                            <p>Region : <span>--</span></p>
                            <figure class="flagHolder">
                                <img src="images/imgFlagUsa.jpg" alt="Flag">
                                <figcaption>USA</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                        <div class="commonLoaderV1"></div>
                    </div>

                </div>
            </div>
            <div class="persons area_per">
                <h3 class="headingV1"><span>Area Manager</span></h3>
                <div class="listContainerV1 area_manager">
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src="images/imgProfileLarge.jpg" alt="Profile">
                        </figure>
                        <div class="details">
                            <b>Name 1</b>
                            <p>Designation : <span>Designation</span></p>
                            <p>Region : <span>--</span></p>
                            <figure class="flagHolder">
                                <img src="images/imgFlagUsa.jpg" alt="Flag">
                                <figcaption>USA</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>                  

                </div>
            </div>
            <div class="persons branch_per">
                <h3 class="headingV1"><span>Supervisor</span></h3>
                <div class="listContainerV1 branch_manager">
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src="images/imgProfileLarge.jpg" alt="Profile">
                        </figure>
                        <div class="details">
                            <b>Name 1</b>
                            <p>Designation : <span>Designation</span></p>
                            <p>Region : <span>--</span></p>
                            <figure class="flagHolder">
                                <img src="images/imgFlagUsa.jpg" alt="Flag">
                                <figcaption>USA</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>                  

                </div>
                <div class="tabHolderV2 count1 persons shift_tab">
                    <div class="tabBtnV2 themeV1">
                        <ul class="shift" id="shiftid">

                        </ul>
                    </div>
                    <div class="tabContents">
                        <div class="tabList" id="tabS">
                            <h3 class="headingV1"><span>Cashiers </span></h3>
                            <div class="allocated_cashiers">

                            </div>
                            <h3></h3>
                            <h3 class="headingV1"><span>Baristas </span></h3>
                            <div class="allocated_baristas">

                            </div>
                        </div> 

                    </div>
                </div>
            </div>


            <div class="commonLoaderV1"></div>
        </aside>
        <div class="customClear"></div>
    </div>



</div>
<div class="customClear"></div>
<div class="allocations"></div>

@endsection
