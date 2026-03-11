@extends('layouts.app')

@section('content')
<style>
    /* Section & Container Styling */
    section.common-section {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background: radial-gradient(900px 600px at 30% -10%, #0f1a12 0%, transparent 70%), var(--bg, #0f141b);
        overflow: hidden;
        color: #fff;
        font-family: 'Inter', sans-serif;
    }

    .login-container {
        width: 100%;
        max-width: 400px;
        padding: 40px;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    /* Brand & Logo */
    .logo {
        width: 93px;
        height: 93px;
        margin: 0 auto 20px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(232, 78, 109, 0.2);
        overflow: hidden;
    }

    .logo img {
        width: 80%;
        height: auto;
        object-fit: contain;
    }

    .brand h1 {
        font-size: 28px;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #fff;
    }

    .brand-green {
        color: #e84e6d; /* Pink accent color */
    }

    h2 {
        font-size: 22px;
        margin-top: 20px;
        font-weight: 600;
    }

    .subtitle {
        font-size: 14px;
        color: #aaa;
        margin-bottom: 30px;
    }

    /* Form Styling */
    .form-group {
        text-align: left;
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        margin-bottom: 8px;
        color: #ddd;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: #3f7871;
        outline: none;
        box-shadow: 0 0 8px rgba(63, 120, 113, 0.3);
    }

    /* Forgot Password Link */
    .forgot-password-link {
        display: block;
        text-align: right;
        font-size: 12px;
        color: #3f7871;
        text-decoration: none;
        font-weight: 600;
        margin-top: 8px;
        transition: color 0.2s;
    }

    .forgot-password-link:hover {
        color: #e84e6d;
        text-decoration: underline;
    }

    /* Button Styling */
    .btn-login {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        background: #3f7871;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.25s;
        margin-top: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-login:hover {
        background: #4a8c84;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .invalid-feedback {
        color: #ff4d4d;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
</style>

<section class="common-section login-section">
    <div class="login-container">
        <div class="brand">
            <div class="logo">
                <a href="/">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Himalaya Trading">git statusgit status
                </a>
            </div>
            <h1>Himalaya <span class="brand-green">Trading</span></h1>
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Login to continue your journey</p>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="member_id">Member ID</label>
                <input id="member_id" type="text" class="form-control @error('member_id') is-invalid @enderror" 
                    name="member_id" value="{{ old('member_id') }}" placeholder="e.g. HGNL10001" required autofocus>

                @error('member_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password">
                
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                @if (Route::has('password.request'))
                    <a class="forgot-password-link" href="{{ route('password.request') }}">
                        Forgot Password?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                Login
            </button>
        </form>
    </div>
</section>
@endsection