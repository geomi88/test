@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <?php if($charttype=="employees"){?>
            <h1>Create Organization Chart - <span>Employee Wise</span></h1>
        <?php }else{ ?>
            <h1>Create Organization Chart - <span>Job Position Wise</span></h1>
        <?php } ?>
    </header>	

    <form action="" method="post" id="frmmain">
        <div class="fieldGroup" id="fieldSet1">
            <input type="hidden" value="{{$charttype}}" id="based_on">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Chart Category</label>
                        <select  name="cmbcategory" id="cmbcategory" class="">
                            <option selected value=''>Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>

                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder checklistcatName">
                        <label>Name</label>
                        <input type="text" name="name" id="name"  autocomplete="off" placeholder="Enter Name" maxlength="250">
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" class="arabicalign" name="alias_name" id="alias_name" placeholder="Enter Alias" maxlength="250">
                    </div>
                </div>
                
            </div>
            
        </div>
    </form>	
   
        <div class="orgChartCreator">

        <div id="orgChart" style="overflow: auto;width: 100%;min-height: 500px;">
            <a class="btnAddChart">Click To Add</a>
            <div id="orgChartContent" style="padding-top: 20px;">

            </div>
        </div>
        <div class="custRow" style="margin-top: 40px;">
        <div class="custCol-4">
            <input type="button" value="Create" id="btnsavechart" class="commonBtn bgGreen addBtn" >
        </div>
    </div>
    </div>


    <div class="chartModal jobpositionsmodal" style="display: none;">
        <a class="btmModalClose" href="javascript:void(0)">X</a>
        <h3>Add Job Position</h3>
       
        <div class="chartModalContent">
            <ul>
                @foreach ($job_positions as $job_position)
                <li>
                    <div class="commonCheckHolder checkboxRender">
                        <label>
                        <input name="gender" type="checkbox" class="clschkchild" id="{{$job_position->id}}" attrName="<?php echo str_replace("_", " ", $job_position->name)?>">
                        <span></span>
                        <em><?php echo str_replace("_", " ", $job_position->name)?></em>
                    </label>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        <a class="btnV2 bgDarkGreen jobok" href="javascript:void(0)">Ok</a>
    </div>
    
    <div class="chartModal rootmodal" style="display: none;">
        <a class="btmModalClose" href="javascript:void(0)">X</a>
        <h3>Add Job Position</h3>
       
        <div class="chartModalContent">
            <ul>
                @foreach ($job_positions as $job_position)
                <li>
                    <div class="commonCheckHolder checkboxRender">
                        <label>
                            <input name="gender" type="checkbox" class="clschkroot" rootid="{{$job_position->id}}" attrName="<?php echo str_replace("_", " ", $job_position->name)?>">
                        <span></span>
                        <em><?php echo str_replace("_", " ", $job_position->name)?></em>
                    </label>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        <a class="btnV2 bgDarkGreen rootok" href="javascript:void(0)">Ok</a>
    </div>
    

    <div class="chartModal empmodal" style="display: none;">
        <a class="btmModalClose" href="javascript:void(0)">X</a>
        <h3>Add Employees</h3>
        <div class="chartModalContent">
            <div class="orgEmpDragList emps">

            </div>

        </div>
        <a class="btnV2 bgDarkGreen empok" href="javascript:void(0)">Ok</a>
    </div>
    
    <div class="overlay"></div>
    <div class="commonLoaderV1"></div>
</div>
<script>
    
    $("#frmmain").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                name: {required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    }, },
                cmbcategory: {required: true},
                alias_name: {required: true},
                
            },
            messages: {
                name: "Enter Name",
                alias_name: "Select Alias Name",
                cmbcategory: "Select Category",
            }
        });
        
    var treeNodes = [];
    var objEmployees = {};
    var arrSelectedJobPositions = [];
    var intGlobalParentId='';
    var intGlobalNodeId='';
    $(function () {
        $('body').on('click', '.btnAddChart', function () {
            $(".clschkroot").prop("checked", false);
            $(".rootmodal").show();
            $(".overlay").show();
        });
        
        $('body').on('click', '.clschkroot', function () {
            var $box = $(this);
            if ($box.is(":checked")) {
              $(".clschkroot").prop("checked", false);
              $box.prop("checked", true);
            } else {
              $box.prop("checked", false);
            }
        });

        $('body').on('click', '.btmModalClose', function () {
            $(".jobpositionsmodal").hide();
            $(".rootmodal").hide();
            $(".empmodal").hide();
            $(".overlay").hide();
            intGlobalNodeId='';
            intGlobalParentId='';
        });

        $('body').on('click', '.btnCatAdd', function () {
            intGlobalNodeId=$(this).parent().attr("node-id");
            $(".emps").html('');
            var blnrootnode=0;
            if($(this).attr("attrrooornot")==0){
                blnrootnode=1;
            }
            getEmployees(intGlobalNodeId,blnrootnode);
            $(".empmodal").show();
            $(".overlay").show();
        });

        $('body').on('click', '.btnEmpAdd', function () {
            intGlobalParentId=$(this).parent().attr("node-id");
            $(".clschkchild").prop("checked", false);
            for(var i=0;i<arrSelectedJobPositions.length;i++){
                $("#"+arrSelectedJobPositions[i]).prop("checked", true);
                $("#"+arrSelectedJobPositions[i]).attr("disabled", true);
            }
            $(".jobpositionsmodal").show();
            $(".overlay").show();
        });

        $('body').on('click', '.jobok', function () {
          if(intGlobalParentId!=''){
            $(".clschkchild:checked:not(:disabled)").each(function() {
                var id=$(this).attr("id");
                var name=$(this).attr("attrName");
                var arraData = { id: id, name: name, parent: intGlobalParentId }
                treeNodes.push(arraData);
                arrSelectedJobPositions.push(id);
            });
          }
          createChart();
          intGlobalParentId='';
          $(".jobpositionsmodal").hide();
          $(".overlay").hide();
        });
        
        $('body').on('click', '.rootok', function () {
            if($(".clschkroot:checked").length==0){
                alert("Please Select One Job position As Root");
                return;
            }
            var id=$(".clschkroot:checked").attr("rootid");
            var name=$(".clschkroot:checked").attr("attrName");
        
            var arraData = { id: id, name: name, parent: 0 }
            treeNodes.push(arraData);
            arrSelectedJobPositions.push(id);
            createChart();
            $(".rootmodal").hide();
            $(".overlay").hide();
            $(".btnAddChart").hide();
        });

        $('body').on('click', '.empok', function () {
            var strHtml='';
            var arrData=[];
            $(".clschkempbyjob:checked").each(function() {
                var id=$(this).attr("id");
                var name=$(this).attr("attrName");
                var pic=$(this).attr("attrPic");
                var flag=$(this).attr("attrFlag");
                var country=$(this).attr("attrCountry");

                strHtml+='<div class="empList"><a class="btnEmpRemove" attremp='+id+' attrjob='+intGlobalNodeId+'>x</a><figure class="imgHolder">'+
                                '<img src="'+pic+'" alt="Profile">'+
                            '</figure>'+
                           '<div class="details">'+
                                '<b>'+name+'</b>'+
                                '<figure class="flagHolder">'+
                                    '<img src="'+flag+'" alt="Flag">'+
                                    '<figcaption>'+country+'</figcaption>'+
                                '</figure>'+
                            '</div>'+
                            '<div class="customClear"></div>'+
                        '</div>';
                
                var empData = { id: id, name: name, pic: pic,flag:flag,country:country,node:intGlobalNodeId }
                arrData.push(empData);
                
                
            });
            
           objEmployees[intGlobalNodeId]=arrData;
           
            $("#emp_"+intGlobalNodeId).html(strHtml);
            $(".empmodal").hide();
            $(".overlay").hide();
            intGlobalNodeId='';
        });
        
        $('body').on('click', '.btnRemoveNode', function () {
            var blnConfirm = confirm("Removing this job postion will delete all underlying job positions,Are you sure to continue?");
            if(blnConfirm){
                var parentid=$(this).parent().attr("node-id");
                var treeNodesTemp=[];
                var removedNodes=[];
                // Remove immediate childs nodes
                for(var i=0;i<treeNodes.length;i++){
                    if(treeNodes[i].parent==parentid){
                        removedNodes.push(treeNodes[i].id);
                        arrSelectedJobPositions.splice($.inArray(treeNodes[i].id, arrSelectedJobPositions),1);
                        delete objEmployees[treeNodes[i].id];
                    }else{
                        treeNodesTemp.push(treeNodes[i]);
                    }
                }
                
                // Remove grand childs nodes
                while(removedNodes.length>0){
                    var removetemp=[];
                    var treeNodesTemp2=[];
                    for(var i=0;i<treeNodesTemp.length;i++){
                        if($.inArray(treeNodesTemp[i].parent, removedNodes)>-1){
                            removetemp.push(treeNodesTemp[i].id);
                            arrSelectedJobPositions.splice($.inArray(treeNodesTemp[i].id, arrSelectedJobPositions),1);
                            delete objEmployees[treeNodesTemp[i].id];
                        }else{
                            treeNodesTemp2.push(treeNodesTemp[i]);
                        }
                    }
                    
                    treeNodesTemp=treeNodesTemp2;
                    removedNodes=removetemp;
                }
                
                treeNodes=treeNodesTemp;
                
                 // Remove node itself
                for(var i=0;i<treeNodes.length;i++){
                    if(treeNodes[i].id==parentid){
                        arrSelectedJobPositions.splice($.inArray(treeNodes[i].id, arrSelectedJobPositions),1);
                        delete objEmployees[treeNodes[i].id];
                        treeNodes.splice(i,1);
                    }
                }

                createChart();
            }
        });
        
        $('body').on('click', '.btnEmpRemove', function () {
            var blnConfirm = confirm("Are you sure to remove this employee?");
            if(blnConfirm){
                var attremp=$(this).attr("attremp");
                var attrjob=$(this).attr("attrjob");

                var arrEmp=objEmployees[attrjob];

                for(var i=0;i<arrEmp.length;i++){
                    if(arrEmp[i].id==attremp){
                         arrEmp.splice(i,1);
                    }
                }

                if(arrEmp.length>0){
                    objEmployees[attrjob]=arrEmp;
                    fillEmployeesInSingleNode(attrjob);
                }else{
                    delete objEmployees[attrjob];
                    $("#emp_"+attrjob).html('');
                }
            }
        });
        
        $('#btnsavechart').on('click', function () {

            if (!$("#frmmain").valid()) {
                return;
            }

            if (treeNodes.length == 0) {
                alert("Please Add Atleast One Node");
                return;
            }
            
            var arraData = {
                name: $("#name").val(),
                alias_name: $("#alias_name").val(),
                category: $("#cmbcategory").val(),
                based_on: $("#based_on").val(),
            }

            arraData = encodeURIComponent(JSON.stringify(arraData))
            
            var treeNodesJson = encodeURIComponent(JSON.stringify(treeNodes));
            var objEmployeesJson = encodeURIComponent(JSON.stringify(objEmployees));
            
            $('#btnsavechart').attr('disabled','disabled');
            $(".commonLoaderV1").show();
            $.ajax({
                type: 'POST',
                url: 'savechart',
                data: '&treeNodesJson=' + treeNodesJson + '&objEmployeesJson=' + objEmployeesJson + '&arrMaster=' + arraData,
                success: function (return_data) {
                    $('#btnsavechart').removeAttr('disabled');
                    $(".commonLoaderV1").hide();
                    window.location.href = '{{url("organizationchart/organizationchart/getchartlist")}}';
                },
                error: function (return_data) {
                    $('#btnsavechart').removeAttr('disabled');
                    $(".commonLoaderV1").hide();
                    window.location.href = '{{url("organizationchart/organizationchart/getchartlist")}}';
                }
            });
            
            $(".commonLoaderV1").hide();
            $('#btnsavechart').removeAttr('disabled');
        })
        
    });

    function createChart(){
    
        if(treeNodes.length==0){
            $(".btnAddChart").show();
            $('#orgChartContent').html('');
        }else{
            org_chart = $('#orgChartContent').orgChart({
                    data: treeNodes
            });
            
            if($("#based_on").val()=="Job_Position"){
                $(".btnCatAdd").hide();
            }
        }
                
        fillEmployeesAllNode();
    }
    
    function getEmployees(jobposition,blnrootnode){
        var arrEmps= '';
        if((jobposition in objEmployees)){
            arrEmps = JSON.stringify(objEmployees[jobposition]);
        }
        
        $.ajax({
            type: 'POST',
            url: 'getemployeesbyjob',
            data: '&jobposition=' + jobposition + '&arrEmps=' + arrEmps + '&blnrootnode=' + blnrootnode,
            success: function (return_data) {
                if(return_data!=-1){
                    $(".emps").html(return_data);
                }else{
                    $(".emps").html('No Emplyee Found');
                }
            }
        });
    }
    
    function fillEmployeesAllNode(){
        $.each( objEmployees, function( key, value ) {
            var arrEmp=value;
            var strHtml='';
            for(var i=0;i<arrEmp.length;i++){
                strHtml+='<div class="empList"><a class="btnEmpRemove" attremp='+arrEmp[i].id+' attrjob='+arrEmp[i].node+'>x</a><figure class="imgHolder">'+
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
    
    function fillEmployeesInSingleNode(node){
        var arrEmp=objEmployees[node];
        var strHtml='';
        for(var i=0;i<arrEmp.length;i++){
            strHtml+='<div class="empList"><a class="btnEmpRemove" attremp='+arrEmp[i].id+' attrjob='+arrEmp[i].node+'>x</a><figure class="imgHolder">'+
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

        $("#emp_"+node).html(strHtml);
    }
    
</script>
@endsection