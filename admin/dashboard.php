<?php
require_once '../auth/check.php';
require_once '../config/db.php';

date_default_timezone_set('Asia/Jakarta');

if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../index.php');
        exit;
}

$assetBase = '../assets';
$baseUrl = '..';
$activeNav = 'admin_dashboard';

$queryOrLog = function (string $sql) use ($conn) {
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            error_log('SQL error: ' . mysqli_error($conn) . ' | ' . $sql);
        }

        return $result;
};

$period = $_GET['period'] ?? 'day';
$period = in_array($period, ['today', 'day', 'month', 'quarter', 'year', 'all'], true) ? $period : 'day';

$buildSeries = function (DateTime $start, int $count, string $interval, string $keyFormat, string $labelFormat): array {
    $keys = [];
    $labels = [];
    for ($i = 0; $i < $count; $i++) {
        $current = clone $start;
        $current->modify('+' . $i . ' ' . $interval);
        $keys[] = $current->format($keyFormat);
        $labels[] = $current->format($labelFormat);
    }

    return [$keys, $labels];
};

$chart_labels = [];
$chart_data = [];
$chart_caption = 'Ringkasan 7 hari';
$data_map = [];

if ($period === 'today') {
    $today = new DateTime('today');
    $keys = [$today->format('Y-m-d')];
    $labels = [$today->format('d M')];

        $result = $queryOrLog(
                "SELECT DATE(tanggal_pesanan) AS k, SUM(total_harga) AS total"
                . " FROM transaksi"
                . " WHERE tanggal_pesanan >= CURDATE()"
                . " AND tanggal_pesanan < DATE_ADD(CURDATE(), INTERVAL 1 DAY)"
                . " GROUP BY k"
        );
    $chart_caption = 'Ringkasan hari ini';
} elseif ($period === 'day') {
    $start = new DateTime('today');
    $start->modify('-6 days');
    [$keys, $labels] = $buildSeries($start, 7, 'day', 'Y-m-d', 'd M');

        $result = $queryOrLog(
                "SELECT DATE(tanggal_pesanan) AS k, SUM(total_harga) AS total"
                . " FROM transaksi"
                . " WHERE tanggal_pesanan >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)"
                . " AND tanggal_pesanan < DATE_ADD(CURDATE(), INTERVAL 1 DAY)"
                . " GROUP BY k"
        );
    $chart_caption = 'Ringkasan 7 hari';
} elseif ($period === 'month') {
    $start = new DateTime('first day of this month');
    $start->modify('-11 months');
    [$keys, $labels] = $buildSeries($start, 12, 'month', 'Y-m', 'M Y');

        $result = $queryOrLog(
                "SELECT DATE_FORMAT(tanggal_pesanan, '%Y-%m') AS k, SUM(total_harga) AS total"
                . " FROM transaksi"
                . " WHERE tanggal_pesanan >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)"
                . " GROUP BY k"
        );
    $chart_caption = 'Ringkasan 12 bulan';
} elseif ($period === 'quarter') {
    $start = new DateTime('first day of this month');
    $start->modify('-2 months');
    [$keys, $labels] = $buildSeries($start, 3, 'month', 'Y-m', 'M Y');

        $result = $queryOrLog(
                "SELECT DATE_FORMAT(tanggal_pesanan, '%Y-%m') AS k, SUM(total_harga) AS total"
                . " FROM transaksi"
                . " WHERE tanggal_pesanan >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)"
                . " GROUP BY k"
        );
    $chart_caption = 'Ringkasan 3 bulan';
} elseif ($period === 'year') {
    $start = new DateTime(date('Y-01-01'));
    $start->modify('-4 years');
    [$keys, $labels] = $buildSeries($start, 5, 'year', 'Y', 'Y');

        $result = $queryOrLog(
                "SELECT YEAR(tanggal_pesanan) AS k, SUM(total_harga) AS total"
                . " FROM transaksi"
                . " WHERE tanggal_pesanan >= DATE_SUB(CURDATE(), INTERVAL 4 YEAR)"
                . " GROUP BY k"
        );
    $chart_caption = 'Ringkasan 5 tahun';
} else {
        $min_result = $queryOrLog("SELECT MIN(tanggal_pesanan) AS min_date FROM transaksi");
    $min_date = null;
    if ($min_result && ($row = mysqli_fetch_assoc($min_result))) {
        $min_date = $row['min_date'] ?? null;
    }

    $startYear = $min_date ? (int) date('Y', strtotime($min_date)) : (int) date('Y');
    $currentYear = (int) date('Y');
    $count = max(1, $currentYear - $startYear + 1);
    $start = new DateTime($startYear . '-01-01');
    [$keys, $labels] = $buildSeries($start, $count, 'year', 'Y', 'Y');

        $result = $queryOrLog(
                "SELECT YEAR(tanggal_pesanan) AS k, SUM(total_harga) AS total"
                . " FROM transaksi"
                . " GROUP BY k"
        );
    $chart_caption = 'Ringkasan semua waktu';
}

if (!empty($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $key = (string) ($row['k'] ?? '');
        if ($key !== '') {
            $data_map[$key] = (int) ($row['total'] ?? 0);
        }
    }
}

$chart_labels = $labels ?? [];
$chart_data = [];
foreach (($keys ?? []) as $key) {
    $chart_data[] = $data_map[$key] ?? 0;
}

$transactions = [];
$result = $queryOrLog(
        "SELECT t.id, t.total_harga, t.tanggal_pesanan, p.id AS pesanan_id, u.username"
        . " FROM transaksi t"
        . " JOIN pesanan p ON t.id_pesanan = p.id"
        . " JOIN users u ON p.id_customer = u.id"
        . " ORDER BY t.id DESC"
        . " LIMIT 10"
);
if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
                $transactions[] = $row;
        }
}

$fetchSum = function (string $sql) use ($conn): int {
        $res = mysqli_query($conn, $sql);
    if ($res && ($row = mysqli_fetch_assoc($res))) {
        return (int) ($row['total'] ?? 0);
    }

    return 0;
};

$income_today = $fetchSum(
        "SELECT SUM(total_harga) AS total FROM transaksi"
        . " WHERE tanggal_pesanan >= CURDATE()"
        . " AND tanggal_pesanan < DATE_ADD(CURDATE(), INTERVAL 1 DAY)"
);
$income_7 = $fetchSum(
        "SELECT SUM(total_harga) AS total FROM transaksi"
        . " WHERE tanggal_pesanan >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)"
        . " AND tanggal_pesanan < DATE_ADD(CURDATE(), INTERVAL 1 DAY)"
);
$income_30 = $fetchSum(
        "SELECT SUM(total_harga) AS total FROM transaksi"
        . " WHERE tanggal_pesanan >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)"
        . " AND tanggal_pesanan < DATE_ADD(CURDATE(), INTERVAL 1 DAY)"
);
$income_all = $fetchSum("SELECT SUM(total_harga) AS total FROM transaksi");

include '../layout/header.php';
?>

<div class="min-h-screen bg-[#f8f9fa] p-6 font-sans">
        <div class="mx-auto w-full max-w-4xl">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-500">Hari ini</div>
                    <div class="text-lg font-semibold text-gray-800">IDR <?= number_format($income_today, 0, ',', '.') ?></div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-500">7 hari</div>
                    <div class="text-lg font-semibold text-gray-800">IDR <?= number_format($income_7, 0, ',', '.') ?></div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-500">30 hari</div>
                    <div class="text-lg font-semibold text-gray-800">IDR <?= number_format($income_30, 0, ',', '.') ?></div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
                    <div class="text-xs text-gray-500">Semua waktu</div>
                    <div class="text-lg font-semibold text-gray-800">IDR <?= number_format($income_all, 0, ',', '.') ?></div>
                </div>
            </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-6 mb-6">
                        <div class="flex justify-between items-start">
                                <div>
                                        <h5 class="text-2xl font-semibold text-gray-800">Grafik Penjualan</h5>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($chart_caption) ?></p>
                                </div>
                        <form method="GET">
                            <select name="period" class="border rounded-lg px-2 py-1 text-sm" onchange="this.form.submit()">
                                                <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>Hari ini</option>
                                                <option value="day" <?= $period === 'day' ? 'selected' : '' ?>>7 hari</option>
                                <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>12 bulan</option>
                                <option value="quarter" <?= $period === 'quarter' ? 'selected' : '' ?>>3 bulan</option>
                                <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>5 tahun</option>
                                <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>Semua waktu</option>
                            </select>
                        </form>
                        </div>
                        <div id="area-chart" class="mt-4"></div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Terbaru</h2>
                        <?php if (empty($transactions)): ?>
                                <div class="text-sm text-gray-500">Belum ada transaksi.</div>
                        <?php else: ?>
                                <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                                <thead>
                                                        <tr class="text-left text-gray-500 border-b">
                                                                <th class="py-2">ID</th>
                                                                <th class="py-2">User</th>
                                                                <th class="py-2">Pesanan</th>
                                                                <th class="py-2">Tanggal</th>
                                                                <th class="py-2">Total</th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <?php foreach ($transactions as $row): ?>
                                                                <tr class="border-b last:border-b-0">
                                                                        <td class="py-2">#<?= (int) $row['id'] ?></td>
                                                                        <td class="py-2"><?= htmlspecialchars($row['username'] ?? '-') ?></td>
                                                                        <td class="py-2">#<?= (int) $row['pesanan_id'] ?></td>
                                                                        <td class="py-2"><?= htmlspecialchars($row['tanggal_pesanan'] ?? '-') ?></td>
                                                                        <td class="py-2">IDR <?= number_format((int) ($row['total_harga'] ?? 0), 0, ',', '.') ?></td>
                                                                </tr>
                                                        <?php endforeach; ?>
                                                </tbody>
                                        </table>
                                </div>
                        <?php endif; ?>
                </div>
        </div>
</div>

<script src="../assets/js/apexcharts.min.js"></script>
<script>
const getBrandColor = () => {
    const computedStyle = getComputedStyle(document.documentElement);
    return computedStyle.getPropertyValue('--color-fg-brand').trim() || '#f58231';
};

const brandColor = getBrandColor();

const options = {
    chart: {
        height: 280,
        maxWidth: '100%',
        type: 'area',
        fontFamily: 'Inter, sans-serif',
        dropShadow: {
            enabled: false,
        },
        toolbar: {
            show: false,
        },
    },
    tooltip: {
        enabled: true,
        x: {
            show: false,
        },
        y: {
            formatter: (value) => {
                const formatted = new Intl.NumberFormat('id-ID').format(value || 0);
                return `IDR ${formatted}`;
            },
        },
    },
    fill: {
        type: 'gradient',
        gradient: {
            opacityFrom: 0.55,
            opacityTo: 0,
            shade: brandColor,
            gradientToColors: [brandColor],
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        width: 6,
    },
    grid: {
        show: false,
        strokeDashArray: 4,
        padding: {
            left: 2,
            right: 2,
            top: 0,
        },
    },
    series: [
        {
            name: 'Transaksi',
            data: <?= json_encode($chart_data) ?>,
            color: brandColor,
        },
    ],
    xaxis: {
        categories: <?= json_encode($chart_labels) ?>,
        labels: {
            show: false,
        },
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
    },
    yaxis: {
        show: false,
    },
};

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('area-chart');
    if (el && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(el, options);
        chart.render();
    }
});
</script>

<?php include '../layout/footer.php'; ?>
