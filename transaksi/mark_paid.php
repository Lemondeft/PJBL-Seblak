<?php
require_once '../auth/check.php';
require_once '../config/db.php';

function getUserId(mysqli $conn, ?int $sessionId, ?string $username): ?int {
    if ($sessionId) {
        return $sessionId;
    }

    if (!$username) {
        return null;
    }

    $escaped = mysqli_real_escape_string($conn, $username);
    $result = mysqli_query($conn, "SELECT id FROM users WHERE username = '$escaped' LIMIT 1");

    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return (int) $row['id'];
    }

    return null;
}

$user_id = getUserId($conn, $_SESSION['user_id'] ?? null, $_SESSION['user'] ?? null);
if (!$user_id) {
    header('Location: qris.php');
    exit;
}

$pesanan_result = mysqli_query(
    $conn,
    "SELECT p.id, p.id_seblak_paket, sp.harga AS paket_harga"
    . " FROM pesanan p"
    . " LEFT JOIN seblak_paket sp ON p.id_seblak_paket = sp.id"
    . " WHERE p.id_customer = $user_id"
);

if ($pesanan_result) {
    while ($pesanan = mysqli_fetch_assoc($pesanan_result)) {
        $pesanan_id = (int) $pesanan['id'];

        $exists = mysqli_query($conn, "SELECT 1 FROM transaksi WHERE id_pesanan = $pesanan_id LIMIT 1");
        if ($exists && mysqli_num_rows($exists) > 0) {
            continue;
        }

        $jumlah = 1;
        $total = 0;

        if (!empty($pesanan['id_seblak_paket'])) {
            $total = (int) ($pesanan['paket_harga'] ?? 0);
        } else {
            $detail_result = mysqli_query(
                $conn,
                "SELECT SUM(d.kuantitas) AS qty_total, SUM(d.kuantitas * t.harga) AS total_harga"
                . " FROM pesanan_prasmanan_detail d"
                . " JOIN seblak_prasmanan t ON d.id_toping = t.id"
                . " WHERE d.id_pesanan = $pesanan_id"
            );

            if ($detail_result && ($detail = mysqli_fetch_assoc($detail_result))) {
                $jumlah = (int) ($detail['qty_total'] ?? 0);
                $total = (int) ($detail['total_harga'] ?? 0);
            }
        }

        mysqli_query(
            $conn,
            "INSERT INTO transaksi (id_pesanan, jumlah_pesanan, total_harga, tanggal_pesanan)"
            . " VALUES ($pesanan_id, $jumlah, $total, CURDATE())"
        );
    }
}

header('Location: success.php');
exit;
