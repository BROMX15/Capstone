<?php
// forgot-password.php  ───────────────────────────────────── UPDATED
session_start();
require_once "../config/connection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../pages/auth/forgot-password.php?error=invalid");
    exit();
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if (!$email) {
    header("Location: ../../pages/auth/forgot-password.php?error=invalid_email");
    exit();
}

$stmt = mysqli_prepare($connection, "SELECT id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    // Don't tell attacker if email exists → security best practice
    header("Location: ../../pages/auth/forgot-password.php?status=sent");
    exit();
}

// Real implementation would send email here
// For development/demo: store in session + show instructions
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

// In real system → save to DB + send email with link
$_SESSION['reset_token']      = $token;
$_SESSION['reset_email']      = $email;
$_SESSION['reset_expires']    = $expires;

header("Location: ../../pages/auth/forgot-password.php?status=sent");
exit();