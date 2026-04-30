<?php
header('Content-Type: application/json');
include 'db_connect.php';

$lessee_id = $_GET['lessee_id'] ?? null;

try {
    // Only fetch jobs that are finished (Completed)
    $query = "SELECT b.*, m.model_name 
              FROM bookings b
              JOIN machines m ON b.machine_id = m.id
              WHERE b.lessee_id = ? AND b.status = 'Completed'
              ORDER BY b.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute([$lessee_id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $history]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>