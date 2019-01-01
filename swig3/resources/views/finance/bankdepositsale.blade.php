@extends('layouts.main')
@section('content')

<div class="infoBarV1 salesFilter">
    <form action="{{ url('finance/bankdepositsale') }}" method="post" id="frmDashboard">
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
                    <b>{{number_format($arrSalesData['actualsale'],2)}}</b>
                </li>
                <li>
                    <span>Current Target</span>
                    <b>{{number_format($arrSalesData['target'],2)}}</b>
                </li>
                <li>
                    <span>Difference</span>
                    <b>{{number_format($arrSalesData['difference'],2)}}</b>
                </li>
                <li>
                    <span>Yearly Sale</span>
                    <b>{{number_format($arrSalesData['yearsale'],2)}}</b>
                </li>
                <li>
                    <span>Yearly Target</span>
                    <b>{{number_format($arrSalesData['yeartarget'],2)}}</b>
                </li>
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

<div class="graphTypeV3">
    <div class="custRow">

        <div id="supervisor_wise_sales" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>

<script>
    $(".graphTypeV3").hide();
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
            text: 'Deposit sales'
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
        series: [{
                name: 'Sale',
                data: <?php echo $full_area_graph_data; ?>,
                size: '90%',
                innerSize: '40%',
                showInLegend: true,
                point: {
                    events: {
                        click: function (event) {
                            showsupervisorsales(this.sale_date);
                        }
                    }
                }
            }]
    });

    chart2 = new Highcharts.chart('supervisor_wise_sales', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Supervisor deposits'
        },
        xAxis: {
            categories: [],
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Sales',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            },
        },
        series: [{
                name: '',
                data: [],
                color: '#D26B7F',
            }]
    });

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

    function showsupervisorsales(sale_date) {
        $(".graphTypeV3").show();
        $.ajax({
            type: 'POST',
            url: '../finance/getsupervisorsale',
            data: '&sale_date=' + sale_date,
            success: function (return_data) {

                chart2.update({
                    chart: {
                        height:return_data.supervisorcount,
                        type: 'bar'
                    },
                    xAxis: {
                        categories: return_data.arrsupervisors,
                        title: {
                            text: null
                        }
                    },
                    series: [{
                            name: 'Sales',
                            data: return_data.arrsales,
                            color: '#D26B7F',
                            point: {
                                events: {
                                    click: function (event) {
                                        supervisordaydeposits(this.url);
                                    }
                                }
                            }
                        }]
                });

            }
        });
    }
    function supervisordaydeposits(strurl) {
        window.open(strurl, '_blank'); 
    }


</script>

@endsection