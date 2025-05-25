<?php
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'] . '/db.php';

if (!isset($_POST['user_id'])) {
    echo json_encode(['stat' => false, 'message' => 'user id required']);
    exit;
}
// Check if user exists
$user_id = $_POST['user_id'];
$stmt = $pdo->prepare("SELECT id FROM users WHERE id=?");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    echo json_encode(['stat' => false, 'message' => 'user not found']);
    exit;
}

$inputs = false;
// add or update profile information
if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $stmt = $pdo->prepare('INSERT INTO profiles (user_id, name) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE name = ?');
    $stmt->execute([$user_id, $name, $name]);
    $inputs = true;
}
if (isset($_POST['bio'])) {
    $bio = $_POST['bio'];
    $stmt = $pdo->prepare('INSERT INTO profiles (user_id, bio) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE bio = ?');
    $stmt->execute([$user_id, $bio, $bio]);
    $inputs = true;
}
if (isset($_POST['age'])) {
    $age = $_POST['age'];
    $stmt = $pdo->prepare('INSERT INTO profiles (user_id, age) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE age = ?');
    $stmt->execute([$user_id, $age, $age]);
    $inputs = true;
}
if ($inputs) {
    echo json_encode(['stat' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['stat' => false, 'message' => 'No profile information provided']);
}

