<?php
require 'db_connect.php';
$userId = $_GET['user_id'];

// Get total spent
$stmtSpent = $conn->prepare("SELECT SUM(total_amount) as total FROM bookings WHERE lessee_id = ? AND status = 'Completed'");
$stmtSpent->execute([$userId]);
$spent = $stmtSpent->fetchColumn() ?: 0;

// Get active count
$stmtActive = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE lessee_id = ? AND status = 'Active'");
$stmtActive->execute([$userId]);
$active = $stmtActive->fetchColumn();

echo json_encode([
    "success" => true,
    "data" => [
        "total_spent" => $spent,
        "active_count" => $active,
        "alerts" => 0
    ]
]);
?>