<?php
$assetBase = $assetBase ?? 'assets';
$baseUrl = $baseUrl ?? '.';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seblak Mama Rizki</title>
    <link rel="stylesheet" href="<?= $assetBase ?>/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>

    <style>
        .icon-white { filter: brightness(0) invert(1); }
        .truncate-2-lines {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-[#f8f9fa]">
<?php if ($isAdmin): ?>
    <div id="admin-toast" class="fixed right-4 top-[16px] z-50 w-full max-w-xs space-y-2"></div>
    <script>
        (() => {
            const container = document.getElementById('admin-toast');
            if (!container) {
                return;
            }

            const endpoint = `${window.location.origin}/admin/notifications.php`;
            const storageKey = 'admin_last_tx_id';
            let latestId = Number(localStorage.getItem(storageKey) || 0);

            const formatMoney = (value) => {
                return new Intl.NumberFormat('id-ID').format(value || 0);
            };

            const renderToast = (item) => {
                const toast = document.createElement('div');
                toast.className = 'bg-white border border-gray-200 shadow-md rounded-xl px-4 py-3 text-sm';
                toast.innerHTML = `
                    <div class="font-semibold text-gray-800">Transaksi baru</div>
                    <div class="text-gray-600">#${item.id} oleh ${item.username}</div>
                    <div class="text-orange-600 font-semibold">IDR ${formatMoney(item.total)}</div>
                `;
                container.prepend(toast);

                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    toast.style.transition = 'opacity 300ms ease';
                    setTimeout(() => toast.remove(), 300);
                }, 6000);
            };

            const poll = async () => {
                try {
                    const response = await fetch(`${endpoint}?since_id=${latestId}`, { cache: 'no-store' });
                    const data = await response.json();
                    if (!data.ok) {
                        return;
                    }
                    if (Array.isArray(data.items)) {
                        data.items.forEach(renderToast);
                    }
                    if (data.latest_id && data.latest_id > latestId) {
                        latestId = data.latest_id;
                        localStorage.setItem(storageKey, String(latestId));
                    }
                } catch (error) {
                    return;
                }
            };

            poll();
            setInterval(poll, 5000);
        })();
    </script>
<?php endif; ?>
