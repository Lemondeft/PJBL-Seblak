<?php
session_start();
require_once '../auth/check.php';
require_once '../config/db.php';

$userId = (int) ($_SESSION['user_id'] ?? 0);
$error_user = '';
$success_user = '';
$error_pass = '';
$success_pass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId > 0) {
    $action = $_POST['action'] ?? '';

    if ($action === 'username') {
        $new_username = trim($_POST['username'] ?? '');
        if ($new_username === '') {
            $error_user = 'Username tidak boleh kosong.';
        } elseif (strlen($new_username) < 3) {
            $error_user = 'Username minimal 3 karakter.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
            $error_user = 'Username hanya boleh huruf, angka, dan underscore.';
        } else {
            $escaped = mysqli_real_escape_string($conn, $new_username);
            $exists = mysqli_query($conn, "SELECT 1 FROM users WHERE username = '$escaped' AND id <> $userId LIMIT 1");
            if ($exists && mysqli_num_rows($exists) > 0) {
                $error_user = 'Username sudah digunakan.';
            } else {
                mysqli_query($conn, "UPDATE users SET username = '$escaped' WHERE id = $userId");
                $_SESSION['user'] = $new_username;
                $success_user = 'Username berhasil diperbarui.';
            }
        }
    }

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            $error_pass = 'Semua field password wajib diisi.';
        } elseif (strlen($new) < 8) {
            $error_pass = 'Password baru minimal 8 karakter.';
        } elseif ($new !== $confirm) {
            $error_pass = 'Konfirmasi password tidak cocok.';
        } else {
            $q = mysqli_query($conn, "SELECT password FROM users WHERE id = $userId LIMIT 1");
            if (! $q || mysqli_num_rows($q) === 0) {
                $error_pass = 'Pengguna tidak ditemukan.';
            } else {
                $row = mysqli_fetch_assoc($q);
                $stored = $row['password'] ?? '';
                if (password_verify($current, $stored) || hash_equals($stored, $current)) {
                    $hash = password_hash($new, PASSWORD_DEFAULT);
                    mysqli_query($conn, "UPDATE users SET password = '$hash' WHERE id = $userId");
                    $success_pass = 'Password berhasil diubah. Silakan login ulang.';
                    // Optionally force logout after password change
                    session_unset();
                    session_destroy();
                    session_start();
                } else {
                    $error_pass = 'Password saat ini salah.';
                }
            }
        }
    }
}

$assetBase = '../assets';
include '../layout/head.php';
?>

<div class="min-h-screen bg-gradient-to-b from-[#ed4a4a] to-[#f58231] text-white flex flex-col">

    <div class="p-5 flex items-center">
        <a href="index.php" class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
            <img src="<?= $assetBase ?>/icons/arrow.svg" class="w-6 h-6" alt="Back">
        </a>
    </div>

    <div class="max-w-3xl mx-auto w-full px-5 py-8">
        <h2 class="text-2xl font-semibold">Pengaturan Akun</h2>
        <p class="text-white/80 mt-1">Atur username dan password akun Anda.</p>

        <div class="mt-6 space-y-6">

            <div class="bg-[#ef654f] rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-lg">Ubah Username</h3>
                        <p class="text-sm text-white/80 mt-1">Username hanya boleh huruf, angka, dan underscore.</p>
                    </div>
                </div>

                <?php if ($error_user): ?>
                    <div class="mt-3 text-sm text-white/90 bg-red-500/60 px-3 py-2 rounded-lg inline-block"><?= htmlspecialchars($error_user) ?></div>
                <?php elseif ($success_user): ?>
                    <div class="mt-3 text-sm text-white/90 bg-green-500/60 px-3 py-2 rounded-lg inline-block"><?= htmlspecialchars($success_user) ?></div>
                <?php endif; ?>

                <form method="POST" id="username-form" class="mt-4">
                    <input type="hidden" name="action" value="username">
                    <div class="flex gap-3 items-center">
                        <input type="text" name="username" value="<?= htmlspecialchars($_SESSION['user']) ?>" required pattern="[a-zA-Z0-9_]+" class="flex-1 px-3 py-2 rounded-lg text-gray-800" />
                        <button type="button" id="username-save" class="bg-white/20 hover:bg-white/30 rounded-lg px-4 py-2">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="bg-[#ef654f] rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-lg">Ganti Password</h3>
                        <p class="text-sm text-white/80 mt-1">Gunakan password minimal 8 karakter.</p>
                    </div>
                </div>

                <?php if ($error_pass): ?>
                    <div class="mt-3 text-sm text-white/90 bg-red-500/60 px-3 py-2 rounded-lg inline-block"><?= htmlspecialchars($error_pass) ?></div>
                <?php elseif ($success_pass): ?>
                    <div class="mt-3 text-sm text-white/90 bg-green-500/60 px-3 py-2 rounded-lg inline-block"><?= htmlspecialchars($success_pass) ?></div>
                <?php endif; ?>

                <form method="POST" id="password-form" class="mt-4 space-y-3">
                    <input type="hidden" name="action" value="change_password">

                    <div>
                        <label class="text-sm">Password Saat Ini</label>
                        <input type="password" name="current_password" required class="w-full px-3 py-2 rounded-lg text-gray-800" />
                    </div>

                    <div>
                        <label class="text-sm">Password Baru</label>
                        <input type="password" name="new_password" required minlength="8" class="w-full px-3 py-2 rounded-lg text-gray-800" />
                    </div>

                    <div>
                        <label class="text-sm">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required minlength="8" class="w-full px-3 py-2 rounded-lg text-gray-800" />
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-white/20 hover:bg-white/30 rounded-lg px-4 py-2">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8">
            <a href="index.php" class="text-white/90 underline">Kembali ke Profil</a>
        </div>
    </div>

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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('username-save');
    const usernameForm = document.getElementById('username-form');
    const modal = document.getElementById('confirm-username');
    const modalCancel = document.getElementById('confirm-username-cancel');
    const modalConfirm = document.getElementById('confirm-username-yes');

    saveBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
    });

    modalCancel?.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modalConfirm?.addEventListener('click', () => {
        modal.classList.add('hidden');
        usernameForm.submit();
    });
});
</script>

<?php include '../layout/footer.php'; ?>
