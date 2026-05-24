<?php
require_once __DIR__ . '/../config/db.php';

function handleCarouselActions(string $redirect = '../admin/settings.php'): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $action = $_POST['action'] ?? '';
    if (!in_array($action, ['carousel_create', 'carousel_update', 'carousel_delete'], true)) {
        return;
    }

    global $conn;

    $uploadDir = __DIR__ . '/../assets/uploads/carousel';
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
        $base = $base !== '' ? $base : 'carousel';
        $suffix = date('YmdHis');
        $filename = $base . '_' . $suffix . ($extension ? '.' . $extension : '');
        $target = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $target)) {
            return null;
        }

        return $filename;
    };

    if ($action === 'carousel_delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            mysqli_query($conn, "DELETE FROM carousel WHERE id = $id");
        }
        header("Location: $redirect");
        exit;
    }

    $urutan = (int) ($_POST['urutan'] ?? 0);
    $aktif = isset($_POST['aktif']) ? 1 : 0;

    if ($action === 'carousel_create') {
        $gambar = $storeUpload('gambar');
        if (!$gambar) {
            header("Location: $redirect");
            exit;
        }

        $gambarEscaped = mysqli_real_escape_string($conn, $gambar);
        mysqli_query(
            $conn,
            "INSERT INTO carousel (gambar, urutan, aktif)"
            . " VALUES ('$gambarEscaped', $urutan, $aktif)"
        );
        header("Location: $redirect");
        exit;
    }

    if ($action === 'carousel_update') {
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
            "UPDATE carousel"
            . " SET gambar = $gambarSql, urutan = $urutan, aktif = $aktif"
            . " WHERE id = $id"
        );
        header("Location: $redirect");
        exit;
    }
}
