<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Login</title>
	<link rel="icon" href="{{ URL::asset('admin/images')}}/favicon.png" type="image/png" />
	<!--[if lt IE 9]><script src="js/IEupdate.js" type="text/javascript"></script><![endif]-->
	<script src="{{ URL::asset('common')}}/js/jquery-2.2.1.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common/js/toastr.js') }}" type="text/javascript"></script>
	<link type="text/css" href="{{ URL::asset('admin')}}/css/bootstrap.min.css" rel="stylesheet" media="all" />
	<link type="text/css" href="{{ URL::asset('admin')}}/css/styles.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('common/css/toastr.css') }}" rel="stylesheet" media="all" />
        <script src="{{ URL::asset('common')}}/js/jquery.validate.min.js" type="text/javascript"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    
    <body>
           <script>
          $.ajaxSetup({
                headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
            });
          </script>
          <script>
        toastr.options = {   
            "closeButton": true,   
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000", 
            "showEasing": "swing", 
            "hideEasing": "linear", 
            "showMethod": "fadeIn", 
            "hideMethod": "fadeOut"
        };
        </script>
         {!! Toastr::render() !!}
	 @yield('content')
	<!--JavaScript -->
	<script src="js/chart.min.js" type="text/javascript"></script>
	<script src="js/chart_piecelabel.js" type="text/javascript"></script>
	<script src="js/custom_scripts.js" type="text/javascript"></script>
</body>
</html>
