@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Update <span>Check Point</span></h1>
    </header>	

    <form action="" method="post" id="frmmain">
        <div class="fieldGroup" id="fieldSet1">

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Check List Category</label>
                        <input type="hidden" name="cid" id="cid" value='{{ $check_point->id }}'>                       
                        <select  name="category_id" id="category_id">
                            <option selected value=''>Select Category</option>
                            @foreach ($categories as $categorie)
                            <option <?php if($check_point->category_id==$categorie->id){echo "Selected";}?> value='{{ $categorie->id }}'>{{$categorie->name}}</option>
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
                            <option <?php if($check_point->job_position==$job_position->id){echo "Selected";}?> value='{{ $job_position->id }}'><?php echo str_replace("_", " ", $job_position->name)?></option>
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
                    <textarea name="checkpoint" id="checkpoint" placeholder="Enter Check Point">{{$check_point->checkpoint}}</textarea>
                </div>
            </div>
            <div class="custCol-6">
                <div class="inputHolder">
                    <label>Alias</label>
                    <textarea name="alias" id="alias" placeholder="Enter Alias">{{$check_point->alias}}</textarea>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays" <?php if($check_point->all_day==1 || count($arrDays)==7){echo "checked";}?> id="chkallday" value="8" type="checkbox"><span></span>
                        <em>All Days</em>
                    </label>
                </div>
            </div>
           
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" <?php if(in_array("1",$arrDays)){ echo "checked";}?> id="chksun" value="1" type="checkbox"><span></span>
                        <em>Sun</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" <?php if(in_array("2",$arrDays)){ echo "checked";}?> id="chkmon" value="2" type="checkbox"><span></span>
                        <em>Mon</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" <?php if(in_array("3",$arrDays)){ echo "checked";}?> id="chktue" value="3" type="checkbox"><span></span>
                        <em>Tue</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" <?php if(in_array("4",$arrDays)){ echo "checked";}?> id="chkwed" value="4" type="checkbox"><span></span>
                        <em>Wed</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" <?php if(in_array("5",$arrDays)){ echo "checked";}?> id="chkthu" value="5" type="checkbox"><span></span>
                        <em>Thu</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" <?php if(in_array("6",$arrDays)){ echo "checked";}?> id="chkfri" value="6" type="checkbox"><span></span>
                        <em>Fri</em>
                    </label>
                </div>
            </div>
            <div class="custCol-3">
                <div class="commonCheckHolder">
                    <label>
                        <input class="chkdays chkweekdays" <?php if(in_array("7",$arrDays)){ echo "checked";}?> id="chksat" value="7" type="checkbox"><span></span>
                        <em>Sat</em>
                    </label>
                </div>
            </div>
                    
            
        </div>
            
           
        </div>
        

    </form>
    <div class="customClear"></div>
    <div class="commonLoaderV1"></div>
    
    <div class="custRow">
        <div class="custCol-4">
            <input type="button" value="Update" id="btnUpdatecheckpoint" class="commonBtn bgGreen addBtn" >
        </div>
    </div>
</div>
<script>
    
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
                job_id: "Select Job Position",
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


        $('#btnUpdatecheckpoint').on('click', function () {

            if (!$("#frmmain").valid()) {
                return;
            }
            
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
                point_id:$("#cid").val(),
                category_id: $("#category_id").val(),
                alias: $("#alias").val(),
                job_id: $("#job_id").val(),
                checkpoint: $("#checkpoint").val(),
                allday: allday,
                sunday: sunday,
                monday: monday,
                tuesday: tuesday,
                wednesday: wednesday,
                thursday: thursday,
                friday: friday,
                satday: satday
            }

            arraData = encodeURIComponent(JSON.stringify(arraData));
            
            
            $('#btnUpdatecheckpoint').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../update',
                data: '&arraData=' + arraData,
                success: function (return_data) {
                    $('#btnUpdatecheckpoint').removeAttr('disabled');
                    window.location.href = '{{url("checklist/check_list")}}';
                },
                error: function (return_data) {
                    $('#btnUpdatecheckpoint').removeAttr('disabled');
                    window.location.href = '{{url("checklist/check_list")}}';
                }
            });
            
            $('#btnUpdatecheckpoint').removeAttr('disabled');
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
        
    });

</script>
@endsection