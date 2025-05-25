<?php
header('Content-Type: application/json');
require '../db.php';

if (!isset($_POST['email'], $_POST['verifie'])) {
    http_response_code(400);
    echo json_encode(['stat' => false, 'message' => 'Email and code required']);
    exit;
}
$email = $_POST['email'];
$verifie = $_POST['verifie'];

// Check if user exists and verification code matches
$stmt = $pdo->prepare('SELECT verifie FROM users WHERE email = ?');
$stmt->execute([$email]);
$row = $stmt->fetch();
if (!$row) {
    http_response_code(409);
    echo json_encode(['stat' => false, 'message' => 'User not found']);
    exit;
}
if ($row['verifie'] !== $verifie) {
    http_response_code(401);
    echo json_encode(['stat' => false, 'message' => 'Invalid code']);
    exit;
}
$stmt = $pdo->prepare('UPDATE users SET verifie = ? WHERE email = ?');
$stmt->execute(["1", $email]);
echo json_encode(['stat' => true, 'message' => 'Code verified']);
?>