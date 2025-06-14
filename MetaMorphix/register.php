<?php
session_start();
require 'db.php';

// Variabel untuk menyimpan pesan error.
$error = '';

// Cek jika form telah di-submit.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan bersihkan.
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Semua field wajib diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Cek apakah username sudah ada di database menggunakan prepared statement.
        $sql_check = "SELECT id FROM users WHERE username = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            $error = "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            // Jika username tersedia, hash password sebelum disimpan ke database.
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Masukkan user baru ke database menggunakan prepared statement.
            $sql_insert = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "ss", $username, $hashed_password);

            if (mysqli_stmt_execute($stmt_insert)) {
                // Jika berhasil, arahkan ke halaman sukses.
                header("Location: register_berhasil.php");
                exit;
            } else {
                $error = "Terjadi kesalahan pada server. Gagal mendaftar.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Proza+Libre:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <style>
        body { background: #24A2D4; background: linear-gradient(90deg, rgba(36, 162, 212, 1) 0%, rgba(232, 252, 240, 1) 100%, rgba(237, 221, 83, 1) 100%); font-family: "Lato", sans-serif; font-weight: 700; font-style: normal; }
        .main { height: 100vh; }
        .kotak { width: 500px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="main d-flex justify-content-center align-items-center">
        <div class="kotak p-5 shadow">
            <h4 class="text-center mb-4">Register</h4>

            <!-- Tampilkan pesan error jika ada -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div>
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" id="username" autocomplete="off" required>
                </div>
                <div class="mt-2">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" autocomplete="off" required>
                </div>
                <div class="mt-2">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="off" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-info form-control mt-3">Buat Akun</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <small>Sudah punya akun? <a href="login.php">Login di sini</a></small>
            </div>
        </div>
    </div>
</body>
</html>
