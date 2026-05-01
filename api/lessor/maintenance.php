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
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Stats
    $stmt = $pdo->prepare("SELECT COUNT(*) as active FROM machines WHERE owner_id = ? AND status = 'Maintenance'");
    $stmt->execute([$lessor_id]);
    $active = (int)$stmt->fetch()['active'];

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as scheduled FROM maintenance_logs ml
        JOIN machines m ON ml.machine_id = m.id
        WHERE m.owner_id = ? AND ml.status = 'Scheduled'
    ");
    $stmt->execute([$lessor_id]);
    $scheduled = (int)$stmt->fetch()['scheduled'];

    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(ml.cost), 0) as total_cost FROM maintenance_logs ml
        JOIN machines m ON ml.machine_id = m.id
        WHERE m.owner_id = ? AND YEAR(ml.service_date) = YEAR(NOW())
    ");
    $stmt->execute([$lessor_id]);
    $totalCost = (float)$stmt->fetch()['total_cost'];

    // Service history
    $stmt = $pdo->prepare("
        SELECT ml.id, ml.service_type, ml.cost, ml.service_date, ml.status,
               m.model_name, m.category
        FROM maintenance_logs ml
        JOIN machines m ON ml.machine_id = m.id
        WHERE m.owner_id = ?
        ORDER BY ml.service_date DESC
    ");
    $stmt->execute([$lessor_id]);
    $logs = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => [
            'active_repairs'   => $active,
            'scheduled'        => $scheduled,
            'total_cost_ytd'   => $totalCost,
            'service_history'  => $logs,
        ]
    ]);

} elseif ($method === 'POST') {
    // Log new service
    $data = json_decode(file_get_contents('php://input'), true);
    $machine_id   = (int)($data['machine_id'] ?? 0);
    $service_type = $data['service_type'] ?? '';
    $cost         = (float)($data['cost'] ?? 0);
    $service_date = $data['service_date'] ?? date('Y-m-d');
    $status       = $data['status'] ?? 'Completed';

    if (!$machine_id || !$service_type || !$cost) {
        echo json_encode(['success' => false, 'message' => 'machine_id, service_type, and cost are required.']); exit;
    }

    // Verify machine belongs to lessor
    $check = $pdo->prepare("SELECT id FROM machines WHERE id = ? AND owner_id = ?");
    $check->execute([$machine_id, $lessor_id]);
    if (!$check->fetch()) { echo json_encode(['success' => false, 'message' => 'Machine not found.']); exit; }

    $stmt = $pdo->prepare("INSERT INTO maintenance_logs (machine_id, service_type, cost, service_date, status) VALUES (?,?,?,?,?)");
    $stmt->execute([$machine_id, $service_type, $cost, $service_date, $status]);

    // If status is Scheduled, mark machine as Maintenance
    if ($status === 'Scheduled') {
        $pdo->prepare("UPDATE machines SET status='Maintenance' WHERE id=?")->execute([$machine_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Service log added.', 'id' => $pdo->lastInsertId()]);
}
