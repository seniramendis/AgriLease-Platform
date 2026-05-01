<?php
// Enable strict error reporting so nothing fails silently
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->name) && isset($data->email) && isset($data->password) && isset($data->role)) {
    try {
        // Force database connection to throw loud errors if something is wrong
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data->email]);
        if($stmt->rowCount() > 0) {
            echo json_encode(["success" => false, "message" => "Email already registered."]);
            exit;
        }

        // 2. Hash the password
        $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

        // 3. Insert into database
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $inserted = $stmt->execute([$data->name, $data->email, $hashed_password, $data->role]);
        
        // 4. GET THE NEW ID
        $new_id = $conn->lastInsertId();

        if ($inserted && $new_id > 0) {
            echo json_encode(["success" => true, "message" => "Success! Saved to Database with ID: " . $new_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to save to database. Execute returned false."]);
        }
        
    } catch(PDOException $e) {
        // This will catch missing tables, wrong column names, or syntax errors!
        echo json_encode(["success" => false, "message" => "MySQL Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Incomplete data provided."]);
}
?>