<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $offline ? 'Temporarily unavailable' : 'Temporarily paused' }} · {{ config('app.name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #ffffff; color: #203c30; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; padding: 20px; box-sizing: border-box; }
        .box { max-width: 480px; text-align: center; background: #ffffff; border: 1px solid #e5f2e6; border-radius: 16px; padding: 36px 28px; }
        .icon { font-size: 44px; }
        h1 { margin: 12px 0 10px; font-size: 22px; color: #203c30; }
        p { margin: 0 0 22px; line-height: 1.6; color: #203c30; }
        a, button { display: inline-block; border: 0; cursor: pointer; font-size: 15px; padding: 10px 18px; border-radius: 8px; background: #287b62; color: #fff; text-decoration: none; font-weight: 600; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/light-green-theme.css') }}?v={{ filemtime(public_path('css/light-green-theme.css')) }}">
</head>
<body>
    <div class="box">
        <div class="icon">{{ $offline ? '🛠️' : '⏸️' }}</div>
        <h1>{{ $offline ? "We'll be back soon" : 'Temporarily paused' }}</h1>
        <p>{{ $message }}</p>
        @if (!empty($showLogout))
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <input type="hidden" name="to" value="login">
                <button type="submit">Log out</button>
            </form>
        @else
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}">Go back</a>
        @endif
    </div>
</body>
</html>
