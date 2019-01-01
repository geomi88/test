@extends('layouts.main')
@section('content')
<div class="infoBarV1 supervisorsalesgraph">
    <form action="{{ url('finance/getsalescomparison') }}" method="post" id="salescomparisongraph">
    <div class="leftSection">
            <div class="dates_div custCol-12">
                <div class="custCol-3">
                    <div class="inputHolder">
                                <label>Month</label>
                                <select class="commoSelect" name="cmp_month_id" id="cmp_month_id">
                                    <option value=''>Select Month</option>
                                    <option <?php echo ($month_id == 1)  ? "selected" : "" ?>  value='1'>January</option>
                                    <option <?php echo ($month_id == 2)  ? "selected" : "" ?>  value='2'>February</option>
                                    <option <?php echo ($month_id == 3)  ? "selected" : "" ?>  value='3'>March</option>
                                    <option <?php echo ($month_id == 4)  ? "selected" : "" ?>  value='4'>April</option>
                                    <option <?php echo ($month_id == 5)  ? "selected" : "" ?>  value='5'>May</option>
                                    <option <?php echo ($month_id == 6)  ? "selected" : "" ?>  value='6'>June</option>
                                    <option <?php echo ($month_id == 7)  ? "selected" : "" ?>  value='7'>July</option>
                                    <option <?php echo ($month_id == 8)  ? "selected" : "" ?>  value='8'>August</option>
                                    <option <?php echo ($month_id == 9)  ? "selected" : "" ?>  value='9'>September</option>
                                    <option <?php echo ($month_id == 10) ? "selected" : "" ?>  value='10'>October</option>
                                    <option <?php echo ($month_id == 11) ? "selected" : "" ?>  value='11'>November</option>
                                    <option <?php echo ($month_id == 12) ? "selected" : "" ?>  value='12'>December</option>
                                    
                                </select>
                            </div>
                </div>
                <div class="custCol-3">
                    <div class="inputHolder">
                                <label>Year1</label>
                                <select class="commoSelect" name="cmp_year1" id="cmp_year1">
                                    <option value=''>Select Year</option>
                              
                                   @foreach (range( $earliest_year,$latest_year) as $i)
                                    <option <?php echo ($year1 == $i) ? "selected" : "" ?>  value='{{$i}}'>{{$i}}</option>
                                    @endforeach
                                </select>
                            </div>
                </div>
                
                <div class="custCol-3">
                   <div class="inputHolder">
                                <label>Year2</label>
                               <select class="commoSelect" name="cmp_year2" id="cmp_year2">
                                    <option value=''>Select Year</option>
                              
                                   @foreach (range( $earliest_year,$latest_year) as $i)
                                    <option <?php echo ($year2 == $i) ? "selected" : "" ?>  value='{{$i}}'>{{$i}}</option>
                                    @endforeach
                                </select>
                            </div>
                </div> 
            
                <div class="custCol-3">
                   <div class="inputHolder">
                                <label>Region</label>
                                <select class="commoSelect" name="cmp_region_id" id="cmp_region_id">
                                    <option value=''>Select Region</option>
                                   @foreach ($regions as $region)
                                    <option <?php echo ($region->id == $region_id) ? "selected" : "" ?>  value='{{ $region->id }}'>{{$region->region_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                </div> 
        </div>
        </div>
    </form>
    <div class="customClear"></div>
</div>
    


<div class="graphTypeV2 chartRegion">

    <div class="leftSide">
        <div class="custRow">
            <div id="sales_graph_container1" style=""></div>
        </div>
    </div>
    <div class="rightSide">
       <div class="custRow">
            <div id="sales_graph_container2" style=""></div>
        </div> 
    </div>
    <div class="customClear"></div>
</div>

<script>
   
var year1=$('#cmp_year1').val();
var year2=$('#cmp_year2').val();

$(function () {
Highcharts.setOptions({
        lang: {
      
      thousandsSep: ','
    }
});
 
    });
// Create the chart
    chart1 = new Highcharts.Chart('sales_graph_container1', {
        chart: {
            type: 'pie', 
            backgroundColor:'transparent'
        },
        title: {
            text: 'Total Sales '+year1
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
            headerFormat: '',
            pointFormat:  "<b> {point.name} </b>: {point.y:,.2f}"
 
        },
        series: [{
                name: 'Months',
                data: <?php echo $full_pi_graph_data_first; ?>,
                size: '90%',
                innerSize: '40%',
                showInLegend: true,
//                point: {
//                    events: {
//                        click: function (event) {
//                            getbranches(this.id,this.name);
//                        }
//                    }
//                }
            }]
    });
      
    
// Create the chart
    chart2 = new Highcharts.Chart('sales_graph_container2', {
        chart: {
            type: 'pie', 
            backgroundColor:'transparent'
        },
        title: {
            text: 'Total Sales '+year2
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
            headerFormat: '',
             pointFormat:  "<b> {point.name} </b>: {point.y:,.2f}"
        },
        series: [{
                name: 'Months',
                data: <?php echo $full_pi_graph_data_second; ?>,
                size: '90%',
                innerSize: '40%',
                showInLegend: true,
//                point: {
//                    events: {
//                        click: function (event) {
//                            getbranches(this.id,this.name);
//                        }
//                    }
//                }
            }]
    });
    




        $('#cmp_region_id').on('change', function () {
         $("#salescomparisongraph").submit();
        });
         
       $('#cmp_month_id').on('change', function () {
            $("#salescomparisongraph").submit();
           
        });
        
        $('#cmp_year1').on('change', function () {
           $("#salescomparisongraph").submit();
        });
        
       $('#cmp_year2').on('change', function () {
            $("#salescomparisongraph").submit();
        });
      
      function getSalesDetails(empid, from_date, to_date, region_id) {
          
     

        var form = document.createElement("form");
        form.action = "<?php  echo url('branchsales/getSupervisorSalesDetails'); ?>";
        form.method = "post";
        form.target="_blank";
        var input1 = document.createElement("input");
        var input2 = document.createElement("input");
        var input3 = document.createElement("input");
        var input4 = document.createElement("input");
        input1.name = "empid";
        input1.type = "hidden";
        input1.value = empid;
        form.appendChild(input1);
        input2.name = "from_date";
        input2.type = "hidden";
        input2.value = from_date;
        form.appendChild(input2);
        input3.name = "to_date";
        input3.type = "hidden";
        input3.value = to_date;
        form.appendChild(input3);
        input4.name = "region_id";
        input4.type = "hidden";
        input4.value = region_id;
        form.appendChild(input4);

        document.body.appendChild(form);
        form.submit();


        // window.open(strurl, '_blank'); 
    }  
</script>

@endsection