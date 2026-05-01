<?php
// api/lessee/get_machinery.php
require_once __DIR__ . '/../db_connect.php';
$pdo = $conn;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$stmt = $pdo->prepare("
    SELECT m.id, m.model_name, m.category, m.hourly_rate, m.image_url,
           m.description, m.serial_number, m.status,
           u.full_name AS owner_name
    FROM machines m
    JOIN users u ON m.owner_id = u.id
    WHERE m.status = 'Available'
    ORDER BY m.id DESC
");
$stmt->execute();
$machines = $stmt->fetchAll();

echo json_encode(['success' => true, 'data' => $machines]);