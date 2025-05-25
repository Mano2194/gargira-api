<?php
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'] . '/db.php';

if (!isset($_POST['user_id'], $_FILES['image'])) {
    echo json_encode(['stat' => false, 'message' => 'user id and image required']);
    exit;
}

$user_id = $_POST['user_id'];
$image = $_FILES['image'];
$master = isset($_POST['master']) ? $_POST['master'] : 0;
$image_name = uniqid();

//check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id=?");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    echo json_encode(['stat' => false, 'message' => 'user not found']);
    exit;
}
$stmt = $pdo->prepare("INSERT INTO images (user_id,url,master) VALUES (?,?,?)");
$stmt->execute([$user_id, $images_folder_path . $image_name, $master]);
// Check if the insert was successful
if ($stmt->rowCount() > 0) {
    // Ensure the directory exists
    if (!move_uploaded_file($image['tmp_name'], $images_folder_dir . $image_name)) {
        echo json_encode(['stat' => false, 'message' => 'Failed to save image']);
        exit;
    }
    echo json_encode(['stat' => true, 'path' => $images_folder_path . $image_name]);
} else {
    echo json_encode(['stat' => false, 'message' => 'Failed to insert image']);
}