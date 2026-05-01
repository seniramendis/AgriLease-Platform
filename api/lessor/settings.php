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
    $stmt = $pdo->prepare("SELECT id, full_name, email, phone, district FROM users WHERE id = ?");
    $stmt->execute([$lessor_id]);
    $user = $stmt->fetch();
    if (!$user) { echo json_encode(['success' => false, 'message' => 'User not found.']); exit; }
    echo json_encode(['success' => true, 'data' => $user]);

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $full_name = trim($data['full_name'] ?? '');
    $phone     = trim($data['phone'] ?? '');
    $district  = trim($data['district'] ?? '');

    if (!$full_name) { echo json_encode(['success' => false, 'message' => 'Full name is required.']); exit; }

    $stmt = $pdo->prepare("UPDATE users SET full_name=?, phone=?, district=? WHERE id=?");
    $stmt->execute([$full_name, $phone, $district, $lessor_id]);

    // Change password if provided
    if (!empty($data['new_password'])) {
        if (strlen($data['new_password']) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']); exit;
        }
        $hash = password_hash($data['new_password'], PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([$hash, $lessor_id]);
    }

    // Update session name
    $_SESSION['full_name'] = $full_name;

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
}
