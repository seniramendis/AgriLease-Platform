<?php
header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

// In a real app, we use sessions. For now, we'll accept the admin_id from the frontend request.
if (isset($data->id) && isset($data->admin_id)) {
    try {
        $conn->beginTransaction();

        // 1. Update the target user to Banned
        $stmt = $conn->prepare("UPDATE users SET status = 'Banned' WHERE id = ?");
        $stmt->execute([$data->id]);

        // 2. Record the action using the REAL admin_id from your session
        $action = "BAN_USER";
        $logStmt = $conn->prepare("INSERT INTO audit_logs (admin_id, action, target_user_id) VALUES (?, ?, ?)");
        $logStmt->execute([$data->admin_id, $action, $data->id]);

        $conn->commit();
        echo json_encode(["success" => true, "message" => "User banned and log recorded."]);

    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing required IDs."]);
}
?>