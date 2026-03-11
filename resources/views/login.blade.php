<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Himalaya Trading</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b0e12;
      --card:#0f141b;
      --muted:#8b9aa5;
      --text:#e9eef3;
      --accent:#a7ff1e;
      --accent-2:#2ee6a6;
      --border:#1b222b;
      --radius:14px;
      --shadow:0 10px 30px rgba(0,0,0,.45);
    }
    *{box-sizing:border-box
    
    }
    section.common-section {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background: radial-gradient(900px 600px at 30% -10%, #0f1a12 0%, transparent 70%), var(--bg);
    overflow: hidden;
}
    body{
      margin:0;
      padding: 0px;
      font-family:"Inter",-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      background:radial-gradient(900px 600px at 30% -10%,#0f1a12 0%,transparent 70%),var(--bg);
      color:var(--text);
      display:flex;
      align-items:center;
      justify-content:center;
  
      overflow:hidden;
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
      /* background:conic-gradient(from 120deg,#2ee6a6,#a7ff1e,#12d1ff,#2ee6a6); */
      display:grid;place-items:center;font-weight:900;color:#000;box-shadow:0 0 0 3px #0f141b;
    }
    .brand h1 {
    font-size: 18px;
    margin: 0;
    letter-spacing: .5px;
}.brand-green {
    color: #e84e6d;
}
    .logo img {
    width: 60px ;
    height: 60px;
    display: grid;
    place-items: center;
    color: #06170a;
    font-weight: 900;
}
    h2{
      font-size:24px;
      margin-bottom:6px;
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
    label{
      display:block;
      font-weight:600;
      margin-bottom:6px;
      color:#b9c6cf;
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
    padding: 9px !important;
    border: none;
    border-radius: 10px;
    background: #3f7871 !important;
    color: #071003;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 0 0 6px #a7ff1e15;
    transition: all .25s;
    font-size: 21px;
    padding: 20px;
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

  <div class="login-container">
<div class="brand">
                <div class="logo">
                    <img src="/storage/logo.png" alt="Himalaya Trading">
                </div>
                <h1>Himalaya <span class="brand-green">Trading</span></h1>
            </div>

    <h2>Welcome Back</h2>
    <p class="subtitle">Login to continue your journey</p>
    <form onsubmit="return handleLogin(event)">
      <div>
        <label for="username">Username</label>
        <input type="text" id="username" placeholder="Enter your username" required>
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
    <!-- <div class="extra-links">
      <p>Forgot password? <a href="#">Reset here</a></p>
      <p>Don’t have an account? <a href="#">Register</a></p>
    </div> -->
  </div>

  <script>
    // Simple form handler
    function handleLogin(e){
      e.preventDefault();
      const user=document.getElementById('username').value.trim();
      const pass=document.getElementById('password').value.trim();
      if(!user||!pass){
        alert("Please fill all fields");
        return false;
      }
      // Demo feedback
      alert(`Welcome ${user}! (demo only)`);
      return false;
    }
  </script>

</body>
</html>
