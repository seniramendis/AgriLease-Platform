<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();
require_once "../db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Driver') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$driver_id = $_SESSION['user_id'];

try {
    // Total cleared (released payments)
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(p.lessor_payout * 0.5), 0) as total_cleared
        FROM payments p
        JOIN bookings b ON p.booking_id = b.id
        WHERE b.driver_id = ? AND p.status = 'Released'
    ");
    $stmt->execute([$driver_id]);
    $cleared = $stmt->fetch(PDO::FETCH_ASSOC);

    // Pending transfer (held payments)
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(p.lessor_payout * 0.5), 0) as total_pending
        FROM payments p
        JOIN bookings b ON p.booking_id = b.id
        WHERE b.driver_id = ? AND p.status = 'Held'
    ");
    $stmt->execute([$driver_id]);
    $pending = $stmt->fetch(PDO::FETCH_ASSOC);

    // Payout history
    $stmt = $conn->prepare("
        SELECT b.id as booking_id, b.end_date,
               jc.start_time, jc.end_time,
               m.model_name,
               p.lessor_payout * 0.5 as driver_amount,
               p.status,
               TIMESTAMPDIFF(MINUTE, jc.start_time, jc.end_time) / 60.0 as hours_logged
        FROM bookings b
        JOIN machines m ON b.machine_id = m.id
        LEFT JOIN job_cards jc ON jc.booking_id = b.id
        LEFT JOIN payments p ON p.booking_id = b.id
        WHERE b.driver_id = ? AND b.status = 'Completed'
        ORDER BY b.end_date DESC
    ");
    $stmt->execute([$driver_id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "total_cleared" => (float)$cleared['total_cleared'],
        "total_pending" => (float)$pending['total_pending'],
        "history" => $history
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
