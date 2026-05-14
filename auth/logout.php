<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    session_unset();
    session_destroy();

    header("Location: login.php");
    exit();
}
?>

<?php
$assetBase = '../assets';
$baseUrl = '..';
include '../layout/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-[#ed4a4a] to-[#f58231] p-6">

    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-sm text-center">

        <h1 class="text-3xl font-bold text-[#ed4a4a] mb-4">
            Logout
        </h1>

        <p class="text-gray-600 mb-8">
            Are you sure you want to log out?
        </p>

        <form method="POST" class="flex gap-4 justify-center">

            <a
                href="../index.php"
                class="px-6 py-3 rounded-2xl
                bg-gray-200 text-gray-700
                hover:bg-gray-300 transition-all">

                Cancel
            </a>

            <button
                type="submit"
                class="px-6 py-3 rounded-2xl
                bg-[#ed4a4a] text-white
                hover:opacity-90 transition-all">

                Logout
            </button>
        </form>
    </div>
</div>

<?php include '../layout/footer.php'; ?>