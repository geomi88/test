<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>Login | Moroccan Taste</title>
        <link rel="icon" href="{{ URL::asset('favicon.png') }}" type="image/png" />
        <!--[if lt IE 9]><script src="js/IEupdate.js" type="text/javascript"></script><![endif]-->
        <script src="{{ URL::asset('js/jquery-2.1.1.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.sortable.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/custom_scripts.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/dev_scripts.js') }}" type="text/javascript"></script> 
        <script src="{{ URL::asset('js/functions.js') }}" type="text/javascript"></script>              
        <link type="text/css" href="{{ URL::asset('css/styles.css') }}" rel="stylesheet" media="all" />
        <script src="{{ URL::asset('js/jquery-min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/toastr.js') }}" type="text/javascript"></script>
        <link type="text/css" href="{{ URL::asset('css/toastr.css') }}" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('css/datepicker.css') }}" rel="stylesheet" media="all" />
         <script src="{{ URL::asset('js/jquery.validate.min.js') }}" type="text/javascript"></script> 
         <script src="{{ URL::asset('js/jquery.datepicker.js') }}" type="text/javascript"></script> 
   
         
    </head>

    </head>
    <body id="body">
        <section id="container">
             {!! Toastr::render() !!}
            @yield('content')
        </section>
    </body>
</html>