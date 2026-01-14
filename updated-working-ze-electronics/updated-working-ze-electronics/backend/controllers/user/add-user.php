<?php
// add-user.php
header('Content-Type: application/json; charset=utf-8');
require_once "../config/connection.php";

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403);
    exit(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];

if (empty($data)) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'error' => 'Invalid or empty request']));
}

// CSRF check
if (!isset($data['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $data['csrf_token'])) {
    http_response_code(403);
    exit(json_encode(['success' => false, 'error' => 'Invalid CSRF token']));
}

$required = ['firstname','lastname','username','email','password','role'];
foreach ($required as $field) {
    if (empty(trim($data[$field] ?? ''))) {
        exit(json_encode(['success' => false, 'error' => "Field '$field' is required"]));
    }
}

$firstname  = trim($data['firstname']);
$lastname   = trim($data['lastname']);
$middlename = trim($data['middlename'] ?? '');
$username   = trim($data['username']);
$email      = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
$password   = $data['password'];
$role       = strtoupper(trim($data['role']));
$gender     = trim($data['gender'] ?? '—');

// Basic validations
if (!$email) {
    exit(json_encode(['success' => false, 'error' => 'Invalid email format']));
}

if (!in_array($role, ['ADMIN','APPROVER','REQUESTOR'])) {
    exit(json_encode(['success' => false, 'error' => 'Invalid role']));
}

// Password strength check
if (strlen($password) < 10 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[^A-Za-z0-9]/', $password)) {
    exit(json_encode([
        'success' => false,
        'error'   => 'Password must be ≥10 characters and contain uppercase, lowercase, number and special character'
    ]));
}

// Check uniqueness
$stmt = $connection->prepare("SELECT 1 FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    exit(json_encode(['success' => false, 'error' => 'Username or email already exists']));
}
$stmt->close();

// Create user
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $connection->prepare("
    INSERT INTO users 
    (firstname, lastname, middlename, username, email, password, role, gender)
    VALUES (?,?,?,?,?,?,?,?)
");

$stmt->bind_param("ssssssss", $firstname, $lastname, $middlename, $username, $email, $hash, $role, $gender);

if ($stmt->execute()) {
    echo json_encode([
        'success'  => true,
        'message'  => 'User created successfully',
        'user_id'  => $connection->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create user']);
}