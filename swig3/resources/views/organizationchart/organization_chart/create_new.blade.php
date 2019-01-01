@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Create Organization Chart</h1>
    </header>	

    <form action="" method="post" id="frmmain">
        <div class="fieldGroup" id="fieldSet1">
            <input type="hidden" value="Custom" id="based_on">
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
        <form id="frmNodename">
            <div class="custCol-8">
                <div class="inputHolder">
                    <label>Node Name</label>
                    <input type="text" name="nodename" id="nodename" autofocus autocomplete="off" placeholder="Enter Name" maxlength="250">
                    <span class="commonError"></span>
                </div>
            </div>
        </form>
        
        <div class="custRow">
            <a class="btnV2 bgDarkGreen jobok" href="javascript:void(0)">Ok</a>
        </div>
    </div>
    
    <div class="chartModal rootmodal" style="display: none;">
        <a class="btmModalClose" href="javascript:void(0)">X</a>
        <form id="frmRootNodename">
            <div class="custCol-8">
                <div class="inputHolder">
                    <label>Node Name</label>
                    <input type="text" name="rootnodename" id="rootnodename" autofocus autocomplete="off" placeholder="Enter Name" maxlength="250">
                    <span class="commonError"></span>
                </div>
            </div>
        </form>
        
        <div class="custRow">
            <a class="btnV2 bgDarkGreen rootok" href="javascript:void(0)">Ok</a>
        </div>
        
    </div>
    

    <div class="chartModal empmodal" style="display: none;">
        <a class="btmModalClose" href="javascript:void(0)">X</a>
        <h3>Add Employees</h3>
        <div class="custRow">
            <div class="custCol-8">
                <div class="inputHolder">
                    <label>Job Position</label>
                    <select  name="cmbjobposition" id="cmbjobposition" class="cmbjobposition">
                        <option selected value=''>Select Job Position</option>
                        @foreach ($job_positions as $job_position)
                            <option value="{{$job_position->id}}"><?php echo str_replace("_", " ", $job_position->name)?></option>
                        @endforeach
                    </select>

                    <span class="commonError"></span>
                </div>
            </div>
            <div class="custCol-4">
                <div class="inputHolder divempcode" >
                    <label>Employee Code</label>
                    <input type="text" name="empcode" id="empcode"  autocomplete="off" placeholder="Enter Code" maxlength="250">
                    <span class="commonError"></span>
                </div>
            </div>
        </div>
        
        <div class="chartModalContent">
            <div class="orgEmpDragList emps">

            </div>

        </div>
        <a class="btnV2 bgDarkGreen empok" href="javascript:void(0)">Ok</a>
    </div>
    
    <div class="chartModal changenodenamemodal" style="display: none;">
        <a class="btmModalClose" href="javascript:void(0)">X</a>
        <form id="frmChangeNodename">
            <div class="custCol-8">
                <div class="inputHolder">
                    <label>Node Name</label>
                    <input type="text" name="changedname" id="changedname"  autocomplete="off" placeholder="Enter Name" maxlength="250">
                    <span class="commonError"></span>
                </div>
            </div>
        </form>
        
        <div class="custRow">
            <a class="btnV2 bgDarkGreen nameok" href="javascript:void(0)">Ok</a>
        </div>
    </div>
    
    <div class="overlay"></div>
    <div class="commonLoaderV1"></div>
</div>

<div class="employDetailcontent" style="display: none">
    <a class="btmModalClose" href="javascript:void(0)">X</a>
    <div class="divcontent">

    </div>
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
        
        $("#frmNodename").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                nodename: {required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    }, },
            },
            messages: {
                nodename: "Node name is required",
            }
        });
        
        $("#frmRootNodename").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                rootnodename: {required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    }, },
            },
            messages: {
                rootnodename: "Node name is required",
            }
        });
        
        $("#frmChangeNodename").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                changedname: {required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    }, },
            },
            messages: {
                changedname: "Node name is required",
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
            $('#rootnodename').focus();
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
            $(".changenodenamemodal").hide();
             $(".employDetailcontent").hide();
            $(".rootmodal").hide();
            $(".empmodal").hide();
            $(".overlay").hide();
            intGlobalNodeId='';
            intGlobalParentId='';
            intChangeNameNodeId='';
            $("#rootnodename").val('');
            $("#nodename").val('');
            $("#cmbjobposition").val('');
            $("#empcode").val('');
            
            $("#nodename").removeClass('valErrorV1');
            $('#nodename').next( ".commonError" ).html('').hide();
            $("#rootnodename").removeClass('valErrorV1');
            $('#rootnodename').next( ".commonError" ).hide();
            $("#changedname").removeClass('valErrorV1');
            $('#changedname').next( ".commonError" ).hide();
        });
        
        $('body').on('click', '.btnCatAdd', function () {
            intGlobalNodeId=$(this).parent().attr("node-id");
            $(".emps").html('');
            
            var jobid= '';
            if((intGlobalNodeId in objEmployees)){
                var arrEmps = objEmployees[intGlobalNodeId][0];
                if(arrEmps){
                    jobid=arrEmps.jobid;
                }
            }
            
            if(jobid!=''){
                $("#cmbjobposition").val(jobid);
                $(".divempcode").show();
                getEmployees(intGlobalNodeId,jobid,0);
            }else{
                $(".divempcode").hide();
            }
            
            $(".empmodal").show();
            $(".overlay").show();
        });
        
        $('body').on('change', '#cmbjobposition', function () {
            var blnrootnode=0;
            $(".emps").html('');
            $(".empmodal").show();
            $(".overlay").show();
            if($("#cmbjobposition").val()!=''){
                getEmployees(intGlobalNodeId,$("#cmbjobposition").val(),blnrootnode);
                $("#empcode").val('');
                $(".divempcode").show();
            }else{
                $(".emps").html('No Employee Found');
                $(".divempcode").hide();
                $("#empcode").val('');
            }
        });
        
        $('body').on('keyup', '#empcode', function () {
            var empcode=$("#empcode").val();
            if($.trim(empcode)!=''){
                $("div.filterempcode[attrsearch*='"+empcode+"']").show();
                $(".filterempcode").not("[attrsearch*='"+empcode+"']").hide();
            }else{
                $(".filterempcode").show();
            }
            
        });

        $('body').on('click', '.btnEmpAdd', function () {
            intGlobalParentId=parseInt($(this).parent().attr("node-id"));
            $(".clschkchild").prop("checked", false);
//            for(var i=0;i<arrSelectedJobPositions.length;i++){
//                $("#"+arrSelectedJobPositions[i]).prop("checked", true);
//                $("#"+arrSelectedJobPositions[i]).attr("disabled", true);
//            }
            $(".jobpositionsmodal").show();
            $('#nodename').focus();
            $(".overlay").show();
        });
        
        $('body').on('click', '.btnChangeName', function () {
            intChangeNameNodeId=$(this).parent().attr("node-id");
            $("#changedname").val($('.node[node-id='+intChangeNameNodeId+']').find("h2").html());
            $(".changenodenamemodal").show();
            $('#changedname').focus();
            $(".overlay").show();
        });
        
        $('body').on('click', '.nameok', function () {
            if(!$("#frmChangeNodename").valid()){
                return;
            }
            
            if(!checkduplicate($("#changedname").val(),intChangeNameNodeId)){
                $("#changedname").addClass('valErrorV1');
                $('#changedname').next( ".commonError" ).html("Node name already taken").show();
                return;
            }else{
                $("#changedname").removeClass('valErrorV1');
                $('#changedname').next( ".commonError" ).hide();
            }
            
            for(var i=0;i<treeNodes.length;i++){
                if(treeNodes[i].id==intChangeNameNodeId){
                    treeNodes[i].name=$("#changedname").val();
                }
            }
            createChart();
            intChangeNameNodeId='';
            $(".overlay").hide();
            $(".changenodenamemodal").hide();
        });

        $('body').on('click', '.jobok', function () {
            
            if(!$("#frmNodename").valid()){
                return;
            }
            
            if(!checkduplicate($("#nodename").val(),-1)){
                $("#nodename").addClass('valErrorV1');
                $('#nodename').next( ".commonError" ).html("Node name already taken").show();
                return;
            }else{
                $("#nodename").removeClass('valErrorV1');
                $('#nodename').next( ".commonError" ).hide();
            }
            
            if(intGlobalParentId!=''){
              var id=generateNodeId();
              var name=$("#nodename").val();
              var arraData = { id: id, name: name, parent: intGlobalParentId }
              treeNodes.push(arraData);
              arrSelectedJobPositions.push(id);
            }
            createChart();
            intGlobalParentId='';
            $(".jobpositionsmodal").hide();
            $(".overlay").hide();
            $("#nodename").val('');
        });
        
        $('body').on('click', '.rootok', function () {
            
            if(!$("#frmRootNodename").valid()){
                return;
            }
            
            if(!checkduplicate($("#rootnodename").val(),-1)){
                $("#rootnodename").addClass('valErrorV1');
                $('#nodename').next( ".commonError" ).html("Node name already taken").show();
                return;
            }else{
                $("#rootnodename").removeClass('valErrorV1');
                $('#nodename').next( ".commonError" ).hide();
            }
            
            var id=50;
            var name=$("#rootnodename").val();
        
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
                var jobid=$(this).attr("attrJobPos");

                strHtml+='<div class="empList"><figure class="imgHolder">'+
                                '<img src="'+pic+'" alt="Profile">'+
                            '</figure>'+
                           '<div class="details">'+
                                '<b class="getempdetails" attrid='+id+'>'+name+'</b>'+
                                '<figure class="flagHolder">'+
                                    '<img src="'+flag+'" alt="Flag">'+
                                    '<figcaption>'+country+'</figcaption>'+
                                '</figure>'+
                            '</div>'+
                            '<div class="customClear"></div>'+
                        '</div>';
                
                var empData = { id: id, name: name, pic: pic,flag:flag,country:country,node:intGlobalNodeId,jobid:jobid }
                arrData.push(empData);
                
                
            });
          
            objEmployees[intGlobalNodeId]=arrData;
           
            $("#emp_"+intGlobalNodeId).html(strHtml);
            $(".empmodal").hide();
            $(".overlay").hide();
            intGlobalNodeId='';
            $("#cmbjobposition").val('');
            $("#empcode").val('');
        });
        
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
              }
        });
  
        $('body').on('click', '.btnRemoveNode', function () {
            var blnConfirm = confirm("Removing this node will delete all underlying child nodes,Are you sure to continue?");
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
                    window.location.href = '{{url("organizationchart/organizationchartnew/getchartlist")}}';
                },
                error: function (return_data) {
                    $('#btnsavechart').removeAttr('disabled');
                    $(".commonLoaderV1").hide();
                    window.location.href = '{{url("organizationchart/organizationchartnew/getchartlist")}}';
                }
            });
            
            $(".commonLoaderV1").hide();
            $('#btnsavechart').removeAttr('disabled');
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

    function createChart(){
    
        if(treeNodes.length==0){
            $(".btnAddChart").show();
            $('#orgChartContent').html('');
        }else{
            org_chart = $('#orgChartContent').orgChart({
                    data: treeNodes
            });
            
        }
                
        fillEmployeesAllNode();
    }
    
    function getEmployees(nodeid,jobposition,blnrootnode){
        
        var arrEmps= '';
        if((nodeid in objEmployees)){
            arrEmps = JSON.stringify(objEmployees[nodeid]);
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
    
    function fillEmployeesInSingleNode(node){
        var arrEmp=objEmployees[node];
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

        $("#emp_"+node).html(strHtml);
    }
    
    function generateNodeId(){
        var arr=treeNodes;
        var minarr = arr.reduce(function(min, obj) { 
           return obj.id > min ? obj.id : min; 
        }, 1);
        
        return parseInt(minarr+100);
    }
    
    function checkduplicate(name,editid){
        var blnDuplicate=true;
        for(var i=0;i<treeNodes.length;i++){
            if(treeNodes[i].name==name && treeNodes[i].id!=editid){
                blnDuplicate=false;
                break;
            }
        }
        
        return blnDuplicate;
    }
    
    
</script>
@endsection