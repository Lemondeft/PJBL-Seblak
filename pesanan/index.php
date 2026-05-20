<?php
require_once '../auth/check.php';
require_once '../config/db.php';

$assetBase = '../assets';
$baseUrl = '..';
$activeNav = 'pesanan';

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

$paket_result = mysqli_query(
    $conn,
    "SELECT id, nama, deskripsi, harga, stok, gambar FROM seblak_paket WHERE stok > 0 ORDER BY id DESC"
);

$pesanan_result = null;
if ($user_id) {
    $pesanan_result = mysqli_query(
        $conn,
        "SELECT p.id, p.id_seblak_paket, sp.nama AS paket_nama, sp.harga AS paket_harga, sp.gambar AS paket_gambar"
        . " FROM pesanan p"
        . " LEFT JOIN seblak_paket sp ON p.id_seblak_paket = sp.id"
		. " WHERE p.id_customer = $user_id"
        . " ORDER BY p.id DESC"
    );
}

include '../layout/header.php';
?>

<div x-data="{ paketOpen: false, prasmananOpen: false }" class="min-h-screen bg-white font-sans">
	<div class="mx-auto w-full max-w-3xl px-4 py-6 sm:px-6 lg:px-10">
		<div class="flex items-center justify-between mb-6">
			<h1 class="text-lg font-black text-gray-800 tracking-wide">PESANAN</h1>
			<span class="text-[10px] uppercase tracking-[0.3em] text-gray-500">Order</span>
		</div>

		<div class="grid grid-cols-2 gap-3 mb-8">
			<button type="button" @click="paketOpen = true" class="group flex flex-col items-center justify-center bg-orange-500 text-white font-semibold text-xs py-2 px-4 rounded-xl shadow-sm hover:bg-orange-600 transition-colors">
				<span>Order Paket</span>
				<span class="icon-base icon-add bg-white mt-0.5 group-hover:scale-105 transition-transform"></span>
			</button>
			<button type="button" @click="prasmananOpen = true" class="group flex flex-col items-center justify-center bg-orange-500 text-white font-semibold text-xs py-2 px-4 rounded-xl shadow-sm hover:bg-orange-600 transition-colors">
				<span>Order Prasmanan</span>
				<span class="icon-base icon-add bg-white mt-0.5 group-hover:scale-105 transition-transform"></span>
			</button>
		</div>

		<h2 class="text-sm font-bold text-gray-800 tracking-wide mb-4">Lihat Semua Pesanan</h2>

		<?php if (!$pesanan_result || mysqli_num_rows($pesanan_result) === 0): ?>
			<div class="rounded-3xl border border-gray-200 bg-orange-500/5 p-5 shadow-sm">
				<div class="border border-dashed border-orange-300/70 rounded-2xl p-6 text-center">
					<div class="w-12 h-12 mx-auto rounded-2xl bg-white/80 border border-orange-200 flex items-center justify-center">
						<span class="icon-base icon-shop text-orange-500"></span>
					</div>
					<p class="text-sm font-semibold text-gray-800 mt-3">Belum ada pesanan</p>
					<p class="text-[11px] text-gray-500 mt-1">Tambahkan menu favoritmu untuk mulai.</p>
				</div>
			</div>
		<?php else: ?>
			<div class="space-y-3">
				<?php while ($pesanan = mysqli_fetch_assoc($pesanan_result)): ?>
					<?php
						$pesanan_id = (int) $pesanan['id'];
						$is_paket = !empty($pesanan['id_seblak_paket']);
						$prasmanan_items = [];
						$prasmanan_total = 0;
						if (!$is_paket) {
							$detail_result = mysqli_query(
								$conn,
								"SELECT d.kuantitas, t.nama, t.harga"
								. " FROM pesanan_prasmanan_detail d"
								. " JOIN seblak_prasmanan t ON d.id_toping = t.id"
								. " WHERE d.id_pesanan = $pesanan_id"
							);
							if ($detail_result) {
								while ($detail = mysqli_fetch_assoc($detail_result)) {
									$prasmanan_items[] = $detail;
									$prasmanan_total += ((int) $detail['kuantitas']) * ((int) $detail['harga']);
								}
							}
						}
					?>
					<div class="bg-orange-500 border border-orange-600/40 rounded-2xl p-3 flex items-start justify-between shadow-sm text-white">
						<div class="w-20 h-14 bg-white/20 rounded-xl border border-white/30 flex-shrink-0 flex items-center justify-center overflow-hidden">
							<?php if ($is_paket && !empty($pesanan['paket_gambar'])): ?>
								<img src="<?= htmlspecialchars($pesanan['paket_gambar']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($pesanan['paket_nama']) ?>">
							<?php else: ?>
								<span class="icon-base icon-shop text-white/80"></span>
							<?php endif; ?>
						</div>
						<div class="flex flex-col items-end flex-grow pl-3 min-h-[56px] relative">
							<form method="POST" action="delete_pesanan.php" class="absolute top-0 right-0">
								<input type="hidden" name="id_pesanan" value="<?= $pesanan_id ?>">
								<button type="submit" class="text-xs font-bold text-white/90 hover:text-white">X</button>
							</form>
							<?php if ($is_paket): ?>
								<span class="text-xs font-bold pr-5 self-start mt-0.5">
									<?= htmlspecialchars($pesanan['paket_nama'] ?? 'Paket Seblak') ?>
								</span>
								<div class="flex items-center justify-between w-full mt-auto">
									<span class="text-[10px] font-bold">Qty 1</span>
									<span class="text-[10px] font-extrabold tracking-wide">IDR <?= number_format((int) $pesanan['paket_harga'], 0, ',', '.') ?></span>
								</div>
							<?php else: ?>
								<div class="flex flex-col self-start mt-0.5">
									<span class="text-xs font-bold leading-none">Seblak Prasmanan</span>
									<span class="text-[9px] font-medium text-orange-100 mt-1 leading-none">Info Selengkapnya</span>
								</div>
								<div class="mt-2 w-full text-[10px] text-orange-100/90">
									<?php foreach ($prasmanan_items as $detail): ?>
										<div class="flex items-center justify-between">
											<span><?= htmlspecialchars($detail['nama']) ?></span>
											<span>x<?= (int) $detail['kuantitas'] ?></span>
										</div>
									<?php endforeach; ?>
								</div>
								<div class="w-full flex justify-end mt-2">
									<span class="text-[10px] font-extrabold tracking-wide">IDR <?= number_format($prasmanan_total, 0, ',', '.') ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>

		<div class="w-full mt-6">
			<button type="button" class="w-full bg-orange-500 text-white font-extrabold text-sm py-2.5 rounded-2xl shadow-md hover:bg-orange-600 active:scale-[0.99] transition-all tracking-wider">
				Pesan
			</button>
		</div>
	</div>

	<div x-show="paketOpen"
		x-transition:enter="transition ease-out duration-200"
		x-transition:enter-start="opacity-0 scale-95"
		x-transition:enter-end="opacity-100 scale-100"
		x-transition:leave="transition ease-in duration-150"
		x-transition:leave-start="opacity-100 scale-100"
		x-transition:leave-end="opacity-0 scale-95"
		class="fixed inset-0 z-50 flex items-center justify-center px-4"
		style="display: none;">
		<div class="relative w-full max-w-md max-h-[90vh] overflow-y-auto bg-orange-500 rounded-3xl p-5 shadow-2xl text-white border border-white/90">
			<div class="bg-white rounded-2xl px-4 py-2 mb-4 flex items-center justify-between">
				<span class="text-xs font-bold text-gray-800">Paket Seblak</span>
				<button @click="paketOpen = false" class="text-gray-600 text-xl leading-none">&times;</button>
			</div>
			<div class="space-y-3">
				<?php if ($paket_result): ?>
					<?php while ($paket = mysqli_fetch_assoc($paket_result)): ?>
						<div class="bg-white/10 backdrop-blur-sm p-2.5 rounded-xl flex items-center gap-3 border border-white/20 shadow-sm">
							<div class="w-14 h-12 bg-white/20 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden border border-white/10">
								<?php if (!empty($paket['gambar'])): ?>
									<img src="<?= htmlspecialchars($paket['gambar']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($paket['nama']) ?>">
								<?php else: ?>
									<span class="icon-base icon-shop text-white/80"></span>
								<?php endif; ?>
							</div>
							<div class="flex-1">
								<div class="text-xs font-bold text-white leading-tight"><?= htmlspecialchars($paket['nama']) ?></div>
								<div class="text-[10px] text-orange-100 font-medium mt-0.5 truncate-2-lines"><?= htmlspecialchars($paket['deskripsi'] ?? '') ?></div>
								<div class="flex items-center justify-between mt-2">
									<span class="text-[10px] font-bold text-orange-100">Rp <?= number_format((int) $paket['harga'], 0, ',', '.') ?></span>
									<form method="POST" action="add_paket.php">
										<input type="hidden" name="id_paket" value="<?= (int) $paket['id'] ?>">
										<button type="submit" class="bg-white text-orange-600 text-[10px] font-bold px-3 py-1 rounded-full">Tambah</button>
									</form>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div x-show="prasmananOpen"
		x-transition:enter="transition ease-out duration-200"
		x-transition:enter-start="opacity-0 scale-95"
		x-transition:enter-end="opacity-100 scale-100"
		x-transition:leave="transition ease-in duration-150"
		x-transition:leave-start="opacity-100 scale-100"
		x-transition:leave-end="opacity-0 scale-95"
		class="fixed inset-0 z-50 flex items-center justify-center px-4"
		style="display: none;">
		<div class="relative w-full max-w-md max-h-[90vh] overflow-y-auto bg-orange-500 rounded-3xl p-5 shadow-2xl text-white border border-white/90">
			<div class="bg-white rounded-2xl px-4 py-2 mb-4 flex items-center justify-between">
				<span class="text-xs font-bold text-gray-800">Prasmanan</span>
				<button @click="prasmananOpen = false" class="text-gray-600 text-xl leading-none">&times;</button>
			</div>
			<?php
				if (!isset($_SESSION['keranjang_prasmanan'])) {
					$_SESSION['keranjang_prasmanan'] = [];
				}
				$total_harga_keranjang = 0;
				$prasmanan_query = "SELECT * FROM seblak_prasmanan WHERE stok > 0";
				$result = mysqli_query($conn, $prasmanan_query);
				include __DIR__ . '/_prasmanan_panel.php';
			?>
		</div>
	</div>

	<div x-show="paketOpen || prasmananOpen" @click="paketOpen = false; prasmananOpen = false" class="fixed inset-0 bg-black/30 z-40 backdrop-blur-sm" style="display: none;"></div>

</div>

<?php include '../layout/footer.php'; ?>