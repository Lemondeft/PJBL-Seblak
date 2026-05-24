<?php
require_once __DIR__ . '/../config/db.php';

function handlePaketActions(string $redirect = '../admin/settings.php'): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $action = $_POST['action'] ?? '';
    if (!in_array($action, ['paket_create', 'paket_update', 'paket_delete'], true)) {
        return;
    }

    global $conn;

    $uploadDir = __DIR__ . '/../assets/uploads/paket';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $storeUpload = function (string $inputName) use ($uploadDir): ?string {
        if (empty($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $original = $_FILES[$inputName]['name'] ?? '';
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $base = pathinfo($original, PATHINFO_FILENAME);
        $base = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $base);
        $base = trim($base, '_');
        $base = $base !== '' ? $base : 'paket';
        $suffix = date('YmdHis');
        $filename = $base . '_' . $suffix . ($extension ? '.' . $extension : '');
        $target = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $target)) {
            return null;
        }

        return 'assets/uploads/paket/' . $filename;
    };

    if ($action === 'paket_delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            mysqli_query($conn, "DELETE FROM seblak_paket WHERE id = $id");
        }
        header("Location: $redirect");
        exit;
    }

    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = (int) ($_POST['harga'] ?? 0);
    $stok = (int) ($_POST['stok'] ?? 0);

    $namaEscaped = mysqli_real_escape_string($conn, $nama);
    $deskripsiEscaped = mysqli_real_escape_string($conn, $deskripsi);

    if ($action === 'paket_create') {
        $gambar = $storeUpload('gambar');
        $gambarSql = $gambar ? "'" . mysqli_real_escape_string($conn, $gambar) . "'" : 'NULL';

        mysqli_query(
            $conn,
            "INSERT INTO seblak_paket (nama, deskripsi, harga, stok, gambar)"
            . " VALUES ('$namaEscaped', '$deskripsiEscaped', $harga, $stok, $gambarSql)"
        );
        header("Location: $redirect");
        exit;
    }

    if ($action === 'paket_update') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: $redirect");
            exit;
        }

        $existing = trim($_POST['existing_gambar'] ?? '');
        $gambar = $storeUpload('gambar');
        $finalGambar = $gambar ?: $existing;
        $gambarSql = $finalGambar !== '' ? "'" . mysqli_real_escape_string($conn, $finalGambar) . "'" : 'NULL';

        mysqli_query(
            $conn,
            "UPDATE seblak_paket"
            . " SET nama = '$namaEscaped', deskripsi = '$deskripsiEscaped', harga = $harga, stok = $stok, gambar = $gambarSql"
            . " WHERE id = $id"
        );
        header("Location: $redirect");
        exit;
    }
}
