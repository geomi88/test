@extends('layouts.main')
@section('content')
<div class="innerContent">
    <div class="">
        <div class="custRow">
             <div class="totalCount">
                Total Employees :<span>{{$totalcount}}</span>
            </div>
            <div id="employee_by_job" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>
    </div>
</div>
<script>
    Highcharts.chart('employee_by_job', {
        chart: {
            height: <?php echo $jobcount;?>,
            type: 'bar'
        },
        
        title: {
            text: 'Employee By Job Position'
        },
       
        xAxis: {
            categories: <?php echo $arrjob;?>,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Count',
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
            name: 'Employee Count',
            data: <?php echo $arrcount;?>,
            color :  '#D26B7F',
            point: {
                events: {
                    click: function (event) {
                        getemployee(this.url);
                    }
                }
            }
        }]
    });

    function getemployee(strurl) {
        window.open(strurl, '_blank'); 
    }
</script>

@endsection