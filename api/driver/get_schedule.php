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
$month = $_GET['month'] ?? date('m');
$year  = $_GET['year']  ?? date('Y');

try {
    // All bookings for this driver in given month
    $stmt = $conn->prepare("
        SELECT b.id, b.status, b.start_date, b.end_date, b.total_amount,
               m.model_name, m.category,
               u.full_name as lessee_name, u.phone as lessee_phone,
               f.field_name, f.address
        FROM bookings b
        JOIN machines m ON b.machine_id = m.id
        JOIN users u ON b.lessee_id = u.id
        JOIN fields f ON b.field_id = f.id
        WHERE b.driver_id = ?
        AND (
            (MONTH(b.start_date) = ? AND YEAR(b.start_date) = ?)
            OR b.status = 'Pending'
        )
        ORDER BY b.start_date ASC
    ");
    $stmt->execute([$driver_id, $month, $year]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dates with jobs
    $busy_dates = [];
    foreach ($bookings as $b) {
        if ($b['start_date']) {
            $busy_dates[] = date('Y-m-d', strtotime($b['start_date']));
        }
    }

    echo json_encode([
        "success" => true,
        "bookings" => $bookings,
        "busy_dates" => array_unique($busy_dates)
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
