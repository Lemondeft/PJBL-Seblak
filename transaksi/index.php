<?php
require_once '../auth/check.php';
require_once '../core/transaksi.php';

$assetBase = '../assets';
$baseUrl = '..';
$activeNav = 'transaksi';
include '../layout/header.php';
?>

<div class="min-h-screen bg-[#f8f9fa] p-6 font-sans">
	<h1 class="text-2xl font-semibold text-gray-800">Metode Transaksi</h1>
	<p class="text-sm text-gray-600 mt-2">Metode transaksi akan ditampilkan di sini.</p>
</div>

<?php include '../layout/footer.php'; ?>