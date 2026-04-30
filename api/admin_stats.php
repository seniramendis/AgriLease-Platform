<?php
header('Content-Type: application/json');
require 'db_connect.php';

try {
    // 1. Total Registered Users
    $users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // 2. Currently Active Leases (Live jobs only)
    $leases = 0;
    $checkTable = $conn->query("SHOW TABLES LIKE 'bookings'")->rowCount();
    if ($checkTable > 0) {
        $leases = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'Active'")->fetchColumn();
    }

    // 3. Financial Metrics (The "Real World" Money Split)
    $total_escrow = 0;
    $platform_revenue = 0; // Your 10% cut
    $lessor_payouts = 0;   // The 90% cut

    if ($checkTable > 0) {
        // Summing all money currently held by the platform
        $total_escrow = $conn->query("SELECT IFNULL(SUM(total_amount), 0.00) FROM bookings WHERE status IN ('Pending', 'Active')")->fetchColumn();

        // Real-world logic: Calculate your 10% platform fee from completed/active bookings
        $platform_revenue = $total_escrow * 0.10;
        $lessor_payouts = $total_escrow * 0.90;
    }

    // 4. Monthly Performance Data (Last 6 Months)
    $chartData = [];
    for ($i = 5; $i >= 0; $i--) {
        $monthName = date('M', strtotime("-$i months"));
        $monthNum = date('m', strtotime("-$i months"));
        $year = date('Y', strtotime("-$i months"));

        $val = 0;
        if ($checkTable > 0) {
            $query = "SELECT IFNULL(SUM(total_amount), 0) FROM bookings 
                      WHERE MONTH(created_at) = '$monthNum' AND YEAR(created_at) = '$year'";
            $val = $conn->query($query)->fetchColumn();
        }
        $chartData[] = ['month' => $monthName, 'value' => (float) $val];
    }

    echo json_encode([
        "success" => true,
        "data" => [
            "users" => (int) $users,
            "leases" => (int) $leases,
            "escrow_total" => number_format((float) $total_escrow, 2, '.', ''),
            "platform_profit" => number_format((float) $platform_revenue, 2, '.', ''),
            "lessor_owed" => number_format((float) $lessor_payouts, 2, '.', ''),
            "chart" => $chartData
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
}
?>