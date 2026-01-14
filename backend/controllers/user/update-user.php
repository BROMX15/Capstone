<?php
// update-user.php
header('Content-Type: application/json; charset=utf-8');
require_once "../config/connection.php";

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// CSRF
if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$id = (int)$data['id'];
$firstname  = trim($data['firstname'] ?? '');
$lastname   = trim($data['lastname'] ?? '');
$middlename = trim($data['middlename'] ?? '');
$email      = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$role       = strtoupper(trim($data['role'] ?? ''));
$gender     = trim($data['gender'] ?? '—');

if ($id <= 0 || empty($firstname) || empty($lastname) || !$email) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing or invalid']);
    exit;
}

$allowed_roles = ['ADMIN', 'APPROVER', 'REQUESTOR'];
if (!in_array($role, $allowed_roles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

$stmt = $connection->prepare("
    UPDATE users 
    SET firstname = ?, lastname = ?, middlename = ?, email = ?, role = ?, gender = ?
    WHERE id = ?
");

$stmt->bind_param("ssssssi", $firstname, $lastname, $middlename, $email, $role, $gender, $id);

if ($stmt->execute()) {
    $affected = $stmt->affected_rows;
    echo json_encode([
        'success' => $affected > 0,
        'message' => $affected > 0 ? 'User updated successfully' : 'No changes or user not found'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Update failed'
    ]);
}