@extends('layouts.main')
@section('content')
<div class="infoBarV1 supervisorsalesgraph">
    <form action="{{ url('branchsales/getcollectiondifference') }}" method="post" id="frmsupervisorgraph">
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
                
                 <div class="custCol-3">
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
                <div class="custCol-3">
                    <div class="inputHolder"> 
                        <label>Difference</label> 
                        <select class="commoSelect" name="diff_id" id="diff_id">
                            <option <?php echo ($diff_id == "cash_diff") ? "selected" : "" ?> value='cash_diff'>Cash Difference</option> 
                            <option <?php echo ($diff_id == "bank_diff") ? "selected" : "" ?> value='bank_diff'>Bank Difference</option>
                        </select> 
                    </div>
                </div>
                
            </div>
        </div>
        <div class="totalCount">
            <ul>
                <li>Total Supervisors :<span>{{$totalcount}}</span></li>
                <li>Total Collection Difference :<span>{{$actualsale}}</span></li>
            </ul>
            
        </div>
    </form>
    <div class="customClear"></div>
</div>
    
<div class="graphTypeV2">
    <div class="custRow">
        
        <div id="supervisor_wise_sales" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>

<script>
    Highcharts.setOptions({

    lang: {
     
      thousandsSep: ','
    }
});
    Highcharts.chart('supervisor_wise_sales', {
        chart: {
            height:<?php echo $supervisorcount;?>,
            type: 'bar'
        },
        
        title: {
            text: 'Collection Difference'
        },
       
        xAxis: {
            categories: <?php echo $arrsupervisors;?>,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Collection Difference',
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
            name: 'Collection Difference',
            //data: [<?php echo $arrsales;?>],
            data: <?php echo $arrsales;?>,
            color :  '#D26B7F',
             point: {
                    events: {
                        click: function (event) {
                            getSalesDetails(this.employee_id, this.first_day, this.last_day, this.region_id,this.diff_id);
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
     
      $('#diff_id').on('change', function () {
         $("#frmsupervisorgraph").submit();
    });
     
      function getSalesDetails(empid, from_date, to_date, region_id,diff_id) {
         
 
        var form = document.createElement("form");
        form.action = "<?php  echo url('branchsales/getCollectionDifferenceDetails'); ?>";
        form.method = "post";
        form.target="_blank";
        var input1 = document.createElement("input");
        var input2 = document.createElement("input");
        var input3 = document.createElement("input");
        var input4 = document.createElement("input");
        var input5 = document.createElement("input");
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
        input5.name = "diff_id";
        input5.type = "hidden";
        input5.value = diff_id;
        form.appendChild(input5);

        document.body.appendChild(form);
        form.submit();


        // window.open(strurl, '_blank'); 
    }  
     
</script>

@endsection