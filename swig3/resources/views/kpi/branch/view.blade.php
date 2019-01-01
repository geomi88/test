@extends('layouts.main')
@section('content')
<?php
$total_sale = $branch_sales->total;
$cash_collection = $branch_sales->cash_collection;
$credit_sale = $branch_sales->credit_sale;
$bank_sale = $branch_sales->bank_sale;
?>


<header class="pageTitle">
    <h1>Sales <span>Analysis</span></h1>
</header>

<div class="customClear"></div>
<div class="salesFilter">
    <div class="leftSide">
        <label>Filter By</label>
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
    <!--        <div class="rightSide">
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
            <h3 class="commonHeadingV2"><span>Branch</span> Sales</h3>
        <div class="salesContentHolder branchSale">
            <div class="leftSide">

                <h4>{{$branch_details->name}}</h4>
                <div class="empList saleAddress">
                    <div class="details">
                        <p>Code : <span>{{$branch_details->branch_code}}</span></p>
                        <p>Address : <span>{{$branch_details->address}}</span></p>
                    </div>
                    <div class="customClear"></div>
                </div>
                <?php foreach ($branch_workers as $branch_worker) { ?>
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src="{{$branch_worker->profilepic}}" alt="">
                        </figure>
                        <div class="details">
                            <b>{{$branch_worker->first_name}} {{$branch_worker->alias_name}}</b>
                            <p>Designation : <span>{{$branch_worker->resource_type}}</span></p>

                        </div>
                        <div class="customClear"></div>
                        <figure class="flagHolder">
                            <img src="{{ URL::asset('images/flags/'.$branch_worker->flag_pic) }}" alt="Flag">
                            <figcaption>{{$branch_worker->country_name}}</figcaption>
                        </figure>
                        <div class="customClear"></div>
                    </div>
                <?php } ?>


            </div>
            <div class="rightSide">
                <?php //if ($branch_sales->total != '') { ?>
                <div class="mapHandle">
                    <span>Current {{$search_key}} : </span>
                    <?php if ($profit_data['profit'] == 'plus') { ?>
                        <img src="{{ URL::asset('images/iconTriangle.png')}}" alt="Map">
                    <?php } else { ?>
                        <img src="{{ URL::asset('images/iconDownTriangle.png')}}" alt="Map">
                    <?php } ?>
<!--                            <strong>{{$branch_sales->total}}</strong>
                <span>Compared to {{$previous_total_sales->total}} Last {{$search_key}}</span>-->
                    <strong><?php if ($branch_sales->total != '') { ?>{{number_format($branch_sales->total,2)}}<?php } else { ?>0<?php } ?></strong>
                    <span>Previous {{$search_key}} : <?php if ($previous_total_sales->total != '') { ?>{{number_format($previous_total_sales->total,2)}}<?php } else { ?>0<?php } ?></span>
                </div>
                <?php //} ?>
                <a class="branchBtns phone" id="contact_by_phone" href="javascript:void();">Contact By Phone</a>
                <a class="branchBtns chat" id="contact_by_chat" href="javascript:void();">Contact By Chat</a>
                <a class="branchBtns history" href="{{ URL::to('kpi/analyst_discussion', ['branch_id' => Crypt::encrypt($branch_details->id)]) }}">Action History</a>
            </div>
        </div>
        </div>




    </div>


    @widget('BranchesSales', ['search_key' => "$search_key"])


    <div class="customClear"></div>
</div>

<div class="custRow">
    <div class="custCol-12">
        <h3 class="commonHeadingV2"><span>Sales</span> Chart</h3>
        <div class="salesFilter">
            <div class="leftSide">
                <label>Filter By</label>
                <form method="post" action="">
                    <?php if ($selected_quarter == '') {
                        $selected_quarter = ceil(date('n') / 3);
                    } ?>
                    <select name="selected_quarter" id="search_key" onchange="this.form.submit()">
                        <option value="1" <?php if ($selected_quarter == 1) {
                        echo "selected";
                    } ?>>First Quarter</option>
                        <option value="2" <?php if ($selected_quarter == 2) {
                        echo "selected";
                    } ?>>Second Quarter</option>
                        <option value="3" <?php if ($selected_quarter == 3) {
                        echo "selected";
                    } ?>>Third Quarter</option>
                        <option value="4" <?php if ($selected_quarter == 4) {
                        echo "selected";
                    } ?>>Fourth Quarter</option>
                    </select>
                </form>
            </div>
            <div class="customClear"></div>
        </div>
        <div class="salesContentHolder">
            <div id="branch_sales_graph_container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div></div>
</div>

<div class="customClear"></div>
<div class="contentArea">
    <div class="innerContent">
        <header class="pageTitle">
            <h1>Branch<span> Sales Analysis</span></h1>
            <div class="subString">Branch Start Date : <?php echo $current_sales_details['branch_start_date']?></div>
            <div class="subString">Total Working Days : <?php echo $current_sales_details['workingdays']?></div>
        </header>
        <div class="salesHandles">
            <h3>Current Quarter Sales Analysis</h3>
            <div class="branchSales">
                <div class="salesRow">
                    <div class="cells">
                        <span>Quarterly Target Amount</span>
                    </div>
                    <div class="cells">
                        <span>Till Date Achieved Sales</span>
                    </div>
                    <div class="cells">
                        <span>Till Date Achieved %</span>
                    </div>
                    <div class="cells">
                        <span>Till Date Variance %</span>
                    </div>
                </div>
                <div class="salesRow">
                    <div class="cells">
                        <p><?php echo number_format($current_sales_details['quarter_target_amount']);?></p>
                    </div>
                    <div class="cells">
                        <p><?php echo number_format($current_sales_details['quarter_achieved_sales']);?></p>
                    </div>
                    <div class="cells">
                        <p><?php echo number_format(round($current_sales_details['quarter_achieved_sales_percentage'],2));?>%</p>
                    </div>
                    <div class="cells">
                        <p><?php echo number_format(round($current_sales_details['quarter_sales_variance_percentage'],2));?>%</p>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="salesHandles">
            <h3>Current Month Sales</h3>
            <div class="branchSales">
                <div class="salesRow">
                    <div class="cells">
                        <span>Monthly Target Amount</span>
                    </div>
                    <div class="cells">
                        <span>Till Date Achieved Sales</span>
                    </div>
                    <div class="cells">
                        <span>Till Date Achieved %</span>
                    </div>
                    <div class="cells">
                        <span>Till Date Variance %</span>
                    </div>
                </div>
                <div class="salesRow">
                    <div class="cells">
                        <p>798991</p>
                    </div>
                    <div class="cells">
                        <p>798991</p>
                    </div>
                    <div class="cells">
                        <p>798991</p>
                    </div>
                    <div class="cells">
                        <p>798991</p>
                    </div>
                </div>
            </div>
        </div>-->

        <div class="salesHandles">
            <h3>Per Day Sales Analysis</h3>
            <div class="branchSales">
                <div class="salesRow">
                    <div class="cells">
                        <span>Daily Target Amount</span>
                    </div>
                    <div class="cells">
                        <span>Achieved Sales</span>
                    </div>
                    <div class="cells">
                        <span>Achieved %</span>
                    </div>
                    <div class="cells">
                        <span>Variance %</span>
                    </div>
                </div>
                <div class="salesRow">
                    <div class="cells">
                        <p><?php echo number_format(round($current_sales_details['daily_target_amount'],2));?></p>
                    </div>
                    <div class="cells">
                        <p><?php echo number_format(round($current_sales_details['daily_achieved_sales'],2));?></p>
                    </div>
                    <div class="cells">
                        <p><?php echo round($current_sales_details['daily_achieved_sales_percentage'],2);?>%</p>
                    </div>
                    <div class="cells">
                        <p><?php echo round($current_sales_details['daily_sales_variance_percentage'],2);?>%</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="salesHandles statusLister">
            <h3>Bottom Sale Line Analysis</h3>
            <div class="branchSales">
                <div class="salesRow saleLineHeading">
                    <div class="cells">
                        <span>Bottom Sale Line (Per Day)</span>
                    </div>
                    <div class="cells">
                        <span>Bottom Sale Line (Per Month)</span>
                    </div>
                    <div class="cells">
                        <span>Achieved Sales (Per Day)</span>
                    </div>
                    <div class="cells">
                        <span>Achieved Sales (Per Month)</span>
                    </div>
                    <div class="cells">
                        <span>Status</span>
                    </div>
                </div>
                <div class="salesRow">
                    <div class="cells">
                        <p><?php echo number_format(round($branch_details->bottom_sale_line/30,2));?></p>
                    </div>
                    <div class="cells">
                        <p><?php echo number_format(round(($branch_details->bottom_sale_line),2));?></p>
                    </div>
                    <div class="cells">
                        <p><?php echo number_format(round($current_sales_details['daily_achieved_sales'],2));?></p>
                    </div>
                    <div class="cells">
                         <?php 
                       
           $num_days=0;
           for($i=1;$i<=date('d');$i++){
              $num_days=$i; 
           }
            ?>
                        <p><?php echo number_format(round(($current_sales_details['daily_achieved_sales']*$num_days),2));?></p>
                    </div>
                    <div class="cells">
                        <p class="statusSales <?php if($current_sales_details['daily_achieved_sales'] >= ($branch_details->bottom_sale_line/30)) { echo "plus";} else { echo "down";}?>"><?php if($current_sales_details['daily_achieved_sales'] >= ($branch_details->bottom_sale_line/30)) { echo "Success";} else { echo "Close";}?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="salesHandles">
            <h3>Shift Sales Analysis</h3>
            <div class="branchSales">
                <div class="salesRow">
                    <div class="cells">
                        <span>Morning Shift Sales Analysis</span>
                        <div class="salesRow">
                            <div class="cells">
                                <div class="shiftTable">
                                    <p>Total Morning Sales</p>
                                    <em><?php echo number_format($current_sales_details['current_quarter_morning_shift_sales']);?></em>
                                </div>
                                <div class="shiftTable">
                                    <p>Achieved %</p>
                                    <em><?php echo round($current_sales_details['morning_shift_achieved_sales_percentage'],2);?>%</em>
                                </div>
                                <div class="shiftTable">
                                    <p>Average Sales Per Shift</p>
                                    <em><?php echo number_format(round($current_sales_details['morning_shift_average_sales'],2));?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cells">
                        <span>Evening Shift Sales Analysis</span>
                        <div class="salesRow">
                            <div class="cells">
                                <div class="shiftTable">
                                    <p>Total Evening Sales</p>
                                    <em><?php echo number_format($current_sales_details['current_quarter_evening_shift_sales']);?></em>
                                </div>
                                <div class="shiftTable">
                                    <p>Achieved %</p>
                                    <em><?php echo round($current_sales_details['evening_shift_achieved_sales_percentage'],2);?>%</em>
                                </div>
                                <div class="shiftTable">
                                    <p>Average Sales Per Shift</p>
                                    <em><?php echo number_format(round($current_sales_details['evening_shift_average_sales'],2));?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modalHolderV2 phone chat">
    <a class="btmModalClose" href="javascript:void(0)">X</a>
    <h2><div class='chat_text'></div></h2>
    <form action="{{ url('kpi/analyst_discussion/store') }}" method="post" id="create_analayst_disussion">
        <input type="hidden" id="branch_id" name="branch_id" value="{{$branch_details->id}}">
        <input type="hidden" id="type" name="type" value="">
        <input type="hidden" id="login_id" name="login_id" value="<?php echo Session::get('login_id'); ?>">
        <label>Subject</label>
        <div class="inputHolder">
            <input type="text" name="subject" id="subject">
        </div>
        <input type="hidden" name='selected_contacts' id='selected_contacts'>
        <label>Contact With</label>
        <div class="inputHolder">
            <div class="selectedContacts">

            </div>

            <input type="text" id="contact_persons" name="contact_persons" readonly>
        </div>

        <div class="contactPersonList">
            <a href="javascript:void(0)">Close (X)</a>
            <div class="customClear"></div>
            <div class="contactPersonHolder">
                <?php
                foreach ($branch_contact_persons as $branch_contact_person) {
                    $flag_pic = URL::asset('images/flags/' . $branch_contact_person->flag_pic);
                    echo '<div class="empList" id="' . $branch_contact_person->emp_id . '">
                                <figure class="imgHolder">
                                    <img src="' . $branch_contact_person->profilepic . '" alt="Profile">
                                </figure>
                                <div class="details">
                                    <b class="emp_name">' . $branch_contact_person->first_name . ' ' . $branch_contact_person->alias_name . '</b>
                                    <p>Designation : <span>' . str_replace("_", " ", $branch_contact_person->resource_type) . '</span></p>

                                </div>
                                <div class="customClear"></div>
                                <figure class="flagHolder">
                                    <img src="' . $flag_pic . '" alt="Flag">
                                    <figcaption>' . $branch_contact_person->country_name . '</figcaption>
                                </figure>
                                <div class="customClear"></div>
                            </div>';
                }
                ?>
            </div>
        </div>

        <label>Message</label>
        <div class="inputHolder">
            <textarea name="message" id="message"></textarea>
        </div>
        <input type="submit" value="Save" class="btnSubmit">
    </form>
</div>
<div class="overlay"></div>

<!--<script>
    Highcharts.chart('branch_sales_graph_container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Branch Sales'
        },
        subtitle: {
            text: '<?php echo $search_key ?>'
        },
        xAxis: {
            categories: [
                'Total Sale',
                'Cash Collection',
                'Credit Sale',
                'Bank Sale'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Amount'
            }
        },
        tooltip: {
            headerFormat: '<table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0"></td>' +
                    '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
                name: 'Sales',
                data: [<?php echo $total_sale . "," . $cash_collection . "," . $credit_sale . "," . $bank_sale; ?>]
            }]
    });
</script>-->

<script>
    Highcharts.setOptions({
        global: {
                useUTC: false
        }
    });
    Highcharts.chart('branch_sales_graph_container', {
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Quarter Sales'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {// don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Amount'
            },
            min: 0
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            //pointFormat: '{point.x:%e. %b}: {point.z} : {point.y:.2f} '
            pointFormat: '{point.x:%e. %b}: {point.y:.2f} '
        },
        plotOptions: {
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
//        series: [{
//                name: 'Total Sales',
//                data: <?php echo $graph_data; ?>
//            }]
        series: <?php echo $graph_data; ?>
    });
</script>



<script>
    var selected_contacts = [];
    $('#contact_persons').on('click', function () {
        $('.contactPersonList, .overlay').show();

    });
    $('.empList').on('click', function () {
        var emp_id = $(this).attr("id");
        var login_id = $('#login_id').val();
        var emp_name = $(this).find(".emp_name").text();
        $('.selectedContacts').append('<div class="contactSelect" ><a href="javascript:void(0)" rel="#' + emp_id + '">X</a>' + emp_name + '</div>');

        emp_id = parseInt(emp_id);
        login_id = parseInt(login_id);
        selected_contacts.push(emp_id);
        selected_contacts.push(login_id);
        $('#selected_contacts').val(JSON.stringify(selected_contacts));
        $('.contactPersonList').hide();
        $(this).hide();
    });
    $('#contact_by_phone').on('click', function () {
        $('.chat_text').text('Contact By Phone');
        $('#type').val('CALL');
    });
    $('#contact_by_chat').on('click', function () {
        $('.chat_text').text('Contact By Chat');
        $('#type').val('CHAT');
    });

    $('body').on('click', '.contactSelect a', function () {
        var emp_id = $(this).attr("rel");
        selected_contacts.splice($.inArray(emp_id, selected_contacts), 1);
        $(emp_id).show();
        $(this).parent().remove();
    });

    $('.btmModalClose').on('click', function () {
        $('.modalHolderV2,.overlay ').hide();
    });
</script>

@endsection
