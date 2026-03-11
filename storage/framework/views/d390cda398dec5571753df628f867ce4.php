<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
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
      --bg:#0b0e12;
      --card:#0f141b;
      --muted:#8b9aa5;
      --text:#e9eef3;
      --accent:#3f7871;
      --accent-2:#3f7871;
      --border:#1b222b;
      --radius:14px;
      --shadow:0 10px 30px rgba(0,0,0,.45);
    }
    *{box-sizing:border-box;
    margin:0px;
    padding:0px;
    }
    body{
      margin:0;
      font-family:"Inter",-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      background:radial-gradient(900px 600px at 30% -10%,#0f1a12 0%,transparent 70%),var(--bg);
      color:var(--text);
    
      overflow-x:hidden !important;
    }
.custom-row label {
    text-align: start !important;
    font-size: 20px !important;
    color: #fff !important;
}
.input-custom select{
        padding: 20px;
    font-size: 20px;
}
.input-custom input {
    padding: 20px;
    font-size: 20px;
}

.check-box-custom {
    display: flex;
    gap: 11px;
}
   .common-section  .container {
    width: 63%;
    background: linear-gradient(180deg, #0f1620, #0b1117);
    border: 1px solid #1f2832;
    border-radius: 20px;
    padding: 35px;
}
    section.common-section {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background: radial-gradient(900px 600px at 30% -10%, #0f1a12 0%, transparent 70%), var(--bg);
}
.reg-section{
  height: 100%  !important;
  padding-top:50px;
  padding-bottom:50px;
}
    .login-container{
      background:linear-gradient(180deg,#0f1620,#0b1117);
      border:1px solid #1f2832;
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
                 radial-gradient(circle at 0% 80%,#12d1ff1b,transparent 60%);
      filter:blur(8px);
      z-index:-1;
    }
    .logo{
      width:56px;height:56px;margin:0 auto 14px;border-radius:50%;
   
      display:grid;place-items:center;font-weight:900;color:#000;box-shadow:0 0 0 3px #0f141b;
    }
    h1{
      font-size:24px;
      margin-bottom:10px;
      text-align:center;
    }
    p.subtitle{
      color:#b2bec7;
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
    color: #b9c6cf;
    font-size: 22px;
}
    input{
      width:100%;
      padding:12px 14px;
      border-radius:10px;
      border:1px solid #24303f;
      background:#0f1620;
      color:#fff;
      font-size:15px;
      transition:border .25s;
    }
    input:focus{
      outline:none;
      border-color:#a7ff1e;
      box-shadow:0 0 10px #a7ff1e33;
    }
    .btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(90deg,var(--accent),#3f7871);
    color: #ffffffff;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 0 0 6px #a7ff1e15;
    transition: all .25s;
    font-size: 25px;
    padding: 10px;
}
    .btn:hover{
      transform:translateY(-1px);
      box-shadow:0 0 0 8px #a7ff1e25;
    }
    .extra-links{
      margin-top:20px;
      font-size:14px;
    }
    .extra-links a{
      color:var(--accent);
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

</head>
<body>
    <div id="app">
        

        <main >
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</body>
</html>
<?php /**PATH /Users/kuldeepsharma/Projects/hgnlplay/resources/views/layouts/app.blade.php ENDPATH**/ ?>