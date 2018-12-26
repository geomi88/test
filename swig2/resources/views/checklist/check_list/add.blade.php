@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Create <span>Check List</span></h1>
    </header>	

    <form action="" method="post" id="frmmain">
        <div class="fieldGroup" id="fieldSet1">

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Check List Category</label>
                        <select  name="category_id" id="category_id">
                            <option selected value=''>Select Category</option>
                            @foreach ($categories as $categorie)
                            <option value='{{ $categorie->id }}'>{{$categorie->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Job Position</label>
                        <select  name="job_id" id="job_id">
                            <option selected value=''>Select Job Position</option>
                            @foreach ($job_positions as $job_position)
                            <option value='{{ $job_position->id }}'><?php echo str_replace("_", " ", $job_position->name)?></option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>

            
        </div>
    </form>	
    <form id="frmquerylist">
        
        
        <div class="checkpointGroup">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Check Point</label>
                        <textarea name="checkpoint" id="checkpoint" placeholder="Enter Check Point"></textarea>
                    </div>
                </div>
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <textarea name="alias" id="alias" class="arabicalign" placeholder="Enter Alias"></textarea>
                    </div>
                </div>
            </div>
        <div class="custRow">
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays" id="chkallday" value="8" type="checkbox"><span></span>
                        <em>All Days</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" id="chksun" value="1" type="checkbox"><span></span>
                        <em>Sun</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" id="chkmon" value="2" type="checkbox"><span></span>
                        <em>Mon</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" id="chktue" value="3" type="checkbox"><span></span>
                        <em>Tue</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" id="chkwed" value="4" type="checkbox"><span></span>
                        <em>Wed</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" id="chkthu" value="5" type="checkbox"><span></span>
                        <em>Thu</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" id="chkfri" value="6" type="checkbox"><span></span>
                        <em>Fri</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" id="chksat" value="7" type="checkbox"><span></span>
                        <em>Sat</em>
                    </label>
                </div>
            </div>
                    
            
        </div>
            
            <div class="custRow">
                <div class="custCol-3">
                <a class="btnAction action bgLightPurple" id="add_query" > Add</a>
            </div>
            </div>
        </div>
        
        
        
        
        
       
        <div class="listHolderType1">
            <div class="listerType1"> 
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>Check Point</td>
                            <td>Alias</td>
                            <td>All Days</td>
                            <td>Sun</td>
                            <td>Mon</td>
                            <td>Tue</td>
                            <td>Wed</td>
                            <td>Thu</td>
                            <td>Fri</td>
                            <td>Sat</td>
                            <td>Edit</td>
                            <td>Remove</td>
                        </tr>
                    </thead>
                    <tbody id="checkquerylist">
                        <tr><td>No check point added</td></tr>
                    </tbody>
                </table>

            </div>					
        </div>

    </form>
    <div class="customClear"></div>
    <div class="commonLoaderV1"></div>
    
    <div class="custRow">
        <div class="custCol-4">
            <input type="button" value="Create" id="btnCreatechecklist" class="commonBtn bgGreen addBtn" >
        </div>
    </div>
</div>
<script>
    var arrPointList = [];
    var intGlobalEditIndex=-1;
    $(document).ready(function ()
    {

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
                category_id: {required: true},
                job_id: {required: true},
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                category_id: "Select Branch",
                job_id: "Select Job Pisition",
            }
        });

        $("#frmquerylist").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                checkpoint: {required: true},
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                checkpoint: "Enter Check Point",
            }
        });


        $('#btnCreatechecklist').on('click', function () {

            if (!$("#frmmain").valid()) {
                return;
            }

            if (arrPointList.length == 0) {
                alert("Please Add Atleast One Check Point");
                return;
            }

            var arraData = {
                category_id: $("#category_id").val(),
                job_id: $("#job_id").val(),
            }

            arraData = encodeURIComponent(JSON.stringify(arraData));
            var arrQueries = encodeURIComponent(JSON.stringify(arrPointList));
            
            $('#btnCreatechecklist').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../check_list/store',
                data: '&arraData=' + arraData + '&arrPointList=' + arrQueries,
                success: function (return_data) {
                    $('#btnCreatechecklist').removeAttr('disabled');
                    window.location.href = '{{url("checklist/check_list")}}';
                },
                error: function (return_data) {
                    $('#btnCreatechecklist').removeAttr('disabled');
                    window.location.href = '{{url("checklist/check_list")}}';
                }
            });
            
            $('#btnCreatechecklist').removeAttr('disabled');
        })
        
        $('#chkallday').click(function() {
        
            if ($('#chkallday').prop("checked")) {
                $('.chkweekdays').prop("checked", true);
            } else {
                $('.chkweekdays').prop("checked", false);
            }

        });
        
        $('body').on('click', '.chkweekdays', function() {
        
            if($('input:checkbox.chkweekdays:not(:checked)').length==0){
                $('#chkallday').prop("checked", true);
            }else{
                $('#chkallday').prop("checked", false);
            }

        });
        


        $('#add_query').on('click', function () {
            
            if (!$("#frmquerylist").valid()) {
                return;
            }
                  
            if($('input:checkbox.chkdays:checked').length==0){
                alert("Please Select Days");
                return;
            }
            
            var allday="";
            var sunday="";
            var monday="";
            var tuesday="";
            var wednesday="";
            var thursday="";
            var friday="";
            var satday="";
            
            if($("#chksun").prop('checked') == true){
                sunday="Yes";
            }else{
                sunday="No";
            }
            
            if($("#chkmon").prop('checked') == true){
                monday="Yes";
            }else{
                monday="No";
            }
            
            if($("#chktue").prop('checked') == true){
                tuesday="Yes";
            }else{
                tuesday="No";
            }
            
            if($("#chkwed").prop('checked') == true){
                wednesday="Yes";
            }else{
                wednesday="No";
            }
            
            if($("#chkthu").prop('checked') == true){
                thursday="Yes";
            }else{
                thursday="No";
            }
            
            if($("#chkfri").prop('checked') == true){
                friday="Yes";
            }else{
                friday="No";
            }
            
            if($("#chksat").prop('checked') == true){
                satday="Yes";
            }else{
                satday="No";
            }
            
            if($("#chkallday").prop('checked') == true){
                allday="Yes";
            }else{
                allday="No";
            }
            
            var arraData = {
                checkpoint: $("#checkpoint").val(),
                alias: $("#alias").val(),
                allday: allday,
                sunday: sunday,
                monday: monday,
                tuesday: tuesday,
                wednesday: wednesday,
                thursday: thursday,
                friday: friday,
                satday: satday
            }
            
            if(intGlobalEditIndex!=-1){
                arrPointList.splice(intGlobalEditIndex, 1);
                intGlobalEditIndex=-1;
            }
            
            arrPointList.push(arraData);
            $("#checkpoint").val('');
            $("#alias").val('');
            $('.chkdays').prop("checked", false);
            showquerylist();

        })
        
    });

    function showquerylist()
    {
        $("#checkquerylist").html('<tr><td>No check point added<td></tr>');
        if (arrPointList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrPointList.length; i++) {
                strHtml += '<tr><td>' + arrPointList[i].checkpoint + '</td><td>' + arrPointList[i].alias + '</td><td>' + arrPointList[i].allday + '</td><td>' + arrPointList[i].sunday + '</td><td>' + arrPointList[i].monday + '</td>\n\
                            <td>' + arrPointList[i].tuesday + '</td><td>' + arrPointList[i].wednesday + '</td><td>' + arrPointList[i].thursday + '</td><td>' + arrPointList[i].friday + '</td><td>' + arrPointList[i].satday + '</td>\n\
                            <td><a href="javascript:edit(' + i + ')" class="btnEditPoint">Edit</a></td><td><a href="javascript:remove(' + i + ')" class="btnTaskDelete styleList">Remove</a></td></tr>';
            }
            $("#checkquerylist").html(strHtml);
        }
    }

    function remove(index) {
        arrPointList.splice(index, 1);
        showquerylist();
    }
    
    function edit(index) {
        $("#checkpoint").val(arrPointList[index].checkpoint);
        $("#alias").val(arrPointList[index].alias);
        if(arrPointList[index].allday=="Yes"){
            $('#chkallday').prop("checked", true);
        }else{
            $('#chkallday').prop("checked", false);
        }
        
        if(arrPointList[index].sunday=="Yes"){
            $('#chksun').prop("checked", true);
        }else{
            $('#chksun').prop("checked", false);
        }
        
        if(arrPointList[index].monday=="Yes"){
            $('#chkmon').prop("checked", true);
        }else{
            $('#chkmon').prop("checked", false);
        }
        
        if(arrPointList[index].tuesday=="Yes"){
            $('#chktue').prop("checked", true);
        }else{
            $('#chktue').prop("checked", false);
        }
        
        if(arrPointList[index].wednesday=="Yes"){
            $('#chkwed').prop("checked", true);
        }else{
            $('#chkwed').prop("checked", false);
        }
        
        if(arrPointList[index].thursday=="Yes"){
            $('#chkthu').prop("checked", true);
        }else{
            $('#chkthu').prop("checked", false);
        }
        
        if(arrPointList[index].friday=="Yes"){
            $('#chkfri').prop("checked", true);
        }else{
            $('#chkfri').prop("checked", false);
        }
        
        if(arrPointList[index].satday=="Yes"){
            $('#chksat').prop("checked", true);
        }else{
            $('#chksat').prop("checked", false);
        }
        intGlobalEditIndex=index;
    }

</script>
@endsection