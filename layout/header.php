<?php
$assetBase = $assetBase ?? 'assets';
$baseUrl = $baseUrl ?? '.';
$activeNav = $activeNav ?? '';

if (!function_exists('navTextClass')) {
  function navTextClass(string $key, string $activeNav): string {
    return $activeNav === $key ? 'text-white' : 'text-[#292D32]';
  }
}

if (!function_exists('navIconClass')) {
  function navIconClass(string $key, string $activeNav): string {
    return $activeNav === $key ? 'icon-white' : 'icon-dark';
  }
}
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
    <style>
        .icon-white { filter: brightness(0) invert(1); }
      .icon-dark { filter: brightness(0) saturate(100%) invert(14%) sepia(8%) saturate(494%) hue-rotate(169deg) brightness(94%) contrast(93%); }
        .truncate-2-lines {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-[#f8f9fa]">

<div x-data="{ open: false }">
  <div class="bg-gradient-to-r from-[#ed4a4a] to-[#f58231] text-white p-4 sm:px-6 lg:px-10 flex justify-between items-center shadow-sm min-h-[80px] relative z-40">
    <a href="<?= $baseUrl ?>/user/index.php" class="flex items-center justify-center hover:scale-105 transition-transform">
      <img src="<?= $assetBase ?>/icons/profile.svg" class="w-8 h-8 icon-white" alt="Profile">
    </a>
    <button @click="open = !open" class="p-2 hover:scale-110 transition-transform focus:outline-none">
      <img src="<?= $assetBase ?>/icons/navbtn.svg" class="w-8 h-8 icon-white" alt="Menu">
    </button>
  </div>

  <div x-show="open"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       class="fixed top-0 right-0 h-full w-64 bg-gradient-to-b from-[#ed4a4a] to-[#f58231] z-50 shadow-2xl overflow-y-auto"
       style="display: none;">

    <div class="p-6 text-center">
      <div class="flex justify-between items-center mb-10">
        <h2 class="text-white font-bold text-xl uppercase tracking-widest">Nav Bar</h2>
        <button @click="open = false" class="text-white text-3xl leading-none">&times;</button>
      </div>

      <nav class="flex flex-col gap-8">
        <a href="<?= $baseUrl ?>/index.php" class="flex flex-col items-center group">
          <img src="<?= $assetBase ?>/icons/home.svg" class="w-7 h-7 mb-1 <?= navIconClass('home', $activeNav) ?> group-hover:scale-110 transition-transform">
          <span class="<?= navTextClass('home', $activeNav) ?> text-xs font-medium">Beranda</span>
        </a>
        <a href="<?= $baseUrl ?>/pesanan/index.php" class="flex flex-col items-center group">
          <img src="<?= $assetBase ?>/icons/shop.svg" class="w-7 h-7 mb-1 <?= navIconClass('pesanan', $activeNav) ?> group-hover:scale-110 transition-transform">
          <span class="<?= navTextClass('pesanan', $activeNav) ?> text-xs font-medium">Pesan</span>
        </a>
        <a href="<?= $baseUrl ?>/favorit.php" class="flex flex-col items-center group">
          <img src="<?= $assetBase ?>/icons/favorite.svg" class="w-7 h-7 mb-1 <?= navIconClass('favorit', $activeNav) ?> group-hover:scale-110 transition-transform">
          <span class="<?= navTextClass('favorit', $activeNav) ?> text-xs font-medium">Favorit</span>
        </a>
        <a href="<?= $baseUrl ?>/transaksi/index.php" class="flex flex-col items-center group">
          <img src="<?= $assetBase ?>/icons/wallet.svg" class="w-7 h-7 mb-1 <?= navIconClass('transaksi', $activeNav) ?> group-hover:scale-110 transition-transform">
          <span class="<?= navTextClass('transaksi', $activeNav) ?> text-xs font-medium leading-tight">Metode Transaksi</span>
        </a>
        <a href="<?= $baseUrl ?>/histori.php" class="flex flex-col items-center group">
          <img src="<?= $assetBase ?>/icons/history.svg" class="w-7 h-7 mb-1 <?= navIconClass('histori', $activeNav) ?> group-hover:scale-110 transition-transform">
          <span class="<?= navTextClass('histori', $activeNav) ?> text-xs font-medium">Histori</span>
        </a>
      </nav>
    </div>
  </div>

  <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/30 z-40 backdrop-blur-sm" style="display: none;"></div>
</div>
