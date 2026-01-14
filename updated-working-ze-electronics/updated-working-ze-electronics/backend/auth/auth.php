<?php
// auth.php - login handler
session_start();
require_once "../config/connection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../login.php?error=invalid_method");
    exit();
}

$input    = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($input) || empty($password)) {
    header("Location: ../../login.php?error=empty_fields");
    exit();
}

// Very simple rate limiting (session based - better with redis/memcache in production)
$attempt_key = 'login_attempts_' . md5($input);
if (!isset($_SESSION[$attempt_key])) {
    $_SESSION[$attempt_key] = ['count' => 0, 'time' => time()];
}

$attempts = &$_SESSION[$attempt_key];

if ($attempts['count'] >= 5 && (time() - $attempts['time']) < 300) {
    header("Location: ../../login.php?error=too_many_attempts");
    exit();
}

$stmt = $connection->prepare("
    SELECT id, firstname, lastname, middlename, username, email, password, role, gender, is_active 
    FROM users 
    WHERE (username = ? OR email = ?) 
    LIMIT 1
");

$stmt->bind_param("ss", $input, $input);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (!$user['is_active']) {
        header("Location: ../../login.php?error=account_disabled");
        exit();
    }

    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION = [
            'loggedin'   => true,
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'role'       => strtoupper($user['role']),
            'fullname'   => trim("{$user['firstname']} {$user['middlename']} {$user['lastname']}"),
            'gender'     => $user['gender'] ?? '—',
            'csrf_token' => bin2hex(random_bytes(32))
        ];

        // Reset attempts
        unset($_SESSION[$attempt_key]);

        $redirect_map = [
            'ADMIN'    => '../../pages/admin/admin-dashboard.php',
            'APPROVER' => '../../pages/approver/dashboard-overview.html',
            'REQUESTOR'=> '../../pages/requestor/dashboard.html'
        ];

        $role = $_SESSION['role'];
        header("Location: " . ($redirect_map[$role] ?? '../../login.php?error=invalid_role'));
        exit();
    }
}

// Failed attempt
$attempts['count']++;
$attempts['time'] = time();

header("Location: ../../login.php?error=invalid_credentials");
exit();