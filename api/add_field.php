<?php
header('Content-Type: application/json');
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['lessee_id'], $data['latitude'], $data['longitude'])) {
    echo json_encode(['success' => false, 'message' => 'Missing location data']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO fields (lessee_id, field_name, latitude, longitude, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['lessee_id'],
        $data['field_name'],
        $data['latitude'],
        $data['longitude'],
        $data['address'] ?? 'Manual Entry'
    ]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>