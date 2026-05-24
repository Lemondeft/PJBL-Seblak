<div class="bg-orange-500 rounded-3xl p-4 sm:p-6 text-white shadow-md">
    <div class="flex justify-between items-center mb-3">
        <span class="text-xs font-bold tracking-wide">Seblak Prasmanan</span>
    </div>

    <div class="relative mb-4">
        <input id="toping-search" type="text" placeholder="Cari Toping..." class="w-full bg-orange-400/50 placeholder-orange-200 text-white rounded-full py-1.5 px-4 text-xs border border-orange-300/30 focus:outline-none">
        <span class="icon-base icon-search text-orange-100 absolute right-4 top-1/2 -translate-y-1/2"></span>
    </div>

    <div id="toping-list" class="space-y-2 max-h-[360px] sm:max-h-[420px] lg:max-h-[520px] overflow-y-auto pr-1 custom-scrollbar">
        <?php while($t = mysqli_fetch_assoc($result)):
            $id_toping = $t['id'];
            $qty_sekarang = $_SESSION['keranjang_prasmanan'][$id_toping] ?? 0;
            $total_harga_item = $t['harga'] * $qty_sekarang;
            $total_harga_keranjang += $total_harga_item;
        ?>
            <div class="bg-white/10 backdrop-blur-sm p-2.5 rounded-xl flex items-center justify-between border border-white/20 shadow-sm transition hover:bg-white/15" data-filter-item data-filter-text="<?= strtolower(htmlspecialchars($t['nama'])) ?>">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden border border-white/10">
                        <?php if(!empty($t['gambar'])): ?>
                            <img src="../assets/uploads/topping/<?= $t['gambar'] ?>" alt="<?= htmlspecialchars($t['nama']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="icon-base icon-shop text-white/60"></span>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-white leading-tight"><?= htmlspecialchars($t['nama']) ?></span>
                        <span class="text-[10px] text-orange-100 font-medium mt-0.5">IDR <?= number_format($t['harga'], 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-1">
                    <div class="flex items-center gap-2 bg-white rounded-lg px-2 py-1 text-orange-600 shadow-sm">
                        <button type="button" onclick="updateToping(<?= $id_toping ?>, 'kurang', <?= $t['harga'] ?>)" class="text-xs font-black px-1 cursor-pointer focus:outline-none select-none">-</button>
                        <span id="qty-<?= $id_toping ?>" class="text-xs font-bold text-gray-800 min-w-[12px] text-center"><?= $qty_sekarang ?></span>
                        <button type="button" onclick="updateToping(<?= $id_toping ?>, 'tambah', <?= $t['harga'] ?>)" class="text-xs font-black px-1 cursor-pointer focus:outline-none select-none">+</button>
                    </div>
                    <span id="total-item-<?= $id_toping ?>" class="text-[9px] font-bold text-white opacity-90">
                        <?= $qty_sekarang > 0 ? 'IDR ' . number_format($total_harga_item, 0, ',', '.') : '' ?>
                    </span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div id="toping-empty" class="hidden text-center text-xs text-orange-100/90 mt-4">
        Toping tidak ditemukan.
    </div>

    <hr class="border-orange-400 my-4 opacity-40">

    <form id="form-prasmanan" action="add_prasmanan.php" method="POST">
        <div class="flex justify-between items-center text-xs font-semibold mb-1">
            <span>Total Harga</span>
            <span id="total-harga" class="text-sm font-black">IDR <?= number_format($total_harga_keranjang, 0, ',', '.') ?></span>
        </div>
        <div class="text-[10px] text-orange-100/80 font-light mb-3">
            + Rp 2.000 per 3 level kepedasan
        </div>
        <input type="hidden" name="total_harga" id="total-harga-input" value="<?= $total_harga_keranjang ?>">
        <input type="hidden" id="base-total" value="<?= $total_harga_keranjang ?>">

        <div class="flex justify-between items-center text-xs font-semibold mb-3">
            <span>Level Kepedasan</span>
            <div class="flex items-center gap-2 bg-orange-600/40 rounded-lg p-1">
                <button type="button" onclick="adjustLevel(-1)" class="w-5 h-5 bg-orange-600 text-center rounded font-bold">-</button>
                <input type="text" name="level_pedas" id="level-input" value="1" class="w-4 text-center bg-transparent focus:outline-none text-xs font-bold" readonly>
                <button type="button" onclick="adjustLevel(1)" class="w-5 h-5 bg-orange-600 text-center rounded font-bold">+</button>
            </div>
        </div>

        <div class="text-xs font-semibold mb-2">Kecenderungan Rasa</div>
        <div class="grid grid-cols-3 gap-2 text-center text-[10px]">
            <label class="cursor-pointer">
                <input type="radio" name="rasa" value="Asin" class="hidden peer" checked>
                <div class="bg-orange-600/30 peer-checked:bg-white peer-checked:text-orange-600 py-1.5 rounded-full border border-orange-400 font-bold transition">Asin</div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="rasa" value="Gurih" class="hidden peer">
                <div class="bg-orange-600/30 peer-checked:bg-white peer-checked:text-orange-600 py-1.5 rounded-full border border-orange-400 font-bold transition">Gurih</div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="rasa" value="Manis" class="hidden peer">
                <div class="bg-orange-600/30 peer-checked:bg-white peer-checked:text-orange-600 py-1.5 rounded-full border border-orange-400 font-bold transition">Manis</div>
            </label>
        </div>

        <button type="submit" class="w-full bg-orange-100 text-orange-600 font-black py-2.5 rounded-xl shadow-md mt-4 text-xs tracking-wider uppercase hover:bg-white transition">
            Pesan
        </button>
    </form>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.4); border-radius: 10px; }
</style>

<script>
function updateToping(id, aksi, harga) {
    fetch('update_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_toping=${id}&aksi=${aksi}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'sukses') {
            document.getElementById(`qty-${id}`).innerText = data.qty_baru;

            let totalItemElement = document.getElementById(`total-item-${id}`);
            if(data.qty_baru > 0) {
                let hitungTotal = data.qty_baru * harga;
                totalItemElement.innerText = "IDR " + hitungTotal.toLocaleString('id-ID');
            } else {
                totalItemElement.innerText = "";
            }

            if (typeof data.total_harga !== 'undefined') {
                updateTotalHarga(data.total_harga);
            }
        }
    })
    .catch(() => {});
}

function updateTotalHarga(total) {
    const totalElement = document.getElementById('total-harga');
    const totalInput = document.getElementById('total-harga-input');
    const baseTotalInput = document.getElementById('base-total');
    const baseTotal = Number(total) || 0;
    const finalTotal = baseTotal + getSpiceSurcharge();
    if (baseTotalInput) {
        baseTotalInput.value = baseTotal;
    }
    if (totalElement) {
        totalElement.innerText = "IDR " + Number(finalTotal).toLocaleString('id-ID');
    }
    if (totalInput) {
        totalInput.value = finalTotal;
    }
}

function adjustLevel(val) {
    let input = document.getElementById('level-input');
    let current = parseInt(input.value);
    if(current + val >= 1 && current + val <= 5) {
        input.value = current + val;
    }
    applySpiceSurcharge();
}

function getSpiceSurcharge() {
    const input = document.getElementById('level-input');
    const level = Number(input?.value || 1);
    return Math.floor(level / 3) * 2000;
}

function applySpiceSurcharge() {
    const baseTotal = Number(document.getElementById('base-total')?.value || 0);
    updateTotalHarga(baseTotal);
}

const topingSearch = document.getElementById('toping-search');
const topingItems = document.querySelectorAll('[data-filter-item]');
const topingEmpty = document.getElementById('toping-empty');

function filterTopingList() {
    const query = (topingSearch?.value || '').trim().toLowerCase();
    let visibleCount = 0;

    topingItems.forEach((item) => {
        const text = item.getAttribute('data-filter-text') || '';
        const isVisible = text.includes(query);
        item.classList.toggle('hidden', !isVisible);
        if (isVisible) {
            visibleCount += 1;
        }
    });

    if (topingEmpty) {
        topingEmpty.classList.toggle('hidden', visibleCount !== 0);
    }
}

if (topingSearch) {
    topingSearch.addEventListener('input', filterTopingList);
}

applySpiceSurcharge();
</script>
