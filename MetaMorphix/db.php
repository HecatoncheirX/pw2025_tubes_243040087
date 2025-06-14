<?php

$conn = mysqli_connect('localhost', 'root', '', 'ecommerce');

if (!$conn) {
  die("Koneksi Gagal: " . mysqli_connect_error());
}
?>