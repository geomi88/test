<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
	 <title>LuxEstate- @yield('title')</title>
	<link rel="icon" href="{{ URL::asset('admin/images')}}/favicon.png" type="image/png" />
	<!--[if lt IE 9]><script src="js/IEupdate.js" type="text/javascript"></script><![endif]-->
	<script src="{{ URL::asset('common')}}/js/jquery-2.2.1.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common/js/toastr.js') }}" type="text/javascript"></script>
	<link type="text/css" href="{{ URL::asset('admin')}}/css/bootstrap.min.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('common')}}/css/jquery-ui.min.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('common')}}/chosen/chosen.min.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('admin')}}/css/styles.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('common/css/toastr.css') }}" rel="stylesheet" media="all" />
        <script src="{{ URL::asset('common')}}/chosen/chosen.jquery.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common')}}/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common')}}/js/additional-methods.js" type="text/javascript"></script>
        <script src="{{ URL::asset('../vendor/unisharp/laravel-ckeditor/ckeditor.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('../vendor/unisharp/laravel-ckeditor/adapters/jquery.js')}}" type="text/javascript"></script>

</head>

 <body>
    <script>
    var basePath= "<?php echo url('/'); ?>";
    </script>
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
         <?php $adminUser=Session::get('adminUser'); ?>
       	<div class="addPropertyHolder">
		<div class="sidebarHolder">
			<div class="logoHolder">
				<a href="{{URL::to('admin/dashboard')}}">
					<figure>
						<img src="{{ URL::asset('admin')}}/images/whiteLogo.png">
					</figure>
				</a>
			</div>
			<ul class="list-unstyled text-capitalize sideMenu">
				<li >
					<a href="<?php echo url('/'); ?>/admin/dashboard"><img src="{{ URL::asset('admin')}}/images/iconDashboard.png"> Dashboard</a>
				</li>
				<li>
					<a href="<?php echo url('/'); ?>/admin/agent-listing"><img src="{{ URL::asset('admin')}}/images/iconAgent.png"> Agents / Owners</a>
				</li>
				<li>
					<a href="<?php echo url('/'); ?>/admin/architect-listing"><img src="{{ URL::asset('admin')}}/images/iconArch.png"> Architects</a>
				</li>
				<li>
					<a href="<?php echo url('/'); ?>/admin/property-listing"><img src="{{ URL::asset('admin')}}/images/iconProperty.png"> Properties</a>
				</li>
				<li>
					<a href="<?php echo url('/'); ?>/admin/neighbourhood-listing"><img src="{{ URL::asset('admin')}}/images/iconNeigh.png"> Neighborhoods</a>
				</li>
				<li>
					<a href="<?php echo url('/'); ?>/admin/estimates-listing"><img src="{{ URL::asset('admin')}}/images/iconEstimate.png"> Estimates</a>
				</li>
                                <li >
					<a href="<?php echo url('/'); ?>/admin/construction-listing"><img src="{{ URL::asset('admin')}}/images/iconEstimate.png"> Construction</a>
				</li>
				<li>
					<a href="<?php echo url('/'); ?>/admin/website-contents"><img src="{{ URL::asset('admin')}}/images/iconWeb.png"> Web Contents</a>
				</li>
				<li>
					<a href="javascript:void(0);"><img src="{{ URL::asset('admin')}}/images/iconSetting.png"> Settings</a>
				</li>
			</ul>
		</div>
		<div class="rightHolder">
			<header class="headerHolder">
				<div class="row">
					<div class="col-12">
						<ul class="list-unstyled list-inline text-capitalize rightNavHolder">
							<li class="list-inline-item userImgHolder relative">
								<figure class="dropdownWrapper">
									welcome <span class="userName ml-1 mr-2">{{$adminUser->name}}</span>
                                                                        <?php
                                                                            $image =url('/') . '/default_image/property_default.png';

                                                                            if ($adminUser->images == "" || $adminUser->images == NULL) {
                                                                                ?>

                                                                                <?php
                                                                            } else {
                                                                                $image = url('/') . '/uploads/agent/' . $adminUser->images;

                                                                                if (!@getimagesize($image)) {
                                                                                    $image = url('/') .'/default_image/property_default.png';
                                                                                }else{
                                                                                    $image =url('/') .'/uploads/agent/' . $adminUser->images;

                                                                                }
                                                                            }
                                                                        ?>
									<img class="dropClick" src="{{$image}}">
									<ul class="list-unstyled headerDropdownHolder dropdownOpen">
										<li>
											<a href="{{URL::to('admin/profile-edit',['id'=>\Crypt::encrypt($adminUser->id)])}}">edit profile</a>
										</li>
										<li>
											<a href="<?php echo url('/'); ?>/admin/logout">Logout</a>
										</li>
									</ul>
								</figure>
							</li>
						</ul>
					</div>
				</div>
			</header>
                     {!! Toastr::render() !!}
			@yield('content')
			<footer class="pageHolder">
				© 2018 LuxEstate. All Rights Reserved.
			</footer>
		</div>
	</div>
        <!--JavaScript -->
	<script src="{{URL::asset('admin')}}/js/chart.min.js" type="text/javascript"></script>
	<script src="{{URL::asset('admin')}}/js/chart_piecelabel.js" type="text/javascript"></script>
	<script src="{{URL::asset('admin')}}/js/custom_scripts.js" type="text/javascript"></script>
        <script src="{{URL::asset('common')}}/js/common_functions.js" type="text/javascript"></script>
        <script src="{{URL::asset('common')}}/js/jquery-ui.min.js" type="text/javascript"></script>

</body>
</html>