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

$id_pesanan = isset($_POST['id_pesanan']) ? (int) $_POST['id_pesanan'] : 0;
$user_id = getUserId($conn, $_SESSION['user_id'] ?? null, $_SESSION['user'] ?? null);

if ($id_pesanan > 0 && $user_id) {
    $exists = mysqli_query(
        $conn,
        "SELECT 1 FROM transaksi WHERE id_pesanan = $id_pesanan LIMIT 1"
    );

    if (!$exists || mysqli_num_rows($exists) === 0) {
        mysqli_query(
            $conn,
            "DELETE FROM pesanan WHERE id = $id_pesanan AND id_customer = $user_id"
        );
    }
}

header('Location: index.php');
exit;
