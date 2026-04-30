<?php
// api/db_connect.php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "agrilease_db";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}
?>