<?php
require_once '../auth/check.php';

$assetBase = '../assets';
include '../layout/head.php';
?>

<div class="w-full min-h-screen bg-white font-sans flex items-start justify-center p-6 sm:p-8">
    <div class="w-full max-w-2xl flex flex-col items-center text-center">
        <div class="w-full flex justify-start mb-4">
            <a href="../pesanan/index.php" class="bg-orange-500/10 p-2 rounded-full">
                <span class="icon-base icon-arrow text-orange-600"></span>
            </a>
        </div>

        <h1 class="text-2xl font-black text-gray-800">Selamat Anda Telah Bertransaksi</h1>

        <div class="mt-6 w-full h-44 flex items-center justify-center bg-center bg-contain bg-no-repeat" style="background-image: url('<?= $assetBase ?>/icons/horaay.png');">
            <img src="<?= $assetBase ?>/icons/logo.png" class="w-32 h-32 object-contain" alt="Seblak Logo">
        </div>

        <p class="text-sm text-gray-500 mt-4">Pembayaran berhasil diproses.</p>

        <div class="mt-10 w-full space-y-3">
            <a href="../pesanan/index.php" class="block w-full bg-orange-500 text-white font-extrabold text-base py-3 rounded-2xl text-center shadow-md hover:bg-orange-600 transition-all">
                Kembali
            </a>
            <a href="../histori.php" class="block text-sm text-orange-600">Berikan ulasan</a>
        </div>
    </div>
</div>

</body>
</html>
