<?php
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'] . '/db.php';

if (!isset($_POST['lat'], $_POST['lng'])) {
    echo json_encode(['stat' => false, 'message' => 'Bad request']);
    exit;
}
$lat = $_POST['lat'];
$lng = $_POST['lng'];

// if id is provided and user exists , update location in the database
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO lats_lngs (user_id, lat, lng) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE lat = ?, lng = ?, last_update = NOW()");
        $stmt->execute([$user_id, $lat, $lng, $lat, $lng]);
    } else {
        echo json_encode(['stat' => false, 'message' => 'user not found']);
        exit;
    }
}

// Haversine formula in SQL to calculate distance in kilometers using positional placeholders
$sql = "SELECT u.id,i.url,p.name,p.bio,p.age,p.instagram,(
    6371 * acos(
        cos(radians(?)) * cos(radians(l.lat)) *
        cos(radians(l.lng) - radians(?)) +
        sin(radians(?)) * sin(radians(l.lat))
    )
) AS distance
FROM users u
JOIN lats_lngs l ON u.id = l.user_id
JOIN profiles p ON u.id = p.user_id
LEFT JOIN images i ON u.id = i.user_id
WHERE l.last_update >= (NOW() - INTERVAL 10 MINUTE)
ORDER BY distance ASC
LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute([$lat, $lng, $lat]);
$users = $stmt->fetchAll();
echo json_encode(['stat' => true, 'users' => $users]);