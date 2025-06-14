<?php
require 'db.php';

// Mengambil kata kunci (keyword) dari permintaan GET AJAX.
// mysqli_real_escape_string digunakan untuk mencegah SQL injection.
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';

// Query untuk mencari produk di database yang namanya cocok (LIKE) dengan keyword.
$query = "SELECT * FROM produk WHERE nama LIKE '%$keyword%'";
$result = mysqli_query($conn, $query);

// Cek apakah ada produk yang ditemukan.
if (mysqli_num_rows($result) > 0) {
    // Looping untuk setiap produk yang ditemukan.
    while ($produk = mysqli_fetch_array($result)) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="image/<?= htmlspecialchars($produk['gambar']); ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama']); ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($produk['nama']); ?></h5>
                    <p class="card-text text-danger fw-bold">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                    <div class="mt-auto">
                        <a href="produk_detail_user.php?id=<?= $produk['id']; ?>" class="btn btn-outline-primary w-100">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    // Jika tidak ada produk yang ditemukan, tampilkan pesan peringatan.
    echo '<div class="col-12"><div class="alert alert-warning text-center" role="alert">Produk tidak ditemukan.</div></div>';
}
?>
