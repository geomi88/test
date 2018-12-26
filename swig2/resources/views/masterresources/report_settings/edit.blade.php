@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />
<script>
    var arrMailRecipients=<?php echo $arrMailRecipients?>;
    var arrExemptedEmps=<?php echo $arrExemptedEmps?>;
</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('masterresources/report_settings')}}">Back</a>
    <header class="pageTitle">
        <h1>Edit <span>Settings</span></h1>
    </header>	

    <form action="" method="post" id="frmreportsettings">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Report Name</label>
                        <input type="hidden" id="editid" name="editid" value="{{$report_settings->id}}">
                        <select class="" name="report_name" id="report_name" class="report_name">
                            <option value="">Select</option>
                            @foreach ($arrReports as $arrReport)
                            <?php $value=str_replace(" ", "_", $arrReport);?>
                            <option value="{{$value}}" <?php if($value==$report_settings->report_name){ echo "selected";} ?> >{{$arrReport}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Send Option</label>
                        <select class="repeatOptn" name="send_option" id="send_option">
                            <option value="">Select</option>
                            <option <?php if($report_settings->type=="Daily"){ echo "selected";} ?>>Daily</option>
                            <option <?php if($report_settings->type=="Weekly"){ echo "selected";} ?>>Weekly</option>
                            <option <?php if($report_settings->type=="Monthly"){ echo "selected";} ?>>Monthly</option>
                        </select>
                    </div>
                </div>

                <div class="custCol-3 clsdays" style="display: none;">
                    <div class="inputHolder">
                        <label>Day</label>
                        <select  name="cmbday" id="cmbday">
                            <option value=''>Select</option>
                            <option value='1' <?php if($report_settings->day==1){ echo "selected";} ?>>Sun</option>
                            <option value='2' <?php if($report_settings->day==2){ echo "selected";} ?>>Mon</option>
                            <option value='3' <?php if($report_settings->day==3){ echo "selected";} ?>>Tue</option>
                            <option value='4' <?php if($report_settings->day==4){ echo "selected";} ?>>Wed</option>
                            <option value='5' <?php if($report_settings->day==5){ echo "selected";} ?>>Thu</option>
                            <option value='6' <?php if($report_settings->day==6){ echo "selected";} ?>>Fri</option>
                            <option value='7' <?php if($report_settings->day==7){ echo "selected";} ?>>Sat</option>

                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Time</label>                        
                        <input type="text" name="sendtime" id="sendtime" value="{{$report_settings->time}}" class="txt">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            </div>
    </form>
    
    <form id="frmRecepient">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label>Mail Recipient</label>
                        <div class="bgSelect">                                                          
                            <input type="text"  name="recipient" id="recipient" class="" autocomplete="off" placeholder="Enter Employee Code or Name">
                            <ul class="add_recepient_list classscroll">
                            </ul>

                            <input type="hidden"  name="recipient_id" id="recipient_id" >
                            <input type="hidden"  name="recipient_code" id="recipient_code" >
                            <input type="hidden"  name="recipient_name" id="recipient_name" >
                            <input type="hidden"  name="recipient_email" id="recipient_email" >
                        </div>
                    </div>
                </div>
                <div class="custCol-3" >
                    <a class="btnAction action bgLightPurple" style="margin-top: 40px;" id="add_recepient" > Add</a>
                </div>
            </div>
        

        <div class="listHolderType1">
            <div class="listerType1"> 
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>Code</td>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Remove</td>
                        </tr>
                    </thead>
                    <tbody id="tblrecepients">
                        <tr><td>No mail recipient added</td></tr>
                    </tbody>
                </table>

            </div>

        </div>
        </div>
    </form>
    
    <form id="frmExemted">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label>Exempted Employees</label>
                        <div class="bgSelect">                                                          
                            <input type="text"  name="exempted" id="exempted" class="" autocomplete="off" placeholder="Enter Employee Code or Name">
                            <ul class="add_exempted_list classscroll">
                            </ul>

                            <input type="hidden"  name="exempted_id" id="exempted_id" >
                            <input type="hidden"  name="exempted_code" id="exempted_code" >
                            <input type="hidden"  name="exempted_name" id="exempted_name" >
                        </div>
                    </div>
                </div>
                <div class="custCol-3" >
                    <a class="btnAction action bgLightPurple" style="margin-top: 40px;" id="add_exempted" > Add</a>
                </div>
            </div>

            <div class="listHolderType1">
                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Code</td>
                                <td>Name</td>
                                <td>Remove</td>
                            </tr>
                        </thead>
                        <tbody id="tblexempted">
                            <tr><td>No exempted employees added</td></tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </form>
    
    
    <div class="custRow">
        <div class="custCol-4">
            <input type="button" value="Update" class="commonBtn bgGreen addBtn" id="btnUpdateSettings">
        </div>
    </div>


<script>
    
    var arrRecepientList = arrMailRecipients;
    var arrExemptedList = arrExemptedEmps;
    $(document).ready(function()
    {
        showrecepientlist();
        showexemptedlist();
        if($('#send_option').val()=="Weekly"){
            $(".clsdays").show();
        }
        $('#sendtime').timepicker({'timeFormat': 'H:i', 'disableTextInput': true,'step': '30'});
        
        $( "#frmreportsettings" ).validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function(element, errorClass){
                        $(element).addClass('valErrorV1');
                    },
                    unhighlight: function(element, errorClass, validClass){
                        $(element).removeClass("valErrorV1");
                    },

            rules: {
                    report_name: {required: true},  
                    send_option: {required: true},  
                    cmbday: {required: true},  
                    sendtime: {required: true},  
                },
                messages: {
                    report_name:{required: "Select Report"},
                    send_option:{required: "Select Send Option"},
                    cmbday:{required: "Select Day"},
                    sendtime:{required: "Select Send Time"}
                }
        });
        
        $("#frmRecepient" ).validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function(element, errorClass){
                        $(element).addClass('valErrorV1');
                    },
                    unhighlight: function(element, errorClass, validClass){
                        $(element).removeClass("valErrorV1");
                    },

            rules: {

                recipient: {
                        required: {
                            depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                        },
                    }  
                },
                messages: {recipient:{required: "Select Employee"}}
        });
        
         $("#frmExemted" ).validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function(element, errorClass){
                        $(element).addClass('valErrorV1');
                    },
                    unhighlight: function(element, errorClass, validClass){
                        $(element).removeClass("valErrorV1");
                    },

            rules: {

                exempted: {
                        required: {
                            depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                        },
                    }  
                },
                messages: {exempted:{required: "Select Employee"}}
        });

        $('#send_option').on('change', function() {
            if($('#send_option').val()=="Weekly"){
                $(".clsdays").show();
            }else{
                $(".clsdays").hide();
            }
        });
        
        $('#btnUpdateSettings').click(function (e) {
            if (!$('#frmreportsettings').valid()) {
                return false;
            }

            if(arrRecepientList.length==0){
                alert("Please add atleast one mail recipient");
            }
            
                        var intDulpicate=0;
            $.ajax({
                type: 'POST',
                url: '../checksettingsexistornot',
                data: '&report_name='+$("#report_name").val() + '&editid='+$("#editid").val(),
                async:false,
                success: function (return_data) {
                   intDulpicate=return_data;
                }
            });
            
            if(intDulpicate==1){
                var blnConfirm=confirm("Already existing settings for "+$("#report_name :selected").text()+" will be removed, are you sure to continue ?");
                if(!blnConfirm){
                    return;
                }
            }
            
            var arraData = {
                report_name: $("#report_name").val(),
                send_option: $("#send_option").val(),
                cmbday: $("#cmbday").val(),
                sendtime: $("#sendtime").val(),
                editid: $("#editid").val(),
            }

            arraData = encodeURIComponent(JSON.stringify(arraData));
            var arrRecepients = encodeURIComponent(JSON.stringify(arrRecepientList));
            var arrExempteds = encodeURIComponent(JSON.stringify(arrExemptedList));
            
            $('#btnUpdateSettings').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../update',
                data: '&arraData=' + arraData + '&arrRecepients=' + arrRecepients + '&arrExempteds=' + arrExempteds,
                success: function (return_data) {
                    $('#btnUpdateSettings').removeAttr('disabled');
                    window.location.href = '{{url("masterresources/report_settings")}}';
                },
                error: function (return_data) {
                    $('#btnUpdateSettings').removeAttr('disabled');
                    window.location.href = '{{url("masterresources/report_settings")}}';
                }
            });
            
            $('#btnUpdateSettings').removeAttr('disabled');
            
        });
    
        $('#recipient').keyup(function () {
            $(".add_recepient_list").html('');
            $('#recipient_id').val('');
            $('#recipient_code').val('');
            $('#recipient_name').val('');
            $('#recipient_email').val('');
            var searchkey = $(this).val();
            
            jQuery.ajax({
                url: "../autocompleteemployee",
                type: 'POST',
                data: {searchkey: searchkey},
                success:function (result) {
                        var total = result.length;
                        if (total == 0) {
                            $('#recipient').val('');
                        }
                        var liname = '';
                        $.each(result, function (i, value) {
                            var stremail;
                            if(value['email']=='' || value['email']==null){
                                stremail=value['contact_email'];
                            }else{
                                stremail=value['email'];
                            }
                            
                            liname += '<li id=' + value['id'] + ' attrcode=' + value['username'] + ' attrname="' + value['first_name'] + '" attremail=' + stremail + '>' + value['first_name'] +' (' + value['username'] + ')' + '</li>';
                        });

                        $(".add_recepient_list").append(liname);

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

                                $('#recipient').val(selectval);

                                $(this).parent().parent().find('.commonError').hide();
                        });


                        $('.bgSelect li').click(function () {
                            $('#recipient_id').val($(this).attr('id'));
                            $('#recipient_code').val($(this).attr('attrcode'));
                            $('#recipient_name').val($(this).attr('attrname'));
                            $('#recipient_email').val($(this).attr('attremail'));
                        });
                    },
            });
        });
        
        $('#exempted').keyup(function () {
            $(".add_exempted_list").html('');
            $('#exempted_id').val('');
            $('#exempted_code').val('');
            $('#exempted_name').val('');
            var searchkey = $(this).val();
            
            jQuery.ajax({
                url: "../autocompleteemployee",
                type: 'POST',
                data: {searchkey: searchkey},
                success:function (result) {
                        var total = result.length;
                        if (total == 0) {
                            $('#exempted').val('');
                        }
                        var liname = '';
                        $.each(result, function (i, value) {
                            liname += '<li id=' + value['id'] + ' attrcode=' + value['username'] + ' attrname="' + value['first_name'] + '">' + value['first_name'] +' (' + value['username'] + ')' + '</li>';
                        });

                        $(".add_exempted_list").append(liname);

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

                                $('#exempted').val(selectval);

                                $(this).parent().parent().find('.commonError').hide();
                        });


                        $('.bgSelect li').click(function () {
                            $('#exempted_id').val($(this).attr('id'));
                            $('#exempted_code').val($(this).attr('attrcode'));
                            $('#exempted_name').val($(this).attr('attrname'));
                        });
                    },
            });
        });
    
        $('#add_recepient').on('click', function () {

            if (!$("#frmRecepient").valid()) {
                return;
            }
            
            if($('#recipient_id').val()==''){
                alert("Please Select Employee");
                return;
            }
            
            var intItemDuplicate = 0;
            if (arrRecepientList.length > 0) {
                for (var i = 0; i < arrRecepientList.length; i++) {
                    if ($('#recipient_id').val() == arrRecepientList[i].recipient_id) {
                        intItemDuplicate = 1;
                    }
                }
            }

            var arraData = {
                recipient_id: $('#recipient_id').val(),
                recipient_code: $('#recipient_code').val(),
                recipient_name: $("#recipient_name").val(),
                recipient_email: $("#recipient_email").val(),
            }
            
            if (intItemDuplicate != 1) {
                arrRecepientList.push(arraData);
                $('#recipient').val('');
                $('#recipient_id').val('');
                $('#recipient_code').val('');
                $('#recipient_name').val('');
                $('#recipient_email').val('');
                showrecepientlist();
            }else{
                alert("Employee Already Selected");
            }
        });
        
        $('#add_exempted').on('click', function () {

            if (!$("#frmExemted").valid()) {
                return;
            }
            
            if($('#exempted_id').val()==''){
                alert("Please Select Employee");
                return;
            }
            
            var intItemDuplicate = 0;
            if (arrExemptedList.length > 0) {
                for (var i = 0; i < arrExemptedList.length; i++) {
                    if ($('#exempted_id').val() == arrExemptedList[i].exempted_id) {
                        intItemDuplicate = 1;
                    }
                }
            }

            var arraData = {
                exempted_id: $('#exempted_id').val(),
                exempted_code: $('#exempted_code').val(),
                exempted_name: $("#exempted_name").val(),
            }
            
            if (intItemDuplicate != 1) {
                arrExemptedList.push(arraData);
                $('#exempted').val('');
                $('#exempted_id').val('');
                $('#exempted_code').val('');
                $('#exempted_name').val('');
                showexemptedlist();
            }else{
                alert("Employee Already Selected");
            }

        });
        
    });

    function showrecepientlist()
    {
        $("#tblrecepients").html('<tr><td>No mail recipient added<td></tr>');
        if (arrRecepientList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrRecepientList.length; i++) {
                strHtml += '<tr><td>' + arrRecepientList[i].recipient_code + '</td><td>' + arrRecepientList[i].recipient_name + '</td><td>' + arrRecepientList[i].recipient_email + '</td>\n\
                            <td><a href="javascript:removerecepievt(' + i + ')" class="btnTaskDelete styleList">Remove</a></td></tr>';
            }
            $("#tblrecepients").html(strHtml);
        }
    }

    function removerecepievt(index) {
        arrRecepientList.splice(index, 1);
        showrecepientlist();
    }
    
    
    function showexemptedlist()
    {
        $("#tblexempted").html('<tr><td>No exempted employees added<td></tr>');
        if (arrExemptedList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrExemptedList.length; i++) {
                strHtml += '<tr><td>' + arrExemptedList[i].exempted_code + '</td><td>' + arrExemptedList[i].exempted_name + '</td>\n\
                            <td><a href="javascript:removeexempted(' + i + ')" class="btnTaskDelete styleList">Remove</a></td></tr>';
            }
            $("#tblexempted").html(strHtml);
        }
    }

    function removeexempted(index) {
        arrExemptedList.splice(index, 1);
        showexemptedlist();
    }
    
</script>
@endsection
