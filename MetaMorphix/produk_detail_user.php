<?php
require 'db.php';
require 'session.php';

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: produk_user.php");
    exit;
}

// Ambil ID dari URL dan pastikan itu adalah angka
$id = (int)$_GET['id'];

// Ambil data produk dari database
$query = "SELECT * FROM produk WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

// Jika produk tidak ditemukan, hentikan eksekusi
if (!$produk) {
    die("<div class='alert alert-danger text-center'>Produk tidak ditemukan.</div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produk['nama']); ?> - Toko Morphix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style_detail_produk.css">
</head>
<body>
    <header>
        <?php require 'navbar_user.php'; ?>
    </header>

    <main class="content-wrapper">
        <div class="container product-detail-container">
            <div class="row">
                <div class="col-12 text-center text-white">

                    <img src="img/<?= htmlspecialchars($produk['gambar']); ?>" alt="<?= htmlspecialchars($produk['nama']); ?>" class="product-image-detail">
                    
                    <h1 class="display-4"><?= htmlspecialchars($produk['nama']); ?></h1>
                    
                    <p class="lead deskripsi-detail mt-3">
                        <?= nl2br(htmlspecialchars($produk['detail'])); ?>
                    </p>

                    <p class="harga-detail">
                        Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
                    </p>

                    <p class="stok-detail">
                        Status Ketersediaan: <?= htmlspecialchars($produk['stock']); ?>
                    </p>

                    <div class="mt-4">
                        <button class="btn btn-primary btn-lg" <?php if ($produk['stock'] == 'Habis') echo 'disabled'; ?>>
                            <i class="bi bi-cart-plus"></i> Checkout
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </main>
    
    <div class="footer-wrapper">
        <?php require 'footer.php'; ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>