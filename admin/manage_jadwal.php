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
    $dokter = $_POST['dokter'];
    $spesialisasi = $_POST['spesialisasi']; // Kolom 'poli' dipakai untuk spesialisasi
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $jam_praktik = $jam_mulai . ' - ' . $jam_selesai;

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO jadwal (dokter, poli, hari, jam, status) VALUES (?, ?, ?, ?, 'Buka')");
        $stmt->execute([$dokter, $spesialisasi, $hari, $jam_praktik]);
    } else {
        $stmt = $conn->prepare("UPDATE jadwal SET dokter=?, poli=?, hari=?, jam=? WHERE id=?");
        $stmt->execute([$dokter, $spesialisasi, $hari, $jam_praktik, $id]);
    }
    header('Location: manage_jadwal.php');
    exit;
}

// Handle Hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM jadwal WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_jadwal.php');
    exit;
}

// Mengambil semua data jadwal untuk ditampilkan
$all_jadwal = $conn->query("SELECT * FROM jadwal ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
    <style>
        /* Menggunakan CSS yang sama persis dengan manage_poli.php */
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
        <h1 class="page-title">Manajemen Jadwal Dokter</h1>
        <div class="header-buttons">
            <a href="dashboard.php" class="btn btn-secondary-outline"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
            <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#formJadwalModal"><i class="bi bi-plus-lg"></i> Tambah Jadwal</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama Dokter</th>
                    <th>Spesialisasi</th>
                    <th>Hari</th>
                    <th>Jam Praktik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_jadwal as $jadwal): ?>
                <tr>
                    <td><?= htmlspecialchars($jadwal['dokter']) ?></td>
                    <td><?= htmlspecialchars($jadwal['poli']) ?></td>
                    <td><?= htmlspecialchars($jadwal['hari']) ?></td>
                    <td><?= htmlspecialchars($jadwal['jam']) ?></td>
                    <td class="action-buttons">
                        <button type="button" class="btn-edit edit-btn" 
                                data-bs-toggle="modal" data-bs-target="#formJadwalModal"
                                data-id="<?= $jadwal['id'] ?>"
                                data-dokter="<?= htmlspecialchars($jadwal['dokter']) ?>"
                                data-spesialisasi="<?= htmlspecialchars($jadwal['poli']) ?>"
                                data-hari="<?= htmlspecialchars($jadwal['hari']) ?>"
                                data-jam="<?= htmlspecialchars($jadwal['jam']) ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="?delete=<?= $jadwal['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus jadwal ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="formJadwalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: var(--radius);">
      <div class="modal-header">
        <h5 class="modal-title" id="formJadwalModalLabel">Tambah Jadwal Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="manage_jadwal.php" method="POST">
          <input type="hidden" name="id" id="jadwal-id">
          <div class="modal-body">
              <div class="mb-3"><label for="dokter" class="form-label">Nama Dokter</label><input type="text" class="form-control" id="dokter" name="dokter" required></div>
              <div class="mb-3"><label for="spesialisasi" class="form-label">Spesialisasi</label><input type="text" class="form-control" id="spesialisasi" name="spesialisasi" required></div>
              <div class="mb-3"><label for="hari" class="form-label">Hari</label><select class="form-select" id="hari" name="hari" required><option value="Senin">Senin</option><option value="Selasa">Selasa</option><option value="Rabu">Rabu</option><option value="Kamis">Kamis</option><option value="Jumat">Jumat</option><option value="Sabtu">Sabtu</option></select></div>
              <div class="row">
                <div class="col"><label for="jam_mulai" class="form-label">Jam Mulai</label><input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required></div>
                <div class="col"><label for="jam_selesai" class="form-label">Jam Selesai</label><input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required></div>
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
    var formJadwalModal = document.getElementById('formJadwalModal');
    formJadwalModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var isEdit = button.classList.contains('edit-btn');
        var modalTitle = formJadwalModal.querySelector('.modal-title');
        
        var form = formJadwalModal.querySelector('form');
        var idInput = form.querySelector('#jadwal-id');
        var dokterInput = form.querySelector('#dokter');
        var spesialisasiInput = form.querySelector('#spesialisasi');
        var hariInput = form.querySelector('#hari');
        var jamMulaiInput = form.querySelector('#jam_mulai');
        var jamSelesaiInput = form.querySelector('#jam_selesai');

        if (isEdit) {
            modalTitle.textContent = 'Edit Jadwal';
            idInput.value = button.dataset.id;
            dokterInput.value = button.dataset.dokter;
            spesialisasiInput.value = button.dataset.spesialisasi;
            hariInput.value = button.dataset.hari;
            
            var jamArray = button.dataset.jam.split(' - ');
            jamMulaiInput.value = jamArray[0] || '';
            jamSelesaiInput.value = jamArray[1] || '';
        } else {
            modalTitle.textContent = 'Tambah Jadwal Baru';
            form.reset();
            idInput.value = '';
        }
    });
});
</script>

</body>
</html>