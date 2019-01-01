
@extends('layouts.login')
@section('content')
<section id="container">
    <section class="contentSection login">
        <div class="loginWrapper">
            <div class="logoWrapper">
                <img src="{{ URL::asset('images/imtiyazat.png') }}">
            </div>
            <span>Login</span>
            <form action="{{ action('Login\AuthController@login') }}" method="post" id="formRegistration">
                <label>User Name</label>
                <div class="inputHolder">
                    <input type="text" name="username" id="username" placeholder="User Name">
                    <div class="commonError"></div>
                </div>
                <label>Password</label>
                <div class="inputHolder">
                    <input type="password" name="password" id="password"  placeholder="Password">
                    <div class="commonError"></div>
                </div>
                <div class="inputHolder">
                    <select class="commoSelect" name="company" id="company">
                        <!--<option value=''>Choose Company</option>-->
                        @foreach ($companies as $company)
                        <option value='{{ $company->id }}'>{{ $company->name}}</option>
                        @endforeach
                    </select>
                    <div class="commonError"></div>
                </div>
               <input type="submit" id="btnLogin" value="Submit" name="submit">
                <div class="customClear"></div>
            </form>
        </div>

    </section>
</section>
<script>
    $(document).ready(function ()
    {
        $("#formRegistration").validate({
            errorElement: "div",
            errorClass: "commonError",
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
            messages: {
                username: "Enter Username",
                password: "Enter Password",
            }
        });
    });
</script>
@endsection