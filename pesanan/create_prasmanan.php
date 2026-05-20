<?php
require_once '../auth/check.php';
require_once '../config/db.php';

if (!isset($_SESSION['keranjang_prasmanan'])) {
    $_SESSION['keranjang_prasmanan'] = [];
}

$total_harga_keranjang = 0;
$query = "SELECT * FROM seblak_prasmanan WHERE stok > 0";
$result = mysqli_query($conn, $query);

$embedded = isset($_GET['embedded']);
$assetBase = '../assets';
$baseUrl = '..';
$activeNav = 'pesanan';

if ($embedded) {
    include '../layout/head.php';
} else {
    include '../layout/header.php';
}
?>

<div class="w-full <?= $embedded ? '' : 'min-h-screen bg-white' ?> font-sans">
    <div class="mx-auto w-full max-w-3xl px-4 py-4 sm:px-6 lg:px-10">
        <?php if (!$embedded): ?>
            <div class="flex items-center justify-between mb-4">
                <a href="index.php" class="bg-orange-500/10 p-2 rounded-full">
                    <span class="icon-base icon-arrow text-orange-600"></span>
                </a>
                <h2 class="text-lg font-black text-gray-800 tracking-wide">PESAN</h2>
                <div class="w-9 h-9"></div>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/_prasmanan_panel.php'; ?>
    </div>
</div>

<?php if ($embedded): ?>
</body>
</html>
<?php else: ?>
<?php include '../layout/footer.php'; ?>
<?php endif; ?>
