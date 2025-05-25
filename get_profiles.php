<?php
header('Content-Type: application/json');
require 'db.php';

if (!isset($_POST['lat'], $_POST['lng'])) {
    http_response_code(400);
    echo json_encode(['stat' => false, 'message' => 'lat and lng required']);
    exit;
}
$lat = $_POST['lat'];
$lng = $_POST['lng'];

// if id is provided and user exists , update location in the database
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        $stmt = $pdo->prepare("INSERT INTO lats_lngs (user_id, lat, lng) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE lat = ?, lng = ?, last_update = NOW()");
        $stmt->execute([$id, $lat, $lng, $lat, $lng]);
    } else {
        http_response_code(402);
        echo json_encode(['stat' => false, 'message' => 'user not found']);
        exit;
    }
}

// Haversine formula in SQL to calculate distance in kilometers using positional placeholders
$sql = "SELECT u.id,u.email, l.lat, l.lng, (
    6371 * acos(
        cos(radians(?)) * cos(radians(l.lat)) *
        cos(radians(l.lng) - radians(?)) +
        sin(radians(?)) * sin(radians(l.lat))
    )
) AS distance
FROM users u
JOIN lats_lngs l ON u.id = l.user_id
WHERE l.last_update >= (NOW() - INTERVAL 10 MINUTE)
ORDER BY distance ASC
LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute([$lat, $lng, $lat]);
$users = $stmt->fetchAll();
echo json_encode(['stat' => true, 'users' => $users]);
?>