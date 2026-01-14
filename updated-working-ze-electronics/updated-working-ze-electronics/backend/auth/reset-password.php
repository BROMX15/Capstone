<?php
// reset-password.php  ────────────────────────────────────── UPDATED
session_start();
require_once "../config/connection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../pages/auth/reset-password.php?error=invalid");
    exit();
}

// Very basic check (real version should use token from URL + DB)
if (empty($_SESSION['reset_token']) || 
    empty($_SESSION['reset_email']) || 
    empty($_SESSION['reset_expires']) ||
    time() > strtotime($_SESSION['reset_expires'])) {
    
    unset($_SESSION['reset_token'], $_SESSION['reset_email'], $_SESSION['reset_expires']);
    header("Location: ../../pages/auth/login.php?error=reset_expired");
    exit();
}

$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (empty($password) || empty($confirm)) {
    header("Location: ../../pages/auth/reset-password.php?error=empty");
    exit();
}

if ($password !== $confirm) {
    header("Location: ../../pages/auth/reset-password.php?error=nomatch");
    exit();
}

if (strlen($password) < 8) {
    header("Location: ../../pages/auth/reset-password.php?error=weak_password");
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($connection, 
    "UPDATE users SET password = ? WHERE email = ?");
mysqli_stmt_bind_param($stmt, "ss", $hash, $_SESSION['reset_email']);
$success = mysqli_stmt_execute($stmt);

unset($_SESSION['reset_token'], $_SESSION['reset_email'], $_SESSION['reset_expires']);

if ($success) {
    header("Location: ../../pages/auth/login.php?success=password_reset");
} else {
    header("Location: ../../pages/auth/login.php?error=reset_failed");
}
exit();