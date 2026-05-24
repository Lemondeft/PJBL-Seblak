<?php
session_start();
require_once '../auth/check.php';
require_once '../config/db.php';

$error = '';
$success = '';
$userId = (int) ($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId > 0) {
    $new_username = trim($_POST['username'] ?? '');
    if ($new_username === '') {
        $error = 'Username tidak boleh kosong.';
    } elseif (strlen($new_username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
        $error = 'Username hanya boleh huruf, angka, dan underscore.';
    } else {
        $escaped = mysqli_real_escape_string($conn, $new_username);
        $exists = mysqli_query($conn, "SELECT 1 FROM users WHERE username = '$escaped' AND id <> $userId LIMIT 1");
        if ($exists && mysqli_num_rows($exists) > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            mysqli_query($conn, "UPDATE users SET username = '$escaped' WHERE id = $userId");
            $_SESSION['user'] = $new_username;
            $success = 'Username berhasil diperbarui.';
        }
    }
}
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

            <div class="flex items-center justify-center gap-2" id="username-view">
                <h1 class="text-3xl font-semibold">
                    <?= htmlspecialchars($_SESSION['user']) ?>
                </h1>

                <button type="button" id="edit-username" class="text-xl opacity-80 hover:opacity-100 transition-opacity cursor-pointer" aria-label="Ubah username">
                    ✎
                </button>
            </div>

            <form method="POST" id="username-form" class="hidden mt-3">
                <div class="flex items-center justify-center gap-2">
                    <input type="text" name="username" value="<?= htmlspecialchars($_SESSION['user']) ?>" class="px-3 py-2 rounded-lg text-gray-800" required pattern="[a-zA-Z0-9_]+" title="Hanya huruf, angka, dan underscore">
                    <button type="submit" class="bg-white/20 hover:bg-white/30 rounded-lg px-3 py-2">Simpan</button>
                    <button type="button" id="cancel-username" class="bg-white/10 hover:bg-white/20 rounded-lg px-3 py-2">Batal</button>
                </div>
            </form>

            <?php if ($error): ?>
                <div class="mt-3 text-sm text-white/90 bg-red-500/60 px-3 py-2 rounded-lg inline-block">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php elseif ($success): ?>
                <div class="mt-3 text-sm text-white/90 bg-green-500/60 px-3 py-2 rounded-lg inline-block">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const editBtn = document.getElementById('edit-username');
    const cancelBtn = document.getElementById('cancel-username');
    const view = document.getElementById('username-view');
    const form = document.getElementById('username-form');
    const modal = document.getElementById('confirm-username');
    const modalCancel = document.getElementById('confirm-username-cancel');
    const modalConfirm = document.getElementById('confirm-username-yes');

    if (!editBtn || !cancelBtn || !view || !form) {
        return;
    }

    editBtn.addEventListener('click', () => {
        view.classList.add('hidden');
        form.classList.remove('hidden');
        form.querySelector('input')?.focus();
    });

    cancelBtn.addEventListener('click', () => {
        form.classList.add('hidden');
        view.classList.remove('hidden');
    });

    form.addEventListener('submit', (event) => {
        if (!modal) {
            return;
        }
        event.preventDefault();
        modal.classList.remove('hidden');
    });

    modalCancel?.addEventListener('click', () => {
        modal?.classList.add('hidden');
    });

    modalConfirm?.addEventListener('click', () => {
        modal?.classList.add('hidden');
        form.submit();
    });
});
</script>

<div id="confirm-username" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative w-full max-w-sm mx-4 bg-[#ef654f] border border-white/70 rounded-2xl shadow-lg p-5 text-white">
        <div class="text-lg font-semibold">Ubah username?</div>
        <div class="text-sm text-white/90 mt-2">Pastikan username sudah benar.</div>
        <div class="mt-5 flex items-center justify-end gap-2">
            <button type="button" id="confirm-username-cancel" class="bg-white/10 hover:bg-white/20 rounded-xl px-4 py-2">Batal</button>
            <button type="button" id="confirm-username-yes" class="bg-white text-[#ef654f] rounded-xl px-4 py-2 font-semibold">Ya</button>
        </div>
    </div>
</div>
</div>