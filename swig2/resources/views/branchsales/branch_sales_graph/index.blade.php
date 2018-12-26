@extends('layouts.main')
@section('content')
<div class="infoBarV1 supervisorsalesgraph">
    <form action="{{ url('branchsales/getbranchsale') }}" method="post" id="frmbranchsale">
        <div class="leftSection">
            <div class="dates_div custCol-12">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>From</label>
                        <input type="text" name="from_date" id="from_date" value="{{$periodStartDate}}" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>To</label>
                        <input  type="text" name="to_date" id="to_date" value="{{$periodEndDate}}" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Region</label>
                        <select class="commoSelect" name="region_id" id="region_id">
                            <option value=''>Select Region</option>
                            @foreach ($regions as $region)
                            <option <?php echo ($region->id == $region_id) ? "selected" : "" ?>  value='{{ $region->id }}'>{{$region->region_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
            </div>
        </div>

        <div class="totalCount">
            <ul>
                <li>Total Branches :<span>{{$totalcount}}</span></li>
                <li>Total Sales :<span>{{$actualsale}}</span></li>
            </ul>

        </div>
    </form>
    <div class="customClear"></div>
</div>
<div class="viewMeetingWrapper">
    <div class="custRow">
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Total Region Sale</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_total_sale) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Sale Amount</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_sale_amount) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Tax Amount</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_tax_amount) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Total Cash Sale</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_cash_sale) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Cash Collection</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_cash_collection) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Cash Difference</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_cashdifference) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Total Bank Card Sale</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_bank_sale) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Bank Collection</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_bank_collection) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Bank Difference</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_bankdifference) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Credit/Free Sale</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_credit_sale) }}</p>
            </div>
        </div>
        
        
        
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Net Difference</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_difference) }}</p>
            </div>
        </div>
        <div class="custCol-2">
            <div class="inputHolder">
                <label>Meal Consumption</label>
                <p class="dataElements">{{ Customhelper::numberformatter($totalSale->t_meal_consumption) }}</p>
            </div>
        </div>
    </div>
</div> 
<div class="graphTypeV2">
    <div class="custRow">

        <div id="branch_wise_sales" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>

<script>
    Highcharts.setOptions({

        lang: {

            thousandsSep: ','
        }
    });
    Highcharts.chart('branch_wise_sales', {
        chart: {
            height:<?php echo $supervisorcount; ?>,
            type: 'bar'
        },

        title: {
            text: 'Sales By Branch'
        },

        xAxis: {
            categories: <?php echo $arrbranches; ?>,
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
                name: 'Sales',
                keys: ['name', 'y', 'sliced', 'selected'],
                data: <?php echo $arrsales; ?>,
                color: '#D26B7F',
                point: {
                    events: {
                        click: function (event) {
                            var id = this.id;
                            load_report(id);
                        }
                    }
                }
            }]
    });
    function load_report(id) {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var url = "{{ url('mis/pos_sales/supervisorreports') }}";
        window.open(url + '?from_date=' + from_date + '&to_date=' + to_date + '&branch_name=' + id);
    }

    $("#from_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function (selected) {
            $("#to_date").datepicker("option", "minDate", selected)
            $("#frmbranchsale").submit();
        }
    });

    $("#to_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function (selected) {
            $("#from_date").datepicker("option", "maxDate", selected)
            $("#frmbranchsale").submit();
        }
    });
    $('#region_id').on('change', function () {
        $("#frmbranchsale").submit();
    });
</script>

@endsection