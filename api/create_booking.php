<?php
header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->machine_id) && isset($data->lessee_id) && isset($data->field_id)) {
    try {
        // Fetch machine rate for LKR calculation
        $mStmt = $conn->prepare("SELECT hourly_rate FROM machines WHERE id = ?");
        $mStmt->execute([$data->machine_id]);
        $rate = $mStmt->fetchColumn();

        // Business Logic: LKR 5,000 deposit + 4-hour rental estimate [cite: 154, 161]
        $total_to_hold = ($rate * 4) + 5000;

        // Mandatory columns: lessee_id, machine_id, field_id, driver_id, status, total_amount [cite: 398]
        $query = "INSERT INTO bookings (
                    lessee_id, 
                    machine_id, 
                    field_id, 
                    driver_id, 
                    status, 
                    total_amount, 
                    created_at
                  ) VALUES (?, ?, ?, 10, 'Pending', ?, NOW())";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            $data->lessee_id,
            $data->machine_id,
            $data->field_id,
            $total_to_hold
        ]);

        echo json_encode(["success" => true, "message" => "Booking Request Sent! LKR 5,000 deposit held in Escrow."]);

    } catch (PDOException $e) {
        // If this still fails, the error message here will tell us exactly which column is missing
        echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Incomplete request: Missing ID data."]);
}
?>