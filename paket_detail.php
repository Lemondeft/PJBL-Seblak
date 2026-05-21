<?php
require_once 'auth/check.php';
require_once 'config/db.php';

$assetBase = 'assets';
$baseUrl = '.';
$activeNav = 'home';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$paket_result = mysqli_query(
    $conn,
    "SELECT id, nama, deskripsi, harga, stok, gambar FROM seblak_paket WHERE id = $id LIMIT 1"
);

$paket = $paket_result ? mysqli_fetch_assoc($paket_result) : null;
if (!$paket) {
    header('Location: index.php');
    exit;
}

$ulasan_result = mysqli_query(
    $conn,
    "SELECT u.username, ul.rating, ul.komentar, ul.tanggal_ulasan"
    . " FROM ulasan ul"
    . " JOIN users u ON ul.id_customer = u.id"
    . " WHERE ul.id_seblak_paket = $id"
    . " ORDER BY ul.tanggal_ulasan DESC"
);

$ulasan_count = 0;
$ulasan_rows = [];
if ($ulasan_result) {
    while ($row = mysqli_fetch_assoc($ulasan_result)) {
        $ulasan_rows[] = $row;
        $ulasan_count += 1;
    }
}

include 'layout/header.php';
?>

<div class="min-h-screen bg-[#f8f9fa] p-6 font-sans">
    <div class="mx-auto w-full max-w-md">
        <div class="flex items-center gap-3 mb-4">
            <a href="index.php" class="bg-orange-500/10 p-2 rounded-full">
                <span class="icon-base icon-arrow text-orange-600"></span>
            </a>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="relative">
                <div class="h-44 bg-gray-200">
                    <?php if (!empty($paket['gambar'])): ?>
                        <img src="<?= htmlspecialchars($paket['gambar']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($paket['nama']) ?>">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
                    <?php endif; ?>
                </div>
                <form method="POST" action="pesanan/add_paket.php" class="absolute left-4 -bottom-4">
                    <input type="hidden" name="id_paket" value="<?= (int) $paket['id'] ?>">
                    <button type="submit" class="bg-orange-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-sm">Tambah</button>
                </form>
            </div>

            <div class="pt-8 px-5 pb-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($paket['nama']) ?></h1>
                        <div class="text-xs text-gray-500 mt-1">Tersedia: <?= (int) $paket['stok'] ?></div>
                    </div>
                    <div class="text-xs font-semibold text-orange-600">IDR <?= number_format((int) $paket['harga'], 0, ',', '.') ?></div>
                </div>

                <div class="mt-4">
                    <h2 class="text-xs font-bold text-gray-700">Deskripsi Produk</h2>
                    <p class="text-xs text-gray-500 mt-2"><?= htmlspecialchars($paket['deskripsi'] ?? '-') ?></p>
                </div>

                <div class="mt-5 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-700">Ulasan</h2>
                    <span class="text-xs text-gray-500">(<?= $ulasan_count ?> ulasan)</span>
                </div>
            </div>
        </div>

        <div class="mt-4 space-y-3">
            <?php if (empty($ulasan_rows)): ?>
                <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center text-xs text-gray-500">
                    Belum ada ulasan untuk paket ini.
                </div>
            <?php else: ?>
                <?php foreach ($ulasan_rows as $ulasan): ?>
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold text-gray-800"><?= htmlspecialchars($ulasan['username']) ?></div>
                            <div class="text-[10px] text-gray-400">
                                <?= date('d/m/Y', strtotime($ulasan['tanggal_ulasan'])) ?>
                            </div>
                        </div>
                        <div class="mt-2 text-orange-500 text-xs">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?= $i <= (int) $ulasan['rating'] ? '★' : '☆' ?>
                            <?php endfor; ?>
                        </div>
                        <p class="text-xs text-gray-600 mt-2"><?= htmlspecialchars($ulasan['komentar'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
