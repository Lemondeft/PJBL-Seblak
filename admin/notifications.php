<?php
require_once '../auth/check.php';
require_once '../config/db.php';

date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

if (($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['ok' => false, 'error' => 'unauthorized']);
    exit;
}

$since_id = isset($_GET['since_id']) ? (int) $_GET['since_id'] : 0;

$result = mysqli_query(
    $conn,
    "SELECT t.id, t.total_harga, t.tanggal_pesanan, u.username"
    . " FROM transaksi t"
    . " JOIN pesanan p ON t.id_pesanan = p.id"
    . " JOIN users u ON p.id_customer = u.id"
    . " WHERE t.id > $since_id"
    . " ORDER BY t.id DESC"
    . " LIMIT 5"
);

$items = [];
$latest_id = $since_id;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) ($row['id'] ?? 0);
        if ($id > $latest_id) {
            $latest_id = $id;
        }
        $items[] = [
            'id' => $id,
            'username' => $row['username'] ?? '-',
            'total' => (int) ($row['total_harga'] ?? 0),
            'date' => $row['tanggal_pesanan'] ?? ''
        ];
    }
}

echo json_encode([
    'ok' => true,
    'latest_id' => $latest_id,
    'items' => array_reverse($items)
]);
