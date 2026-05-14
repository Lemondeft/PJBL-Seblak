<?php
session_start();
require_once '../auth/check.php';
?>

<?php
$assetBase = '../assets';
include '../layout/head.php';
?>

<div class="min-h-screen bg-gradient-to-b from-[#ed4a4a] to-[#f58231] text-white flex flex-col">

    <div class="p-5 flex items-center">

        <a
            href="../index.php"
            class="w-12 h-12
            rounded-full
            bg-white/20
            backdrop-blur-sm
            flex items-center justify-center">

            <img
                src="<?= $assetBase ?>/icons/arrow.svg"
                class="w-6 h-6"
                alt="Back">
        </a>
    </div>

    <div class="flex flex-col items-center">
        <div
    class="w-28 h-28
    rounded-full
    flex items-center justify-center">

    <img
        src="<?= $assetBase ?>/icons/profile.svg"
        class="w-full h-full"
        alt="Profile">
</div>
        <div class="mt-5 text-center">

            <div class="flex items-center justify-center gap-2">

                <h1 class="text-3xl font-semibold">
                    <?= htmlspecialchars($_SESSION['user']) ?>
                </h1>

                <span class="text-xl opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
                    ✎
                </span>
            </div>

            <p class="text-white/80 mt-2">
                Di Suatu Tempat, Bumi
            </p>
        </div>
    </div>

    <div class="mt-10 px-5 flex-1">

        <p class="text-sm tracking-wide text-white/80 mb-4">
            ACCOUNT SETTINGS
        </p>

        <div class="space-y-5">

            <a
                href="settings.php"
                class="bg-[#ef654f]
                rounded-2xl
                shadow-lg
                px-5 py-5
                flex items-center justify-between hover:bg-[#ed4a4a] hover:scale-[1.01] transition-all ">

                <div class="flex items-center gap-4">

                    <img
                        src="<?= $assetBase ?>/icons/settings_active.svg"
                        class="w-8 h-8"
                        alt="Settings">

                    <span class="text-xl font-medium">
                        Pengaturan
                    </span>
                </div>

                <img
                    src="<?= $assetBase ?>/icons/arrow.svg"
                    class="w-6 h-6 rotate-180"
                    alt="Arrow">
            </a>

            <a
                href="../auth/logout.php"
                class="bg-[#ef654f]
                rounded-2xl
                shadow-lg
                px-5 py-5
                flex items-center justify-between hover:bg-[#ed4a4a] hover:scale-[1.01] transition-all">

                <div class="flex items-center gap-4">

                    <img
                        src="<?= $assetBase ?>/icons/log-out_active.svg"
                        class="w-8 h-8"
                        alt="Logout">

                    <span class="text-xl font-medium">
                        Log Out
                    </span>
                </div>

                <img
                    src="<?= $assetBase ?>/icons/arrow.svg"
                    class="w-6 h-6 rotate-180"
                    alt="Arrow">
            </a>
        </div>
    </div>
    <div class="mt-8 flex justify-center p-4">
    <a href="delete_acc.php"
       class="bg-red-600 border border-white/70 rounded-2xl h-12 flex items-center justify-center text-white font-semibold hover:bg-red-500 transition-colors px-6">
        Hapus Akun
    </a>
</div>
</div>

<?php include '../layout/footer.php'; ?>