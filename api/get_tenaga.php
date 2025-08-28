<?php
require_once '../db_connect.php';
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

try {
    // --- Query Data ---
    $sql = "SELECT jenis, jumlah FROM tenaga_kesehatan ORDER BY jenis ASC";
    $stmt = $conn->query($sql);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Output JSON ---
    echo json_encode($data, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

// Tutup koneksi
$conn = null;
?>
