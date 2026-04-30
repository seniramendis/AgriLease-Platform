<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

// We only care about the Email and Password!
if (isset($data->email) && isset($data->password)) {
    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Find the user by their email - Now also selecting 'status'
        $stmt = $conn->prepare("SELECT id, full_name, role, password_hash, status FROM users WHERE email = ?");
        $stmt->execute([$data->email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 1. Verify the user exists AND the encrypted password matches
        if ($user && password_verify($data->password, $user['password_hash'])) {

            // 2. REAL WORLD SECURITY: Check if the account is Banned
            // This prevents banned users from entering their dashboards
            if (isset($user['status']) && $user['status'] === 'Banned') {
                echo json_encode([
                    "success" => false,
                    "message" => "Access Denied: Your account has been suspended. Please contact support."
                ]);
                exit; // Stop the script here
            }

            // 3. Success! Send the REAL data from the database
            // 3. Success! Send the REAL data from the database
// ADDING: 'email' and 'phone' so the dashboard can unlock the NIC verification
            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "user" => [
                    "id" => $user['id'],
                    "name" => $user['full_name'],
                    "email" => $data->email, // This ensures the email is saved to localStorage
                    "role" => $user['role']
                ]
            ]);

        } else {
            echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Please enter your email and password."]);
}
?>