@extends('layouts.main')
@section('content')

<div class="graphTypeV2 chartRegion">
    <div class="salesFilter">
    <form action="{{ url('cost_analysis/getmonthlysale') }}" method="post" id="frmregiongraph">
    <div class="custRow">
        
            <div class="custCol-3">
                <div class="inputHolder">
                    <label>Month</label>
                    <select  name="cmbmonth" id="cmbmonth">
                        <option <?php if($currentMonth=='01'){ echo "selected";}?> value='01'>January</option>
                        <option <?php if($currentMonth=='02'){ echo "selected";}?> value='02'>February</option>
                        <option <?php if($currentMonth=='03'){ echo "selected";}?> value='03'>March</option>
                        <option <?php if($currentMonth=='04'){ echo "selected";}?> value='04'>April</option>
                        <option <?php if($currentMonth=='05'){ echo "selected";}?> value='05'>May</option>
                        <option <?php if($currentMonth=='06'){ echo "selected";}?> value='06'>June</option>
                        <option <?php if($currentMonth=='07'){ echo "selected";}?> value='07'>July</option>
                        <option <?php if($currentMonth=='08'){ echo "selected";}?> value='08'>August</option>
                        <option <?php if($currentMonth=='09'){ echo "selected";}?> value='09'>September</option>
                        <option <?php if($currentMonth=='10'){ echo "selected";}?> value='10'>October</option>
                        <option <?php if($currentMonth=='11'){ echo "selected";}?> value='11'>November</option>
                        <option <?php if($currentMonth=='12'){ echo "selected";}?> value='12'>December</option>
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
            <div class="custCol-3">
                <div class="inputHolder">
                    <label>Year</label>
                    <select  name="cmbyear" id="cmbyear">
                        
                        <?php 
                            $Year=date('Y');
                            for($startYear=2017;$startYear<=$Year;$startYear++){
                        ?>
                            <option  <?php if($currentYear==$startYear){ echo "selected";}?> value='{{$startYear}}'>{{$startYear}}</option>
                        <?php }?>
                            
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
        
    </div>
    </form>
    <div class="customClear"></div>
</div>

    <div class="leftSide">
        <div class="custRow">
            <div id="region_graph" style=""></div>
        </div>
    </div>
    <div class="rightSide clsBranches">
        
    </div>
    <div class="customClear"></div>
</div>
<div id="divbottom">
<div id="divbrgraph" style="display: none;">

<div class="graphTypeV2">
    <div class="salesFilter">
    <form action="{{ url('cost_analysis/getbranchgraph') }}" method="post" id="frmbranchgraph">
    <div class="custRow">
            <input type="hidden" id="hiddenbranchid" name="hiddenbranchid">
            <div class="custCol-3" style="margin-left: 35px;">
                <div class="inputHolder">
                    <label>Month</label>
                    <select  name="cmbmonth1" id="cmbmonth1">
                        <option <?php if($currentMonth=='01'){ echo "selected";}?> value='01'>January</option>
                        <option <?php if($currentMonth=='02'){ echo "selected";}?> value='02'>February</option>
                        <option <?php if($currentMonth=='03'){ echo "selected";}?> value='03'>March</option>
                        <option <?php if($currentMonth=='04'){ echo "selected";}?> value='04'>April</option>
                        <option <?php if($currentMonth=='05'){ echo "selected";}?> value='05'>May</option>
                        <option <?php if($currentMonth=='06'){ echo "selected";}?> value='06'>June</option>
                        <option <?php if($currentMonth=='07'){ echo "selected";}?> value='07'>July</option>
                        <option <?php if($currentMonth=='08'){ echo "selected";}?> value='08'>August</option>
                        <option <?php if($currentMonth=='09'){ echo "selected";}?> value='09'>September</option>
                        <option <?php if($currentMonth=='10'){ echo "selected";}?> value='10'>October</option>
                        <option <?php if($currentMonth=='11'){ echo "selected";}?> value='11'>November</option>
                        <option <?php if($currentMonth=='12'){ echo "selected";}?> value='12'>December</option>
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
            <div class="custCol-3">
                <div class="inputHolder">
                    <label>Year</label>
                    <select  name="cmbyear1" id="cmbyear1">
                        
                        <?php 
                            for($startYear=2017;$startYear<=$Year;$startYear++){
                        ?>
                            <option  <?php if($currentYear==$startYear){ echo "selected";}?> value='{{$startYear}}'>{{$startYear}}</option>
                        <?php }?>
                       
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
            
            <div class="custCol-6">
                <div class="inputHolder">
                     <label></label>
                    <span id="lblBranch"></span>
                </div>
            </div>
    </div>
        
    </form>
    <div class="customClear"></div>
</div>

    <div class="custRow">
        <div id="branch_graph" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
</div>
</div>
</div>
<script>

    $(document).ready(function ()
    {
        $('body').on('click', '.scrollBtmGraph', function() {
            var target = $('#divbottom');
            $('html, body').stop().animate({
                   scrollTop: target.offset().top
                }, 1000);
        });
    });
// Create the chart
    chart = new Highcharts.Chart('region_graph', {
        chart: {
            type: 'pie', 
            backgroundColor:'transparent'
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
                            getbranches(this.id,this.name);
                        }
                    }
                }
            }]
    });
    
    chart2 = new Highcharts.Chart('branch_graph', {
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            
            xAxis: {
                categories: [
                    
                ],
                title: {
                    text: 'Category'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Amount'
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.x + '</b>: ' + this.y;
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Category',
                colorByPoint: true,
                data: []

            }]
        })
        
        $('#cmbmonth').on('change', function () {
            $("#frmregiongraph").submit();
            $("#divbrgraph").hide();
        });
        
        $('#cmbyear').on('change', function () {
            $("#divbrgraph").hide();
            $("#frmregiongraph").submit();
        });
        
        $('#cmbmonth1').on('change', function () {
            var branchid=$("#hiddenbranchid").val();
            showbranchgraph(branchid);
        });
        
        $('#cmbyear1').on('change', function () {
            var branchid=$("#hiddenbranchid").val();
            showbranchgraph(branchid);
        });
         
         
    $(".clsBranches").html("Select Region From Graph");
    
    function getbranches(region_id,region_name) {
        $.ajax({
            type: 'POST',
            url: '../cost_analysis/getbranches',
            data: '&region_id=' + region_id + '&region_name=' + region_name +
                  '&month=' + $("#cmbmonth").val() + '&year=' + $("#cmbyear").val(),
            success: function (return_data) {
                if(return_data!=-1){
                    $(".clsBranches").html(return_data);
                }else{
                    $(".clsBranches").html('');
                }    
                
            }
        });
    }
    
    function showbranchgraph(branchid) {
        $("#hiddenbranchid").val(branchid)
        $("#divbrgraph").show()
        $.ajax({
            type: 'POST',
            url: '../cost_analysis/getbranchgraph',
            data: '&branchid=' + branchid +
                  '&month=' + $("#cmbmonth1").val() + '&year=' + $("#cmbyear1").val(),
            success: function (return_data) {
                
                $("#lblBranch").html(return_data.branch);
                 chart2.update({
                    xAxis: {
                        categories: return_data.arrCategory,
                        title: {
                            text: 'Category'
                        }
                        
                    },
                    series: [
                        {
                        name: 'Category',
                        colorByPoint: true,
                        data: return_data.arrSeries,
                        title: {
                            text: 'Amount'
                        }
                    }]
                });
                
            }
        });
    }
    
     
     function filterbranch() {
         $("#branchpop").html('') ;
          
           var region_id= $("#regionid").val(); 
           var region_name= $("#regionname").val();
          
           var search= $("#search").val();
           
       
           $.ajax({
            type: 'POST',
            url: '../cost_analysis/filterbranch',
            data: '&region_id=' + region_id + '&region_name=' + region_name + '&search='+search+
                  '&month=' + $("#cmbmonth").val() + '&year=' + $("#cmbyear").val(),
            success: function (return_data) {
                console.log(return_data);
                if(return_data!=-1){
                    $("#branchpop").html(return_data);
                }else{
                     $("#branchpop").html('');
                }    
                
            }
        });
    }
    
</script>

@endsection