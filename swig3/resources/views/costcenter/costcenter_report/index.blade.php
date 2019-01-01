@extends('layouts.main')
@section('content')

<div class="infoBarV1 supervisorsalesgraph">
    <div class="salesFilter">
    <form action="{{ url('costcenter_report/getbranchwisecost') }}" method="post" id="frmbranchcost">
    
           
        <div class="custCol-4">
            <div class="inputHolder">
                <label>Cost Name</label>
                <select  name="selectedcostid" id="selectedcostid">
                    <option selected value=''>All</option>
                    @foreach ($costnames as $costname)
                    <option <?php if($costname->id==$selectedcostid){ echo "selected";}?> value="{{$costname->id}}"><?php echo str_replace("_", " ", $costname->cost_name)?></option>
                    @endforeach
                </select>
                <input type="hidden" value="<?php if($selectedcostid==''){ echo "All";}else{ echo str_replace("_", " ", $selectedcostname);}?>" id="namehidden">
                <span class="commonError"></span>
            </div>
        </div>
            
    
    <div class="totalCount" style="margin-top: -8px;">
        <ul>
            <li>Total Branches :<span>{{$totalcount}}</span></li>
            <li>Total Expense :<span>{{$totalcost}}</span></li>
        </ul>

    </div>
    </form>
    </div>
    <div class="customClear"></div>
</div>
    
<div class="graphTypeV2">
    <div class="custRow">
        
        <div id="supervisor_wise_sales" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>

<script>
    $(document).ready(function () {
    var costname=$("#selectedcostid :selected").text();
    Highcharts.chart('supervisor_wise_sales', {
        chart: {
            height:<?php echo $branchcount;?>,
            type: 'bar'
        },
        
        title: {
            text: 'Cost Center Report'
        },
       
        xAxis: {
            categories: <?php echo $arrbranches;?>,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Expense',
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
            name: 'Expense ('+ costname +')',
            data: [<?php echo $arrsales;?>],
            color :  '#D26B7F',
        }]
    });


    $('#selectedcostid').on('change', function () {
    
        $("#frmbranchcost").submit();
    });
     
     });
</script>

@endsection