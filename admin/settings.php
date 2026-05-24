<?php
require_once '../auth/check.php';
require_once '../config/db.php';
require_once '../core/paket.php';
require_once '../core/toping.php';
require_once '../core/carousel.php';

date_default_timezone_set('Asia/Jakarta');

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$queryOrLog = function (string $sql) use ($conn) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        error_log('SQL error: ' . mysqli_error($conn) . ' | ' . $sql);
    }

    return $result;
};

handlePaketActions('settings.php');
handleTopingActions('settings.php');
handleCarouselActions('settings.php');

$paket_rows = [];
$paket_result = $queryOrLog("SELECT * FROM seblak_paket ORDER BY id DESC");
if ($paket_result) {
    while ($row = mysqli_fetch_assoc($paket_result)) {
        $paket_rows[] = $row;
    }
}

$toping_rows = [];
$toping_result = $queryOrLog("SELECT * FROM seblak_prasmanan ORDER BY id DESC");
if ($toping_result) {
    while ($row = mysqli_fetch_assoc($toping_result)) {
        $toping_rows[] = $row;
    }
}

$carousel_rows = [];
$carousel_error = '';
$carousel_result = $queryOrLog("SELECT * FROM carousel ORDER BY urutan ASC, id ASC");
if ($carousel_result) {
    while ($row = mysqli_fetch_assoc($carousel_result)) {
        $carousel_rows[] = $row;
    }
} else {
    $carousel_error = 'Tabel carousel belum ada. Jalankan SQL untuk membuatnya.';
}

$assetBase = '../assets';
$baseUrl = '..';
$activeNav = 'admin_settings';
include '../layout/header.php';
?>

<div class="min-h-screen bg-[#f8f9fa] p-4 font-sans">
    <div class="mx-auto w-full max-w-2xl">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Admin Settings</h1>

        <details class="bg-white border border-gray-200 rounded-2xl p-4 mb-4" open>
            <summary class="text-lg font-semibold text-gray-800 cursor-pointer">Tambah Paket</summary>
            <form method="POST" enctype="multipart/form-data" class="grid gap-3 mt-4">
            <input type="hidden" name="action" value="paket_create">
            <input type="text" name="nama" placeholder="Nama" class="border rounded-lg px-3 py-2" required>
            <textarea name="deskripsi" placeholder="Deskripsi" class="border rounded-lg px-3 py-2" rows="2"></textarea>
            <div class="grid grid-cols-2 gap-3">
                <input type="number" name="harga" placeholder="Harga" class="border rounded-lg px-3 py-2" required>
                <input type="number" name="stok" placeholder="Stok" class="border rounded-lg px-3 py-2" required>
            </div>
            <input type="file" name="gambar" class="border rounded-lg px-3 py-2" accept="image/*">
            <button type="submit" class="bg-orange-500 text-white rounded-lg px-4 py-2">Simpan</button>
            </form>
        </details>

        <details class="bg-white border border-gray-200 rounded-2xl p-4 mb-4">
            <summary class="text-lg font-semibold text-gray-800 cursor-pointer">Daftar Paket</summary>
            <div class="mt-4">
                <?php if (empty($paket_rows)): ?>
                    <div class="text-sm text-gray-500">Belum ada paket.</div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($paket_rows as $paket): ?>
                            <form method="POST" enctype="multipart/form-data" class="border rounded-xl p-3 grid gap-3">
                                <input type="hidden" name="action" value="paket_update">
                                <input type="hidden" name="id" value="<?= (int) $paket['id'] ?>">
                                <input type="hidden" name="existing_gambar" value="<?= htmlspecialchars($paket['gambar'] ?? '') ?>">
                                <div class="text-xs text-gray-500">ID: <?= (int) $paket['id'] ?></div>
                                <input type="text" name="nama" value="<?= htmlspecialchars($paket['nama'] ?? '') ?>" class="border rounded-lg px-3 py-2" required>
                                <textarea name="deskripsi" class="border rounded-lg px-3 py-2" rows="2"><?= htmlspecialchars($paket['deskripsi'] ?? '') ?></textarea>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" name="harga" value="<?= (int) ($paket['harga'] ?? 0) ?>" class="border rounded-lg px-3 py-2" required>
                                    <input type="number" name="stok" value="<?= (int) ($paket['stok'] ?? 0) ?>" class="border rounded-lg px-3 py-2" required>
                                </div>
                                <input type="file" name="gambar" class="border rounded-lg px-3 py-2" accept="image/*">
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="bg-orange-500 text-white rounded-lg px-4 py-2">Update</button>
                                    <button type="submit" name="action" value="paket_delete" class="bg-red-500 text-white rounded-lg px-4 py-2" onclick="return confirm('Hapus paket ini?')">Hapus</button>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </details>

        <details class="bg-white border border-gray-200 rounded-2xl p-4 mb-4">
            <summary class="text-lg font-semibold text-gray-800 cursor-pointer">Tambah Topping</summary>
            <form method="POST" enctype="multipart/form-data" class="grid gap-3 mt-4">
            <input type="hidden" name="action" value="toping_create">
            <input type="text" name="nama" placeholder="Nama" class="border rounded-lg px-3 py-2" required>
            <div class="grid grid-cols-2 gap-3">
                <input type="number" name="harga" placeholder="Harga" class="border rounded-lg px-3 py-2" required>
                <input type="number" name="stok" placeholder="Stok" class="border rounded-lg px-3 py-2" required>
            </div>
            <input type="file" name="gambar" class="border rounded-lg px-3 py-2" accept="image/*">
            <button type="submit" class="bg-orange-500 text-white rounded-lg px-4 py-2">Simpan</button>
            </form>
        </details>

        <details class="bg-white border border-gray-200 rounded-2xl p-4 mb-4">
            <summary class="text-lg font-semibold text-gray-800 cursor-pointer">Daftar Topping</summary>
            <div class="mt-4">
                <?php if (empty($toping_rows)): ?>
                    <div class="text-sm text-gray-500">Belum ada topping.</div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($toping_rows as $toping): ?>
                            <form method="POST" enctype="multipart/form-data" class="border rounded-xl p-3 grid gap-3">
                                <input type="hidden" name="action" value="toping_update">
                                <input type="hidden" name="id" value="<?= (int) $toping['id'] ?>">
                                <input type="hidden" name="existing_gambar" value="<?= htmlspecialchars($toping['gambar'] ?? '') ?>">
                                <div class="text-xs text-gray-500">ID: <?= (int) $toping['id'] ?></div>
                                <input type="text" name="nama" value="<?= htmlspecialchars($toping['nama'] ?? '') ?>" class="border rounded-lg px-3 py-2" required>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" name="harga" value="<?= (int) ($toping['harga'] ?? 0) ?>" class="border rounded-lg px-3 py-2" required>
                                    <input type="number" name="stok" value="<?= (int) ($toping['stok'] ?? 0) ?>" class="border rounded-lg px-3 py-2" required>
                                </div>
                                <input type="file" name="gambar" class="border rounded-lg px-3 py-2" accept="image/*">
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="bg-orange-500 text-white rounded-lg px-4 py-2">Update</button>
                                    <button type="submit" name="action" value="toping_delete" class="bg-red-500 text-white rounded-lg px-4 py-2" onclick="return confirm('Hapus topping ini?')">Hapus</button>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </details>

        <details class="bg-white border border-gray-200 rounded-2xl p-4 mb-4">
            <summary class="text-lg font-semibold text-gray-800 cursor-pointer">Tambah Carousel</summary>
            <?php if ($carousel_error): ?>
                <div class="text-sm text-red-600 mt-3"><?= htmlspecialchars($carousel_error) ?></div>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data" class="grid gap-3 mt-4">
                    <input type="hidden" name="action" value="carousel_create">
                    <input type="number" name="urutan" placeholder="Urutan" class="border rounded-lg px-3 py-2" value="0">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="aktif" checked>
                        Aktif
                    </label>
                    <input type="file" name="gambar" class="border rounded-lg px-3 py-2" accept="image/*" required>
                    <button type="submit" class="bg-orange-500 text-white rounded-lg px-4 py-2">Simpan</button>
                </form>
            <?php endif; ?>
        </details>

        <details class="bg-white border border-gray-200 rounded-2xl p-4">
            <summary class="text-lg font-semibold text-gray-800 cursor-pointer">Daftar Carousel</summary>
            <div class="mt-4">
                <?php if ($carousel_error): ?>
                    <div class="text-sm text-gray-500">Belum ada data carousel.</div>
                <?php elseif (empty($carousel_rows)): ?>
                    <div class="text-sm text-gray-500">Belum ada carousel.</div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($carousel_rows as $carousel): ?>
                            <form method="POST" enctype="multipart/form-data" class="border rounded-xl p-3 grid gap-3">
                                <input type="hidden" name="action" value="carousel_update">
                                <input type="hidden" name="id" value="<?= (int) $carousel['id'] ?>">
                                <input type="hidden" name="existing_gambar" value="<?= htmlspecialchars($carousel['gambar'] ?? '') ?>">
                                <div class="text-xs text-gray-500">ID: <?= (int) $carousel['id'] ?></div>
                                <input type="number" name="urutan" value="<?= (int) ($carousel['urutan'] ?? 0) ?>" class="border rounded-lg px-3 py-2">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="aktif" <?= !empty($carousel['aktif']) ? 'checked' : '' ?>>
                                    Aktif
                                </label>
                                <input type="file" name="gambar" class="border rounded-lg px-3 py-2" accept="image/*">
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="bg-orange-500 text-white rounded-lg px-4 py-2">Update</button>
                                    <button type="submit" name="action" value="carousel_delete" class="bg-red-500 text-white rounded-lg px-4 py-2" onclick="return confirm('Hapus carousel ini?')">Hapus</button>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </details>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
