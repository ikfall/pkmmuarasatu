<?php
// Memanggil file koneksi database
require_once '../db_connect.php';

// Atur header output sebagai JSON
header('Content-Type: application/json');

try {
    // 1. Query untuk mengambil SEMUA data dari tabel poli
    // Pastikan nama kolom (nama, deskripsi, gambar_url, icon) sesuai dengan yang ada di database Anda
    $stmt = $conn->query("SELECT nama, kategori,deskripsi, gambar_url, icon FROM poli ORDER BY nama ASC");
    
    // 2. Ambil semua baris hasil sebagai array asosiatif
    $polis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Kirim hasil sebagai JSON
    // Jika tidak ada data, ini akan mengirimkan array kosong: []
    echo json_encode($polis);
    
} catch (PDOException $e) {
    // Jika terjadi error pada database, kirim pesan error
    http_response_code(500); // Set status code ke 500 (Internal Server Error)
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>