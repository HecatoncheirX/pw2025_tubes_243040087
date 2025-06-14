<?php
require 'db.php';
require 'session.php';

// Inisialisasi variabel.
$errors = '';
$id = $_GET['id']; // Ambil ID produk dari URL.

// Ambil data produk spesifik dari database berdasarkan ID.
$query = mysqli_query($conn, "SELECT a.*, b.nama as nama_kategori FROM produk a JOIN kategori b ON a.kategori_id = b.id WHERE a.id = '$id'");
$data = mysqli_fetch_array($query);

// Ambil semua kategori lain untuk pilihan dropdown (kecuali kategori produk saat ini).
$queryKategori = mysqli_query($conn, "SELECT * FROM kategori WHERE id != '" . $data['kategori_id'] . "'");

// === Logika saat form di-submit untuk MENYIMPAN PERUBAHAN ===
if (isset($_POST['simpan'])) {
    $errors = '';
    // Ambil data dari form.
    // Variabel ?? = memeriksa apakah sebuah variabel ada dan bukan kosong nilainya
    $nama = htmlspecialchars($_POST['nama'] ?? '');
    $kategori = htmlspecialchars($_POST['kategori'] ?? '');
    $harga = htmlspecialchars($_POST['harga'] ?? '');
    $detail = htmlspecialchars($_POST['detail'] ?? '');
    $stock = htmlspecialchars($_POST['stock'] ?? '');

    // Validasi input, mirip dengan halaman tambah produk.
    if (empty($nama)) $errors .= "Nama produk tidak boleh kosong.<br>";
    if (empty($kategori)) $errors .= "Kategori harus dipilih.<br>";
    if (empty($harga)) $errors .= "Harga tidak boleh kosong.<br>";
    elseif (!is_numeric($harga) || $harga <= 500) $errors .= "Harga harus berupa angka dan lebih dari 500.<br>";
    if (empty($detail)) $errors .= "Detail produk tidak boleh kosong.<br>";

    // === Logika Update Gambar (jika ada gambar baru diupload) ===
    $nama_file_unik = $data['gambar']; // Defaultnya adalah nama gambar lama.
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $gambar = $_FILES['gambar'];
        $nama_file = basename($gambar['name']);
        $ukuran_gambar = $gambar['size'];
        $tipe_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $tmp_file = $gambar['tmp_name'];

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if ($ukuran_gambar > 500000) $errors .= "Ukuran gambar tidak boleh lebih dari 500KB.<br>";
        if (!in_array($tipe_file, $allowed_types)) $errors .= "File gambar harus bertipe jpg, jpeg, png, atau gif.<br>";
        
        // Jika valid, buat nama file unik baru.
        if(empty($errors)) {
            $nama_file_unik = pathinfo($nama_file, PATHINFO_FILENAME) . "_" . uniqid() . "." . $tipe_file;
            $target_file = "img/" . $nama_file_unik;
        }
    }

    // Cek duplikat (jika tidak ada error validasi sebelumnya).
    if (empty($errors)) {
        $cekDuplikat = mysqli_query($conn, "SELECT nama FROM produk WHERE nama='$nama' AND kategori_id='$kategori' AND id != '$id'");
        if (mysqli_num_rows($cekDuplikat) > 0) $errors .= "Produk dengan nama ini sudah ada di kategori tersebut.<br>";
    }
} 
// === Logika saat tombol HAPUS di-klik ===
elseif (isset($_POST['hapus'])) {
    $queryHapus = mysqli_query($conn, "DELETE FROM produk WHERE id='$id'");
    if ($queryHapus) {
        // Hapus juga file gambar dari server untuk menghemat ruang.
        if ($data['gambar'] && file_exists("img/" . $data['gambar'])) {
            unlink("img/" . $data['gambar']);
        }
        echo "<div class='alert alert-success'>Produk berhasil dihapus.</div>";
        echo "<meta http-equiv='refresh' content='2; url=produk.php' />";
        exit; // Hentikan skrip.
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus produk.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Produk</title>
</head>
<body>
    <?php require 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Edit Produk</h2>
        <div class="col-12 col-md-8 mb-5">
            <!-- Form untuk mengedit produk, value diisi dengan data yang ada -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $data['nama']; ?>" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control">
                        <!-- Opsi pertama adalah kategori yang sedang dipilih -->
                        <option value="<?php echo $data['kategori_id']; ?>"><?php echo $data['nama_kategori']; ?></option>
                        <!-- Opsi lainnya adalah sisa kategori yang ada -->
                        <?php while ($dataKategori = mysqli_fetch_array($queryKategori)) {
                            echo "<option value='" . $dataKategori['id'] . "'>" . $dataKategori['nama'] . "</option>";
                        } ?>
                    </select>
                </div>  
                <div class="mb-3">
                    <label for="harga">Harga</label>
                    <input type="number" class="form-control" value="<?php echo $data['harga'] ?>" name="harga">
                </div>
                <div class="mb-3">
                    <div>
                        <label for="currentFoto">Gambar Produk Sekarang</label><br>
                        <img src="img/<?php echo $data['gambar'] ?>" alt="Gambar Produk" width="300px" class="img-thumbnail">
                    </div>
                    <label for="gambar" class="mt-2">Ganti Gambar (Opsional)</label>
                    <input type="file" name="gambar" id="gambar" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="detail">Detail Produk</label>
                    <textarea name="detail" id="detail" cols="30" rows="5" class="form-control"><?php echo $data['detail']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="stock">Ketersediaan Stock</label>
                    <select name="stock" id="stock" class="form-control">
                        <option value="tersedia" <?php echo ($data['stock'] == 'tersedia' ? 'selected' : ''); ?>>Tersedia</option>
                        <option value="habis" <?php echo ($data['stock'] == 'habis' ? 'selected' : ''); ?>>Habis</option>
                    </select>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" name="simpan">Simpan Perubahan</button>
                    <!-- Konfirmasi JavaScript sebelum menghapus -->
                    <button type="submit" class="btn btn-danger" name="hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                </div>
            </form>

            <?php
            // === Tampilkan Pesan Error atau Sukses Setelah Submit 'simpan' ===
            if (isset($_POST['simpan'])) {
                if (empty($errors)) {
                    // Jika tidak ada error, lakukan proses update.
                    // Jika ada gambar baru diupload, pindahkan filenya.
                    if (isset($target_file) && !empty($target_file) && isset($tmp_file)) {
                        if (move_uploaded_file($tmp_file, $target_file)) {
                            // Hapus gambar lama jika berhasil upload gambar baru.
                            if ($data['gambar'] && file_exists("img/" . $data['gambar'])) {
                                unlink("img/" . $data['gambar']);
                            }
                        } else {
                           echo "<div class='alert alert-danger mt-3'>Gagal mengupload gambar baru.</div>";
                           exit;
                        }
                    }

                    // Query untuk update data produk di database.
                    $update = mysqli_query($conn, "UPDATE produk SET nama = '$nama', kategori_id = '$kategori', harga = '$harga', gambar = '$nama_file_unik', detail = '$detail', stock = '$stock' WHERE id = '$id'");
                    if ($update) {
                        echo "<div class='alert alert-success mt-3'>Produk berhasil diupdate.</div>";
                        echo "<meta http-equiv='refresh' content='2; url=produk.php' />";
                    } else {
                        echo "<div class='alert alert-danger mt-3'>Gagal mengupdate produk.</div>";
                    }
                } else {
                    // Jika ada error validasi, tampilkan semua error.
                    echo "<div class='alert alert-danger mt-3'>$errors</div>";
                }
            }
            ?>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
