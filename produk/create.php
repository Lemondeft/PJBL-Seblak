<?php
require_once '../auth/check.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];

    mysqli_query($conn, "INSERT INTO produk (nama, harga) VALUES ('$nama', $harga)");

    header("Location: index.php");
    exit;
}
?>

<?php include '../layout/header.php'; ?>
<h5>Tambah Produk</h5>

<form method="POST">
    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama" name="nama" required>
    </div>
    <div class="mb-3">
        <label for="harga" class="form-label">Harga</label>
        <input type="number" class="form-control" id="harga" name="harga" required>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
<?php include '../layout/footer.php';

?>