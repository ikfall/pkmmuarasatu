<?php
require_once '../db_connect.php';

header('Content-Type: application/json');

$stmt = $conn->query("SELECT judul, tag, isi, tanggal FROM berita ORDER BY tanggal DESC LIMIT 6");
$berita = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($berita);
?>