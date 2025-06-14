<?php
require 'session.php';
require 'db.php';

// Cek apakah pengguna sudah login dan apakah rolenya adalah 'admin'.
// Jika tidak, pengguna akan diarahkan ke halaman login.
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Mengambil semua data dari tabel 'kategori'.
$queryKategori = mysqli_query($conn, 'SELECT * FROM kategori');
// Menghitung jumlah baris (jumlah kategori) yang ada.
$jumlahKategori = mysqli_num_rows($queryKategori);

// Mengambil semua data dari tabel 'produk'.
$queryProduk = mysqli_query($conn, 'SELECT * FROM produk');
// Menghitung jumlah baris (jumlah produk) yang ada.
$jumlahProduk = mysqli_num_rows($queryProduk);
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Admin Panel</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    </head>
    
    <style>
        .kotak {
            border: solid;
        }
        
        .kotak-kategori {
            background-color: #8DD8FF;
            border-radius: 15px;
        }

        .kotak-produk {
            background-color: #4E71FF;
            border-radius: 15px;
        }

        .icon-size {
            font-size: 70px
        }

        .no-deco {
            text-decoration: none;
        }
    </style>

    <body>
        <?php require 'navbar.php'; ?>
        <div class="container mt-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="bi bi-house"></i>Home
                    </li>
                </ol>
            </nav>

            <h2>Halo <?php echo $_SESSION['username'] ?></h2>

            <div class="container mt-5">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="kotak-kategori p-3">
                            <div class="row">
                                <div class="col-6">
                                    <i class="bi bi-justify icon-size"></i>
                                 </div>
                                <div class="col-6">
                                    <h3 class="fs-2">Kategori</h3>
                                    <p class="fs-6 "> <?php echo $jumlahKategori; ?> Kategori</p>
                                    <p><a href="kategori.php" class="text-white no-deco">Lihat Detail</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="kotak-produk p-3">
                            <div class="row">
                                <div class="col-6">
                                    <i class="bi bi-box2 icon-size"></i>
                                </div>
                                <div class="col-6">
                                    <h3 class="fs-2">Produk</h3>
                                    <p class="fs-6 "><?php echo $jumlahProduk; ?> Produk</p>
                                    <p><a href="produk.php" class="text-white no-deco">Lihat Detail</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- Memuat JavaScript dari Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    </body>
</html>
