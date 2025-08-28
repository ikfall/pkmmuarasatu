<?php
// FILE: admin/debug.php
// Alat untuk memeriksa masalah login

// Mengatur agar semua error ditampilkan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo '<pre style="font-family: monospace; border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;">';
echo '<h1>Hasil Pengecekan Login</h1><hr>';

// --- 1. MEMERIKSA KONEKSI DATABASE ---
echo "<h2>1. Cek Koneksi (db_connect.php)</h2>";
try {
    require_once '../db_connect.php';
    echo "✅ SUKSES: Berhasil terhubung ke database.<br>";
} catch (PDOException $e) {
    echo "❌ GAGAL: Tidak bisa terhubung ke database.<br>";
    echo "Pesan Error: " . $e->getMessage() . "<br>";
    echo "SOLUSI: Periksa kembali file 'db_connect.php'. Pastikan nama database, username ('root'), dan password ('') sudah benar.";
    echo '</pre>';
    exit; // Berhenti di sini jika koneksi gagal
}
echo "<hr>";


// --- 2. MEMERIKSA DATA ADMIN DI DATABASE ---
echo "<h2>2. Cek Data di Tabel 'admin'</h2>";
$username_dicari = 'admin';
$stmt = $conn->prepare("SELECT username, password_hash FROM admin WHERE username = ?");
$stmt->execute([$username_dicari]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "✅ SUKSES: User dengan username '{$user['username']}' ditemukan.<br>";
    echo "Isi password_hash di database:<br>";
    echo "<strong style='color: blue;'>" . htmlspecialchars($user['password_hash']) . "</strong><br>";
} else {
    echo "❌ GAGAL: User dengan username '{$username_dicari}' TIDAK DITEMUKAN di database.<br>";
    echo "SOLUSI: Jalankan kembali perintah SQL INSERT untuk membuat user admin.";
    echo '</pre>';
    exit; // Berhenti jika user tidak ada
}
echo "<hr>";


// --- 3. MEMVERIFIKASI PASSWORD ---
echo "<h2>3. Cek Kecocokan Password</h2>";
$password_yang_dicoba = 'adminpkm';
echo "Mencoba mencocokkan password: '{$password_yang_dicoba}'<br>";
echo "dengan hash dari database...<br><br>";

if (password_verify($password_yang_dicoba, $user['password_hash'])) {
    echo "<strong style='font-size: 1.2em; color: green;'>✅ SUKSES: Password COCOK!</strong><br>";
    echo "Seharusnya Anda sudah bisa login sekarang. Jika masih gagal, mungkin ada masalah cache di browser atau salah ketik saat login.";
} else {
    echo "<strong style='font-size: 1.2em; color: red;'>❌ GAGAL: Password TIDAK COCOK!</strong><br>";
    echo "SOLUSI: Hash di database tidak sesuai dengan password '{$password_yang_dicoba}'. Anda HARUS menjalankan kembali perintah SQL DELETE dan INSERT yang saya berikan sebelumnya untuk mereset password di database.";
}

echo '</pre>';
?>