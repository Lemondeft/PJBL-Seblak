<?php
require_once '../auth/check.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['keranjang_prasmanan'])) {
    $_SESSION['keranjang_prasmanan'] = [];
}

$id_toping = isset($_POST['id_toping']) ? (int) $_POST['id_toping'] : 0;
$aksi = $_POST['aksi'] ?? '';

if ($id_toping <= 0 || !in_array($aksi, ['tambah', 'kurang'], true)) {
    echo json_encode([
        'status' => 'gagal',
        'pesan' => 'Permintaan tidak valid.'
    ]);
    exit;
}

$qty_sekarang = $_SESSION['keranjang_prasmanan'][$id_toping] ?? 0;

if ($aksi === 'tambah') {
    $qty_sekarang++;
} else {
    $qty_sekarang = max(0, $qty_sekarang - 1);
}

if ($qty_sekarang === 0) {
    unset($_SESSION['keranjang_prasmanan'][$id_toping]);
} else {
    $_SESSION['keranjang_prasmanan'][$id_toping] = $qty_sekarang;
}

$total_harga = 0;
if (!empty($_SESSION['keranjang_prasmanan'])) {
    $ids = array_keys($_SESSION['keranjang_prasmanan']);
    $ids = array_map('intval', $ids);
    $ids_list = implode(',', $ids);

    $result = mysqli_query($conn, "SELECT id, harga FROM seblak_prasmanan WHERE id IN ($ids_list)");
    $harga_map = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $harga_map[(int) $row['id']] = (int) $row['harga'];
        }
    }

    foreach ($_SESSION['keranjang_prasmanan'] as $id => $qty) {
        if (isset($harga_map[$id])) {
            $total_harga += $harga_map[$id] * $qty;
        }
    }
}

echo json_encode([
    'status' => 'sukses',
    'qty_baru' => $qty_sekarang,
    'total_harga' => $total_harga
]);
