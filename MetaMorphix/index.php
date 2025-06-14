<?php 

require 'db.php';
require 'session.php'; 


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Toko Morphix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php require 'navbar_user.php'; ?>
<!-- Banner -->
<div class="container-fluid banner-produk d-flex align-items-center">
    <div class="container text-center text-white font-banner">
        <h1>Toko Morphix</h1>
        <h5>Selamat Datang DI Toko Gaming Equipment Kami!</h5>
        <div class="col-md-8 offset-md-2"></div>
    </div>
</div>


<div class="container-fluid py-5">
    <div class="container">
        <div class="row align-items-center gy-5">

            <!-- Tentang Kami-->
            <div class="col-lg-5 col-md-12 text-white text-center text-lg-start">
                <h2 class="mb-3">Tentang Kami</h2>
                <p class="fs-5 mt-3">
                    Selamat datang di Toko Morphix! Kami adalah destinasi utama bagi para gamer yang mencari equipment gaming berkualitas untuk meningkatkan performa. Kami percaya bahwa perlengkapan yang tepat adalah kunci menuju kemenangan. Oleh karena itu, kami menyediakan produk original dan terpercaya, mulai dari keyboard mechanical, mouse presisi tinggi, hingga headset imersif. Temukan gear andalan Anda dan dominasi setiap permainan bersama kami.
                </p>
                <a href="produk_user.php" class="btn btn-outline-light mt-3">Lihat Semua Produk</a>
            </div>

            <!-- Produk Kami -->
            <div class="col-lg-7 col-md-12">
                <h3 class="text-white text-center mb-4">Lihat Produk Kita</h3>
                <div class="row gy-4">
                    <!-- Kartu Keyboard -->
                    <div class="col-md-6 col-6">
                        <a href="produk_user.php?kategori=keyboard" class="card-link">
                            <div class="card text-white card-highlight produk-keyboard">
                                <div class="card-img-overlay d-flex flex-column justify-content-end">
                                    <h4 class="card-title">Keyboard</h4>
                                    <p class="card-hover-text">Lihat Lebih Lanjut</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Kartu Mouse -->
                    <div class="col-md-6 col-6">
                        <a href="produk_user.php?kategori=mouse" class="card-link">
                            <div class="card text-white card-highlight produk-mouse">
                                <div class="card-img-overlay d-flex flex-column justify-content-end">
                                    <h4 class="card-title">Mouse</h4>
                                    <p class="card-hover-text">Lihat Lebih Lanjut</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Kartu Headset -->
                    <div class="col-md-6 col-6">
                        <a href="produk_user.php?kategori=headset" class="card-link">
                            <div class="card text-white card-highlight produk-headset">
                                <div class="card-img-overlay d-flex flex-column justify-content-end">
                                    <h4 class="card-title">Headset</h4>
                                    <p class="card-hover-text">Lihat Lebih Lanjut</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Kartu Monitor -->
                    <div class="col-md-6 col-6">
                        <a href="produk_user.php?kategori=monitor" class="card-link">
                            <div class="card text-white card-highlight produk-monitor">
                                <div class="card-img-overlay d-flex flex-column justify-content-end">
                                    <h4 class="card-title">Monitor</h4>
                                    <p class="card-hover-text">Lihat Lebih Lanjut</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php' ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>