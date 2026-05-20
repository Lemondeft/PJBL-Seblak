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

if (empty($_SESSION['keranjang_prasmanan'])) {
    header('Location: index.php');
    exit;
}

$user_id = getUserId($conn, $_SESSION['user_id'] ?? null, $_SESSION['user'] ?? null);
if (!$user_id) {
    header('Location: index.php');
    exit;
}

$insert = mysqli_query(
    $conn,
    "INSERT INTO pesanan (id_customer, id_seblak_paket, id_seblak_prasmanan) VALUES ($user_id, NULL, NULL)"
);

if (!$insert) {
    header('Location: index.php');
    exit;
}

$pesanan_id = (int) mysqli_insert_id($conn);
foreach ($_SESSION['keranjang_prasmanan'] as $id_toping => $qty) {
    $id_toping = (int) $id_toping;
    $qty = (int) $qty;
    if ($id_toping > 0 && $qty > 0) {
        mysqli_query(
            $conn,
            "INSERT INTO pesanan_prasmanan_detail (id_pesanan, id_toping, kuantitas) VALUES ($pesanan_id, $id_toping, $qty)"
        );
    }
}

$_SESSION['keranjang_prasmanan'] = [];

header('Location: index.php');
exit;
