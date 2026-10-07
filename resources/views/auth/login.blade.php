@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <style>
        /* ================= LAYOUT & BACKGROUND ================= */
        .hgnl-login-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            background: radial-gradient(900px 600px at 20% -10%, rgba(63, 120, 113, .28) 0%, transparent 65%),
                radial-gradient(700px 500px at 100% 110%, rgba(41, 59, 143, .25) 0%, transparent 60%), #06090c;
            padding: 40px 16px;
            font-family: 'Inter', sans-serif;
        }

        /* ================= WIDE GLASS CARD ================= */
        .hgnl-login-card {
            width: 95%;
            max-width: 1100px;
            /* Wider for desktop consistency */
            background-color: #10171f;
            border: 1px solid #1b222b;
            border-radius: 16px;
            padding: 60px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);
            box-sizing: border-box;
        }

        /* ================= BRANDING SECTION ================= */
        .login-header {
            display: flex;
            align-items: center;
            margin-bottom: 50px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 30px;
        }

        .login-logo {
            flex-shrink: 0;
            padding: 6px;
            background: #fff;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 25px;
            overflow: hidden;
            box-shadow: 0 0 0 4px rgba(108, 195, 183, 0.15);
        }

        .login-logo img {
            display: block;
            height: 76px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
        }

        .brand-text {
            text-align: left;
        }

        .brand-text h1 {
            font-size: 32px;
            margin: 0;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            text-align: inherit;
        }

        .brand-accent {
            color: #6cc3b7;
        }

        .brand-text p {
            color: #a0acb3;
            margin: 5px 0 0 0;
            font-size: 15px;
        }

        /* ================= FORM GRID ================= */
        .login-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* Split for Member ID and Password */
            gap: 30px;
        }

        .login-field {
            display: flex;
            flex-direction: column;
        }

        .login-field.full-width {
            grid-column: span 2;
        }

        .login-field label {
            font-size: 12px;
            text-transform: uppercase;
            color: #b9c6cf;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* ================= INPUTS ================= */
        .login-input {
            width: 100%;
            padding: 18px;
            border-radius: 8px;
            border: 1px solid #1b222b;
            background-color: #0b0e12;
            color: #fff;
            font-size: 16px;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
        }

        .login-input::placeholder {
            color: #6b7782;
        }

        .login-input:hover {
            border-color: #2a3542;
        }

        .login-input:focus {
            outline: none;
            border-color: #6cc3b7;
            background-color: #0d1218;
            box-shadow: 0 0 0 3px rgba(108, 195, 183, 0.25);
        }

        .login-input.is-invalid {
            border-color: #ff6b6b;
        }

        .field-error {
            color: #ff8a8a;
            font-size: 13px;
            margin-top: 6px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .login-input {
            padding-right: 84px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: #141c26;
            color: #b9c6cf;
            border: 1px solid #24303f;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: #fff;
            border-color: #6cc3b7;
        }

        .hgnl-login-card :focus-visible {
            outline: 2px solid #8fd8cd;
            outline-offset: 2px;
        }

        .forgot-link {
            align-self: flex-end;
            font-size: 13px;
            color: #6cc3b7;
            text-decoration: none;
            margin-top: 10px;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* ================= ACTION BUTTONS ================= */
        .btn-container {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            /* Login is primary, Register is secondary */
            gap: 20px;
            margin-top: 40px;
        }

        .hgnl-btn-primary {
            background: linear-gradient(90deg, #3f7871, #4f958c);
            color: #fff;
            font-weight: 800;
            border: none;
            border-radius: 10px;
            padding: 20px;
            font-size: 16px;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 24px rgba(63, 120, 113, 0.35);
            letter-spacing: .5px;
        }

        .hgnl-btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        .hgnl-btn-secondary {
            background: transparent;
            color: #6cc3b7;
            font-weight: 700;
            border: 2px solid #3f7871;
            border-radius: 10px;
            padding: 18px;
            font-size: 14px;
            text-transform: uppercase;
            text-decoration: none;
            text-align: center;
            transition: 0.3s;
        }

        .hgnl-btn-secondary:hover {
            background: rgba(108, 195, 183, 0.08);
            border-color: #6cc3b7;
            color: #fff;
        }

        @media (prefers-reduced-motion: reduce) {
            .hgnl-btn-primary,
            .hgnl-btn-secondary,
            .login-input {
                transition: none;
            }

            .hgnl-btn-primary:hover {
                transform: none;
            }
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 850px) {

            .login-grid,
            .btn-container {
                grid-template-columns: 1fr;
            }

            .login-field.full-width {
                grid-column: span 1;
            }

            .hgnl-login-card {
                padding: 32px 22px;
            }

            .brand-text h1 {
                font-size: 26px;
            }

            .login-header {
                margin-bottom: 32px;
                padding-bottom: 24px;
            }

            .btn-container {
                margin-top: 16px;
            }

            .login-header,
            .brand-text {
                flex-direction: column;
                text-align: center;
            }

            .login-logo {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
    <div class="hgnl-login-wrapper">
        <div class="hgnl-login-card">

            <div class="login-header">
                <div class="login-logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Himalaya Pay logo">
                </div>
                <div class="brand-text">
                    <h1>Himalaya <span class="brand-accent">Trading</span></h1>
                    <p>Login to manage your portfolio and team.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="login-grid">

                    <div class="login-field">
                        <label for="member_id">Member ID</label>
                        <input id="member_id" type="text" class="login-input @error('member_id') is-invalid @enderror"
                            name="member_id" value="{{ old('member_id') }}" placeholder="e.g. HGNL10001"
                            autocomplete="username" autocapitalize="characters" spellcheck="false" required autofocus
                            @error('member_id') aria-invalid="true" aria-describedby="member_id-error" @enderror>

                        @error('member_id')
                            <span class="field-error" id="member_id-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="login-field">
                        <label for="password">Security Password</label>
                        <div class="password-wrap">
                            <input id="password" type="password"
                                class="login-input @error('password') is-invalid @enderror" name="password"
                                placeholder="Enter your password" autocomplete="current-password" required
                                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                            <button type="button" class="toggle-password" aria-controls="password"
                                aria-pressed="false">Show</button>
                        </div>

                        @error('password')
                            <span class="field-error" id="password-error" role="alert">{{ $message }}</span>
                        @enderror

                        @if (Route::has('password.request'))
                            <a class="forgot-link" href="{{ route('password.request') }}">Forgot Security Key?</a>
                        @endif
                    </div>

                    <div class="login-field full-width">
                        <div class="btn-container">
                            <button type="submit" class="hgnl-btn-primary">
                                Login
                            </button>
                            <a href="{{ route('member.register') }}" class="hgnl-btn-secondary">
                                Create Account
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.getAttribute('aria-controls'));
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.textContent = show ? 'Hide' : 'Show';
                btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            });
        });
    </script>
@endsection
