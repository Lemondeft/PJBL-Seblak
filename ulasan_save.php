<?php
require_once 'auth/check.php';
require_once 'config/db.php';

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
$id_paket = isset($_POST['id_seblak_paket']) ? (int) $_POST['id_seblak_paket'] : 0;
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
$komentar = trim($_POST['komentar'] ?? '');

if (!$user_id || $id_paket <= 0 || $rating < 1 || $rating > 5) {
    header('Location: histori.php');
    exit;
}

$komentar_escaped = mysqli_real_escape_string($conn, $komentar);

mysqli_query(
    $conn,
    "INSERT INTO ulasan (id_customer, id_seblak_paket, rating, komentar)"
    . " VALUES ($user_id, $id_paket, $rating, '$komentar_escaped')"
);

header('Location: histori.php');
exit;
