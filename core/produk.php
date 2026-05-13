<?php
require_once __DIR__ . '/../config/db.php';

function handleProdukCreate(string $redirect = 'index.php'): void
{
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		return;
	}

	global $conn;

	$nama = $_POST['nama'] ?? '';
	$harga = $_POST['harga'] ?? '';

	mysqli_query($conn, "INSERT INTO produk (nama, harga) VALUES ('$nama', $harga)");

	header("Location: $redirect");
	exit;
}
