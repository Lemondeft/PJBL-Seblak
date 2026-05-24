<?php
require_once __DIR__ . '/../config/db.php';

function handleTopingActions(string $redirect = '../admin/settings.php'): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $action = $_POST['action'] ?? '';
    if (!in_array($action, ['toping_create', 'toping_update', 'toping_delete'], true)) {
        return;
    }

    global $conn;

    $uploadDir = __DIR__ . '/../assets/uploads/topping';
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
        $base = $base !== '' ? $base : 'toping';
        $suffix = date('YmdHis');
        $filename = $base . '_' . $suffix . ($extension ? '.' . $extension : '');
        $target = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $target)) {
            return null;
        }

        return $filename;
    };

    if ($action === 'toping_delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            mysqli_query($conn, "DELETE FROM seblak_prasmanan WHERE id = $id");
        }
        header("Location: $redirect");
        exit;
    }

    $nama = trim($_POST['nama'] ?? '');
    $harga = (int) ($_POST['harga'] ?? 0);
    $stok = (int) ($_POST['stok'] ?? 0);

    $namaEscaped = mysqli_real_escape_string($conn, $nama);

    if ($action === 'toping_create') {
        $gambar = $storeUpload('gambar');
        $gambarSql = $gambar ? "'" . mysqli_real_escape_string($conn, $gambar) . "'" : 'NULL';

        mysqli_query(
            $conn,
            "INSERT INTO seblak_prasmanan (nama, harga, stok, gambar)"
            . " VALUES ('$namaEscaped', $harga, $stok, $gambarSql)"
        );
        header("Location: $redirect");
        exit;
    }

    if ($action === 'toping_update') {
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
            "UPDATE seblak_prasmanan"
            . " SET nama = '$namaEscaped', harga = $harga, stok = $stok, gambar = $gambarSql"
            . " WHERE id = $id"
        );
        header("Location: $redirect");
        exit;
    }
}
