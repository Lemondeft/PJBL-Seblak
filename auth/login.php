<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = trim($_POST['username'] ?? '');
    $pass_input = $_POST['password'] ?? '';

    if ($user_input === '' || $pass_input === '') {
        $error = "Username dan password wajib diisi!";
    } elseif (strlen($pass_input) < 8) {
        $error = "Password minimal 8 karakter!";
    } else {
        $user_escaped = mysqli_real_escape_string($conn, $user_input);
        $query = mysqli_query($conn, "
            SELECT * FROM users
            WHERE username='$user_escaped'
            LIMIT 1
        ");

        if ($query && mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            $stored_password = $data['password'] ?? '';

            if (password_verify($pass_input, $stored_password) || hash_equals($stored_password, $pass_input)) {
                $_SESSION['user'] = $data['username'];
                $_SESSION['user_id'] = (int) ($data['id'] ?? 0);
                $_SESSION['role'] = $data['role'] ?? 'user';

                header("Location: ../index.php");
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>

<?php
$assetBase = '../assets';
$baseUrl = '..';
include '../layout/head.php';
?>

<div class="w-full min-h-screen bg-white flex flex-col relative overflow-hidden font-sans">

    <div
        id="login-form-section"
        class="fixed inset-x-0 top-0 h-[80vh]
        bg-gradient-to-b from-[#ed4a4a] to-[#f58231]
        transform -translate-y-[110%]
        transition-transform duration-500 ease-in-out
        z-50 rounded-b-[30px]
        p-8 flex flex-col shadow-xl">

        <div class="w-full max-w-sm mx-auto flex flex-col h-full">

            <div class="mb-8">

                <h2 class="text-white text-5xl font-normal">
                    Sign In
                </h2>

                <div class="w-24 h-[1px] bg-white mt-1"></div>
            </div>

            <?php if ($error): ?>
                <p class="text-white text-md italic text-center mb-5">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST" class="space-y-5">

                <div>

                    <label class="block text-white text-xl mb-2 font-light">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        placeholder="Masukkan username"
                        required
                        class="w-full bg-[#f8f8f8]
                        rounded-full py-3 px-6
                        focus:outline-none
                        text-gray-700
                        shadow-inner border-none">
                </div>

                <div>

                    <label class="block text-white text-xl mb-2 font-light">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="•••••••"
                        required
                        minlength="8"
                        class="w-full bg-[#f8f8f8]
                        rounded-full py-3 px-6
                        focus:outline-none
                        text-gray-700
                        shadow-inner border-none">
                </div>

                <div class="flex justify-between items-center text-white text-sm">

                    <label class="flex items-center gap-2 cursor-pointer">

                        <input
                            type="checkbox"
                            class="accent-white">

                        Ingat Saya
                    </label>

                    <a
                        href="#"
                        class="underline underline-offset-4 decoration-white/50">

                        Lupa Sandi?
                    </a>
                </div>


                <div class="pt-4 flex justify-center">

                    <button
                        type="submit"
                        class="border-2 border-white
                        bg-white/10 text-white
                        font-medium py-2 px-14
                        rounded-2xl text-2xl
                        hover:bg-white/20
                        transition-all">

                        Login
                    </button>
                </div>
            </form>

            <p class="text-white text-center text-md mt-10">

                Belum memiliki akun?

                <a
                    href="register.php"
                    class="underline cursor-pointer text-white">

                    Sign Up
                </a>
            </p>

            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2">

                <button
                    onclick="toggleLogin()"
                    class="bg-black/10 hover:bg-black/20 p-2 rounded-full backdrop-blur-sm transition-all">

                    <img
                        src="<?= $assetBase ?>/icons/arrow.svg"
                        class="w-8 h-8 rotate-90"
                        alt="Close">
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center w-full min-h-screen">

        <div
            class="w-full
            bg-gradient-to-r from-[#ed4a4a] to-[#f58231]
            text-white pt-16 pb-20
            rounded-b-[40px]
            shadow-lg
            flex flex-col items-center relative">

            <h1 class="text-5xl font-normal tracking-tight">
                Sign In
            </h1>

            <button
                onclick="toggleLogin()"
                class="absolute -bottom-6 bg-black/10 hover:bg-black/20 p-2 rounded-full backdrop-blur-sm transition-all">

                <img
                    src="<?= $assetBase ?>/icons/arrow.svg"
                    class="w-8 h-8 -rotate-90"
                    alt="Open">
            </button>
        </div>
        <div class="flex flex-col items-center justify-center flex-grow py-10">

            <h2
                class="text-[#ed4a4a]
                text-4xl font-light
                mb-8 tracking-widest">

                Welcome
            </h2>

            <div
    class="w-48 h-48 rounded-full
    bg-black flex items-center
    justify-center overflow-hidden
    shadow-2xl p-4">

    <img
        src="<?= $assetBase ?>/icons/logo.png"
        alt="Logo"
        class="w-full h-full">
</div>

            <div class="mt-10 text-center">

                <h3
                    class="text-[#ed4a4a]
                    text-4xl font-bold tracking-tight">

                    Seblak
                </h3>

                <h3
                    class="text-[#ed4a4a]
                    text-4xl font-bold tracking-tight uppercase">

                    Mama Rizki
                </h3>
            </div>

            <a
                href="../index.php"
                class="mt-10 text-orange-500
                font-medium underline underline-offset-8">

                Jadi Pengunjung
            </a>
        </div>
    </div>
</div>

<script>
function toggleLogin() {

    const panel = document.getElementById(
        'login-form-section'
    );

    if (
        panel.classList.contains('-translate-y-[110%]')
    ) {

        panel.classList.remove(
            '-translate-y-[110%]'
        );

        panel.classList.add(
            'translate-y-0'
        );

    } else {

        panel.classList.remove(
            'translate-y-0'
        );

        panel.classList.add(
            '-translate-y-[110%]'
        );
    }
}

<?php if ($error): ?>
toggleLogin();
<?php endif; ?>
</script>
