<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Safqa - Login</title>
        <link rel="icon" href="favicon.png" type="image/png" />
        <script src="<?php echo e(URL::asset('js/jquery-2.1.1.min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(URL::asset('js/custom_scripts.js')); ?>" type="text/javascript"></script>
        <link type="text/css" href="<?php echo e(URL::asset('css/styles.css')); ?>" rel="stylesheet" media="all" />
        <script src="<?php echo e(URL::asset('js/jquery-min.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(URL::asset('js/toastr.js')); ?>" type="text/javascript"></script>
        <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
        
        
    </head>

    <body id="body">
        
        <section id="container" class="loginHolder">
            <?php echo Toastr::render(); ?> 
            <?php echo $__env->yieldContent('content'); ?>
        </section>
    </body>
    
        
        
        
</html>