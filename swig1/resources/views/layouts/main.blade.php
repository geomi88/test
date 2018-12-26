<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Safqa</title>


        <link type="text/css" href="{{ URL::asset('css/styles.css') }}" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('css/toastr.css') }}" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('css/jquery.datetimepicker.css') }}" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('css/foundation.css') }}" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('css/jquery-customselect.css') }}" rel="stylesheet" media="all" />

        <script src="{{ URL::asset('js/jquery-2.1.1.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/custom_scripts.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/highcharts.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/toastr.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.datetimepicker.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery-customselect.js') }}" type="text/javascript"></script>

        <script src="{{ URL::asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/additional-methods.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.cookie.js') }}" type="text/javascript"></script>


    </body>
</html>

</head>

<body id="body" class="minMenu8">
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



    <section id="container">
        <section class="navHolder">
            <div class="headerTop">
                <a href="javascript:void(0)">
                    <img src="{{ URL::asset('images/logo.png')}}" alt="Safqa">
                </a>
            </div>
            <nav>
                <ul>
                    <li class="iconDash"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="iconUser"><a href="{{ url('users') }}">User Management</a></li>
                    <li class="iconCompany"><a href="{{ url('company') }}">Company Management</a></li>
                    <li class="iconAd"><a href="{{ url('ads') }}">AD Management</a></li>
                    <li class="iconCatList"><a href="{{ url('category') }}">Category Listing</a>
                        <ul>
                            <li><a href="{{ url('category/add') }}">Add New Category</a></li>

                        </ul>
                    </li>
                    <li class="iconCatEdit"><a href="{{url('orders')}}">Order List</a></li>
                    <li class="iconProList"><a href="{{url('products')}}">Product List</a></li>
                    <li class="iconAd"><a href="{{url('users/notification')}}">Public Notification</a></li>
                </ul>
            </nav>
        </section>
        <aside class="rightContent">
            <header class="topHeader">
                <a href="javascript:void(0)" class="mainNavCtrl" title="Menu"></a>
                <div class="menuType1">
                    <div class="profileInfo">
                        <figure style="background-image: url('images/profilePic.png');">

                        </figure>
                        <span>Welcome Admin</span>
                        <ul>
                            <li><a href="javascript:void(0)">Edit Profile</a></li>
                            <li><a href="{{ url('/logout') }}">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="innerContent">
            {!! Toastr::render() !!}
            @yield('content')
            </div>
        </aside>

    </section>


</script>
</body>
</html>

