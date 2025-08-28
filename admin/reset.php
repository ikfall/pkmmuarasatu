<?php
// FILE: admin/reset_password.php
// Alat untuk mereset password admin secara paksa

require_once '../db_connect.php';

$username_target = 'admin';
$password_baru = 'adminpkm';

echo '<pre style="font-family: monospace; border: 1px solid #ccc; padding: 15px;">';
echo '<h1>Hasil Reset Password</h1><hr>';

try {
    // 1. Buat hash baru dari password yang kita inginkan
    $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
    echo "Membuat hash baru untuk password '{$password_baru}'... Selesai.<br>";

    // 2. Hapus user lama untuk memastikan kebersihan
    $stmt_delete = $conn->prepare("DELETE FROM admin WHERE username = ?");
    $stmt_delete->execute([$username_target]);
    echo "Menghapus user '{$username_target}' yang lama... Selesai.<br>";

    // 3. Masukkan kembali user dengan hash yang baru dan 100% benar
    $stmt_insert = $conn->prepare("INSERT INTO admin (username, password_hash) VALUES (?, ?)");
    $stmt_insert->execute([$username_target, $hash_baru]);
    echo "Membuat ulang user '{$username_target}' dengan hash baru... Selesai.<br><hr>";

    echo "<strong style='font-size: 1.2em; color: green;'>✅ BERHASIL!</strong><br>";
    echo "Password untuk user '{$username_target}' telah direset menjadi '{$password_baru}'.<br>";
    echo "Silakan coba login sekarang.";

} catch (PDOException $e) {
    echo "<strong style='font-size: 1.2em; color: red;'>❌ GAGAL!</strong><br>";
    echo "Terjadi error database: " . $e->getMessage();
}

echo '</pre>';
?>