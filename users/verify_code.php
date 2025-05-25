<?php
header('Content-Type: application/json');
require '../db.php';

if (!isset($_POST['email'], $_POST['code'])) {
    http_response_code(400);
    echo json_encode(['stat' => false, 'message' => 'Email and code required']);
    exit;
}
$email = $_POST['email'];
$code = $_POST['code'];

$stmt = $pdo->prepare('SELECT code, expires FROM users WHERE email = ?');
$stmt->execute([$email]);
$row = $stmt->fetch();
if (!$row) {
    http_response_code(409);
    echo json_encode(['stat' => false, 'message' => 'User not found']);
    exit;
}
if ($row['code'] !== $code) {
    http_response_code(401);
    echo json_encode(['stat' => false, 'message' => 'Invalid code']);
    exit;
}
if (time() > $row['expires']) {
    http_response_code(410);
    echo json_encode(['stat' => false, 'message' => 'Code expired']);
    exit;
}
echo json_encode(['stat' => true, 'message' => 'Code verified']);
?>