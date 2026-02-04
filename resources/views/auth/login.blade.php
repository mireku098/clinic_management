<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">
    <title>Clinic Admin - Login</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
            <div class="account-center">
                <div class="account-box">
                    <form method="POST" action="{{ route('login.perform') }}" class="form-signin">
                        @csrf
                        <div class="account-logo">
                            <a href="{{ route('dashboard') }}">
                                <img src="{{ asset('assets/img/logo-dark.png') }}" alt="Clinic Admin Logo">
                            </a>
                        </div>
                        <div class="form-group">
                            <label for="email">Username or Email</label>
                            <input id="email" type="text" name="email" class="form-control" required autofocus>
                        </div>
                        <div class="form-group password-field">
                            <label for="password">Password</label>
                            <div class="password-wrapper">
                                <input id="password" type="password" name="password" class="form-control" required>
                                <button type="button" class="toggle-password-icon" aria-label="Toggle password visibility" data-target="#password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <a href="#">Forgot your password?</a>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary account-btn">Login</button>
                        </div>
                        <div class="text-center register-link">
                            Don’t have an account? <a href="{{ route('register') }}">Register Now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .password-wrapper {
            position: relative;
        }
        .toggle-password-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #6c757d;
            padding: 0;
            line-height: 1;
        }
        .toggle-password-icon:focus {
            outline: none;
            color: #0d6efd;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-password-icon').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.querySelector(this.dataset.target);
                    if (!input) {
                        return;
                    }

                    const isHidden = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isHidden ? 'text' : 'password');
                    this.innerHTML = isHidden
                        ? '<i class="fa fa-eye-slash"></i>'
                        : '<i class="fa fa-eye"></i>';
                });
            });
        });
    </script>
    @if(session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const swalConfig = @json(session('swal'));
                if (window.Swal && swalConfig) {
                    Swal.fire({
                        icon: swalConfig.icon || 'info',
                        title: swalConfig.title || 'Notification',
                        text: swalConfig.text || '',
                        showConfirmButton: swalConfig.showConfirmButton ?? false,
                        timer: swalConfig.timer || 2200,
                    });
                }
            });
        </script>
    @endif
</body>
</html>
