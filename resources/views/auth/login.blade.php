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
            padding: 40px 0;
            box-sizing: border-box;
            background: radial-gradient(circle at 30% -20%, #1a2a1f 0%, transparent 60%), #06090c;
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
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 25px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(167, 255, 30, 0.2);
        }

        .login-logo img {
            width: 85%;
            object-fit: contain;
        }

        .brand-text h1 {
            font-size: 32px;
            margin: 0;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .brand-accent {
            color: #a7ff1e;
            /* Neon Green matched to Register form */
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
            color: #a0acb3;
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

        .login-input:focus {
            outline: none;
            border-color: #a7ff1e;
            background-color: #0d1218;
            box-shadow: 0 0 15px rgba(167, 255, 30, 0.1);
        }

        .forgot-link {
            align-self: flex-end;
            font-size: 13px;
            color: #a7ff1e;
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
            background-color: #a7ff1e;
            color: #000;
            font-weight: 800;
            border: none;
            border-radius: 10px;
            padding: 20px;
            font-size: 16px;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(167, 255, 30, 0.2);
        }

        .hgnl-btn-primary:hover {
            background-color: #c1ff5e;
            transform: translateY(-2px);
        }

        .hgnl-btn-secondary {
            background: transparent;
            color: #a7ff1e;
            font-weight: 700;
            border: 2px solid #a7ff1e;
            border-radius: 10px;
            padding: 18px;
            font-size: 14px;
            text-transform: uppercase;
            text-decoration: none;
            text-align: center;
            transition: 0.3s;
        }

        .hgnl-btn-secondary:hover {
            background: rgba(167, 255, 30, 0.05);
            border-color: #fff;
            color: #fff;
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
                padding: 30px;
            }

            .login-header {
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
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Himalaya Trading">
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
                            name="member_id" value="{{ old('member_id') }}" placeholder="e.g. HGNL10001" required autofocus>

                        @error('member_id')
                            <span style="color: #ff5a5a; font-size: 12px; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="login-field">
                        <label for="password">Security Password</label>
                        <input id="password" type="password" class="login-input @error('password') is-invalid @enderror"
                            name="password" placeholder="••••••••" required>

                        @if (Route::has('password.request'))
                            <a class="forgot-link" href="{{ route('password.request') }}">Forgot Security Key?</a>
                        @endif

                        @error('password')
                            <span style="color: #ff5a5a; font-size: 12px; margin-top: 5px;">{{ $message }}</span>
                        @enderror
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
@endsection
