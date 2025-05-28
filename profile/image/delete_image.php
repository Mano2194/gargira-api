<?php
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'] . '/db.php';

// Check if image url is set
if (!isset($_POST['image_url'])) {
    echo json_encode(['stat' => false, 'message' => 'Bad request']);
    exit;
}
$image_url = $_POST['image_url'];

//delete image from database
$stmt = $pdo->prepare("DELETE FROM images WHERE url=?");
$stmt->execute([$image_url]);

//delete image file from server
$deleted = false;
$image_name = basename($image_url);
$image_dir = $images_folder_dir . $image_name;
if (file_exists($image_dir)) {
    unlink($image_dir);
    $deleted = true;
}

// echo response
if ($stmt->rowCount() > 0 || $deleted) {
    echo json_encode(['stat' => true, 'message' => 'Image deleted successfully']);
} else {
    echo json_encode(['stat' => false, 'message' => 'Image not found']);
}

