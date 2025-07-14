<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMA Negeri 4 Pamekasan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dfeeff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #0d47a1;
            padding: 1rem;
            color: white;
            border-bottom: 5px solid #fbc02d;
        }

        .header .school-name {
            font-weight: bold;
        }

        .login-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-form__btn {
            background-color: #0d47a1;
            color: #fff;
        }

        .login-form__btn:hover {
            background-color: #093170;
        }

        .forgot-password {
            font-size: 0.85rem;
            display: block;
            margin-top: 0.5rem;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header d-flex align-items-center">
        <img src="{{ asset('images/logologin.png') }}" alt="Logo" style="height:50px; margin-right: 15px;">
    </div>

    <div class="login-container">
        <h5 class="text-center fw-bold mb-1">LUPA PASSWORD?</h5>
        <p class="text-center text-muted">Masukkan email Anda untuk menerima link reset password</p>

        <div class="login-card">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" :value="old('email')" required autofocus placeholder="Email">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn login-form__btn submit w-100">
                    {{ __('Kirim Link Reset Password') }}
                </button>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
