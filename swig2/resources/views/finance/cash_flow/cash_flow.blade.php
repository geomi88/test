@extends('layouts.main')
@section('content')
<style>
    .clstotal{
        right: 0;
        position: relative;
        width: inherit;
        display: inline-block;
        float: right;
        margin-top: -87px;
        font-size: 17px;
    }
</style>
<div class="infoBarV1 salesFilter">
    <form action="{{ url('finance/getcustomcashflow') }}" method="post" id="frmCashFLow">
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
        
    
    </form>
    <div class="customClear"></div>
    
    <div class="clstotal" >
        <ul>
            <li>Total Income :<span>{{ Customhelper::numberformatter($totalincome)}}</span></li>
            <li>Total Expense :<span>{{ Customhelper::numberformatter($totalexpense)}}</span></li>
            <li>Difference :<span>{{ Customhelper::numberformatter($difference)}}</span></li>
            <li><a href="javascript:void(0);" onclick="window.open('{{Url("finance/remittance_report?type=notpaid")}}','_blank')">Pending Amount :<span>{{ Customhelper::numberformatter($tot_pending)}} <b><span style="color: #e03131;font-size: 20px;">({{ $total_count }})</span></b></span></a></li>
        </ul>

    </div>
</div>

<div class="graphTypeV2">

    <div class="custRow">
        <div id="branch_sales_graph_container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>



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
            text: 'Daily Income & Expenditure Graph'
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
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            funExpenseReport(this.date);
                        }
                    }
                }
            }
        },

        series: <?php echo $graph_data; ?>
    });
    
//    For future reference point click
   /* plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            alert('Category: ' + this.name + ', value: ' + this.y);
                        }
                    }
                }
            }
        },*/
    
    $("#from_date").datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '1950:c',
         changeMonth: true,
         changeYear: true,
         onSelect: function(selected) {
            $("#to_date").datepicker("option","minDate", selected)
            $("#frmCashFLow").submit();
         }
     });

     $("#to_date").datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '1950:c',
         changeMonth: true,
         changeYear: true,
         onSelect: function(selected) {
            $("#from_date").datepicker("option","maxDate", selected)
            $("#frmCashFLow").submit();
         }
     });

    function funExpenseReport(date){
        if(date!=''){
            window.open('{{url("finance/remittance_report")}}?date='+ date, "_blank"); 
        }
    }

</script>

@endsection