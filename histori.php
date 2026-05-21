<?php
require_once 'auth/check.php';
require_once 'config/db.php';

$assetBase = 'assets';
$baseUrl = '.';
$activeNav = 'histori';

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
$orders = [];

if ($user_id) {
    $transaksi_result = mysqli_query(
        $conn,
        "SELECT t.id AS transaksi_id, t.id_pesanan, t.jumlah_pesanan, t.total_harga, t.tanggal_pesanan,"
        . " p.id_seblak_paket, sp.nama AS paket_nama"
        . " FROM transaksi t"
        . " JOIN pesanan p ON t.id_pesanan = p.id"
        . " LEFT JOIN seblak_paket sp ON p.id_seblak_paket = sp.id"
        . " WHERE p.id_customer = $user_id"
        . " ORDER BY t.id DESC"
    );

    if ($transaksi_result) {
        while ($transaksi = mysqli_fetch_assoc($transaksi_result)) {
            $is_paket = !empty($transaksi['id_seblak_paket']);
            $label = $is_paket ? ($transaksi['paket_nama'] ?? 'Paket Seblak') : 'Seblak Prasmanan';

            $orders[] = [
                'id' => (int) $transaksi['id_pesanan'],
                'transaksi_id' => (int) $transaksi['transaksi_id'],
                'paket_id' => $is_paket ? (int) $transaksi['id_seblak_paket'] : null,
                'label' => $label,
                'total' => (int) ($transaksi['total_harga'] ?? 0),
                'tanggal' => $transaksi['tanggal_pesanan'] ?? '',
                'status' => 'Selesai',
                'metode' => 'QRIS',
                'jumlah' => (int) ($transaksi['jumlah_pesanan'] ?? 1),
                'is_paket' => $is_paket
            ];
        }
    }
}

$rate_id = isset($_GET['rate']) ? (int) $_GET['rate'] : 0;
$rate_order = null;
foreach ($orders as $order) {
    if (($order['transaksi_id'] ?? 0) === $rate_id && !empty($order['paket_id'])) {
        $rate_order = $order;
        break;
    }
}

include 'layout/header.php';
?>

<div class="min-h-screen bg-[#f8f9fa] p-6 font-sans">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
        <div class="flex-1">
            <h1 class="text-2xl font-semibold text-gray-800">Histori</h1>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="bg-orange-500 text-white rounded-2xl p-4 shadow-sm">
                    <div class="text-xs uppercase tracking-wide">Pembelian Berhasil</div>
                    <div class="text-2xl font-black mt-2"><?= count($orders) ?></div>
                    <div class="text-[10px] text-orange-100">Dalam 3 bulan</div>
                </div>
                <div class="bg-orange-500 text-white rounded-2xl p-4 shadow-sm">
                    <div class="text-xs uppercase tracking-wide">Pembelian Dibatalkan</div>
                    <div class="text-2xl font-black mt-2">0</div>
                    <div class="text-[10px] text-orange-100">Dalam 3 bulan</div>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-sm font-bold text-gray-700 mb-3">List Produk</h2>
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-x-auto">
                    <table class="min-w-full text-xs text-gray-700">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-left">Order Id</th>
                                <th class="px-3 py-2 text-left">Tanggal</th>
                                <th class="px-3 py-2 text-left">Jumlah</th>
                                <th class="px-3 py-2 text-left">Metode Pembayaran</th>
                                <th class="px-3 py-2 text-left">Status</th>
                                <th class="px-3 py-2 text-left">Harga</th>
                                <th class="px-3 py-2 text-left">Tinggalkan Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="px-3 py-4 text-center text-gray-500">Belum ada histori.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $index => $order): ?>
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 font-semibold text-gray-800"><?= htmlspecialchars($order['label']) ?></td>
                                        <td class="px-3 py-2">#<?= str_pad((string) $order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-3 py-2">
                                            <?= $order['tanggal'] ? date('d-m-y', strtotime($order['tanggal'])) : '-' ?>
                                        </td>
                                        <td class="px-3 py-2"><?= (int) $order['jumlah'] ?></td>
                                        <td class="px-3 py-2"><?= htmlspecialchars($order['metode']) ?></td>
                                        <td class="px-3 py-2">
                                            <span class="bg-orange-500 text-white text-[10px] px-2 py-1 rounded-full"><?= htmlspecialchars($order['status']) ?></span>
                                        </td>
                                        <td class="px-3 py-2">IDR <?= number_format($order['total'], 0, ',', '.') ?></td>
                                        <td class="px-3 py-2">
                                            <?php if (!empty($order['paket_id'])): ?>
                                                <a href="histori.php?rate=<?= (int) $order['transaksi_id'] ?>" class="bg-orange-500 text-white text-[10px] px-2 py-1 rounded-full">Rating</a>
                                            <?php else: ?>
                                                <span class="text-[10px] text-gray-400">Tidak tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

                <?php if ($rate_order): ?>
                    <div x-data="{ ratingOpen: true }">
                        <div x-show="ratingOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
                            <div class="w-full max-w-sm bg-white rounded-2xl border border-gray-200 shadow-xl p-5">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-sm font-bold text-gray-700">Rating: <?= htmlspecialchars($rate_order['label']) ?></h2>
                                    <button type="button" @click="ratingOpen = false" class="text-gray-500 text-lg">&times;</button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Kami sangat menghargai penilaian Anda.</p>

                                <form method="POST" action="ulasan_save.php" class="mt-4">
                                    <input type="hidden" name="id_seblak_paket" value="<?= (int) $rate_order['paket_id'] ?>">
                                    <input type="hidden" name="rating" id="rating-input" value="0">

                                    <label class="block text-xs font-semibold text-gray-600">Komentar</label>
                                    <textarea name="komentar" rows="3" class="w-full mt-2 border border-gray-300 rounded-xl p-2 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Masukkan kesan dan pesan Anda..."></textarea>

                                    <div class="mt-4">
                                        <div class="text-xs font-semibold text-gray-600">Rating Bintang</div>
                                        <div class="flex items-center gap-2 mt-2 text-orange-500" id="rating-stars">
                                            <button type="button" data-rating="1" class="text-lg">&#9734;</button>
                                            <button type="button" data-rating="2" class="text-lg">&#9734;</button>
                                            <button type="button" data-rating="3" class="text-lg">&#9734;</button>
                                            <button type="button" data-rating="4" class="text-lg">&#9734;</button>
                                            <button type="button" data-rating="5" class="text-lg">&#9734;</button>
                                        </div>
                                    </div>

                                    <button type="submit" class="mt-5 w-full bg-orange-500 text-white text-sm font-bold py-2 rounded-2xl shadow-sm hover:bg-orange-600 transition-all">
                                        Kirim
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div x-show="ratingOpen" @click="ratingOpen = false" class="fixed inset-0 bg-black/30 z-40 backdrop-blur-sm" style="display: none;"></div>
                    </div>
                <?php endif; ?>

                <script>
                const ratingStars = document.getElementById('rating-stars');
                const ratingInput = document.getElementById('rating-input');

                if (ratingStars && ratingInput) {
                    ratingStars.addEventListener('click', (event) => {
                        const target = event.target;
                        const value = Number(target?.getAttribute('data-rating')) || 0;
                        if (!value) {
                            return;
                        }

                        ratingInput.value = value;
                        const stars = ratingStars.querySelectorAll('button');
                        stars.forEach((star) => {
                            const rating = Number(star.getAttribute('data-rating')) || 0;
                            star.innerHTML = rating <= value ? '&#9733;' : '&#9734;';
                        });
                    });
                }
                </script>

                    </div>
                </div>

<?php include 'layout/footer.php'; ?>
