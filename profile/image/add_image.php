<?php
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'] . '/db.php';

// Check if user_id and image are set
if (!isset($_POST['user_id'], $_FILES['image'])) {
    echo json_encode(['stat' => false, 'message' => 'Bad request']);
    exit;
}
$user_id = $_POST['user_id'];
$image = $_FILES['image'];
$image_name = uniqid();

//insert image url into database
try {
    $stmt = $pdo->prepare("INSERT INTO images (user_id,url) VALUES (?,?)");
    $stmt->execute([$user_id, $images_folder_path . $image_name]);
} catch (\Throwable $th) {
    echo json_encode(['stat' => false, 'message' => 'Failed to insert image']);
    exit;
}


//move image to images folder
if (!is_dir($images_folder_dir) || !move_uploaded_file($image['tmp_name'], $images_folder_dir . $image_name)) {
    $stmt = $pdo->prepare("DELETE FROM images WHERE url=?");
    $stmt->execute([$images_folder_path . $image_name]);
    echo json_encode(['stat' => false, 'message' => 'Failed to save image']);
    exit;
}

echo json_encode(['stat' => true, 'message' => $images_folder_path . $image_name]);

