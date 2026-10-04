<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $offline ? 'Temporarily unavailable' : 'Temporarily paused' }} · {{ config('app.name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0d1318; color: #d4dee8; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; padding: 20px; box-sizing: border-box; }
        .box { max-width: 480px; text-align: center; background: #141c22; border: 1px solid #1f2832; border-radius: 16px; padding: 36px 28px; }
        .icon { font-size: 44px; }
        h1 { margin: 12px 0 10px; font-size: 22px; color: #fff; }
        p { margin: 0 0 22px; line-height: 1.6; color: #a9b9c7; }
        a { display: inline-block; padding: 10px 18px; border-radius: 8px; background: #3f7871; color: #fff; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">{{ $offline ? '🛠️' : '⏸️' }}</div>
        <h1>{{ $offline ? "We'll be back soon" : 'Temporarily paused' }}</h1>
        <p>{{ $message }}</p>
        @unless ($offline)
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}">Go back</a>
        @endunless
    </div>
</body>
</html>
