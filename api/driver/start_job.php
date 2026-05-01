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
$booking_id = $data['booking_id'] ?? null;
$initial_reading = $data['initial_reading'] ?? null;

if (!$booking_id || $initial_reading === null || $initial_reading === '') {
    echo json_encode(["success" => false, "message" => "Booking ID and initial reading are required."]);
    exit;
}

try {
    // Verify booking belongs to this driver
    $stmt = $conn->prepare("SELECT id, status FROM bookings WHERE id = ? AND driver_id = ? AND status = 'Pending'");
    $stmt->execute([$booking_id, $driver_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo json_encode(["success" => false, "message" => "Booking not found or not in Pending status."]);
        exit;
    }

    // Check if job card already exists
    $stmt = $conn->prepare("SELECT id FROM job_cards WHERE booking_id = ?");
    $stmt->execute([$booking_id]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Job card already exists for this booking."]);
        exit;
    }

    // Create job card
    $stmt = $conn->prepare("INSERT INTO job_cards (booking_id, driver_id, initial_reading, start_time) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$booking_id, $driver_id, $initial_reading]);

    // Update booking status to In Progress
    $stmt = $conn->prepare("UPDATE bookings SET status = 'In Progress', start_date = NOW() WHERE id = ?");
    $stmt->execute([$booking_id]);

    echo json_encode(["success" => true, "message" => "Job started successfully."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
