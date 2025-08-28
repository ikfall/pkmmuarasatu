<?php
echo "Memulai tes koneksi lengkap...<br>";

// Mengaktifkan error reporting untuk melihat semua masalah
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Langkah 1: Mencoba me-load file koneksi
require_once '../db_connect.php';
echo "Langkah 1: File db_connect.php berhasil di-load.<br>";

// Langkah 2: Memeriksa apakah koneksi ke database berhasil dibuat
if (isset($conn) && $conn instanceof PDO) {
    echo "✅ <strong>Langkah 2: SUKSES! Koneksi ke database berhasil dibuat.</strong>";
} else {
    echo "❌ <strong>Langkah 2: GAGAL! Koneksi ke database TIDAK berhasil dibuat.</strong> Periksa detail di db_connect.php atau pastikan server MySQL Anda berjalan.";
}