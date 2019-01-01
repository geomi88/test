<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="{{ csrf_token() }}"/>
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Project Details | Luxestate</title>
        <link rel="icon" href="{{ URL::asset('common/images')}}/favicon.png" type="image/png" />
        <!--[if lt IE 9]><script src="js/IEupdate.js" type="text/javascript"></script><![endif]-->
        <script src="{{ URL::asset('common')}}/js/jquery-2.2.1.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/custom_scripts.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/range-slider.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/iscroll.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/imageviewer.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/gallery.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common')}}/js/slick.min.js" type="text/javascript"></script>
        <link type="text/css" href="{{ URL::asset('common')}}/css/foundation.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('common')}}/css/slick.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('web')}}/css/slideshow.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('web')}}/css/imageviewer.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('web')}}/css/styles.css" rel="stylesheet" media="all" />


    </head>

    <body id="body">
         <script>
          $.ajaxSetup({
                headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
            });
          </script>
          @yield('content')
          <footer class="NewpageFooter">
               <div class="footerContainer">
                    <div class="row">
                        <div class="small-12 medium-6 large-6 column footerLeft">
                            <span>Jordaenskaai 21, 2000 Antwerp    T : 5653265656   E : info@luxestate .be</span>
                        </div>
                        <div class="small-12 medium-6 large-6 column footerRight">
                            &copy; 2018 luxestate Development. All rights reserved. 
                        </div>
                    </div>
                </div> 
            </footer> 
    </body>
</html>
