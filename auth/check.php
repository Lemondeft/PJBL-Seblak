<?php
session_start();

if (!isset($_SESSION['user'])) {
    // Menggunakan path relatif agar aman di dalam sub-folder project
    $current_dir = dirname($_SERVER['SCRIPT_NAME']);
    if (str_contains($current_dir, '/pesanan') || str_contains($current_dir, '/auth') || str_contains($current_dir, '/transaksi') || str_contains($current_dir, '/produk') || str_contains($current_dir, '/customer') || str_contains($current_dir, '/admin') || str_contains($current_dir, '/user')) {
        header("Location: ../auth/login.php");
    } else {
        header("Location: auth/login.php");
    }
    exit;
}