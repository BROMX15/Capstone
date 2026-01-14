<?php
// logout.php
session_start();

// Clear all session data
$_SESSION = [];

// Destroy session and cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Regenerate ID to prevent session fixation (even after destroy)
session_start();
session_regenerate_id(true);
session_destroy();

header("Location: ../../login.php");
exit;