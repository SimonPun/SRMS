<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('sneat_backend/assets/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <meta name="description" content="Reset your password" />

    <link rel="icon" type="image/x-icon" href="{{ asset('frontend/resources/images/') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('sneat_backend/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat_backend/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat_backend/assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat_backend/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat_backend/assets/vendor/css/pages/page-auth.css') }}" />

    <script src="{{ asset('sneat_backend/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat_backend/assets/js/config.js') }}"></script>
    <style>
        .auth-shell {
            max-width: 36rem;
            width: 100%;
        }

        .auth-card-body {
            padding: 2.5rem;
        }

        .auth-logo {
            width: 88px;
            height: 88px;
        }

        .auth-toggle-btn {
            min-width: 48px;
        }

        @media (max-width: 575.98px) {
            .authentication-wrapper.authentication-basic {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }

            .auth-card-body {
                padding: 1.5rem 1rem;
            }

            .auth-logo {
                width: 72px;
                height: 72px;
            }

            .auth-title {
                font-size: 1.6rem;
                line-height: 1.2;
            }
        }
    </style>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner auth-shell">
                <div class="card">
                    <div class="card-body auth-card-body">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary mb-3 auth-logo">
                                <span class="text-white fw-bold" style="font-size: 1.4rem; letter-spacing: 0.08em;">SRMS</span>
                            </div>
                            <h2 class="mb-1 auth-title">Reset Password</h2>
                            <p class="text-muted mb-0">Set a new password for your account</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $email) }}"
                                    placeholder="you@example.com" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="resetPassword" name="password"
                                        placeholder="........" required />
                                    <button class="btn btn-outline-secondary auth-toggle-btn" type="button" id="toggleResetPassword">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="resetPasswordConfirmation"
                                        name="password_confirmation" placeholder="........" required />
                                    <button class="btn btn-outline-secondary auth-toggle-btn" type="button"
                                        id="toggleResetPasswordConfirmation">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                            </div>

                            <button class="btn btn-dark w-100">Reset Password</button>
                        </form>

                        <p class="text-center mt-4 mb-0">
                            <a href="{{ route('login') }}">Back to login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const resetPasswordInput = document.getElementById('resetPassword');
        const toggleResetPassword = document.getElementById('toggleResetPassword');
        const resetPasswordConfirmationInput = document.getElementById('resetPasswordConfirmation');
        const toggleResetPasswordConfirmation = document.getElementById('toggleResetPasswordConfirmation');

        const bindToggle = (input, button) => {
            if (!input || !button) {
                return;
            }

            button.addEventListener('click', () => {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.innerHTML = isPassword
                    ? '<i class="bx bx-show"></i>'
                    : '<i class="bx bx-hide"></i>';
            });
        };

        bindToggle(resetPasswordInput, toggleResetPassword);
        bindToggle(resetPasswordConfirmationInput, toggleResetPasswordConfirmation);
    </script>
</body>

</html>
