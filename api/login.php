<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// ✅ FIX: Start session BEFORE any output
session_start();

require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

// 1. Verify we received email and password first
if (isset($data->email) && isset($data->password)) {
    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Fetch the user from the database
        $stmt = $conn->prepare("SELECT id, full_name, role, password_hash, status FROM users WHERE email = ?");
        $stmt->execute([$data->email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Setup Role Match logic AFTER the user is fetched
        $roleMatch = false;
        if ($user && isset($data->role)) {
            $roleMatch = (strtolower($user['role']) === strtolower($data->role)) || ($user['role'] === 'Admin');
        }

        // 4. Check if user exists, password is correct, and role matches
        if ($user && password_verify($data->password, $user['password_hash']) && $roleMatch) {

            // Check if banned
            if (isset($user['status']) && $user['status'] === 'Banned') {
                echo json_encode([
                    "success" => false,
                    "message" => "Access Denied: Your account has been suspended. Please contact support."
                ]);
                exit;
            }

            // ✅ FIX: Save user to PHP session so backend APIs can verify who is logged in
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];

            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "user" => [
                    "id" => $user['id'],
                    "name" => $user['full_name'],
                    "email" => $data->email,
                    "role" => $user['role'] // Returns the actual DB role, useful if they logged in as Admin
                ]
            ]);

        } else {
            echo json_encode(["success" => false, "message" => "Invalid credentials or role mismatch. Please select the correct role."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Please enter your email and password."]);
}
?>