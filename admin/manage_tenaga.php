<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../db_connect.php';

// === Simpan (Tambah / Edit) ===
if(isset($_POST['save'])){
    $id     = $_POST['id'];
    $jenis  = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];

    if(empty($id)){ // Tambah
        $stmt = $conn->prepare("INSERT INTO tenaga_kesehatan (jenis, jumlah) VALUES (?,?)");
        $stmt->execute([$jenis,$jumlah]);
    } else {        // Edit
        $stmt = $conn->prepare("UPDATE tenaga_kesehatan SET jenis=?, jumlah=? WHERE id=?");
        $stmt->execute([$jenis,$jumlah,$id]);
    }
    header('Location: manage_tenaga.php');
    exit;
}

// === Hapus ===
if(isset($_GET['delete'])){
    $stmt = $conn->prepare("DELETE FROM tenaga_kesehatan WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header('Location: manage_tenaga.php');
    exit;
}

// === Ambil semua data (ASC agar urut dari awal input) ===
$all_tenaga = $conn->query("SELECT * FROM tenaga_kesehatan ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Tenaga Kesehatan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{--green-900:#0b3d2e;--green-700:#0f5c44;--green-600:#137a56;--green-500:#16a168;--green-100:#e9fff6;--text-dark:#0e2a22;--white:#ffffff;--shadow:0 10px 25px rgba(19,122,86,.1);--radius:18px}
    body{font-family:'Poppins',sans-serif;background:linear-gradient(180deg,#f7fffb 0%,#ffffff 40%);color:var(--text-dark)}
    .page-wrapper{max-width:1200px;margin:40px auto;padding:30px;background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow)}
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
    .page-title{font-family:'Montserrat',sans-serif;font-weight:700;color:var(--green-700);position:relative}
    .page-title:after{content:"";display:block;width:60%;height:4px;margin-top:8px;background:linear-gradient(90deg,#41c28c,var(--green-600));border-radius:2px}
    .btn-green{background:var(--green-600);border-color:var(--green-600);color:#fff;border-radius:12px;font-weight:600;padding:10px 22px;box-shadow:0 5px 15px rgba(19,122,86,.2)}
    .table thead{background:var(--green-600);color:#fff}
    .action-buttons button{border:none;color:#fff;padding:6px 10px;border-radius:6px}
    .btn-edit{background:#ffc107}
    .btn-delete{background:#dc3545}
    
  </style>
</head>
<body>

<div class="page-wrapper">
  <div class="page-header">
    <h1 class="page-title">Manajemen Tenaga Kesehatan</h1>
    <div>
      <a href="dashboard.php" class="btn btn-secondary me-2"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
      <button class="btn btn-green" data-bs-toggle="modal" data-bs-target="#tenagaModal" id="addBtn">
        <i class="bi bi-plus-lg"></i> Tambah Data
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-clean align-middle">
      <thead>
        <tr><th>No</th><th>Jenis Tenaga</th><th class="text-center">Jumlah</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php if(!$all_tenaga): ?>
          <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
        <?php else: $no = 1; foreach($all_tenaga as $t): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($t['jenis']) ?></td>
            <td class="text-center"><span class="badge text-bg-success"><?= $t['jumlah'] ?></span></td>
            <td class="action-buttons">
              <button class="btn-edit" data-bs-toggle="modal" data-bs-target="#tenagaModal"
                      data-id="<?= $t['id'] ?>" data-jenis="<?= htmlspecialchars($t['jenis']) ?>" data-jumlah="<?= $t['jumlah'] ?>">
                <i class="bi bi-pencil-square"></i>
              </button>
              <a href="?delete=<?= $t['id'] ?>" class="btn-delete" onclick="return confirm('Hapus data ini?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="tenagaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:var(--radius)">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="manage_tenaga.php" method="POST">
        <input type="hidden" name="id" id="tenaga-id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Jenis Tenaga Kesehatan</label>
            <input type="text" class="form-control" name="jenis" id="jenis" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" class="form-control" name="jumlah" id="jumlah" required>
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
document.addEventListener('DOMContentLoaded', function(){
  var modal  = document.getElementById('tenagaModal');
  var addBtn = document.getElementById('addBtn');

  modal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var title  = modal.querySelector('.modal-title');
    var idInput     = document.getElementById('tenaga-id');
    var jenisInput  = document.getElementById('jenis');
    var jumlahInput = document.getElementById('jumlah');

    if(button === addBtn){
      title.textContent = 'Tambah Data';
      idInput.value     = '';
      jenisInput.value  = '';
      jumlahInput.value = '';
    } else {
      title.textContent = 'Edit Data';
      idInput.value     = button.getAttribute('data-id');
      jenisInput.value  = button.getAttribute('data-jenis');
      jumlahInput.value = button.getAttribute('data-jumlah');
    }
  });
});
</script>
</body>
</html>
