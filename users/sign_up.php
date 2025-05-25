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

// Check if user exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['stat' => false, 'message' => 'Email already exists']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT); // Hash password
$verifie = true;

//send verification email if password != google or apple
if ($password != 'google' && $password != 'apple') {
    $verifie = rand(111111, 999999);// Generate a random 6-digit code
    mail($email, 'Your Verification Code', "Your code is: $verifie");
}

$stmt = $pdo->prepare('INSERT INTO users (email,verifie, password) VALUES (?, ?, ?)');
if ($stmt->execute([$email, $verifie, $hash,])) {
    echo json_encode(['stat' => true, 'message' => 'Registration successful']);
} else {
    http_response_code(500);
    echo json_encode(['stat' => false, 'message' => 'Registration failed']);
}
?>