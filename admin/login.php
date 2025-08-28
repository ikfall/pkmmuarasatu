<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Puskesmas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            /* Background gradient hijau-putih */
            background: linear-gradient(135deg, #e8f5e9, #ffffff, #f1f8e9);
            background-size: 400% 400%;
            animation: gradientBG 12s ease infinite;
        }

        /* animasi lembut gradient */
        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 18px;
            padding: 2.5rem;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease-in-out;
            text-align: center;
            position: relative;
        }

        .login-icon {
            font-size: 3rem;
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2E7D32;
            margin-bottom: 1.5rem;
        }

        .input-group-text {
            background: #f8f9fa;
            border-radius: 10px 0 0 10px;
        }

        .form-control {
            border-radius: 0 10px 10px 0;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
        }

        .btn-login {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #45A049, #2E7D32);
            transform: translateY(-2px);
            box-shadow: 0px 6px 15px rgba(46, 125, 50, 0.3);
        }

        .alert-danger {
            border-radius: 10px;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer kecil */
        .footer {
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Icon medis -->
        <div class="login-icon">
        <i class="fa-solid fa-hospital"></i>
        </div>

        <h3 class="card-title">Admin Puskesmas</h3>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
   <i class="fa-solid fa-circle-exclamation"></i> Username atau Password salah.
</div>
        <?php endif; ?>

        <form action="handle_login.php" method="post">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn-login">Login</button>
            </div>
        </form>

        <div class="footer">
            © 2025 Puskesmas Muara Satu – Dinas Kesehatan Kota Lhokseumawe
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
