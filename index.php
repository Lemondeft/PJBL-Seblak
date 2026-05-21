<?php
require_once 'auth/check.php';
require_once 'config/db.php';

$assetBase = 'assets';
$baseUrl = '.';
$activeNav = 'home';

$search = $_GET['search'] ?? '';
$detail_id = isset($_GET['detail']) ? (int) $_GET['detail'] : 0;
$query = "SELECT * FROM seblak_paket";

if ($search) {
    $query .= " WHERE nama LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}
$result = mysqli_query($conn, $query);

$detail_paket = null;
$detail_ulasan = [];
if ($detail_id > 0) {
  $detail_result = mysqli_query(
    $conn,
    "SELECT id, nama, deskripsi, harga, stok, gambar FROM seblak_paket WHERE id = $detail_id LIMIT 1"
  );
  $detail_paket = $detail_result ? mysqli_fetch_assoc($detail_result) : null;

  if ($detail_paket) {
    $ulasan_result = mysqli_query(
      $conn,
      "SELECT u.username, ul.rating, ul.komentar, ul.tanggal_ulasan"
      . " FROM ulasan ul"
      . " JOIN users u ON ul.id_customer = u.id"
      . " WHERE ul.id_seblak_paket = $detail_id"
      . " ORDER BY ul.tanggal_ulasan DESC"
    );

    if ($ulasan_result) {
      while ($row = mysqli_fetch_assoc($ulasan_result)) {
        $detail_ulasan[] = $row;
      }
    }
  }
}
?>

<?php include 'layout/header.php'; ?>



  <div class="p-5 sm:p-6 lg:px-10">

    <div class="mb-6">
      <h2 class="text-lg text-gray-800 mb-3 tracking-wide font-semibold">TERKINI</h2>
      <div id="carousel-placeholder" class="relative w-full h-48 sm:h-56 lg:h-72 rounded-2xl overflow-hidden shadow-lg border border-gray-300">
        <img src="https://images.unsplash.com/photo-1626804475297-41609ea004eb?w=600&h=300&fit=crop" class="w-full h-full object-cover">
      </div>
    </div>

    <div class="mb-6">
      <h2 class="text-lg text-gray-800 mb-3 tracking-wide font-semibold">JENIS MENU</h2>
      <div class="border border-gray-400 rounded-xl p-1 flex items-center gap-3 bg-white w-full max-w-xs hover:shadow-md transition-shadow cursor-pointer">
        <img src="https://images.unsplash.com/photo-1548943487-a2e4e43b4850?w=100&h=100&fit=crop" class="w-14 h-14 rounded-lg object-cover">
        <span class="text-gray-900 tracking-wider text-sm font-medium uppercase">SEBLAK</span>
      </div>
    </div>

    <div class="flex justify-between items-end mb-1">
      <h2 class="text-base text-gray-900 tracking-wide font-semibold">PAKET SEBLAK</h2>
      <form method="GET" class="relative w-40 sm:w-48 lg:w-56">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari..." class="w-full border border-gray-600 bg-transparent rounded-full py-0.5 px-3 pr-8 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-600 flex items-center">
            <span class="icon-base icon-search bg-gray-600"></span>
        </button>
      </form>
    </div>

    <div class="flex items-center mb-6">
       <div class="flex-grow border-t border-gray-400"></div>
       <span class="icon-base icon-filter bg-gray-700 ml-1"></span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-x-4 gap-y-8 px-1">

      <?php 
      while ($row = mysqli_fetch_assoc($result)) { 
          $isOutOfStock = ($row['stok'] <= 0);
      ?>
        <div class="group flex flex-col items-center <?= $isOutOfStock ? 'opacity-70' : '' ?>">

          <div class="relative w-full rounded-2xl border border-gray-500 overflow-hidden aspect-[4/3] bg-gray-200 shadow-sm group-hover:shadow-lg transition-all">
            <a href="?detail=<?= (int) $row['id'] ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="absolute inset-0 z-10" aria-label="Lihat detail paket"></a>
            
            <?php if (!empty($row['gambar'])): ?>
              <img src="<?= $row['gambar'] ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-gray-400 italic text-xs">No Image</div>
            <?php endif; ?>

            <?php if ($isOutOfStock): ?>
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-bold text-xs tracking-wide rotate-[-15deg] border-2 border-red-500 z-10 uppercase">HABIS</div>
            <?php endif; ?>

            <button class="absolute top-2 right-2 text-white hover:text-red-500 drop-shadow-md z-20 <?= $isOutOfStock ? 'pointer-events-none' : '' ?>">
                <span class="icon-base icon-fav bg-white"></span>
            </button>

            <form method="POST" action="pesanan/add_paket.php" class="absolute bottom-0 left-0 w-full z-20">
              <input type="hidden" name="id_paket" value="<?= (int) $row['id'] ?>">
              <button type="submit" class="w-full bg-[#ff8c42] py-1.5 flex justify-center items-center border-t border-gray-500 hover:bg-orange-500 transition-colors z-20 <?= $isOutOfStock ? 'bg-gray-400 pointer-events-none' : '' ?>">
                <span class="icon-base icon-add bg-gray-800"></span>
              </button>
            </form>
          </div>

          <div class="mt-3 text-center px-1">
            <p class="text-[13px] md:text-sm text-gray-900 leading-tight font-medium">
              <?= htmlspecialchars($row['nama']) ?>
            </p>
            
            <p class="text-[10px] text-gray-500 leading-tight mt-0.5 max-w-[140px] truncate-2-lines">
               <?= htmlspecialchars($row['deskripsi']) ?>
            </p>

            <p class="text-xs text-orange-600 font-semibold mt-1">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
          </div>

        </div>
      <?php } ?>

    </div>

  </div>

<?php if ($detail_paket): ?>
  <div class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
    <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-3xl shadow-2xl border border-gray-200">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div>
          <h2 class="text-lg font-black text-gray-800"><?= htmlspecialchars($detail_paket['nama']) ?></h2>
          <p class="text-xs text-gray-500">Detail paket dan ulasan</p>
        </div>
        <a href="index.php<?= $search ? '?search=' . urlencode($search) : '' ?>" class="text-gray-600 text-2xl leading-none">&times;</a>
      </div>

      <div class="p-6 grid gap-6 lg:grid-cols-[1.2fr_1fr]">
        <div class="rounded-3xl border border-gray-200 bg-white shadow-sm overflow-hidden">
          <div class="h-56 bg-gray-200">
            <?php if (!empty($detail_paket['gambar'])): ?>
              <img src="<?= htmlspecialchars($detail_paket['gambar']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($detail_paket['nama']) ?>">
            <?php else: ?>
              <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
            <?php endif; ?>
          </div>
          <div class="px-5 py-4">
            <div class="flex items-start justify-between">
              <div>
                <div class="text-xs text-gray-500">Tersedia: <?= (int) $detail_paket['stok'] ?></div>
                <div class="text-xs text-gray-500 mt-1">Deskripsi</div>
              </div>
              <div class="text-sm font-semibold text-orange-600">IDR <?= number_format((int) $detail_paket['harga'], 0, ',', '.') ?></div>
            </div>
            <p class="text-sm text-gray-600 mt-3"><?= htmlspecialchars($detail_paket['deskripsi'] ?? '-') ?></p>
            <form method="POST" action="pesanan/add_paket.php" class="mt-4">
              <input type="hidden" name="id_paket" value="<?= (int) $detail_paket['id'] ?>">
              <button type="submit" class="bg-orange-500 text-white text-xs font-bold px-4 py-2 rounded-full shadow-sm">Tambah</button>
            </form>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700">Ulasan</h3>
            <span class="text-xs text-gray-500">(<?= count($detail_ulasan) ?> ulasan)</span>
          </div>
          <div class="mt-4 space-y-3">
            <?php if (empty($detail_ulasan)): ?>
              <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 text-center text-sm text-gray-500">
                Belum ada ulasan untuk paket ini.
              </div>
            <?php else: ?>
              <?php foreach ($detail_ulasan as $ulasan): ?>
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
                  <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($ulasan['komentar'] ?? '') ?></p>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="fixed inset-0 bg-black/30 z-40 backdrop-blur-sm"></div>
<?php endif; ?>

<?php include 'layout/footer.php'; ?>