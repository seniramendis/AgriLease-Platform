<?php
// api/lessor/fleet.php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require_once __DIR__ . '/../db_connect.php';
$pdo = $conn;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Lessor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
$lessor_id = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all machines for this lessor
    $stmt = $pdo->prepare("
        SELECT id, category, model_name, serial_number, hourly_rate, status,
               description, total_hours, next_service_date, image_url
        FROM machines WHERE owner_id = ? ORDER BY id DESC
    ");
    $stmt->execute([$lessor_id]);
    $machines = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $machines]);

} elseif ($method === 'POST') {
    // Add new machine
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;

    $required = ['category', 'model_name', 'serial_number', 'hourly_rate'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
            exit;
        }
    }

    // Check serial number uniqueness
    $check = $pdo->prepare("SELECT id FROM machines WHERE serial_number = ?");
    $check->execute([$data['serial_number']]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Serial number already exists.']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO machines (owner_id, category, model_name, serial_number, hourly_rate, description, status, next_service_date, image_url)
        VALUES (?, ?, ?, ?, ?, ?, 'Available', ?, ?)
    ");
    $stmt->execute([
        $lessor_id,
        $data['category'],
        $data['model_name'],
        $data['serial_number'],
        $data['hourly_rate'],
        $data['description'] ?? '',
        !empty($data['next_service_date']) ? $data['next_service_date'] : null,
        !empty($data['image_url']) ? $data['image_url'] : null,
    ]);
    $newId = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'message' => 'Machine registered successfully.', 'id' => $newId]);

} elseif ($method === 'PUT') {
    // Edit existing machine
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);
    if (!$id) { echo json_encode(['success' => false, 'message' => 'Machine ID required.']); exit; }

    // Verify ownership
    $check = $pdo->prepare("SELECT id FROM machines WHERE id = ? AND owner_id = ?");
    $check->execute([$id, $lessor_id]);
    if (!$check->fetch()) { echo json_encode(['success' => false, 'message' => 'Machine not found.']); exit; }

    $stmt = $pdo->prepare("
        UPDATE machines SET category=?, model_name=?, hourly_rate=?, status=?, description=?,
        total_hours=?, next_service_date=?, image_url=? WHERE id=? AND owner_id=?
    ");
    $stmt->execute([
        $data['category'],
        $data['model_name'],
        $data['hourly_rate'],
        $data['status'],
        $data['description'] ?? '',
        $data['total_hours'] ?? 0,
        !empty($data['next_service_date']) ? $data['next_service_date'] : null,
        !empty($data['image_url']) ? $data['image_url'] : null,
        $id,
        $lessor_id,
    ]);
    echo json_encode(['success' => true, 'message' => 'Machine updated successfully.']);

} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);

    $check = $pdo->prepare("SELECT id FROM machines WHERE id = ? AND owner_id = ?");
    $check->execute([$id, $lessor_id]);
    if (!$check->fetch()) { echo json_encode(['success' => false, 'message' => 'Machine not found.']); exit; }

    $pdo->prepare("DELETE FROM machines WHERE id = ? AND owner_id = ?")->execute([$id, $lessor_id]);
    echo json_encode(['success' => true, 'message' => 'Machine deleted.']);
}
