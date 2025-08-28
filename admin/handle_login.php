<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Akses ditolak');
}

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT username, password_hash FROM admin WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id();
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $user['username'];
    header('Location: dashboard.php');
} else {
    header('Location: login.php?error=1');
}
?>