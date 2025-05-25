<?php
header('Content-Type: application/json');
require '../db.php';

if (!isset($_POST['email'], $_POST['new_password'])) {
    http_response_code(400);
    echo json_encode(['stat' => false, 'message' => 'Email and new password required']);
    exit;
}
$email = $_POST['email'];
$new_password = $_POST['new_password'];

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['stat' => false, 'message' => 'User not found']);
    exit;
}

$hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
if ($stmt->execute([$hash, $email])) {
    echo json_encode(['stat' => true, 'message' => 'Password updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['stat' => false, 'message' => 'Password update failed']);
}
?>
