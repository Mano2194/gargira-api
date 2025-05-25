<?php
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'] . '/db.php';

if (!isset($_POST['user_id'], $_POST['url'])) {
    echo json_encode(['stat' => false, 'message' => 'user id and image url required']);
    exit;
}
$user_id = $_POST['user_id'];
$url = $_POST['url'];

// Check if user have images
$stmt = $pdo->prepare('SELECT url FROM images WHERE user_id = ?');
$stmt->execute([$user_id]);
if ($stmt->rowCount() === 0) {
    echo json_encode(['stat' => false, 'message' => 'No images found for user']);
    exit;
}

// Check if the provided URL exists in the user's images
$urls = $stmt->fetchAll();
foreach ($urls as $row) {
    if ($row['url'] == $url) {
        //update all images to not master
        $stmt = $pdo->prepare('UPDATE images SET master = 0 WHERE user_id = ?');
        $stmt->execute([$user_id]);
        // Set the new master image
        $stmt = $pdo->prepare('UPDATE images SET master = 1 WHERE user_id = ? AND url = ?');
        $stmt->execute([$user_id, $url]);
        echo json_encode(['stat' => true, 'message' => 'Master image updated successfully']);
        exit;
    }
}
echo json_encode(['stat' => false, 'message' => 'Failed Wrong image url']);