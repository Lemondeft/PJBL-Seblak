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

$user_id = getUserId($conn, $_SESSION['user_id'] ?? null, $_SESSION['user'] ?? null);
$orders = [];
$subtotal = 0;

if ($user_id) {
    $pesanan_result = mysqli_query(
        $conn,
        "SELECT p.id, p.id_seblak_paket, sp.nama AS paket_nama, sp.harga AS paket_harga, sp.gambar AS paket_gambar"
        . " FROM pesanan p"
        . " LEFT JOIN seblak_paket sp ON p.id_seblak_paket = sp.id"
        . " LEFT JOIN transaksi t ON t.id_pesanan = p.id"
        . " WHERE p.id_customer = $user_id AND t.id IS NULL"
        . " ORDER BY p.id DESC"
    );

    if ($pesanan_result) {
        while ($pesanan = mysqli_fetch_assoc($pesanan_result)) {
            $pesanan_id = (int) $pesanan['id'];
            $is_paket = !empty($pesanan['id_seblak_paket']);
            $item_total = 0;
            $details = [];

            if ($is_paket) {
                $item_total = (int) ($pesanan['paket_harga'] ?? 0);
                $details[] = [
                    'label' => $pesanan['paket_nama'] ?? 'Paket Seblak',
                    'total' => $item_total
                ];
            } else {
                $detail_result = mysqli_query(
                    $conn,
                    "SELECT d.kuantitas, t.nama, t.harga"
                    . " FROM pesanan_prasmanan_detail d"
                    . " JOIN seblak_prasmanan t ON d.id_toping = t.id"
                    . " WHERE d.id_pesanan = $pesanan_id"
                );

                if ($detail_result) {
                    while ($detail = mysqli_fetch_assoc($detail_result)) {
                        $qty = (int) $detail['kuantitas'];
                        $harga = (int) $detail['harga'];
                        $item_total += $qty * $harga;
                        $details[] = [
                            'label' => $detail['nama'],
                            'qty' => $qty
                        ];
                    }
                }
            }

            $subtotal += $item_total;

            $orders[] = [
                'id' => $pesanan_id,
                'is_paket' => $is_paket,
                'nama' => $is_paket ? ($pesanan['paket_nama'] ?? 'Paket Seblak') : 'Seblak Prasmanan',
                'gambar' => $pesanan['paket_gambar'] ?? '',
                'total' => $item_total,
                'details' => $details
            ];
        }
    }
}

$assetBase = '../assets';
include '../layout/head.php';
?>

<div class="w-full min-h-screen bg-white font-sans flex items-start justify-center p-6 sm:p-8">
    <div class="w-full max-w-2xl flex flex-col">
        <div class="flex items-center gap-4 mb-6">
            <a href="../pesanan/index.php" class="bg-orange-500/10 p-2 rounded-full">
                <span class="icon-base icon-arrow text-orange-600"></span>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-800">Konfirmasi</h1>
                <p class="text-sm text-gray-500">Pesanan Yang Dipesan</p>
            </div>
        </div>

        <div class="space-y-4">
            <?php if (empty($orders)): ?>
                <div class="border border-dashed border-orange-300/70 rounded-2xl p-6 text-center">
                    <p class="text-base font-semibold text-gray-800">Belum ada pesanan</p>
                    <p class="text-sm text-gray-500 mt-1">Tambahkan menu sebelum konfirmasi.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="bg-orange-500 rounded-2xl p-4 flex items-center gap-4 shadow-sm text-white border border-orange-600/30">
                        <div class="w-20 h-16 bg-white/20 rounded-xl border border-white/30 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($order['gambar'])): ?>
                                <img src="<?= htmlspecialchars($order['gambar']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($order['nama']) ?>">
                            <?php else: ?>
                                <span class="icon-base icon-shop text-white/80"></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold"><?= htmlspecialchars($order['nama']) ?></span>
                                <span class="text-xs font-bold">IDR <?= number_format($order['total'], 0, ',', '.') ?></span>
                            </div>
                            <?php if (!$order['is_paket']): ?>
                                <div class="text-xs text-orange-100 mt-1">Info Selengkapnya</div>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="../pesanan/delete_pesanan.php">
                            <input type="hidden" name="id_pesanan" value="<?= (int) $order['id'] ?>">
                            <button type="submit" class="text-xs font-bold text-white/90">X</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-8">
            <h2 class="text-base font-bold text-gray-700 mb-2">Informasi Pembayaran</h2>
            <div class="space-y-2 text-sm text-gray-700">
                <?php if (empty($orders)): ?>
                    <div class="flex justify-between"><span>Subtotal</span><span>IDR 0</span></div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="flex justify-between">
                            <span><?= htmlspecialchars($order['nama']) ?></span>
                            <span>IDR <?= number_format($order['total'], 0, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="border-t border-gray-200 pt-2 flex justify-between font-bold">
                        <span>Subtotal</span>
                        <span>IDR <?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-10">
            <a href="qris.php" class="block w-full bg-orange-500 text-white font-extrabold text-base py-3 rounded-2xl text-center shadow-md hover:bg-orange-600 transition-all">
                Selanjutnya
            </a>
        </div>
    </div>
</div>

</body>
</html>
