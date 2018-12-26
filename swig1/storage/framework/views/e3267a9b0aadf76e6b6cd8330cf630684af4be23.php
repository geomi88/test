<?php $__env->startSection('content'); ?>
<div class="contentHolderV1">
    <h1>Edit User</h1>

        <form method="post" action="<?php echo e(action('Users\UsersController@update')); ?>" id="edituserform" class="formTypeV1">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" id="base_path" value="<?php echo url('/');?>">
                <label>First Name</label>
                <input type="text" name="firstName" id="firstName" value="<?php echo e($user_details->firstName); ?>">
                <div class="customClear"></div>
                <div class="firstNameError error"></div>
                <label>Last Name</label>
                <input type="text" name="lastName" id="lastName" value="<?php echo e($user_details->lastName); ?>">
                <div class="customClear"></div>
                <div class="lastNameError error"></div>
                <label>Email Address</label>
                <input type="text" name="email" id="email" value="<?php echo e($user_details->email); ?>">
                <div class="customClear"></div>
                <div class="emailError error"></div>
                <label>Phone Number</label>
                <input type="text" name="phoneNumber" id="phoneNumber" value="<?php echo e($user_details->phoneNumber); ?>">
                <div class="customClear"></div>
                <div class="phoneNumberError error"></div>
                <input type="hidden" name="user_id" id="user_id" value="<?php echo e($user_details->id); ?>">
                <input class="commonButton" id="saveUser" type="button" value="SAVE">  
        </form>
    

</div>  

<div class="customClear"></div>
</div>


<script>
    $(document).ready(function () {

        $("#saveUser").click(function (event) {
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
            var user_id = $('#user_id').val();
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
                        url: base_path+'/users/checkemail',
                        data: 'email=' + email +'&user_id='+ user_id,
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
                        url: base_path+'/users/checkphone',
                        data: 'phoneNumber=' + phoneNumber +'&user_id='+ user_id,
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


            if (errors == 1)
            {
                return false;
            }
            
            else
            {
                $( "#edituserform" ).submit();
            }

        });


    });


</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>