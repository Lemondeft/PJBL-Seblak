<?php
require_once '../auth/check.php';

$assetBase = '../assets';
include '../layout/head.php';

$invoice = 'INV-' . date('ymd') . '-' . random_int(1000, 9999);
?>

<div class="w-full min-h-screen bg-white font-sans flex items-start justify-center p-6 sm:p-8">
    <div class="w-full max-w-2xl flex flex-col">
        <div class="flex items-center gap-4 mb-6">
            <a href="confirm.php" class="bg-orange-500/10 p-2 rounded-full">
                <span class="icon-base icon-arrow text-orange-600"></span>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-800">QRIS</h1>
                <p class="text-sm text-gray-500">Scan untuk pembayaran</p>
            </div>
        </div>

        <div class="text-base text-gray-700 text-center leading-snug">
            Setelah transaksi QRIS telah dibayar, kamu akan langsung diarahkan ke halaman selanjutnya.
        </div>

        <div class="mt-6 flex flex-col items-center">
            <div class="text-sm font-bold text-gray-800">SEBLAK PUSPOGIWANG</div>
            <div class="text-xs text-gray-500">NMI: <?= htmlspecialchars($invoice) ?></div>
        </div>

        <div class="mt-6 flex items-center justify-center">
            <div class="w-64 h-64 sm:w-72 sm:h-72 bg-gray-100 rounded-2xl border border-gray-200 flex items-center justify-center">
                <svg width="180" height="180" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="120" height="120" fill="white"/>
                    <rect x="6" y="6" width="30" height="30" fill="#111"/>
                    <rect x="84" y="6" width="30" height="30" fill="#111"/>
                    <rect x="6" y="84" width="30" height="30" fill="#111"/>
                    <rect x="44" y="44" width="32" height="32" fill="#111"/>
                    <rect x="44" y="6" width="10" height="10" fill="#111"/>
                    <rect x="66" y="6" width="10" height="10" fill="#111"/>
                    <rect x="6" y="44" width="10" height="10" fill="#111"/>
                    <rect x="104" y="44" width="10" height="10" fill="#111"/>
                    <rect x="44" y="104" width="10" height="10" fill="#111"/>
                    <rect x="66" y="104" width="10" height="10" fill="#111"/>
                </svg>
            </div>
        </div>

        <div class="mt-4 text-xs text-gray-500 text-center">
            Satu QRIS untuk semua. Cek aplikasi penyelenggara.
        </div>

        <div class="mt-10">
            <a href="mark_paid.php" class="block w-full bg-orange-500 text-white font-extrabold text-base py-3 rounded-2xl text-center shadow-md hover:bg-orange-600 transition-all">
                Saya Sudah Bayar
            </a>
        </div>
    </div>
</div>

</body>
</html>
