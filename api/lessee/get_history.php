<?php
// api/lessee/get_history.php
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

$stmt = $pdo->prepare("
    SELECT b.id, b.status, b.total_amount, b.created_at,
           m.model_name AS machine_name, m.category, m.image_url,
           u.full_name AS owner_name
    FROM bookings b
    JOIN machines m ON b.machine_id = m.id
    JOIN users u ON m.owner_id = u.id
    WHERE b.lessee_id = ?
      AND b.status IN ('Completed', 'Cancelled')
    ORDER BY b.created_at DESC
");
$stmt->execute([$lessee_id]);
$history = $stmt->fetchAll();

echo json_encode(['success' => true, 'data' => $history]);