<?php
// api/lessor/bookings.php

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
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all bookings for this lessor's machines
    $stmt = $pdo->prepare("
        SELECT b.id, b.status, b.start_date, b.end_date, b.total_amount, b.created_at,
               m.model_name, m.category, m.serial_number,
               u.full_name as lessee_name, u.phone as lessee_phone
        FROM bookings b
        JOIN machines m ON b.machine_id = m.id
        JOIN users u ON b.lessee_id = u.id
        WHERE m.owner_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$lessor_id]);
    $bookings = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $bookings]);

} elseif ($method === 'PUT') {
    // Update booking status (Accept / Cancel)
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = (int)($data['id'] ?? 0);
    $new_status = $data['status'] ?? '';

    $allowed = ['In Progress', 'Completed', 'Cancelled'];
    if (!in_array($new_status, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status.']); exit;
    }

    // Verify booking belongs to this lessor
    $check = $pdo->prepare("
        SELECT b.id FROM bookings b
        JOIN machines m ON b.machine_id = m.id
        WHERE b.id = ? AND m.owner_id = ?
    ");
    $check->execute([$booking_id, $lessor_id]);
    if (!$check->fetch()) { echo json_encode(['success' => false, 'message' => 'Booking not found.']); exit; }

    $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$new_status, $booking_id]);

    // If completed, release payment
    if ($new_status === 'Completed') {
        $pay = $pdo->prepare("UPDATE payments SET status = 'Released' WHERE booking_id = ?");
        $pay->execute([$booking_id]);
    }

    echo json_encode(['success' => true, 'message' => "Booking updated to '$new_status'."]);
}
