<?php
header('Content-Type: application/json');
include 'db_connect.php';

$lessee_id = $_GET['lessee_id'] ?? null;

if (!$lessee_id) {
    echo json_encode(['success' => false, 'message' => 'User ID missing']);
    exit;
}

try {
    // JOIN allows us to get machine names and owner names in one go
    $query = "SELECT b.*, m.model_name, m.category, m.image_url, u.full_name as owner_name 
              FROM bookings b
              JOIN machines m ON b.machine_id = m.id
              JOIN users u ON m.owner_id = u.id
              WHERE b.lessee_id = ?
              ORDER BY b.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute([$lessee_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $bookings]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>