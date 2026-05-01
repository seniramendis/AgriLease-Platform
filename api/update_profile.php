<?php
header('Content-Type: application/json');
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID missing']);
    exit;
}

try {
    // Dynamically build update query based on which fields were sent
    $fields = [];
    $params = [];
    
    if (isset($data['full_name'])) { $fields[] = "full_name = ?"; $params[] = $data['full_name']; }
    if (isset($data['phone'])) { $fields[] = "phone = ?"; $params[] = $data['phone']; }
    if (isset($data['district'])) { $fields[] = "district = ?"; $params[] = $data['district']; }
    if (isset($data['nic_number'])) { $fields[] = "nic_number = ?"; $params[] = $data['nic_number']; }

    $params[] = $data['id'];
    $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>