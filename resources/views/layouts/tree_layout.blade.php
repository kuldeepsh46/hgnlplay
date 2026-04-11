<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e4f7a;
            --secondary: #e53935;
            --bg: #f0f2f5;
            --card-bg: #ffffff;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg);
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        .header-ui {
            background: var(--primary);
            color: white;
            padding: 30px 20px;
            border-radius: 0 0 30px 30px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Responsive UI Card */
        .tree-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        @yield('styles')
    </style>
</head>
<body>
    <div class="header-ui">
        <h2 style="margin:0; font-weight: 800;">My Network</h2>
        <p style="opacity: 0.8; margin: 5px 0 0 0;">Visual Genealogy Tree</p>
    </div>
    <div class="container">
        <div class="tree-card">
            @yield('content')
        </div>
    </div>
</body>
</html>