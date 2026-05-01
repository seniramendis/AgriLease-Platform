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
$final_reading = $data['final_reading'] ?? null;

if (!$booking_id || $final_reading === null || $final_reading === '') {
    echo json_encode(["success" => false, "message" => "Booking ID and final reading are required."]);
    exit;
}

try {
    // Verify booking belongs to this driver and is In Progress
    $stmt = $conn->prepare("SELECT b.id, b.total_amount, jc.initial_reading FROM bookings b JOIN job_cards jc ON jc.booking_id = b.id WHERE b.id = ? AND b.driver_id = ? AND b.status = 'In Progress'");
    $stmt->execute([$booking_id, $driver_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo json_encode(["success" => false, "message" => "Active booking not found."]);
        exit;
    }

    if ((float)$final_reading <= (float)$booking['initial_reading']) {
        echo json_encode(["success" => false, "message" => "Final reading must be greater than initial reading."]);
        exit;
    }

    // Update job card with final reading
    $stmt = $conn->prepare("UPDATE job_cards SET final_reading = ?, end_time = NOW() WHERE booking_id = ?");
    $stmt->execute([$final_reading, $booking_id]);

    // Update booking status to Completed
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Completed', end_date = NOW() WHERE id = ?");
    $stmt->execute([$booking_id]);

    // Create payment record (80% to driver/lessor, 20% platform fee)
    $total = (float)$booking['total_amount'];
    $platform_fee = $total * 0.20;
    $lessor_payout = $total * 0.80;

    $stmt = $conn->prepare("INSERT INTO payments (booking_id, total_amount, platform_fee, lessor_payout, status) VALUES (?, ?, ?, ?, 'Held')");
    $stmt->execute([$booking_id, $total, $platform_fee, $lessor_payout]);

    echo json_encode(["success" => true, "message" => "Job completed successfully!", "amount" => $lessor_payout]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
