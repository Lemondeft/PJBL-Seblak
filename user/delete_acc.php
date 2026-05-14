<?php
session_start();
require_once '../auth/check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/db.php';
    
    $username = $_SESSION['user'];
    
    $query = "DELETE FROM users WHERE username = '$username'";
    
    if (mysqli_query($conn, $query)) {
        session_unset();
        session_destroy();
        
        echo "<script>
                alert('Akun Anda telah dihapus secara permanen.');
                window.location.href = '../auth/login.php';
              </script>";
        exit();
    } else {
        $error = "Gagal menghapus akun: " . mysqli_error($conn);
    }
}
?>

<?php
$assetBase = '../assets';
$baseUrl = '..';
include '../layout/head.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-[#ed4a4a] to-[#f58231] p-6">

    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-sm text-center border-t-8 border-red-600">

        <div class="text-red-500 mb-4 flex justify-center">
             <span class="text-6xl text-red-600">⚠</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-4">
            Hapus Akun?
        </h1>

        <p class="text-gray-600 mb-8 text-sm leading-relaxed">
            Tindakan ini bersifat <b>PERMANEN</b>. Semua data profil dan histori pesanan Anda akan hilang selamanya.
        </p>

        <?php if (isset($error)): ?>
            <p class="text-red-500 text-xs mb-4"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" class="flex flex-col gap-3">

            <button
                type="submit"
                class="w-full px-6 py-3 rounded-2xl
                bg-red-600 text-white font-bold
                hover:bg-red-700 transition-all shadow-md shadow-red-200">
                YA, HAPUS PERMANEN
            </button>

            <a
                href="index.php" class="w-full px-6 py-3 rounded-2xl
                bg-gray-100 text-gray-600 font-medium
                hover:bg-gray-200 transition-all text-center">
                Batal
            </a>
        </form>
    </div>
</div>

<?php include '../layout/footer.php'; ?>