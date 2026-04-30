<?php
// api/lessee/cancel_booking.php
session_start();
require_once __DIR__ . '/../db_connect.php';
$pdo = $conn;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Lessee') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
$lessee_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST required']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true);
$booking_id = (int)($data['booking_id'] ?? 0);

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'booking_id required']);
    exit;
}

$check = $pdo->prepare("SELECT id, status FROM bookings WHERE id = ? AND lessee_id = ?");
$check->execute([$booking_id, $lessee_id]);
$booking = $check->fetch();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

if ($booking['status'] !== 'Pending') {
    echo json_encode(['success' => false, 'message' => 'Only Pending bookings can be cancelled']);
    exit;
}

$pdo->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?")->execute([$booking_id]);

try {
    $pdo->prepare("UPDATE payments SET status = 'Refunded' WHERE booking_id = ?")->execute([$booking_id]);
} catch (Exception $e) { /* non-fatal */ }

echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);