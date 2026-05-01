<?php
header('Content-Type: application/json');
require 'db_connect.php';

try {
    // Fetch all users except passwords
    $stmt = $conn->query("SELECT id, full_name, email, role, DATE_FORMAT(created_at, '%b %d, %Y') as joined_date FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $users]);
} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>