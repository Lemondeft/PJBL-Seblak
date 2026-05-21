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

$id_paket = isset($_POST['id_paket']) ? (int) $_POST['id_paket'] : 0;
if ($id_paket <= 0) {
    header('Location: index.php');
    exit;
}

$user_id = getUserId($conn, $_SESSION['user_id'] ?? null, $_SESSION['user'] ?? null);
if (!$user_id) {
    header('Location: index.php');
    exit;
}

mysqli_query(
    $conn,
    "INSERT INTO pesanan (id_customer, id_seblak_paket, id_seblak_prasmanan, level_pedas, rasa)"
    . " VALUES ($user_id, $id_paket, NULL, NULL, NULL)"
);

header('Location: index.php');
exit;
