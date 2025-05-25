<?php
header('Content-Type: application/json');
require '../db.php';

if (!isset($_POST['email'])) {
    http_response_code(400);
    echo json_encode(['stat' => false, 'message' => 'Email required']);
    exit;
}

$email = $_POST['email'];
$code = rand(111111, 999999); // Generate a random 6-digit code
$expires = time() + 600; //genrate expiration time (10 minutes from now)

// Update existing code if user exists
$stmt = $pdo->prepare('UPDATE users SET code = ?, expires = ? WHERE email = ?');
$stmt->execute([$code, $expires, $email]);
if ($stmt->rowCount() === 0) {
    http_response_code(500);
    echo json_encode(['stat' => false, 'message' => 'User not found']);
    exit;
}
// Send code to email (simple mail)
mail($email, 'Your Verification Code', "Your code is: $code");
echo json_encode(['stat' => true, 'message' => 'Verification code sent']);
?>