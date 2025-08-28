<?php
// --- GANTI DENGAN DETAIL DATABASE ANDA ---
$host = 'localhost';
$db_name = 'puskesmass'; // ganti dengan nama database Anda
$username = 'root'; // ganti dengan username database
$password = ''; // ganti dengan password database

try {
    $conn = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi Gagal: " . $e->getMessage());
}
?>