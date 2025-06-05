<?php
// signup_login.php

// Initialize variables
$mode = isset($_POST['mode']) ? $_POST['mode'] : 'login'; // default to login

// Shared
$name = $email = $password = $confirm_password = "";
$name_err = $email_err = $password_err = $confirm_password_err = $login_err = $success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($mode === 'signup') {
        // SIGN UP LOGIC
        if (empty(trim($_POST["name"]))) {
            $name_err = "Please enter your name.";
        } else {
            $name = trim($_POST["name"]);
        }
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        } else {
            $email = trim($_POST["email"]);
        }
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm your password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if ($password !== $confirm_password) {
                $confirm_password_err = "Passwords do not match.";
            }
        }
        if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
            $success = "Account created successfully! (Add DB logic here)";
            // Insert into DB here
        }
    } else {
        // LOGIN LOGIC
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } else {
            $email = trim($_POST["email"]);
        }
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }
        // Dummy check (replace with DB logic)
        if (empty($email_err) && empty($password_err)) {
            if ($email === "user@example.com" && $password === "password123") {
                header("Location: dashboard.php");
                exit();
            } else {
                $login_err = "Invalid email or password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Auth | Sign Up & Log In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/signup.css">
    <script>
        function toggleMode(mode) {
            document.getElementById('mode').value = mode;
            document.getElementById('auth-title').innerText = (mode === 'signup') ? 'Sign Up' : 'Log In';
            document.getElementById('signup-fields').style.display = (mode === 'signup') ? 'block' : 'none';
            document.getElementById('signup-fields-confirm').style.display = (mode === 'signup') ? 'block' : 'none';
            var btns = document.getElementsByClassName('auth-toggle-btn');
            btns[0].classList.toggle('active', mode === 'login');
            btns[1].classList.toggle('active', mode === 'signup');
        }
        window.onload = function() {
            toggleMode('<?php echo $mode; ?>');
        }
    </script>
</head>
<body>
    <div class="auth-3d-container">
        <div class="auth-card" id="authCard">
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
                <button type="button" class="auth-toggle-btn<?php echo ($mode === 'login') ? ' active' : ''; ?>" onclick="toggleMode('login')">Log In</button>
                <button type="button" class="auth-toggle-btn<?php echo ($mode === 'signup') ? ' active' : ''; ?>" onclick="toggleMode('signup')">Sign Up</button>
            </div>
            <h2 id="auth-title"><?php echo ($mode === 'signup') ? 'Sign Up' : 'Log In'; ?></h2>
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($login_err && $mode === 'login'): ?>
                <div class="error"><?php echo $login_err; ?></div>
            <?php endif; ?>
            <form action="" method="post" autocomplete="off" style="width:100%;z-index:3;">
                <input type="hidden" name="mode" id="mode" value="<?php echo $mode; ?>">
                <div id="signup-fields" style="display:<?php echo ($mode === 'signup') ? 'block' : 'none'; ?>;">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder=" " value="<?php echo htmlspecialchars($name); ?>" id="nameInput">
                        <label for="nameInput">Name</label>
                        <?php if ($name_err): ?><div class="error"><?php echo $name_err; ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder=" " value="<?php echo htmlspecialchars($email); ?>" id="emailInput">
                    <label for="emailInput">Email</label>
                    <?php if ($email_err): ?><div class="error"><?php echo $email_err; ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder=" " id="passwordInput">
                    <label for="passwordInput">Password</label>
                    <?php if ($password_err): ?><div class="error"><?php echo $password_err; ?></div><?php endif; ?>
                </div>
                <div id="signup-fields-confirm" style="display:<?php echo ($mode === 'signup') ? 'block' : 'none'; ?>;">
                    <div class="form-group">
                        <input type="password" name="confirm_password" class="form-control" placeholder=" " id="confirmPasswordInput">
                        <label for="confirmPasswordInput">Confirm Password</label>
                        <?php if ($confirm_password_err): ?><div class="error"><?php echo $confirm_password_err; ?></div><?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="submitBtn"><?php echo ($mode === 'signup') ? 'Create Account' : 'Log In'; ?></button>
            </form>
        </div>
    </div>
</body>
</html> 