<?php
require_once 'auth/check.php';
require_once 'config/db.php';

$assetBase = 'assets';
$baseUrl = '.';
$activeNav = 'home';

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM seblak_paket";

if ($search) {
    $query .= " WHERE nama LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}
$result = mysqli_query($conn, $query);
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

            <button class="absolute bottom-0 left-0 w-full bg-[#ff8c42] py-1.5 flex justify-center items-center border-t border-gray-500 hover:bg-orange-500 transition-colors z-20 <?= $isOutOfStock ? 'bg-gray-400 pointer-events-none' : '' ?>">
                <span class="icon-base icon-add bg-gray-800"></span>
            </button>
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

<?php include 'layout/footer.php'; ?>