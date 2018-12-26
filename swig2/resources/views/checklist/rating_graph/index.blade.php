@extends('layouts.main')
@section('content')
<div class="infoBarV1 supervisorsalesgraph">
    <form action="{{ url('checklist/getratinggraph') }}" method="post" id="frmsupervisorgraph">
    <div class="leftSection">
            <div class="dates_div custCol-12">
                <div class="custCol-4" style="">
                    <div class="inputHolder">
                        <label>From</label>
                        <input type="text" name="from_date" id="from_date" value="{{$periodStartDate}}" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
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
<!--        <div class="totalCount">
            <ul>
                <li>Total :<span>{{$totalcount}}</span></li>
            </ul>
        </div>-->
    </form>
    <div class="customClear"></div>
</div>
    
<div class="graphTypeV2">
    <div class="custRow">
        
        <div id="supervisor_wise_sales" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>

<script>
    Highcharts.chart('supervisor_wise_sales', {
        chart: {
            type: 'bar'
        },
        
        title: {
            text: 'Check Points Rating'
        },
       
        xAxis: {
            categories: ['Good','Average','Bad'],
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Check Points Rating',
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
            name: 'Rating Count',
            data: <?php echo $arrcount;?>,
            color :  '#D26B7F',
            point: {
                events: {
                    click: function (event) {
                        getwarnings(this.url);
                    }
                }
            }
        }]
    });


    $("#from_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function(selected) {
           $("#to_date").datepicker("option","minDate", selected)
           $("#frmsupervisorgraph").submit();
        }
    });

    $("#to_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function(selected) {
           $("#from_date").datepicker("option","maxDate", selected)
           $("#frmsupervisorgraph").submit();
        }
    });
     
    $('#region_id').on('change', function () {
         $("#frmsupervisorgraph").submit();
    });
    
    function getwarnings(strurl) {
        window.open(strurl, '_blank'); 
    }
      
</script>

@endsection