<?php
header('Content-Type: application/json');
require 'db_connect.php';

// Check if an owner_id is provided to filter by a specific fleet
$owner_id = isset($_GET['owner_id']) ? $_GET['owner_id'] : null;

try {
    if ($owner_id) {
        // Query for the Lessor: Get all machinery owned by this specific user
        $sql = "SELECT * FROM machines WHERE owner_id = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$owner_id]);
    } else {
        // Query for the Lessee: Get all machinery currently marked as 'Available'
        $sql = "SELECT * FROM machines WHERE status = 'Available' ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    $machinery = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $machinery
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>