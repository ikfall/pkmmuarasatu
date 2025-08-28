<?php
require_once '../db_connect.php';

header('Content-Type: application/json');

$stmt = $conn->query("SELECT hari, poli, dokter, jam, status FROM jadwal");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$jadwal = [];
foreach ($results as $row) {
    $jadwal[$row['hari']][] = $row;
}

echo json_encode($jadwal);
?>