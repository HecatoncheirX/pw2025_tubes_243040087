<?php
    require 'db.php';
    require 'session.php';

    // Inisialisasi variabel untuk pesan.
    $errors = '';
    $sukses = '';

    // === Logika saat Form Penambahan Produk di-submit ===
    if (isset($_POST['simpan'])) {
        $errors = '';

        // Mengambil data dari form dan melindunginya dengan htmlspecialchars.
        $nama = htmlspecialchars($_POST['nama'] ?? '');
        $kategori = htmlspecialchars($_POST['kategori'] ?? '');
        $harga = htmlspecialchars($_POST['harga'] ?? '');
        $detail = htmlspecialchars($_POST['detail'] ?? '');
        $stock = htmlspecialchars($_POST['stock'] ?? '');

        // === Validasi Input Form ===
        if (empty($nama)) $errors .= "Nama produk tidak boleh kosong.<br>";
        if (empty($kategori)) $errors .= "Kategori harus dipilih.<br>";
        if (empty($harga)) $errors .= "Harga tidak boleh kosong.<br>";
        elseif (!is_numeric($harga) || $harga <= 500) $errors .= "Harga harus berupa angka dan lebih dari 500.<br>";
        if (empty($detail)) $errors .= "Detail produk tidak boleh kosong.<br>";

        // === Validasi Gambar ===
        $nama_file_unik = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
            $gambar = $_FILES['gambar'];
            $nama_file = basename($gambar['name']);
            $ukuran_gambar = $gambar['size'];
            $tipe_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
            $tmp_file = $gambar['tmp_name'];

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if ($ukuran_gambar > 500000) $errors .= "Ukuran gambar tidak boleh lebih dari 500KB.<br>"; // 500KB
            if (!in_array($tipe_file, $allowed_types)) $errors .= "File gambar harus bertipe jpg, jpeg, png, atau gif.<br>";
            
            // Jika tidak ada error validasi sebelumnya, buat nama file unik.
            if(empty($errors)) {
                $nama_file_unik = pathinfo($nama_file, PATHINFO_FILENAME) . "_" . uniqid() . "." . $tipe_file;
                $target_folder = "img/";
                $target_file = $target_folder . $nama_file_unik;
            }
        } else {
            $errors .= "Gambar harus diunggah.<br>";
        }

        // === Logika Penyimpanan Data ke Database ===
        // Hanya berjalan jika tidak ada error dari validasi di atas.
        if (empty($errors)) {
            // Cek duplikat produk (nama dan kategori yang sama).
            $cekDuplikat = mysqli_query($conn, "SELECT nama FROM produk WHERE nama='$nama' AND kategori_id='$kategori'");
            if (mysqli_num_rows($cekDuplikat) > 0) {
                $errors .= "Produk dengan nama ini sudah ada di kategori tersebut.<br>";
            } else {
                // Pindahkan file gambar yang diupload ke folder tujuan.
                if (move_uploaded_file($tmp_file, $target_file)) {
                    // Masukkan data produk baru ke database.
                    mysqli_query($conn, "INSERT INTO produk (nama, kategori_id, harga, gambar, detail, stock) 
                                         VALUES ('$nama', '$kategori', '$harga', '$nama_file_unik', '$detail', '$stock')");
                    $sukses = "Produk berhasil ditambahkan.";
                    echo "<meta http-equiv='refresh' content='2; url=produk.php' />";
                } else {
                    $errors = "Terjadi kesalahan saat mengupload gambar.";
                }
            }
        }
    }
    
    // === Logika untuk Menampilkan dan Mencari Produk ===
    $kategori_semua = mysqli_query($conn, 'SELECT * FROM kategori');

    // Query dasar untuk mengambil data produk dengan join ke tabel kategori.
    $baseQuery = 'SELECT a.*, b.nama AS nama_kategori FROM produk a JOIN kategori b ON a.kategori_id = b.id';
    $conditions = [];

    // Jika ada keyword pencarian, tambahkan kondisi WHERE.
    $keyword = '';
    if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
        $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
        $conditions[] = "a.nama LIKE '%$keyword%'";
    }

    // Jika ada filter kategori, tambahkan kondisi WHERE.
    $filter_kategori = '';
    if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
        $filter_kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
        $conditions[] = "a.kategori_id = '$filter_kategori'";
    }

    // Gabungkan semua kondisi menjadi satu query.
    if (count($conditions) > 0) {
        $baseQuery .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $queryProduk = mysqli_query($conn, $baseQuery);
    $jumlahProduk = mysqli_num_rows($queryProduk);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Produk</title>
</head>
<body>
    <?php require 'navbar.php'; ?>
    <div class="container mt-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../MetaMorphix" class="no-decoration text-muted"><i class="bi bi-house"></i> Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>
        
        <!-- Form Tambah Produk -->
        <div class="my-5 col-12 col-md-8">
            <h3>Tambah Produk</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <!-- Input fields untuk data produk -->
                <div class="mb-3">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="kategori-form">Kategori</label>
                    <select name="kategori" id="kategori-form" class="form-control">
                        <option value="">Pilih Kategori</option>
                        <?php 
                        mysqli_data_seek($kategori_semua, 0); // Reset pointer
                        while ($data = mysqli_fetch_array($kategori_semua)) { ?>
                            <option value="<?php echo $data['id']; ?>"><?php echo $data['nama']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="harga">Harga</label>
                    <input type="number" class="form-control" name="harga">
                </div>
                <div class="mb-3">
                    <label for="gambar">Masukan Gambar</label>
                    <input type="file" name="gambar" id="gambar" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="detail">Masukan Detail Produk</label>
                    <textarea name="detail" id="detail" cols="30" rows="5" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="stock">Ketersediaan Stock</label>
                    <select name="stock" id="stock" class="form-control">
                        <option value="tersedia">Tersedia</option>
                        <option value="habis">Habis</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" name="simpan">Simpan</button>
                </div>
            </form>
            <!-- Tampilkan pesan error atau sukses -->
            <?php
            if (!empty($errors)) echo "<div class='alert alert-danger mt-3'>$errors</div>";
            if (!empty($sukses)) echo "<div class='alert alert-success mt-3'>$sukses</div>";
            ?>
        </div>

        <!-- Daftar Produk -->
        <div class="mt-3">
            <h2>List Produk</h2>
            <!-- Form Pencarian dan Filter -->
            <div class="row mt-4">
                <div class="col-12">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-5 mb-2"><input type="text" class="form-control" placeholder="Cari nama produk..." name="keyword" value="<?php echo htmlspecialchars($keyword); ?>"></div>
                            <div class="col-md-5 mb-2">
                                <select name="kategori" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    <?php 
                                    mysqli_data_seek($kategori_semua, 0); 
                                    while ($data = mysqli_fetch_array($kategori_semua)) { ?>
                                        <option value="<?php echo $data['id']; ?>" <?php echo ($filter_kategori == $data['id']) ? 'selected' : ''; ?>>
                                            <?php echo $data['nama']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2"><button class="btn btn-primary w-100" type="submit">Cari</button></div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabel Produk -->
            <div class="table-responsive mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Ketersediaan Stok</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($jumlahProduk == 0) {
                        echo "<tr><td colspan='6' class='text-center'>Data Produk Tidak Ditemukan</td></tr>";
                    } else {
                        $jumlah = 1;
                        while ($data = mysqli_fetch_array($queryProduk)) { ?>
                            <tr>
                                <td><?php echo $jumlah++; ?></td>
                                <td><?php echo $data['nama']; ?></td>
                                <td><?php echo $data['nama_kategori']; ?></td>
                                <td>Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $data['stock']; ?></td>
                                <td><a href='produk_detail.php?id=<?php echo $data['id']; ?>' class='btn btn-info btn-sm' title="Lihat Detail"><i class='bi bi-search'></i></a></td>
                            </tr>
                        <?php }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
