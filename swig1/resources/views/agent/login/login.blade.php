@extends('layout.admin.login')
@section('content')
	<div class="container-fluid">
		<div class="loginPage">
			<div class="loginWrapper clearfix">
				<div class="halfWidth text-center logoWrap">
					<figure>
						<img class="img-fluid" src="images/whiteLogo.png">
					</figure>
					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been try's standard</p>
				</div>
				<div class="halfWidth">
					<div class="loginFormHolder">
                                             <form action="doAgentLogin" method="post" id="formAdminLogin">
                                    {{ csrf_field() }}
						<input class="inputStyle" type="text" name="username" id="username" placeholder="Enter Username">
						<input class="inputStyle" type="password" name="password" id="password" placeholder="Enter Password">
						<button class="btnStyle mt-3 text-uppercase" id="btnlogin" type="submit">login</button>
                                             </form>
                                                <div class="forgetHolder">
							<a class="forgetPassword" href="javascript:void(0);">forgot password?</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- <footer>
			© 2018 Partniori. All Rights Reserved.
		</footer> -->
	</div>


<script>
    $(document).ready(function ()
    {
        $("#formAdminLogin").validate({
            errorElement: "label",
            errorClass: "error",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('valErrorV1');
            },
            rules: {
                username:
                        {
                            required: true,
                        },
                password:
                        {
                            required: true,
                        }

            },
            submitHandler: function () {
                form.submit();
            },
            email: {
                username: "Enter Email",
                password: "Enter Password",
            }
        });
    });
</script>
@endsection	