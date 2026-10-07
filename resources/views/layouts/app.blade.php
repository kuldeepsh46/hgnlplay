<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="theme-color" content="#f0f8ef">

    <title>@hasSection('title')@yield('title') - @endif{{ config('app.name', 'HGNL Pay') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script> 
  <style>
    :root{
      --bg:#f0f8ef;
      --card:#ffffff;
      --muted:#52695f;
      --text:#203c30;
      --accent:#287b62;
      --accent-2:#287b62;
      --accent-text:#08734f;
      --focus:#218e65;
      --border:#cddfd3;
      --radius:14px;
      --shadow:0 10px 30px rgba(13,77,51,.12);
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:"Inter",-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      background:radial-gradient(900px 600px at 30% -10%,#ffffff 0%,transparent 70%),var(--bg);
      color:var(--text);
      line-height:1.5;
      -webkit-font-smoothing:antialiased;
      overflow-x:hidden !important;
    }
    :focus-visible{
      outline:2px solid var(--focus);
      outline-offset:2px;
    }
    @media (prefers-reduced-motion: reduce){
      *,*::before,*::after{
        animation-duration:.01ms !important;
        transition-duration:.01ms !important;
        scroll-behavior:auto !important;
      }
    }
.custom-row label {
    text-align: start !important;
    font-size: 16px !important;
    color: #203c30 !important;
}
.input-custom select,
.input-custom input {
    padding: 12px 14px;
    font-size: 16px;
}

.check-box-custom {
    display: flex;
    gap: 11px;
}
   .common-section  .container {
    width: 63%;
    background: #ffffff;
    border: 1px solid #e5f2e6;
    border-radius: 20px;
    padding: 35px;
}
    section.common-section {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 24px 16px;
    background: radial-gradient(900px 600px at 30% -10%, #ffffff 0%, transparent 70%), var(--bg);
}
.reg-section{
  height: 100%  !important;
  padding-top:50px;
  padding-bottom:50px;
}
    .login-container{
      background:#ffffff;
      border:1px solid #e5f2e6;
      border-radius:20px;
      padding:40px 35px;
      width:100%;
      max-width:400px;
      box-shadow:var(--shadow);
      text-align:center;
      position:relative;
    }
    .login-container::before{
      content:"";
      position:absolute;
      inset:-40px;
      background:radial-gradient(circle at 50% 0%,#bfff2d22,transparent 60%),
                 radial-gradient(circle at 0% 80%,#327f9e1b,transparent 60%);
      filter:blur(8px);
      z-index:-1;
    }
    .logo{
      width:56px;height:56px;margin:0 auto 14px;border-radius:50%;
   
      display:grid;place-items:center;font-weight:900;color:#000;box-shadow:0 0 0 3px #cddfd3;
    }
    h1{
      font-size:24px;
      margin-bottom:10px;
      text-align:center;
    }
    p.subtitle{
      color:#203c30;
      margin-bottom:30px;
      font-size:14px;
    }
    form{
      display:grid;
      gap:20px;
      text-align:left;
    }
 .common-section label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #203c30;
    font-size: 16px;
}
    input{
      width:100%;
      padding:12px 14px;
      border-radius:10px;
      border:1px solid #cddfd3;
      background:#ffffff;
      color:#203c30;
      font-size:15px;
      transition:border .25s;
    }
    input::placeholder{color:#6b7782}
    input:focus{
      outline:none;
      border-color:var(--accent-text);
      box-shadow:0 0 0 3px rgba(8,115,79,.15);
    }
    .btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(90deg,var(--accent),#287b62);
    color: #ffffffff;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 0 0 6px rgba(35,132,91,.08);
    transition: all .25s;
    font-size: 18px;
    padding: 12px;
}
    .btn:hover{
      transform:translateY(-1px);
      filter:brightness(1.1);
      box-shadow:0 0 0 8px rgba(35,132,91,.15);
    }
    .extra-links{
      margin-top:20px;
      font-size:14px;
    }
    .extra-links a{
      color:var(--accent-text);
      text-decoration:none;
    }
    .extra-links a:hover{
      text-decoration:underline;
    }

    @media(max-width:480px){
      .login-container{
        margin:0 18px;
        padding:32px 24px;
      }
    }
  </style>

    <link rel="stylesheet" href="{{ asset('css/light-green-theme.css') }}">

</head>
<body>
    <div id="app">
        

        <main id="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
