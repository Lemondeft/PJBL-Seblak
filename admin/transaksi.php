<?php
require_once '../auth/check.php';

date_default_timezone_set('Asia/Jakarta');

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$assetBase = '../assets';
$baseUrl = '..';
include '../layout/header.php';
?>

<div class="min-h-screen bg-[#f8f9fa] p-6 font-sans">
    <h1 class="text-2xl font-semibold text-gray-800">Pantau Transaksi</h1>
</div>

<?php include '../layout/footer.php'; ?>
