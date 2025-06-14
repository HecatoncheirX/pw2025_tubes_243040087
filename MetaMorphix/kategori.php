<?php
require 'db.php';
require 'session.php';


// Menginisialisasi variabel untuk pesan
$errors = '';
$sukses = '';

// Blok ini dieksekusi setelah form di-submit
if (isset($_POST['simpan_Kategori'])) {
    $kategori = htmlspecialchars(trim($_POST['kategori']));

    // 1. Validasi input: Pastikan tidak kosong
    if (empty($kategori)) {
        $errors = "Nama kategori tidak boleh kosong.";
    } 
    else {
        // 2. Validasi duplikat: Jika tidak kosong, cek apakah sudah ada di database
        $queryDataSudahAda = mysqli_query($conn, "SELECT nama FROM kategori WHERE nama = '$kategori'");
        
        if (mysqli_num_rows($queryDataSudahAda) > 0) {
            // Jika sudah ada, beri pesan error
            $errors = "Kategori dengan nama tersebut sudah ada.";
        }
        else {
            // 3. Jika valid (tidak kosong dan bukan duplikat), simpan ke database
            $querySimpan = mysqli_query($conn, "INSERT INTO kategori (nama) VALUES ('$kategori')");

            if ($querySimpan) {
                $sukses = "Kategori baru berhasil disimpan.";
                // Refresh halaman setelah 2 detik untuk menampilkan data baru dan membersihkan form
                echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
            } else {
                // Jika gagal menyimpan, beri pesan error dari database
                $errors = "Terjadi kesalahan saat menyimpan ke database: " . mysqli_error($conn);
            }
        }
    }
}

// Ambil semua data kategori untuk ditampilkan di tabel
$queryKategori = mysqli_query($conn, 'SELECT * FROM kategori');
$jumlahKategori = mysqli_num_rows($queryKategori);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .no-decoration {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php require 'navbar.php'; ?>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="../MetaMorphix" class="no-decoration text-muted">
                        <i class="bi bi-house"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Kategori
                </li>
            </ol>
        </nav>

        <div class="my-5 col-12 col-md-6">
            <h3>Tambah Kategori</h3>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="kategori">Kategori</label>
                    <input type="text" id="kategori" name="kategori" placeholder="Input Nama Kategori" class="form-control" autocomplete="off">
                </div>
                <div>
                    <button class="btn btn-primary" type="submit" name="simpan_Kategori">Simpan</button>
                </div>
            </form>

            <?php
            // Tampilkan pesan error jika ada
            if (!empty($errors)) {
                echo "<div class='alert alert-danger mt-3'>$errors</div>";
            }
            // Tampilkan pesan sukses jika ada
            if (!empty($sukses)) {
                echo "<div class='alert alert-success mt-3'>$sukses</div>";
            }
            ?>
        </div>

        <div class="mt-3">
            <h2>List Kategori</h2>
            <div class="table-responsive mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($jumlahKategori == 0) {
                            echo "<tr><td colspan='3' class='text-center'>Data Kategori Tidak Tersedia</td></tr>";
                        } else {
                            $angka = 1;
                            while ($data = mysqli_fetch_array($queryKategori)) {
                        ?>
                                <tr>
                                    <td><?php echo $angka++; ?></td>
                                    <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td>
                                        <a href="kategori_detail.php?id=<?php echo $data['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="bi bi-search"></i>
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
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
