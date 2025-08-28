<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../db_connect.php';

// Handle UPLOAD GAMBAR BARU
if (isset($_POST['upload'])) {
    if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['gambar_file'];
        $deskripsi = $_POST['deskripsi'];
        $upload_dir = 'Galeri/';
        
        $nama_file_unik = uniqid() . '_' . basename($file['name']);
        $target_file = $upload_dir . $nama_file_unik;
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // Batas 5 MB

        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO galeri (gambar_path, deskripsi) VALUES (?, ?)");
                $stmt->execute([$target_file, $deskripsi]);
            }
        }
    }
    header('Location: manage_galeri.php');
    exit;
}

// Handle HAPUS GAMBAR
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt_select = $conn->prepare("SELECT gambar_path FROM galeri WHERE id = ?");
    $stmt_select->execute([$id]);
    $gambar_untuk_dihapus = $stmt_select->fetchColumn();

    $stmt_delete = $conn->prepare("DELETE FROM galeri WHERE id = ?");
    $stmt_delete->execute([$id]);

    if ($gambar_untuk_dihapus && file_exists($gambar_untuk_dihapus)) {
        unlink($gambar_untuk_dihapus);
    }
    header('Location: manage_galeri.php');
    exit;
}

// Mengambil semua data galeri untuk ditampilkan
$all_gambar = $conn->query("SELECT * FROM galeri ORDER BY tanggal_upload DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-900: #0b3d2e; --green-700: #0f5c44; --green-600: #137a56;
            --green-500: #16a168; --green-100: #e9fff6; --text-dark: #0e2a22;
            --white: #ffffff; --shadow: 0 10px 25px rgba(19, 122, 86, 0.1);
            --radius: 18px;
        }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(180deg, #f7fffb 0%, #ffffff 40%); color: var(--text-dark); }
        .page-wrapper { max-width: 1200px; margin: 40px auto; padding: 30px; background-color: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title { font-family: 'Montserrat', sans-serif; font-weight: 700; color: var(--green-700); position: relative; display: inline-block; }
        .page-title:after { content: ""; display: block; width: 60%; height: 4px; margin-top: 8px; background: linear-gradient(90deg, #41c28c, var(--green-600)); border-radius: 2px; }
        .btn-secondary-outline { color: var(--green-700); background-color: transparent; border: 1px solid var(--green-600); border-radius: 12px; padding: 10px 22px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
        .btn-secondary-outline:hover { background-color: var(--green-100); color: var(--green-900); }
        .upload-section { padding: 25px; background-color: var(--green-100); border-radius: var(--radius); margin-bottom: 30px; border: 1px solid var(--green-300); }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .gallery-item { position: relative; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .gallery-item img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform 0.3s ease; }
        .gallery-item:hover img { transform: scale(1.05); }
        .gallery-item .delete-btn { position: absolute; top: 10px; right: 10px; background-color: rgba(220, 53, 69, 0.8); color: white; border: none; border-radius: 50%; width: 35px; height: 35px; display: grid; place-items: center; cursor: pointer; transition: background-color 0.2s ease; opacity: 0; }
        .gallery-item:hover .delete-btn { opacity: 1; }
        .delete-btn:hover { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">Manajemen Galeri</h1>
        <a href="dashboard.php" class="btn btn-secondary-outline"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="upload-section">
        <h4 class="mb-3">Upload Gambar Baru</h4>
        <form action="manage_galeri.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <label for="gambar_file" class="form-label">Pilih File Gambar</label>
                    <input type="file" class="form-control" id="gambar_file" name="gambar_file" required>
                </div>
                <div class="col-md-5 mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                    <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Contoh: Kegiatan Imunisasi Massal">
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <button type="submit" name="upload" class="btn btn-success w-100">
                        <i class="bi bi-upload"></i> Upload
                    </button>
                </div>
            </div>
        </form>
    </div>

    <h4 class="mt-5">Gambar di Galeri</h4>
    <div class="gallery-grid mt-3">
        <?php foreach ($all_gambar as $gambar): ?>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($gambar['gambar_path']) ?>" alt="<?= htmlspecialchars($gambar['deskripsi']) ?>">
                <a href="?delete=<?= $gambar['id'] ?>" class="delete-btn" title="Hapus Gambar" onclick="return confirm('Yakin ingin menghapus gambar ini?')">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>