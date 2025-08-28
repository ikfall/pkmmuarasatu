<?php 
// Baris ini mengambil data sesi untuk memastikan admin sudah login
require_once 'auth.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Puskesmas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* CSS Reset dan Styling Dasar */
        :root {
            --green-900: #0b3d2e;
            --green-700: #0f5c44;
            --green-100: #e9fff6;
            --text-dark: #0e2a22;
            --white: #ffffff;
            --shadow: 0 4px 15px rgba(0,0,0,0.08);
            --radius: 8px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f8f7; /* Latar belakang sedikit abu-abu */
            margin: 0;
            color: var(--text-dark);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Styling untuk Header */
        .header {
            background-color: var(--green-900);
            color: var(--white);
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5em;
            margin: 0;
        }
        .header a {
            color: var(--white);
            text-decoration: none;
            background-color: var(--green-700);
            padding: 8px 15px;
            border-radius: var(--radius);
            transition: background-color 0.2s ease;
        }
        .header a:hover {
            background-color: #137a56;
        }

        /* Styling untuk Konten Utama */
        main {
            padding: 40px 0;
        }
        
        .welcome-message {
            margin-bottom: 30px;
        }
        
        /* Grid untuk Kartu Dashboard */
        .dashboard-grid {
            display: grid;
            /* Membuat kolom yang responsif */
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        /* Styling untuk setiap kartu */
        .dashboard-card {
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px); /* Efek mengangkat saat disentuh mouse */
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .dashboard-card i {
            font-size: 3rem; /* Ukuran ikon */
            color: var(--green-700);
            margin-bottom: 15px;
        }

        .dashboard-card h3 {
            margin: 0;
            font-weight: 600;
        }

    </style>
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Dashboard Admin</h1>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <main class="container">
        <div class="welcome-message">
            <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Pilih salah satu menu di bawah untuk mulai mengelola konten website.</p>
        </div>
        
        <div class="dashboard-grid">
            <a href="manage_poli.php" class="dashboard-card">
                <i class="bi bi-hospital"></i>
                <h3>Kelola Poli & Layanan</h3>
            </a>
            
          <a href="manage_jadwal.php" class="dashboard-card">
    <i class="bi bi-calendar2-week"></i>
    <h3>Kelola Jadwal Dokter</h3>
</a>

            <a href="manage_tenaga.php" class="dashboard-card">
    <i class="bi bi-people-fill"></i>
    <h3>Kelola Tenaga Kesehatan</h3>
</a>


            <a href="manage_galeri.php" class="dashboard-card">
    <i class="bi bi-images"></i>
    <h3>Kelola Galeri</h3>
</a>
            </a>
            
            <a href="manage_berita.php" class="dashboard-card">
    <i class="bi bi-newspaper"></i>
    <h3>Kelola Berita</h3>
</a>
            </a>
        </div>
    </main>

</body>
</html>

