<?php
// api/lessee/lessee_stats.php
session_start();
require_once __DIR__ . '/../db_connect.php';
$pdo = $conn;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Lessee') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
$lessee_id = (int)$_SESSION['user_id'];

$spentStmt = $pdo->prepare("
    SELECT COALESCE(SUM(total_amount), 0) AS total_spent
    FROM bookings
    WHERE lessee_id = ? AND status = 'Completed'
");
$spentStmt->execute([$lessee_id]);
$spent = $spentStmt->fetch()['total_spent'];

$activeStmt = $pdo->prepare("
    SELECT COUNT(*) AS active_count
    FROM bookings
    WHERE lessee_id = ? AND status = 'In Progress'
");
$activeStmt->execute([$lessee_id]);
$active = $activeStmt->fetch()['active_count'];

$pendingStmt = $pdo->prepare("
    SELECT COUNT(*) AS pending_count
    FROM bookings
    WHERE lessee_id = ? AND status = 'Pending'
");
$pendingStmt->execute([$lessee_id]);
$pending = $pendingStmt->fetch()['pending_count'];

$currentStmt = $pdo->prepare("
    SELECT b.id, b.status, b.total_amount,
           m.model_name AS machine_name, m.image_url AS img,
           u.full_name AS driver_name
    FROM bookings b
    JOIN machines m ON b.machine_id = m.id
    JOIN users u ON m.owner_id = u.id
    WHERE b.lessee_id = ? AND b.status = 'In Progress'
    ORDER BY b.created_at DESC
    LIMIT 1
");
$currentStmt->execute([$lessee_id]);
$current = $currentStmt->fetch();

echo json_encode([
    'success' => true,
    'data' => [
        'total_spent'    => $spent,
        'active_count'   => $active,
        'alerts'         => $pending,
        'active_booking' => $current ?: null,
    ]
]);