<?php 
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
    $judul = $_POST['judul'];
    $tag = $_POST['tag'];
    $isi = $_POST['isi'];
    $tanggal = date('Y-m-d'); // Tanggal hari ini

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO berita (judul, tag, isi, tanggal) VALUES (?, ?, ?, ?)");
        $stmt->execute([$judul, $tag, $isi, $tanggal]);
    } else {
        // Saat edit, kita tidak mengubah tanggal aslinya
        $stmt = $conn->prepare("UPDATE berita SET judul=?, tag=?, isi=? WHERE id=?");
        $stmt->execute([$judul, $tag, $isi, $id]);
    }
    header('Location: manage_berita.php');
    exit;
}

// Handle Hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_berita.php');
    exit;
}

// Mengambil semua data berita untuk ditampilkan
$all_berita = $conn->query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Berita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
    <style>
        /* Menggunakan CSS yang sama persis dengan halaman sebelumnya */
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
        .header-buttons { display: flex; align-items: center; gap: 10px; }
        .btn-green { --bs-btn-bg: var(--green-600); --bs-btn-border-color: var(--green-600); --bs-btn-hover-bg: var(--green-500); --bs-btn-hover-border-color: var(--green-500); --bs-btn-color: #fff; --bs-btn-hover-color: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(19,122,86,0.2); font-weight: 600; padding: 10px 22px; }
        .btn-secondary-outline { color: var(--green-700); background-color: transparent; border: 1px solid var(--green-600); border-radius: 12px; padding: 10px 22px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
        .btn-secondary-outline:hover { background-color: var(--green-100); color: var(--green-900); }
        .table thead { background-color: var(--green-600); color: var(--white); }
        .table { border-radius: var(--radius); overflow: hidden; }
        .action-buttons a, .action-buttons button { border: none; color: white; padding: 6px 10px; border-radius: 6px; }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }
        .modal-header { background-color: var(--green-700); color: var(--white); }
        .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">Manajemen Berita</h1>
        <div class="header-buttons">
            <a href="dashboard.php" class="btn btn-secondary-outline"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
            <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#formBeritaModal"><i class="bi bi-plus-lg"></i> Tambah Berita</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tag</th>
                    <th>Isi Berita (Ringkas)</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_berita as $berita): ?>
                <tr>
                    <td><?= htmlspecialchars($berita['judul']) ?></td>
                    <td><span class="badge bg-success"><?= htmlspecialchars($berita['tag']) ?></span></td>
                    <td><?= htmlspecialchars(substr($berita['isi'], 0, 70)) ?>...</td>
                    <td><?= date('d M Y', strtotime($berita['tanggal'])) ?></td>
                    <td class="action-buttons">
                        <button type="button" class="btn-edit edit-btn" 
                                data-bs-toggle="modal" data-bs-target="#formBeritaModal"
                                data-id="<?= $berita['id'] ?>"
                                data-judul="<?= htmlspecialchars($berita['judul']) ?>"
                                data-tag="<?= htmlspecialchars($berita['tag']) ?>"
                                data-isi="<?= htmlspecialchars($berita['isi']) ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="?delete=<?= $berita['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus berita ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="formBeritaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: var(--radius);">
      <div class="modal-header">
        <h5 class="modal-title" id="formBeritaModalLabel">Tambah Berita Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="manage_berita.php" method="POST">
          <input type="hidden" name="id" id="berita-id">
          <div class="modal-body">
              <div class="mb-3"><label for="judul" class="form-label">Judul Berita</label><input type="text" class="form-control" id="judul" name="judul" required></div>
              <div class="mb-3"><label for="tag" class="form-label">Tag (Contoh: Info, Pengumuman, Program)</label><input type="text" class="form-control" id="tag" name="tag" required></div>
              <div class="mb-3"><label for="isi" class="form-label">Isi Berita</label><textarea class="form-control" id="isi" name="isi" rows="5" required></textarea></div>
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
    var formBeritaModal = document.getElementById('formBeritaModal');
    formBeritaModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var isEdit = button.classList.contains('edit-btn');
        var modalTitle = formBeritaModal.querySelector('.modal-title');
        
        var form = formBeritaModal.querySelector('form');
        var idInput = form.querySelector('#berita-id');
        var judulInput = form.querySelector('#judul');
        var tagInput = form.querySelector('#tag');
        var isiInput = form.querySelector('#isi');

        if (isEdit) {
            modalTitle.textContent = 'Edit Berita';
            idInput.value = button.dataset.id;
            judulInput.value = button.dataset.judul;
            tagInput.value = button.dataset.tag;
            isiInput.value = button.dataset.isi;
        } else {
            modalTitle.textContent = 'Tambah Berita Baru';
            form.reset();
            idInput.value = '';
        }
    });
});
</script>

</body>
</html>