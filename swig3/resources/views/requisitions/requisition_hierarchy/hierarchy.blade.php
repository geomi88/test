@extends('layouts.main')
@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <header class="pageTitleV3">
        <h1>Requisition Hierarchy</h1>
    </header>
    <div class="inputAreaWrapper">
        <form id="frmaddlevel">
            <div class="custCol-4">
                <div class="inputHolder  ">
                    <label>Select Requisition</label>
                    <select id="requisitiontype" name="requisitiontype" class="requisitiontype">
                        <option value="">Select Option</option>
                        @foreach ($requisitions as $requisition)
                            <option value="{{$requisition->id}}">{{$requisition->name}}</option>
                        @endforeach
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
            <div class="custRow">
                <div class="commonCheckHolder radioRender radioTop">
                    <label>
                        <input id="topManager" name="level_type" class="level_type" type="radio" value="1" >
                        <span></span>
                        <em>Top Manager</em>
                    </label>
                </div>
               <div class="commonCheckHolder radioRender">
                    <label>
                        <input id="otherEmployees" name="level_type" class="level_type" value="2" type="radio" checked>
                        <span></span>
                        <em>Other Employees</em>
                    </label>
                </div>
            </div>
            
            <div class="custRow" style="margin-top: 5px;">
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label id="lblemployee">Employee</label>
                        <div class="bgSelect">                                                          
                            <input type="text" name="employee" id="employee" autocomplete="off" placeholder="Enter Code or Name">

                            <ul class="employee_list classscroll">
                            </ul>

                            <input type="hidden"  name="emp_id" id="emp_id" >
                            <input type="hidden"  name="emp_code" id="emp_code" >
                            <input type="hidden"  name="emp_fname" id="emp_fname" >
                            <input type="hidden"  name="emp_aname" id="emp_aname" >
                            <input type="hidden"  name="emp_jobpos" id="emp_jobpos" >
                        </div>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <a class="btnAction action bgGreen" id="add_employee"  style="margin-top: 26px;cursor: pointer;"> Add</a>
                    <a class="btnAction action bgLightPurple" id="reset_employee"  style="margin-top: 26px;cursor: pointer;margin-left: 20px;"> Reset</a>
                </div>
               
            </div>
        </form>
    </div>

    <div class="listHolderType2">
        <div class="listTableTitle">
            Added Approvers
        </div>
        <div class="listTableContent">
            <div class="tbleListWrapper">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="headingHolder">
                        <tr>
                            <th class="drag_level">Level</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Job Position</th>
                            <th class="hierarchydelete"></th>
                            <th class="hierarchydelete"></th>
                        </tr>
                    </thead>
                    <tbody class="tblapproverlist" id="tblapproverlist">
                        <tr><td>No Level added<td></tr>
                    </tbody>
                </table>
            </div>
        </div>
      
    </div>
    <div class="bottomBtnsHolder">
        <input type="button" id="btnSaveReqLevels" class="btnIcon btnSaveV3  lightGreenV3" value="Save">
        <div class="customClear "></div>
     </div>
<div class="commonLoaderV1"></div>
</div>
<script>
    var arrLevelList = [];
    var arrtempdata = [];
    var intGlobalEditIndex = -1;
    $(function () {
        $("#frmaddlevel").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                requisitiontype: {required: true},
                employee: {required: true},
            },
            messages: {
                requisitiontype: "Select Requisition",
                employee: "Select Employee",
            }
        });
        
        $(".level_type").click(function () {
            var val=$(this).val();
            if(val==1){
              $("#employee").val("Top Manager");
              $('#emp_id').val(-1);
            }else{
                $("#employee").val("");
                $('#emp_id').val('');
            }
        });

        $('#employee').keyup(function () {
          //   $("#lblemployee").html('Employee');
            
            $(".employee_list").html('');
            $('#emp_id').val('');
            $('#emp_code').val('');
            $('#emp_fname').val('');
            $('#emp_aname').val('');
            $('#emp_jobpos').val('');
            var searchkey = $(this).val();

            jQuery.ajax({
                url: "../autocompleteemployees",
                type: 'POST',
                data: {searchkey: searchkey},
                success: function (result) {
                    var total = result.length;
                    if (total == 0) {
                        resetapproverform();
                    }
                    var liname = '';
                    $.each(result, function (i, value) {
                        liname += '<li id=' + value['id'] +
                                ' attrcode=' + value['code'] +
                                ' attrfname="' + value['first_name'] +
                                '" attraname="' + value['alias_name'] +
                                '" attrjob=' +  value['jobposition'] +'>' + value['first_name'] + " ("+ value['code'] + ")" + '</li>';
                    });

                    $(".employee_list").html(liname);

                    var $selectText = $('.bgSelect input');
                    var $selectLi = $('.bgSelect li');

                    var selectval;
                    var Drop = 0;

                    $('body').click(function () {
                        if (Drop == 1) {
                            $('.bgSelect ul').hide();
                            Drop = 0;
                        }
                    });

                    $selectText.click(function () {
                        $('.bgSelect ul').hide();
                        Drop = 0;
                        if (Drop == 0) {
                            $(this).parent().find('ul').slideDown();
                        }
                        setTimeout(function () {
                            Drop = 1;
                        }, 50);

                    });

                    $selectLi.click(function () {
                        Drop = 1;
                        selectval = $(this).text();

                        $('#employee').val(selectval);

                        $(this).parent().parent().find('.commonError').hide();
                    });


                    $('.bgSelect li').click(function () {
                        $('#emp_id').val($(this).attr('id'));
                        $('#emp_code').val($(this).attr('attrcode'));
                        $('#emp_fname').val($(this).attr('attrfname'));
                        $('#emp_aname').val($(this).attr('attraname'));
                        $('#emp_jobpos').val($(this).attr('attrjob'));
                    });
                },
            });
        });

        $('#reset_employee').on('click', function () {
            resetapproverform();
        });
        
        $('#add_employee').on('click', function () {
           
            if (!$("#frmaddlevel").valid()) {
                return;
            }

            if($('#emp_id').val()==""){
                alert("Please select Employee");
                return;
            }
            
            var intItemDuplicate = 0;
            if (arrLevelList.length > 0) {
                for (var i = 0; i < arrLevelList.length; i++) {
                    if ($('#emp_id').val() == arrLevelList[i].emp_id) {
                        intItemDuplicate = 1;
                    }
                }
            }

            if (intItemDuplicate == 1) {
                alert("Employee Already Selected");
                return;
            }
            
            
            var level=arrLevelList.length + 1;
            if(intGlobalEditIndex!=-1){
                level=arrLevelList[intGlobalEditIndex].level;
                var blnConfirm= confirm('Are you sure to change level '+ level+ ' employee ?');
                if(!blnConfirm){
                   resetapproverform();
                   return; 
                }
            } 
            
            if($("#employee").val()=="Top Manager"){
                var arraData = {
                    level: level,
                    emp_id: -1,
                    code: '',
                    name: "Top Manager",
                    approver_type: "TOP_MANAGER",
                    jobpos: "Top Manager",
                }
                $("#otherEmployees").prop("checked",true);
                $("#topManager").prop("checked",false);
                $("#topManager").prop("disabled",true);
            }else{
                var arraData = {
                    level: level,
                    emp_id: $('#emp_id').val(),
                    code: $('#emp_code').val(),
                    name: $("#emp_fname").val(),
                    approver_type: "EMPLOYEE",
                    jobpos: $("#emp_jobpos").val(),
                }
            }
            
            if(intGlobalEditIndex!=-1){
                arrLevelList[intGlobalEditIndex]=arraData;
            } else {
                arrLevelList.push(arraData);
            }
            
            showapproverlist();
            resetapproverform();
        });

        $("#btnSaveReqLevels").click(function () {
            if (arrLevelList.length == 0) {
                alert("Please add atleast one approver");
                return;
            }
            
           // alert(arrLevelList.length);
          
           if ($("#requisitiontype :selected").text()=="Payment Advice" && arrLevelList.length < 2) {
                alert("Please add atleast two levels of approvers");
                return;
            }
            
          
           var reqcount=-1;
            $.ajax({
                type: 'POST',
                url: 'gethierarchy',
                data: '&requisitiontype=' + $("#requisitiontype").val(),
                async:false,
                success: function (return_data) {
                    arrtempdata=return_data.hierarchy;
                    reqcount=return_data.req_count;
                }
            });
            
            var intModified=0;
            if(arrtempdata.length>0){
                if(arrtempdata.length!=arrLevelList.length){
                    intModified=1;
                    
                }else{
                    for (var i = 0; i < arrLevelList.length; i++) {
                        if(arrLevelList[i].emp_id!=arrtempdata[i].emp_id){
                            intModified=1;
                        }
                    }
                }
            }
            
            var blnConfirm;
            if(intModified==1 && reqcount>0){
                blnConfirm= confirm("Changes in requisition hierarchy,All active requisitions will moves to this new order for the approval, Are you sure to submit");
            }else{
                blnConfirm= confirm("Are you sure to submit");
            }
            
            if(!blnConfirm){
               return; 
            }
            
            var arrLevelsList = encodeURIComponent(JSON.stringify(arrLevelList));
            
            $('.commonLoaderV1').show();
            $('#btnSaveReqLevels').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: 'store',
                data: '&requisitiontype=' + $("#requisitiontype").val() + '&arrLevelList=' + arrLevelsList,
                success: function (return_data) {
                   // console.log(return_data);
                $('.commonLoaderV1').hide();
                  window.location.href = '{{url("requisitions")}}';
                }
            });
            
            $('#btnSaveReqLevels').removeAttr('disabled');
            $('.commonLoaderV1').hide();
        });

        $('body').on('change', '.requisitiontype', function () {
             $("#lblemployee").html('Employee');
            arrLevelList = [];
            $("#topManager").prop("disabled",false);
            $("#tblapproverlist").html('<tr><td>No Level added<td></tr>');

            $.ajax({
                type: 'POST',
                url: 'gethierarchy',
                data: '&requisitiontype=' + $("#requisitiontype").val(),
                async:false,
                success: function (return_data) {
                    
                //    console.log(return_data);
                    if(return_data.requisition_name=="Payment Advice"){
                        $(".radioTop").hide();
                    }else{
                        $(".radioTop").show();
                    }
                    arrLevelList=return_data.hierarchy;
                    showapproverlist();
                }
            });

        });

    });

    function showapproverlist() {
        $("#tblapproverlist").html('<tr><td>No Level added<td></tr>');
        if (arrLevelList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrLevelList.length; i++) {
                strHtml += '<tr class="clslevels" id='+ arrLevelList[i].level +'><td class="drag_level"> Level ' + arrLevelList[i].level + '</td><td>' + arrLevelList[i].code + '</td><td>' + arrLevelList[i].name + '</td>\n\
                            <td>' + arrLevelList[i].jobpos.replace(/_/g, ' ') + '</td>'+
                            '<td><a href="javascript:editlevel(' + i + ')" class="tbleEdit" title="Edit"></a></td><td class="hierarchydelete"><a class="tbleClose" href="javascript:remove(' + i + ');" title="Delete"></a> </td></tr>';

            }
           
            $("#tblapproverlist").html(strHtml);
        }
    }

    function remove(index) {
        var blnConfirm= confirm("Are you sure to delete");
        if(!blnConfirm){
           return; 
        }
            
        if(arrLevelList[index].emp_id==0){
            $("#topManager").prop("disabled",false);
        }
        arrLevelList.splice(index, 1);
        resetapproverform();
        updatelevels();
        showapproverlist();
    }
    
    function editlevel(index) {
        $("#lblemployee").html('Change <span>level '+arrLevelList[index].level+'</span> Employee');
        intGlobalEditIndex = index;
    }
    
    function updatelevels() {
        if (arrLevelList.length > 0) {
            for (var i = 0; i < arrLevelList.length; i++) {
                arrLevelList[i].level=parseInt(i+1);
            }
        }
    }

    function resetapproverform() {
        $('#emp_id').val('');
        $('#employee').val('');
        $('#emp_code').val('');
        $('#emp_fname').val('');
        $('#emp_aname').val('');
        $('#emp_jobpos').val('');
        $("#lblemployee").html('Employee');
        intGlobalEditIndex=-1;
    }
    
</script>

@endsection