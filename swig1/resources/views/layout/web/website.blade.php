<!DOCTYPE html >
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-32249683-2"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-32249683-2');
        </script>

       <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>LuxEstate</title>
        <link rel="icon" href="{{ URL::asset('common/images')}}/favicon.png" type="image/png" />
        <!--[if lt IE 9]><script src="js/IEupdate.js" type="text/javascript"></script><![endif]-->
        <script src="{{ URL::asset('common')}}/js/jquery-2.2.1.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common')}}/js/slick.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/custom_scripts.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/range-slider.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/iscroll.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/web_page_script.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/imageviewer.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('web')}}/js/gallery.js" type="text/javascript"></script>
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
        <section class="layoutWrapper">
            <div class="mobileNav">
                <ul>
                    <li ><a href="javascript:tenureChange('1');">{{__('for_sale')}}</a></li>
                    <li ><a href="{{URL('/')}}/web/newconstruction">{{__('new_construction')}}</a></li>
                    <li ><a href="javascript:tenureChange('2');">{{__('for_rent')}}</a></li><li  text-transform:capitalize;><a href="{{URL('/')}}/web/architects">{{__('architects')}}</a></li>
                    <li  text-transform:capitalize;><a href="{{URL('/')}}/web/neighborhoods">{{__('neighborhoods')}}</a></li>
                    <li  text-transform:capitalize;><a href="{{URL('/')}}/web/stayinformed">{{__('stay_informed')}}</a></li>
                    <li  text-transform:capitalize;><a href="#interestedForm">{{__('estimate')}}</a></li>
                    <li  text-transform:capitalize;><a href="#interestedForm">{{__('contact')}}</a></li>
                </ul>
            </div>


            <section id="container" class="@yield('pageClass', 'Default Content') @yield('innerClass', 'needBanner')">
                <div class="pageLogo mobLogo">
                    <a href="<?php echo url('/'); ?>"> <figure><img src="{{ URL::asset('web/images')}}/logoLuxEstate.png" alt="Luxestate"></figure> </a>
                </div>
                <div class="iconMobNav">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <header class="pageHeader">
                    <div class="row">
                        <div class="headerLeft small-12 medium-6 large-6 column">
                            <div  class="headerLeftContent">
                                <a href="mailto:info@luxestate.eu">info@luxestate.eu</a>
                                <div class="dropdown dropList langSelectHolder">
                                    <input id="language" data-rel="{{Session::get('lang')}}" type="text" placeholder="{{Session::get('langName')}}" readonly="">
                                    <ul style="">
                                        <li onclick="changeLang('en','English');">English</li>
                                        <li onclick="changeLang('ka','Georgian');">Georgian</li>
                                        <li onclick="changeLang('ru','Russian');">Russian</li>
                                    </ul>
                                </div>
                                <nav>
                                    <ul>
                                        <li class="@yield('sale', '')" ><a href="javascript:tenureChange('1');">{{__('for_sale')}}</a></li>
                                        <li class="@yield('construction', '')" ><a href="{{URL('/')}}/web/newconstruction">{{__('new_construction')}}</a></li>
                                        <li class="@yield('rent', '')" ><a href="javascript:tenureChange('2');">{{__('for_rent')}}</a></li>
                                        <li class="@yield('architects', '')"><a href="{{URL('/')}}/web/architects">{{__('architects')}}</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="pageLogo">
                            <a href="<?php echo url('/'); ?>"><figure><img src="{{ URL::asset('web/images')}}/logoLuxEstate.png" alt="Luxestate"></figure></a>
                        </div>
                        <div class="headerRight small-12 medium-6 large-6 column">
                            <div class="headerRightContent">
                                <a href="tel:255">+525 8558 555</a>
                                <ul class="socialLinks">
                                    <li><figure><img src="{{ URL::asset('web/images')}}/iconFB.png" alt="Facebook"></figure></li>
                                    <li><figure><img src="{{ URL::asset('web/images')}}/iconTwitter.png" alt="Twiter"></figure></li>
                                    <li><figure><img src="{{ URL::asset('web/images')}}/iconIn.png" alt="Linkedin"></figure></li>
                                </ul>


                                <nav>
                                    <ul>
                                        <li class="@yield('neighborhood', '')"  ><a href="{{URL('/')}}/web/neighborhoods">{{__('neighborhoods')}}</a></li>
                                        <li class="@yield('stayinformed', '')" ><a href="{{URL('/')}}/web/stayinformed">{{__('stay_informed')}}</a></li>
                                        <li class="@yield('estimate', '')"><a href="{{URL('/')}}/web/estimate">{{__('estimate')}}</a></li>
                                        <li class="@yield('contact', '')"><a href="{{URL('/')}}#interestedForm">{{__('contact')}}</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </header>
                @yield('content')
                <footer class="pageFooter">
                    <div class="row">
                        <div class="small-6 medium-6 large-6 column footerLeft">
                            <figure><img src="{{ URL::asset('web/images')}}/logoLuxEstate.png" alt=""></figure>
                        </div>
                        <div class="small-6 medium-6 large-6 column footerRight">
                            &copy; 2018 luxestate Development. All rights reserved.
                        </div>
                    </div>
                </footer>
            </section>
        </section>
    </body>
    <script>
        function changeLang(lang,langName){

            $.ajax({
            type:'POST',
            url: '<?php echo url('/'); ?>/web/changeLang',
            data: {lang:lang,langName:langName},
            success:function (){
             location.reload();
                },
            });
        }

        function tenureChange(tenuretype){

        var form = document.createElement("form");
        form.method = "GET";
        form.action = "<?php echo url('/'); ?>/properties/search";

        var element1 = document.createElement("input");
        element1.value=tenuretype;
        element1.name="for_tenure";
        form.appendChild(element1);

        var element2 = document.createElement("input");
        element2.value="";
        element2.name="building_type";
        form.appendChild(element2);

        var element3 = document.createElement("input");
        element3.value="";
        element3.name="price";
        form.appendChild(element3);

        var element4 = document.createElement("input");
        element4.value="";
        element4.name="places_list";
        form.appendChild(element4);
        document.body.appendChild(form);
        form.submit();


        }

        $.ajaxSetup({
                headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
            });
    </script>
</html>
