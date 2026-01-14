<?php
session_start();

// If already logged in → redirect based on role
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && !empty($_SESSION['role'])) {
    $redirect = match (strtoupper($_SESSION['role'])) {
        'ADMIN'     => 'pages/admin/admin-dashboard.php',
        'APPROVER'  => 'pages/approver/pending-approvals.html',
        'REQUESTOR' => 'pages/requestor/dashboard.html',
        default     => 'login.php?error=invalid_role'
    };
    header("Location: ../../$redirect");
    exit();
}

// Generate CSRF token for login form (optional but recommended)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ZE Electronics - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/auth/style.css" />
</head>
<body>

<div class="container">
    <div class="login-wrapper">
        <div class="logo-container">
            <img src="assets/images/company-logo.png" alt="ZE Electronics Logo" />
        </div>

        <div class="login-box">
            <h4>Enter your<br/>Company Account</h4>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php
                    $msg = match ($_GET['error']) {
                        'empty_fields'      => 'Please fill in all fields',
                        'invalid_credentials' => 'Invalid username or password',
                        'too_many_attempts' => 'Too many failed attempts. Please try again later.',
                        'invalid_role'      => 'Account role is invalid. Contact support.',
                        default             => 'An error occurred. Please try again.'
                    };
                    echo htmlspecialchars($msg);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i>
                    <?php
                    $successMsg = match ($_GET['success']) {
                        'password_reset' => 'Password successfully reset. Please login.',
                        default          => 'Operation completed successfully.'
                    };
                    echo htmlspecialchars($successMsg);
                    ?>
                </div>
            <?php endif; ?>

            <form action="backend/auth/auth.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="input-group">
                    <i class="fas fa-user icon"></i>
                    <input type="text" name="username" placeholder="Username or Email" required autocomplete="username" autofocus />
                </div>

                <div class="input-group">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password" placeholder="Password" required autocomplete="current-password" />
                </div>

                <button type="submit" class="btn login-btn">SIGN IN</button>
            </form>

            <div class="links">
                <a href="pages/auth/forgot-password.php" class="forgot-link links-btn">Forgot Password?</a>
                <a href="pages/auth/reset-password.php" class="reset-link links-btn">Reset Password?</a>
            </div>
        </div>
    </div>

    <div class="right-image">
        <img src="assets/images/company-background.png" alt="ZE Electronics Team" />
        <div class="overlay"></div>
    </div>
</div>

</body>
</html>