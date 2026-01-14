<?php
// fetch-users.php
header('Content-Type: application/json; charset=utf-8');
require_once "../config/connection.php";

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403);
    exit(json_encode([
        'success' => false,
        'error'   => 'Unauthorized'
    ]));
}

try {
    $sql = "SELECT 
                id, firstname, lastname, middlename, username, email, 
                role, gender, created_at, is_active 
            FROM users 
            ORDER BY id DESC";

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        throw new Exception("Query failed");
    }

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = [
            'id'         => (int)$row['id'],
            'firstname'  => $row['firstname'] ?? '',
            'lastname'   => $row['lastname'] ?? '',
            'middlename' => $row['middlename'] ?? '',
            'username'   => $row['username'] ?? '',
            'email'      => $row['email'] ?? '',
            'role'       => $row['role'] ?? 'REQUESTOR',
            'gender'     => $row['gender'] ?? '—',
            'created_at' => $row['created_at'],
            'active'     => (bool)$row['is_active']
        ];
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data'    => $users,
        'total'   => count($users)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Server error occurred'
        // Do NOT expose $e->getMessage() in production!
    ]);
}