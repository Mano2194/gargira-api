<?php
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'] . '/db.php';

if (!isset($_POST['user_id'], $_POST['image_url'])) {
    echo json_encode(['stat' => false, 'message' => 'user id and image url required']);
    exit;
}
$user_id = $_POST['user_id'];
$image_url = $_POST['image_url'];
$image_name = basename($image_url);

//check if image is master
$stmt = $pdo->prepare("SELECT master FROM images WHERE url=?");
$stmt->execute([$image_url]);
if ($stmt->rowCount() === 0) {
    echo json_encode(['stat' => false, 'message' => 'Image not found']);
    exit;
}
$master = $stmt->fetchColumn();

//delete image from database
$stmt = $pdo->prepare("DELETE FROM images WHERE url=?");
$stmt->execute([$image_url]);

//select any other image for this user to make it master if the deleted image was master
if ($master == 1) {
    $stmt = $pdo->prepare("SELECT url FROM images WHERE user_id=? LIMIT 1");
    $stmt->execute([$user_id]);
    if ($stmt->rowCount() > 0) {
        $new_master_image_url = $stmt->fetchColumn();
        //update the new master image
        $stmt = $pdo->prepare("UPDATE images SET master=1 WHERE url=?");
        $stmt->execute([$new_master_image_url]);
    }
}

//delete image file from server
$image_dir = $images_folder_dir . $image_name;
if (file_exists($image_dir)) {
    unlink($image_dir);
    echo json_encode(['stat' => true, 'message' => 'Image deleted successfully']);
} else {
    echo json_encode(['stat' => false, 'message' => 'Image file not found']);
}