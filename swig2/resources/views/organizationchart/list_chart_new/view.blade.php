@extends('layouts.main')
@section('content')

<script>
    var arrnodes=<?php echo $treenodes?>;
    var arremps=<?php echo $arrEmployees?>;
</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('organizationchart/organizationchartnew/getchartlist')}}">Back</a>
    <header class="pageTitle" style="margin-bottom: 20px;">
        <h1><span>Organization Chart</span></h1>
    </header>
    
    <a class="btnAction print bgGreen" id="download"  href="#" style="margin-bottom: 10px;">Download image</a>
    <div class="reportV1 mapView">
        
        <ul class="custRow">
            <li class="custCol-5">
                <b>Chart Name</b>
                {{$master_data->name}} {{$master_data->alias_name}}
            </li>
            <li class="custCol-5">
                <b>Category</b>
                {{$master_data->category}}
            </li>
        </ul>
        
    </div>
    
    <div class="OrgchartZoom">
        <a class="btnZoomIn"></a>
        <a class="btnZoomOut"></a>
    </div>
    
    <div class="orgChartCreator" id="orgChartCreator">

        <div id="orgChart" style="overflow: auto;width: 100%;min-height: 500px;">
            
            <div id="orgChartContent" style="padding-top: 20px;">

            </div>
        </div>
        
    </div>
    <div id="result" style="display: none;">
        
    </div>
    
</div>

<div class="overlay" style="display: none"></div>
<div class="employDetailcontent" style="display: none">
    <a class="btmModalClose" href="javascript:void(0)">X</a>
    <div class="divcontent">
        
        
    </div>
</div>
<script>
    var treeNodes = arrnodes;
    var objEmployees=arremps;
    
    createChart();
    
    $(function() { 
        $("#download").click(function() { 
            $('#orgChartContent').css('transform', 'scale(1)');
            html2canvas(document.querySelector("#divchart"),{allowTaint: false}).then(function (canvas){
                    $("#result").html(canvas);
                    canvas.setAttribute('id',"mycanvas");
                    Canvas2Image.saveAsPNG(canvas); 
                });
        });
        
        $('body').on('click', '.btmModalClose', function () {
            $(".employDetailcontent").hide();
            $(".overlay").hide();
           
        });
        
        $('body').on('click', '.getempdetails', function () {
            $(".divcontent").html('');
            $.ajax({
                type: 'POST',
                url: '../getprofiledetails',
                data: '&emp_id=' + $(this).attr("attrid"),
                success: function (data) {
                   $(".divcontent").html(data);
                }
            });
        
            $(".employDetailcontent").show();
            $(".overlay").show();
           
        });
      
    }); 
    
    $(document).ready(function() {
          
        var zoom = 1;

        $('.btnZoomOut').on('click', function () {
                zoom -= .1
                $('#orgChartContent').css('transform', 'scale(' + zoom + ')');
        });

        $('.btnZoomIn').on('click', function () {
                zoom += .1
                $('#orgChartContent').css('transform', 'scale(' + zoom + ')');
        });
        
    }); 

    
    function createChart(){
        
        if(treeNodes.length>0){
           org_chart = $('#orgChartContent').orgChart({
                    data: treeNodes
            });
            
        }
                
        $(".btnCatAdd").hide();
        $(".btnEmpAdd").hide();
        $(".btnRemoveNode").hide();
        $(".btnChangeName").hide();
        fillEmployeesAllNode();
    }
    
    
    
    function fillEmployeesAllNode(){
        $.each( objEmployees, function( key, value ) {
            var arrEmp=value;
            var strHtml='';
            for(var i=0;i<arrEmp.length;i++){
                strHtml+='<div class="empList"><figure class="imgHolder">'+
                                  '<img src="'+arrEmp[i].pic+'" alt="Profile">'+
                              '</figure>'+
                             '<div class="details">'+
                                  '<b class="getempdetails" attrid='+arrEmp[i].id+'>'+arrEmp[i].name+'</b>'+
                                  '<figure class="flagHolder">'+
                                      '<img src="'+arrEmp[i].flag+'" alt="Flag">'+
                                      '<figcaption>'+arrEmp[i].country+'</figcaption>'+
                                  '</figure>'+
                              '</div>'+
                              '<div class="customClear"></div>'+
                          '</div>';
            }
            
            $("#emp_"+key).html(strHtml);
        });
    }
    
</script>
@endsection