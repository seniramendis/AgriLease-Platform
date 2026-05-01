<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require_once __DIR__ . '/../db_connect.php';
$pdo = $conn; // your db_connect uses $conn, our code uses $pdo

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Lessor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
$lessor_id = (int)$_SESSION['user_id'];

// Available balance (Released payments)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(p.lessor_payout), 0) as available
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN machines m ON b.machine_id = m.id
    WHERE m.owner_id = ? AND p.status = 'Released'
");
$stmt->execute([$lessor_id]);
$available = $stmt->fetch()['available'];

// Pending in escrow (Held payments)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(p.lessor_payout), 0) as escrow
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN machines m ON b.machine_id = m.id
    WHERE m.owner_id = ? AND p.status = 'Held'
");
$stmt->execute([$lessor_id]);
$escrow = $stmt->fetch()['escrow'];

// Payout history
$stmt = $pdo->prepare("
    SELECT p.id, p.total_amount, p.lessor_payout, p.platform_fee, p.status, p.created_at,
           m.model_name, m.category,
           b.id as booking_id
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN machines m ON b.machine_id = m.id
    WHERE m.owner_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$lessor_id]);
$history = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data' => [
        'available_balance' => (float)$available,
        'escrow_balance'    => (float)$escrow,
        'payout_history'    => $history,
    ]
]);
