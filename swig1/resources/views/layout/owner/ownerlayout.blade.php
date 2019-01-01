<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Luxestate</title>
	<link rel="icon" href="{{ URL::asset('owner/images')}}/favicon.png" type="image/png" />
	<!--[if lt IE 9]><script src="js/IEupdate.js" type="text/javascript"></script><![endif]-->
	<script src="{{ URL::asset('common')}}/js/jquery-2.2.1.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common/js/toastr.js') }}" type="text/javascript"></script>
	<link type="text/css" href="{{ URL::asset('owner')}}/css/bootstrap.min.css" rel="stylesheet" media="all" />
	<link type="text/css" href="{{ URL::asset('common')}}/css/jquery-ui.min.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('common')}}/chosen/chosen.min.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('owner')}}/css/styles.css" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('common/css/toastr.css') }}" rel="stylesheet" media="all" />
        <script src="{{ URL::asset('common')}}/chosen/chosen.jquery.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common')}}/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="{{ URL::asset('common')}}/js/additional-methods.js" type="text/javascript"></script>
        <script src="{{ URL::asset('../vendor/unisharp/laravel-ckeditor/ckeditor.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('../vendor/unisharp/laravel-ckeditor/adapters/jquery.js')}}" type="text/javascript"></script>




</head>
<body>
     <script>
    var basePath= "<?php echo url('/');?>";
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
        <?php $ownerUser=Session::get('ownerUser'); ?>
	<div class="container-fluid pageHolder">
		<header class="headerHolder">
			<div class="row">
				<div class="col-2">
					<a href="index.html">
						<figure>
							<img src="{{ URL::asset('admin')}}/images/logo.png">
						</figure>
					</a>
				</div>
				<div class="col-4">
					<ul class="list-unstyled list-inline navHolder text-uppercase">
						<li class="@yield('property', '') list-inline-item"><a href="<?php echo url('/'); ?>/owner/property-listing">properties</a></li>
					</ul>
				</div>
				<div class="col-6">
					<ul class="list-unstyled list-inline text-capitalize rightNavHolder">
						<li class="list-inline-item addNewProperty">
							<a href="<?php echo url('/'); ?>/owner/add-property">
								<figure>
									<img class="mr-1" src="{{URL::asset('admin/images')}}/iconPlus.png"> add new property
								</figure>
							</a>
						</li>
						<li class="list-inline-item userImgHolder relative">
							<figure class="dropdownWrapper">
								welcome <span class="userName ml-1 mr-2">{{$ownerUser->name}}</span>
                                                                 <?php
                                                                    $image =url('/') . '/default_image/property_default.png';

                                                                    if ($ownerUser->image == "" || $ownerUser->image == NULL) {
                                                                        ?>

                                                                        <?php
                                                                    } else {
                                                                        $image = url('/') . '/uploads/owner/' . $ownerUser->image;

                                                                        if (!@getimagesize($image)) {
                                                                            $image = url('/') .'/default_image/property_default.png';
                                                                        }else{
                                                                            $image =url('/') .'/uploads/owner/' . $ownerUser->image;

                                                                        }
                                                                    }
                                                                ?>
								<img class="dropClick" src="{{$image}}">
								<ul class="list-unstyled headerDropdownHolder dropdownOpen">
									<li>
                                        <a href="{{URL::to('owner/profile-edit',['id'=>\Crypt::encrypt($ownerUser->id)])}}">edit profile</a>
                                    </li>
									<li>
										<a href="<?php echo url('/'); ?>/owner/owner-logout">Logout</a>
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
            <footer>
			© 2018 Partniori. All Rights Reserved.
		</footer>
	</div>
	<!--JavaScript -->
	<script src="{{URL::asset('admin')}}/js/chart.min.js" type="text/javascript"></script>
	<script src="{{URL::asset('admin')}}/js/chart_piecelabel.js" type="text/javascript"></script>
	<script src="{{URL::asset('admin')}}/js/custom_scripts.js" type="text/javascript"></script>
    <script src="{{URL::asset('common')}}/js/jquery-ui.min.js" type="text/javascript"></script>

</body>
</html>