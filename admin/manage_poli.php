<?php 
// ===== BAGIAN LOGIKA PHP =====
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../db_connect.php';

// Handle Simpan (Tambah & Edit)
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $icon = $_POST['icon'];
    $gambar_lama = $_POST['gambar_lama'];
    $gambar_final = $gambar_lama;
    if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['gambar_file'];
        $upload_dir = 'uploads/';
        $nama_file_unik = uniqid() . '_' . basename($file['name']);
        $target_file = $upload_dir . $nama_file_unik;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024;
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $gambar_final = $target_file; 
                if (!empty($gambar_lama) && file_exists($gambar_lama)) { unlink($gambar_lama); }
            }
        }
    }
    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO poli (nama, deskripsi, icon, gambar_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $deskripsi, $icon, $gambar_final]);
    } else {
        $stmt = $conn->prepare("UPDATE poli SET nama=?, deskripsi=?, icon=?, gambar_url=? WHERE id=?");
        $stmt->execute([$nama, $deskripsi, $icon, $gambar_final, $id]);
    }
    header('Location: manage_poli.php');
    exit;
}

// Handle Hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt_select = $conn->prepare("SELECT gambar_url FROM poli WHERE id = ?");
    $stmt_select->execute([$id]);
    $gambar_untuk_dihapus = $stmt_select->fetchColumn();
    $stmt_delete = $conn->prepare("DELETE FROM poli WHERE id = ?");
    $stmt_delete->execute([$id]);
    if ($gambar_untuk_dihapus && file_exists($gambar_untuk_dihapus)) { unlink($gambar_untuk_dihapus); }
    header('Location: manage_poli.php');
    exit;
}

// Mengambil semua data poli untuk ditampilkan
$all_poli = $conn->query("SELECT * FROM poli ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Poli & Layanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-900: #0b3d2e;
            --green-700: #0f5c44;
            --green-600: #137a56;
            --green-500: #16a168;
            --green-100: #e9fff6;
            --text-dark: #0e2a22;
            --white: #ffffff;
            --shadow: 0 10px 25px rgba(19, 122, 86, 0.1);
            --radius: 18px;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #f7fffb 0%, #ffffff 40%);
            color: var(--text-dark);
        }
        .page-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: var(--green-700);
            position: relative;
            display: inline-block;
        }
        .page-title:after {
            content: ""; display: block; width: 60%; height: 4px; margin-top: 8px;
            background: linear-gradient(90deg, #41c28c, var(--green-600)); border-radius: 2px;
        }
        .header-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-green {
            --bs-btn-bg: var(--green-600); --bs-btn-border-color: var(--green-600);
            --bs-btn-hover-bg: var(--green-500); --bs-btn-hover-border-color: var(--green-500);
            --bs-btn-color: #fff; --bs-btn-hover-color: #fff;
            border-radius: 12px; box-shadow: 0 5px 15px rgba(19,122,86,0.2);
            font-weight: 600;
            padding: 10px 22px;
        }
        .btn-secondary-outline {
            color: var(--green-700);
            background-color: transparent;
            border: 1px solid var(--green-600);
            border-radius: 12px;
            padding: 10px 22px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-secondary-outline:hover {
            background-color: var(--green-100);
            color: var(--green-900);
        }
        .table thead {
            background-color: var(--green-600);
            color: var(--white);
        }
        .table {
            border-radius: var(--radius);
            overflow: hidden;
        }
        .table img {
            border-radius: 8px;
        }
        .action-buttons a, .action-buttons button {
            border: none; color: white; padding: 6px 10px; border-radius: 6px;
        }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }
        .modal-header {
            background-color: var(--green-700);
            color: var(--white);
        }
        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">Manajemen Poli & Layanan</h1>
        <div class="header-buttons">
            <a href="dashboard.php" class="btn btn-secondary-outline">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#formPoliModal">
                <i class="bi bi-plus-lg"></i> Tambah Poli
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Poli</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_poli as $poli): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($poli['gambar_url']) ?>" alt="" width="120"></td>
                    <td><?= htmlspecialchars($poli['nama']) ?></td>
                    <td><?= htmlspecialchars($poli['deskripsi']) ?></td>
                    <td class="action-buttons">
                        <button type="button" class="btn-edit" 
                                data-bs-toggle="modal" data-bs-target="#formPoliModal"
                                data-id="<?= $poli['id'] ?>"
                                data-nama="<?= htmlspecialchars($poli['nama']) ?>"
                                data-deskripsi="<?= htmlspecialchars($poli['deskripsi']) ?>"
                                data-icon="<?= htmlspecialchars($poli['icon']) ?>"
                                data-gambar_lama="<?= htmlspecialchars($poli['gambar_url']) ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="?delete=<?= $poli['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus poli ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="formPoliModal" tabindex="-1" aria-labelledby="formPoliModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: var(--radius);">
      <div class="modal-header">
        <h5 class="modal-title" id="formPoliModalLabel">Tambah Poli Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="manage_poli.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" id="poli-id">
          <input type="hidden" name="gambar_lama" id="gambar_lama">
          <div class="modal-body">
              <div class="mb-3"><label for="nama" class="form-label">Nama Poli</label><input type="text" class="form-control" id="nama" name="nama" required></div>
              <div class="mb-3"><label for="deskripsi" class="form-label">Deskripsi</label><textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea></div>
              <div class="mb-3"><label for="icon" class="form-label">Nama Ikon Bootstrap</label><input type="text" class="form-control" id="icon" name="icon"></div>
              <div class="mb-3">
                  <label for="gambar_file" class="form-label">Upload Gambar (Kosongkan jika tidak ingin ganti)</label>
                  <input type="file" class="form-control" id="gambar_file" name="gambar_file">
                  <small id="info-gambar-lama" class="form-text text-muted"></small>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" name="save">Simpan</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var formPoliModal = document.getElementById('formPoliModal');
    formPoliModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var isEdit = button.classList.contains('edit-btn');
        var modalTitle = formPoliModal.querySelector('.modal-title');
        var poliIdInput = formPoliModal.querySelector('#poli-id');
        var namaInput = formPoliModal.querySelector('#nama');
        var deskripsiInput = formPoliModal.querySelector('#deskripsi');
        var iconInput = formPoliModal.querySelector('#icon');
        var gambarLamaInput = formPoliModal.querySelector('#gambar_lama');
        var infoGambarLama = formPoliModal.querySelector('#info-gambar-lama');
        var gambarFileInput = formPoliModal.querySelector('#gambar_file');
        
        if (isEdit) {
            modalTitle.textContent = 'Edit Poli';
            poliIdInput.value = button.dataset.id;
            namaInput.value = button.dataset.nama;
            deskripsiInput.value = button.dataset.deskripsi;
            iconInput.value = button.dataset.icon;
            gambarLamaInput.value = button.dataset.gambar_lama;
            infoGambarLama.textContent = 'Gambar saat ini: ' + button.dataset.gambar_lama.replace('uploads/', '');
            gambarFileInput.removeAttribute('required');
        } else {
            modalTitle.textContent = 'Tambah Poli Baru';
            poliIdInput.value = '';
            formPoliModal.querySelector('form').reset();
            infoGambarLama.textContent = '';
            gambarFileInput.setAttribute('required', 'required');
        }
    });
});
</script>

</body>
</html>