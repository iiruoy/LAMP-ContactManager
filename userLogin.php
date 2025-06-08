<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Stock Auth | Sign Up & Log In</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="stylesheet" href="css/signup.css">
  <script>
    function toggleMode(m) {
      document.getElementById('mode').value = m;
      document.getElementById('auth-title').innerText = m === 'signup' ? 'Sign Up' : 'Log In';
      document.getElementById('signup-fields').style.display = m === 'signup' ? 'block' : 'none';
      document.getElementById('signup-fields-confirm').style.display = m === 'signup' ? 'block' : 'none';
      document.querySelectorAll('.auth-toggle-btn').forEach((btn, i) => {
        btn.classList.toggle('active', (i === 0 && m === 'login') || (i === 1 && m === 'signup'));
      });
    }

    window.onload = () => toggleMode('login');

    async function handleSubmit(e) {
      e.preventDefault();
      const mode = document.getElementById('mode').value;
      const email = document.querySelector('[name="email"]').value.trim();
      const password = document.querySelector('[name="password"]').value.trim();

      let payload = { login: email, password };

      if (mode === 'signup') {
        const name = document.querySelector('[name="name"]').value.trim();
        const confirmPassword = document.querySelector('[name="confirm_password"]').value.trim();
        if (password !== confirmPassword) {
          alert("Passwords do not match.");
          return;
        }
        const [first, last] = name.split(' ');
        payload = {
          FirstName: first || '',
          LastName: last || '',
          Email: email,
          Phone: '',
          Company: '',
          login: email,
          password: password
        };
      }

      const res = await fetch('/proxy.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const result = await res.json();

      if (result.error) {
        alert('Error: ' + result.error);
      } else if (mode === 'login') {
        console.log("Login response:", result);
        sessionStorage.setItem('user_id', result.id);
        sessionStorage.setItem('user_name', result.firstName);
        window.location.href = 'dashboard.php';
      } else {
        alert("Account created. Please log in.");
        toggleMode('login');
      }
    }
  </script>
</head>
<body>
  <div class="auth-3d-container">
    <div class="auth-card">
         <div class="ticker-tape">
                <div class="ticker-track" id="tickerTrack">
                    <span>AAPL 192.32 ▲</span>
                    <span>GOOG 2831.12 ▼</span>
                    <span>TSLA 712.45 ▲</span>
                    <span>AMZN 3342.88 ▼</span>
                    <span>MSFT 299.01 ▲</span>
                    <span>NVDA 402.12 ▲</span>
                </div>
            </div>
            <svg class="stock-bg-chart" viewBox="0 0 340 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polyline class="stock-line" points="0,40 30,35 60,38 90,25 120,30 150,20 180,28 210,18 240,25 270,10 300,18 340,8"
                    style="fill:none;stroke:#2ed573;stroke-width:3;stroke-linecap:round;stroke-linejoin:round;" />
                <circle class="stock-dot" r="4" fill="#2ed573">
                    <animate attributeName="cx" values="0;340" dur="3s" repeatCount="indefinite"/>
                    <animate attributeName="cy" values="40;8" dur="3s" repeatCount="indefinite"/>
                </circle>
            </svg>
      <div class="auth-toggle">
        <button type="button" class="auth-toggle-btn" onclick="toggleMode('login')">Log In</button>
        <button type="button" class="auth-toggle-btn" onclick="toggleMode('signup')">Sign Up</button>
      </div>
      <h2 id="auth-title">Log In</h2>
      <form id="authForm" autocomplete="off" onsubmit="handleSubmit(event)">
        <input type="hidden" name="mode" id="mode" value="login">
        <div id="signup-fields" style="display:none">
          <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder=" ">
            <label>Name</label>
          </div>
        </div>
        <div class="form-group">
          <input type="email" name="email" class="form-control" placeholder=" ">
          <label>Email</label>
        </div>
        <div class="form-group">
          <input type="password" name="password" class="form-control" placeholder=" ">
          <label>Password</label>
        </div>
        <div id="signup-fields-confirm" style="display:none">
          <div class="form-group">
            <input type="password" name="confirm_password" class="form-control" placeholder=" ">
            <label>Confirm Password</label>
          </div>
        </div>
        <button type="submit" class="btn-primary">
          Submit
        </button>
      </form>
    </div>
  </div>
</body>
</html>
