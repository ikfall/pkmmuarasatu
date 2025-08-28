<?php
require_once '../db_connect.php';
header('Content-Type: application/json');

// Ambil 8 gambar terbaru
$stmt = $conn->query("SELECT gambar_path, deskripsi FROM galeri ORDER BY tanggal_upload DESC LIMIT 8");
$galeri = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($galeri);
?>