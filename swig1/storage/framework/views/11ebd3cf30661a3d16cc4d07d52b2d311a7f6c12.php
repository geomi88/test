<?php $__env->startSection('content'); ?>
<div class="contentHolderV1">
    <h1>Add Company</h1>

        <form method="post" action="<?php echo e(action('Company\CompanyController@store')); ?>" id="addcompanyform" enctype="multipart/form-data" class="formTypeV1">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" id="base_path" value="<?php echo url('/');?>">
            <b>Personal Information</b>
            <div class="formSection">
                <label>First Name</label>
                <input type="text" name="firstName" id="firstName">
                <div class="customClear"></div>
                <div class="firstNameError error"></div>
                <label>Last Name</label>
                <input type="text" name="lastName" id="lastName">
                <div class="customClear"></div>
                <div class="lastNameError error"></div>
                <label>Email Address</label>
                <input type="text" name="email" id="email">
                <div class="customClear"></div>
                <div class="emailError error"></div>
                <label>Phone Number </label>
                <input type="text" name="phoneNumber" id="phoneNumber" value="+974">
                <div class="customClear"></div>
                <div class="phoneNumberError error"></div>
                <label>Password</label>
                <input type="password" name="password" id="password">
                <div class="customClear"></div>
                <div class="passwordError error"></div>
                <label>Repeat Password</label>
                <input type="password" name="repeat_password" id="repeat_password">
                <div class="customClear"></div>
                <div class="repeat_passwordError error"></div>
            </div>
            <div class="customClear"></div>
            <b>Company Information</b>
            <div class="formSection">
                <label>Company Name</label>
                <input type="text" name="companyName" id="companyName">
                <div class="customClear"></div>
                <div class="companyNameError error"></div>
                <label>Company Description</label>
                <textarea name="companyDescription" id="companyDescription"></textarea>
                <div class="customClear"></div>
                <div class="companyDescriptionError error"></div>
                
                <label>Company Logo</label>
                <input type="file" name="companyLogo" id="companyLogo">
                <div class="customClear"></div>
                <div class="companyLogoError error"></div>
            </div>
            <input class="commonButton" id="saveCompany" type="button" value="SAVE">  
        </form>
    

</div>  

<div class="customClear"></div>
</div>


<script>
    $(document).ready(function () {

        $("#saveCompany").click(function (event) {
            event.preventDefault();
            var base_path = $('#base_path').val();
            var firstName = $('#firstName').val();
            firstName = firstName.trim();
            var lastName = $('#lastName').val();
            lastName = lastName.trim();
            var email = $('#email').val();
            email = email.trim();
            var phoneNumber = $('#phoneNumber').val();
            phoneNumber = phoneNumber.trim();
            var password = $('#password').val();
            password = password.trim();
            var repeat_password = $('#repeat_password').val();
            repeat_password = repeat_password.trim();
            var companyName = $('#companyName').val();
            companyName = companyName.trim();
            var companyDescription = $('#companyDescription').val();
            companyDescription = companyDescription.trim();
            var errors = 0;
            if (firstName == '') {
                $('.firstNameError').html('Please enter First Name');
                errors = 1;
            } else {
                $('.firstNameError').html('');
            }

            if (lastName == '') {
                $('.lastNameError').html('Please enter Last Name');
                errors = 1;
            } else {
                $('.lastNameError').html('');
            }

            if (email == '') {
                $('.emailError').html('Please enter Email Address');
                errors = 1;
            } else {
                var testresults;
                var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
                if (filter.test(email)) {
                    testresults = true;
                } else {
                    testresults = false;
                }
                if (testresults == false) {
                    $('.emailError').html('Enter Valid Email ID');
                    errors = 1;
                } else {
                    $('.emailError').html('');
                    $.ajax({
                        type: 'get',
                        url: base_path+'/company/checkemail',
                        data: 'email=' + email,
                        async: false,
                        cache: false,
                        timeout: 30000,
                        success: function (response) {
                            if (response == 1) {
                                $('.emailError').html('Email already exist.');
                                errors = 1;
                            } else {
                                $('.emailError').html('');
                            }
                        }
                    });
                }
            }

            if (phoneNumber == '') {
                $('.phoneNumberError').html('Please enter Phone Number');
                errors = 1;
            } else {
                var phonetestresults;
                var phonefilter = /\(?([0-9])\)?/
                if (phonefilter.test(phoneNumber)) {
                    phonetestresults = true;
                } else {
                    phonetestresults = false;
                }
                if(phonetestresults == false)
                {
                    $('.phoneNumberError').html('Please enter digits only');
                    errors = 1;
                }
                else {
                $('.phoneNumberError').html('');
                $.ajax({
                        type: 'get',
                        url: base_path+'/company/checkphone',
                        data: 'phoneNumber=' + phoneNumber,
                        async: false,
                        cache: false,
                        timeout: 30000,
                        success: function (response) {
                            if (response == 1) {
                                $('.phoneNumberError').html('Phone Number already exist.');
                                errors = 1;
                            } else {
                                $('.phoneNumberError').html('');
                            }
                        }
                    });
                }
            }

            if (password == '') {
                $('.passwordError').html('Please enter Password');
                errors = 1;
            } else {
                $('.passwordError').html('');
            }
            if (repeat_password == '') {
                $('.repeat_passwordError').html('Please enter Password');
                errors = 1;
            } else {
                $('.repeat_passwordError').html('');
            }

            if(password != repeat_password)
            {
                $('.repeat_passwordError').html('Passwords Mismatch');
            }
            
            if (companyName == '') {
                $('.companyNameError').html('Please enter Company Name');
                errors = 1;
            } else {
                $('.companyNameError').html('');
                $.ajax({
                        type: 'get',
                        url: base_path+'/company/checkname',
                        data: 'companyName=' + companyName,
                        async: false,
                        cache: false,
                        timeout: 30000,
                        success: function (response) {
                            if (response == 1) {
                                $('.companyNameError').html('Company Name already exist.');
                                errors = 1;
                            } else {
                                $('.companyNameError').html('');
                            }
                        }
                    });
            }
            if (companyDescription == '') {
                $('.companyDescriptionError').html('Please enter Company Description');
                errors = 1;
            } else {
                $('.companyDescriptionError').html('');
            }
             if ($('#companyLogo').get(0).files.length === 0) {
                $('.companyLogoError').html('No files selected.');
                errors = 1;
            } else {
                $('.companyLogoError').html('');
            }
            if (errors == 1)
            {
                return false;
            }
            
            else
            {
                $( "#addcompanyform" ).submit();
            }

        });

       
    });


</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>