@extends('layouts.main')
@section('content')
<script>
    var arrnodes=<?php echo $treenodes?>;
    var arremps=<?php echo $arrEmployees?>;
</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('organizationchart/organizationchart/getchartlist')}}">Back</a>
    <header class="pageTitle">
        <h1><span>Organization Chart</span></h1>
    </header>	
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
    
        <div class="orgChartCreator">

        <div id="orgChart" style="overflow: auto;width: 100%;min-height: 500px;">
            
            <div id="orgChartContent" style="padding-top: 20px;">

            </div>
        </div>
        
    </div>
   
</div>
<script>
    var treeNodes = arrnodes;
    var objEmployees=arremps;
    
    createChart();

    function createChart(){
        
        if(treeNodes.length>0){
           org_chart = $('#orgChartContent').orgChart({
                    data: treeNodes
            });
            
        }
                
        $(".btnCatAdd").hide();
        $(".btnEmpAdd").hide();
        $(".btnRemoveNode").hide();
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
                                  '<b>'+arrEmp[i].name+'</b>'+
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