<?php
require 'db.php';
require 'session.php'; 

// Mengambil ID kategori dari parameter URL (?id=...).
$id  = $_GET ['id'];

// Mengambil data kategori dari database berdasarkan ID yang didapat.
$query = mysqli_query($conn,"SELECT * FROM kategori WHERE id = '$id' ");
$data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Kategori</title>
</head>
<body>
    <?php require 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Detail Kategori</h2>
            <div class="col-12 col-md-6">
                <div>
                    <form action="" method="POST">
                        <label for="kategori">Kategori</label>
                        <input type="text" name="kategori" id="kategori" class="form-control" value="<?php echo $data['nama'] ?>">
                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary mb-3" name="editBtn">Edit</button>
                            <button type="submit" class="btn btn-danger mb-3" name="deleteBtn">Hapus</button>
                        </div>
                    </form>

                    <?php
                        // Logika jika tombol 'Edit' ditekan.
                        if(isset($_POST['editBtn'])) {
                            $kategori = htmlspecialchars($_POST['kategori']);

                            // Jika nama tidak berubah, kembali ke halaman kategori.
                            if($data['nama'] == $kategori) {
                                echo "<meta http-equiv='refresh' content='0; url=kategori.php' />";
                            }
                            else {
                                // Cek apakah nama baru sudah ada di database (validasi duplikat).
                                $query = mysqli_query($conn,"SELECT * FROM kategori WHERE nama = '$kategori'");
                                $jumlahData  = mysqli_num_rows($query);

                                if($jumlahData > 0) {
                                    echo"<div class='alert alert-warning mt-3' role='alert'>Kategori Sudah Ada</div>";
                                }
                                else {
                                    // Update nama kategori di database.
                                    $querySimpan = mysqli_query($conn,"UPDATE kategori SET nama = '$kategori' WHERE id = '$id'");
                                    if ($querySimpan) {
                                        echo "<div class='alert alert-primary' role='alert'>Kategori Berhasil Diupdate</div>";
                                        echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
                                    } else {
                                        echo mysqli_error($conn);
                                    }
                                }
                             }  
                        } 

                        // Logika jika tombol 'Hapus' ditekan.
                         if (isset($_POST["deleteBtn"])) {
                            // pengecekankategori ini sedang digunakan oleh produk.
                            $queryCheck = mysqli_query($conn, "SELECT * FROM produk WHERE kategori_id='$id'");
                            $hitungData = mysqli_num_rows($queryCheck);

                            if ($hitungData > 0) {
                                // Jika digunakan, tidak bisa dihapus.
                                echo "<div class='alert alert-danger' role='alert'>
                                        Kategori tidak bisa dihapus karena sedang digunakan dalam produk
                                    </div>";
                            } else {
                                // Jika tidak digunakan, hapus kategori dari database.
                                $queryDelete = mysqli_query($conn, "DELETE FROM kategori WHERE id = '$id'");
                                if ($queryDelete) {
                                    echo "<div class='alert alert-primary mt-3' role='alert'>Kategori Berhasil Dihapus</div>";
                                    echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
                                } else {
                                    echo "Error: " . mysqli_error($conn);
                                }
                            }
                        }
                    ?>
                </div>
            </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
