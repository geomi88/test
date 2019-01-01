<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>LuxEstate- @yield('title')</title>
        <link rel="icon" href="{{ URL::asset('admin/images')}}/favicon.png" type="image/png" />
        <!--[if lt IE 9]><script src="{{ URL::asset('admin')}}/js/IEupdate.js" type="text/javascript"></script><![endif]-->
        <script src="{{ URL::asset('common')}}/js/jquery-2.2.1.min.js" type="text/javascript"></script>
        <link type="text/css" href="{{ URL::asset('admin')}}/css/bootstrap.min.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('admin')}}/css/styles.css" rel="stylesheet" media="all" />
    </head>
    <body>
        <script>
          $.ajaxSetup({
                headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
            });
          </script>
        <div class="container-fluid pageHolder">
            <header class="headerHolder">
                <div class="row">
                    <div class="col-2">
                        <a href="index.html">
                            <figure>
                                <img src="{{ URL::asset('admin/images')}}/logo.png">
                            </figure>
                        </a>
                    </div>
                    <div class="col-4">
                        <ul class="list-unstyled list-inline navHolder text-uppercase">
                            <li class="list-inline-item active"><a href="index.html">dashboard</a></li>
                            <li class="list-inline-item"><a href="properties.html">properties</a></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul class="list-unstyled list-inline text-capitalize rightNavHolder">
                            <li class="list-inline-item addNewProperty">
                                <a href="javascript:void(0);">
                                    <figure>
                                        <img class="mr-1" src="{{ URL::asset('admin/images')}}/iconPlus.png"> add new property
                                    </figure>
                                </a>
                            </li>
                            <li class="list-inline-item userImgHolder relative">
                                <figure class="dropdownWrapper">
                                    welcome <span class="userName ml-1 mr-2">jake</span>
                                    <img class="dropClick" src="{{ URL::asset('admin/images')}}/iconUser.jpg">
                                    <ul class="list-unstyled headerDropdownHolder dropdownOpen">
                                        <li>
                                            <a href="javascript:void(0);">profile</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">Logout</a>
                                        </li>
                                    </ul>
                                </figure>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            @yield('content')
            <footer>
                © 2018 Partniori. All Rights Reserved.
            </footer>
        </div>
        <!--JavaScript -->
        <script src="{{ URL::asset('admin')}}/js/chart.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('admin')}}/js/chart_piecelabel.js" type="text/javascript"></script>
        <script src="{{ URL::asset('admin')}}/js/custom_scripts.js" type="text/javascript"></script>
    </body>
</html>
