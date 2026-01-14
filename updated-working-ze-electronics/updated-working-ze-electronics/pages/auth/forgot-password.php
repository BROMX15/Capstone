<?php
// Small PHP block for displaying messages (optional but very useful)
$status = $_GET['status'] ?? '';
$error  = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Forgot Password - ZE Electronics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/auth/forgot-password.css?v=<?= time() ?>">
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="profile">
            <img src="../../assets/images/avatar.jpg" alt="ZE Electronics Logo" />
            <span class="role">ZE Electronics</span>
        </div>

        <div class="sidebar-brand">
            <div class="logo">ZE</div>
            <span>Procurement System</span>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="forgot-container">
            <div class="forgot-card">
                <div class="forgot-header">
                    <i class="fas fa-lock"></i>
                    <h1>Forgot Password</h1>
                    <p>Enter your email address and we'll send you a password reset link</p>
                </div>

                <!-- Success / Error Messages -->
                <?php if ($status === 'sent'): ?>
                    <div class="alert success">
                        <i class="fas fa-check-circle"></i>
                        If an account with that email exists, a reset link has been sent.<br>
                        <small>Please check your inbox (and spam/junk folder).</small>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php
                        echo htmlspecialchars(match($error) {
                            'invalid_email'    => 'Please enter a valid email address',
                            'invalid_method'   => 'Invalid request method. Please use the form.',
                            'too_many_requests'=> 'Too many attempts. Please try again later.',
                            'system_error'     => 'System error. Please try again later.',
                            default            => 'An error occurred. Please try again.'
                        });
                        ?>
                    </div>
                <?php endif; ?>

                <!-- The Form - VERY IMPORTANT FIXES HERE -->
                <form class="forgot-form" method="POST" action="../../backend/auth/forgot-password.php">
                    <!-- Optional: CSRF protection (if you generate token in session) -->
                    <!-- <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"> -->

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="name@zeelectronics.com" 
                            required 
                            autocomplete="email"
                            autofocus
                        />
                    </div>

                    <button type="submit" class="btn-reset">
                        Send Reset Link
                    </button>
                </form>

                <div class="forgot-footer">
                    <a href="../../login.php" class="back-link">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>