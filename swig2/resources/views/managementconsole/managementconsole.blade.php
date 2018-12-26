@extends('layouts.main')
@section('content')

<div class="infoBarV1 salesFilter">
    <form action="{{ url('managementconsole/getmonthlysale') }}" method="post" id="frmDashboard">
        <div class="leftSection">
            <div class="dates_div custCol-8">
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label>From</label>
                        <input type="text" name="from_date" id="from_date" value="{{$arrSalesData['PeriodStartDate']}}" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label>To</label>
                        <input  type="text" name="to_date" id="to_date" value="{{$arrSalesData['PeriodEndDate']}}" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rightSection">
            <ul class="infoListV1">
                <li <?php
                if ($arrSalesData['difference'] < 0) {
                    echo "class='active'";
                }
                ?>>
                    <span>Current Sale</span>
                    <b>{{ Customhelper::numberformatter($arrSalesData['actualsale'])}}</b>
                </li>
                <li>
                    <span>Current Target</span>
                    <b>{{ Customhelper::numberformatter($arrSalesData['target'])}}</b>
                </li>
                <li>
                    <span>Difference</span>
                    <b>{{ Customhelper::numberformatter($arrSalesData['difference'])}}</b>
                </li>
                <li>
                    <a href="{{ url('finance/sales_variance_report') }}"> <span>Yearly Sale</span></a>
                    <b>{{ Customhelper::numberformatter($arrSalesData['yearsale'])}}</b>
                </li>
                <li>
                    <a href="{{ url('finance/minimum_sales_plan') }}"> <span>Yearly Target</span></a>
                    <b>{{ Customhelper::numberformatter($arrSalesData['yeartarget'])}}</b>
                </li>

                <?php
                if ($arrSalesData['target'] == 0) {
                    $currentDifference = 0;
                } else {
                    $currentDifference = (($arrSalesData['actualsale'] - $arrSalesData['target']) / $arrSalesData['target']) * 100;
                }

                if ($arrSalesData['yeartarget'] == 0) {
                    $yearlySalesPerc = 0;
                } else {
                    $yearlySalesPerc = ($arrSalesData['yearsale'] / $arrSalesData['yeartarget']) * 100;
                }

                if ($arrSalesData['target'] == 0) {

                    $currentSalesPerc = 0;
                } else {

                    $currentSalesPerc = ($arrSalesData['actualsale'] / $arrSalesData['target']) * 100;
                }
                ?>

                <?php
                $year = date("Y");
                $month = date('m');
                $curdays = 0;
//$subdays=0;
                $days = 0;
                $remainingdays = 0;
                for ($i = 1; $i <= 12; $i++) {
                    if ($i > $month) {
                        $days += cal_days_in_month(CAL_GREGORIAN, $i, $year);
                    } else if ($i == $month) {
                        $curdays += cal_days_in_month(CAL_GREGORIAN, $i, $year);
                    } else {
                        // $subdays+=cal_days_in_month(CAL_GREGORIAN,$i,$year);
                    }
                }

                $remainingdays = $days + $curdays - date('j');
                ?>

            </ul>

            <div class="droplistV1 left bgSelect">
                Total Employees ({{$totEmployees}})
                <input type="text" placeholder="Emp List" readonly>
                <ul class="dropEmpListing">
                    @foreach ($allEmployees as $emp)
                    <li>
                        <a href="{{ URL::to('employeewithjobposition', ['id' => Crypt::encrypt($emp->job_id)]) }}"><?php echo str_replace("_", " ", $emp->job_name); ?> ({{$emp->emp_count}})</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="droplistV1 right bgSelect">
<?php echo "Total Branches (" . count($allBranches) . ")"; ?>
                <input type="text" placeholder="Emp List" readonly>
                <ul class="dropEmpListing">
                    @foreach ($allBranches as $branch)
                    <li>
                        <a href="{{ URL::to('kpi/branch/view', ['id' => Crypt::encrypt($branch->id)]) }}">{{$branch->br_name}}</a>
                    </li>
                    @endforeach

                </ul>
            </div>

        </div>
    </form>
    <div class="customClear"></div>
</div>

<div class="graphTypeV2">

    <div class="custRow">
        <div id="branch_sales_graph_container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>

<div class="graphTypeV2 chartRegion">

    <div class="leftSide">
        <div class="custRow">
            <div id="low_sales_graph_container" style=""></div>
            <a style="display:none;" href="javascript:void(0);" onclick="javascript:get_branch_wise_sale();" class="commonBtn bgGreen viewBtn" id="sale_summary_button">Sales Summary</a>
            <input type="hidden" value="" id="branch_sales_region">
        </div>
    </div>
    <div class="rightSide clsBranches">

    </div>
    <div class="customClear"></div>
</div>

<script>
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    Highcharts.chart('branch_sales_graph_container', {
        chart: {
            type: 'area'
        },
        title: {
            text: 'Sales Performance'
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
            labels: {
                formatter: function () {
                    return this.value;
                }
            }
        },
        tooltip: {
            headerFormat: '{point.x:%b %e}<br>',
            pointFormat: '{series.name} produced <b>{point.y:.0f}</b>'
        },
        plotOptions: {
            area: {
                pointStart: 1,
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 2,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        },
        series: <?php echo $full_area_graph_data; ?>
    });


// Create the chart
    chart = new Highcharts.Chart('low_sales_graph_container', {
        chart: {
            type: 'pie',
            backgroundColor: 'transparent'
        },
        title: {
            text: 'Regional Sales'
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        plotOptions: {
            pie: {
                shadow: false
            },
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.point.name + '</b>: ' + this.y + ' %';
            }
        },
        series: [{
                name: 'Region',
                data: <?php echo $full_pi_graph_data; ?>,
                size: '90%',
                innerSize: '40%',
                showInLegend: true,
                point: {
                    events: {
                        click: function (event) {
                            getbranches(this.id, this.name);
                        }
                    }
                }
            }]
    });

    $(".clsBranches").html("Select Region From Graph");

    function getbranches(region_id, region_name) {
        $.ajax({
            type: 'POST',
            url: '../managementconsole/getbranches',
            data: '&region_id=' + region_id + '&region_name=' + region_name +
                    '&from_date=' + $("#from_date").val() + '&to_date=' + $("#to_date").val(),
            success: function (return_data) {
//                console.log(return_data);
                if (return_data != -1) {
                    $(".clsBranches").html(return_data);
                } else {
                    $(".clsBranches").html('');
                }

            }
        }).done(function () {
            if ($("#reg_enc_id").val() != '') {
                $("#branch_sales_region").val($("#reg_enc_id").val());
                $("#sale_summary_button").show();
            }
        });
    }

    $("#from_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function (selected) {
            $("#to_date").datepicker("option", "minDate", selected)
            $("#frmDashboard").submit();
        }
    });

    $("#to_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function (selected) {
            $("#from_date").datepicker("option", "maxDate", selected)
            $("#frmDashboard").submit();
        }
    });



    function filterbranch() {
        $("#branchpop").html('');

        var region_id = $("#regionid").val();
        var region_name = $("#regionname").val();

        var search = $("#search").val();


        $.ajax({
            type: 'POST',
            url: '../managementconsole/filterbranches',
            data: '&region_id=' + region_id + '&region_name=' + region_name + '&search=' + search +
                    '&from_date=' + $("#from_date").val() + '&to_date=' + $("#to_date").val(),
            success: function (return_data) {

                if (return_data != -1) {
                    $("#branchpop").html(return_data);
                } else {
                    $("#branchpop").html('');
                }

            }
        });
    }
    function get_branch_wise_sale() {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var region_id = $("#branch_sales_region").val();
        var url = "{{ url('branchsales/getbranchsale') }}";
        window.open(url + "?from_date=" + from_date + "&to_date=" + to_date + "&region_name=" + region_id, "_blank");
    }
</script>

@endsection