<?php $__env->startSection('content'); ?>

        <form method="post" action="<?php echo e(action('Login\AuthController@checklogin')); ?>" id="loginForm">
            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
            <div class="loginContent">
                <img src="<?php echo e(URL::asset('images/loginLogo.png')); ?>" class="loginlogo" alt="logo">
                <h4>Admin Login</h4>
                <div class="inputHolder">
                    <label for="loginId">Username</label>
                    <input type="text" id="username" name="username">
                    <span class="usernameError error"></span>
                </div>
                <div class="inputHolder">
                    <label for="loginPassword">Password</label>
                    <input type="password" name="password" id="password">
                    <span class="passwordError error"></span>
                </div>
                <input type="submit" class="btnSubmit" id="admin_login" value="Login">
                <div class="customClear"></div>
            </div>
        </form>
<script>
    $(document).ready(function ()
    {
        $('#admin_login').on("click", function () {
            var username = $('#username').val();
            username = $.trim(username);
            var password = $('#password').val();
            password = $.trim(password);
            var errors = 0;
            if (username == '') {
                $('.usernameError').html('Please enter Username');
                errors = 1;
            } else {
                $('.usernameError').html('');
            }
            if (password == '') {
                $('.passwordError').html('Please enter Password');
                errors = 1;
            } else {
                $('.passwordError').html('');
            }
            if (errors == 1)
            {
                return false;
            }

        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>