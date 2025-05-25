<?php
header('Content-Type: application/json');
require '../db.php';

if (!isset($_POST['email'], $_POST['password'])) {
    http_response_code(400);
    echo json_encode(['stat' => false, 'message' => 'Email and password required']);
    exit;
}
$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password'])) {
    $del = $pdo->prepare('DELETE FROM users WHERE id = ?');
    if ($del->execute([$user['id']])) {
        echo json_encode(['stat' => true, 'message' => 'Account deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['stat' => false, 'message' => 'Account deletion failed']);
    }
} else {
    http_response_code(401);
    echo json_encode(['stat' => false, 'message' => 'Invalid credentials']);
}
?>
