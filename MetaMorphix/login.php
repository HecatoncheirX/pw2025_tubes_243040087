<?php
session_start();
require 'db.php';
// Variabel untuk menyimpan pesan error.
$error = ''; 

// Cek jika form telah di-submit dengan metode POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil username dan password dari form.
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validasi dasar: pastikan input tidak kosong.
    if (empty($username) || empty($password)) {
        $error = "Semua field wajib diisi.";
    } else {
        // Menggunakan prepared statement untuk keamanan (mencegah SQL Injection).
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username); // 's' berarti parameter adalah string.
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Cek apakah username ditemukan (harus ada 1 hasil).
        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Memverifikasi password yang diinput dengan hash yang ada di database.
            if (password_verify($password, $user['password'])) {
                // Jika password benar, simpan username ke dalam session.
                $_SESSION['username'] = $user['username'];
                // Arahkan ke halaman yang sesuai berdasarkan username.
                if ($user['username'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit; // Hentikan skrip.
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
    <style>
        body { background: #24A2D4; background: linear-gradient(90deg,rgba(36, 162, 212, 1) 0%, rgba(232, 252, 240, 1) 100%, rgba(237, 221, 83, 1) 100%); font-family: "Lato", sans-serif; font-weight: 700; font-style: normal; }
        .main { height: 100vh; }
        .kotak { width: 500px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="main d-flex justify-content-center align-items-center">
        <div class="kotak p-5 shadow">
            <h4 class="text-center mb-4">Selamat Datang Di Toko Morphix</h4>
            <h5 class="text-center mb-4">Login</h5>

            <!-- Menampilkan pesan error jika ada -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div>
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" id="username" autocomplete="off">
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" autocomplete="off">
                </div>
                <div>
                    <button type="submit" class="btn btn-info form-control mt-3">Masukan Akun</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <small>Belum punya akun? <a href="register.php">Daftar di sini</a></small>
            </div>
        </div>
    </div>
</body>
</html>
