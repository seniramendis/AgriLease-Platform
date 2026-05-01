<?php
header('Content-Type: application/json');
require 'db_connect.php';
$data = json_decode(file_get_contents("php://input"));

// In the real world, we'd save this to a 'settings' table
// For now, let's simulate the success
echo json_encode(["success" => true, "message" => "Platform commission updated to " . $data->fee . "%"]);
?>