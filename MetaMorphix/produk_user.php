<?php
require 'db.php';
require 'session.php';

// Ambil semua kategori untuk ditampilkan di sidebar.
$queryKategori = mysqli_query($conn, "SELECT * FROM kategori");

// Ambil parameter dari URL untuk pencarian dan filter.
$namaProduk = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$kategoriId = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

// Query dasar untuk menampilkan semua produk.
$queryProduk = "SELECT * FROM produk";

// Jika ada keyword pencarian (dari halaman lain atau direct URL), filter berdasarkan nama.
if ($namaProduk != '') {
    $queryProduk = "SELECT * FROM produk WHERE nama LIKE '%$namaProduk%'";
} 
// Jika ada filter kategori (dari klik di sidebar), filter berdasarkan kategori.
elseif ($kategoriId != 0) {
    $queryProduk = "SELECT * FROM produk WHERE kategori_id = '$kategoriId'";
}

$resultProduk = mysqli_query($conn, $queryProduk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk - Toko Morphix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style_produk.css">
</head>
<body>
    <?php require 'navbar_user.php'; ?>
    <div class="container-fluid banner-produk-page d-flex align-items-center justify-content-center">
        <h1 class="text-white">Produk</h1>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <h4 class="text-white">Kategori</h4>
                <div class="list-group">
                    <!-- Tampilkan semua kategori sebagai link filter -->
                    <a href="produk_user.php" class="list-group-item list-group-item-action <?php if($kategoriId == 0) echo 'active'; ?>">Semua Kategori</a>
                    <?php while ($kategori = mysqli_fetch_array($queryKategori)) { ?>
                        <a href="produk_user.php?kategori=<?= $kategori['id']; ?>" class="list-group-item list-group-item-action <?php if($kategoriId == $kategori['id']) echo 'active'; ?>">
                            <?= htmlspecialchars($kategori['nama']); ?>
                        </a>
                    <?php } ?>
                </div>

                <h4 class="mt-4 text-white">Pencarian</h4>
                <div class="input-group">
                    <input type="text" class="form-control" id="keyword" placeholder="Cari nama produk..." autocomplete="off">
                </div>
            </div>

            <div class="col-lg-9">
                <h3 class="text-center text-white">Daftar Produk</h3>
                <div class="row" id="product-list">
                    <?php
                    // Tampilkan produk yang sesuai dengan query awal.
                    if (mysqli_num_rows($resultProduk) > 0) {
                        while ($produk = mysqli_fetch_array($resultProduk)) { ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="img/<?= htmlspecialchars($produk['gambar']); ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama']); ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= htmlspecialchars($produk['nama']); ?></h5>
                                        <p class="card-text text-danger fw-bold">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                                        <div class="mt-auto">
                                            <a href="produk_detail_user.php?id=<?= $produk['id']; ?>" class="btn btn-outline-primary w-100">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="col-12">
                            <div class="alert alert-warning text-center" role="alert">
                                Produk yang Anda cari tidak ditemukan.
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php require 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script JavaScript untuk AJAX (pencarian live) -->
    <script>
        $(document).ready(function() {
            // Event listener saat pengguna mengetik di kolom pencarian
            $('#keyword').on('keyup', function() {
                var keyword = $(this).val();

                // Mengirim request AJAX ke file 'ajax_produk.php'
                $.ajax({
                    url: 'ajax_produk.php', // File target
                    type: 'GET',
                    data: { keyword: keyword }, // Data yang dikirim (kata kunci)
                    success: function(data) {
                        // Jika berhasil, ganti konten div '#product-list' dengan hasil dari AJAX.
                        $('#product-list').html(data);
                    }
                });
            });
        });
    </script>
</body>
</html>
