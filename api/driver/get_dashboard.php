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
    // Get driver info
    $stmt = $conn->prepare("SELECT id, full_name, email, phone, district FROM users WHERE id = ? AND role = 'Driver'");
    $stmt->execute([$driver_id]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get total earnings (from completed bookings where this driver was assigned)
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(b.total_amount * 0.80), 0) as total_earnings
        FROM bookings b 
        WHERE b.driver_id = ? AND b.status = 'Completed'
    ");
    $stmt->execute([$driver_id]);
    $earnings = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get jobs completed count
    $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM bookings WHERE driver_id = ? AND status = 'Completed'");
    $stmt->execute([$driver_id]);
    $completed = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get pending jobs count
    $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM bookings WHERE driver_id = ? AND status = 'Pending'");
    $stmt->execute([$driver_id]);
    $pending = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get average rating
    $stmt = $conn->prepare("SELECT COALESCE(AVG(rating), 0) as avg_rating FROM driver_ratings WHERE driver_id = ?");
    $stmt->execute([$driver_id]);
    $rating = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get current active booking (In Progress)
    $stmt = $conn->prepare("
        SELECT b.id, b.status, b.start_date, b.total_amount,
               m.model_name, m.category, m.image_url,
               u.full_name as lessee_name, u.phone as lessee_phone,
               f.field_name, f.address, f.latitude, f.longitude,
               jc.initial_reading, jc.final_reading, jc.start_time
        FROM bookings b
        JOIN machines m ON b.machine_id = m.id
        JOIN users u ON b.lessee_id = u.id
        JOIN fields f ON b.field_id = f.id
        LEFT JOIN job_cards jc ON jc.booking_id = b.id
        WHERE b.driver_id = ? AND b.status = 'In Progress'
        ORDER BY b.created_at DESC LIMIT 1
    ");
    $stmt->execute([$driver_id]);
    $active_booking = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get upcoming bookings (Pending)
    $stmt = $conn->prepare("
        SELECT b.id, b.status, b.start_date, b.total_amount,
               m.model_name, m.category,
               u.full_name as lessee_name,
               f.field_name, f.address
        FROM bookings b
        JOIN machines m ON b.machine_id = m.id
        JOIN users u ON b.lessee_id = u.id
        JOIN fields f ON b.field_id = f.id
        WHERE b.driver_id = ? AND b.status = 'Pending'
        ORDER BY b.start_date ASC LIMIT 5
    ");
    $stmt->execute([$driver_id]);
    $upcoming = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Weekly earnings (last 7 days)
    $stmt = $conn->prepare("
        SELECT DAYOFWEEK(b.end_date) as day_of_week,
               COALESCE(SUM(b.total_amount * 0.80), 0) as daily_earnings
        FROM bookings b
        WHERE b.driver_id = ? AND b.status = 'Completed'
        AND b.end_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DAYOFWEEK(b.end_date)
    ");
    $stmt->execute([$driver_id]);
    $weekly_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $weekly = array_fill(0, 7, 0);
    foreach ($weekly_raw as $row) {
        $idx = ($row['day_of_week'] + 5) % 7; // Convert to Mon=0
        $weekly[$idx] = (float)$row['daily_earnings'];
    }

    echo json_encode([
        "success" => true,
        "driver" => $driver,
        "stats" => [
            "total_earnings" => (float)$earnings['total_earnings'],
            "jobs_completed" => (int)$completed['completed'],
            "pending_jobs" => (int)$pending['pending'],
            "avg_rating" => round((float)$rating['avg_rating'], 1)
        ],
        "active_booking" => $active_booking,
        "upcoming" => $upcoming,
        "weekly_earnings" => $weekly
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
