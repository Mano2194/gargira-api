<?php
header('Content-Type: application/json');
require '../db.php';

if (!isset($_POST['email'], $_POST['password'])) {
    http_response_code(400);
    echo json_encode(['stat' => false,'message' => 'Email and password required']);
    exit;
}
$email = $_POST['email'];
$password = $_POST['password'];

// Check if user exists
$stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();
// check password
if ($user && password_verify($password, $user['password'])) {
    echo json_encode(['stat' => true, 'user_id' => $user['id']]);
} else {
    http_response_code(401);
    echo json_encode(['stat' => false,'message' => 'Invalid credentials']);
}
?>
