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
$data = json_decode(file_get_contents("php://input"), true);

$category    = $data['category']    ?? '';
$booking_ref = $data['booking_ref'] ?? '';
$description = trim($data['description'] ?? '');

if (empty($description)) {
    echo json_encode(["success" => false, "message" => "Description is required."]);
    exit;
}

// Map category to enum
$cat_map = [
    'Machine Breakdown' => 'Machine Breakdown',
    'Client Dispute'    => 'Client Dispute',
    'App / Payment Issue' => 'Payment Issue',
];
$db_category = $cat_map[$category] ?? 'Other';

try {
    $booking_id = null;

    if (!empty($booking_ref)) {
        // Extract numeric ID from #AG-XXXXXX format
        $clean = preg_replace('/[^0-9]/', '', $booking_ref);
        if ($clean) {
            $stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND driver_id = ?");
            $stmt->execute([$clean, $driver_id]);
            $b = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($b) {
                $booking_id = $b['id'];
            }
        }
    }

    // If no booking_id found, use most recent booking for this driver
    if (!$booking_id) {
        $stmt = $conn->prepare("SELECT id FROM bookings WHERE driver_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$driver_id]);
        $b = $stmt->fetch(PDO::FETCH_ASSOC);
        $booking_id = $b ? $b['id'] : null;
    }

    if (!$booking_id) {
        echo json_encode(["success" => false, "message" => "No bookings found. Cannot submit a ticket without a related booking."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO disputes (booking_id, reported_by, category, description, severity, status) VALUES (?, ?, ?, ?, 'Medium', 'Unresolved')");
    $stmt->execute([$booking_id, $driver_id, $db_category, $description]);

    echo json_encode(["success" => true, "message" => "Ticket submitted successfully. Our team will contact you shortly."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
