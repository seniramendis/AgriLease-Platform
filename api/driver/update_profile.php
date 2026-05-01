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

$full_name = trim($data['full_name'] ?? '');
$phone     = trim($data['phone'] ?? '');
$district  = trim($data['district'] ?? '');

if (empty($full_name)) {
    echo json_encode(["success" => false, "message" => "Full name is required."]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, district = ? WHERE id = ? AND role = 'Driver'");
    $stmt->execute([$full_name, $phone, $district, $driver_id]);

    $_SESSION['full_name'] = $full_name;

    echo json_encode(["success" => true, "message" => "Profile updated successfully."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
