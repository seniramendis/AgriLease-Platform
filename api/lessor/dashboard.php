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

// Total revenue (sum of released payments for this lessor's machines)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(p.lessor_payout), 0) as total_revenue
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN machines m ON b.machine_id = m.id
    WHERE m.owner_id = ? AND p.status = 'Released'
");
$stmt->execute([$lessor_id]);
$revenue = $stmt->fetch();

// Active rentals (bookings In Progress)
$stmt = $pdo->prepare("
    SELECT COUNT(*) as active_rentals
    FROM bookings b
    JOIN machines m ON b.machine_id = m.id
    WHERE m.owner_id = ? AND b.status = 'In Progress'
");
$stmt->execute([$lessor_id]);
$activeRentals = $stmt->fetch();

// Pending maintenance alerts (machines in Maintenance or overdue service)
$stmt = $pdo->prepare("
    SELECT COUNT(*) as maintenance_alerts
    FROM machines
    WHERE owner_id = ? AND status = 'Maintenance'
");
$stmt->execute([$lessor_id]);
$maintenance = $stmt->fetch();

// Overdue logs
$stmt = $pdo->prepare("
    SELECT COUNT(*) as overdue_count
    FROM maintenance_logs ml
    JOIN machines m ON ml.machine_id = m.id
    WHERE m.owner_id = ? AND ml.status = 'Overdue'
");
$stmt->execute([$lessor_id]);
$overdue = $stmt->fetch();

// Fleet summary (top 5 machines)
$stmt = $pdo->prepare("
    SELECT id, category, model_name, serial_number, status, total_hours, next_service_date
    FROM machines WHERE owner_id = ? ORDER BY id DESC LIMIT 5
");
$stmt->execute([$lessor_id]);
$fleet = $stmt->fetchAll();

// Pending bookings count
$stmt = $pdo->prepare("
    SELECT COUNT(*) as pending_bookings
    FROM bookings b
    JOIN machines m ON b.machine_id = m.id
    WHERE m.owner_id = ? AND b.status = 'Pending'
");
$stmt->execute([$lessor_id]);
$pending = $stmt->fetch();

echo json_encode([
    'success' => true,
    'data' => [
        'total_revenue'      => (float)$revenue['total_revenue'],
        'active_rentals'     => (int)$activeRentals['active_rentals'],
        'maintenance_alerts' => (int)$maintenance['maintenance_alerts'],
        'overdue_count'      => (int)$overdue['overdue_count'],
        'pending_bookings'   => (int)$pending['pending_bookings'],
        'fleet_preview'      => $fleet,
    ]
]);
