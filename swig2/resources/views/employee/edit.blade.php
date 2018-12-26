@extends('layouts.main')

@section('content')
<style>
    .bgSelection{
        position: relative;
    }
    .bgSelection ul {
    background: #fff;
    border-left: 1px solid #cad0d7;
    border-right: 1px solid #cad0d7;
    border-bottom: 1px solid #cad0d7;
    box-sizing: border-box;
    display: none;
    margin: 0;
    padding: 0;
    position: absolute;
    top: 37px;
    left: 0;
    width: 100%;
    z-index: 1;
}

.bgSelection li {
    font-size: 14px;
    padding: 10px 12px;
}

.bgSelection li:hover {
    background: #f9f9f9;
    cursor: pointer;
}
</style>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Employee</span></h1>
    </header>


    <div class="formListV1">
        <input type="hidden" id="div_id" value="{{$div_id}}">
        <form action="" method="post" id="employee_updation" enctype="multipart/form-data">

            <div class="formListV1Dtl" id="step1">
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li><span>2</span></li>
                        <li><span>3</span></li>
                    </ul>
                </div>
                <h3>Step 1</h3>
                <div class="fieldGroup" id="fieldSet1">
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder employeeCode">
                                <label>Employee Code</label>
                                <input type="text" name="employee_code" id="employee_code" value="{{$employee_data->username}}" onpaste="return false;" autocomplete="off" placeholder="Enter Employee Code">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <!--<div class="custCol-4">
                            <div class="inputHolder">
                                <label>Password</label>
                                <input type="text" name="password" id="password">
                                <span class="commonError"></span>
                            </div>
                        </div>-->
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Employee First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{$employee_data->first_name}}" placeholder="Enter First Name">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>

                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Employee Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" value="{{$employee_data->middle_name}}" placeholder="Enter Middle Name">
                                <span class="commonError">Text</span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Employee Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{$employee_data->last_name}}" placeholder="Enter Last Name">
                                <span class="commonError">Text</span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder passportno">
                                <label>Passport Number</label>
                                <input type="text" name="passport_number" id="passport_number" value="{{$employee_data->passport_number}}" placeholder="Enter Passport Number">
                            </div>
                        </div>
                    </div>
                    <div class="custRow">

                        <div class="custCol-4">
                            <div class="inputHolder resid">
                                <label>Residence ID Number</label>
                                <input type="text" name="residence_id_number" id="residence_id_number" value="{{$employee_data->residence_id_number}}" placeholder="Enter Residence ID">
                                <span class="commonError">Text</span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Alias Name</label>
                                <input type="text" name="alias_name" id="alias_name" value="{{$employee_data->alias_name}}" placeholder="Enter Alias Name">
                            </div>
                        </div>
                        
                       <div class="custCol-4">
                            <div class="inputHolder gossino">
                                <label>Gossi Number</label>
                                <input type="text" name="gossi_number" id="gossi_number" value="{{$employee_data->gossi_number}}" placeholder="Enter Gossi Number">
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Mobile number</label>
                                <input type="text" name="mobile_number" id="mobile_number" value="{{$employee_data->mobile_number}}" placeholder="Enter Mobile Number">
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Password</label>
                                <input type="text" name="password" id="password" placeholder="Update Password" onkeyup="CheckPasswordStrength(this.value)">
                                <span id="password_strength"></span>
                            </div>
                        </div>
                            <div class="custCol-4">
                                    <div class="inputHolder fieldMargin">
                                        <label>Date of Hiring</label>
                                        <input type="text" name="date_of_hiring" id="date_of_hiring" readonly="readonly" value="{{$employee_data->date_of_hiring}}" placeholder="Enter Date of Hiring">
                                    </div>
                                </div>
                    </div>

                    <div class="custRow">
                        <div class="custCol-8">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <div class="inputHolder emailID">
                                        <label>Email Id</label>
                                        <input type="text" class="email-group" name="email" id="email" onpaste="return false;" value="{{$employee_data->email}}" autocomplete="off" placeholder="Enter Email Id">
                                    </div>
                                </div>
                                <div class="custCol-6">
                                    <div class="inputHolder emailID">
                                        <label>Contact Email Id</label>
                                        <input type="text" class="email-group" name="contact_email" id="contact_email" value="{{$employee_data->contact_email}}" onpaste="return false;" autocomplete="off" placeholder="Enter Contact Email Id">
                                    </div>
                                </div>
                                <div class="custCol-6">
                                    <div class="inputHolder fieldMargin">
                                        <label>Date of Birth</label>
                                        <input type="text" name="dob" id="dob" readonly="readonly" value="{{$employee_data->dob}}" placeholder="Enter Date Of Birth">
                                    </div>
                                </div>
                            </div>
                            <div class="custRow">
                                <div class="custCol-6">
                                    <div class="inputHolder bgSelect fieldMargin">
                                        <label>Gender</label>
                                        <?php
                                        $g = 1;
                                        ?>
                                        @foreach ($gender_list as $gender)

                                        <div class="commonCheckHolder radioRender">
                                            <label>

                                                <input type="radio" name="gender" id="gender" value="{{ $gender->id }}" <?php if ($gender->id == $employee_data->gender) { ?> checked <?php } ?>>
                                                <span></span>
                                                <em>{{ $gender->name}}</em>
                                            </label>
                                        </div>
                                        <?php
                                        $g++;
                                        ?>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Current Address </label>
                                <textarea name="current_address" id="current_address" placeholder="Enter Address">{{$employee_data->current_address}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-6">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Nationality Type</label>                                        
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="nationality_type" name="nationality_type" id="nationality_type" value="0" <?php if ($employee_data->nationality_type == 0) { ?> checked <?php } ?>>
                                        <span></span>
                                        <em>Saudi</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="nationality_type" name="nationality_type" id="nationality_type" value="1" <?php if ($employee_data->nationality_type == 1) { ?> checked <?php } ?>>
                                        <span></span>
                                        <em>Foreigner</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">

                        <div class="custCol-4">
                            <div class="inputHolder nat">
                                <label>Choose Nationality</label>
                                <select class="chosen-select" name="nationality" id="nationality">
                                    @foreach ($countries as $country)
                                    <option value='{{ $country->id }}' <?php
                                    if ($country->id == $employee_data->nationality) {
                                        echo "selected";
                                    }
                                    ?>>{{ $country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="custCol-4">

                        </div>
                        <div class="custCol-6">

                        </div>
                    </div>
                </div>

                <div class="custRow">
                    <div class="custCol-4">
                    </div>
                    <input type="hidden" id="employee_code_status">
                    <div class="custCol-8">
                        <!--<a href="javascript:void(0)" class="commonBtn bgGreen addNext right addemployeeBtn" rel="#step2">Save and Go</a>-->
                        <button class="commonBtn bgGreen open1 right addNext" type="button">Save and Go <span class="fa fa-arrow-right"></span></button> 
                        <div class="customClear"></div>
                    </div>
                </div>
            </div>


            <div class="formListV1Dtl" id="step2" style="display: none;">
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li class="selected"><span>2</span></li>
                        <li><span>3</span></li>
                    </ul>
                </div>
                <h3>Step 2</h3>
                <div class="stepFields">
                    <div class="custRow">
                        <!--                        <div class="custCol-4">
                                                    <div class="inputHolder">
                                                        <label>Company &amp; Division </label>
                                                                   
                                                    </div>
                                                </div>-->
                        <input type="hidden" name="company" id="company" onpaste="return false;" autocomplete="off" value="{{ $companies }}">

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Division</label>
                                <select class="chosen-select" name="division" id="division">
                                    @foreach ($divisions as $division)
                                    <option <?php echo ($division->id == $employee_data->division) ? "selected" : "" ?> value='{{ $division->id }}'>{{ $division->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <input type="hidden" id="curPosition" name="curPosition" value="{{ $employee_data->job_position }}">
                                <label>System Position</label>
                                <select class="chosen-select" name="job_position" id="job_position">
                                    @foreach ($job_positions as $position)
                                    <option <?php echo ($position->id == $employee_data->job_position) ? "selected" : "" ?> value='{{ $position->id }}'><?php echo str_replace('_', ' ', $position->name); ?></option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                      
                   
                         <div class="custCol-4">
                            <div class="inputHolder">
                              <label>ID Profession</label>
                                <select class="chosen-select" name="id_professional" id="id_professional">
                                    @foreach ($id_professional as $idprofs)
                                    <option <?php echo ($idprofs->id == $employee_data->id_professional) ? "selected" : "" ?> value='{{ $idprofs->id }}'><?php echo str_replace('_', ' ', $idprofs->name); ?></option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        

  
                        

                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-4">

                            <div class="inputHolder">
                                <label>Top Manager</label>
                                <select class="chosen-select" name="top_manager" id="top_manager">
                                    @foreach ($employees as $employee)
                                    <option <?php echo ($employee->id == $employee_data->top_manager_id) ? "selected" : "" ?> value='{{ $employee->id }}'><?php echo $employee->first_name . ' (' . $employee->username . ')'; ?></option>
                                    @endforeach
                                </select>



                            </div>
                        </div>
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Region</label>
                                    <select class="chosen-select" name="region" id="region">
                                        @foreach ($regions as $region)
                                        <option <?php echo ($region->id == $employee_data->region) ? "selected" : "" ?> value='{{ $region->id }}'>{{ $region->name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                        
                    </div>
                    
                    
                           <div class="custRow">
                                <div class="custCol-4">
                           
                                <div class="inputHolder">
                                <label>Basic Salary</label>
                                <input type="text"  name="basic_salary" id="basic_salary" class="" value="{{$employee_data->basic_salary}}" placeholder="Enter Basic Salary" autocomplete="off">
                                </div>

                                 </div>
                               
                                <div class="custCol-4">
                           
                                <div class="inputHolder">
                                <label>Housing Allowance</label>
                                <input type="text"  name="housing_allowance" id="housing_allowance" class="" value="{{$employee_data->housing_allowance}}" placeholder="Enter Housing Allowance" autocomplete="off">
                                </div>

                                 </div>
                               
                                 <div class="custCol-4">
                           
                                <div class="inputHolder">
                                <label>Transportation Allowance</label>
                                <input type="text"  name="transportation_allowance" id="transportation_allowance" class="" value="{{$employee_data->transportation_allowance}}" placeholder="Enter Transportation Allowance" autocomplete="off">
                                </div>

                                 </div>
                    </div>
                    
                        <div class="custRow">
                               
                               
                                <div class="custCol-4">
                           
                                <div class="inputHolder">
                                <label>Food Allowance</label>
                                <input type="text"  name="food_allowance" id="food_allowance" class="" value="{{$employee_data->food_allowance}}" placeholder="Enter Food Allowance" autocomplete="off">
                                </div>

                                 </div>
                               
                                 <div class="custCol-4">
                           
                                <div class="inputHolder">
                                <label>Other Expense</label>
                                <input type="text"  name="other_expense" id="other_expense" class="" value="{{$employee_data->other_expense}}" placeholder="Enter Other Allowance" autocomplete="off">
                                </div>

                                 </div>
                    </div>
                    
                    
                    <div class="custRow">
                        <div class="docText">
                            <div class="inputHolder">
                                <label>Job Description Agreement</label>
                                <textarea name="job_description" id="job_description" >{{$employee_data->job_description}}</textarea>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <button class="commonBtn bgGreen addPrev  back2 " type="button">Previous <span class="fa fa-arrow-right"></span></button> 
                    </div>
                    <div class="custCol-8">
                        <!--                        <a href="javascript:void(0)" class="commonBtn bgGreen addNext right updateEmployeeBtn" rel="#step3">Save and Go</a>-->
                        <button class="commonBtn bgGreen right addNext open2  " type="button">Save and Go <span class="fa fa-arrow-right"></span></button> 
                        <div class="customClear"></div>
                    </div>
                </div>
            </div>
        </form>


        <div class="formListV1Dtl" id="step4" style="display: none;">

            <form action="{{url('employee/update_upload_documents')}}" method="post" id="docuform" enctype='multipart/form-data'>
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li class="selected"><span>2</span></li>
                        <li class="selected"><span>3</span></li>
                    </ul>
                </div>
                <h3>Step 3</h3>
                <div class="custRow">
                    <div class="custCol-8">                            
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Upload Profile Image</label>
                                    <input type="file" onchange="readURL(this);" id="profilepic" class="profilepic" name="profilepic"  accept="image/*"/>      
                                    <span class="commonError"></span>
                                    <img id="profile_previewpic">
                                </div>
                            </div>
                            <?php if ($employee_data->profilepic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$employee_data->profilepic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Identification Card</label>
                                    <input type="file" onchange="indentpicurl(this);" id="indentpic" class="profilepic" name="indentpic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="indentpic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($id_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" name='id_pic' value href="{{$id_pic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Job Description</label>
                                    <input type="file" onchange="jobprofilepicurl(this);" id="jobprofilepic" class="profilepic" name="jobprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="jobprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($job_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$job_pic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Contract</label>
                                    <input type="file" onchange="conprofilepicurl(this);" id="conprofilepic" class="profilepic" name="conprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="conprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($contract_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$contract_pic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Starting From</label>
                                    <input type="file" onchange="stprofilepicurl(this);" id="stprofilepic" class="profilepic" name="stprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="stprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($st_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$st_pic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Personal Profile</label>
                                    <input type="file" onchange="perprofilepicurl(this);" id="perprofilepic" class="profilepic" name="perprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="perprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($pp_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$pp_pic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Qualification</label>
                                    <input type="file" onchange="qprofilepicurl(this);" id="qprofilepic" class="profilepic" name="qprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="qprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($q_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$q_pic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>CV</label>
                                    <input type="file" onchange="cvprofilepicurl(this);" id="cvprofilepic" class="profilepic" name="cvprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="cvprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($c_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$c_pic}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Arabic CV</label>
                                    <input type="file" onchange="arabiccvurl(this);" id="cvarabic" class="profilepic" name="cvarabic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="arabiccv_previewpic">-->
                                </div>
                            </div>
                            <?php if ($acv_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$acv_pic}}">View</a>
                            <?php } ?>
                        </div>

                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Job Offer</label>
                                    <input type="file" onchange="joboffprofilepicurl(this);" id="joboffprofilepic" class="profilepic" name="joboffprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="joboffprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($j_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$j_pic}}">View</a>
                            <?php } ?>
                        </div> <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Passport</label>
                                    <input type="file" onchange="passprofilepicurl(this);" id="passprofilepic" class="profilepic" name="passprofilepic" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="passprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if ($pa_pic != '') { ?>
                                <a class="btnAction edit bgBlue documentfile" href="{{$pa_pic}}">View</a>
                            <?php } ?>
                        </div>

                    </div>
                </div>
                <br>
                <div class="custRow">
                    <div class="custCol-4">
                        <button class="commonBtn bgGreen addPrev back4 " type="button">Previous <span class="fa fa-arrow-right"></span></button> 

                    </div>
                    <div class="custCol-8">
                        <input type="submit" value="Update" class="commonBtn bgBlue addNext right open4">
                        <div class="customClear"></div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <div class="commonLoaderV1"></div>
    <div class="commonModalHolder">
        <div class="modalContent">
            <a href="javascript:void(0)" class="btnModalClose">Close(X)</a>
            <iframe id="frame" style="width:100%;height:100%;"></iframe>
        </div>
    </div>
</div>
<script>


    $('.documentfile').on('click', function () {
        var documentfile = $(this).attr('href');
        var arr = documentfile.split(".");      // Split the string using dot as separator
        var extension = arr.pop();       // Get last element
        var myArray = ["doc", "docx"];

        if ($.inArray(extension, myArray) == -1) {
            $('.commonModalHolder').show();
        }

        $('#frame').attr('src', documentfile)
        return false;

    })



    $('.btnModalClose').on('click', function () {
        $('#frame').attr("src", "");
        $('.commonModalHolder').hide()

    })
</script>
<script>
    $(function () {
        $('.nat').hide();
        $(".nationality_type").click(function () {
            var val = $(this).val();
            if (val == 1)
            {

                $('#nationality').val(<?php echo $employee_data->nationality; ?>);
                $('.nat').show();

            } else
            {
                $('#nationality option:contains("Saudi Arabia")').prop('selected', true);
                $('.nat').hide();

            }
        });
        var v1 = jQuery("#employee_updation").validate({
            rules: {
                employee_code: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    minlength: 2,
                    remote:
                            {
                                url: "../checkemployeecode",
                                type: "post",
                                data:
                                        {
                                            employee_code: function () {
                                                return $.trim($("#employee_code").val());
                                            }
                                        },
                                dataFilter: function (data)
                                {
                                    var json = JSON.parse(data);
                                    if (json.msg == "true") {
                                        //return "\"" + "That Company name is taken" + "\"";

                                        $('.employeeCode').addClass('ajaxLoaderV1');
                                        $('.employeeCode').removeClass('validV1');
                                        $('.employeeCode').addClass('errorV1');
                                        return false;
                                    } else
                                    {
                                        $('.employeeCode').addClass('ajaxLoaderV1');
                                        $('.employeeCode').removeClass('errorV1');
                                        $('.employeeCode').addClass('validV1');
                                        //valid="true";
                                        return true;
                                    }
                                }
                            }
                },
                password: {

                    minlength: {
                        depends: function () {
                            //  alert($('#password').val());
                            if ($('#password').val() == '') {
                                // $(this).val($.trim($(this).val()));
                                return "0";
                            } else {
                                return "6";
                            }
                        }
                    },
                },
                first_name: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    minlength: 2,
                    maxlength: 100,
                },
                passport_number: {

                    remote:
                            {
                                url: "../checkpassportno",
                                type: "post",
                                data:
                                        {
                                            passport_number: function () {
                                                return $.trim($("#passport_number").val());
                                            }
                                        },
                                dataFilter: function (data)
                                {
                                    var json = JSON.parse(data);
                                    if (json.msg == "true") {

                                        $('.passportno').addClass('ajaxLoaderV1');
                                        $('.passportno').removeClass('validV1');
                                        $('.passportno').addClass('errorV1');
                                        return false;
                                    } else
                                    {
                                        $('.passportno').addClass('ajaxLoaderV1');
                                        $('.passportno').removeClass('errorV1');
                                        $('.passportno').addClass('validV1');
                                        return true;
                                    }
                                }
                            },
                },
                alias_name: {
                    required: true,
                }, 
                basic_salary: {
                    required: true,
                    number:true,
                },
                housing_allowance: {
                    number:true,
                }, 
                transportation_allowance: {
                    number:true,
                },
                food_allowance: {
                    number:true,
                },
                other_expense: {
                    number:true,
                },
               
                residence_id_number: {

                    remote:
                            {
                                url: "../checkresidentsid",
                                type: "post",
                                data:
                                        {
                                            residence_id_number: function () {
                                                return $.trim($("#residence_id_number").val());
                                            }
                                        },
                                dataFilter: function (data)
                                {
                                    var json = JSON.parse(data);
                                    if (json.msg == "true") {

                                        $('.resid').addClass('ajaxLoaderV1');
                                        $('.resid').removeClass('validV1');
                                        $('.resid').addClass('errorV1');
                                        return false;
                                    } else
                                    {
                                        $('.resid').addClass('ajaxLoaderV1');
                                        $('.resid').removeClass('errorV1');
                                        $('.resid').addClass('validV1');
                                        return true;
                                    }
                                }
                            },
                },
                
                 gossi_number: {
                        number:true,
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    
                    remote:
                            {
                                url: "../checkgossino",
                                type: "post",
                                data:
                                        {
                                            gossi_number: function () {
                                                return $.trim($("#gossi_number").val());
                                            }
                                        },
                                dataFilter: function (data)
                                {
                                    var json = JSON.parse(data);
                                    if (json.msg == "true") {

                                        $('.gossino').addClass('ajaxLoaderV1');
                                        $('.gossino').removeClass('validV1');
                                        $('.gossino').addClass('errorV1');
                                        return false;
                                    } else
                                    {
                                        $('.gossino').addClass('ajaxLoaderV1');
                                        $('.gossino').removeClass('errorV1');
                                        $('.gossino').addClass('validV1');
                                        return true;
                                    }
                                }
                            },
                },
                /*last_name: {
                 required: true,
                 },*/

                email: {
                    require_from_group: [1, ".email-group"],
                    customemail: {
                        depends: function () {
                            $(this).val($.trim($(this).val()));
                            return true;
                        }
                    },
                    remote:
                            {
                                url: "../checkemail",
                                type: "post",
                                data:
                                        {
                                            email: function () {
                                                return $("#email").val();
                                            }
                                        },
                                dataFilter: function (data)
                                {
                                    var json = JSON.parse(data);
                                    if (json.msg == "true") {
                                        //return "\"" + "That Company name is taken" + "\"";

                                        $('.emailID').addClass('ajaxLoaderV1');
                                        $('.emailID').removeClass('validV1');
                                        $('.emailID').addClass('errorV1');
                                        // valid="false"
                                    } else
                                    {
                                        $('.emailID').addClass('ajaxLoaderV1');
                                        $('.emailID').removeClass('errorV1');
                                        $('.emailID').addClass('validV1');
                                        //valid="true";
                                        return true;
                                    }
                                }
                            }
                },
                contact_email: {
                    require_from_group: [1, ".email-group"],
                    customemail: {
                        depends: function () {
                            $(this).val($.trim($(this).val()));
                            return true;
                        }
                    },
                },
                dob: {
                    required: true,
                    //date: true
                },
                date_of_hiring: {
                    required: true,
                    //date: true
                },
                mobile_number: {
                    required: {
                        depends: function () {
                            $(this).val($.trim($(this).val()));
                            return true;
                        }
                    },
                    number: true
                },
            },
            messages: {
                employee_code: {
                    required: "Enter Code",
                    remote: "Empolyee Code Already Taken"
                },
                password: {
                    minlength: "Minimum 6 characters required",
                    remote: "Empolyee Code Already Taken"
                },
                first_name: "Enter First Name",
                middle_name: "Enter Middle Name",
                last_name: "Enter Last Name",
                passport_number: {required: "Enter Passport Number", remote: "Passport Number Alredy Exist"},
                father_name: "Enter Father Name",
                mother_name: "Enter Mother Name",
                alias_name: "Enter Alias Name",
                residence_id_number: {required: "Enter Residence ID Number", remote: "Residence ID Alredy Exist"},
                email: {required: "Enter Email ID", remote: "Email ID Alredy Exist", customemail: "Please enter a valid email"},
                contact_email: "Enter Vaild Contact Email ID",
                dob:
                        {required: "Enter DOB",
                            //date: "Accept Only Date"
                        }, 
                date_of_hiring:
                        {required: "Enter Date of Hiring",
                            //date: "Accept Only Date"
                        },
                mobile_number: {
                    required: "Enter Mobile Number",
                    number: "Accept number only",
                    remote: "Mobile Number Alredy Exist"
                }
            },

            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });
        $("#employee_updation").valid();
        $(".open1").click(function () {
            $('#container').animate({scrollTop: 28}, {queue: false, duration: 1000});
            if (v1.form()) {
                $(".formListV1Dtl").hide("fast");
                $("#employee_updation").submit();
                $("#step2").show();
            }

        });

        $(".open2").click(function () {
            var top_manager = $("#top_manager").val();
            if (top_manager == '') {
                alert("Select top manager");
            } else {
                if (v1.form()) {
                    $(".formListV1Dtl").hide("fast");
                    $("#employee_updation").submit();
                    $('#curPosition').val($('#job_position').val());
                    $("#step4").show();
                }
            }
        });

        $(".back2").click(function () {
            $(".formListV1Dtl").hide();
            $("#step1").show();
        });

        $(".back4").click(function () {
            $(".formListV1Dtl").hide();
            $("#step2").show();
        });

        $("#dob").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: new Date(1950, 10 - 1, 25),
            maxDate: 'Y',
            yearRange: '1950:c',
            changeMonth: true,
            changeYear: true
        });
            $("#date_of_hiring").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: new Date(1950, 10 - 1, 25),
            maxDate: 'Y',
            yearRange: '1950:c',
            changeMonth: true,
            changeYear: true
        });

        $('#passport_number').keyup(function (event) {
            if ($.trim($(this).val()) == '') {
                $(this).val($.trim($(this).val()));
            }
        });

        $('#residence_id_number').keyup(function (event) {
            if ($.trim($(this).val()) == '') {
                $(this).val($.trim($(this).val()));
            }
        });

        $('#job_position').change(function () {
            if ($('#job_position').val() != '') {

                if ($('#curPosition').val() != $('#job_position').val()) {
                    alert("Job position changed, all allocations will be deactivated")
                }
            }
        });

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#profile_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function indentpicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#indentpic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function jobprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#jobprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function conprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#conprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function stprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#stprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function perprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#perprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function qprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#qprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function cvprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cvprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function arabiccvurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#arabiccv_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function passprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#passprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function joboffprofilepicurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#joboffprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }


    ///////////////////////////////////
    $('#employee_updation').submit(function (e) {

        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        //Preventing the default behavior of the form 
        //Because of this line the form will do nothing i.e will not refresh or redirect the page 
        e.preventDefault();
        //Creating an ajax method
        $.ajax({
            //Getting the url of the uploadphp from action attr of form 
            //this means currently selected element which is our form 
            url: '../store',
            //For file upload we use post request
            type: "POST",
            //Creating data from form 
            data: new FormData(this),
            //Setting these to false because we are sending a multipart request
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                console.log(data);
                window.scrollTo(0, 0);
                //If the request is successfull we will get the scripts output in data variable 
                //Showing the result in our html element 
            },
            error: function () {
            }
        });


    });

    $('#docuform').submit(function (e) {    
        $.ajax({
            url: '../update_upload_documents',
            //For file upload we use post request
            type: "POST",
            //Creating data from form 
            data: new FormData(this),
            //Setting these to false because we are sending a multipart request
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (data) {
                $(".commonLoaderV1").hide();
                
            },
            error: function () {
            }
        });
    });



    $(document).ready(function () {

        var div_val = $("#div_id").val();
        if (div_val == "step4") {

            $("#step1").show();
            $("#step2").hide();
//     $("#step3").hide();
            $("#step4").hide();

        }

    });


    function CheckPasswordStrength(password) {
        // alert(password.length);
        var password_strength = document.getElementById("password_strength");

        //TextBox left blank.
        if (password.length == 0) {
            password_strength.innerHTML = "";
            return;
        }


        //Regular Expressions.
        var regex = new Array();
        regex.push("[A-Z]"); //Uppercase Alphabet.
        regex.push("[a-z]"); //Lowercase Alphabet.
        regex.push("[0-9]"); //Digit.
        regex.push("[$@$!%*#?&]"); //Special Character.

        var passed = 0;

        //Validate for each Regular Expression.
        for (var i = 0; i < regex.length; i++) {
            if (new RegExp(regex[i]).test(password)) {
                passed++;
            }
        }

        //Validate for length of Password.
        if (passed > 2 && password.length > 8) {
            passed++;
        }

        //Display status.
        var color = "";
        var strength = "";
        var fontsize = "15px";
        switch (passed) {
            case 0:
            case 1:
                strength = "Weak";
                color = "red";
                break;
            case 2:
                strength = "Good";
                color = "darkorange";
                break;
            case 3:
            case 4:
                strength = "Strong";
                color = "green";
                break;
            case 5:
                strength = "Very Strong";
                color = "darkgreen";
                break;
        }
        password_strength.innerHTML = strength;
        password_strength.style.color = color;
        password_strength.style.fontSize = "16px";

    }

    $('#job_description').ckeditor();

    $('#top_manager_id').keyup(function () {
        $(".add_manager").html('');
        $('#top_manager').val('');
        var term = $(this).val();
        //alert(term);
        jQuery.ajax({
            url: "../topmanager",
            dataType: 'json',
            type: 'POST',
            data: {term: term},
            success:
                    function (result) {
                        //alert(JSON.stringify(result));
                        var total = result.length;
                        if (total == 0) {
                            $('#top_manager_id').val('');
                        }
                        //if(count == 1 && )
                        var liname = '';
                        $.each(result, function (i, value) {

                            liname += '<li id=' + value['id'] + '>' + value['name'] + '</li>';
                           // console.log(value['name']);

                        });
                        $(".add_manager").append(liname);



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

                            if ($(this).parent().parent().parent().find('.suggestionDroplist').length > 0) {
//                            $(this).parent().parent().parent().find('input').val(selectval);
                                $('#top_manager_id').val(selectval);

                                $(this).parent().parent().parent().find('.commonError').hide();
                            } else {
//                            $(this).parent().parent().find('input').val(selectval);
                                $('#top_manager_id').val(selectval);

                                $(this).parent().parent().find('.commonError').hide();
                            }

                        });


                        $('.bgSelect li').click(function () {

                            var hiddenId = ($(this).attr('id'));
                            $('#top_manager').val(hiddenId);
                        });
                    },
        });
    });



   $('#val_professional').keyup(function () {

        $(".add_idprofessional").html('');
        $('#id_professional').val('');
        var term = $(this).val();
        //alert(term);
        jQuery.ajax({
            url: "../idprofessional",
            dataType: 'json',
            type: 'POST',
            data: {term: term},
            success:
                    function (result) {
                        //alert(JSON.stringify(result));
                        var total = result.length;
                        if (total == 0) {
                            $('#val_professional').val('');
                        }
                        //if(count == 1 && )
                        var liname = '';
                        $.each(result, function (i, value) {

                            liname += '<li id=' + value['id'] + '>' + value['name'] + '</li>';
                         //   console.log(value['name']);

                        });
                        $(".add_idprofessional").append(liname);



                        var $selectText = $('.bgSelection input');
                        var $selectLi = $('.bgSelection li');

                        var selectval;
                        var Drop = 0;
                 
                        $('body').on('click',function() {
                          
                            if (Drop == 1) {
                                $('.bgSelection ul').hide();
                                Drop = 0;
                            }
                        });
                        $selectText.click(function () {
                           
                            $('.bgSelection ul').hide();
                            Drop = 0;
                            if (Drop == 0) {
                              //  alert("Drop"+Drop);
                                $(this).parent().find('ul').slideDown();
                            }
//                            setTimeout(function () {
//                                Drop = 1;
//                            }, 50);


                        });

                        $selectLi.click(function () {
                            Drop = 1;
                            selectval = $(this).text();

                            if ($(this).parent().parent().parent().find('.suggestionDroplist').length > 0) {
//                            $(this).parent().parent().parent().find('input').val(selectval);
                                $('#val_professional').val(selectval);

                                $(this).parent().parent().parent().find('.commonError').hide();
                            } else {
//                            $(this).parent().parent().find('input').val(selectval);
                                $('#val_professional').val(selectval);

                                $(this).parent().parent().find('.commonError').hide();
                            }

                        });


                        $('.bgSelection li').click(function () {

                            var hiddenId = ($(this).attr('id'));
                            $('#id_professional').val(hiddenId);
                        });
                    },
        });
    });


</script>
@endsection