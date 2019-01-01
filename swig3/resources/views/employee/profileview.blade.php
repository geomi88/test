@extends('layouts.main')
@section('content')
            <div class="customClear"></div>
            <div class="contentArea">

                <div class="innerContent">
                    
                    <div class="allListHolder">
                        <div class="profileWidget">
                            <figure class="proPic" style="background-image: url({{$profile_details->profilepic}}" alt="Profile">
                                <figcaption>{{$profile_details->username}}</figcaption>
                            </figure>
                            <aside>
                                <h3>{{$profile_details->first_name.' '.$profile_details->middle_name.' '.$profile_details->last_name}}<span>{{str_replace("_"," ",$profile_details->job_position_name)}}</span></h3>
                                <ul class="quickInfo">
                                    <li class="phone"><a>{{$profile_details->mobile_number}}</a></li>
                                    <li class="email"><a>{{$profile_details->email}}</a></li>
                                </ul>
                                <div class="country">
                                    <?php $flagUrl="../images/flags/".$profile_details->flag_pic;
                                    ?>
                                    <figure style="background-image: url('<?php echo $flagUrl?>')" alt="Flag"></figure>
                                    <span>{{$profile_details->country_name}}</span>
                                </div>
                            </aside>
                        </div>
                        <div class="profileDtlView">
                            <div class="toggleOptn">
                                <h5><em></em>Professional Details</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                        <li><b>Top Manager</b> : {{$profile_details->topmanager_first_name.' '.$profile_details->topmanager_middle_name.' '.$profile_details->topmanager_last_name}}</li>
                                        <li><b>Division</b> : {{$profile_details->division_name}}</li>
                                      
                                    </ul>
                                </div>
                            </div>

                            <div class="toggleOptn">
                                <h5><em></em>Personal Details</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                          <li><b>DOB</b> : {{$profile_details->dob}}</li>
                                          <li><b>Passport No</b> : {{$profile_details->passport_number}}</li>
                                       <li><b>Residence Id</b>: {{$profile_details->residence_id_number}}</li>
                                        <li><b>Address</b> : {{$profile_details->current_address}}</li>
                                         
                                    </ul>
                                </div>
                            </div>
                               <div class="toggleOptn">
                                <h5><em></em>Job Description Agreement</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                          <li> <textarea id="job_description" readonly style="width:500px;height:500px;">{!! $profile_details->job_description !!}</textarea></li>
                                        
                                    </ul>
                                </div>
                            </div>        
                             <div class="toggleOptn">

                                <h5><em></em>Update Password</h5>
                                <div class="toggleOptnDtl">
                                     <ul>
                                        <li>
                                            <b>Password</b> :
                                            <form class="pswdReset" action="{{url('employee/updatepassword')}}" method="post" id="employee_details" enctype="multipart/form-data">
                                                <label>New Password</label>
                                                <input type="password" name="password" id="password" placeholder="Enter Your Password" onkeyup="CheckPasswordStrength(this.value)">
                                                <span id="password_strength"></span>
                                                <label>Confirm Password</label>
                                                <input type="password" id ="confirm_password" placeholder="Confirm Password">
                                            </form>
                                        </li>
                                    </ul>
                                    <a class="commonBtn addBtn bgGreen" onclick="submit()">Update</a>
                                
                                </div>
                                
                            </div>
                           
                        </div>
                    </div>
                </div>

            </div>
       
       
       
<script type="text/javascript">
    function CheckPasswordStrength(password) {
        //alert(password);
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
    }
    
    
    
     $(function () {
    $(".commonBtn").on('click',function () {
        
          var password=$("#password").val();
          var confirm_password=$("#confirm_password").val();
            if((password===confirm_password) && password!="") {
              
                $("#employee_details").submit();
               }
        });
    });
    function  submit(){
       var password=$("#password").val();
          var confirm_password=$("#confirm_password").val();
            if((password===confirm_password) && password!="") {
              
                $("#employee_details").submit();
               }
    }
    
    $('#job_description').ckeditor();
    
</script>
@endsection