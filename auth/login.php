<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['user'] = $data['username'];

        header("Location: ../index.php");
        exit();
    } else {
        $error = "Login gagal";
    }
}
?>

<?php include '../layout/header.php'; ?>
<h5>Login</h5>
<?php if (isset($error)) : ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">  
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<?php include '../layout/footer.php'; ?>
