<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register - Rosella</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body class="h-100">

    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>

    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">
                                <a class="text-center" href="#"><h4>Rosella</h4></a>

                                <!-- Session Errors -->
                                @if ($errors->any())
                                    <div class="alert alert-danger mt-2">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form class="mt-5 mb-5 login-input" method="POST" action="{{ route('register') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Full Name">
                                    </div>

                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Email">
                                    </div>

                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control" required placeholder="Password">
                                    </div>

                                    <div class="form-group">
                                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm Password">
                                    </div>

                                    <button type="submit" class="btn login-form__btn submit w-100">{{ __('Register') }}</button>
                                </form>

                                <p class="mt-5 login-form__footer">Already have an account?
                                    <a href="{{ route('login') }}" class="text-primary">Login</a> now
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('plugins/common/common.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/gleek.js') }}"></script>
    <script src="{{ asset('js/styleSwitcher.js') }}"></script>
</body>
</html>
