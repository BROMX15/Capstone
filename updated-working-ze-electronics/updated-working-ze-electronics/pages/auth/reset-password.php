<?php
// Optional: Small PHP block to show error/success messages from redirect
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Reset Password - ZE Electronics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../assets/css/auth/reset-password.css?v=<?= time() ?>">
</head>
<body>

<div class="container">
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

    <main class="main-content">
        <div class="reset-container">
            <div class="reset-card">
                <div class="reset-header">
                    <i class="fas fa-key"></i>
                    <h1>Reset Your Password</h1>
                    <p>Create a strong new password for your account</p>
                </div>

                <!-- Error message display -->
                <?php if ($error): ?>
                    <div class="alert error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php
                        echo htmlspecialchars(match($error) {
                            'empty'          => 'Please fill in both password fields',
                            'nomatch'        => 'Passwords do not match',
                            'weak_password'  => 'Password is too weak. It must be at least 10 characters and contain uppercase, lowercase, number and special character.',
                            'reset_expired'  => 'This reset link has expired. Please request a new one.',
                            'system_error'   => 'Something went wrong. Please try again.',
                            default          => 'An error occurred. Please try again.'
                        });
                        ?>
                    </div>
                <?php endif; ?>

                <!-- The Form – Important fixes here -->
                <form class="reset-form" method="POST" action="../../backend/auth/reset-password.php">
                    <!-- Optional CSRF (if you implement it later) -->
                    <!-- <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"> -->

                    <!-- If using token in URL (future improvement) -->
                    <!-- <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>"> -->

                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <input 
                            type="password" 
                            id="new-password" 
                            name="password" 
                            placeholder="Enter your new password" 
                            required 
                            minlength="10"
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirm New Password</label>
                        <input 
                            type="password" 
                            id="confirm-password" 
                            name="confirm_password" 
                            placeholder="Confirm your new password" 
                            required 
                            minlength="10"
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="password-requirements">
                        <p>Password must:</p>
                        <ul>
                            <li>Be at least 10 characters long</li>
                            <li>Contain at least one uppercase letter</li>
                            <li>Contain at least one lowercase letter</li>
                            <li>Contain at least one number</li>
                            <li>Contain at least one special character</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-reset">
                        Reset Password
                    </button>
                </form>

                <div class="reset-footer">
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